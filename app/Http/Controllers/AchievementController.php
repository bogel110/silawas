<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Achievement;
use App\Models\School;

class AchievementController extends Controller
{
    // ==========================================
    // AREA ADMIN SEKOLAH
    // ==========================================
    public function indexAdmin()
    {
        // Pengecekan Hak Akses Admin (Kebal huruf besar/kecil)
        if (strtolower(auth()->user()->role) !== 'admin_sekolah') {
            abort(403, 'Akses Ditolak: Halaman ini khusus untuk Admin Sekolah.');
        }
        
        $schoolId = auth()->user()->school_id;
        $achievements = Achievement::where('school_id', $schoolId)->orderBy('tanggal', 'desc')->get();
        
        return view('achievements.admin', compact('achievements'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'peringkat' => 'required|string',
            'tingkat' => 'required|string',
            'kategori' => 'required|string',
            'tipe_peserta' => 'required|string',
            'keterangan' => 'required|string'
        ]);

        $data = $request->all();
        // Otomatis mengunci inputan data ke sekolah Admin yang sedang login
        $data['school_id'] = auth()->user()->school_id; 
        
        Achievement::create($data);

        return back()->with('success', 'Data Prestasi berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $achievement = Achievement::findOrFail($id);
        $achievement->update($request->all());
        
        return back()->with('success', 'Data Prestasi berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Achievement::findOrFail($id)->delete();
        
        return back()->with('success', 'Data Prestasi berhasil dihapus!');
    }

    public function exportAdmin()
    {
        if (strtolower(auth()->user()->role) !== 'admin_sekolah') {
            abort(403, 'Akses Ditolak: Hanya Admin Sekolah yang dapat mengunduh data ini.');
        }

        $schoolId = auth()->user()->school_id;
        $data = Achievement::where('school_id', $schoolId)->latest()->get();
        $filename = "Data_Prestasi_Sekolah_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type" => "text/csv", 
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache", 
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0", 
            "Expires" => "0"
        ];

        $callback = function() use($data) {
            $file = fopen('php://output', 'w');
            // Menambahkan BOM agar file CSV dibaca dengan rapi (UTF-8) di Microsoft Excel
            fputs($file, $bom =(chr(0xEF) . chr(0xBB) . chr(0xBF))); 
            
            // Baris Judul Kolom
            fputcsv($file, ['Tanggal', 'Peringkat', 'Tingkat', 'Kategori', 'Peserta', 'Keterangan Lomba'], ';');

            // Baris Isi Data
            foreach ($data as $row) {
                fputcsv($file, [
                    \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y'),
                    $row->peringkat, 
                    $row->tingkat, 
                    $row->kategori, 
                    $row->tipe_peserta, 
                    $row->keterangan
                ], ';');
            }
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    // ==========================================
    // AREA PENGAWAS (Menampilkan Rekap & Grafik)
    // ==========================================
    public function indexPengawas(Request $request)
    {
        // Pengecekan Hak Akses Pengawas (Kebal huruf besar/kecil)
        if (strtolower(auth()->user()->role) !== 'pengawas') {
            abort(403, 'Akses Ditolak: Halaman ini khusus untuk Pengawas Sekolah.');
        }

        $schools = School::orderBy('name', 'asc')->get();
        $selectedSchoolId = $request->get('school_id');
        $selectedSchool = $selectedSchoolId ? School::find($selectedSchoolId) : null;

        $achievements = collect();
        $chartData = [0, 0, 0, 0]; // Urutan: Kota/Kab, Provinsi, Nasional, Internasional

        if ($selectedSchool) {
            $achievements = Achievement::where('school_id', $selectedSchoolId)->orderBy('tanggal', 'desc')->get();
            
            // Hitung kalkulasi angka untuk ditampilkan pada Grafik Chart.js
            $chartData = [
                $achievements->where('tingkat', 'Kota/Kabupaten')->count(),
                $achievements->where('tingkat', 'Provinsi')->count(),
                $achievements->where('tingkat', 'Nasional')->count(),
                $achievements->where('tingkat', 'Internasional')->count(),
            ];
        }

        return view('achievements.pengawas', compact('schools', 'selectedSchool', 'achievements', 'chartData'));
    }
    public function exportPengawas(Request $request)
    {
        if (strtolower(auth()->user()->role) !== 'pengawas') abort(403);

        $schoolId = $request->get('school_id');
        $query = Achievement::with('school');

        // Jika memilih sekolah tertentu, export khusus sekolah itu. Jika tidak, export semua.
        if ($schoolId) {
            $query->where('school_id', $schoolId);
            $schoolName = School::find($schoolId)->name;
        } else {
            $schoolName = "Semua_Sekolah";
        }

        $data = $query->orderBy('tanggal', 'desc')->get();
        $filename = "Rekap_Prestasi_" . str_replace(' ', '_', $schoolName) . "_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($data) {
            $file = fopen('php://output', 'w');
            fputs($file, $bom =(chr(0xEF) . chr(0xBB) . chr(0xBF))); 
            
            // Perhatikan ada tambahan kolom "Nama Sekolah" untuk pengawas
            fputcsv($file, ['Tanggal', 'Nama Sekolah', 'Peringkat', 'Tingkat', 'Kategori', 'Peserta', 'Keterangan Lomba'], ';');

            foreach ($data as $row) {
                fputcsv($file, [
                    \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y'),
                    $row->school->name ?? '-',
                    $row->peringkat, 
                    $row->tingkat, 
                    $row->kategori, 
                    $row->tipe_peserta, 
                    $row->keterangan
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}