<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PengawasBinaanController;

// Semua route di dalam grup ini wajib login (auth)
Route::middleware(['auth'])->group(function () {
    // Kita arahkan halaman utama (/) dan /dashboard ke DashboardController kita
    Route::get('/', [DashboardController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Route untuk Detail Sekolah
    Route::get('/school/{id}', [SchoolController::class, 'show'])->name('school.show');

    // Menu tersendiri untuk rekap Laporan Kegiatan
    Route::get('/laporan-kegiatan', [SchoolController::class, 'laporanKegiatan'])->name('reports.index');

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

    // Route untuk Pengawas/Super Admin menyimpan catatan pada laporan bulanan
    Route::put('/monthly-report/{id}/catatan', [SchoolController::class, 'updateCatatanLaporan'])->name('school.update_catatan_laporan');

    // Route bawaan Breeze untuk ganti password/profil (biarkan saja agar tidak error)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    

    // Rute Khusus Pengawas -> Administrator
    Route::prefix('administrator')->name('admin.')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/import-template', [UserController::class, 'downloadAdminImportTemplate'])->name('users.import_template');
        Route::post('/users/import', [UserController::class, 'importAdmins'])->name('users.import');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::put('/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset_password');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // Rute Khusus Super Admin -> Pengawas dan Sekolah Binaan
    Route::prefix('super-admin')->name('super-admin.')->group(function () {
        Route::get('/pengawas-binaan', [PengawasBinaanController::class, 'index'])->name('pengawas-binaan.index');
        Route::put('/pengawas-binaan/{user}', [PengawasBinaanController::class, 'update'])->name('pengawas-binaan.update');
    });

    // Tambahkan baris ini untuk menangani aksi hapus sekolah
    Route::delete('/school/{id}', [SchoolController::class, 'destroy'])->name('school.destroy');
    
    // Download Excel seluruh sekolaah binaan
    Route::get('/schools/export', [SchoolController::class, 'exportExcel'])->name('school.export');

    //Download excel rekap kehadiran kepala sekolah
    Route::get('/school/{id}/export-attendance', [SchoolController::class, 'exportAttendanceExcel'])->name('school.export_attendance');
    
    // Menampilkan Halaman Utama Modul KBM
    Route::get('/kbm', [App\Http\Controllers\KbmController::class, 'index'])->name('kbm.index');
    //Modul KBM
    Route::post('/school/{id}/kbm', [SchoolController::class, 'storeKbm'])->name('school.store_kbm');
    //Edit KBM
    Route::put('/school/kbm/{id}', [SchoolController::class, 'updateKbm'])->name('school.update_kbm');
    //Delete Kontrol KBM
    Route::delete('/school/kbm/{id}', [SchoolController::class, 'destroyKbm'])->name('school.destroy_kbm');
    //Pengawas/Super Admin update catatan pada KBM
    Route::put('/school/kbm/{id}/catatan', [SchoolController::class, 'updateCatatanKbm'])->name('school.update_catatan_kbm');

    //Menu Jurnal KBM
    Route::get('/jurnal-kepsek', [App\Http\Controllers\AttendanceController::class, 'index'])->name('jurnal.index');
    // Route untuk mengubah dan menghapus absensi harian
    Route::put('/attendance/{id}', [SchoolController::class, 'updateAttendance'])->name('attendance.update');
    Route::delete('/attendance/{id}', [SchoolController::class, 'destroyAttendance'])->name('attendance.destroy');
    
    //Menu Ubah Password
    Route::get('/profile/password', [ProfileController::class, 'editPassword'])->name('profile.password.edit');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Modul Siklus & Strategi Pengawas
    Route::get('/siklus-strategi', [App\Http\Controllers\CycleStrategyController::class, 'index'])->name('strategy.index');
    Route::post('/siklus-strategi', [App\Http\Controllers\CycleStrategyController::class, 'store'])->name('strategy.store');
    Route::put('/siklus-strategi/{id}', [App\Http\Controllers\CycleStrategyController::class, 'update'])->name('strategy.update');
    Route::delete('/siklus-strategi/{id}', [App\Http\Controllers\CycleStrategyController::class, 'destroy'])->name('strategy.destroy');
    Route::get('/siklus-strategi/export', [App\Http\Controllers\CycleStrategyController::class, 'export'])->name('strategy.export');

    // Modul Siklus Pendampingan (Khusus Pengawas)
    Route::get('/siklus-pendampingan', [App\Http\Controllers\MentoringCycleController::class, 'index'])->name('mentoring.index');
    Route::post('/siklus-pendampingan', [App\Http\Controllers\MentoringCycleController::class, 'store'])->name('mentoring.store');
    Route::put('/siklus-pendampingan/{id}', [App\Http\Controllers\MentoringCycleController::class, 'update'])->name('mentoring.update');
    Route::delete('/siklus-pendampingan/{id}', [App\Http\Controllers\MentoringCycleController::class, 'destroy'])->name('mentoring.destroy');
    Route::get('/siklus-pendampingan/export', [App\Http\Controllers\MentoringCycleController::class, 'export'])->name('mentoring.export');

    // Rute Prestasi Khusus ADMIN SEKOLAH
    Route::get('/prestasi', [App\Http\Controllers\AchievementController::class, 'indexAdmin'])->name('achievement.admin');
    Route::post('/prestasi', [App\Http\Controllers\AchievementController::class, 'store'])->name('achievement.store');
    Route::put('/prestasi/{id}', [App\Http\Controllers\AchievementController::class, 'update'])->name('achievement.update');
    Route::delete('/prestasi/{id}', [App\Http\Controllers\AchievementController::class, 'destroy'])->name('achievement.destroy');
    Route::get('/prestasi/export', [App\Http\Controllers\AchievementController::class, 'exportAdmin'])->name('achievement.export');

    // Rute Alumni Khusus ADMIN SEKOLAH
    Route::get('/alumni', [App\Http\Controllers\AlumniController::class, 'index'])->name('alumni.index');
    Route::get('/alumni/search', [App\Http\Controllers\AlumniController::class, 'search'])->name('alumni.search');
    Route::get('/alumni/search-pengawas', [App\Http\Controllers\AlumniController::class, 'searchPengawas'])->name('alumni.search_pengawas');
    Route::get('/alumni/table-data', [App\Http\Controllers\AlumniController::class, 'getTableData'])->name('alumni.table_data');
    Route::get('/alumni/export-template', [App\Http\Controllers\AlumniController::class, 'exportTemplate'])->name('alumni.export_template');
    Route::get('/alumni/export-data', [App\Http\Controllers\AlumniController::class, 'exportData'])->name('alumni.export_data');
    Route::post('/alumni/import', [App\Http\Controllers\AlumniController::class, 'importAlumni'])->name('alumni.import');
    Route::post('/alumni', [App\Http\Controllers\AlumniController::class, 'store'])->name('alumni.store');
    Route::put('/alumni/{id}', [App\Http\Controllers\AlumniController::class, 'update'])->name('alumni.update');
    Route::delete('/alumni/{id}', [App\Http\Controllers\AlumniController::class, 'destroy'])->name('alumni.destroy');

    // Rute Alumni Khusus PENGAWAS & SUPER ADMIN
    Route::get('/peta-alumni', [App\Http\Controllers\AlumniController::class, 'indexPengawas'])->name('alumni.pengawas');
    Route::get('/peta-alumni/search', [App\Http\Controllers\AlumniController::class, 'searchPengawas'])->name('alumni.search_pengawas');
    Route::get('/peta-alumni/table-data', [App\Http\Controllers\AlumniController::class, 'getTableDataPengawas'])->name('alumni.table_data_pengawas');
    Route::get('/peta-alumni/export', [App\Http\Controllers\AlumniController::class, 'exportPengawas'])->name('alumni.export.pengawas');
    Route::get('/rekap-prestasi', [App\Http\Controllers\AchievementController::class, 'indexPengawas'])->name('achievement.pengawas');
    Route::get('/rekap-prestasi/export', [App\Http\Controllers\AchievementController::class, 'exportPengawas'])->name('achievement.export.pengawas');
        
    Route::put('/schools/{id}/update-drive', [SchoolController::class, 'updateDriveLink'])->name('schools.updateDrive');
}
);

// Memuat route bawaan otentikasi Laravel (Login, Register, Logout)
require __DIR__.'/auth.php';
