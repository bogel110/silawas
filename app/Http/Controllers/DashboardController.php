<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Jika yang login adalah Admin Sekolah, langsung arahkan ke sekolahnya
        if ($user->role === 'admin_sekolah') {
            return redirect()->route('school.show', $user->school_id);
        }

        // 2. Jika yang login adalah Pengawas, tampilkan Dashboard Utama
        $totalSchools = School::count();
        $schools = School::all()->sortByDesc('skor_performa');
        $avgCompletion = $totalSchools > 0 ? $schools->avg('skor_performa') : 0;

        return view('Dashboard', compact('totalSchools', 'schools', 'avgCompletion'));
    }
}
