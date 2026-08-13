<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use App\Models\Attendance;
use App\Models\MonthlyReport;
use App\Models\KbmReport;

class SchoolController extends Controller
{
    public function show($id)
    {
        // Mencari data sekolah beserta relasi absensi dan laporan bulanannya
        $school = School::with(['attendances', 'monthlyReports'])->findOrFail($id);

        $this->authorizeSchoolAccess($school->id);

        // --- TAMBAHAN: Hitung Progres Modul 2 (Berdasarkan Tahun Pelajaran Aktif) ---
        $now = \Carbon\Carbon::now();
        $currentTahunPelajaran = $now->month >= 7 
            ? $now->year . '/' . ($now->year + 1) 
            : ($now->year - 1) . '/' . $now->year;

        $modul2Stats = [
            'kurikulum' => $school->monthlyReports()->where('tahun_pelajaran', $currentTahunPelajaran)->whereNotNull('kurikulum_link')->where('kurikulum_link', '!=', '')->count(),
            'kesiswaan' => $school->monthlyReports()->where('tahun_pelajaran', $currentTahunPelajaran)->whereNotNull('kesiswaan_link')->where('kesiswaan_link', '!=', '')->count(),
            'sarpras'   => $school->monthlyReports()->where('tahun_pelajaran', $currentTahunPelajaran)->whereNotNull('sarpras_link')->where('sarpras_link', '!=', '')->count(),
            'humas'     => $school->monthlyReports()->where('tahun_pelajaran', $currentTahunPelajaran)->whereNotNull('humas_link')->where('humas_link', '!=', '')->count(),
        ];
        // -------------------------------------------------------

        return view('schools.show', compact('school', 'modul2Stats', 'currentTahunPelajaran'));
    }

    public function laporanKegiatan(Request $request)
    {
        $user = auth()->user();
        $schools = collect();
        $school = null;

        if ($user && $user->role === 'admin_sekolah') {
            if (! $user->school_id) {
                abort(403, 'Akun admin sekolah belum terhubung ke data sekolah.');
            }

            $school = School::with('monthlyReports')->findOrFail($user->school_id);
        } elseif ($user && in_array($user->role, ['pengawas', 'super_admin'], true)) {
            $schools = $this->supervisedSchoolsQuery()->orderBy('name')->get();

            if ($request->filled('school_id')) {
                $school = School::with('monthlyReports')->findOrFail($request->school_id);
                $this->authorizeSchoolAccess($school->id);
            }
        } else {
            abort(403, 'Akses ditolak.');
        }

        $now = \Carbon\Carbon::now();
        $currentTahunPelajaran = $now->month >= 7
            ? $now->year . '/' . ($now->year + 1)
            : ($now->year - 1) . '/' . $now->year;

        $modul2Stats = [
            'kurikulum' => 0,
            'kesiswaan' => 0,
            'sarpras'   => 0,
            'humas'     => 0,
        ];

        if ($school) {
            $modul2Stats = [
                'kurikulum' => $school->monthlyReports()->where('tahun_pelajaran', $currentTahunPelajaran)->whereNotNull('kurikulum_link')->where('kurikulum_link', '!=', '')->count(),
                'kesiswaan' => $school->monthlyReports()->where('tahun_pelajaran', $currentTahunPelajaran)->whereNotNull('kesiswaan_link')->where('kesiswaan_link', '!=', '')->count(),
                'sarpras'   => $school->monthlyReports()->where('tahun_pelajaran', $currentTahunPelajaran)->whereNotNull('sarpras_link')->where('sarpras_link', '!=', '')->count(),
                'humas'     => $school->monthlyReports()->where('tahun_pelajaran', $currentTahunPelajaran)->whereNotNull('humas_link')->where('humas_link', '!=', '')->count(),
            ];
        }

        return view('reports.index', compact('schools', 'school', 'modul2Stats', 'currentTahunPelajaran'));
    }

    public function updateDriveLink(Request $request, $id)
    {
        $school = School::findOrFail($id);
        $this->authorizeAdminForSchool($school->id);

        $request->validate(['drive_link' => 'nullable|url']);
        $school->update(['drive_link' => $request->drive_link]);

        return redirect()->back()->with('success', 'Link Contact & Drive Sekolah berhasil diperbarui!');
    }
    
    public function storeAttendance(Request $request, $id)
    {
        $school = School::findOrFail($id);
        $this->authorizeAdminForSchool($school->id);

        $request->validate([
            'siswa_hadir'  => 'required|integer|min:0',
            'guru_hadir'   => 'required|integer|min:0',
            'kepsek_hadir' => 'required|boolean',
            'tupoksi'      => 'required|string',
            'keterangan'   => 'nullable|string',
            'foto_kegiatan' => 'nullable|string',
        ]);

        Attendance::updateOrCreate(
            [
                'school_id' => $id,
                'tanggal'   => now()->format('Y-m-d'),
            ],
            [
                'siswa_hadir'  => $request->siswa_hadir,
                'guru_hadir'   => $request->guru_hadir,
                'kepsek_hadir' => $request->kepsek_hadir,
                'tupoksi'      => $request->tupoksi,
                'keterangan'   => $request->keterangan,
                'foto_kegiatan' => $request->foto_kegiatan,
            ]
        );

        return redirect()->back()->with('success', 'Data Jurnal Kepsek berhasil disimpan / diperbarui!');
    }

    public function storeMonthlyReport(Request $request, $id)
    {
        $school = School::findOrFail($id);
        $this->authorizeAdminForSchool($school->id);

        $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun_pelajaran' => 'required|string',
            'semester' => 'required|string',
            'kurikulum_link' => 'nullable',
            'kesiswaan_link' => 'nullable',
            'sarpras_link' => 'nullable',
            'humas_link' => 'nullable',
        ]);

        $report = MonthlyReport::where([
            'school_id' => $id,
            'bulan' => $request->bulan,
            'tahun_pelajaran' => $request->tahun_pelajaran,
            'semester' => $request->semester,
        ])->first();

        if ($report) {
            $report->update([
                'tahun' => date('Y'),
                'kurikulum_link' => $request->filled('kurikulum_link') ? $request->kurikulum_link : $report->kurikulum_link,
                'kesiswaan_link' => $request->filled('kesiswaan_link') ? $request->kesiswaan_link : $report->kesiswaan_link,
                'sarpras_link' => $request->filled('sarpras_link') ? $request->sarpras_link : $report->sarpras_link,
                'humas_link' => $request->filled('humas_link') ? $request->humas_link : $report->humas_link,
            ]);
        } else {
            MonthlyReport::create([
                'school_id' => $id,
                'bulan' => $request->bulan,
                'tahun_pelajaran' => $request->tahun_pelajaran,
                'semester' => $request->semester,
                'tahun' => date('Y'),
                'kurikulum_link' => $request->kurikulum_link,
                'kesiswaan_link' => $request->kesiswaan_link,
                'sarpras_link' => $request->sarpras_link,
                'humas_link' => $request->humas_link,
            ]);
        }

        return redirect()->back()->with('success', 'Laporan bulanan Wakasek berhasil disimpan!');
    }

    public function updateLinks(Request $request, $id)
    {
        $school = School::findOrFail($id);
        $this->authorizeAdminForSchool($school->id);

        $request->validate([
            'ijop_link' => 'nullable|url',
            'ksp_link' => 'nullable|url', 
            'akreditasi_link' => 'nullable|url', 
            'gtk_link' => 'nullable|url',
            'pd_link' => 'nullable|url',
            'sarpras_link' => 'nullable|url',
            'rapor_link' => 'nullable|url',
            'rkt_link'        => 'nullable|url', 
            'rkas_link'       => 'nullable|url', 
            'contact_link'    => 'nullable|url', 
        ]);

        $school->update($request->only([
            'ijop_link', 'ksp_link', 'akreditasi_link','gtk_link', 'pd_link', 'sarpras_link', 'rapor_link', 'rkt_link', 'rkas_link', 'contact_link',
        ]));

        return redirect()->back()->with('success', 'Tautan dokumen master berhasil diperbarui!');
    }

    public function updateCatatan(Request $request, $id)
    {
        $this->authorizePengawas();

        $request->validate([
            'catatan_pengawas' => 'nullable|string',
        ]);

        $school = School::findOrFail($id);
        $this->authorizeSchoolAccess($school->id);
        $school->update(['catatan_pengawas' => $request->catatan_pengawas]);

        return redirect()->back()->with('success', 'Catatan evaluasi Pengawas berhasil disimpan!');
    }

    public function updateAttendance(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);
        $this->authorizeAdminForSchool($attendance->school_id);

        $data = $request->validate([
            'siswa_hadir' => 'required|integer|min:0',
            'guru_hadir' => 'required|integer|min:0',
            'kepsek_hadir' => 'required|boolean',
            'tupoksi' => 'required|string',
            'keterangan' => 'nullable|string',
            'foto_kegiatan' => 'nullable|string',
        ]);

        $attendance->update($data);

        return redirect()->back()->with('success', 'Data Jurnal Kepsek berhasil diperbarui!');
    }

    public function destroyAttendance($id)
    {
        $attendance = Attendance::findOrFail($id);
        $this->authorizeSchoolAccess($attendance->school_id);

        $attendance->delete();
        return redirect()->back()->with('success', 'Data kehadiran berhasil dihapus!');
    }

    public function updateMonthlyReport(Request $request, $id)
    {
        $report = MonthlyReport::findOrFail($id);
        $this->authorizeAdminForSchool($report->school_id);
        
        $data = $request->validate([
            'tahun_pelajaran' => 'required|string',
            'semester' => 'required|string',
            'kurikulum_link' => 'nullable',
            'kesiswaan_link' => 'nullable',
            'sarpras_link' => 'nullable',
            'humas_link' => 'nullable',
        ]);

        $report->update($data);

        return redirect()->back()->with('success', 'Laporan bulanan berhasil diperbarui!');
    }

    public function updateCatatanLaporan(Request $request, $id)
    {
        $this->authorizePengawas();

        $request->validate([
            'catatan_pengawas' => 'nullable|string',
        ]);

        $report = MonthlyReport::findOrFail($id);
        // Authorization check if the pengawas can access this school
        $this->authorizeSchoolAccess($report->school_id);
        
        $report->update(['catatan_pengawas' => $request->catatan_pengawas]);

        return redirect()->back()->with('success', 'Catatan pengawas untuk laporan bulanan berhasil disimpan!');
    }

    public function destroyMonthlyReport($id)
    {
        $report = MonthlyReport::findOrFail($id);
        $this->authorizeAdminForSchool($report->school_id);

        $report->delete();
        return redirect()->back()->with('success', 'Laporan bulanan berhasil dihapus!');
    }

    public function destroy($id)
    {
        $this->authorizeSuperAdmin();

        $school = School::findOrFail($id);
        $school->delete();

        return redirect()->back()->with('success', 'Data sekolah berhasil dihapus!');
    }

    public function exportExcel()
    {
        $this->authorizePengawas();

        $schools = $this->supervisedSchoolsQuery()->get(); 
        $filename = "Data_Performa_Sekolah_Binaan_" . date('Ymd') . ".csv";

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['No.', 'Nama Sekolah', 'Level', 'Status', 'Skor Performa'];

        $callback = function() use($schools, $columns) {
            $file = fopen('php://output', 'w');
            
            fputs($file, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
            fputcsv($file, $columns, ';');

            $nomor = 1;

            foreach ($schools as $school) {
                $row = [
                    $nomor,
                    $school->name,
                    $school->level,
                    $school->status,
                    $school->skor_performa . '%'
                ];

                fputcsv($file, $row, ';');
                $nomor++;
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportAttendanceExcel(Request $request, $id)
    {
        $bulan = $request->query('bulan');
        $tahun = $request->query('tahun');

        $school = School::with(['attendances' => function($query) use ($bulan, $tahun) {
            if ($bulan) {
                $query->whereMonth('tanggal', $bulan);
            }
            if ($tahun) {
                $query->whereYear('tanggal', $tahun);
            }
            $query->orderBy('tanggal', 'desc');
        }])->findOrFail($id);
        $this->authorizeSchoolAccess($school->id);

        $namaSekolah = str_replace(' ', '_', $school->name);
        $filename = "Rekap_Jurnal_" . $namaSekolah . "_" . ($bulan ? $bulan . "_" : "") . ($tahun ? $tahun . "_" : "") . date('Ymd') . ".csv";

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['No.', 'Tanggal', 'Siswa Hadir', 'Guru Hadir', 'Status Kepsek', 'Tupoksi Kepsek', 'Keterangan', 'Link Foto Kegiatan'];

        $callback = function() use($school, $columns) {
            $file = fopen('php://output', 'w');
            
            fputs($file, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
            fputcsv($file, $columns, ';');

            $nomor = 1;
            foreach ($school->attendances as $absen) {
                $statusKepsek = $absen->kepsek_hadir ? 'Hadir' : 'Tidak Hadir';
                
                $row = [
                    $nomor,
                    $absen->tanggal,
                    $absen->siswa_hadir,
                    $absen->guru_hadir,
                    $statusKepsek,
                    $absen->tupoksi ?? '-',      
                    $absen->keterangan ?? '-',
                    $absen->foto_kegiatan ?? '-',
                ];

                fputcsv($file, $row, ';');
                $nomor++;
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function storeKbm(Request $request, $id)
    {
        $school = School::findOrFail($id);
        $this->authorizeAdminForSchool($school->id);

        $request->validate([
            'tahun_pelajaran' => 'required|string',
            'intra_link'      => 'nullable|url',
            'ko_link'         => 'nullable|url',
            'extra_link'      => 'nullable|url',
        ]);

        KbmReport::updateOrCreate(
            [
                'school_id' => $id,
                'tahun_pelajaran' => $request->tahun_pelajaran,
            ],
            [
                'intra_link' => $request->intra_link,
                'ko_link'    => $request->ko_link,
                'extra_link' => $request->extra_link,
            ]
        );

        return redirect()->back()->with('success', 'Laporan KBM berhasil disimpan!');
    }

    public function updateKbm(Request $request, $id)
    {
        $kbm = KbmReport::findOrFail($id);
        $this->authorizeAdminForSchool($kbm->school_id);

        $request->validate([
            'tahun_pelajaran' => 'required|string',
            'intra_link'      => 'nullable|url',
            'ko_link'         => 'nullable|url',
            'extra_link'      => 'nullable|url',
        ]);

        $kbm->update([
            'tahun_pelajaran' => $request->tahun_pelajaran,
            'intra_link'      => $request->intra_link,
            'ko_link'         => $request->ko_link,
            'extra_link'      => $request->extra_link,
        ]);

        return redirect()->back()->with('success', 'Data rekap KBM berhasil diperbarui!');
    }

    public function destroyKbm($id)
    {
        $kbm = KbmReport::findOrFail($id);
        $this->authorizeAdminForSchool($kbm->school_id);

        $kbm->delete();

        return redirect()->back()->with('success', 'Data laporan KBM berhasil dihapus!');
    }

    public function updateCatatanKbm(Request $request, $id)
    {
        $this->authorizePengawas();

        $request->validate([
            'catatan_pengawas' => 'nullable|string',
        ]);

        $kbm = KbmReport::findOrFail($id);
        // Authorization check if the pengawas can access this school
        $this->authorizeSchoolAccess($kbm->school_id);
        
        $kbm->update(['catatan_pengawas' => $request->catatan_pengawas]);

        return redirect()->back()->with('success', 'Catatan pengawas untuk KBM berhasil disimpan!');
    }
}
