# 🌟 KATALOKA — Platform Suara & Aspirasi Kampus Berbasis AI

> **Kataloka** adalah platform web modern untuk menjembatani mahasiswa dan manajemen kampus dalam menyampaikan serta menindaklanjuti aspirasi, keluhan fasilitas, dan kritik yang membangun secara transparan, cerdas, dan responsif.

---

## 📖 Tentang Aplikasi

Kataloka dirancang untuk mempermudah ekosistem kampus dalam menciptakan lingkungan belajar yang lebih baik. Platform ini menyelesaikan permasalahan lambatnya penanganan birokrasi tradisional melalui integrasi **Analisis AI Otomatis**, **Papan Aspirasi Terbuka**, **Linimasa Penanganan 3 Tahap Real-Time**, serta **Kuesioner/Survei Evaluasi Pembelajaran**.

Desain antarmuka Kataloka mengusung estetika visual modern yang terinspirasi dari gaya *Moonli* (palet warna hitam pekat, aksen *neon lime* `#B5F938`, latar *clean slate* `#F7F8F5`, tipografi Syne & Space Grotesk) dengan performa perpindahan halaman **Single Page Application (SPA)** yang instan berkat Laravel Livewire v4.

---

## ✨ Fitur-Fitur Utama

### 🎓 1. Untuk Mahasiswa (Pengguna Umum)
- **Tulis Aspirasi Baru & Ekstraksi AI:**
  - Mahasiswa dapat mengirim keluhan, ide perbaikan, atau aspirasi ke berbagai kategori (Fasilitas, Akademik, Administrasi, Kebersihan, Keamanan, Kegiatan).
  - **Analisis AI Cerdas:** AI secara otomatis mengekstrak inti permasalahan menjadi rangkuman singkat serta mendeteksi level urgensi (*Normal*, *High*, atau *Urgent*).
  - **Lampiran Foto Bukti Kerusakan (Opsional):** Dilengkapi fitur unggah foto bukti dengan pratinjau langsung sebelum dikirim.
  - **Mode Anonim:** Pilihan untuk menyembunyikan identitas pelapor demi keamanan dan kenyamanan privasi mahasiswa.
- **Papan Aspirasi Publik & Komunitas:**
  - Telusuri aspirasi mahasiswa lain secara transparan.
  - Filter interaktif berdasarkan kategori, status penyelesaian, pencarian live kata kunci, dan pengurutan (Terbaru, Terpopuler, Paling Mendesak).
  - Navigasi paginasi dengan nomor halaman yang rapi.
- **Dukungan Aspirasi (Upvote):**
  - Berikan upvote untuk aspirasi mendesak agar mendapat perhatian dan prioritas penanganan lebih cepat oleh pihak kampus.
- **Linimasa Progres Penanganan:**
  - Pantau status tindak lanjut aduan melalui 3 tahap visual: *1. Terkirim* ➔ *2. Sedang Diproses* ➔ *3. Tuntas/Selesai*.
- **Kuesioner / Survei Mahasiswa:**
  - Mahasiswa yang telah login dapat berpartisipasi mengisi survei evaluasi mutu pembelajaran dan kualitas perkuliahan yang diterbitkan kampus.

---

### 🛡️ 2. Untuk Administrator Kampus
- **Dashboard Analitik & Grafik Visual:**
  - Ringkasan metrik: Total Aspirasi, Tiket Menunggu, Sedang Diproses, Tuntas, dan Masukan Berstatus Mendesak.
  - Grafik distribusi kategori aduan dan status penanganan interaktif menggunakan Chart.js.
  - Pemantauan cepat tiket-tiket prioritas tinggi (*Urgent Feedbacks*).
- **Manajemen Aspirasi & Tanggapan Resmi:**
  - Admin dapat memperbarui status tiket (*Pending*, *Sedang Diproses*, *Tuntas*, atau *Ditolak*).
  - Mengirimkan tanggapan resmi dari pihak kampus yang langsung dapat dibaca oleh pelapor dan publik.
  - Fitur **Tindakan Massal (Bulk Actions):** Ubah status atau hapus banyak aspirasi sekaligus dengan satu kali klik.
  - Fitur **Cetak / Export Laporan:** Siap cetak untuk kebutuhan arsip dan pelaporan pimpinan.
- **Pusat Manajemen Survei:**
  - Membuat survei berkala (skala 1–5 kepuasan belajar, pilihan ganda, dan kotak saran).
  - Mengaktifkan/menonaktifkan survei kapan saja.
  - **Privasi Hasil Survei:** Skor rata-rata dan total pengisi survei diproteksi khusus dan hanya dapat diakses oleh administrator demi menjaga objektivitas.

---

## ⚡ Teknologi yang Digunakan

| Komponen | Teknologi |
| :--- | :--- |
| **Framework Backend** | [Laravel 13](https://laravel.com) (PHP 8.3+) |
| **Fullstack Reactive UI** | [Livewire v4](https://livewire.laravel.com) (SPA Mode dengan `wire:navigate`) |
| **Styling & Design System**| [Tailwind CSS v4](https://tailwindcss.com) + Moonli Theme (`#B5F938`, Black, Clean Canvas) |
| **Database** | MySQL |
| **Visualisasi Data** | [Chart.js](https://www.chartjs.org) |
| **AI Integration** | `AiFeedbackService` (Analisis teks otomatis, ekstraksi poin inti, dan deteksi urgensi) |
| **Bundler Aset** | [Vite](https://vitejs.dev) |

---

## 🚀 Panduan Instalasi & Menjalankan Proyek

### 1. Kebutuhan Sistem
- PHP >= 8.3
- Composer
- Node.js & NPM
- MySQL / MariaDB

### 2. Langkah Instalasi

1. **Clone repository ini:**
   ```bash
   git clone <url-repository>
   cd Kataloka
   ```

2. **Pasang dependensi PHP & JavaScript:**
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment:**
   Salin file `.env.example` menjadi `.env` lalu sesuaikan konfigurasi database:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Pastikan pengaturan database di `.env` sudah benar:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=kataloka
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Migrasi Database & Seeding Data Awal:**
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Hubungkan Symlink Storage (Untuk Foto Bukti Aduan):**
   ```bash
   php artisan storage:link
   ```

6. **Kompilasi Aset Frontend:**
   ```bash
   npm run build
   # Atau untuk mode development:
   npm run dev
   ```

7. **Jalankan Server Lokal:**
   ```bash
   php artisan serve
   ```
   Akses aplikasi di browser pada: `http://127.0.0.1:8000`

---

## 🔑 Akun Bawaan (Default Credentials)

Setelah menjalankan `php artisan db:seed`, akun berikut dapat langsung digunakan untuk mencoba aplikasi:

| Peran (Role) | Email | Password | Keterangan |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@kataloka.test` | `AdminKataloka123!` | Akses penuh ke Dashboard Analitik, Manajemen Tiket, Respon Resmi, dan Survei. |
| **Mahasiswa** | Dapat mendaftar langsung melalui menu **Daftar** di halaman awal menggunakan email aktif Anda. |

---

## 🧪 Pengujian (Testing)

Aplikasi telah dilengkapi unit & feature test lengkap yang mencakup otentikasi, pengiriman aspirasi, proteksi peran, kalkulasi AI, dan pengisian survei:

```bash
php artisan test
```

---

<p align="center">
  Dibuat dengan ❤️ untuk kemajuan lingkungan kampus yang transparan dan kolaboratif.
</p>
