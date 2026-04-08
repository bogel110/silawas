<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;

class SchoolController extends Controller
{
    public function show($id)
    {
        // Mencari data sekolah beserta relasi absensi dan laporan bulanannya
        $school = School::with(['attendances', 'monthlyReports'])->findOrFail($id);

            $filledLinks = 0;
            // Cek semua 9 kolom link
            if ($school->ijop_link) $filledLinks++;
            if ($school->ksp_link) $filledLinks++;
            if ($school->akreditasi_link) $filledLinks++;
            if ($school->gtk_link) $filledLinks++;
            if ($school->pd_link) $filledLinks++;
            if ($school->sarpras_link) $filledLinks++;
            if ($school->rpp_link) $filledLinks++;
            if ($school->ekskul_link) $filledLinks++;
            if ($school->rapor_link) $filledLinks++;

            // Pembagi harus sama yaitu 9
        $school->score = ($filledLinks / 9) * 100;

        return view('schools.show', compact('school'));
    }
    
    public function storeAttendance(Request $request, $id)
    {
        // 1. Validasi input dari form
        $request->validate([
            'siswa_hadir' => 'required|integer|min:0',
            'guru_hadir' => 'required|integer|min:0',
            'kepsek_hadir' => 'required|boolean',
        ]);

        // 2. Dapatkan tanggal hari ini
        $hariIni = now()->format('Y-m-d');

        // 3. Simpan atau Update (Jika hari ini sudah ngisi, maka datanya akan diperbarui)
        \App\Models\Attendance::updateOrCreate(
            [
                'school_id' => $id,
                'tanggal' => $hariIni, // Memastikan data yang diinput hanya untuk hari berjalan
            ],
            [
                'siswa_hadir' => $request->siswa_hadir,
                'guru_hadir' => $request->guru_hadir,
                'kepsek_hadir' => $request->kepsek_hadir,
            ]
        );

        // 4. Kembalikan ke halaman sekolah dengan pesan sukses
        return redirect()->back()->with('success', 'Data absensi hari ini berhasil disimpan!');
    }

    public function storeMonthlyReport(Request $request, $id)
    {
        // 1. Validasi Input (Pastikan formatnya adalah URL/Link)
        $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'kurikulum_link' => 'nullable|url',
            'kesiswaan_link' => 'nullable|url',
            'sarpras_link' => 'nullable|url',
            'humas_link' => 'nullable|url',
        ]);

        // 2. Simpan atau Update Laporan di bulan dan tahun yang dipilih
        \App\Models\MonthlyReport::updateOrCreate(
            [
                'school_id' => $id,
                'bulan' => $request->bulan,
                'tahun' => date('Y'), // Tahun berjalan otomatis
            ],
            [
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
            'ksp_link' => 'nullable|url', // Tambahan
            'akreditasi_link' => 'nullable|url', // Tambahan
            'gtk_link' => 'nullable|url',
            'pd_link' => 'nullable|url',
            'sarpras_link' => 'nullable|url',
            'rpp_link' => 'nullable|url',
            'ekskul_link' => 'nullable|url',
            'rapor_link' => 'nullable|url',
        ]);

        // 3. Update data ke database
        $school->update($request->only([
            'ijop_link', 'ksp_link', 'akreditasi_link','gtk_link', 'pd_link', 'sarpras_link', 'rpp_link', 'ekskul_link', 'rapor_link'
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
        'kurikulum_link' => 'nullable|url',
        'kesiswaan_link' => 'nullable|url',
        'sarpras_link' => 'nullable|url',
        'humas_link' => 'nullable|url',
    ]);

    $report->update($request->only(['kurikulum_link', 'kesiswaan_link', 'sarpras_link', 'humas_link']));

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
 
}