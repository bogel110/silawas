<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Achievement;
use App\Models\School;
use Carbon\Carbon;

class AchievementController extends Controller
{
    // ==========================================
    // AREA ADMIN SEKOLAH
    // ==========================================
    public function indexAdmin()
    {
        if (strtolower(auth()->user()->role) !== 'admin_sekolah') abort(403);
        $schoolId = auth()->user()->school_id;
        $achievements = Achievement::where('school_id', $schoolId)->orderBy('tanggal', 'desc')->get();
        return view('achievements.admin', compact('achievements'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal'      => 'required|date',
            'peringkat'    => 'required|string',
            'tingkat'      => 'required|string',
            'kategori'     => 'required|string',
            'tipe_peserta' => 'required|string',
            'nama_peserta' => 'required|string|max:255', 
            'keterangan'   => 'required|string'
        ]);

        $data = $request->all();
        $data['school_id'] = auth()->user()->school_id; 
        Achievement::create($data);

        return back()->with('success', 'Data Prestasi berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal'      => 'required|date',
            'peringkat'    => 'required|string',
            'tingkat'      => 'required|string',
            'kategori'     => 'required|string',
            'tipe_peserta' => 'required|string',
            'nama_peserta' => 'required|string|max:255', 
            'keterangan'   => 'required|string'
        ]);

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
        if (strtolower(auth()->user()->role) !== 'admin_sekolah') abort(403);

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
            fputs($file, $bom =(chr(0xEF) . chr(0xBB) . chr(0xBF))); 
            
            fputcsv($file, ['Tanggal', 'Peringkat', 'Tingkat', 'Kategori', 'Tipe Peserta', 'Nama Peserta', 'Keterangan Lomba'], ';');

            foreach ($data as $row) {
                fputcsv($file, [
                    Carbon::parse($row->tanggal)->format('d/m/Y'),
                    $row->peringkat, 
                    $row->tingkat, 
                    $row->kategori, 
                    $row->tipe_peserta, 
                    $row->nama_peserta, 
                    $row->keterangan
                ], ';');
            }
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    // ==========================================
    // AREA PENGAWAS (Menyeluruh & Per Sekolah)
    // ==========================================
    public function indexPengawas(Request $request)
    {
        if (strtolower(auth()->user()->role) !== 'pengawas') {
            abort(403, 'Akses Ditolak: Halaman ini khusus untuk Pengawas Sekolah.');
        }

        $schools = School::orderBy('name', 'asc')->get();
        $selectedSchoolId = $request->get('school_id');
        $selectedSchool = $selectedSchoolId ? School::find($selectedSchoolId) : null;

        // 1. DATA GLOBAL (Untuk Grafik & Kartu Angka di Paling Atas)
        $allAchievements = Achievement::all();
        $globalChartData = [
            $allAchievements->where('tingkat', 'Kota/Kabupaten')->count(),
            $allAchievements->where('tingkat', 'Provinsi')->count(),
            $allAchievements->where('tingkat', 'Nasional')->count(),
            $allAchievements->where('tingkat', 'Internasional')->count(),
        ];
        $totalPrestasi = $allAchievements->count();
        $totalSiswa = $allAchievements->where('tipe_peserta', 'Siswa')->count();
        $totalGuruTendik = $allAchievements->whereIn('tipe_peserta', ['Guru', 'Tendik'])->count();

        // 2. DATA TABEL (Berdasarkan Pilihan Filter)
        if ($selectedSchool) {
            $achievements = Achievement::with('school')->where('school_id', $selectedSchoolId)->orderBy('tanggal', 'desc')->get();
        } else {
            // Jika tidak ada sekolah yang dipilih, tampilkan SEMUA data di tabel
            $achievements = Achievement::with('school')->orderBy('tanggal', 'desc')->get();
        }

        return view('achievements.pengawas', compact(
            'schools', 'selectedSchool', 'achievements',
            'globalChartData', 'totalPrestasi', 'totalSiswa', 'totalGuruTendik'
        ));
    }

    public function exportPengawas(Request $request)
    {
        if (strtolower(auth()->user()->role) !== 'pengawas') abort(403);

        $schoolId = $request->get('school_id');
        $query = Achievement::with('school');

        // Jika filter sekolah diisi, ambil sekolah tersebut. Jika kosong, export semua.
        if ($schoolId) {
            $query->where('school_id', $schoolId);
            $schoolName = School::find($schoolId)->name;
        } else {
            $schoolName = "Seluruh_Sekolah_Binaan";
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
            
            fputcsv($file, ['Tanggal', 'Nama Sekolah', 'Peringkat', 'Tingkat', 'Kategori', 'Tipe Peserta', 'Nama Peserta', 'Keterangan Lomba'], ';');

            foreach ($data as $row) {
                fputcsv($file, [
                    Carbon::parse($row->tanggal)->format('d/m/Y'),
                    $row->school->name ?? '-',
                    $row->peringkat, 
                    $row->tingkat, 
                    $row->kategori, 
                    $row->tipe_peserta, 
                    $row->nama_peserta,
                    $row->keterangan
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}