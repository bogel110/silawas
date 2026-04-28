<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use App\Models\CycleStrategy;

class CycleStrategyController extends Controller
{
    public function index(Request $request)
    {  
        // Pastikan HANYA PENGAWAS yang bisa mengakses
        if (auth()->user()->role !== 'pengawas') {
            abort(403, 'Akses Ditolak: Modul ini khusus untuk Pengawas Sekolah.');
        }

        $schools = School::orderBy('name', 'asc')->get();
        
        // Tangkap data sekolah yang dipilih dari Dropdown (URL GET)
        $selectedSchoolId = $request->get('school_id');
        $selectedSchool = $selectedSchoolId ? School::find($selectedSchoolId) : null;

        // Jika sekolah dipilih, ambil data strateginya. Jika tidak, ambil SEMUA data strategi.
        $query = CycleStrategy::with('school');
        
        if ($selectedSchool) {
            $query->where('school_id', $selectedSchoolId);
        }

        $strategies = $query->latest()->get();

        // Ambil SEMUA data strategi khusus untuk Recap, agar selalu merepresentasikan seluruh sekolah
        $allStrategies = CycleStrategy::all();

        // Siapkan Data Rekapitulasi Keseluruhan (Semua Sekolah)
        $recapAll = [
            'total' => $allStrategies->count(),
            'seeding' => $allStrategies->where('strategy', 'Penyemaian Perubahan (Seeding Change)')->count(),
            'rapid' => $allStrategies->where('strategy', 'Perubahan Segera (Rapid Change)')->count(),
            'reinforcing' => $allStrategies->where('strategy', 'Penguatan Perubahan (Reinforcing Change)')->count(),
            'gradual' => $allStrategies->where('strategy', 'Perubahan Berangsur (Gradual Change)')->count(),
            'triggering' => $allStrategies->where('strategy', 'Pemicu Perubahan (Triggering Change)')->count(),
            'sustainable' => $allStrategies->where('strategy', 'Perubahan Berkelanjutan (Sustainable Change)')->count(),
        ];

        // Siapkan Data Rekapitulasi Khusus Sekolah yang Dipilih (Jika ada)
        $recapSchool = null;
        if ($selectedSchool) {
            $recapSchool = [
                'total' => $strategies->count(),
                'seeding' => $strategies->where('strategy', 'Penyemaian Perubahan (Seeding Change)')->count(),
                'rapid' => $strategies->where('strategy', 'Perubahan Segera (Rapid Change)')->count(),
                'reinforcing' => $strategies->where('strategy', 'Penguatan Perubahan (Reinforcing Change)')->count(),
                'gradual' => $strategies->where('strategy', 'Perubahan Berangsur (Gradual Change)')->count(),
                'triggering' => $strategies->where('strategy', 'Pemicu Perubahan (Triggering Change)')->count(),
                'sustainable' => $strategies->where('strategy', 'Perubahan Berkelanjutan (Sustainable Change)')->count(),
            ];
        }

        return view('strategy.index', compact('schools', 'selectedSchool', 'strategies', 'recapAll', 'recapSchool'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'school_id' => 'required|exists:schools,id',
            'strategy' => 'required|string',
            'keterangan' => 'nullable|string'
        ]);

        CycleStrategy::create($request->all());

        return back()->with('success', 'Siklus & Strategi berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $strategy = CycleStrategy::findOrFail($id);
        $strategy->update($request->all());
        return back()->with('success', 'Data strategi berhasil diperbarui!');
    }

    public function destroy($id)
    {
        CycleStrategy::findOrFail($id)->delete();
        return back()->with('success', 'Data strategi berhasil dihapus!');
    }

    // Tambahkan parameter Request untuk menangkap filter sekolah saat Export
    public function export(Request $request) 
    {
        $schoolId = $request->get('school_id');
        $query = CycleStrategy::with('school');

        // Jika sedang memilih sekolah tertentu, export khusus data sekolah itu
        if ($schoolId) {
            $query->where('school_id', $schoolId);
            $schoolName = School::find($schoolId)->name;
        } else {
            $schoolName = "Semua_Sekolah";
        }

        $strategies = $query->latest()->get();
        $filename = "Rekap_Siklus_Strategi_" . str_replace(' ', '_', $schoolName) . "_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // Nama-nama kolom di baris paling atas Excel
        $columns = ['Tanggal', 'Nama Sekolah', 'Skor Performa', 'Strategi Pendampingan', 'Keterangan Tambahan'];

        $callback = function() use($strategies, $columns) {
            $file = fopen('php://output', 'w');
            
            // Tambahkan BOM agar Excel membaca karakter dengan benar (UTF-8)
            fputs($file, $bom =(chr(0xEF) . chr(0xBB) . chr(0xBF))); 
            
            // Tulis baris header dengan pemisah titik koma (;)
            fputcsv($file, $columns, ';');

            // Tulis data per baris dengan pemisah titik koma (;)
            foreach ($strategies as $str) {
                fputcsv($file, [
                    $str->created_at->format('d/m/Y'),
                    $str->school->name ?? '-',
                    ($str->school->skor_performa ?? '0') . '%',
                    $str->strategy,
                    $str->keterangan ?? '-'
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}