@extends('layouts.app')

@section('title', 'Administrator - Kelola Admin Sekolah')

@section('content')
<div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3">
    <div>
        <h2 class="display-6 fw-extrabold font-headline mb-0">Kelola Pengguna</h2>
        <p class="text-muted small mb-0">Manajemen akun Administrator Sekolah dan Pengawas.</p>
    </div>
    
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary fw-bold d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahPengawas">
            <span class="material-symbols-outlined fs-6">shield_person</span> Tambah Pengawas
        </button>
        <button class="btn btn-outline-primary fw-bold d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
            <span class="material-symbols-outlined fs-6">person_add</span> Tambah Admin Sekolah
        </button>
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
                        <th width="5%" class="text-center">No.</th>
                        <th>Nama Pengguna</th>
                        <th>Email Login</th>
                        <th>Level Pengguna</th>
                        <th>ID & Nama Sekolah</th>
                        <th>Status Sekolah</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    @forelse($users as $user)
                    <tr class="user-row">
                        <td class="text-center text-muted fw-bold row-number"></td>
                        
                        <td class="fw-bold user-name">{{ $user->name }}</td>
                        <td class="user-email">{{ $user->email }}</td>
                        <td>
                            @if($user->role === 'pengawas')
                                <span class="badge bg-primary text-white">Pengawas</span>
                            @else
                                <span class="badge bg-primary text-white">
                                    {{ $user->role ?? 'Operator' }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($user->role === 'pengawas')
                                <span class="badge bg-primary text-white">Semua Sekolah Binaan</span>
                            @else
                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                    {{ $user->school->name ?? ($user->school_name ?? 'Tidak ada data') }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($user->role === 'pengawas')
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
                                                        <option value="pengawas" {{ $user->role == 'pengawas' ? 'selected' : '' }}>Pengawas</option>
                                                        <option value="admin_sekolah" {{ $user->role == 'admin_sekolah' ? 'selected' : '' }}>Admin Sekolah / Operator</option>
                                                    </select>
                                                </div>

                                                {{-- Pilihan Sekolah --}}
                                                <div class="mb-3" id="editSchoolField{{ $user->id }}" style="{{ $user->role == 'pengawas' ? 'display:none;' : '' }}">
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
                    <p class="small text-muted mb-4">Akun pengawas memiliki hak akses tak terbatas untuk memantau dan mengevaluasi seluruh sekolah binaan.</p>
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
        const userRows = Array.from(document.querySelectorAll('.user-row')); 
        const notFoundRow = document.getElementById('notFoundRow');
        const paginationControls = document.getElementById('paginationControls');
        const pageInfo = document.getElementById('pageInfo');

        let currentPage = 1;
        let rowsPerPage = parseInt(entriesSelect.value); 

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

            userRows.forEach(row => row.style.display = 'none');

            if (filteredRows.length === 0 && userRows.length > 0) {
                notFoundRow.style.display = '';
                paginationControls.innerHTML = '';
                pageInfo.textContent = 'Menampilkan 0 data';
                return;
            } else {
                notFoundRow.style.display = 'none';
            }

            const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
            if (currentPage > totalPages) currentPage = totalPages || 1;

            const startIndex = (currentPage - 1) * rowsPerPage;
            const endIndex = startIndex + rowsPerPage;

            const rowsToShow = filteredRows.slice(startIndex, endIndex);
            rowsToShow.forEach((row, index) => {
                row.style.display = ''; 
                const numberCell = row.querySelector('.row-number');
                if(numberCell) {
                    numberCell.textContent = startIndex + index + 1;
                }
            });

            const endItem = Math.min(endIndex, filteredRows.length);
            pageInfo.textContent = `Menampilkan ${startIndex + 1} - ${endItem} dari total ${filteredRows.length} akun`;

            renderPaginationUI(totalPages);
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

        renderTable();
    });
</script>
@endsection