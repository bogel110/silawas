<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use App\Models\MonthlyReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Jika yang login adalah Admin Sekolah, langsung arahkan ke sekolahnya
        if ($user->role === 'admin_sekolah') {
            if (! $user->school_id) {
                abort(403, 'Akun admin sekolah belum terhubung ke data sekolah.');
            }

            return redirect()->route('school.show', $user->school_id);
        }

        $this->authorizePengawas();

        // 2. Jika yang login adalah Pengawas/Super Admin, tampilkan Dashboard Utama
        $schools = $this->supervisedSchoolsQuery()->get()->sortByDesc('skor_performa');
        $totalSchools = $schools->count();
        $avgCompletion = $totalSchools > 0 ? $schools->avg('skor_performa') : 0;

        // 3. Progres Modul 2 (Laporan Bulanan) - Berdasarkan Tahun Pelajaran Aktif
        $now = Carbon::now();
        $currentTahunPelajaran = $now->month >= 7 
            ? $now->year . '/' . ($now->year + 1) 
            : ($now->year - 1) . '/' . $now->year;

        $monthlyReports = MonthlyReport::where('tahun_pelajaran', $currentTahunPelajaran)
            ->whereIn('school_id', $schools->pluck('id'))
            ->get();
            
        $modul2Stats = [
            'kurikulum' => $monthlyReports->whereNotNull('kurikulum_link')->where('kurikulum_link', '!=', '')->count(),
            'kesiswaan' => $monthlyReports->whereNotNull('kesiswaan_link')->where('kesiswaan_link', '!=', '')->count(),
            'sarpras' => $monthlyReports->whereNotNull('sarpras_link')->where('sarpras_link', '!=', '')->count(),
            'humas' => $monthlyReports->whereNotNull('humas_link')->where('humas_link', '!=', '')->count(),
        ];

        return view('Dashboard', compact('totalSchools', 'schools', 'avgCompletion', 'modul2Stats', 'currentTahunPelajaran'));
    }
}
