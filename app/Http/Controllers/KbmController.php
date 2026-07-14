<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use App\Models\KbmReport;

class KbmController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $schools = [];
        $kbms = collect();
        $selectedSchool = null;

        if (in_array($user->role, ['pengawas', 'super_admin'], true)) {
            $schools = $this->supervisedSchoolsQuery()->orderBy('name', 'asc')->get();
            $schoolIds = $schools->pluck('id');
            
            $query = KbmReport::with('school')
                ->whereIn('school_id', $schoolIds)
                ->orderBy('tahun_pelajaran', 'desc');

            if ($request->filled('school_id')) {
                $query->where('school_id', $request->school_id);
                $selectedSchool = School::findOrFail($request->school_id);
                $this->authorizeSchoolAccess($selectedSchool->id);
            }

            $kbms = $query->get();
        } 
        elseif ($user->role === 'admin_sekolah') {
            if (! $user->school_id) {
                abort(403, 'Akun admin sekolah belum terhubung ke data sekolah.');
            }

            $selectedSchool = School::findOrFail($user->school_id);
            $this->authorizeSchoolAccess($selectedSchool->id);

            $kbms = KbmReport::where('school_id', $user->school_id)
                                ->orderBy('tahun_pelajaran', 'desc')
                                ->get();
        } else {
            abort(403, 'Akses ditolak.');
        }

        return view('kbm.index', compact('schools', 'kbms', 'selectedSchool'));
    }
}
