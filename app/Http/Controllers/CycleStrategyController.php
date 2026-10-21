<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use App\Models\CycleStrategy;
use Illuminate\Validation\Rule;

class CycleStrategyController extends Controller
{
    private const STRATEGIES = [
        'Penyemaian Perubahan (Seeding Change)',
        'Perubahan Segera (Rapid Change)',
        'Penguatan Perubahan (Reinforcing Change)',
        'Perubahan Berangsur (Gradual Change)',
        'Pemicu Perubahan (Triggering Change)',
        'Perubahan Berkelanjutan (Sustainable Change)',
    ];

    public function index(Request $request)
    {  
        $this->authorizePengawas();

        $schools = $this->supervisedSchoolsQuery()->orderBy('name', 'asc')->get();
        
        // Tangkap data sekolah yang dipilih dari Dropdown (URL GET)
        $selectedSchoolId = $request->get('school_id');
        $selectedSchool = $selectedSchoolId ? School::findOrFail($selectedSchoolId) : null;
        if ($selectedSchool) {
            $this->authorizeSchoolAccess($selectedSchool->id);
        }

        // Jika sekolah dipilih, ambil data strateginya. Jika tidak, ambil SEMUA data strategi.
        $schoolIds = $schools->pluck('id');
        $query = CycleStrategy::with('school')->whereIn('school_id', $schoolIds);
        
        if ($selectedSchool) {
            $query->where('school_id', $selectedSchoolId);
        }

        $strategies = $query->latest()->get();

        // Ambil SEMUA data strategi khusus untuk Recap, agar selalu merepresentasikan seluruh sekolah
        $allStrategies = CycleStrategy::whereIn('school_id', $schoolIds)->get();

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

    private const TAHAP_PENDAMPINGAN = [
        'Pendampingan Perencanaan Program',
        'Pelaksanaan Program',
        'Evaluasi dan Umpan Balik',
    ];

    private const METODE_PENDAMPINGAN = [
        'Training',
        'Consulting',
        'Mentoring',
        'Coaching',
        'Fasilitating',
    ];

    public function store(Request $request)
    {
        $this->authorizePengawas();

        $data = $request->validate([
            'school_id'            => 'required|exists:schools,id',
            'strategy'             => ['required', 'string', Rule::in(self::STRATEGIES)],
            'tahap_pendampingan'   => ['nullable', 'string', Rule::in(self::TAHAP_PENDAMPINGAN)],
            'fokus_sasaran'        => 'nullable|string',
            'metode_pendampingan'  => ['nullable', 'string', Rule::in(self::METODE_PENDAMPINGAN)],
            'langkah_rencana_aksi' => 'nullable|string',
            'output_indikator'     => 'nullable|string',
            'jadwal_pendampingan'  => 'nullable|date',
        ]);

        $this->authorizeSchoolAccess($data['school_id']);

        CycleStrategy::create($data);

        return back()->with('success', 'Siklus & Strategi berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $this->authorizePengawas();

        $data = $request->validate([
            'strategy'             => ['required', 'string', Rule::in(self::STRATEGIES)],
            'tahap_pendampingan'   => ['nullable', 'string', Rule::in(self::TAHAP_PENDAMPINGAN)],
            'fokus_sasaran'        => 'nullable|string',
            'metode_pendampingan'  => ['nullable', 'string', Rule::in(self::METODE_PENDAMPINGAN)],
            'langkah_rencana_aksi' => 'nullable|string',
            'output_indikator'     => 'nullable|string',
            'jadwal_pendampingan'  => 'nullable|date',
        ]);

        $strategy = CycleStrategy::findOrFail($id);
        $this->authorizeSchoolAccess($strategy->school_id);
        $strategy->update($data);
        return back()->with('success', 'Data strategi berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $this->authorizePengawas();

        $strategy = CycleStrategy::findOrFail($id);
        $this->authorizeSchoolAccess($strategy->school_id);
        $strategy->delete();
        return back()->with('success', 'Data strategi berhasil dihapus!');
    }

    // Tambahkan parameter Request untuk menangkap filter sekolah saat Export
    public function export(Request $request) 
    {
        $this->authorizePengawas();

        $schoolId = $request->get('school_id');
        abort_unless($schoolId, 404, 'Pilih sekolah binaan terlebih dahulu untuk mengunduh rekap.');
        $this->authorizeSchoolAccess($schoolId);

        $school = School::findOrFail($schoolId);
        $strategies = CycleStrategy::with('school')
            ->where('school_id', $school->id)
            ->latest()
            ->get();
        $schoolName = $school->name;
        $principalName = \App\Models\User::where('school_id', $school->id)
            ->where('role', 'admin_sekolah')
            ->value('name') ?? '';
        $supervisorName = auth()->user()->name;
        $filename = "Rekap_Siklus_Strategi_" . str_replace(' ', '_', $schoolName) . "_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $detailColumns = [
            'No',
            'Strategi Pendampingan',
            'Tahap Pendampingan',
            'Fokus/Sasaran Pendampingan',
            'Metode Pendampingan',
            'Langkah/Rencana Aksi',
            'Output dan Indikator',
            'Jadwal/Waktu Pendampingan',
        ];

        $callback = function() use($strategies, $detailColumns, $supervisorName, $schoolName, $principalName) {
            $file = fopen('php://output', 'w');
            
            // Tambahkan BOM agar Excel membaca karakter dengan benar (UTF-8)
            fputs($file, $bom =(chr(0xEF) . chr(0xBB) . chr(0xBF))); 
            
            // Informasi identitas rekap: label di kolom A, tanda titik dua dan nilai di kolom B.
            fputcsv($file, ['Nama Pengawas', ': ' . $supervisorName], ';');
            fputcsv($file, ['Unit Kerja', ':'], ';');
            fputcsv($file, ['Nama Sekolah', ': ' . $schoolName], ';');
            fputcsv($file, ['Nama Kepala Sekolah', ': ' . $principalName], ';');
            fputcsv($file, ['NPSN', ':'], ';');
            fputcsv($file, ['Alamat Sekolah', ':'], ';');
            fputcsv($file, ['Periode Pendampingan', ':'], ';');
            fputcsv($file, [], ';');
            fputcsv($file, $detailColumns, ';');

            // Tulis data strategi khusus untuk sekolah binaan yang dipilih.
            foreach ($strategies as $index => $str) {
                fputcsv($file, [
                    $index + 1,
                    $str->strategy,
                    $str->tahap_pendampingan ?? '',
                    $str->fokus_sasaran ?? '',
                    $str->metode_pendampingan ?? '',
                    $str->langkah_rencana_aksi ?? '',
                    $str->output_indikator ?? '',
                    $str->jadwal_pendampingan ? \Carbon\Carbon::parse($str->jadwal_pendampingan)->format('d/m/Y') : ''
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
