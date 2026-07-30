# Resume Perubahan

Berikut ringkasan semua perubahan yang telah dilakukan pada sistem SiLawas.

---

## 1. Peta Alumni – Status "Lain-Lain"

### Tujuan
Menambahkan opsi status **Lain-Lain** di samping *Melanjutkan Studi* dan *Bekerja* pada fitur Peta Alumni.

### Perubahan

| Area | Detail |
|------|--------|
| **Migration** | `database/migrations/2026_07_27_100000_add_lain_lain_to_alumni_status.php` — mengubah enum `status` menjadi `['Melanjutkan Studi','Bekerja','Lain-Lain']` |
| **Controller** | `AlumniController@store`, `@update`, `@importAlumni`, `@exportTemplate` — semuanya menangani status Lain-Lain |
| **View Admin** | `resources/views/alumni/index.blade.php` — dropdown filter, toggle Lain-Lain, tabel dengan badge, kartu statistik, info import |
| **View Pengawas** | `resources/views/alumni/pengawas.blade.php` — kartu statistik Lain-Lain, pie chart 3 segmen, tabel |
| **Partial AJAX** | `resources/views/alumni/partials/table-body.blade.php` — menampilkan badge status |
| | `resources/views/alumni/partials/table-body-pengawas.blade.php` — menampilkan badge dan detail status |

---

## 2. Prestasi – Tingkat "Kecamatan"

### Tujuan
Menambahkan opsi tingkat **Kecamatan** pada menu Prestasi (sebelumnya hanya Kota, Provinsi, Nasional, Internasional).

### Perubahan

| Area | Detail |
|------|--------|
| **View Admin Sekolah** | `resources/views/achievements/admin.blade.php` — dropdown pilihan tingkat menyertakan Kecamatan |
| **View Pengawas** | `resources/views/achievements/pengawas.blade.php` — grafik rekap menampilkan segmen Kecamatan dengan warna, label, tooltip |
| **Controller** | `AchievementController@indexPengawas` — data array chart mencakup data Kecamatan |

---

## 3. Jurnal Kepala Sekolah – Foto Kegiatan

### Tujuan
Menambahkan kolom **foto_kegiatan** (opsional) pada Jurnal Kepala Sekolah agar pengguna bisa menyertakan link Google Drive.

### Perubahan

| Area | Detail |
|------|--------|
| **Migration #1** | `2026_07_27_110000_add_foto_kegiatan_to_attendances_table.php` — menambah kolom `string`, nullable |
| **Migration #2** | `2026_07_27_111000_change_foto_kegiatan_to_text_on_attendances_table.php` — mengubah kolom menjadi `TEXT` agar muat URL panjang |
| **Controller** | `SchoolController@storeAttendance` — validasi `nullable|string` (bukan `url`) agar link Google Drive lolos |
| **View Jurnal** | `resources/views/journal/index.blade.php` — input `foto_kegiatan` di modal, kolom tabel "Foto", tampilan error (`$errors->any()`) |
| **Export CSV** | Kolom foto_kegiatan disertakan dalam export |

---

## 4. Border Statistik Spesifik – Light/Dark Mode

### Tujuan
Menyesuaikan border kartu Statistik Spesifik (role Superadmin & Pengawas) agar tampil rapi di mode terang maupun gelap.

### Perubahan

| Area | Detail |
|------|--------|
| **CSS Global** | `resources/views/layouts/app.blade.php` baris ~678 — override class `.border-success` di dark mode menggunakan `rgba(var(--bs-primary-rgb), var(--bs-border-opacity, 1))` agar tidak hitam pekat |
| **View Pengawas** | `resources/views/achievements/pengawas.blade.php` (baris ~102–117) — tiga kartu (Total Prestasi, Siswa, Guru/Tendik) disamakan border-nya: |
| | - Semua pakai `border` + `style="border-color: var(--line) !important;"` |
| | - Tidak ada lagi `border-success` / `border-opacity-25` agar ketebalan border seragam |
| | - Warna teks (hijau untuk Total Prestasi, hitam untuk Siswa/Guru/Tendik) tetap dipertahankan |
| | - Siswa dan Guru/Tendik sebelumnya tidak punya border sama sekali |

### Hasil
Ketiga kartu kini memiliki border tipis dengan ketebalan dan warna yang konsisten, beradaptasi otomatis di light mode (border abu-abu) dan dark mode (border sesuai `--line`).

---

## Struktur File yang Diubah / Ditambahkan

```
database/migrations/
├── 2026_07_27_100000_add_lain_lain_to_alumni_status.php    [BARU]
├── 2026_07_27_110000_add_foto_kegiatan_to_attendances_table.php [BARU]
├── 2026_07_27_111000_change_foto_kegiatan_to_text_on_attendances_table.php [BARU]

app/Http/Controllers/
├── AlumniController.php        [DIUBAH]
├── AchievementController.php   [DIUBAH]
├── SchoolController.php        [DIUBAH]

resources/views/
├── alumni/
│   ├── index.blade.php                    [DIUBAH]
│   ├── pengawas.blade.php                 [DIUBAH]
│   └── partials/
│       ├── table-body.blade.php           [DIUBAH]
│       └── table-body-pengawas.blade.php  [DIUBAH]
├── achievements/
│   ├── admin.blade.php                    [DIUBAH]
│   └── pengawas.blade.php                 [DIUBAH]
├── journal/
│   └── index.blade.php                    [DIUBAH]
└── layouts/
    └── app.blade.php                      [DIUBAH]
```
