<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index(Request $request )
    {
        $user = auth()->user();
        $selectedSchool = null;
        $schools = [];

        // Logika untuk Pengawas: Ambil semua data sekolah untuk dimasukkan ke dropdown
        if ($user->role === 'pengawas') {
            $schools = \App\Models\School::orderBy('name', 'asc')->get();
            
            // Jika pengawas sudah memilih sekolah dari dropdown
            if ($request->filled('school_id')) {
                $selectedSchool = \App\Models\School::with(['attendances' => function($query) {
                    $query->orderBy('tanggal', 'desc'); // Urutkan jurnal terbaru di atas
                }])->findOrFail($request->school_id);
            }
        } 
        // Logika untuk Admin Sekolah: Langsung tampilkan jurnal sekolahnya sendiri
            elseif ($user->role === 'admin_sekolah') {
            $selectedSchool = \App\Models\School::with(['attendances' => function($query) {
                $query->orderBy('tanggal', 'desc');
            }])->findOrFail($user->school_id);
            
        }
        //dd($selectedSchool);
        return view('journal.index', compact('schools', 'selectedSchool'));
    }
}
