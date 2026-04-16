<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;

// Semua route di dalam grup ini wajib login (auth)
Route::middleware(['auth'])->group(function () {
    // Kita arahkan halaman utama (/) dan /dashboard ke DashboardController kita
    Route::get('/', [DashboardController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Route untuk Detail Sekolah
    Route::get('/school/{id}', [SchoolController::class, 'show'])->name('school.show');

    // Route BARU untuk menyimpan absensi harian
    Route::post('/school/{id}/attendance', [SchoolController::class, 'storeAttendance'])->name('school.store_attendance');

    // Route BARU untuk menyimpan Laporan Bulanan Wakasek
    Route::post('/school/{id}/monthly-report', [SchoolController::class, 'storeMonthlyReport'])->name('school.store_monthly_report');
    
    // Route BARU untuk update link dokumen master (Modul 1, 2, 4)
    Route::post('/school/{id}/update-links', [SchoolController::class, 'updateLinks'])->name('school.update_links');

    // Route BARU khusus Pengawas untuk menyimpan catatan evaluasi
    Route::post('/school/{id}/catatan', [SchoolController::class, 'updateCatatan'])->name('school.update_catatan');

    // Route untuk Update dan Hapus Laporan Bulanan (Modul 3)
    Route::put('/monthly-report/{id}', [SchoolController::class, 'updateMonthlyReport'])->name('school.update_monthly_report');
    Route::delete('/monthly-report/{id}', [SchoolController::class, 'destroyMonthlyReport'])->name('school.destroy_monthly_report');

    // Route bawaan Breeze untuk ganti password/profil (biarkan saja agar tidak error)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rute Khusus Pengawas -> Administrator
    Route::prefix('administrator')->name('admin.')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    Route::prefix('administrator')->name('admin.')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    
    // TAMBAHKAN BARIS INI:
    Route::put('/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset_password');});

    // Tambahkan baris ini untuk menangani aksi hapus sekolah
    Route::delete('/school/{id}', [SchoolController::class, 'destroy'])->name('school.destroy');
    // Download Excel seluruh sekolaah binaan
    Route::get('/schools/export', [App\Http\Controllers\SchoolController::class, 'exportExcel'])->name('school.export');

    //Download excel rekap kehadiran kepala sekolah
    Route::get('/school/{id}/export-attendance', [App\Http\Controllers\SchoolController::class, 'exportAttendanceExcel'])->name('school.export_attendance');
    
    // Menampilkan Halaman Utama Modul KBM
    Route::get('/kbm', [App\Http\Controllers\KbmController::class, 'index'])->name('kbm.index');
    //Modul KBM
    Route::post('/school/{id}/kbm', [SchoolController::class, 'storeKbm'])->name('school.store_kbm');
    //Edit KBM
    Route::put('/school/kbm/{id}', [App\Http\Controllers\SchoolController::class, 'updateKbm'])->name('school.update_kbm');
    //Delete Kontrol KBM
    Route::delete('/school/kbm/{id}', [App\Http\Controllers\SchoolController::class, 'destroyKbm'])->name('school.destroy_kbm');

    //Menu Jurnal KBM
    Route::get('/jurnal-kepsek', [App\Http\Controllers\AttendanceController::class, 'index'])->name('jurnal.index');
    // Route untuk menghapus absensi harian
    Route::delete('/attendance/{id}', [SchoolController::class, 'destroyAttendance'])->name('attendance.destroy');


    }
);

// Memuat route bawaan otentikasi Laravel (Login, Register, Logout)
 require __DIR__.'/auth.php';