<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $selectedSchool = null;
        $schools = [];

        // Logika untuk Pengawas/Super Admin: Ambil data sekolah sesuai hak akses
        if (in_array($user->role, ['pengawas', 'super_admin'], true)) {
            $schools = $this->supervisedSchoolsQuery()->orderBy('name', 'asc')->get();

            // Jika pengawas sudah memilih sekolah dari dropdown
            if ($request->filled('school_id')) {
                $selectedSchool = School::with(['attendances' => function ($query) {
                    $query->orderBy('tanggal', 'desc'); // Urutkan jurnal terbaru di atas
                }])->findOrFail($request->school_id);

                $this->authorizeSchoolAccess($selectedSchool->id);
            }
        }
        // Logika untuk Admin Sekolah: Langsung tampilkan jurnal sekolahnya sendiri
        elseif ($user->role === 'admin_sekolah') {
            if (! $user->school_id) {
                abort(403, 'Akun admin sekolah belum terhubung ke data sekolah.');
            }

            $selectedSchool = School::with(['attendances' => function ($query) {
                $query->orderBy('tanggal', 'desc');
            }])->findOrFail($user->school_id);

            $this->authorizeSchoolAccess($selectedSchool->id);
        } else {
            abort(403, 'Akses ditolak.');
        }

        return view('journal.index', compact('schools', 'selectedSchool'));
    }
}
