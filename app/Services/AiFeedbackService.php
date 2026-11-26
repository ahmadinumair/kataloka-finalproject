<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiFeedbackService
{
    /**
     * Menganalisis judul dan deskripsi aspirasi untuk menghasilkan rangkuman AI & tingkat urgensi yang akurat.
     *
     * @param string $title Judul aspirasi
     * @param string $description Deskripsi lengkap
     * @param string|null $category Nama kategori bidang (opsional untuk akurasi lebih tinggi)
     * @param string|null $type Jenis masukan (opsional, misal: Keluhan, Aspirasi, Saran)
     * @return array{summary: string, urgency: string, action_hint: string}
     */
    public function analyzeFeedback(string $title, string $description, ?string $category = null, ?string $type = null): array
    {
        $apiKey = config('services.gemini.key') ?? env('GEMINI_API_KEY');

        // Jika API Key tersedia, gunakan Google Gemini API untuk analisis tingkat tinggi
        if (!empty($apiKey)) {
            $apiResult = $this->callGeminiApi($title, $description, $category, $type, $apiKey);
            if ($apiResult !== null) {
                return $apiResult;
            }
        }

        // Advanced Fallback NLP Engine: Menghasilkan rangkuman cerdas, padat, dan akurat secara lokal
        return $this->smartLocalNlpAnalysis($title, $description, $category, $type);
    }

    /**
     * Memanggil Google Gemini API dengan prompt engineering khusus keluhan/aspirasi kampus.
     */
    protected function callGeminiApi(string $title, string $description, ?string $category, ?string $type, string $apiKey): ?array
    {
        $contextInfo = '';
        if ($category) {
            $contextInfo .= "Kategori Bidang: {$category}\n";
        }
        if ($type) {
            $contextInfo .= "Jenis Masukan: {$type}\n";
        }

        $prompt = <<<PROMPT
Anda adalah asisten AI resmi sistem aspirasi kampus Kataloka. Tugas Anda adalah menganalisis keluhan/aspirasi mahasiswa dengan SANGAT AKURAT, OBJEKTIF, dan LANGSUNG PADA INTI MASALAH.

DATA MASUKAN:
{$contextInfo}Judul: {$title}
Deskripsi: {$description}

PANDUAN PENULISAN RANGKUMAN (SUMMARY):
1. Tulis rangkuman dalam 1 hingga 2 kalimat padat (maksimal 35 kata).
2. LANGSUNG sebutkan: [Objek Masalah/Fasilitas/Isu Keamanan] + [Lokasi Spesifik jika ada] + [Kondisi/Dampak Nyata] + [Tindakan/Harapan].
3. DILARANG menggunakan kata pembuka klise seperti: "Pelapor mengatakan", "Mahasiswa mengeluhkan bahwa", "Aspirasi ini membahas", atau salam pembuka.
4. Jangan menyalin ulang kata per kata jika ada basa-basi, langsung ekstrak substansi utamanya.

PANDUAN URGENSI (URGENCY) - WAJIB SANGAT KETAT & AKURAT:
- "urgent":
  * ANCAMAN KEAMANAN & KRIMINALITAS: Intimidasi ormas, premanisme, pemalakan/pungli paksa, teror, kekerasan fisik, tawuran/pengeroyokan, senjata tajam, pencurian/begal, pelecehan seksual, situasi kampus terancam tidak aman.
  * KONDISI DARURAT FISIK & KESELAMATAN: Kebakaran, korsleting listrik parah/berasap, atap/plafon/dinding ambruk, pipa air utama jebol, gas bocor, orang terluka parah/pingsan, orang terjebak lift anjlok.
- "high":
  * GANGGUAN VITAL AKADEMIK: Server ujian/SIAKAD down, portal KRS error saat deadline, wifi/internet vital mati total, listrik padam/AC mati di ruang kuliah atau ujian saat siang hari, proyektor mati saat perkuliahan/sidang.
  * FASILITAS VITAL TERPUTUS: Air bersih mati total satu gedung, toilet mampet parah meluap, kebocoran atap yang deras saat hujan.
  * KERAWANAN KEAMANAN: CCTV mati di titik vital, pos satpam kosong, kehilangan barang berharga (laptop/helm), orang mencurigakan berkeliaran.
- "normal": Kerusakan minor non-darurat, AC kurang dingin, kursi goyang, kran wastafel menetes, proyektor redup, kebersihan toilet kurang wangi, saran perbaikan fasilitas rutin, antrean administrasi.
- "low": Ungkapan apresiasi, terima kasih, usulan minor estetika, warna cat, dekorasi, atau tanaman taman.

OUTPUT JSON FORMAT (WAJIB VALID JSON):
{
  "summary": "Tuliskan rangkuman padat dan akurat di sini.",
  "urgency": "urgent|high|normal|low",
  "action_hint": "Rekomendasi singkat tindakan teknis/administrasi untuk admin."
}
PROMPT;

        // Model Gemini yang dicoba berurutan (gemini-1.5-flash -> gemini-2.5-flash)
        $models = ['gemini-1.5-flash', 'gemini-2.5-flash'];

        foreach ($models as $model) {
            try {
                $response = Http::timeout(8)->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}",
                    [
                        'contents' => [
                            [
                                'parts' => [
                                    ['text' => $prompt]
                                ]
                            ]
                        ],
                        'generationConfig' => [
                            'responseMimeType' => 'application/json',
                            'temperature' => 0.2, // Rendah agar output sangat fokus dan faktual
                        ]
                    ]
                );

                if ($response->successful()) {
                    $jsonText = $response->json('candidates.0.content.parts.0.text');
                    $parsed = json_decode($jsonText, true);

                    if ($parsed && !empty($parsed['summary'])) {
                        $urgency = in_array($parsed['urgency'] ?? '', ['low', 'normal', 'high', 'urgent'])
                            ? $parsed['urgency']
                            : 'normal';

                        return [
                            'summary' => trim($parsed['summary']),
                            'urgency' => $urgency,
                            'action_hint' => trim($parsed['action_hint'] ?? ''),
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("Gemini API ({$model}) call failed: " . $e->getMessage());
            }
        }

        return null;
    }

    /**
     * Advanced Smart Local NLP: Ekstraksi konteks mendalam saat offline atau tanpa API Key.
     * Mampu membuang basa-basi dan mengekstrak subjek, lokasi, masalah, dan rekomendasi.
     *
     * @return array{summary: string, urgency: string, action_hint: string}
     */
    protected function smartLocalNlpAnalysis(string $title, string $description, ?string $category, ?string $type): array
    {
        $cleanDesc = $this->stripFillers($description);
        $fullText = strtolower($title . ' ' . $description);

        // 1. Deteksi Tingkat Urgensi Akurat
        $urgency = $this->detectUrgency($fullText);

        // 2. Ekstraksi Lokasi Spesifik
        $location = $this->extractLocation($title . ' ' . $description);

        // 3. Ekstraksi Subjek Fasilitas / Isu Pokok
        $subject = $this->extractSubject($title . ' ' . $description, $category);

        // 4. Ekstraksi Masalah / Kondisi Negatif
        $issue = $this->extractIssue($title . ' ' . $cleanDesc);

        // 5. Ekstraksi Dampak atau Harapan
        $impactOrHope = $this->extractImpactOrHope($cleanDesc);

        // 6. Rancang Rangkuman Padat dan Profesional
        $prefixType = $type ? ucfirst($type) : ($urgency === 'urgent' || $urgency === 'high' ? 'Keluhan' : 'Aspirasi');

        $summaryParts = [];
        if ($subject && $issue) {
            $isSecurity = str_contains(strtolower($subject . ' ' . $issue), 'keamanan') 
                || str_contains(strtolower($subject . ' ' . $issue), 'ormas')
                || str_contains(strtolower($subject . ' ' . $issue), 'ancaman')
                || str_contains(strtolower($subject . ' ' . $issue), 'preman');

            if ($isSecurity) {
                $coreProblem = "{$subject} yang menghadapi {$issue}";
            } else {
                $coreProblem = "{$subject} mengalami {$issue}";
            }

            if ($location) {
                $coreProblem .= " di {$location}";
            }
            $summaryParts[] = "{$prefixType} terkait {$coreProblem}";
        } else {
            // Gunakan kalimat judul yang dibersihkan
            $cleanTitle = rtrim(preg_replace('/^(keluhan|aspirasi|laporan|aduan)\s*[:\-]\s*/i', '', trim($title)), '.');
            $summaryParts[] = "{$prefixType} mengenai {$cleanTitle}" . ($location ? " di {$location}" : "");
        }

        if ($impactOrHope) {
            $summaryParts[] = $impactOrHope;
        } else {
            if ($urgency === 'urgent') {
                $isSecurityEmergency = str_contains($fullText, 'ormas') 
                    || str_contains($fullText, 'keamanan') 
                    || str_contains($fullText, 'preman') 
                    || str_contains($fullText, 'ancam') 
                    || str_contains($fullText, 'maling') 
                    || str_contains($fullText, 'intimidasi')
                    || str_contains($fullText, 'pelecehan')
                    || str_contains($fullText, 'tawuran');

                if ($isSecurityEmergency) {
                    $summaryParts[] = "Situasi dinilai mengancam keselamatan dan ketertiban kampus sehingga memerlukan penanganan darurat dan pengamanan ketat.";
                } else {
                    $summaryParts[] = "Kondisi dinilai membahayakan dan membutuhkan penanganan darurat segera.";
                }
            } elseif ($urgency === 'high') {
                $summaryParts[] = "Mengganggu kelancaran kegiatan belajar mengajar sehingga perlu prioritas perbaikan.";
            } elseif ($type === 'Apresiasi' || str_contains($fullText, 'terima kasih')) {
                $summaryParts[] = "Sebagai bentuk apresiasi atas peningkatan layanan dan kebersihan kampus.";
            } else {
                $summaryParts[] = "Diharapkan tindak lanjut dan pengecekan dari pihak pengelola terkait.";
            }
        }

        $summary = implode('. ', $summaryParts);
        if (!str_ends_with($summary, '.')) {
            $summary .= '.';
        }

        // Action hint untuk admin
        $actionHints = [
            'urgent' => (str_contains($fullText, 'ormas') || str_contains($fullText, 'keamanan') || str_contains($fullText, 'preman') || str_contains($fullText, 'ancam') || str_contains($fullText, 'intimidasi') || str_contains($fullText, 'tawuran') || str_contains($fullText, 'pelecehan'))
                ? 'Tindakan segera: Kerahkan komandan satpam kampus atau koordinasi dengan aparat berwajib untuk pengamanan lokasi dan perlindungan sivitas.'
                : 'Tindakan segera: Kirim tim tanggap darurat dan amankan lokasi.',
            'high' => 'Tindakan prioritas: Pengecekan teknisi sarpras / unit terkait dalam 1x24 jam.',
            'normal' => 'Tindakan standar: Jadwalkan pemeliharaan / koordinasi unit terkait.',
            'low' => 'Tindakan catatan: Dokumentasikan sebagai bahan evaluasi berkala.',
        ];

        return [
            'summary' => $summary,
            'urgency' => $urgency,
            'action_hint' => $actionHints[$urgency],
        ];
    }

    /**
     * Menghapus basa-basi (filler words / conversational noise) umum bahasa Indonesia.
     */
    protected function stripFillers(string $text): string
    {
        $patterns = [
            '/halo\s*(admin|bapak|ibu|kakak|semuanya)?[,\.\!\?]?/i',
            '/selamat\s*(pagi|siang|sore|malam)[,\.\!\?]?/i',
            '/assalamu[\'\’]?alaikum[\s\w]*[,\.\!\?]?/i',
            '/kepada\s*(yth|pihak|bapak|ibu)[\s\w]*[,\.\!\?]?/i',
            '/saya\s*(selaku|adalah)?\s*mahasiswa[\s\w]*[,\.\!\?]/i',
            '/mohon\s*maaf\s*(sebelumnya|mengganggu)[,\.\!\?]?/i',
            '/dengan\s*ini\s*saya\s*(ingin|bermaksud)[\s\w]*[:,\.]?/i',
            '/saya\s*hanya\s*ingin\s*(menyampaikan|mengeluhkan|curhat)[\s\w]*[:,\.]?/i',
            '/keluh\s*kesah\s*saya\s*adalah[\s\w]*[:,\.]?/i',
            '/semoga\s*(pesan|laporan)\s*ini\s*dibaca[\s\w]*[,\.]?/i',
        ];

        $cleaned = preg_replace($patterns, '', $text);
        return trim(preg_replace('/\s+/', ' ', $cleaned ?? $text));
    }

    /**
     * Deteksi tingkat urgensi berdasarkan spektrum kata kunci yang diperluas.
     */
    protected function detectUrgency(string $text): string
    {
        // 1. KONDISI DARURAT / URGENT (Keamanan, Ancaman Kriminalitas, Keselamatan Jiwa & Bencana Fisik)
        $urgentKeywords = [
            // Ancaman Keamanan, Premanisme, & Kriminalitas (Akar kata & frasa)
            'ormas', 'preman', 'ancam', 'intimidasi', 'teror', 'palak', 'pungli',
            'kekerasan', 'aniaya', 'keroyok', 'pukul', 'tawuran', 'bentrok', 'rusuh',
            'senjata', 'sajam', 'celurit', 'pisau', 'senpi', 'pistol',
            'begal', 'jambret', 'rampok', 'maling', 'curi', 'curanmor', 'bobol',
            'pelecehan', 'cabul', 'perkosa', 'asusila', 'penguntit', 'stalker',
            'keamanan kampus', 'tidak aman', 'penyusup', 'orang tak dikenal',

            // Bahaya Fisik, Medis, Bencana, & Sengatan/Kecelakaan
            'darurat', 'bahaya', 'gawat', 'nyawa', 'korban',
            'bakar', 'kobaran api', 'asap pekat', 'asap tebal', 'ledak',
            'konslet', 'korslet', 'terbakar', 'ambruk', 'roboh', 'runtuh', 'jebol parah',
            'menimpa', 'luka', 'darah', 'patah tulang', 'pingsan', 'racun', 'keracunan',
            'tersengat', 'setrum', 'kesetrum', 'lift anjlok', 'lift jatuh', 'terjebak lift',
            'gas bocor', 'bocor gas', 'ular', 'tawon vespa', 'banjir bandang',
            'tanah longsor', 'pohon tumbang', 'tolong segera'
        ];

        foreach ($urgentKeywords as $word) {
            if (str_contains($text, $word)) {
                return 'urgent';
            }
        }

        // 2. KONDISI TINGGI / HIGH (Gangguan Vital Akademik, Pemadaman, Kerawanan)
        $highKeywords = [
            'mati total', 'mati lampu', 'mati listrik', 'listrik padam', 'ac mati', 'ac panas',
            'proyektor rusak', 'proyektor mati', 'ujian', 'uts', 'uas', 'sidang', 'bentrok jadwal',
            'sistem down', 'server down', 'siakad error', 'krs error', 'wifi mati', 'internet putus',
            'air mati', 'toilet mampet', 'kran patah', 'atap bocor', 'hujan deras bocor', 'tidak bisa kuliah',
            'cctv mati', 'pos satpam kosong', 'kehilangan helm', 'kehilangan dompet', 'kehilangan laptop',
            'orang mencurigakan'
        ];

        foreach ($highKeywords as $word) {
            if (str_contains($text, $word)) {
                return 'high';
            }
        }

        // 3. KONDISI RENDAH / LOW (Apresiasi, Usulan Kosmetik & Estetika)
        $lowKeywords = [
            'apresiasi', 'terima kasih', 'makasih', 'keren', 'bagus', 'rapi', 'wangi',
            'usul warna', 'cat dinding', 'dekorasi', 'tanaman', 'estetika'
        ];

        foreach ($lowKeywords as $word) {
            if (str_contains($text, $word)) {
                return 'low';
            }
        }

        return 'normal';
    }

    /**
     * Mengekstrak nama gedung, lantai, atau ruangan spesifik dari teks.
     */
    protected function extractLocation(string $text): ?string
    {
        // Contoh pattern: "Gedung B Lantai 3 Ruang 302", "Lab Komputer 2", "Toilet Rektorat Lantai 1", "Gerbang Utama"
        $pattern = '/\b(?:gedung\s+[a-z0-9]+|lantai\s+[0-9]+|ruang(?:an)?\s+[0-9a-z\-]+|lab(?:oratorium)?\s+[\w\-]+|perpustakaan|perpus|toilet|wc|parkir(?:an)?\s*(?:motor|mobil)?|kantin|musholl?a|masjid|selasar|koridor|auditorium|aula|gerbang\s*(?:utama|belakang|depan)?|pos\s*satpam)\b(?:\s+(?:lantai|ruang|gedung)\s+[0-9a-z\-]+)*/i';

        if (preg_match($pattern, $text, $matches)) {
            return ucwords(strtolower(trim($matches[0])));
        }

        return null;
    }

    /**
     * Mengekstrak subjek utama fasilitas atau layanan.
     */
    protected function extractSubject(string $text, ?string $category): ?string
    {
        $subjects = [
            // Keamanan & Ketertiban
            'ormas' => 'Keamanan Kampus & Gangguan Ormas',
            'preman' => 'Keamanan & Ketertiban Kampus',
            'keamanan' => 'Keamanan & Ketertiban Kampus',
            'satpam' => 'Layanan Keamanan Satpam',
            'security' => 'Layanan Keamanan Kampus',
            'pos satpam' => 'Pos Pengamanan Satpam',
            'cctv' => 'Sistem Pengawasan CCTV',
            'pencurian' => 'Keamanan & Pencegahan Pencurian',
            'maling' => 'Keamanan Kampus dari Pencurian',
            'curanmor' => 'Keamanan Area Parkir Kendaraan',
            'pelecehan' => 'Keamanan & Perlindungan Seksual',
            'intimidasi' => 'Keamanan & Perlindungan dari Intimidasi',
            'pemalakan' => 'Penertiban Aksi Pemalakan & Pungli',

            // Fasilitas Fisik & Sarpras
            'ac' => 'Pendingin Ruangan (AC)',
            'air conditioner' => 'Pendingin Ruangan (AC)',
            'proyektor' => 'Proyektor Ruang Kuliah',
            'lcd' => 'Layar LCD / Proyektor',
            'wifi' => 'Jaringan Wi-Fi Kampus',
            'internet' => 'Koneksi Internet',
            'plafon' => 'Plafon / Atap Gedung',
            'atap' => 'Atap Bangunan',
            'lampu' => 'Penerangan / Lampu',
            'stopkontak' => 'Stopkontak / Colokan Listrik',
            'colokan' => 'Stopkontak Listrik',
            'kursi' => 'Fasilitas Kursi Belajar',
            'meja' => 'Meja Perkuliahan',
            'papan tulis' => 'Papan Tulis Kelas',
            'spidol' => 'Perlengkapan Spidol & Penghapus',
            'toilet' => 'Kondisi Toilet / Sanitasi',
            'kran' => 'Kran Air Wastafel',
            'air' => 'Pasokan Air Bersih',
            'lift' => 'Fasilitas Lift Gedung',
            'jadwal' => 'Jadwal Kuliah',
            'krs' => 'Sistem Pengisian KRS',
            'siakad' => 'Portal SIAKAD',
            'parkir' => 'Area Parkir Kendaraan',
            'kebersihan' => 'Kebersihan Lingkungan Kampus',
            'listrik' => 'Instalasi Listrik Gedung',
            'jalan' => 'Akses Jalan Kampus',
            'kantin' => 'Kantin Kampus',
            'laboratorium' => 'Fasilitas Laboratorium',
            'lab' => 'Fasilitas Laboratorium',
            'perpustakaan' => 'Layanan Perpustakaan',
            'ukt' => 'Pembayaran UKT',
        ];

        $lower = strtolower($text);
        foreach ($subjects as $key => $formalName) {
            if (preg_match('/\b' . preg_quote($key, '/') . '\b/i', $lower)) {
                return $formalName;
            }
        }

        return $category ? "Fasilitas {$category}" : null;
    }

    /**
     * Mengekstrak kondisi kerusakan atau masalah utama.
     */
    protected function extractIssue(string $text): ?string
    {
        $issues = [
            // Masalah Keamanan & Ancaman
            'terancam' => 'ancaman keamanan dan intimidasi',
            'ancaman' => 'ancaman bahaya/keamanan',
            'ormas' => 'gangguan pihak ormas/eksternal',
            'preman' => 'gangguan premanisme',
            'intimidasi' => 'tindakan intimidasi fisik/verbal',
            'diteror' => 'teror dan rasa was-was',
            'dipalak' => 'praktik pemalakan / pungli liar',
            'pemalakan' => 'aksi pemalakan paksa',
            'kemalingan' => 'insiden pencurian',
            'maling' => 'kasus pencurian',
            'dicuri' => 'kehilangan akibat pencurian',
            'hilang' => 'kehilangan barang berharga',
            'pelecehan' => 'tindakan pelecehan yang meresahkan',
            'tawuran' => 'potensi tawuran dan bentrokan',
            'kekerasan' => 'tindakan kekerasan fisik',
            'kebakaran' => 'bahaya kebakaran',
            'korslet' => 'korsleting listrik membahayakan',
            'konslet' => 'korsleting listrik membahayakan',
            'meledak' => 'ledakan berbahaya',
            'roboh' => 'kondisi roboh dan runtuh',
            'ambruk' => 'kondisi ambruk membahayakan',
            'anjlok' => 'kondisi anjlok berbahaya',

            // Masalah Teknis & Fasilitas
            'mati total' => 'kerusakan mati total',
            'mati' => 'tidak berfungsi / mati',
            'rusak parah' => 'kerusakan berat',
            'rusak' => 'gangguan kerusakan',
            'bocor' => 'kebocoran air',
            'jebol' => 'kondisi jebol dan lapuk',
            'kotor' => 'kondisi kurang bersih dan bau',
            'bau' => 'aroma tidak sedap',
            'mampet' => 'saluran tersumbat (mampet)',
            'padam' => 'pemadaman listrik',
            'panas' => 'suhu ruangan yang panas dan pengap',
            'buram' => 'tampilan visual buram/redup',
            'lemot' => 'koneksi sangat lambat',
            'lambat' => 'akses lambat',
            'down' => 'gangguan sistem (down)',
            'error' => 'kendala sistem error',
            'penuh' => 'kapasitas tidak mencukupi / over kapasitas',
            'kurang' => 'jumlah fasilitas yang kurang memadai',
            'gelap' => 'pencahayaan yang gelap dan minim',
        ];

        $lower = strtolower($text);
        foreach ($issues as $key => $formalIssue) {
            if (preg_match('/\b' . preg_quote($key, '/') . '\b/i', $lower)) {
                return $formalIssue;
            }
        }

        return null;
    }

    /**
     * Mengekstrak kalimat dampak nyata atau permohonan solusi.
     */
    protected function extractImpactOrHope(string $text): ?string
    {
        $sentences = preg_split('/[\.\n\!\?]+/', $text);
        if (!$sentences) {
            return null;
        }

        $impactPatterns = [
            '/mengganggu/i',
            '/kesulitan/i',
            '/tidak bisa/i',
            '/khawatir/i',
            '/takut/i',
            '/panas/i',
            '/resah/i',
            '/terancam/i',
            '/bahaya/i',
            '/keamanan/i',
            '/aman/i',
            '/tindak/i',
            '/tertib/i',
            '/was[- ]?was/i',
            '/mohon\s*(di|agar|untuk)/i',
            '/tolong\s*(di|agar|untuk)/i',
            '/perlu\s*(di|penambahan|perbaikan)/i',
            '/diharapkan/i',
            '/segera/i',
        ];

        foreach ($sentences as $sentence) {
            $trimmed = trim($sentence);
            if (strlen($trimmed) < 15 || strlen($trimmed) > 140) {
                continue;
            }

            foreach ($impactPatterns as $pattern) {
                if (preg_match($pattern, $trimmed)) {
                    // Rapikan huruf pertama jadi kapital
                    return ucfirst(rtrim($trimmed, '.')) . '.';
                }
            }
        }

        return null;
    }
}
