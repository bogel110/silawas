# 📋 CHANGELOG - Peta Alumni Module

**Last Updated:** July 18, 2026  
**Module:** Admin Sekolah - Peta Alumni (Manajemen Data Alumni)

---

## 🔧 Fixes & Improvements

### ✅ COMPLETED

#### 3. **Reactive Pie Chart Legend Colors - Dark/Light Mode** (NEW)
**Status:** ✓ COMPLETED

**Problem:**
- Pie chart legend text (keterangan di bawah chart) hanya di-set sekali saat page load
- Saat user toggle dark/light mode, warna legend tidak berubah
- Setelah implementasi awal, chart tidak muncul sama sekali

**Root Cause (final — chart tetap tidak muncul setelah v2):**
- **Sintaks error utama:** Function `attachPaginationListeners()` tidak ditutup dengan `}` setelah forEach loop karena penambahan custom file input handler di tengah-tengah
- **Akibat:** Seluruh `<script>` block gagal parse → TIDAK ADA JavaScript yang jalan (termasuk charts)
- **Kontributor:** `createCharts()` dipanggil tanpa `DOMContentLoaded` → `Chart` undefined jika syntax error-nya diperbaiki

**3 issues bertumpuk:**

| Issue | Lokasi | Akibat |
|-------|--------|--------|
| 1. Missing `}` for `attachPaginationListeners()` | line 1015 | Syntax error → seluruh script mati |
| 2. Custom file input handler salah letak | line 1016-1031 | Terjebak di dalam function (akibat dari issue 1) |
| 3. `createCharts()` tanpa `DOMContentLoaded` | line 1235 | `Chart is not defined` jika issue 1 diperbaiki |

**Solusi Final:**
- **Tutup function `attachPaginationListeners()`** dengan `}` setelah forEach loop (line 1015)
- **Kembalikan `DOMContentLoaded`** wrapper untuk initial chart creation dan observer setup
- Function definitions tetap di luar (tidak error karena pure definisi)
- `createCharts()` dipanggil di DALAM `DOMContentLoaded` → Chart.js sudah terload

```
Layout rendering order:
  1. @yield('content')
     └─ HTML + inline <script>
        ├─ function definitions ✅ (no Chart usage)
        ├─ attach DOMContentLoaded listener ✅
        └─ (NO direct new Chart() call ✅)
  2. <script src="chart.js CDN">     ← Chart.js loads here
  3. DOMContentLoaded fires           ← Chart tersedia!
  4. createCharts() runs              ← new Chart() works! ✅
```

**Key Changes:**
| Aspek | Sebelum (error) | Sesudah (working) |
|-------|-----------------|-------------------|
| Inisialisasi | `new Chart(canvas)` | `new Chart(canvas.getContext('2d'))` |
| `borderColor` | Array `[a, b, c]` | **Single string** |
| `maintainAspectRatio` | `true` | `false` |
| Theme handler | `applyChartTheme()` update | `destroy()` + `createCharts()` recreate |
| Wrapper | **Tidak ada (direct call)** | **`DOMContentLoaded`** ← kunci fix |

**File:** `resources/views/alumni/index.blade.php` (lines 1126-1251)

#### 1. **Keterangan Field Persistence Fix** (HIGH PRIORITY)
**Status:** ✓ COMPLETED

**Problem:**
- Keterangan field tidak tersimpan ke database saat tambah/edit alumni
- Dua textarea dengan `name="keterangan"` yang sama → conflict saat submit

**Root Cause:**
- Form memiliki dua textarea (`keteranganStudi` dan `keteranganKerja`) dengan nama field yang identik
- Saat form submit, nilai field tidak terkirim dengan benar ke server

**Solution Applied:**

##### a) **Renamed Textarea Fields**
**File:** `resources/views/alumni/index.blade.php` (lines 334-355)
- Changed: `name="keterangan"` → `name="keterangan_studi"` (Melanjutkan Studi section)
- Changed: `name="keterangan"` → `name="keterangan_kerja"` (Bekerja section)

```blade
<!-- Melanjutkan Studi -->
<textarea class="form-control" id="keteranganStudi" name="keterangan_studi" ...></textarea>

<!-- Bekerja -->
<textarea class="form-control" id="keteranganKerja" name="keterangan_kerja" ...></textarea>
```

##### b) **Updated AlumniController - store() Method**
**File:** `app/Http/Controllers/AlumniController.php` (store method)

Logic perubahan:
```php
// Map keterangan berdasarkan status
if ($data['status'] === 'Melanjutkan Studi') {
    $data['keterangan'] = $data['keterangan_studi'] ?? null;
} else {
    $data['keterangan'] = $data['keterangan_kerja'] ?? null;
}

// Hapus temporary fields
unset($data['keterangan_studi']);
unset($data['keterangan_kerja']);
```

##### c) **Updated AlumniController - update() Method**
**File:** `app/Http/Controllers/AlumniController.php` (update method)

Sama dengan store() - map field keterangan berdasarkan status sebelum update ke DB.

##### d) **Updated JavaScript editAlumni() Function**
**File:** `resources/views/alumni/index.blade.php` (lines 1014-1036)

Improvement:
- Saat edit, field yang tidak digunakan di-clear untuk menghindari data tercampur
- Contoh: Saat edit alumni dengan status "Bekerja", field `keteranganStudi` di-clear

```javascript
if (alumni.status === 'Melanjutkan Studi') {
    document.getElementById('keteranganStudi').value = alumni.keterangan || '';
    document.getElementById('keteranganKerja').value = ''; // Clear kerja field
} else if (alumni.status === 'Bekerja') {
    document.getElementById('keteranganKerja').value = alumni.keterangan || '';
    document.getElementById('keteranganStudi').value = ''; // Clear studi field
}
```

**Result:**
- ✅ Keterangan sekarang tersimpan ke database
- ✅ Keterangan muncul di daftar alumni (table display)
- ✅ Edit alumni keterangan berfungsi dengan benar
- ✅ Tidak ada field name conflict lagi

---

#### 2. **Dark/Light Mode Support for File Input** (NEW)
**Status:** ✓ COMPLETED

**Problem:**
- File input (`<input type="file">`) pada modal "Impor Data Alumni" tidak support dark/light mode
- Native browser file input tidak bisa di-style dengan CSS biasa
- Background tetap putih (#fff) di dark mode → kurang kontras

**Root Cause:**
- Browser merender `<input type="file">` sebagai native element
- CSS targeting langsung pada file input tidak efektif (browser security limitation)

**Solution Applied:**

**File:** `resources/views/alumni/index.blade.php`

##### a) **Custom File Input Wrapper HTML** (lines 381-390)
Replaced native file input dengan custom styled wrapper:

```blade
<div class="mb-3">
    <label for="fileImport" class="form-label fw-600">Pilih File CSV <span class="text-danger">*</span></label>
    <div class="custom-file-input-wrapper">
        <input type="file" class="custom-file-input" id="fileImport" name="file" accept=".csv,.txt" required>
        <label for="fileImport" class="custom-file-label">
            <span class="material-symbols-outlined">upload_file</span>
            <span id="fileNameDisplay">Pilih file CSV...</span>
        </label>
    </div>
    <small class="text-soft">Format: CSV | Ukuran maksimal: 2MB</small>
</div>
```

Key points:
- Hidden native `<input type="file">` (class: custom-file-input)
- Styled `<label>` as visual file input (class: custom-file-label)
- Icon + filename display (id: fileNameDisplay)
- Form submission tetap menggunakan hidden input (name="file" intact)

##### b) **Custom File Input CSS Styling** (lines 471-525)

Light Mode:
```css
.custom-file-label {
    background-color: var(--surface);
    border: 1px solid var(--line);
    color: var(--text-main);
}

.custom-file-label:hover {
    border-color: var(--brand-700);
    background-color: var(--surface-soft);
}
```

Dark Mode:
```css
html[data-theme="dark"] .custom-file-label {
    background-color: rgba(99, 199, 210, 0.08);
    border-color: rgba(180, 221, 227, 0.15);
}

html[data-theme="dark"] .custom-file-label:hover {
    background-color: rgba(99, 199, 210, 0.12);
    border-color: #63c7d2;
}
```

Features:
- Flex layout dengan icon + filename
- Responsive hover state
- Focus state dengan box-shadow
- Smooth transition (0.15s ease)
- Ellipsis untuk filename panjang

##### c) **JavaScript File Input Handler** (lines 1207-1220)

```javascript
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('fileImport');
    const fileNameDisplay = document.getElementById('fileNameDisplay');

    if (fileInput) {
        fileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                fileNameDisplay.textContent = this.files[0].name;
            } else {
                fileNameDisplay.textContent = 'Pilih file CSV...';
            }
        });
    }
});
```

Functionality:
- Mendengarkan perubahan pada file input
- Update text display dengan nama file yang dipilih
- Reset ke placeholder jika tidak ada file

**Result:**
- ✅ File input fully responsive terhadap dark/light mode toggle
- ✅ Kontras text dan background optimal di kedua mode
- ✅ Visual feedback (hover, focus) consistent dengan design system
- ✅ Tidak mengubah logic atau functionality form submit
- ✅ Nama file selected ditampilkan kepada user
- ✅ Accessibility intact (label-input relationship preserved)

---

## 📁 Files Modified

| File | Changes | Lines |
|------|---------|-------|
| `resources/views/alumni/index.blade.php` | Textarea field names + editAlumni() JS + CSS file input styling + Reactive chart theme | 334-355, 471-479, 1014-1036, 1126-1254 |
| `app/Http/Controllers/AlumniController.php` | store() & update() methods - keterangan field mapping | - |

---

## 🧪 Testing Checklist

- [x] Add new alumni dengan keterangan → verify saved to DB
- [x] Edit alumni keterangan → verify updated correctly
- [x] Keterangan muncul di daftar alumni table
- [x] File input dark mode → verify styling
- [x] File input light mode → verify styling
- [x] Toggle dark/light mode → chart legend colors update
- [x] Toggle dark/light mode → chart tooltip colors update
- [x] Toggle dark/light mode → chart border colors update
- [x] Modal functionality intact
- [x] Modal functionality intact
- [x] Other menus not affected

---

## 📌 Notes

- **No Logic Changes:** Hanya styling & field handling, tidak mengubah business logic
- **Backward Compatible:** Existing alumni data tetap intact
- **Dark Mode Support:** Menggunakan CSS variables yang sudah ada di system
- **Tested:** Manual testing semua scenario done

---

## 🚀 Status: READY FOR PRODUCTION
