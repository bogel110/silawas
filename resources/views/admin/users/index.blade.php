@extends('layouts.app')

@section('title', 'Administrator - Kelola Admin Sekolah')

@push('styles')
    <style>
        .user-admin-hero {
            overflow: hidden;
        }

        .user-admin-actions {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.75rem;
            width: min(100%, 460px);
        }

        .user-admin-action {
            --action-rgb: 13, 110, 253;
            --action-color: #0d6efd;
            justify-content: flex-start;
            min-height: 50px;
            border: 1px solid rgba(var(--action-rgb), 0.55) !important;
            border-radius: 12px;
            padding-inline: 1rem;
            background:
                linear-gradient(135deg, rgba(var(--action-rgb), 0.13), rgba(var(--action-rgb), 0.035)),
                var(--surface) !important;
            color: var(--action-color) !important;
            white-space: normal;
            min-width: 0;
            line-height: 1.15;
            text-align: left;
            box-shadow: 0 10px 22px rgba(var(--action-rgb), 0.08) !important;
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease, background 0.18s ease, color 0.18s ease;
        }

        .user-admin-action span:last-child {
            min-width: 0;
            overflow-wrap: anywhere;
        }

        .user-admin-action.is-primary {
            --action-rgb: 15, 107, 125;
            --action-color: var(--brand-800);
            background:
                linear-gradient(135deg, rgba(var(--action-rgb), 0.16), rgba(var(--action-rgb), 0.05)),
                var(--surface) !important;
        }

        .user-admin-action.is-success {
            --action-rgb: 25, 135, 84;
            --action-color: #198754;
            background:
                linear-gradient(135deg, rgba(var(--action-rgb), 0.15), rgba(var(--action-rgb), 0.04)),
                var(--surface) !important;
        }

        .user-admin-action:hover,
        .user-admin-action:focus,
        .user-admin-action:focus-visible {
            border-color: rgba(var(--action-rgb), 0.9) !important;
            color: var(--action-color) !important;
            transform: translateY(-2px);
            box-shadow:
                0 0 0 4px rgba(var(--action-rgb), 0.14),
                0 16px 30px rgba(var(--action-rgb), 0.2) !important;
        }

        .user-admin-action:active {
            transform: translateY(0) scale(0.985);
            box-shadow:
                0 0 0 5px rgba(var(--action-rgb), 0.2),
                0 8px 18px rgba(var(--action-rgb), 0.18) !important;
        }

        html[data-theme="dark"] .user-admin-action {
            --action-color: #7fe8f2;
            background:
                linear-gradient(135deg, rgba(var(--action-rgb), 0.22), rgba(var(--action-rgb), 0.055)),
                #102d34 !important;
            color: var(--action-color) !important;
            box-shadow: 0 10px 22px rgba(0, 0, 0, 0.18) !important;
        }

        html[data-theme="dark"] .user-admin-action.is-primary {
            --action-rgb: 99, 199, 210;
            --action-color: #9ff5fb;
            background:
                linear-gradient(135deg, rgba(var(--action-rgb), 0.22), rgba(var(--action-rgb), 0.055)),
                #102d34 !important;
        }

        html[data-theme="dark"] .user-admin-action.is-success {
            --action-rgb: 35, 197, 94;
            --action-color: #34d399;
            background:
                linear-gradient(135deg, rgba(var(--action-rgb), 0.2), rgba(var(--action-rgb), 0.05)),
                #102d34 !important;
        }

        html[data-theme="dark"] .user-admin-action:hover,
        html[data-theme="dark"] .user-admin-action:focus,
        html[data-theme="dark"] .user-admin-action:focus-visible {
            color: var(--action-color) !important;
            box-shadow:
                0 0 0 4px rgba(var(--action-rgb), 0.16),
                0 18px 34px rgba(0, 0, 0, 0.34) !important;
        }

        @media (max-width: 575.98px) {
            .user-admin-actions {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
<div class="hero-panel user-admin-hero mb-4">
    <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-4">
        <div>
            <span class="section-kicker mb-3">
                <span class="material-symbols-outlined" style="font-size: 1rem;">manage_accounts</span>
                Administrator
            </span>
            <h2 class="display-6 fw-extrabold font-headline mb-1">Kelola Pengguna</h2>
            <p class="text-soft small mb-0">Manajemen akun Super Admin, Pengawas, dan Administrator Sekolah.</p>
        </div>
        
        <div class="user-admin-actions">
            <button class="btn btn-primary fw-bold d-flex align-items-center gap-2 shadow-sm user-admin-action is-primary" data-bs-toggle="modal" data-bs-target="#modalTambahSuperAdmin">
                <span class="material-symbols-outlined fs-6">admin_panel_settings</span>
                <span>Tambah Super Admin</span>
            </button>
            <button class="btn btn-outline-primary fw-bold d-flex align-items-center gap-2 shadow-sm user-admin-action" data-bs-toggle="modal" data-bs-target="#modalTambahPengawas">
                <span class="material-symbols-outlined fs-6">shield_person</span>
                <span>Tambah Pengawas</span>
            </button>
            <button class="btn btn-outline-success fw-bold d-flex align-items-center gap-2 shadow-sm user-admin-action is-success" data-bs-toggle="modal" data-bs-target="#modalImportAdmin">
                <span class="material-symbols-outlined fs-6">upload_file</span>
                <span>Import Excel</span>
            </button>
            <button class="btn btn-outline-primary fw-bold d-flex align-items-center gap-2 shadow-sm user-admin-action" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
                <span class="material-symbols-outlined fs-6">person_add</span>
                <span>Tambah Admin Sekolah</span>
            </button>
        </div>
    </div>
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

@if(session('import_errors'))
    <div class="alert alert-warning small">
        <div class="fw-bold mb-2">Beberapa baris import dilewati:</div>
        <ul class="mb-0">
            @foreach(session('import_errors') as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    
    {{-- HEADER TABEL: Terbagi 2 Baris agar rapi & sejajar ujung ke ujung --}}
    <div class="card-header bg-light bg-opacity-50 border-bottom pt-4 pb-3 px-4">
        
        {{-- Baris 1: Judul --}}
        <div class="mb-3">
            <h5 class="font-headline fw-bold mb-0">Daftar Pengguna</h5>
        </div>

        {{-- Baris 2: Data Entry (Kiri) & Search (Kanan) Sejajar --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            
            {{-- Data Entry (Tampil Data) --}}
            <div class="d-flex align-items-center gap-2">
                <span class="small text-muted fw-bold d-none d-sm-inline">Tampilkan</span>
                <select id="entriesUser" class="form-select form-select-sm border-0 shadow-sm bg-white" style="width: auto; cursor: pointer;">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                <span class="small text-muted fw-bold d-none d-sm-inline">Data</span>
            </div>

            {{-- Kotak Pencarian (Search) --}}
            <div class="input-group input-group-sm shadow-sm" style="max-width: 250px;">
                <span class="input-group-text bg-white border-0">
                    <span class="material-symbols-outlined fs-6 text-muted">search</span>
                </span>
                <input type="text" id="searchUser" class="form-control border-0 ps-0 bg-white" placeholder="Cari data pengguna...">
            </div>

        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive p-4 pt-3">
            <table class="table align-middle mb-0" id="userTable">
                <thead class="bg-light text-muted small">
                    <tr>
                        <th width="5%" class="text-center sort-user-header" data-sort-key="no">
                            <button type="button" class="sort-user-btn justify-content-center" data-sort-key="no">
                                No. <span class="material-symbols-outlined sort-icon">unfold_more</span>
                            </button>
                        </th>
                        <th class="sort-user-header" data-sort-key="name">
                            <button type="button" class="sort-user-btn" data-sort-key="name">
                                Nama Pengguna <span class="material-symbols-outlined sort-icon">unfold_more</span>
                            </button>
                        </th>
                        <th class="sort-user-header" data-sort-key="email">
                            <button type="button" class="sort-user-btn" data-sort-key="email">
                                Email Login <span class="material-symbols-outlined sort-icon">unfold_more</span>
                            </button>
                        </th>
                        <th class="sort-user-header" data-sort-key="level">
                            <button type="button" class="sort-user-btn" data-sort-key="level">
                                Level Pengguna <span class="material-symbols-outlined sort-icon">unfold_more</span>
                            </button>
                        </th>
                        <th>ID & Nama Sekolah</th>
                        <th>Status Sekolah</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    @forelse($users as $user)
                    <tr class="user-row"
                        data-sort-index="{{ $loop->index }}"
                        data-sort-name="{{ $user->name }}"
                        data-sort-email="{{ $user->email }}"
                        data-sort-level="{{ $user->role ?? 'admin_sekolah' }}">
                        <td class="text-center text-muted fw-bold row-number"></td>
                        
                        <td class="fw-bold user-name">{{ $user->name }}</td>
                        <td class="user-email">{{ $user->email }}</td>
                        <td>
                            @if($user->role === 'super_admin')
                                <span class="badge bg-dark text-white">Super Admin</span>
                            @elseif($user->role === 'pengawas')
                                <span class="badge bg-primary text-white">Pengawas</span>
                            @else
                                <span class="badge bg-primary text-white">
                                    {{ $user->role ?? 'Operator' }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($user->role === 'super_admin')
                                <span class="badge bg-dark text-white">Akses semua sekolah</span>
                            @elseif($user->role === 'pengawas')
                                <span class="badge bg-primary bg-opacity-10 text-primary">{{ $user->supervisedSchools->count() }} sekolah binaan</span>
                            @else
                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                    {{ $user->school->name ?? ($user->school_name ?? 'Tidak ada data') }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @if(in_array($user->role, ['super_admin', 'pengawas'], true))
                                <span class="text-muted small">-</span>
                            @else
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
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-3">
                                {{-- Tombol Edit User --}}
                                <button type="button" class="btn btn-link text-primary p-0" data-bs-toggle="modal" data-bs-target="#modalEditUser{{ $user->id }}" title="Edit Data">
                                    <span class="material-symbols-outlined fs-5">edit</span>
                                </button>

                                <button type="button" class="btn btn-link text-success p-0" data-bs-toggle="modal" data-bs-target="#modalResetPassword{{ $user->id }}" title="Reset Password">
                                    <span class="material-symbols-outlined fs-5">lock_reset</span>
                                </button>
                                
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Hapus akun ini secara permanen?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger p-0" title="Hapus Akun">
                                        <span class="material-symbols-outlined fs-5">delete</span>
                                    </button>
                                </form>
                            </div>

                            {{-- MODAL EDIT USER --}}
                            <div class="modal fade" id="modalEditUser{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow rounded-4 text-start">
                                        <div class="modal-header border-bottom-0">
                                            <h5 class="modal-title font-headline fw-bold">Edit Data Pengguna</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body p-4 pt-0">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Nama Lengkap</label>
                                                    <input type="text" name="name" class="form-control" required value="{{ $user->name }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Email Login</label>
                                                    <input type="email" name="email" class="form-control" required value="{{ $user->email }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Level Pengguna</label>
                                                    <select name="role" class="form-select" required onchange="toggleEditSchool(this, '{{ $user->id }}')">
                                                        <option value="super_admin" {{ $user->role == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                                        <option value="pengawas" {{ $user->role == 'pengawas' ? 'selected' : '' }}>Pengawas</option>
                                                        <option value="admin_sekolah" {{ $user->role == 'admin_sekolah' ? 'selected' : '' }}>Admin Sekolah / Operator</option>
                                                    </select>
                                                </div>

                                                {{-- Pilihan Sekolah --}}
                                                <div class="mb-3" id="editSchoolField{{ $user->id }}" style="{{ $user->role == 'admin_sekolah' ? '' : 'display:none;' }}">
                                                    <label class="form-label small fw-bold text-primary">Penempatan Sekolah</label>
                                                    <select name="school_id" class="form-select choices-school-select">
                                                        <option value="">-- Silakan Pilih Sekolah --</option>
                                                        @foreach($schools as $sch)
                                                            <option value="{{ $sch->id }}" {{ $user->school_id == $sch->id ? 'selected' : '' }}>
                                                                {{ $sch->name }} ({{ $sch->level }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-top-0 bg-light rounded-bottom-4">
                                                <button type="button" class="btn btn-outline-secondary btn-sm fw-bold" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary btn-sm fw-bold px-4">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
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
                                                    <input type="text" name="password" class="form-control" required placeholder="Silawas2026">
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
                    <tr id="emptyRow">
                        <td colspan="7" class="text-center py-4 text-muted small">Belum ada data pengguna.</td>
                    </tr>
                    @endforelse

                    <tr id="notFoundRow" style="display: none;">
                        <td colspan="7" class="text-center text-muted py-4">Pengguna yang dicari tidak ditemukan.</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="p-4 pt-3 bg-light bg-opacity-25 border-top d-flex justify-content-between align-items-center">
            <small class="text-muted fw-semibold" id="pageInfo">Menampilkan data...</small>
            <nav id="paginationControls"></nav>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH SUPER ADMIN --}}
<div class="modal fade" id="modalTambahSuperAdmin" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title font-headline fw-bold">Tambah Akun Super Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <input type="hidden" name="role" value="super_admin">
                <div class="modal-body p-4 pt-0">
                    <p class="small text-muted mb-4">Akun super admin dapat melihat seluruh sekolah dan mengatur sekolah binaan untuk setiap pengawas.</p>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" required placeholder="Contoh: Super Admin SILAWAS">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email Login</label>
                        <input type="email" name="email" class="form-control" required placeholder="superadmin@silawas.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Password Awal (Min. 8 Karakter)</label>
                        <input type="text" name="password" class="form-control" required placeholder="Contoh: Silawas2026!">
                    </div>
                </div>
                <div class="modal-footer border-top-0 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-dark btn-sm fw-bold w-50">Buat Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL IMPORT ADMIN SEKOLAH --}}
<div class="modal fade" id="modalImportAdmin" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom-0">
                <div>
                    <h5 class="modal-title font-headline fw-bold">Import Admin Sekolah</h5>
                    <p class="small text-muted mb-0">Tambahkan admin sekolah secara kolektif dari file Excel.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4 pt-0">
                    <div class="import-format-panel small mb-4">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
                            <div>
                                <div class="fw-bold">Format kolom baris pertama</div>
                                <div class="text-muted">Download template, edit datanya, lalu upload kembali.</div>
                            </div>
                            <a href="{{ route('admin.users.import_template') }}" class="btn btn-outline-success btn-sm fw-bold d-flex align-items-center justify-content-center gap-1">
                                <span class="material-symbols-outlined fs-6">download</span>
                                Download Format Excel
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0 import-format-table">
                                <thead>
                                    <tr>
                                        <th>nama_admin</th>
                                        <th>email</th>
                                        <th>password</th>
                                        <th>nama_sekolah</th>
                                        <th>level_sekolah</th>
                                        <th>status_sekolah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Budi Santoso</td>
                                        <td>admin@sekolah.sch.id</td>
                                        <td>Silawas2026!</td>
                                        <td>SMAN 1 Contoh</td>
                                        <td>SMA</td>
                                        <td>Negeri</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="small text-muted mt-3">
                            Kolom tidak boleh diubah namanya. Isi <strong>level_sekolah</strong> dengan SMA/SMK dan <strong>status_sekolah</strong> dengan Negeri/Swasta.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">File Excel / CSV</label>
                        <input type="file" name="import_file" class="form-control import-file-input" accept=".xlsx,.csv,.txt" required>
                        <div class="form-text">
                            Gunakan file .xlsx atau .csv. Email yang sudah terdaftar akan dilewati dan dilaporkan.
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm fw-bold px-4">
                        <span class="material-symbols-outlined fs-6 align-middle">upload_file</span>
                        Import Admin Sekolah
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH PENGAWAS --}}
<div class="modal fade" id="modalTambahPengawas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title font-headline fw-bold">Tambah Akun Pengawas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <input type="hidden" name="role" value="pengawas"> 
                <div class="modal-body p-4 pt-0">
                    <p class="small text-muted mb-4">Akun pengawas hanya dapat memantau sekolah yang ditugaskan melalui menu Pengawas Binaan.</p>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Lengkap Pengawas</label>
                        <input type="text" name="name" class="form-control" required placeholder="Contoh: Drs. Budi Santoso, M.Pd">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email Login</label>
                        <input type="email" name="email" class="form-control" required placeholder="pengawas@silawas.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Password Awal (Min. 8 Karakter)</label>
                        <input type="text" name="password" class="form-control" required placeholder="Contoh: Silawas2026!">
                    </div>
                </div>
                <div class="modal-footer border-top-0 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold w-50">Buat Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH ADMIN & SEKOLAH --}}
<div class="modal fade" id="modalTambahUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title font-headline fw-bold">Tambah Admin & Sekolah Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 pt-0">
                    <div class="row">
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

{{-- ========================================================================= --}}
{{-- SCRIPT: CHOICES.JS (Pencarian Dropdown Sekolah) & LOGIKA HALAMAN --}}
{{-- ========================================================================= --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<style>
    .import-format-panel {
        border: 1px solid rgba(15, 107, 125, 0.12);
        border-radius: 18px;
        background: rgba(15, 107, 125, 0.06);
        padding: 1rem;
    }
    .import-format-table {
        min-width: 720px;
    }
    .import-format-table th {
        color: var(--text-soft);
        font-size: 0.68rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        white-space: nowrap;
    }
    .import-format-table td {
        color: var(--text-main);
        font-weight: 600;
        white-space: nowrap;
    }
    html[data-theme="dark"] .import-format-panel {
        border-color: rgba(180, 221, 227, 0.14);
        background: rgba(99, 199, 210, 0.08);
    }
    html[data-theme="dark"] .import-format-table {
        --bs-table-color: var(--text-main);
        --bs-table-bg: transparent;
        --bs-table-border-color: rgba(180, 221, 227, 0.12);
    }
    html[data-theme="dark"] .import-format-table th,
    html[data-theme="dark"] .import-format-table td {
        color: var(--text-main) !important;
    }
    .import-file-input::file-selector-button {
        margin: -0.375rem 0.75rem -0.375rem -0.75rem;
        min-height: 44px;
        border: 0;
        border-right: 1px solid rgba(15, 107, 125, 0.12);
        background: rgba(15, 107, 125, 0.08);
        color: var(--brand-800);
        font-weight: 800;
    }
    html[data-theme="dark"] .import-file-input {
        background-color: #112a31 !important;
        border-color: rgba(180, 221, 227, 0.18) !important;
        color: #e7f3f5 !important;
        -webkit-text-fill-color: #e7f3f5 !important;
        color-scheme: dark;
    }
    html[data-theme="dark"] .import-file-input::file-selector-button {
        border-right-color: rgba(180, 221, 227, 0.18);
        background: #1d4b56;
        color: #ffffff;
        -webkit-text-fill-color: #ffffff;
    }
    html[data-theme="dark"] #modalImportAdmin .form-text {
        color: #9fb8bf !important;
    }
    .sort-user-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        width: 100%;
        border: 0;
        background: transparent;
        color: inherit;
        padding: 0;
        font: inherit;
        font-weight: 800;
        letter-spacing: inherit;
        text-align: left;
        text-transform: inherit;
    }
    .sort-user-btn:hover,
    .sort-user-btn:focus {
        color: var(--brand-700);
    }
    .sort-user-btn .sort-icon {
        color: var(--text-soft);
        font-size: 1rem;
        line-height: 1;
    }
    .sort-user-btn.active-sort {
        color: var(--brand-700);
    }
    .sort-user-btn.active-sort .sort-icon {
        color: var(--brand-700);
    }
    html[data-theme="dark"] .sort-user-btn:hover,
    html[data-theme="dark"] .sort-user-btn:focus,
    html[data-theme="dark"] .sort-user-btn.active-sort,
    html[data-theme="dark"] .sort-user-btn.active-sort .sort-icon {
        color: var(--brand-800);
    }
    .sort-user-header {
        cursor: pointer;
        user-select: none;
    }

    /* Styling Choices agar cocok dengan Bootstrap 5 */
    .choices { margin-bottom: 0; font-size: 0.9rem; }
    .choices__inner {
        background-color: #fff !important;
        border: 1px solid #dee2e6 !important;
        border-radius: 0.375rem !important;
        padding: 0.2rem 1rem !important;
        min-height: 38px !important;
        display: flex; align-items: center;
    }
    .choices[data-type*="select-one"] .choices__input {
        width: 100% !important; max-width: 100% !important;
        background-color: #f8f9fa !important; border: 1px solid #dee2e6 !important;
        border-radius: 0.375rem !important; padding: 10px !important;
        margin: 5px 0 10px 0 !important;
    }
    .choices__list--dropdown { border-radius: 0.5rem !important; z-index: 9999 !important; }
    .choices__list--dropdown .choices__item--selectable.is-highlighted {
        background-color: #0d6efd !important; color: white !important;
    }
</style>

<script>
    // FUNGSI UNTUK TOGGLE PILIHAN SEKOLAH DI MODAL EDIT
    function toggleEditSchool(select, id) {
        const schoolField = document.getElementById('editSchoolField' + id);
        if(select.value === 'admin_sekolah') {
            schoolField.style.display = 'block';
        } else {
            schoolField.style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // 1. INIT CHOICES.JS PADA SEMUA MODAL EDIT
        const schoolSelects = document.querySelectorAll('.choices-school-select');
        schoolSelects.forEach(select => {
            new Choices(select, {
                searchEnabled: true,
                searchPlaceholderValue: 'Ketik nama sekolah...',
                itemSelectText: '',
                noResultsText: 'Sekolah tidak ditemukan',
                noChoicesText: 'Tidak ada pilihan',
                shouldSort: false,
                placeholder: true
            });
        });

        // 2. SCRIPT PENCARIAN & PAGINATION MENGGUNAKAN JAVASCRIPT
        const searchInput = document.getElementById('searchUser');
        const entriesSelect = document.getElementById('entriesUser'); 
        const userTableBody = document.getElementById('userTableBody');
        const userRows = Array.from(document.querySelectorAll('.user-row')); 
        const notFoundRow = document.getElementById('notFoundRow');
        const paginationControls = document.getElementById('paginationControls');
        const pageInfo = document.getElementById('pageInfo');
        const sortButtons = Array.from(document.querySelectorAll('.sort-user-btn'));
        const sortHeaders = Array.from(document.querySelectorAll('.sort-user-header'));

        let currentPage = 1;
        let rowsPerPage = parseInt(entriesSelect.value); 
        let sortKey = 'no';
        let sortDirection = 'asc';

        function getSortValue(row, key) {
            if (key === 'no') {
                return parseInt(row.dataset.sortIndex || '0', 10);
            }

            if (key === 'name') {
                return (row.dataset.sortName || '').trim().toLowerCase();
            }

            if (key === 'email') {
                return (row.dataset.sortEmail || '').trim().toLowerCase();
            }

            if (key === 'level') {
                return (row.dataset.sortLevel || '').trim().toLowerCase();
            }

            return '';
        }

        function sortRows(rows) {
            return [...rows].sort((a, b) => {
                const valueA = getSortValue(a, sortKey);
                const valueB = getSortValue(b, sortKey);
                let result = 0;

                if (typeof valueA === 'number' && typeof valueB === 'number') {
                    result = valueA - valueB;
                } else {
                    result = String(valueA).localeCompare(String(valueB), 'id', { numeric: true, sensitivity: 'base' });
                }

                return sortDirection === 'asc' ? result : -result;
            });
        }

        function updateSortButtons() {
            sortButtons.forEach(button => {
                const isActive = button.dataset.sortKey === sortKey;
                const icon = button.querySelector('.sort-icon');

                button.classList.toggle('active-sort', isActive);

                if (icon) {
                    icon.textContent = isActive ? (sortDirection === 'asc' ? 'arrow_upward' : 'arrow_downward') : 'unfold_more';
                }
            });
        }

        function renderTable() {
            const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
            
            // PERUBAHAN UTAMA: Filter khusus agar mencari secara akurat ke kolom 1 sampai 5
            const filteredRows = userRows.filter(row => {
                const columns = row.querySelectorAll('td');
                if (columns.length < 6) return false;

                const nama    = columns[1].textContent.toLowerCase();
                const email   = columns[2].textContent.toLowerCase();
                const level   = columns[3].textContent.toLowerCase();
                const sekolah = columns[4].textContent.toLowerCase();
                const status  = columns[5].textContent.toLowerCase();

                // Kembalikan True jika keyword ada di salah satu kolom tersebut
                return nama.includes(searchTerm) || 
                       email.includes(searchTerm) || 
                       level.includes(searchTerm) || 
                       sekolah.includes(searchTerm) || 
                       status.includes(searchTerm);
            });
            const sortedRows = sortRows(filteredRows);

            userRows.forEach(row => row.style.display = 'none');

            if (filteredRows.length === 0 && userRows.length > 0) {
                if (userTableBody && notFoundRow) {
                    userTableBody.replaceChildren(notFoundRow);
                }
                notFoundRow.style.display = '';
                paginationControls.innerHTML = '';
                pageInfo.textContent = 'Menampilkan 0 data';
                updateSortButtons();
                return;
            } else {
                notFoundRow.style.display = 'none';
            }

            if (userRows.length === 0) {
                pageInfo.textContent = 'Menampilkan 0 data';
                paginationControls.innerHTML = '';
                updateSortButtons();
                return;
            }

            const totalPages = Math.ceil(sortedRows.length / rowsPerPage);
            if (currentPage > totalPages) currentPage = totalPages || 1;

            const startIndex = (currentPage - 1) * rowsPerPage;
            const endIndex = startIndex + rowsPerPage;

            const rowsToShow = sortedRows.slice(startIndex, endIndex);

            if (userTableBody) {
                userTableBody.replaceChildren(...rowsToShow, notFoundRow);
            }

            rowsToShow.forEach((row, index) => {
                row.style.display = ''; 
                const numberCell = row.querySelector('.row-number');
                if(numberCell) {
                    numberCell.textContent = startIndex + index + 1;
                }
            });

            const endItem = Math.min(endIndex, sortedRows.length);
            pageInfo.textContent = `Menampilkan ${startIndex + 1} - ${endItem} dari total ${sortedRows.length} akun`;

            renderPaginationUI(totalPages);
            updateSortButtons();
        }

        function renderPaginationUI(totalPages) {
            if (totalPages <= 1) {
                paginationControls.innerHTML = '';
                return;
            }

            let html = '<ul class="pagination pagination-sm mb-0 shadow-sm">';
            
            html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                        <a class="page-link text-primary" href="#" data-page="${currentPage - 1}">Prev</a>
                     </li>`;

            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, startPage + 4);
            if (endPage - startPage < 4) {
                startPage = Math.max(1, endPage - 4);
            }

            for (let i = startPage; i <= endPage; i++) {
                html += `<li class="page-item ${currentPage === i ? 'active' : ''}">
                            <a class="page-link" href="#" data-page="${i}">${i}</a>
                         </li>`;
            }

            html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                        <a class="page-link text-primary" href="#" data-page="${currentPage + 1}">Next</a>
                     </li>`;
            
            html += '</ul>';
            paginationControls.innerHTML = html;

            paginationControls.querySelectorAll('.page-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const page = parseInt(this.getAttribute('data-page'));
                    if (!isNaN(page) && page >= 1 && page <= totalPages) {
                        currentPage = page;
                        renderTable();
                    }
                });
            });
        }

        // Listener untuk Kotak Pencarian
        if(searchInput) {
            searchInput.addEventListener('keyup', function() {
                currentPage = 1;
                renderTable();
            });
        }

        // Listener untuk Dropdown Tampil Data (Data Entry)
        if(entriesSelect) {
            entriesSelect.addEventListener('change', function() {
                rowsPerPage = parseInt(this.value);
                currentPage = 1;
                renderTable();
            });
        }

        sortHeaders.forEach(header => {
            header.addEventListener('click', function() {
                const nextSortKey = this.dataset.sortKey;

                if (sortKey === nextSortKey) {
                    sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
                } else {
                    sortKey = nextSortKey;
                    sortDirection = 'asc';
                }

                currentPage = 1;
                renderTable();
            });
        });

        renderTable();
    });
</script>
@endsection
