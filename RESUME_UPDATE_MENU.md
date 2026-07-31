# Resume Update Menu SiLawas

Dokumen ini merangkum perubahan menu dan fitur yang telah dilakukan pada aplikasi SiLawas.

## 1. Menu Peta Alumni

### 1.1 Penambahan Status Alumni "Lain-Lain"

Status alumni kini mendukung pilihan tambahan:

- Melanjutkan Studi
- Bekerja
- Lain-Lain

Perubahan mencakup:

- Penambahan enum status alumni melalui migration.
- Form tambah dan edit data alumni.
- Proses import data alumni.
- Template export/import alumni.
- Statistik alumni.
- Tampilan tabel alumni untuk Admin Sekolah.
- Tampilan tabel alumni untuk Pengawas/Superadmin.
- Partial AJAX tabel alumni.

File terkait:

- `database/migrations/2026_07_27_100000_add_lain_lain_to_alumni_status.php`
- `app/Http/Controllers/AlumniController.php`
- `resources/views/alumni/index.blade.php`
- `resources/views/alumni/pengawas.blade.php`
- `resources/views/alumni/partials/table-body.blade.php`
- `resources/views/alumni/partials/table-body-pengawas.blade.php`

### 1.2 Pencarian Sekolah pada Peta Alumni Pengawas/Superadmin

Pada halaman Peta Alumni untuk role Pengawas/Superadmin, filter sekolah telah diperbarui menjadi dropdown custom dengan fitur pencarian.

Perubahan mencakup:

- Dropdown sekolah dapat dicari langsung dari dalam dropdown.
- Hidden select `#schoolSelect` tetap dipertahankan agar logic lama tetap berjalan.
- Parameter `school_id` tetap digunakan seperti sebelumnya.
- Tampilan mendukung dark mode dan light mode.
- Z-index panel filter diperbaiki agar dropdown tidak tertimpa tabel.

File terkait:

- `resources/views/alumni/pengawas.blade.php`

### 1.3 Grafik Peta Alumni Admin Sekolah

Pada halaman Peta Alumni Admin Sekolah, tampilan grafik telah disesuaikan menjadi tiga chart sejajar.

Chart yang ditampilkan:

1. Status Alumni
2. Klasifikasi Studi
3. Klasifikasi Pekerjaan

Perubahan mencakup:

- Layout chart menggunakan tiga kolom.
- Chart Status Alumni menggunakan data statistik existing.
- Tampilan chart disesuaikan dengan referensi desain.
- Warna dan ukuran chart disesuaikan agar lebih konsisten.

File terkait:

- `resources/views/alumni/index.blade.php`

### 1.4 Kartu Statistik Peta Alumni Admin Sekolah

Kartu statistik pada halaman Peta Alumni Admin Sekolah telah diperbarui mengikuti desain referensi.

Perubahan mencakup:

- Tampilan kartu menggunakan ikon di sisi kanan.
- Angka statistik dibuat lebih besar.
- Ikon menggunakan background soft.
- Ditambahkan ornamen lingkaran pada kartu.
- Label kartu kelima diubah dari `Kelengkapan` menjadi `Persentase Lanjut`.
- Nilai Persentase Lanjut dihitung dari jumlah alumni yang melanjutkan studi dibanding total alumni.

Rumus:

```blade
{{ $stats['total'] > 0 ? round(($stats['melanjutkan_studi'] / $stats['total']) * 100) : 0 }}%
```

File terkait:

- `resources/views/alumni/index.blade.php`

## 2. Menu Prestasi

### 2.1 Penambahan Tingkat Kecamatan

Menu Prestasi kini mendukung tingkat prestasi `Kecamatan`.

Perubahan mencakup:

- Dropdown tingkat prestasi pada Admin Sekolah ditambahkan pilihan Kecamatan.
- Grafik rekap prestasi Pengawas/Superadmin menampilkan data tingkat Kecamatan.
- Tooltip, label, dan warna chart disesuaikan.
- Data chart pada controller diperbarui agar menyertakan Kecamatan.

File terkait:

- `resources/views/achievements/admin.blade.php`
- `resources/views/achievements/pengawas.blade.php`
- `app/Http/Controllers/AchievementController.php`

### 2.2 Penyesuaian Border Statistik Spesifik

Pada halaman Prestasi Pengawas/Superadmin, border kartu Statistik Spesifik telah diseragamkan.

Perubahan mencakup:

- Tiga kartu Statistik Spesifik menggunakan border yang sama.
- Border hijau khusus pada kartu Total Prestasi dihapus.
- Tampilan border sudah disesuaikan untuk light mode dan dark mode.

File terkait:

- `resources/views/achievements/pengawas.blade.php`
- `resources/views/layouts/app.blade.php`

## 3. Menu Jurnal Harian Kepala Sekolah

### 3.1 Penambahan Foto Kegiatan

Menu Jurnal Harian Kepala Sekolah kini mendukung input foto kegiatan dalam bentuk link.

Perubahan mencakup:

- Penambahan kolom `foto_kegiatan` pada tabel attendance.
- Tipe kolom diubah menjadi `TEXT` agar dapat menyimpan link panjang, seperti Google Drive.
- Validasi controller menggunakan `nullable|string`.
- Form jurnal ditambahkan input foto kegiatan.
- Tabel jurnal ditambahkan kolom Foto.
- Export CSV menyertakan data foto kegiatan.
- Tampilan error validasi ditambahkan agar pesan error terlihat di halaman.

File terkait:

- `database/migrations/2026_07_27_110000_add_foto_kegiatan_to_attendances_table.php`
- `database/migrations/2026_07_27_111000_change_foto_kegiatan_to_text_on_attendances_table.php`
- `app/Http/Controllers/SchoolController.php`
- `resources/views/journal/index.blade.php`

### 3.2 Tampilan Kalender untuk Pengawas/Superadmin

Tampilan Data Jurnal Harian Kepala Sekolah untuk role Pengawas/Superadmin telah diubah dari tabel menjadi kalender bulanan.

Perubahan mencakup:

- Data jurnal ditampilkan dalam bentuk kalender bulanan.
- Kalender memiliki navigasi bulan sebelumnya dan bulan berikutnya.
- Tombol `Hari Ini` ditambahkan.
- Tampilan pill `Bulan`, `Minggu`, dan `Hari` ditambahkan sebagai elemen UI.
- Header hari menggunakan format SEN sampai MIN.
- Grid kalender menggunakan 42 cell agar layout bulan stabil.
- Data jurnal diproses melalui variabel `$calendarJournals` di Blade.
- Search existing `#searchAbsensi` tetap digunakan untuk memfilter event kalender.
- Tabel lama tetap ada di DOM namun disembunyikan untuk role Pengawas/Superadmin.
- Admin Sekolah tetap menggunakan tampilan tabel lama.

File terkait:

- `resources/views/journal/index.blade.php`

### 3.3 Tombol Hapus Event Kalender

Tombol hapus pada event kalender telah diganti dari teks `Hapus` menjadi ikon sampah.

Perubahan mencakup:

- Tombol lebih ringkas dan tidak memenuhi area event kalender.
- Ikon menggunakan Material Symbols `delete`.
- Style tombol menggunakan class `.journal-calendar-delete-btn`.

File terkait:

- `resources/views/journal/index.blade.php`

## 4. Catatan Verifikasi

Beberapa verifikasi telah dilakukan selama proses perubahan:

- `git diff --check` sudah dijalankan dan tidak menunjukkan error whitespace.
- Verifikasi Blade menggunakan artisan belum dapat dilakukan dari shell/API karena command `php` tidak tersedia di PATH.

Error yang muncul:

```bash
/usr/bin/bash: line 1: php: command not found
```

## 5. Ringkasan File Utama yang Berubah

Berikut file utama yang berkaitan dengan update menu:

- `app/Http/Controllers/AlumniController.php`
- `app/Http/Controllers/AchievementController.php`
- `app/Http/Controllers/SchoolController.php`
- `resources/views/alumni/index.blade.php`
- `resources/views/alumni/pengawas.blade.php`
- `resources/views/alumni/partials/table-body.blade.php`
- `resources/views/alumni/partials/table-body-pengawas.blade.php`
- `resources/views/achievements/admin.blade.php`
- `resources/views/achievements/pengawas.blade.php`
- `resources/views/journal/index.blade.php`
- `resources/views/layouts/app.blade.php`
- `database/migrations/2026_07_27_100000_add_lain_lain_to_alumni_status.php`
- `database/migrations/2026_07_27_110000_add_foto_kegiatan_to_attendances_table.php`
- `database/migrations/2026_07_27_111000_change_foto_kegiatan_to_text_on_attendances_table.php`
