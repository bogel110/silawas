<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected function authorizePengawas(): void
    {
        if (auth()->user()?->role !== 'pengawas') {
            abort(403, 'Akses ditolak. Halaman ini khusus Pengawas.');
        }
    }

    protected function authorizeSchoolAccess($schoolId): void
    {
        $user = auth()->user();

        if ($user?->role === 'pengawas') {
            return;
        }

        if ($user?->role === 'admin_sekolah' && (int) $user->school_id === (int) $schoolId) {
            return;
        }

        abort(403, 'Akses ditolak.');
    }

    protected function authorizeAdminForSchool($schoolId): void
    {
        $user = auth()->user();

        if ($user?->role === 'admin_sekolah' && (int) $user->school_id === (int) $schoolId) {
            return;
        }

        abort(403, 'Akses ditolak. Aksi ini hanya untuk admin sekolah terkait.');
    }
}
