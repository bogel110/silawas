<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alumni;
use App\Models\School;
use Carbon\Carbon;

class AlumniController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeAdminForSchool(auth()->user()->school_id);

        $schoolId = auth()->user()->school_id;
        $search = $request->get('search', '');
        $perPage = $request->get('per_page', 10);
        
        $query = Alumni::where('school_id', $schoolId);

        // Filter berdasarkan search term
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', '%' . $search . '%')
                  ->orWhere('tahun_lulus', 'like', '%' . $search . '%')
                  ->orWhere('status', 'like', '%' . $search . '%')
                  ->orWhere('jenis_studi', 'like', '%' . $search . '%')
                  ->orWhere('jenis_pekerjaan', 'like', '%' . $search . '%')
                  ->orWhere('keterangan', 'like', '%' . $search . '%');
            });
        }

        $alumni = $query->orderBy('tahun_lulus', 'desc')
            ->orderBy('nama_lengkap', 'asc')
            ->paginate($perPage);

        // Hitung statistik alumni
        $allAlumni = Alumni::where('school_id', $schoolId)->get();
        $stats = [
            'total' => $allAlumni->count(),
            'melanjutkan_studi' => $allAlumni->where('status', 'Melanjutkan Studi')->count(),
            'bekerja' => $allAlumni->where('status', 'Bekerja')->count(),
            'ptn' => $allAlumni->where('jenis_studi', 'PTN')->count(),
            'pts' => $allAlumni->where('jenis_studi', 'PTS')->count(),
            'kedinasan_studi' => $allAlumni->where('jenis_studi', 'KEDINASAN')->count(),
            'asn' => $allAlumni->where('jenis_pekerjaan', 'ASN')->count(),
            'tni' => $allAlumni->where('jenis_pekerjaan', 'TNI')->count(),
            'polri' => $allAlumni->where('jenis_pekerjaan', 'POLRI')->count(),
            'swasta' => $allAlumni->where('jenis_pekerjaan', 'SWASTA')->count(),
        ];

        return view('alumni.index', compact('alumni', 'search', 'stats', 'perPage'));
    }

    public function search(Request $request)
    {
        $this->authorizeAdminForSchool(auth()->user()->school_id);

        $schoolId = auth()->user()->school_id;
        $search = trim($request->get('q', ''));
        
        $query = Alumni::where('school_id', $schoolId);

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', '%' . $search . '%')
                  ->orWhere('tahun_lulus', 'like', '%' . $search . '%')
                  ->orWhere('status', 'like', '%' . $search . '%')
                  ->orWhere('jenis_studi', 'like', '%' . $search . '%')
                  ->orWhere('jenis_pekerjaan', 'like', '%' . $search . '%')
                  ->orWhere('keterangan', 'like', '%' . $search . '%');
            });
        }

        $alumni = $query->orderBy('tahun_lulus', 'desc')
            ->orderBy('nama_lengkap', 'asc')
            ->get();

        return response()->json(['data' => $alumni, 'count' => $alumni->count()]);
    }

    public function searchPengawas(Request $request)
    {
        $this->authorizePengawas();

        $schoolId = $request->get('school_id');
        $search = trim($request->get('q', ''));
        
        // Authorize access to this school
        if ($schoolId) {
            $this->authorizeSchoolAccess($schoolId);
            $query = Alumni::where('school_id', $schoolId);
        } else {
            $query = Alumni::whereIn('school_id', $this->supervisedSchoolIds());
        }

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', '%' . $search . '%')
                  ->orWhere('tahun_lulus', 'like', '%' . $search . '%')
                  ->orWhere('status', 'like', '%' . $search . '%')
                  ->orWhere('jenis_studi', 'like', '%' . $search . '%')
                  ->orWhere('jenis_pekerjaan', 'like', '%' . $search . '%')
                  ->orWhere('keterangan', 'like', '%' . $search . '%');
            });
        }

        $alumni = $query->orderBy('tahun_lulus', 'desc')
            ->orderBy('nama_lengkap', 'asc')
            ->get();

        return response()->json(['data' => $alumni, 'count' => $alumni->count()]);
    }

    public function store(Request $request)
    {
        $this->authorizeAdminForSchool(auth()->user()->school_id);

        $rules = [
            'nama_lengkap' => 'required|string|max:255',
            'tahun_lulus'  => 'required|integer|min:1900|max:' . (date('Y') + 10),
            'status'       => 'required|in:Melanjutkan Studi,Bekerja',
            'keterangan_studi' => 'nullable|string',
            'keterangan_kerja' => 'nullable|string',
        ];

        // Validasi berdasarkan status
        if ($request->input('status') === 'Melanjutkan Studi') {
            $rules['jenis_studi'] = 'required|in:PTN,PTS,KEDINASAN';
            $rules['jalur_penerimaan'] = 'required|in:SNBP,SNBT,MANDIRI,KEDINASAN';
            $rules['jenis_pekerjaan'] = 'nullable';
        } elseif ($request->input('status') === 'Bekerja') {
            $rules['jenis_pekerjaan'] = 'required|in:ASN,TNI,POLRI,SWASTA';
            $rules['jenis_studi'] = 'nullable';
            $rules['jalur_penerimaan'] = 'nullable';
        }

        $data = $request->validate($rules);

        // Map keterangan berdasarkan status
        if ($data['status'] === 'Melanjutkan Studi') {
            $data['keterangan'] = $data['keterangan_studi'] ?? null;
            $data['jenis_pekerjaan'] = null;
        } else {
            $data['keterangan'] = $data['keterangan_kerja'] ?? null;
            $data['jenis_studi'] = null;
            $data['jalur_penerimaan'] = null;
        }

        // Remove temporary fields
        unset($data['keterangan_studi']);
        unset($data['keterangan_kerja']);

        $data['school_id'] = auth()->user()->school_id;
        Alumni::create($data);

        return back()->with('success', 'Data Alumni berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $alumni = Alumni::findOrFail($id);
        $this->authorizeAdminForSchool($alumni->school_id);

        $rules = [
            'nama_lengkap' => 'required|string|max:255',
            'tahun_lulus'  => 'required|integer|min:1900|max:' . (date('Y') + 10),
            'status'       => 'required|in:Melanjutkan Studi,Bekerja',
            'keterangan_studi' => 'nullable|string',
            'keterangan_kerja' => 'nullable|string',
        ];

        // Validasi berdasarkan status
        if ($request->input('status') === 'Melanjutkan Studi') {
            $rules['jenis_studi'] = 'required|in:PTN,PTS,KEDINASAN';
            $rules['jalur_penerimaan'] = 'required|in:SNBP,SNBT,MANDIRI,KEDINASAN';
            $rules['jenis_pekerjaan'] = 'nullable';
        } elseif ($request->input('status') === 'Bekerja') {
            $rules['jenis_pekerjaan'] = 'required|in:ASN,TNI,POLRI,SWASTA';
            $rules['jenis_studi'] = 'nullable';
            $rules['jalur_penerimaan'] = 'nullable';
        }

        $data = $request->validate($rules);

        // Map keterangan berdasarkan status
        if ($data['status'] === 'Melanjutkan Studi') {
            $data['keterangan'] = $data['keterangan_studi'] ?? null;
            $data['jenis_pekerjaan'] = null;
        } else {
            $data['keterangan'] = $data['keterangan_kerja'] ?? null;
            $data['jenis_studi'] = null;
            $data['jalur_penerimaan'] = null;
        }

        // Remove temporary fields
        unset($data['keterangan_studi']);
        unset($data['keterangan_kerja']);

        $alumni->update($data);

        return back()->with('success', 'Data Alumni berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $alumni = Alumni::findOrFail($id);
        $this->authorizeAdminForSchool($alumni->school_id);

        $alumni->delete();

        return back()->with('success', 'Data Alumni berhasil dihapus!');
    }

    public function exportTemplate()
    {
        $this->authorizeAdminForSchool(auth()->user()->school_id);

        $filename = "Template_Alumni_" . date('Y-m-d') . ".csv";
        
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            fputs($file, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));
            
            // Header
            fputcsv($file, ['Nama Lengkap', 'Tahun Lulus', 'Status', 'Jenis Studi', 'Jalur Penerimaan', 'Jenis Pekerjaan', 'Keterangan'], ';');
            
            // Sample rows - Studi
            fputcsv($file, ['Budi Santoso', '2024', 'Melanjutkan Studi', 'PTN', 'SNBP', '', 'Teknik Informatika - Universitas Gadjah Mada'], ';');
            fputcsv($file, ['Siti Nurhaliza', '2024', 'Melanjutkan Studi', 'PTS', 'MANDIRI', '', 'Akuntansi - Universitas Indonesia'], ';');
            fputcsv($file, ['Ahmad Wijaya', '2023', 'Melanjutkan Studi', 'KEDINASAN', 'KEDINASAN', '', 'Manajemen Pemerintahan - STPDN'], ';');
            
            // Sample rows - Bekerja
            fputcsv($file, ['Dewi Lestari', '2024', 'Bekerja', '', '', 'ASN', 'Administrasi - Aparatur Sipil Negara'], ';');
            fputcsv($file, ['Roni Hermawan', '2023', 'Bekerja', '', '', 'SWASTA', 'IT Developer - PT Telkom'], ';');
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportData()
    {
        $this->authorizeAdminForSchool(auth()->user()->school_id);

        $schoolId = auth()->user()->school_id;
        $data = Alumni::where('school_id', $schoolId)
            ->orderBy('tahun_lulus', 'desc')
            ->orderBy('nama_lengkap', 'asc')
            ->get();

        $filename = "Data_Alumni_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use($data) {
            $file = fopen('php://output', 'w');
            fputs($file, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));
            
            fputcsv($file, ['Nama Lengkap', 'Tahun Lulus', 'Status', 'Jenis Studi', 'Jalur Penerimaan', 'Jenis Pekerjaan', 'Keterangan'], ';');

            foreach ($data as $row) {
                fputcsv($file, [
                    $row->nama_lengkap,
                    $row->tahun_lulus,
                    $row->status,
                    $row->jenis_studi ?? '',
                    $row->jalur_penerimaan ?? '',
                    $row->jenis_pekerjaan ?? '',
                    $row->keterangan ?? ''
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importAlumni(Request $request)
    {
        $this->authorizeAdminForSchool(auth()->user()->school_id);

        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ], [
            'file.required' => 'File tidak boleh kosong',
            'file.mimes' => 'File harus berformat CSV',
            'file.max' => 'Ukuran file maksimal 2MB',
        ]);

        $file = $request->file('file');
        $schoolId = auth()->user()->school_id;
        $errors = [];
        $imported = 0;
        $row = 0;

        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            // Skip header
            fgetcsv($handle, 0, ';');
            $row = 1;

            while (($data = fgetcsv($handle, 0, ';')) !== false) {
                $row++;

                // Skip empty rows
                if (empty($data[0])) continue;

                try {
                    $validated = [
                        'nama_lengkap' => trim($data[0] ?? ''),
                        'tahun_lulus'  => intval($data[1] ?? ''),
                        'status'       => trim($data[2] ?? ''),
                        'jenis_studi'  => !empty($data[3]) ? trim($data[3]) : null,
                        'jalur_penerimaan' => !empty($data[4]) ? trim($data[4]) : null,
                        'jenis_pekerjaan'  => !empty($data[5]) ? trim($data[5]) : null,
                        'keterangan'   => !empty($data[6]) ? trim($data[6]) : null,
                    ];

                    // Validasi nama
                    if (empty($validated['nama_lengkap'])) {
                        $errors[] = "Baris $row: Nama lengkap tidak boleh kosong";
                        continue;
                    }

                    // Validasi tahun lulus
                    if ($validated['tahun_lulus'] < 1900 || $validated['tahun_lulus'] > (date('Y') + 10)) {
                        $errors[] = "Baris $row: Tahun lulus harus antara 1900 dan " . (date('Y') + 10);
                        continue;
                    }

                    // Validasi status
                    if (!in_array($validated['status'], ['Melanjutkan Studi', 'Bekerja'])) {
                        $errors[] = "Baris $row: Status harus 'Melanjutkan Studi' atau 'Bekerja'";
                        continue;
                    }

                    // Validasi berdasarkan status
                    if ($validated['status'] === 'Melanjutkan Studi') {
                        if (empty($validated['jenis_studi'])) {
                            $errors[] = "Baris $row: Jenis Studi tidak boleh kosong jika Status 'Melanjutkan Studi'";
                            continue;
                        }
                        if (!in_array($validated['jenis_studi'], ['PTN', 'PTS', 'KEDINASAN'])) {
                            $errors[] = "Baris $row: Jenis Studi harus 'PTN', 'PTS', atau 'KEDINASAN'";
                            continue;
                        }
                        if (empty($validated['jalur_penerimaan'])) {
                            $errors[] = "Baris $row: Jalur Penerimaan tidak boleh kosong jika Status 'Melanjutkan Studi'";
                            continue;
                        }
                        if (!in_array($validated['jalur_penerimaan'], ['SNBP', 'SNBT', 'MANDIRI', 'KEDINASAN'])) {
                            $errors[] = "Baris $row: Jalur Penerimaan harus 'SNBP', 'SNBT', 'MANDIRI', atau 'KEDINASAN'";
                            continue;
                        }
                        // Clear pekerjaan
                        $validated['jenis_pekerjaan'] = null;
                    } else if ($validated['status'] === 'Bekerja') {
                        if (empty($validated['jenis_pekerjaan'])) {
                            $errors[] = "Baris $row: Jenis Pekerjaan tidak boleh kosong jika Status 'Bekerja'";
                            continue;
                        }
                        if (!in_array($validated['jenis_pekerjaan'], ['ASN', 'TNI', 'POLRI', 'SWASTA'])) {
                            $errors[] = "Baris $row: Jenis Pekerjaan harus 'ASN', 'TNI', 'POLRI', atau 'SWASTA'";
                            continue;
                        }
                        // Clear studi
                        $validated['jenis_studi'] = null;
                        $validated['jalur_penerimaan'] = null;
                    }

                    $validated['school_id'] = $schoolId;
                    Alumni::create($validated);
                    $imported++;

                } catch (\Exception $e) {
                    $errors[] = "Baris $row: " . $e->getMessage();
                }
            }

            fclose($handle);
        }

        if ($imported === 0 && !empty($errors)) {
            return back()->with('error', 'Gagal mengimpor data. ' . implode(' | ', array_slice($errors, 0, 5)));
        }

        $message = "Berhasil mengimpor $imported data alumni";
        if (!empty($errors)) {
            $message .= ". Terdapat " . count($errors) . " baris error (lihat di bawah)";
        }

        return back()->with('success', $message)->with('import_errors', array_slice($errors, 0, 10));
    }

    // ==========================================
    // AREA PENGAWAS & SUPER ADMIN
    // ==========================================
    public function indexPengawas(Request $request)
    {
        $this->authorizePengawas();

        $schools = $this->supervisedSchoolsQuery()->orderBy('name', 'asc')->get();
        $schoolIds = $schools->pluck('id');
        $selectedSchoolId = $request->get('school_id');
        $selectedSchool = $selectedSchoolId ? School::findOrFail($selectedSchoolId) : null;
        
        if ($selectedSchool) {
            $this->authorizeSchoolAccess($selectedSchool->id);
        }

        // Data alumni
        $alumni = [];
        $stats = [
            'total' => 0,
            'melanjutkan_studi' => 0,
            'bekerja' => 0,
            'ptn' => 0,
            'pts' => 0,
            'kedinasan' => 0,
            'asn' => 0,
            'tni' => 0,
            'polri' => 0,
            'swasta' => 0,
        ];

        if ($selectedSchool) {
            $alumni = Alumni::where('school_id', $selectedSchoolId)
                ->orderBy('tahun_lulus', 'desc')
                ->orderBy('nama_lengkap', 'asc')
                ->get();

            $stats['total'] = $alumni->count();
            $stats['melanjutkan_studi'] = $alumni->where('status', 'Melanjutkan Studi')->count();
            $stats['bekerja'] = $alumni->where('status', 'Bekerja')->count();
            $stats['ptn'] = $alumni->where('jenis_studi', 'PTN')->count();
            $stats['pts'] = $alumni->where('jenis_studi', 'PTS')->count();
            $stats['kedinasan'] = $alumni->where('jenis_studi', 'KEDINASAN')->count();
            $stats['asn'] = $alumni->where('jenis_pekerjaan', 'ASN')->count();
            $stats['tni'] = $alumni->where('jenis_pekerjaan', 'TNI')->count();
            $stats['polri'] = $alumni->where('jenis_pekerjaan', 'POLRI')->count();
            $stats['swasta'] = $alumni->where('jenis_pekerjaan', 'SWASTA')->count();
        }

        return view('alumni.pengawas', compact('schools', 'selectedSchool', 'alumni', 'stats'));
    }

    public function exportPengawas(Request $request)
    {
        $this->authorizePengawas();

        $schoolId = $request->get('school_id');
        $query = Alumni::with('school')->whereIn('school_id', $this->supervisedSchoolIds());

        // Jika filter sekolah diisi, ambil sekolah tersebut. Jika kosong, export semua.
        if ($schoolId) {
            $this->authorizeSchoolAccess($schoolId);
            $query->where('school_id', $schoolId);
            $schoolName = School::findOrFail($schoolId)->name;
        } else {
            $schoolName = "Seluruh_Sekolah_Binaan";
        }

        $data = $query->orderBy('tahun_lulus', 'desc')->orderBy('nama_lengkap', 'asc')->get();
        $filename = "Peta_Alumni_" . str_replace(' ', '_', $schoolName) . "_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use($data) {
            $file = fopen('php://output', 'w');
            fputs($file, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));
            
            fputcsv($file, ['Nama Sekolah', 'Nama Lengkap', 'Tahun Lulus', 'Status', 'Jenis Studi', 'Jalur Penerimaan', 'Jenis Pekerjaan', 'Keterangan'], ';');

            foreach ($data as $row) {
                fputcsv($file, [
                    $row->school->name ?? '-',
                    $row->nama_lengkap,
                    $row->tahun_lulus,
                    $row->status,
                    $row->jenis_studi ?? '',
                    $row->jalur_penerimaan ?? '',
                    $row->jenis_pekerjaan ?? '',
                    $row->keterangan ?? ''
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function getTableData(Request $request)
    {
        $this->authorizeAdminForSchool(auth()->user()->school_id);

        $schoolId = auth()->user()->school_id;
        $perPage = (int) $request->get('per_page', 10);
        $page = (int) $request->get('page', 1);
        
        $alumni = Alumni::where('school_id', $schoolId)
            ->orderBy('tahun_lulus', 'desc')
            ->orderBy('nama_lengkap', 'asc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'html' => view('alumni.partials.table-body', compact('alumni'))->render(),
            'pagination' => view('alumni.partials.pagination', compact('alumni', 'perPage'))->render(),
            'total' => $alumni->total(),
            'per_page' => $perPage,
            'current_page' => $page,
        ]);
    }

    public function getTableDataPengawas(Request $request)
    {
        $this->authorizePengawas();

        $schoolId = $request->get('school_id');
        $perPage = (int) $request->get('per_page', 10);
        $page = (int) $request->get('page', 1);

        if ($schoolId) {
            $this->authorizeSchoolAccess($schoolId);
            $alumni = Alumni::where('school_id', $schoolId)
                ->orderBy('tahun_lulus', 'desc')
                ->orderBy('nama_lengkap', 'asc')
                ->paginate($perPage, ['*'], 'page', $page);
        } else {
            $alumni = Alumni::whereIn('school_id', $this->supervisedSchoolIds())
                ->orderBy('tahun_lulus', 'desc')
                ->orderBy('nama_lengkap', 'asc')
                ->paginate($perPage, ['*'], 'page', $page);
        }

        return response()->json([
            'html' => view('alumni.partials.table-body-pengawas', compact('alumni'))->render(),
            'pagination' => view('alumni.partials.pagination-pengawas', compact('alumni', 'perPage', 'schoolId'))->render(),
            'total' => $alumni->total(),
            'per_page' => $perPage,
            'current_page' => $page,
        ]);
    }
}

