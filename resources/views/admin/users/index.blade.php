@extends('layouts.app')

@section('title', 'Administrator - Kelola Admin Sekolah')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-end">
    <div>
        <h2 class="display-6 fw-extrabold font-headline mb-0">Kelola Pengguna</h2>
        <p class="text-muted small mb-0">Manajemen akun Administrator Sekolah.</p>
    </div>
    <button class="btn btn-primary fw-bold d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
        <span class="material-symbols-outlined fs-6">person_add</span> Tambah Akun
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show small" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger small">
        <ul class="mb-0">
            @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
    </div>
@endif

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="bg-light text-muted small">
                    <tr>
                        <th>Nama Pengguna</th>
                        <th>Email Login</th>
                        <th>Bertugas di Sekolah</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="fw-bold">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                {{ $user->school_name ?? 'Tidak ada data sekolah' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-3">
                                {{-- Tombol Reset Password --}}
                                <button type="button" class="btn btn-link text-warning p-0" data-bs-toggle="modal" data-bs-target="#modalResetPassword{{ $user->id }}" title="Reset Password">
                                    <span class="material-symbols-outlined fs-5">lock_reset</span>
                                </button>
                                
                                {{-- Tombol Hapus --}}
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Hapus akun ini secara permanen?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger p-0" title="Hapus Akun">
                                        <span class="material-symbols-outlined fs-5">delete</span>
                                    </button>
                                </form>
                            </div>

                            {{-- Modal Reset Password Khusus untuk User Ini --}}
                            <div class="modal fade" id="modalResetPassword{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow rounded-4 text-start">
                                        <div class="modal-header border-bottom-0">
                                            <h5 class="modal-title font-headline fw-bold">Reset Password Akun</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('admin.users.reset_password', $user->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body p-4 pt-0">
                                                <p class="small text-muted mb-3">Masukkan password baru untuk akun <strong>{{ $user->name }}</strong>.</p>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Password Baru (Min. 8 karakter)</label>
                                                    {{-- UBAH type="password" MENJADI type="text" DI BAWAH INI --}}
                                                    <input type="text" name="password" class="form-control" required placeholder="Ketik password baru...">
                                                </div>
                                            </div>
                                            <div class="modal-footer border-top-0 bg-light rounded-bottom-4">
                                                <button type="submit" class="btn btn-warning w-100 fw-bold">Update Password</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted small">Belum ada data admin sekolah.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title font-headline fw-bold">Tambah Admin Sekolah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 pt-0">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Lengkap Admin</label>
                        <input type="text" name="name" class="form-control" required placeholder="Contoh: Budi Santoso">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email Login</label>
                        <input type="email" name="email" class="form-control" required placeholder="admin@namasekolah.sch.id">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Password Lengkap (Min. 8 karakter)</label>
                        {{-- UBAH type="password" MENJADI type="text" DI BAWAH INI --}}
                        <input type="text" name="password" class="form-control" required placeholder="Contoh: Silawas2026!">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Sekolah (Manual)</label>
                        <input type="text" name="school_name" class="form-control" required placeholder="Contoh: SMAN 1 Surabaya">
                        <small class="text-muted" style="font-size: 11px;">Ketik nama sekolah tempat admin bertugas.</small>
                    </div>
                </div>
                <div class="modal-footer border-top-0 bg-light rounded-bottom-4">
                    <button type="submit" class="btn btn-primary w-100 fw-bold">Buat Akun Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection