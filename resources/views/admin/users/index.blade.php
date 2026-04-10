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
                        <th>Level Pengguna</th>
                        <th>ID & Nama Sekolah</th>
                        <th>Status Sekolah</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="fw-bold">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                {{ $user->role ?? 'Operator' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                {{-- Menampilkan School ID dan Nama Sekolah dari relasi --}}
                                [ID: {{ $user->school_id ?? '-' }}] {{ $user->school->name ?? ($user->school_name ?? 'Tidak ada data') }}
                            </span>
                        </td>
                        <td>
                            {{-- Mengambil status sekolah dari relasi $user->school->status jika ada --}}
                            @php
                                $status = $user->school->status ?? ($user->school_status ?? '');
                            @endphp
                            
                            @if($status == 'Negeri')
                                <span class="badge bg-success bg-opacity-10 text-success">Negeri</span>
                            @elseif($status == 'Swasta')
                                <span class="badge bg-warning bg-opacity-10 text-warning">Swasta</span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-3">
                                <button type="button" class="btn btn-link text-warning p-0" data-bs-toggle="modal" data-bs-target="#modalResetPassword{{ $user->id }}" title="Reset Password">
                                    <span class="material-symbols-outlined fs-5">lock_reset</span>
                                </button>
                                
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Hapus akun ini secara permanen?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger p-0" title="Hapus Akun">
                                        <span class="material-symbols-outlined fs-5">delete</span>
                                    </button>
                                </form>
                            </div>

                            {{-- Modal Reset Password --}}
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
                                                    <input type="password" name="password" class="form-control" required placeholder="Silawas2026">
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
                        <td colspan="6" class="text-center py-4 text-muted small">Belum ada data admin sekolah.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Tambah User --}}
<div class="modal fade" id="modalTambahUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg"> {{-- Ditambah modal-lg agar lebih lebar --}}
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title font-headline fw-bold">Tambah Admin & Sekolah Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 pt-0">
                    <div class="row">
                        {{-- Kolom Kiri: Data Pengguna --}}
                        <div class="col-md-6 border-end pe-4">
                            <h6 class="fw-bold text-primary mb-3">A. Data Pengguna</h6>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Nama Lengkap Admin</label>
                                <input type="text" name="name" class="form-control" required placeholder="Contoh: Budi Santoso">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Level Pengguna</label>
                                <select name="role" class="form-select" required>
                                    <option value="" disabled selected>Pilih Level...</option>
                                    <option value="admin_sekolah">Kepala Sekolah</option>
                                    <option value="admin_sekolah">Operator Sekolah</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Email Login</label>
                                <input type="email" name="email" class="form-control" required placeholder="admin@sekolah.sch.id">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Password Lengkap</label>
                                <input type="text" name="password" class="form-control" required placeholder="Contoh: Silawas2026!">
                            </div>
                        </div>

                        {{-- Kolom Kanan: Data Sekolah --}}
                        <div class="col-md-6 ps-4">
                            <h6 class="fw-bold text-primary mb-3">B. Data Sekolah Baru</h6>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Nama Sekolah</label>
                                <input type="text" name="school_name" class="form-control" required placeholder="Contoh: SMAN 1 Surabaya">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Level Sekolah</label>
                                <select name="school_level" class="form-select" required>
                                    <option value="" disabled selected>Pilih Level...</option>
                                    <option value="SD">SD</option>
                                    <option value="SMP">SMP</option>
                                    <option value="SMA">SMA</option>
                                    <option value="SMK">SMK</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Status Sekolah</label>
                                <select name="school_status" class="form-select" required>
                                    <option value="" disabled selected>Pilih Status...</option>
                                    <option value="Negeri">Negeri</option>
                                    <option value="Swasta">Swasta</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 bg-light rounded-bottom-4">
                    <button type="submit" class="btn btn-primary w-100 fw-bold">Buat Akun & Simpan Sekolah</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection