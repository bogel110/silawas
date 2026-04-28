<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use App\Models\MentoringCycle;

class MentoringCycleController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizePengawas();

        $schools = School::orderBy('name', 'asc')->get();
        
        // Cek apakah pengawas sudah memilih sekolah dari dropdown
        $selectedSchoolId = $request->get('school_id');
        $selectedSchool = $selectedSchoolId ? School::findOrFail($selectedSchoolId) : null;

        $cycles = collect();
        $recap = [];

        // Jika sekolah sudah dipilih, ambil data dan hitung rekapnya
        if ($selectedSchool) {
            $cycles = MentoringCycle::where('school_id', $selectedSchoolId)
                                    ->orderBy('tanggal', 'desc')
                                    ->get();

            $recap = [
                'total' => $cycles->count(),
                'perencanaan' => $cycles->where('siklus', 'Perencanaan Pendampingan')->count(),
                'perencanaan_prog' => $cycles->where('siklus', 'Pendampingan Perencanaan Program')->count(),
                'pelaksanaan_prog' => $cycles->where('siklus', 'Pendampingan Pelaksanaan Program')->count(),
                'pelaporan' => $cycles->where('siklus', 'Pelaporan Pendampingan')->count(),
            ];
        }

        return view('mentoring.index', compact('schools', 'selectedSchool', 'cycles', 'recap'));
    }

    public function store(Request $request)
    {
        $this->authorizePengawas();

        $data = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'siklus' => 'required|string',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string'
        ]);

        MentoringCycle::create($data);
        return back()->with('success', 'Data Siklus Pendampingan berhasil disimpan!');
    }

    public function update(Request $request, $id)
    {
        $this->authorizePengawas();

        $data = $request->validate([
            'siklus' => 'required|string',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string'
        ]);

        $cycle = MentoringCycle::findOrFail($id);
        $cycle->update($data);
        return back()->with('success', 'Data Siklus Pendampingan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $this->authorizePengawas();

        MentoringCycle::findOrFail($id)->delete();
        return back()->with('success', 'Data Siklus Pendampingan berhasil dihapus!');
    }
    public function export(Request $request)
    {
        $this->authorizePengawas();

        $schoolId = $request->get('school_id');
        $query = MentoringCycle::with('school');
        
        // Jika sedang memilih sekolah tertentu, export data sekolah itu saja
        if ($schoolId) {
            $query->where('school_id', $schoolId);
            $schoolName = School::findOrFail($schoolId)->name;
        } else {
            $schoolName = "Semua_Sekolah";
        }

        $data = $query->latest()->get();
        $filename = "Siklus_Pendampingan_" . str_replace(' ', '_', $schoolName) . "_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Tanggal', 'Nama Sekolah', 'Tahapan Siklus', 'Keterangan'];

        $callback = function() use($data, $columns) {
            $file = fopen('php://output', 'w');
            fputs($file, $bom =(chr(0xEF) . chr(0xBB) . chr(0xBF))); // BOM untuk Excel Indonesia
            fputcsv($file, $columns, ';');

            foreach ($data as $row) {
                fputcsv($file, [
                    \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y'),
                    $row->school->name,
                    $row->siklus,
                    $row->keterangan
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
