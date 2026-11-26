<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\FeedbackType;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Panggil AdminSeeder
        $this->call(AdminSeeder::class);

        // Buat Akun Mahasiswa Demo jika belum ada
        $student = User::updateOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'name' => 'Budi Mahasiswa',
                'password' => Hash::make('Password123!'),
                'role' => 'student',
            ]
        );

        $studentDummy = User::updateOrCreate(
            ['email' => 'student@kataloka.test'],
            [
                'name' => 'Budi Mahasiswa (Legacy)',
                'password' => Hash::make('Password123!'),
                'role' => 'student',
            ]
        );

        $student2 = User::updateOrCreate(
            ['email' => 'siti@kataloka.test'],
            [
                'name' => 'Siti Nurhaliza',
                'password' => Hash::make('Password123!'),
                'role' => 'student',
            ]
        );

        // Feedback Types
        $types = [
            'Aspirasi',
            'Keluhan',
            'Saran & Masukan',
            'Apresiasi',
        ];

        foreach ($types as $type) {
            FeedbackType::firstOrCreate(['name' => $type]);
        }

        // Categories
        $categories = [
            'Fasilitas & Sarana Prasarana',
            'Akademik & Perkuliahan',
            'Layanan Administrasi',
            'Kebersihan & Lingkungan',
            'Keamanan Kampus',
            'Kegiatan Mahasiswa',
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category]);
        }

        $admin = User::where('role', 'admin')->first();
        $catFasilitas = Category::where('name', 'Fasilitas & Sarana Prasarana')->first();
        $catAkademik = Category::where('name', 'Akademik & Perkuliahan')->first();
        $catKebersihan = Category::where('name', 'Kebersihan & Lingkungan')->first();
        $typeKeluhan = FeedbackType::where('name', 'Keluhan')->first();
        $typeSaran = FeedbackType::where('name', 'Saran & Masukan')->first();
        $typeApresiasi = FeedbackType::where('name', 'Apresiasi')->first();

        // Sample Feedbacks
        if (\App\Models\Feedback::count() === 0) {
            $fb1 = \App\Models\Feedback::create([
                'user_id' => $student->id,
                'type_id' => $typeKeluhan->id,
                'category_id' => $catFasilitas->id,
                'title' => 'AC Gedung B Lantai 3 Ruang 302 Mati Total',
                'description' => 'Saat perkuliahan siang hari ruang 302 sangat panas karena AC mati total sejak 3 hari lalu. Mahasiswa kesulitan konsentrasi selama sesi kuliah.',
                'status' => 'in_progress',
                'is_anonymous' => false,
                'urgency' => 'high',
                'ai_summary' => 'Inti Aduan: [AC Gedung B Lantai 3 Ruang 302 Mati Total] — Ruang kuliah 302 panas karena AC mati 3 hari mengganggu kenyamanan belajar. Tingkat Penanganan: Prioritas Tinggi (Mengganggu operasional).',
            ]);

            if ($admin) {
                \App\Models\Response::create([
                    'feedback_id' => $fb1->id,
                    'admin_id' => $admin->id,
                    'message' => 'Terima kasih atas laporannya. Tim sarana dan prasarana kampus sedang melakukan perbaikan kompresor AC hari ini.',
                ]);
            }

            $fb2 = \App\Models\Feedback::create([
                'user_id' => $student2->id,
                'type_id' => $typeKeluhan->id,
                'category_id' => $catFasilitas->id,
                'title' => 'Plafon Selasar Depan Perpustakaan Jebol dan Bahaya',
                'description' => 'Ada plafon selasar dekat perpustakaan yang jebol dan kayunya rapuh, khawatir ambruk dan menimpa mahasiswa yang lewat saat jam ramai.',
                'status' => 'pending',
                'is_anonymous' => true,
                'urgency' => 'urgent',
                'ai_summary' => 'Inti Aduan: [Plafon Selasar Depan Perpustakaan Jebol dan Bahaya] — Plafon rapuh dan jebol membahayakan pejalan kaki di selasar perpustakaan. Tingkat Penanganan: Sangat Mendesak / Bahaya.',
            ]);

            $fb3 = \App\Models\Feedback::create([
                'user_id' => $student->id,
                'type_id' => $typeSaran->id,
                'category_id' => $catAkademik->id,
                'title' => 'Usulan Penambahan Kursi dan Stopkontak di Ruang Baca',
                'description' => 'Ruang baca mandiri perpustakaan sering penuh saat musim ujian. Sangat disarankan untuk menambah colokan listrik di setiap meja belajar.',
                'status' => 'resolved',
                'is_anonymous' => false,
                'urgency' => 'normal',
                'ai_summary' => 'Inti Aduan: [Usulan Penambahan Kursi dan Stopkontak] — Saran penambahan fasilitas charging station dan meja baca untuk menampung mahasiswa saat ujian. Tingkat Penanganan: Normal / Prosedural.',
            ]);

            if ($admin) {
                \App\Models\Response::create([
                    'feedback_id' => $fb3->id,
                    'admin_id' => $admin->id,
                    'message' => 'Usulan telah direalisasikan. Sebanyak 12 stopkontak baru telah dipasang di meja baca perpustakaan lantai 2.',
                ]);
            }

            $fb4 = \App\Models\Feedback::create([
                'user_id' => $student2->id,
                'type_id' => $typeApresiasi->id,
                'category_id' => $catKebersihan->id,
                'title' => 'Apresiasi Petugas Kebersihan Gedung Rektorat dan Parkir',
                'description' => 'Area parkir dan toilet gedung rektorat kini jauh lebih wangi, bersih, dan rapi. Terima kasih kepada tim kebersihan kampus atas dedikasinya!',
                'status' => 'resolved',
                'is_anonymous' => false,
                'urgency' => 'low',
                'ai_summary' => 'Inti Aduan: [Apresiasi Petugas Kebersihan] — Ucapan terima kasih atas terjaganya kebersihan area parkir dan toilet. Tingkat Penanganan: Saran Minor / Apresiasi.',
            ]);

            // Upvotes
            \App\Models\FeedbackUpvote::create(['feedback_id' => $fb1->id, 'user_id' => $student2->id]);
            \App\Models\FeedbackUpvote::create(['feedback_id' => $fb2->id, 'user_id' => $student->id]);
        }

        // Sample Survey Mahasiswa
        if ($admin && \App\Models\Survey::count() === 0) {
            $survey = \App\Models\Survey::create([
                'title' => 'Survei Kepuasan Pembelajaran & Evaluasi Kualitas Pengajar (3 Bulan)',
                'description' => 'Evaluasi berkala terhadap mutu pengajaran dosen, kenyamanan ruang kelas, dan pengalaman belajar mahasiswa selama 3 bulan terakhir.',
                'target_audience' => 'Seluruh Mahasiswa Aktif',
                'is_active' => true,
                'created_by' => $admin->id,
            ]);

            $q1 = \App\Models\SurveyQuestion::create([
                'survey_id' => $survey->id,
                'question' => 'Seberapa jelas dan mudah dipahami materi yang disampaikan oleh pengajar / dosen di kelas?',
                'type' => 'scale_1_5',
                'order' => 1,
            ]);

            $q2 = \App\Models\SurveyQuestion::create([
                'survey_id' => $survey->id,
                'question' => 'Bagaimana tingkat kepuasan Anda terhadap ketepatan waktu dan kedisiplinan pengajar?',
                'type' => 'satisfaction_4',
                'order' => 2,
            ]);

            $q3 = \App\Models\SurveyQuestion::create([
                'survey_id' => $survey->id,
                'question' => 'Bagaimana penilaian Anda terhadap fasilitas kelas (AC, proyektor, meja kursi) selama 3 bulan ini?',
                'type' => 'scale_1_5',
                'order' => 3,
            ]);

            $q4 = \App\Models\SurveyQuestion::create([
                'survey_id' => $survey->id,
                'question' => 'Secara umum, bagaimana kepuasan Anda terhadap pengalaman belajar di kampus ini?',
                'type' => 'satisfaction_5',
                'order' => 4,
            ]);

            // Sample response from student 2 (siti)
            if ($student2) {
                $resp = \App\Models\SurveyResponse::create([
                    'survey_id' => $survey->id,
                    'user_id' => $student2->id,
                    'additional_feedback' => 'Pengajar sangat kompeten dan ramah, fasilitas kelas agar tetap dijaga rutin.',
                ]);

                \App\Models\SurveyAnswer::create([
                    'survey_response_id' => $resp->id,
                    'survey_question_id' => $q1->id,
                    'answer' => '5',
                    'score' => 5,
                ]);

                \App\Models\SurveyAnswer::create([
                    'survey_response_id' => $resp->id,
                    'survey_question_id' => $q2->id,
                    'answer' => 'Sangat Puas',
                    'score' => 4,
                ]);

                \App\Models\SurveyAnswer::create([
                    'survey_response_id' => $resp->id,
                    'survey_question_id' => $q3->id,
                    'answer' => '4',
                    'score' => 4,
                ]);

                \App\Models\SurveyAnswer::create([
                    'survey_response_id' => $resp->id,
                    'survey_question_id' => $q4->id,
                    'answer' => 'Sangat Puas',
                    'score' => 5,
                ]);
            }
        }
    }
}
