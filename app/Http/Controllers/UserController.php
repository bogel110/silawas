<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\School;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use ZipArchive;

class UserController extends Controller
{
    public function index()
    {
        $this->authorizeSuperAdmin();

        $users = User::with(['school', 'supervisedSchools'])->latest()->get();
        $schools = School::orderBy('name', 'asc')->get(); 
        
        return view('admin.users.index', compact('users', 'schools'));
    }

    public function store(Request $request)
    {
        $this->authorizeSuperAdmin();

        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:8',
            'role'      => 'required|in:super_admin,pengawas,admin_sekolah',
        ]);

        $school_id = null;

        if ($request->role === 'admin_sekolah') {
            $request->validate([
                'school_name'   => 'required|string|max:255',
                'school_level'  => 'required|string',
                'school_status' => 'required|string',
            ]);

            $school = School::firstOrCreate(
                ['name' => $request->school_name],
                [
                    'level' => $request->school_level,
                    'status' => $request->school_status
                ]
            );
            $school_id = $school->id;
        }

        User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => $request->role,
            'school_id' => $school_id,
        ]);

        if (auth()->user()?->role === 'pengawas' && $school_id) {
            auth()->user()->supervisedSchools()->syncWithoutDetaching([$school_id]);
        }

        $pesan = match ($request->role) {
            'super_admin' => 'Akun Super Admin berhasil ditambahkan!',
            'pengawas' => 'Akun Pengawas berhasil ditambahkan!',
            default => 'Akun Admin dan data Sekolah berhasil ditambahkan!',
        };

        return redirect()->back()->with('success', $pesan);
    }

    public function importAdmins(Request $request)
    {
        $this->authorizeSuperAdmin();

        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,csv,txt|max:4096',
        ]);

        $rows = $this->readImportRows($request->file('import_file')->getRealPath(), $request->file('import_file')->getClientOriginalExtension());
        if (count($rows) === 0) {
            return back()->withErrors(['import' => 'File import kosong atau format kolom tidak terbaca.']);
        }

        $created = 0;
        $skipped = [];

        foreach ($rows as $index => $row) {
            $lineNumber = $index + 2;
            $data = [
                'name' => $row['name'] ?? null,
                'email' => $row['email'] ?? null,
                'password' => $row['password'] ?? null,
                'school_name' => $row['school_name'] ?? null,
                'school_level' => $row['school_level'] ?? null,
                'school_status' => $row['school_status'] ?? null,
            ];

            $validator = Validator::make($data, [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'school_name' => 'required|string|max:255',
                'school_level' => 'required|string',
                'school_status' => 'required|string',
            ]);

            if ($validator->fails()) {
                $skipped[] = 'Baris ' . $lineNumber . ': ' . implode(' ', $validator->errors()->all());
                continue;
            }

            $school = School::firstOrCreate(
                ['name' => $data['school_name']],
                [
                    'level' => $data['school_level'],
                    'status' => $data['school_status'],
                ]
            );

            User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'admin_sekolah',
                'school_id' => $school->id,
            ]);

            if (auth()->user()?->role === 'pengawas') {
                auth()->user()->supervisedSchools()->syncWithoutDetaching([$school->id]);
            }

            $created++;
        }

        if ($created === 0 && count($skipped) > 0) {
            return back()->withErrors(['import' => 'Tidak ada admin sekolah yang berhasil diimport.'])->with('import_errors', $skipped);
        }

        return back()
            ->with('success', $created . ' admin sekolah berhasil diimport.')
            ->with('import_errors', $skipped);
    }

    public function downloadAdminImportTemplate()
    {
        $this->authorizeSuperAdmin();

        $rows = [
            ['nama_admin', 'email', 'password', 'nama_sekolah', 'level_sekolah', 'status_sekolah'],
            ['Budi Santoso', 'admin@sekolah.sch.id', 'Silawas2026!', 'SMAN 1 Contoh', 'SMA', 'Negeri'],
            ['Siti Aminah', 'operator@sekolah.sch.id', 'Silawas2026!', 'SMK Contoh', 'SMK', 'Swasta'],
        ];

        if (!class_exists(ZipArchive::class)) {
            return $this->downloadAdminImportCsvTemplate($rows);
        }

        $path = tempnam(sys_get_temp_dir(), 'format_import_admin_');
        $zip = new ZipArchive();

        if ($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return $this->downloadAdminImportCsvTemplate($rows);
        }

        $zip->addFromString('[Content_Types].xml', $this->xlsxContentTypesXml());
        $zip->addFromString('_rels/.rels', $this->xlsxRootRelsXml());
        $zip->addFromString('xl/workbook.xml', $this->xlsxWorkbookXml());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->xlsxWorkbookRelsXml());
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->xlsxWorksheetXml($rows));
        $zip->close();

        return response()->download($path, 'Format_Import_Admin_Sekolah.xlsx')->deleteFileAfterSend(true);
    }

    private function downloadAdminImportCsvTemplate(array $rows)
    {
        $content = chr(0xEF) . chr(0xBB) . chr(0xBF);

        foreach ($rows as $row) {
            $content .= implode(';', array_map(fn ($value) => '"' . str_replace('"', '""', $value) . '"', $row)) . "\r\n";
        }

        return response($content, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="Format_Import_Admin_Sekolah.csv"',
        ]);
    }

    private function readImportRows(string $path, string $extension): array
    {
        $extension = strtolower($extension);

        if ($extension === 'xlsx') {
            return $this->readXlsxRows($path);
        }

        return $this->readCsvRows($path);
    }

    private function readCsvRows(string $path): array
    {
        $handle = fopen($path, 'r');
        if ($handle === false) {
            return [];
        }

        $headers = null;
        $rows = [];

        while (($line = fgetcsv($handle, 0, ';')) !== false) {
            if (count($line) === 1) {
                $line = str_getcsv($line[0], ',');
            }

            if ($headers === null) {
                $headers = $this->normalizeHeaders($line);
                continue;
            }

            $rows[] = $this->combineImportRow($headers, $line);
        }

        fclose($handle);

        return array_values(array_filter($rows));
    }

    private function readXlsxRows(string $path): array
    {
        if (!class_exists(ZipArchive::class)) {
            return [];
        }

        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            return [];
        }

        $sharedStrings = $this->readSharedStrings($zip);
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();

        if ($sheetXml === false) {
            return [];
        }

        $sheet = simplexml_load_string($sheetXml);
        if ($sheet === false || !isset($sheet->sheetData->row)) {
            return [];
        }

        $headers = null;
        $rows = [];

        foreach ($sheet->sheetData->row as $row) {
            $values = [];
            foreach ($row->c as $cell) {
                $cellRef = (string) $cell['r'];
                $columnIndex = $this->columnIndexFromCellReference($cellRef);
                $values[$columnIndex] = $this->readCellValue($cell, $sharedStrings);
            }

            if (empty($values)) {
                continue;
            }

            ksort($values);
            $values = array_replace(array_fill(0, max(array_keys($values)) + 1, ''), $values);

            if ($headers === null) {
                $headers = $this->normalizeHeaders($values);
                continue;
            }

            $rows[] = $this->combineImportRow($headers, $values);
        }

        return array_values(array_filter($rows));
    }

    private function readSharedStrings(ZipArchive $zip): array
    {
        $xml = $zip->getFromName('xl/sharedStrings.xml');
        if ($xml === false) {
            return [];
        }

        $shared = simplexml_load_string($xml);
        if ($shared === false) {
            return [];
        }

        $strings = [];
        foreach ($shared->si as $item) {
            if (isset($item->t)) {
                $strings[] = (string) $item->t;
                continue;
            }

            $text = '';
            foreach ($item->r as $run) {
                $text .= (string) $run->t;
            }
            $strings[] = $text;
        }

        return $strings;
    }

    private function readCellValue($cell, array $sharedStrings): string
    {
        $type = (string) $cell['t'];

        if ($type === 's') {
            return trim($sharedStrings[(int) $cell->v] ?? '');
        }

        if ($type === 'inlineStr') {
            return trim((string) ($cell->is->t ?? ''));
        }

        return trim((string) ($cell->v ?? ''));
    }

    private function columnIndexFromCellReference(string $cellReference): int
    {
        $letters = preg_replace('/[^A-Z]/', '', strtoupper($cellReference));
        $index = 0;

        foreach (str_split($letters) as $letter) {
            $index = ($index * 26) + (ord($letter) - 64);
        }

        return max(0, $index - 1);
    }

    private function normalizeHeaders(array $headers): array
    {
        $aliases = [
            'nama' => 'name',
            'nama_admin' => 'name',
            'name' => 'name',
            'email' => 'email',
            'email_login' => 'email',
            'password' => 'password',
            'kata_sandi' => 'password',
            'nama_sekolah' => 'school_name',
            'school_name' => 'school_name',
            'sekolah' => 'school_name',
            'level_sekolah' => 'school_level',
            'school_level' => 'school_level',
            'level' => 'school_level',
            'status_sekolah' => 'school_status',
            'school_status' => 'school_status',
            'status' => 'school_status',
        ];

        return array_map(function ($header) use ($aliases) {
            $key = strtolower(trim((string) $header));
            $key = preg_replace('/^\xEF\xBB\xBF/', '', $key);
            $key = preg_replace('/[^a-z0-9]+/', '_', $key);
            $key = trim($key, '_');

            return $aliases[$key] ?? $key;
        }, $headers);
    }

    private function combineImportRow(array $headers, array $values): ?array
    {
        $row = [];
        foreach ($headers as $index => $header) {
            if ($header === '') {
                continue;
            }

            $row[$header] = trim((string) ($values[$index] ?? ''));
        }

        return collect($row)->filter(fn ($value) => $value !== '')->isEmpty() ? null : $row;
    }

    private function xlsxContentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '</Types>';
    }

    private function xlsxRootRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';
    }

    private function xlsxWorkbookXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
            . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="Import Admin" sheetId="1" r:id="rId1"/></sheets>'
            . '</workbook>';
    }

    private function xlsxWorkbookRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '</Relationships>';
    }

    private function xlsxWorksheetXml(array $rows): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<cols>'
            . '<col min="1" max="1" width="24" customWidth="1"/>'
            . '<col min="2" max="2" width="28" customWidth="1"/>'
            . '<col min="3" max="3" width="18" customWidth="1"/>'
            . '<col min="4" max="4" width="28" customWidth="1"/>'
            . '<col min="5" max="6" width="16" customWidth="1"/>'
            . '</cols><sheetData>';

        foreach ($rows as $rowIndex => $row) {
            $excelRow = $rowIndex + 1;
            $xml .= '<row r="' . $excelRow . '">';

            foreach ($row as $columnIndex => $value) {
                $cell = $this->xlsxColumnName($columnIndex + 1) . $excelRow;
                $escaped = htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
                $xml .= '<c r="' . $cell . '" t="inlineStr"><is><t>' . $escaped . '</t></is></c>';
            }

            $xml .= '</row>';
        }

        return $xml . '</sheetData></worksheet>';
    }

    private function xlsxColumnName(int $index): string
    {
        $name = '';

        while ($index > 0) {
            $index--;
            $name = chr(65 + ($index % 26)) . $name;
            $index = intdiv($index, 26);
        }

        return $name;
    }

    // ==========================================
    // FUNGSI BARU: UPDATE DATA USER
    // ==========================================
    public function update(Request $request, $id)
    {
        $this->authorizeSuperAdmin();

        $user = User::findOrFail($id);

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id, // Abaikan email milik dia sendiri
            'role'  => 'required|in:super_admin,pengawas,admin_sekolah',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        // Atur ID Sekolah berdasarkan Role
        if (in_array($request->role, ['super_admin', 'pengawas'], true)) {
            $user->school_id = null; // Pengawas/Super Admin tidak terikat 1 sekolah
        } else {
            $request->validate([
                'school_id' => 'required|exists:schools,id',
            ]);

            $user->school_id = $request->school_id;
        }

        $user->save();

        if ($user->role !== 'pengawas') {
            $user->supervisedSchools()->sync([]);
        }

        return redirect()->back()->with('success', 'Data pengguna berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $this->authorizeSuperAdmin();

        if ((int) auth()->id() === (int) $id) {
            return back()->withErrors(['user' => 'Anda tidak dapat menghapus akun yang sedang digunakan.']);
        }
        
        User::findOrFail($id)->delete();
        return back()->with('success', 'Akun berhasil dihapus!');
    }
    
    public function resetPassword(Request $request, $id)
    {
        $this->authorizeSuperAdmin();

        $request->validate([
            'password' => 'required|string|min:8',
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Password untuk akun ' . $user->name . ' berhasil direset!');
    }
}
