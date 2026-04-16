<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use App\Models\KbmReport; // Pastikan model KBM dipanggil

class KbmController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $schools = [];
        $kbms = collect();
        $selectedSchool = null;

        if ($user->role === 'pengawas') {
            $schools = School::orderBy('name', 'asc')->get();
            
            // Ambil semua KBM, urutkan berdasarkan Tahun Pelajaran
            $query = KbmReport::with('school')->orderBy('tahun_pelajaran', 'desc');

            if ($request->filled('school_id')) {
                $query->where('school_id', $request->school_id);
                $selectedSchool = School::find($request->school_id);
            }

            $kbms = $query->get();
        } 
        elseif ($user->role === 'admin_sekolah') {
            $selectedSchool = School::findOrFail($user->school_id);
            // Ambil KBM khusus sekolah admin yang login
            $kbms = KbmReport::where('school_id', $user->school_id)
                                ->orderBy('tahun_pelajaran', 'desc')
                                ->get();
        }

        return view('kbm.index', compact('schools', 'kbms', 'selectedSchool'));
    }

    public function store_kbm()
    {

    }
}