# Ringkasan Perubahan Menu Peta Alumni

## Ringkasan Umum

Perubahan dilakukan pada modul **Peta Alumni** untuk meningkatkan pengelolaan data alumni, pencarian data, pagination, tampilan statistik, serta kompatibilitas tampilan mode terang dan gelap.

## Menu yang Mengalami Perubahan

### 1. Peta Alumni / Daftar Alumni Admin Sekolah

Perubahan yang dilakukan:

- Menambahkan opsi jumlah data per halaman:
  - 5 data
  - 10 data
  - 25 data
  - 50 data
  - 100 data
  - 200 data
- Mempertahankan pagination dinamis berbasis AJAX.
- Mempertahankan fitur live search berbasis AJAX dengan jeda pencarian 300 ms.
- Memperbaiki form tambah dan edit data alumni.
- Menambahkan field keterangan yang menyesuaikan status alumni:
  - **Melanjutkan Studi**
  - **Bekerja**
- Menambahkan pilihan klasifikasi alumni:
  - Melanjutkan studi: PTN, PTS, atau Kedinasan.
  - Jalur penerimaan: SNBP, SNBT, Mandiri, atau Kedinasan.
  - Bekerja: ASN, TNI, POLRI, atau Swasta.
- Memperbaiki tampilan pie chart statistik alumni.
- Memperbaiki inisialisasi chart agar berjalan setelah halaman selesai dimuat.
- Memperbaiki warna dan tampilan legenda chart saat tema berubah.
- Memperbaiki tampilan input file pada modal impor data alumni untuk mode terang dan gelap.

### 2. Peta Alumni Pengawas dan Superadmin

Perubahan yang dilakukan:

- Menambahkan filter berdasarkan sekolah.
- Menambahkan opsi jumlah data per halaman:
  - 5 data
  - 10 data
  - 25 data
  - 50 data
  - 100 data
  - 200 data
- Mempertahankan live search berbasis AJAX.
- Mempertahankan pagination dinamis berbasis AJAX.
- Menampilkan statistik jumlah alumni berdasarkan status dan kategori.
- Menampilkan pie chart data studi dan pekerjaan alumni.
- Menggunakan tampilan bersama untuk pengawas dan superadmin.

### 3. Form dan Modal Data Alumni

Perubahan yang dilakukan:

- Memisahkan nama field textarea keterangan menjadi:
  - `keterangan_studi`
  - `keterangan_kerja`
- Controller memetakan field tersebut ke kolom database `keterangan` sesuai status alumni.
- Field keterangan yang tidak digunakan akan dikosongkan saat proses edit untuk mencegah data tercampur.
- Field form ditampilkan secara kondisional berdasarkan status alumni.

## Perbaikan Teknis

- Memperbaiki error sintaks JavaScript pada fungsi pagination.
- Menempatkan inisialisasi chart di dalam event `DOMContentLoaded`.
- Menggunakan partial view untuk isi tabel dan pagination agar pembaruan data AJAX lebih terstruktur.
- Controller menerima parameter `per_page` dari request untuk mengatur jumlah data yang ditampilkan.
- Struktur database tetap menggunakan satu kolom `keterangan` untuk menyimpan keterangan studi atau pekerjaan.

## File yang Berkaitan

- `app/Http/Controllers/AlumniController.php`
- `resources/views/alumni/index.blade.php`
- `resources/views/alumni/pengawas.blade.php`
- `resources/views/alumni/partials/table-body.blade.php`
- `resources/views/alumni/partials/table-body-pengawas.blade.php`
- `resources/views/alumni/partials/pagination.blade.php`
- `resources/views/alumni/partials/pagination-pengawas.blade.php`
- `resources/views/layouts/app.blade.php`
- `routes/web.php`

## Kesimpulan

Perubahan pada modul Peta Alumni berfokus pada peningkatan kemudahan penggunaan, konsistensi tampilan untuk setiap role, kecepatan pencarian data, fleksibilitas jumlah data per halaman, serta perbaikan penyimpanan field keterangan alumni.
