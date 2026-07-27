# Dokumentasi Sistem SILAWAS

**Sistem Informasi Laporan dan Pendampingan Satuan Pendidikan**

---

## Daftar Isi

1. [Ikhtisar Sistem](#1-ikhtisar-sistem)
2. [Struktur Database](#2-struktur-database)
3. [Role dan Hak Akses](#3-role-dan-hak-akses)
4. [Modul Aplikasi](#4-modul-aplikasi)
   - 4.1. [Dashboard](#41-dashboard)
   - 4.2. [Detail Sekolah (Modul 1, 2, 4)](#42-detail-sekolah-modul-1-2-4)
   - 4.3. [Jurnal Kepala Sekolah](#43-jurnal-kepala-sekolah)
   - 4.4. [Laporan Bulanan Wakasek (Modul 2)](#44-laporan-bulanan-wakasek-modul-2)
   - 4.5. [KBM (Kegiatan Belajar Mengajar)](#45-kbm-kegiatan-belajar-mengajar)
   - 4.6. [Siklus dan Strategi Pendampingan Pengawas](#46-siklus-dan-strategi-pendampingan)
   - 4.7. [Siklus Pendampingan](#47-siklus-pendampingan)
   - 4.8. [Prestasi / Achievement](#48-prestasi)
   - 4.9. [Peta Alumni](#49-peta-alumni)
   - 4.10. [Manajemen Akun & Administrator](#410-manajemen-akun)
   - 4.11. [Profil Pengguna](#411-profil-pengguna)
5. [Daftar Route Lengkap](#5-daftar-route-lengkap)
6. [Struktur View](#6-struktur-view)
7. [File-file Penting](#7-file-file-penting)

---

## 1. Ikhtisar Sistem

### Deskripsi

SILAWAS adalah aplikasi berbasis web untuk pendataan dan monitoring satuan pendidikan (SMA/SMK). Aplikasi ini digunakan oleh tiga jenis pengguna:

- **Super Admin** — mengelola seluruh sistem, akun pengawas, dan admin sekolah.
- **Pengawas** — memantau dan mengevaluasi sekolah binaan.
- **Admin Sekolah** — mengisi data sekolah, laporan bulanan, data alumni, dan prestasi.

### Tech Stack

| Komponen | Teknologi |
|----------|-----------|
| Framework | Laravel 13 |
| PHP | ^8.3 |
| Database | MySQL / MariaDB |
| CSS | Bootstrap + Tailwind CSS (login) |
| Frontend | Blade templating + JavaScript |
| Chart | Chart.js CDN |
| Calendar | FullCalendar (Siklus Pendampingan) |
| Select | Choices.js (Siklus Pendampingan) |
| Build | Vite + Laravel Breeze |

### Arsitektur Auth

- **Guard:** `web` (session-based).
- **Provider:** Eloquent `User` model.
- **Role:** Disimpan di kolom `users.role` sebagai string.
  - `super_admin`
  - `pengawas`
  - `admin_sekolah`
- **Authorization:** Custom helper di `Controller.php`:
  - `authorizePengawas()` — akses untuk `pengawas` dan `super_admin`.
  - `authorizeSuperAdmin()` — akses khusus `super_admin`.
  - `authorizeSchoolAccess($schoolId)` — akses berdasarkan role dan kepemilikan.
  - `authorizeAdminForSchool($schoolId)` — akses khusus admin sekolah untuk sekolahnya.
  - `supervisedSchoolsQuery()` — query sekolah sesuai hak akses pengawas/super admin.
- **Super Admin Default:**
  - Email: `superadmin@silawas.com`
  - Password: `password123`

---

## 2. Struktur Database

### 2.1. Tabel `users`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint, PK | |
| name | string | Nama pengguna |
| email | string, unique | Email login |
| email_verified_at | timestamp, nullable | |
| password | string (hashed) | |
| role | string, nullable | `super_admin`, `pengawas`, `admin_sekolah` |
| school_id | bigint, FK->schools, nullable | Sekolah tempat admin sekolah bekerja |
| school_name | string, nullable | Nama sekolah (untuk keperluan import) |
| remember_token | string, nullable | |
| created_at / updated_at | timestamp | |

**Relasi:**
- `belongsTo(School)` melalui `school_id`
- `belongsToMany(School)` melalui `pengawas_school` (sekolah binaan pengawas)

### 2.2. Tabel `schools`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint, PK | |
| name | string | Nama sekolah (contoh: SMAN 1 Surabaya) |
| level | string | SMA / SMK |
| status | string | Negeri / Swasta |
| **Modul 1: Administrasi** | | |
| ijop_link | string, nullable | Ijin Operasional |
| ksp_link | string, nullable | Kurikulum Satuan Pendidikan (KSP) |
| akreditasi_link | string, nullable | Akreditasi |
| gtk_link | string, nullable | Data GTK (Guru & Tendik) |
| pd_link | string, nullable | Data Pokok Pendidikan (PD) |
| sarpras_link | string, nullable | Sarana Prasarana |
| **Modul 4: Rapor Pendidikan** | | |
| rapor_link | string, nullable | Rapor Pendidikan |
| **Modul 1: Perencanaan** | | |
| rkt_link | string, nullable | Rencana Kerja Tahunan (RKT) |
| rkas_link | string, nullable | Rencana Kegiatan dan Anggaran Sekolah (RKAS) |
| **Lainnya** | | |
| drive_link | string, nullable | Google Drive sekolah |
| contact_link | string, nullable | Kontak sekolah |
| catatan_pengawas | text, nullable | Catatan evaluasi pengawas |
| created_at / updated_at | timestamp | |

**Relasi:**
- `hasMany(Attendance)` — absensi harian
- `hasMany(MonthlyReport)` — laporan bulanan wakasek
- `hasMany(KbmReport)` — laporan KBM
- `hasMany(Alumni)` — data alumni
- `belongsToMany(User, pengawas_school)` — pengawas binaan

### 2.3. Tabel `pengawas_school` (Pivot)

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint, PK | |
| user_id | bigint, FK->users | ID pengawas |
| school_id | bigint, FK->schools | ID sekolah binaan |
| unique(user_id, school_id) | | |

### 2.4. Tabel `attendances`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint, PK | |
| school_id | bigint, FK->schools | |
| tanggal | date | Tanggal absensi |
| guru_hadir | integer | Jumlah guru hadir |
| siswa_hadir | integer | Jumlah siswa hadir |
| kepsek_hadir | boolean | Kepala sekolah hadir (true/false) |
| tupoksi | string, nullable | Tugas Pokok dan Fungsi Kepala Sekolah |
| keterangan | text, nullable | Keterangan tambahan |
| created_at / updated_at | timestamp | |

### 2.5. Tabel `monthly_reports`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint, PK | |
| school_id | bigint, FK->schools | |
| bulan | integer | 1 (Januari) - 12 (Desember) |
| tahun | year | Tahun laporan |
| tahun_pelajaran | string | Contoh: 2025/2026 |
| semester | string | Contoh: Ganjil, Genap |
| kurikulum_link | string, nullable | Link laporan kurikulum |
| kesiswaan_link | string, nullable | Link laporan kesiswaan |
| sarpras_link | string, nullable | Link laporan sarpras |
| humas_link | string, nullable | Link laporan humas |
| catatan_pengawas | text, nullable | Catatan pengawas untuk laporan ini |
| created_at / updated_at | timestamp | |

### 2.6. Tabel `kbm_reports`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint, PK | |
| school_id | bigint, FK->schools | |
| tahun_pelajaran | string | Contoh: 2025/2026 |
| intra_link | text, nullable | Intrakurikuler (RPP/Modul Ajar) |
| ko_link | text, nullable | Kokurikuler |
| extra_link | text, nullable | Ekstrakurikuler |
| catatan_pengawas | text, nullable | Catatan pengawas |
| created_at / updated_at | timestamp | |

### 2.7. Tabel `cycle_strategies`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint, PK | |
| school_id | bigint, FK->schools | |
| strategy | string | Nama strategi (6 jenis) |
| keterangan | text, nullable | Catatan tambahan |
| created_at / updated_at | timestamp | |

**6 Jenis Strategi:**
1. Penyemaian Perubahan (Seeding Change)
2. Perubahan Segera (Rapid Change)
3. Penguatan Perubahan (Reinforcing Change)
4. Perubahan Berangsur (Gradual Change)
5. Pemicu Perubahan (Triggering Change)
6. Perubahan Berkelanjutan (Sustainable Change)

### 2.8. Tabel `mentoring_cycles`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint, PK | |
| school_id | bigint, FK->schools | |
| siklus | string | Tahapan siklus (4 jenis) |
| tanggal | date | Tanggal pelaksanaan |
| keterangan | text, nullable | Catatan tambahan |
| created_at / updated_at | timestamp | |

**4 Tahapan Siklus Pendampingan:**
1. Perencanaan Pendampingan
2. Pendampingan Perencanaan Program
3. Pendampingan Pelaksanaan Program
4. Pelaporan Pendampingan

### 2.9. Tabel `achievements`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint, PK | |
| school_id | bigint, FK->schools | |
| tanggal | date | Tanggal lomba |
| peringkat | string | Juara 1, 2, 3, Harapan, dll |
| tingkat | string | Kota/Kabupaten, Provinsi, Nasional, Internasional |
| kategori | string | Individu / Tim |
| tipe_peserta | string | Siswa / Guru / Tendik |
| nama_peserta | string, nullable | Nama peserta |
| keterangan | text | Deskripsi lomba |
| link_sertifikat | string, nullable | Link bukti/sertifikat |
| created_at / updated_at | timestamp | |

### 2.10. Tabel `alumnis`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint, PK | |
| school_id | bigint, FK->schools | |
| nama_lengkap | string | Nama alumni |
| tahun_lulus | year | Tahun kelulusan |
| status | enum('Melanjutkan Studi', 'Bekerja') | Status setelah lulus |
| jenis_studi | enum('PTN', 'PTS', 'KEDINASAN'), nullable | Jenis perguruan tinggi |
| jalur_penerimaan | enum('SNBP', 'SNBT', 'MANDIRI', 'KEDINASAN'), nullable | Jalur masuk PT |
| jenis_pekerjaan | enum('ASN', 'TNI', 'POLRI', 'SWASTA'), nullable | Jenis pekerjaan |
| keterangan | text, nullable | Keterangan (nama PT/jurusan atau perusahaan/jabatan) |
| created_at / updated_at | timestamp | |

### 2.11. Tabel Default Laravel

- `password_reset_tokens` — reset password
- `sessions` — session user
- `cache` — cache store
- `cache_locks` — cache locks
- `jobs` — queue jobs
- `job_batches` — queue batches

---

## 3. Role dan Hak Akses

### Super Admin
- Akses ke semua data sekolah binaan (seperti pengawas).
- **Manajemen Akun:** CRUD semua pengguna (`/administrator/users`).
- **Pengawas Binaan:** Atur sekolah binaan untuk tiap pengawas (`/super-admin/pengawas-binaan`).
- **Hapus Sekolah:** Hanya super admin yang bisa menghapus data sekolah.
- Melihat semua halaman pengawas.

### Pengawas
- Melihat dashboard seluruh sekolah binaan.
- Mengelola siklus dan strategi pendampingan.
- Mengelola siklus pendampingan (dengan kalender).
- Melihat rekap jurnal kepsek, laporan bulanan, KBM.
- Melihat data prestasi dan alumni dari sekolah binaan.
- Memberikan catatan evaluasi pada berbagai modul.

### Admin Sekolah
- Terikat pada **satu sekolah** (`school_id`).
- **Modul 1:** Upload link dokumen administrasi sekolah.
- **Modul 2:** Input laporan bulanan wakasek (4 bidang).
- **Jurnal Kepala Sekolah:** Input kehadiran harian.
- **KBM:** Input link dokumen pembelajaran.
- **Prestasi:** Input data prestasi sekolah.
- **Alumni:** Input data alumni, import/export CSV.
- **Profile:** Edit profil dan password sendiri.

---

## 4. Modul Aplikasi

### 4.1. Dashboard

| Aspek | Detail |
|-------|--------|
| Route | `GET /` dan `GET /dashboard` |
| Controller | `DashboardController@index` |
| View | `Dashboard.blade.php` |

**Logika:**
- Admin sekolah langsung diarahkan ke halaman detail sekolah.
- Pengawas/Super Admin melihat:
  - Total sekolah binaan.
  - Rata-rata skor performa sekolah.
  - Progress Modul 2 (Laporan Bulanan) untuk tahun pelajaran aktif.
  - Tabel performa sekolah (urut berdasarkan skor).
- Export CSV performa sekolah.

### 4.2. Detail Sekolah (Modul 1, 2, 4)

| Aspek | Detail |
|-------|--------|
| Route | `GET /school/{id}` |
| Controller | `SchoolController@show` |
| View | `schools/show.blade.php` |

**Fitur:**
- Tampilan detail satu sekolah.
- Skor performa sekolah (berdasarkan kelengkapan link).
- Status label (Berkas Lengkap / Beberapa Berkas Tidak Lengkap / Berkas Kurang Lengkap / Tidak Mengisi).
- **Modul 1:** Input link dokumen (ijop, ksp, akreditasi, gtk, pd, sarpras).
- **Modul 4:** Link rapor pendidikan.
- **Modul 1 (Perencanaan):** Link RKT, RKAS.
- **Link Drive:** Google Drive sekolah.
- **Contact Link:** Kontak sekolah.
- **Catatan Pengawas:** Evaluasi dari pengawas.
- Progress Modul 2 untuk tahun pelajaran aktif.

### 4.3. Jurnal Kepala Sekolah

| Aspek | Detail |
|-------|--------|
| Route | `GET /jurnal-kepsek` |
| Controller | `AttendanceController@index` |
| View | `journal/index.blade.php` |

**Fitur:**
- **Admin Sekolah:** Input jurnal harian (siswa hadir, guru hadir, kepsek hadir, tupoksi, keterangan). Update otomatis (updateOrCreate per tanggal).
- **Pengawas/Super Admin:** Pilih sekolah via dropdown, lihat rekap jurnal.
- Export CSV rekap jurnal.
- Hapus data jurnal.

### 4.4. Laporan Bulanan Wakasek (Modul 2)

| Aspek | Detail |
|-------|--------|
| Route | `GET /laporan-kegiatan` |
| Controller | `SchoolController@laporanKegiatan` |
| View | `reports/index.blade.php` |

**Fitur:**
- **Admin Sekolah:** Input link laporan bulanan dari 4 wakasek:
  - Kurikulum
  - Kesiswaan
  - Sarpras
  - Humas
- Input bulan, tahun pelajaran, semester.
- Update/Create otomatis (updateOrCreate per kombinasi school_id + bulan + tahun_pelajaran + semester).
- **Pengawas/Super Admin:** Pilih sekolah, lihat rekap, beri catatan pengawas untuk tiap laporan.
- Hapus laporan.

### 4.5. KBM (Kegiatan Belajar Mengajar)

| Aspek | Detail |
|-------|--------|
| Route | `GET /kbm` |
| Controller | `KbmController@index` |
| View | `kbm/index.blade.php` |

**Fitur:**
- **Admin Sekolah:** Input link dokumen pembelajaran per tahun pelajaran:
  - Intrakurikuler (RPP/Modul Ajar)
  - Kokurikuler
  - Ekstrakurikuler
- **Pengawas/Super Admin:** Pilih sekolah via dropdown, lihat rekap KBM, beri catatan pengawas.
- **Controller SchoolController** menangani CRUD (store/update/destroy) dan catatan pengawas.

### 4.6. Siklus dan Strategi Pendampingan

| Aspek | Detail |
|-------|--------|
| Route | `GET /siklus-strategi` |
| Controller | `CycleStrategyController@index` |
| View | `strategy/index.blade.php` |

**Fitur:**
- **Khusus Pengawas dan Super Admin.**
- Pilih sekolah dari dropdown.
- Input strategi pendampingan (6 jenis strategi) + keterangan.
- Rekapitulasi global (semua sekolah) + per sekolah.
- Export CSV.

### 4.7. Siklus Pendampingan

| Aspek | Detail |
|-------|--------|
| Route | `GET /siklus-pendampingan` |
| Controller | `MentoringCycleController@index` |
| View | `mentoring/index.blade.php` |

**Fitur:**
- **Khusus Pengawas dan Super Admin.**
- Pilih sekolah via Choices.js dropdown.
- Input siklus pendampingan (4 tahapan), tanggal, keterangan.
- Rekapitulasi per tahapan.
- Kalender (FullCalendar) yang menampilkan jadwal pendampingan.
- Pemilih tahun untuk navigasi kalender.
- Export CSV.

### 4.8. Prestasi

#### Admin Sekolah

| Aspek | Detail |
|-------|--------|
| Route | `GET /prestasi` |
| Controller | `AchievementController@indexAdmin` |
| View | `achievements/admin.blade.php` |

**Fitur:**
- Input prestasi: tanggal, peringkat, tingkat, kategori, tipe_peserta, nama_peserta, keterangan, link_sertifikat.
- CRUD penuh.
- Export CSV.

#### Pengawas / Super Admin

| Aspek | Detail |
|-------|--------|
| Route | `GET /rekap-prestasi` |
| Controller | `AchievementController@indexPengawas` |
| View | `achievements/pengawas.blade.php` |

**Fitur:**
- Pilih sekolah dari dropdown.
- Kartu statistik global (seluruh sekolah binaan) dan per sekolah.
- Grafik tingkat lomba per tipe peserta (Siswa vs Guru/Tendik).
- Grafik kategori per tipe peserta (Individu vs Tim).
- Tabel data prestasi.
- Export CSV.

### 4.9. Peta Alumni

#### Admin Sekolah

| Aspek | Detail |
|-------|--------|
| Route | `GET /alumni` |
| Controller | `AlumniController@index` |
| View | `alumni/index.blade.php` |

**Fitur:**
- Input data alumni:
  - Nama, tahun lulus, status (Melanjutkan Studi / Bekerja).
  - Jika studi: jenis studi (PTN/PTS/KEDINASAN), jalur penerimaan (SNBP/SNBT/MANDIRI/KEDINASAN), keterangan.
  - Jika bekerja: jenis pekerjaan (ASN/TNI/POLRI/SWASTA), keterangan.
- CRUD modal.
- Live search AJAX (debounce 300ms).
- Pagination AJAX dinamis.
- Opsi jumlah data per halaman: 5, 10, 25, 50, 100, 200.
- Statistik kartu: total, melanjutkan studi, bekerja.
- Pie chart studi (PTN/PTS/KEDINASAN) dan pekerjaan (ASN/TNI/POLRI/SWASTA).
- Dukungan dark/light mode untuk chart dan file input.
- Import CSV (template download, upload, validasi).
- Export CSV.
- Export template CSV.

#### Pengawas / Super Admin

| Aspek | Detail |
|-------|--------|
| Route | `GET /peta-alumni` |
| Controller | `AlumniController@indexPengawas` |
| View | `alumni/pengawas.blade.php` |

**Fitur:**
- Filter sekolah dari dropdown.
- Statistik dan pie chart studi/pekerjaan.
- Tabel data alumni dengan pagination dan live search.
- Opsi jumlah data per halaman: 5, 10, 25, 50, 100, 200.
- Export CSV (per sekolah atau seluruh sekolah binaan).

### 4.10. Manajemen Akun

#### Administrator (Super Admin)

| Aspek | Detail |
|-------|--------|
| Route | `GET /administrator/users` |
| Controller | `UserController@index` |
| View | `admin/users/index.blade.php` |

**Fitur:**
- Daftar semua pengguna.
- Tambah pengguna baru (Super Admin / Pengawas / Admin Sekolah).
- Saat tambah Admin Sekolah: buat sekolah baru (firstOrCreate) jika belum ada.
- Edit pengguna (ubah role, nama, email, sekolah).
- Reset password.
- Hapus pengguna (dengan proteksi akun sendiri dan minimal satu super admin).
- Import massal admin sekolah dari file CSV/XLSX.
- Download template format import (CSV atau XLSX).

#### Pengawas Binaan (Super Admin)

| Aspek | Detail |
|-------|--------|
| Route | `GET /super-admin/pengawas-binaan` |
| Controller | `PengawasBinaanController@index` |
| View | `super-admin/pengawas-binaan/index.blade.php` |

**Fitur:**
- Daftar semua pengawas.
- Atur sekolah binaan untuk tiap pengawas (checkbox select).
- Sync many-to-many via pivot table `pengawas_school`.

### 4.11. Profil Pengguna

| Aspek | Detail |
|-------|--------|
| Route | `GET /profile` |
| Controller | `ProfileController@edit` |
| View | `profile/edit.blade.php` |

**Fitur:**
- Edit profil (nama, email).
- Ganti password.
- Hapus akun.

---

## 5. Daftar Route Lengkap

### Authentication

| Method | URI | Controller | Middleware | Nama |
|--------|-----|-----------|------------|------|
| GET | /login | AuthenticatedSessionController@create | guest | login |
| POST | /login | AuthenticatedSessionController@store | guest | - |
| GET | /forgot-password | PasswordResetLinkController@create | guest | password.request |
| POST | /forgot-password | PasswordResetLinkController@store | guest | password.email |
| GET | /reset-password/{token} | NewPasswordController@create | guest | password.reset |
| POST | /reset-password | NewPasswordController@store | guest | password.store |
| GET | /verify-email | EmailVerificationPromptController | auth | verification.notice |
| GET | /verify-email/{id}/{hash} | VerifyEmailController | signed,throttle | verification.verify |
| POST | /email/verification-notification | EmailVerificationNotificationController@store | throttle | verification.send |
| GET | /confirm-password | ConfirmablePasswordController@show | auth | password.confirm |
| POST | /confirm-password | ConfirmablePasswordController@store | auth | - |
| PUT | /password | PasswordController@update | auth | password.update |
| POST | /logout | AuthenticatedSessionController@destroy | auth | logout |

### Dashboard

| Method | URI | Controller | Nama |
|--------|-----|-----------|------|
| GET | / | DashboardController@index | - |
| GET | /dashboard | DashboardController@index | dashboard |

### Profil

| Method | URI | Controller | Nama |
|--------|-----|-----------|------|
| GET | /profile | ProfileController@edit | profile.edit |
| PATCH | /profile | ProfileController@update | profile.update |
| DELETE | /profile | ProfileController@destroy | profile.destroy |
| GET | /profile/password | ProfileController@editPassword | profile.password.edit |
| PUT | /profile/password | ProfileController@updatePassword | profile.password.update |

### Sekolah

| Method | URI | Controller | Nama |
|--------|-----|-----------|------|
| GET | /school/{id} | SchoolController@show | school.show |
| DELETE | /school/{id} | SchoolController@destroy | school.destroy |
| GET | /schools/export | SchoolController@exportExcel | school.export |
| PUT | /schools/{id}/update-drive | SchoolController@updateDriveLink | schools.updateDrive |

### Laporan Kegiatan

| Method | URI | Controller | Nama |
|--------|-----|-----------|------|
| GET | /laporan-kegiatan | SchoolController@laporanKegiatan | reports.index |

### Jurnal Kepala Sekolah

| Method | URI | Controller | Nama |
|--------|-----|-----------|------|
| POST | /school/{id}/attendance | SchoolController@storeAttendance | school.store_attendance |
| GET | /school/{id}/export-attendance | SchoolController@exportAttendanceExcel | school.export_attendance |
| DELETE | /attendance/{id} | SchoolController@destroyAttendance | attendance.destroy |
| GET | /jurnal-kepsek | AttendanceController@index | jurnal.index |

### Laporan Bulanan

| Method | URI | Controller | Nama |
|--------|-----|-----------|------|
| POST | /school/{id}/monthly-report | SchoolController@storeMonthlyReport | school.store_monthly_report |
| PUT | /monthly-report/{id} | SchoolController@updateMonthlyReport | school.update_monthly_report |
| DELETE | /monthly-report/{id} | SchoolController@destroyMonthlyReport | school.destroy_monthly_report |
| PUT | /monthly-report/{id}/catatan | SchoolController@updateCatatanLaporan | school.update_catatan_laporan |

### Link Dokumen

| Method | URI | Controller | Nama |
|--------|-----|-----------|------|
| POST | /school/{id}/update-links | SchoolController@updateLinks | school.update_links |

### Catatan Pengawas

| Method | URI | Controller | Nama |
|--------|-----|-----------|------|
| POST | /school/{id}/catatan | SchoolController@updateCatatan | school.update_catatan |

### KBM

| Method | URI | Controller | Nama |
|--------|-----|-----------|------|
| GET | /kbm | KbmController@index | kbm.index |
| POST | /school/{id}/kbm | SchoolController@storeKbm | school.store_kbm |
| PUT | /school/kbm/{id} | SchoolController@updateKbm | school.update_kbm |
| DELETE | /school/kbm/{id} | SchoolController@destroyKbm | school.destroy_kbm |
| PUT | /school/kbm/{id}/catatan | SchoolController@updateCatatanKbm | school.update_catatan_kbm |

### Siklus dan Strategi

| Method | URI | Controller | Nama |
|--------|-----|-----------|------|
| GET | /siklus-strategi | CycleStrategyController@index | strategy.index |
| POST | /siklus-strategi | CycleStrategyController@store | strategy.store |
| PUT | /siklus-strategi/{id} | CycleStrategyController@update | strategy.update |
| DELETE | /siklus-strategi/{id} | CycleStrategyController@destroy | strategy.destroy |
| GET | /siklus-strategi/export | CycleStrategyController@export | strategy.export |

### Siklus Pendampingan

| Method | URI | Controller | Nama |
|--------|-----|-----------|------|
| GET | /siklus-pendampingan | MentoringCycleController@index | mentoring.index |
| POST | /siklus-pendampingan | MentoringCycleController@store | mentoring.store |
| PUT | /siklus-pendampingan/{id} | MentoringCycleController@update | mentoring.update |
| DELETE | /siklus-pendampingan/{id} | MentoringCycleController@destroy | mentoring.destroy |
| GET | /siklus-pendampingan/export | MentoringCycleController@export | mentoring.export |

### Prestasi

| Method | URI | Controller | Nama |
|--------|-----|-----------|------|
| GET | /prestasi | AchievementController@indexAdmin | achievement.admin |
| POST | /prestasi | AchievementController@store | achievement.store |
| PUT | /prestasi/{id} | AchievementController@update | achievement.update |
| DELETE | /prestasi/{id} | AchievementController@destroy | achievement.destroy |
| GET | /prestasi/export | AchievementController@exportAdmin | achievement.export |
| GET | /rekap-prestasi | AchievementController@indexPengawas | achievement.pengawas |
| GET | /rekap-prestasi/export | AchievementController@exportPengawas | achievement.export.pengawas |

### Alumni

| Method | URI | Controller | Nama |
|--------|-----|-----------|------|
| GET | /alumni | AlumniController@index | alumni.index |
| GET | /alumni/search | AlumniController@search | alumni.search |
| GET | /alumni/search-pengawas | AlumniController@searchPengawas | alumni.search_pengawas |
| GET | /alumni/table-data | AlumniController@getTableData | alumni.table_data |
| GET | /alumni/export-template | AlumniController@exportTemplate | alumni.export_template |
| GET | /alumni/export-data | AlumniController@exportData | alumni.export_data |
| POST | /alumni/import | AlumniController@importAlumni | alumni.import |
| POST | /alumni | AlumniController@store | alumni.store |
| PUT | /alumni/{id} | AlumniController@update | alumni.update |
| DELETE | /alumni/{id} | AlumniController@destroy | alumni.destroy |
| GET | /peta-alumni | AlumniController@indexPengawas | alumni.pengawas |
| GET | /peta-alumni/search | AlumniController@searchPengawas | alumni.search_pengawas |
| GET | /peta-alumni/table-data | AlumniController@getTableDataPengawas | alumni.table_data_pengawas |
| GET | /peta-alumni/export | AlumniController@exportPengawas | alumni.export.pengawas |

### Manajemen Akun

| Method | URI | Controller | Nama |
|--------|-----|-----------|------|
| GET | /administrator/users | UserController@index | admin.users.index |
| POST | /administrator/users | UserController@store | admin.users.store |
| GET | /administrator/users/import-template | UserController@downloadAdminImportTemplate | admin.users.import_template |
| POST | /administrator/users/import | UserController@importAdmins | admin.users.import |
| PUT | /administrator/users/{id} | UserController@update | admin.users.update |
| PUT | /administrator/users/{id}/reset-password | UserController@resetPassword | admin.users.reset_password |
| DELETE | /administrator/users/{id} | UserController@destroy | admin.users.destroy |
| GET | /super-admin/pengawas-binaan | PengawasBinaanController@index | super-admin.pengawas-binaan.index |
| PUT | /super-admin/pengawas-binaan/{user} | PengawasBinaanController@update | super-admin.pengawas-binaan.update |

---

## 6. Struktur View

```
resources/views/
├── Dashboard.blade.php                    # Dashboard utama (pengawas/super admin)
├── welcome.blade.php                      # Halaman selamat datang
├── achievements/
│   ├── admin.blade.php                    # Prestasi - admin sekolah
│   └── pengawas.blade.php                 # Rekap prestasi - pengawas
├── admin/
│   └── users/
│       └── index.blade.php                # Manajemen pengguna - super admin
├── alumni/
│   ├── index.blade.php                    # Peta alumni - admin sekolah
│   ├── pengawas.blade.php                 # Peta alumni - pengawas/super admin
│   └── partials/
│       ├── table-body.blade.php           # Partial tabel alumni (admin)
│       ├── pagination.blade.php           # Partial pagination (admin)
│       ├── table-body-pengawas.blade.php  # Partial tabel alumni (pengawas)
│       └── pagination-pengawas.blade.php  # Partial pagination (pengawas)
├── auth/
│   ├── login.blade.php                    # Halaman login (custom SILAWAS)
│   ├── register.blade.php                 # Registrasi (Breeze)
│   ├── forgot-password.blade.php          # Lupa password
│   ├── reset-password.blade.php           # Reset password
│   ├── confirm-password.blade.php         # Konfirmasi password
│   └── verify-email.blade.php             # Verifikasi email
├── components/
│   ├── application-logo.blade.php         # Logo aplikasi (Breeze)
│   ├── auth-session-status.blade.php      # Status session auth
│   ├── danger-button.blade.php            # Tombol bahaya
│   ├── dropdown.blade.php                 # Dropdown komponen
│   ├── dropdown-link.blade.php            # Link dropdown
│   ├── input-error.blade.php              # Error input
│   ├── input-label.blade.php              # Label input
│   ├── modal.blade.php                    # Modal komponen
│   ├── nav-link.blade.php                 # Navigasi link
│   ├── primary-button.blade.php           # Tombol primer
│   ├── responsive-nav-link.blade.php      # Navigasi responsif
│   ├── secondary-button.blade.php         # Tombol sekunder
│   └── text-input.blade.php               # Input teks
├── journal/
│   └── index.blade.php                    # Jurnal kepala sekolah
├── kbm/
│   └── index.blade.php                    # KBM (Kegiatan Belajar Mengajar)
├── layouts/
│   ├── app.blade.php                      # Layout utama (auth) - sidebar, navbar, Chart.js
│   ├── guest.blade.php                    # Layout guest (Breeze)
│   └── navigation.blade.php               # Navigasi (Breeze)
├── mentoring/
│   └── index.blade.php                    # Siklus pendampingan (dengan FullCalendar)
├── profile/
│   ├── edit.blade.php                     # Edit profil
│   ├── password.blade.php                 # Ganti password
│   └── partials/
│       ├── delete-user-form.blade.php     # Form hapus akun
│       ├── update-password-form.blade.php # Form update password
│       └── update-profile-information-form.blade.php  # Form update profil
├── reports/
│   └── index.blade.php                    # Laporan kegiatan bulanan
├── schools/
│   └── show.blade.php                     # Detail sekolah (Modul 1, 2, 4)
├── strategy/
│   └── index.blade.php                    # Siklus dan strategi pendampingan
└── super-admin/
    └── pengawas-binaan/
        └── index.blade.php                # Atur sekolah binaan pengawas
```

---

## 7. File-file Penting

### Controllers (`app/Http/Controllers/`)

| File | Fungsi Utama |
|------|-------------|
| `Controller.php` | Base controller dengan helper authorisasi |
| `DashboardController.php` | Dashboard utama pengawas/super admin |
| `SchoolController.php` | CRUD sekolah, attendance, monthly report, KBM, catatan |
| `AttendanceController.php` | View jurnal kepsek (multi role) |
| `KbmController.php` | View KBM (multi role) |
| `AlumniController.php` | CRUD alumni, import/export, search, pagination |
| `AchievementController.php` | CRUD prestasi admin + rekap pengawas |
| `CycleStrategyController.php` | Siklus & strategi pendampingan |
| `MentoringCycleController.php` | Siklus pendampingan dengan kalender |
| `UserController.php` | Manajemen user, import admin, reset password |
| `PengawasBinaanController.php` | Atur sekolah binaan pengawas |
| `ProfileController.php` | Edit profil, password, hapus akun |

### Models (`app/Models/`)

| File | Tabel | Relasi Utama |
|------|-------|-------------|
| `User.php` | users | school, supervisedSchools |
| `School.php` | schools | attendances, monthlyReports, kbmReports, alumni, supervisors |
| `Attendance.php` | attendances | school |
| `MonthlyReport.php` | monthly_reports | school |
| `KbmReport.php` | kbm_reports | school |
| `Alumni.php` | alumnis | school |
| `Achievement.php` | achievements | school |
| `CycleStrategy.php` | cycle_strategies | school |
| `MentoringCycle.php` | mentoring_cycles | school |

### Lainnya

| File | Fungsi |
|------|--------|
| `routes/web.php` | Definisi route aplikasi |
| `routes/auth.php` | Route authentication |
| `routes/console.php` | Artisan command |
| `bootstrap/app.php` | Konfigurasi middleware dan routing |
| `config/auth.php` | Konfigurasi autentikasi |
| `CHANGELOG_ALUMNI.md` | Log perubahan modul alumni |
| `RINGKASAN_PERUBAHAN_PETA_ALUMNI.md` | Ringkasan perubahan alumni |
| `PRD.md` | Product requirement document |
