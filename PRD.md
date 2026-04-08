# PRODUCT REQUIREMENTS DOCUMENT (PRD) & DEVELOPMENT PLAN – SILAWAS

**Nama Produk:** SILAWAS (Sistem Informasi Laporan dan Pengawasan Sekolah)  
**Platform:** Web Dashboard (Utama untuk Pengawas) & Web/Mobile App (Untuk Input Sekolah)  
**Target Pengguna:** Pengawas Sekolah, Kepala Sekolah, Guru, dan Staf Sekolah.  
**Tech Stack:** Laravel 13, MySQL, Inertia.js (React/Vue), Tailwind CSS

---

## BAGIAN 1: PRODUCT REQUIREMENTS DOCUMENT (PRD)

### 1. Ringkasan Eksekutif (Executive Summary)
**SILAWAS** adalah sebuah platform digital yang dirancang untuk membantu Pengawas Sekolah dalam memonitor, mengevaluasi, dan mengukur capaian kinerja sekolah-sekolah yang berada di bawah naungannya. Platform ini mengkonsolidasikan data administrasi, proses Kegiatan Belajar Mengajar (KBM), laporan manajerial dari Wakil Kepala Sekolah, hingga hasil Rapor Pendidikan menjadi sebuah visualisasi grafik capaian yang mudah dipahami dan terukur.

### 2. Tujuan Produk (Product Goals)
* **Sentralisasi Data:** Menyatukan seluruh dokumen dan laporan sekolah dalam satu pintu.
* **Efisiensi Waktu:** Memudahkan pengawas untuk mengecek kelengkapan administrasi dan KBM tanpa harus selalu datang fisik ke sekolah.
* **Pengambilan Keputusan Berbasis Data:** Menyediakan grafik capaian (*dashboard analytics*) untuk melihat sekolah mana yang sudah berkinerja baik dan mana yang butuh pendampingan khusus.

### 3. Persona Pengguna (User Personas)
1.  **Pengawas (Super User):** Memiliki hak akses untuk melihat, menilai, memverifikasi laporan, dan melihat grafik capaian dari semua sekolah binaan.
2.  **Kepala Sekolah / Operator Sekolah:** Mengunggah data administrasi utama dan Rapor Pendidikan.
3.  **Wakil Kepala Sekolah (Wakasek):** Mengunggah laporan bulanan sesuai bidangnya (Kurikulum, Kesiswaan, Sarpras, Humas).
4.  **Guru:** Mengunggah dokumen KBM (Modul, RPP, Absen, Jurnal) dan laporan Ekstra/Kokurikuler.

### 4. Ruang Lingkup & Fitur Utama

#### Pilar 1: Manajemen Administrasi Sekolah
Pengawas dapat memantau status kelengkapan, validitas, dan masa berlaku dokumen administrasi inti sekolah.
* **IJOP (Izin Operasional):** Unggah dokumen scan IJOP, input nomor surat, dan *reminder* masa berlaku.
* **GTK (Guru & Tenaga Kependidikan):** Rekapitulasi jumlah guru, status kepegawaian, sertifikasi, dan linieritas ijazah.
* **PD (Peserta Didik):** Data demografi siswa, jumlah rombongan belajar (rombel), dan tren mutasi siswa.
* **SARPRAS (Sarana & Prasarana):** Inventarisasi kondisi ruang kelas, laboratorium, perpustakaan, dan fasilitas penunjang lainnya.

#### Pilar 2: Kontrol Kegiatan Belajar Mengajar (KBM)
Fitur untuk memastikan standar kualitas pembelajaran di kelas berjalan sesuai regulasi.
* **Perangkat Ajar (Modul & RPP):** Pengawas dapat melihat, mengunduh, dan memberikan catatan (*feedback*) pada modul ajar/RPP yang diunggah guru.
* **Absen & Jurnal Mengajar:** Laporan harian/mingguan kehadiran guru di kelas beserta jurnal materi yang disampaikan.
* **Kokurikuler (Proyek P5):** Dokumentasi dan laporan progres kegiatan Proyek Penguatan Profil Pelajar Pancasila.
* **Ekstrakurikuler:** Daftar kegiatan ekskul, jadwal, pembina, dan laporan capaian prestasi siswa.

#### Pilar 3: Laporan Bulanan Wakasek
Sistem pelaporan berkala bagi manajemen sekolah untuk dinilai oleh Pengawas. Terdapat notifikasi otomatis jika laporan belum diunggah melewati tanggal yang ditentukan (misal: tanggal 5 setiap bulan).
* **Wakasek Kurikulum:** Laporan ketercapaian target kurikulum, supervisi akademik internal, dan evaluasi belajar.
* **Wakasek Kesiswaan:** Laporan kehadiran siswa, pelanggaran tata tertib, kegiatan OSIS, dan prestasi.
* **Wakasek Sarpras:** Laporan pemeliharaan gedung, pengadaan barang, dan kondisi aset bulan tersebut.
* **Wakasek Humas:** Laporan kerja sama industri/DU-DI (untuk SMK), komite sekolah, dan kehumasan masyarakat.

#### Pilar 4: Kontrol Rapor Pendidikan
* **Input Data:** Form untuk mengunggah ringkasan nilai Rapor Pendidikan tahunan.
* **Perbandingan:** Fitur untuk membandingkan capaian Rapor Pendidikan tahun berjalan dengan tahun sebelumnya (*Year-on-Year growth*).

#### Pilar 5: Dashboard Grafik Capaian (*The Output*)
Sistem akan mengolah data dari pilar 1-4 untuk menghasilkan *Scoring System* dan visualisasi.
* **Grafik Capaian per Sekolah (Radar/Bar Chart):** Menampilkan skor masing-masing area.
* **Leaderboard Sekolah Binaan:** Peringkat sekolah berdasarkan akumulasi persentase kepatuhan dan kelengkapan.
* **Indikator Warna (Traffic Light System):**
    * **Hijau (80-100%):** Sangat Baik (Data lengkap, laporan tepat waktu).
    * **Kuning (60-79%):** Cukup (Ada laporan tertunda atau RPP belum lengkap).
    * **Merah (<60%):** Kurang (Butuh teguran/kunjungan pembinaan segera).

### 5. Kebutuhan Non-Fungsional (Non-Functional Requirements)
* **Keamanan (Security):** Otentikasi login, *Role-Based Access Control* (RBAC), enkripsi password.
* **Penyimpanan (Storage):** Mendukung format PDF, Excel, Word, Image. Integrasi *cloud storage* disarankan.
* **Aksesibilitas:** *User-friendly* dan *responsive* (bisa dibuka di PC, tablet, maupun smartphone).

---

## BAGIAN 2: DESAIN DATABASE MYSQL

### 1. Master Data & Autentikasi
```sql
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL
);

CREATE TABLE schools (
    id INT AUTO_INCREMENT PRIMARY KEY,
    npsn VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    address TEXT,
    principal_name VARCHAR(100),
    pengawas_id INT, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    school_id INT NULL, 
    role_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);