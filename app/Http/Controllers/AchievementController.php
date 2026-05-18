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
        $this->authorizeAdminForSchool(auth()->user()->school_id);

        $schoolId = auth()->user()->school_id;
        $achievements = Achievement::where('school_id', $schoolId)->orderBy('tanggal', 'desc')->get();
        return view('achievements.admin', compact('achievements'));
    }

    public function store(Request $request)
    {
        $this->authorizeAdminForSchool(auth()->user()->school_id);

        $data = $request->validate([
            'tanggal'      => 'required|date',
            'peringkat'    => 'required|string',
            'tingkat'      => 'required|string',
            'kategori'     => 'required|string',
            'tipe_peserta' => 'required|string',
            'nama_peserta' => 'required|string|max:255', 
            'keterangan'   => 'required|string'
        ]);

        $this->authorizeAdminForSchool(auth()->user()->school_id);
        $data['school_id'] = auth()->user()->school_id;
        Achievement::create($data);

        return back()->with('success', 'Data Prestasi berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'tanggal'      => 'required|date',
            'peringkat'    => 'required|string',
            'tingkat'      => 'required|string',
            'kategori'     => 'required|string',
            'tipe_peserta' => 'required|string',
            'nama_peserta' => 'required|string|max:255', 
            'keterangan'   => 'required|string'
        ]);

        $achievement = Achievement::findOrFail($id);
        $this->authorizeAdminForSchool($achievement->school_id);

        $achievement->update($data);
        
        return back()->with('success', 'Data Prestasi berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $achievement = Achievement::findOrFail($id);
        $this->authorizeAdminForSchool($achievement->school_id);

        $achievement->delete();
        return back()->with('success', 'Data Prestasi berhasil dihapus!');
    }

    public function exportAdmin()
    {
        $this->authorizeAdminForSchool(auth()->user()->school_id);

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
        $this->authorizePengawas();

        $schools = School::orderBy('name', 'asc')->get();
        $selectedSchoolId = $request->get('school_id');
        $selectedSchool = $selectedSchoolId ? School::findOrFail($selectedSchoolId) : null;

        // 1. DATA GLOBAL (Untuk Grafik & Kartu Angka Paling Atas - Selalu Dihitung)
        $allAchievements = Achievement::all();
        $globalTingkatChart = [
            $allAchievements->where('tingkat', 'Kota/Kabupaten')->count(),
            $allAchievements->where('tingkat', 'Provinsi')->count(),
            $allAchievements->where('tingkat', 'Nasional')->count(),
            $allAchievements->where('tingkat', 'Internasional')->count(),
        ];
        $globalTingkatTipeChart = [
            'siswa' => [
                $allAchievements->where('tingkat', 'Kota/Kabupaten')->where('tipe_peserta', 'Siswa')->count(),
                $allAchievements->where('tingkat', 'Provinsi')->where('tipe_peserta', 'Siswa')->count(),
                $allAchievements->where('tingkat', 'Nasional')->where('tipe_peserta', 'Siswa')->count(),
                $allAchievements->where('tingkat', 'Internasional')->where('tipe_peserta', 'Siswa')->count(),
            ],
            'guru_tendik' => [
                $allAchievements->where('tingkat', 'Kota/Kabupaten')->whereIn('tipe_peserta', ['Guru', 'Tendik'])->count(),
                $allAchievements->where('tingkat', 'Provinsi')->whereIn('tipe_peserta', ['Guru', 'Tendik'])->count(),
                $allAchievements->where('tingkat', 'Nasional')->whereIn('tipe_peserta', ['Guru', 'Tendik'])->count(),
                $allAchievements->where('tingkat', 'Internasional')->whereIn('tipe_peserta', ['Guru', 'Tendik'])->count(),
            ],
        ];
        $globalKategoriTipeChart = [
            'individu' => [
                $allAchievements->where('tipe_peserta', 'Siswa')->where('kategori', 'Individu')->count(),
                $allAchievements->where('tipe_peserta', 'Guru')->where('kategori', 'Individu')->count(),
                $allAchievements->where('tipe_peserta', 'Tendik')->where('kategori', 'Individu')->count(),
            ],
            'tim' => [
                $allAchievements->where('tipe_peserta', 'Siswa')->where('kategori', 'Tim')->count(),
                $allAchievements->where('tipe_peserta', 'Guru')->where('kategori', 'Tim')->count(),
                $allAchievements->where('tipe_peserta', 'Tendik')->where('kategori', 'Tim')->count(),
            ]
        ];

        $totalPrestasi = $allAchievements->count();
        $totalSiswa = $allAchievements->where('tipe_peserta', 'Siswa')->count();
        $totalGuruTendik = $allAchievements->whereIn('tipe_peserta', ['Guru', 'Tendik'])->count();

        // 2. DATA SPESIFIK SEKOLAH & TABEL
        $schoolTingkatChart = [0, 0, 0, 0];
        $schoolTingkatTipeChart = [
            'siswa' => [0, 0, 0, 0],
            'guru_tendik' => [0, 0, 0, 0],
        ];
        $schoolKategoriTipeChart = [
            'individu' => [0, 0, 0],
            'tim' => [0, 0, 0]
        ];
        $schoolTotalPrestasi = 0;
        $schoolTotalSiswa = 0;
        $schoolTotalGuruTendik = 0;

        if ($selectedSchool) {
            $achievements = Achievement::with('school')->where('school_id', $selectedSchoolId)->orderBy('tanggal', 'desc')->get();
            
            // Hitung data HANYA untuk sekolah yang dipilih
            $schoolTingkatChart = [
                $achievements->where('tingkat', 'Kota/Kabupaten')->count(),
                $achievements->where('tingkat', 'Provinsi')->count(),
                $achievements->where('tingkat', 'Nasional')->count(),
                $achievements->where('tingkat', 'Internasional')->count(),
            ];
            $schoolTingkatTipeChart = [
                'siswa' => [
                    $achievements->where('tingkat', 'Kota/Kabupaten')->where('tipe_peserta', 'Siswa')->count(),
                    $achievements->where('tingkat', 'Provinsi')->where('tipe_peserta', 'Siswa')->count(),
                    $achievements->where('tingkat', 'Nasional')->where('tipe_peserta', 'Siswa')->count(),
                    $achievements->where('tingkat', 'Internasional')->where('tipe_peserta', 'Siswa')->count(),
                ],
                'guru_tendik' => [
                    $achievements->where('tingkat', 'Kota/Kabupaten')->whereIn('tipe_peserta', ['Guru', 'Tendik'])->count(),
                    $achievements->where('tingkat', 'Provinsi')->whereIn('tipe_peserta', ['Guru', 'Tendik'])->count(),
                    $achievements->where('tingkat', 'Nasional')->whereIn('tipe_peserta', ['Guru', 'Tendik'])->count(),
                    $achievements->where('tingkat', 'Internasional')->whereIn('tipe_peserta', ['Guru', 'Tendik'])->count(),
                ],
            ];
            $schoolKategoriTipeChart = [
                'individu' => [
                    $achievements->where('tipe_peserta', 'Siswa')->where('kategori', 'Individu')->count(),
                    $achievements->where('tipe_peserta', 'Guru')->where('kategori', 'Individu')->count(),
                    $achievements->where('tipe_peserta', 'Tendik')->where('kategori', 'Individu')->count(),
                ],
                'tim' => [
                    $achievements->where('tipe_peserta', 'Siswa')->where('kategori', 'Tim')->count(),
                    $achievements->where('tipe_peserta', 'Guru')->where('kategori', 'Tim')->count(),
                    $achievements->where('tipe_peserta', 'Tendik')->where('kategori', 'Tim')->count(),
                ]
            ];

            $schoolTotalPrestasi = $achievements->count();
            $schoolTotalSiswa = $achievements->where('tipe_peserta', 'Siswa')->count();
            $schoolTotalGuruTendik = $achievements->whereIn('tipe_peserta', ['Guru', 'Tendik'])->count();

        } else {
            // Jika tidak ada filter, tabel menampilkan semua data
            $achievements = Achievement::with('school')->orderBy('tanggal', 'desc')->get();
        }

        return view('achievements.pengawas', compact(
            'schools', 'selectedSchool', 'achievements',
            'globalTingkatChart', 'globalTingkatTipeChart', 'globalKategoriTipeChart', 'totalPrestasi', 'totalSiswa', 'totalGuruTendik',
            'schoolTingkatChart', 'schoolTingkatTipeChart', 'schoolKategoriTipeChart', 'schoolTotalPrestasi', 'schoolTotalSiswa', 'schoolTotalGuruTendik'
        ));
    }

    public function exportPengawas(Request $request)
    {
        $this->authorizePengawas();

        $schoolId = $request->get('school_id');
        $query = Achievement::with('school');

        // Jika filter sekolah diisi, ambil sekolah tersebut. Jika kosong, export semua.
        if ($schoolId) {
            $query->where('school_id', $schoolId);
            $schoolName = School::findOrFail($schoolId)->name;
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
