<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;

class PengawasBinaanController extends Controller
{
    public function index()
    {
        $this->authorizeSuperAdmin();

        $pengawas = User::with('supervisedSchools')
            ->where('role', 'pengawas')
            ->orderBy('name')
            ->get();

        $schools = School::orderBy('name')->get();

        return view('super-admin.pengawas-binaan.index', compact('pengawas', 'schools'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeSuperAdmin();

        if ($user->role !== 'pengawas') {
            abort(404);
        }

        $data = $request->validate([
            'school_ids' => 'nullable|array',
            'school_ids.*' => 'distinct|exists:schools,id',
        ]);

        $user->supervisedSchools()->sync($data['school_ids'] ?? []);

        return back()->with('success', 'Sekolah binaan untuk ' . $user->name . ' berhasil diperbarui.');
    }
}
