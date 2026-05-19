<?php

namespace App\Http\Controllers;

use App\Models\School;

abstract class Controller
{
    protected function authorizePengawas(): void
    {
        if (! in_array(auth()->user()?->role, ['pengawas', 'super_admin'], true)) {
            abort(403, 'Akses ditolak. Halaman ini khusus Pengawas.');
        }
    }

    protected function authorizeSuperAdmin(): void
    {
        if (auth()->user()?->role !== 'super_admin') {
            abort(403, 'Akses ditolak. Halaman ini khusus Super Admin.');
        }
    }

    protected function authorizeSchoolAccess($schoolId): void
    {
        $user = auth()->user();

        if ($user?->role === 'super_admin') {
            return;
        }

        if ($user?->role === 'pengawas') {
            if ($user->supervisedSchools()->whereKey($schoolId)->exists()) {
                return;
            }

            abort(403, 'Akses ditolak. Sekolah ini bukan sekolah binaan Anda.');
        }

        if ($user?->role === 'admin_sekolah'
            && $user->school_id !== null
            && $schoolId !== null
            && (int) $user->school_id === (int) $schoolId) {
            return;
        }

        abort(403, 'Akses ditolak.');
    }

    protected function authorizeAdminForSchool($schoolId): void
    {
        $user = auth()->user();

        if ($user?->role === 'admin_sekolah'
            && $user->school_id !== null
            && $schoolId !== null
            && (int) $user->school_id === (int) $schoolId) {
            return;
        }

        abort(403, 'Akses ditolak. Aksi ini hanya untuk admin sekolah terkait.');
    }

    protected function supervisedSchoolsQuery()
    {
        $user = auth()->user();

        if ($user?->role === 'super_admin') {
            return School::query();
        }

        if ($user?->role === 'pengawas') {
            return School::query()->whereIn('id', $user->supervisedSchools()->pluck('schools.id'));
        }

        return School::query()->whereRaw('1 = 0');
    }

    protected function supervisedSchoolIds(): array
    {
        return $this->supervisedSchoolsQuery()->pluck('schools.id')->all();
    }
}
