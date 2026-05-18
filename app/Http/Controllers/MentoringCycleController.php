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

        $schools = $this->supervisedSchoolsQuery()->orderBy('name', 'asc')->get();
        
        // Cek apakah pengawas sudah memilih sekolah dari dropdown
        $selectedSchoolId = $request->get('school_id');
        $selectedSchool = $selectedSchoolId ? School::findOrFail($selectedSchoolId) : null;
        if ($selectedSchool) {
            $this->authorizeSchoolAccess($selectedSchool->id);
        }

        $cycles = collect();
        $recap = [];
        $calendarEvents = [];
        $availableYears = [(int) now()->year];
        $initialCalendarMonth = now()->format('Y-m');

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

            $cycleYears = $cycles->pluck('tanggal')
                                ->filter()
                                ->map(fn ($tanggal) => \Carbon\Carbon::parse($tanggal)->year)
                                ->push((int) now()->year);

            $availableYears = range($cycleYears->max(), $cycleYears->min());

            if ($cycles->isNotEmpty()) {
                $initialCalendarMonth = \Carbon\Carbon::parse($cycles->first()->tanggal)->format('Y-m');
            }

            $colors = [
                'Perencanaan Pendampingan' => '#0dcaf0',
                'Pendampingan Perencanaan Program' => '#ffc107',
                'Pendampingan Pelaksanaan Program' => '#0d6efd',
                'Pelaporan Pendampingan' => '#198754',
            ];

            $calendarEvents = $cycles->map(function ($cycle) use ($colors) {
                return [
                    'title' => $cycle->siklus,
                    'start' => \Carbon\Carbon::parse($cycle->tanggal)->format('Y-m-d'),
                    'allDay' => true,
                    'color' => $colors[$cycle->siklus] ?? '#6c757d',
                    'textColor' => $cycle->siklus === 'Pendampingan Perencanaan Program' ? '#18323a' : '#ffffff',
                    'description' => $cycle->keterangan ?: '-',
                    'modalTarget' => '#editModal' . $cycle->id,
                ];
            })->values()->all();
        }

        return view('mentoring.index', compact(
            'schools',
            'selectedSchool',
            'cycles',
            'recap',
            'calendarEvents',
            'availableYears',
            'initialCalendarMonth'
        ));
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

        $this->authorizeSchoolAccess($data['school_id']);

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
        $this->authorizeSchoolAccess($cycle->school_id);
        $cycle->update($data);
        return back()->with('success', 'Data Siklus Pendampingan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $this->authorizePengawas();

        $cycle = MentoringCycle::findOrFail($id);
        $this->authorizeSchoolAccess($cycle->school_id);
        $cycle->delete();
        return back()->with('success', 'Data Siklus Pendampingan berhasil dihapus!');
    }
    public function export(Request $request)
    {
        $this->authorizePengawas();

        $schoolId = $request->get('school_id');
        $query = MentoringCycle::with('school')->whereIn('school_id', $this->supervisedSchoolIds());
        
        // Jika sedang memilih sekolah tertentu, export data sekolah itu saja
        if ($schoolId) {
            $this->authorizeSchoolAccess($schoolId);
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
