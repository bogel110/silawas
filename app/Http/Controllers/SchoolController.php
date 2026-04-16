<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use Illuminate\Support\Facades\Auth;

class SchoolController extends Controller
{
    public function show($id)
    {

        $user = Auth::user();

        // Mencari data sekolah beserta relasi absensi dan laporan bulanannya
        $school = School::with(['attendances', 'monthlyReports'])->findOrFail($id);

        // PROTEKSI: Jika yang login adalah Admin Sekolah
        if ($user->role === 'admin_sekolah') {
            // Bandingkan apakah nama sekolah di URL sama dengan school_name di akunnya
            if ($school->id !== $user->school_id) {
                abort(403, 'Anda tidak memiliki hak akses untuk melihat data sekolah lain.');
            }
        }

        $filledLinks = 0;
        // Cek semua 9 kolom link
        if ($school->ijop_link) $filledLinks++;
        if ($school->ksp_link) $filledLinks++;
        if ($school->akreditasi_link) $filledLinks++;
        if ($school->gtk_link) $filledLinks++;
        if ($school->pd_link) $filledLinks++;
        if ($school->sarpras_link) $filledLinks++;
        if ($school->rapor_link) $filledLinks++;
        if ($school->rkt_link) $filledLinks++;
        if ($school->rkas_link) $filledLinks++;

        // Pembagi harus sama yaitu 9
        $school->score = ($filledLinks / 9) * 100;

        return view('schools.show', compact('school'));
    }
    
    public function storeAttendance(Request $request, $id)
    {
        // 1. Validasi Inputan
        $request->validate([
            'siswa_hadir'  => 'required|integer|min:0',
            'guru_hadir'   => 'required|integer|min:0',
            'kepsek_hadir' => 'required|boolean',
            'tupoksi'      => 'required|string',
            'keterangan'   => 'nullable|string',
        ]);

        // 2. Simpan (Create) atau Ubah (Update) Data
        \App\Models\Attendance::updateOrCreate(
            // Array Pertama: Kondisi Pencarian
            // Mencari data absensi untuk sekolah ini pada tanggal hari ini
            [
                'school_id' => $id,
                'tanggal'   => now()->format('Y-m-d'),
            ],
            // Array Kedua: Data yang akan diisi/diubah
            [
                'siswa_hadir'  => $request->siswa_hadir,
                'guru_hadir'   => $request->guru_hadir,
                'kepsek_hadir' => $request->kepsek_hadir,
                'tupoksi'      => $request->tupoksi,
                'keterangan'   => $request->keterangan,
            ]
        );

        // 3. Kembali ke halaman detail dengan pesan sukses
        return redirect()->back()->with('success', 'Data Jurnal Kepsek berhasil disimpan / diperbarui!');
    }

    public function storeMonthlyReport(Request $request, $id)
    {
        // 1. Validasi Input (Pastikan formatnya adalah URL/Link)
        $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun_pelajaran' => 'required|string',
            'kurikulum_link' => 'nullable',
            'kesiswaan_link' => 'nullable',
            'sarpras_link' => 'nullable',
            'humas_link' => 'nullable',
        ]);

        // 2. Simpan atau Update Laporan di bulan dan tahun yang dipilih
        \App\Models\MonthlyReport::updateOrCreate(
            [
                'school_id' => $id,
                'bulan' => $request->bulan,
                'tahun' => date('Y'), // Tahun berjalan otomatis
            ],
            [
                'tahun_pelajaran' => $request->tahun_pelajaran,
                'kurikulum_link' => $request->kurikulum_link,
                'kesiswaan_link' => $request->kesiswaan_link,
                'sarpras_link' => $request->sarpras_link,
                'humas_link' => $request->humas_link,
            ]
        );

        return redirect()->back()->with('success', 'Laporan bulanan Wakasek berhasil disimpan!');
    }

    public function updateLinks(Request $request, $id)
    {
        // 1. Cari data sekolah
        $school = \App\Models\School::findOrFail($id);

        // 2. Validasi input (Pastikan semuanya berupa URL jika diisi)
        $request->validate([
            'ijop_link' => 'nullable|url',
            'ksp_link' => 'nullable|url', 
            'akreditasi_link' => 'nullable|url', 
            'gtk_link' => 'nullable|url',
            'pd_link' => 'nullable|url',
            'sarpras_link' => 'nullable|url',
            'rapor_link' => 'nullable|url',
            'rkt_link'        => 'nullable|url', // Tambahan baru
            'rkas_link'       => 'nullable|url', // Tambahan baru
        ]);

        // 3. Update data ke database
        $school->update($request->only([
            'ijop_link', 'ksp_link', 'akreditasi_link','gtk_link', 'pd_link', 'sarpras_link', 'rapor_link', 'rkt_link', 'rkas_link'
        ]));

        return redirect()->back()->with('success', 'Tautan dokumen master berhasil diperbarui!');
    }

    public function updateCatatan(Request $request, $id)
    {
        // Pastikan hanya pengawas yang bisa melakukan ini (keamanan ekstra)
        if (auth()->user()->role !== 'pengawas') {
            abort(403, 'Anda tidak memiliki akses untuk memberikan catatan.');
        }

        // Validasi dan simpan data
        $request->validate([
            'catatan_pengawas' => 'nullable|string',
        ]);

        $school = \App\Models\School::findOrFail($id);
        $school->update(['catatan_pengawas' => $request->catatan_pengawas]);

        return redirect()->back()->with('success', 'Catatan evaluasi Pengawas berhasil disimpan!');
    }

    public function destroyAttendance($id)
    {
        $attendance = \App\Models\Attendance::findOrFail($id);
        
        // Keamanan: Pastikan hanya admin sekolah pemilik data atau pengawas yang bisa hapus
        if (auth()->user()->role === 'pengawas' || auth()->user()->school_id === $attendance->school_id) {
            $attendance->delete();
            return redirect()->back()->with('success', 'Data kehadiran berhasil dihapus!');
        }

        abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
    }

    public function updateMonthlyReport(Request $request, $id)
    {
        $report = \App\Models\MonthlyReport::findOrFail($id);
        
        $request->validate([
            'kurikulum_link' => 'nullable',
            'kesiswaan_link' => 'nullable',
            'sarpras_link' => 'nullable',
            'humas_link' => 'nullable',
        ]);

        $report->update($request->only(['tahun_pelajaran','kurikulum_link', 'kesiswaan_link', 'sarpras_link', 'humas_link']));

        return redirect()->back()->with('success', 'Laporan bulanan berhasil diperbarui!');
    }

    public function destroyMonthlyReport($id)
    {
        $report = \App\Models\MonthlyReport::findOrFail($id);
        
        // Proteksi: Hanya admin sekolah bersangkutan atau pengawas
        if (auth()->user()->role === 'pengawas' || auth()->user()->school_id === $report->school_id) {
            $report->delete();
            return redirect()->back()->with('success', 'Laporan bulanan berhasil dihapus!');
        }

        abort(403);
    }

    public function destroy($id)
    {
        $school = \App\Models\School::findOrFail($id);
        
        // Hapus sekolah
        $school->delete();

        return redirect()->back()->with('success', 'Data sekolah berhasil dihapus!');
    }

    public function exportExcel()
    {
        // Ambil semua data sekolah
        $schools = \App\Models\School::all(); 
        $filename = "Data_Performa_Sekolah_Binaan_" . date('Ymd') . ".csv";

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // Tambahkan kolom nomor (No.) agar lebih rapi
        $columns = ['No.', 'Nama Sekolah', 'Level', 'Status', 'Skor Performa'];

        $callback = function() use($schools, $columns) {
            $file = fopen('php://output', 'w');
            
            // 1. BOM untuk UTF-8 (agar karakter rapi di Excel)
            fputs($file, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

            // 2. Tulis Header dengan separator titik koma (;)
            fputcsv($file, $columns, ';');

            $nomor = 1;

            foreach ($schools as $school) {
                
                // MENGHITUNG SKOR SECARA DINAMIS (Sama seperti di fungsi show)
                $filledLinks = 0;
                if ($school->ijop_link) $filledLinks++;
                if ($school->ksp_link) $filledLinks++;
                if ($school->akreditasi_link) $filledLinks++;
                if ($school->gtk_link) $filledLinks++;
                if ($school->pd_link) $filledLinks++;
                if ($school->sarpras_link) $filledLinks++;
                if ($school->rapor_link) $filledLinks++;

                // Perhitungan skor
                $calculatedScore = ($filledLinks / 9) * 100;
                
                // Memformat skor menjadi 1 angka desimal + menambahkan simbol %
                $formattedScore = number_format($calculatedScore, 1) . '%';

                $row = [
                    $nomor,
                    $school->name,
                    $school->level,
                    $school->status,
                    $formattedScore
                ];

                // Tulis Baris dengan separator titik koma (;)
                fputcsv($file, $row, ';');
                
                $nomor++;
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportAttendanceExcel($id)
{
    // 1. Ambil data sekolah beserta data absensi yang berelasi
    $school = \App\Models\School::with(['attendances' => function($query) {
        $query->orderBy('tanggal', 'desc');
    }])->findOrFail($id);

    // 2. Format nama file excel agar mengandung nama sekolah
    $namaSekolah = str_replace(' ', '_', $school->name);
    $filename = "Rekap_Jurnal_" . $namaSekolah . "_" . date('Ymd') . ".csv";

    $headers = [
        "Content-type"        => "text/csv; charset=UTF-8",
        "Content-Disposition" => "attachment; filename=$filename",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    // 3. Header kolom Excel (Menambahkan Tupoksi dan Keterangan)
    $columns = ['No.', 'Tanggal', 'Siswa Hadir', 'Guru Hadir', 'Status Kepsek', 'Tupoksi Kepsek', 'Keterangan'];

    $callback = function() use($school, $columns) {
        $file = fopen('php://output', 'w');
        
        // BOM (Byte Order Mark) agar kompatibel dengan Excel (menangani karakter spesial/UTF-8)
        fputs($file, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
        
        // Tulis Header menggunakan titik koma (;)
        fputcsv($file, $columns, ';');

        $nomor = 1;
        foreach ($school->attendances as $absen) {
            $statusKepsek = $absen->kepsek_hadir ? 'Hadir' : 'Tidak Hadir';
            
            // 4. Masukkan data ke dalam baris (Gunakan ?? '-' jika data kosong)
            $row = [
                $nomor,
                $absen->tanggal,
                $absen->siswa_hadir,
                $absen->guru_hadir,
                $statusKepsek,
                $absen->tupoksi ?? '-',      // Tambahan Kolom Tupoksi
                $absen->keterangan ?? '-'    // Tambahan Kolom Keterangan
            ];

            // Tulis baris data menggunakan titik koma (;)
            fputcsv($file, $row, ';');
            $nomor++;
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
    }

    public function storeKbm(Request $request, $id)
    {
    $request->validate([
        'tahun_pelajaran' => 'required|string',
        'intra_link'      => 'nullable|url',
        'ko_link'         => 'nullable|url',
        'extra_link'      => 'nullable|url',
    ]);

    \App\Models\KbmReport::updateOrCreate(
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
        $kbm = \App\Models\KbmReport::findOrFail($id);

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
        $kbm = \App\Models\KbmReport::findOrFail($id);
        $kbm->delete();

        return redirect()->back()->with('success', 'Data laporan KBM berhasil dihapus!');
    }
}