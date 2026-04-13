@extends('layouts.app')

@section('title', 'Administrator - Kelola Admin Sekolah')

@section('content')
<div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3">
    <div>
        <h2 class="display-6 fw-extrabold font-headline mb-0">Kelola Pengguna</h2>
        <p class="text-muted small mb-0">Manajemen akun Administrator Sekolah dan Pengawas.</p>
    </div>
    
    {{-- BAGIAN YANG DIUBAH: Dua Tombol Terpisah --}}
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

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    {{-- Header Tabel & KOTAK PENCARIAN --}}
    <div class="p-4 bg-light bg-opacity-50 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 border-bottom">
        <div>
            <h5 class="font-headline fw-bold mb-0">Daftar Pengguna</h5>
        </div>
        
        <div class="input-group" style="max-width: 300px;">
            <span class="input-group-text bg-white border-end-0">
                <span class="material-symbols-outlined fs-6 text-muted">search</span>
            </span>
            <input type="text" id="searchUser" class="form-control border-start-0 ps-0" placeholder="Cari nama atau email...">
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
                <tbody>
                    @forelse($users as $user)
                    <tr class="user-row">
                        <td class="text-center text-muted fw-bold row-number"></td>
                        
                        <td class="fw-bold user-name">{{ $user->name }}</td>
                        <td class="user-email">{{ $user->email }}</td>
                        <td>
                            @if($user->role === 'pengawas')
                                <span class="badge bg-primary text-white">Pengawas</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                    {{ $user->role ?? 'Operator' }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($user->role === 'pengawas')
                                <span class="text-muted small fst-italic">Semua Sekolah Binaan</span>
                            @else
                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                    [ID: {{ $user->school_id ?? '-' }}] {{ $user->school->name ?? ($user->school_name ?? 'Tidak ada data') }}
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
                        <td colspan="7" class="text-center py-4 text-muted small">Belum ada data admin sekolah.</td>
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

{{-- ========================================== --}}
{{-- MODAL BARU: TAMBAH AKUN PENGAWAS --}}
{{-- ========================================== --}}
<div class="modal fade" id="modalTambahPengawas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title font-headline fw-bold">Tambah Akun Pengawas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                {{-- Mengirim data role 'pengawas' secara tersembunyi --}}
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

{{-- ========================================== --}}
{{-- MODAL LAMA: TAMBAH ADMIN & SEKOLAH --}}
{{-- ========================================== --}}
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

{{-- SCRIPT: Live Search + Sliding Pagination + Auto Numbering --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchUser');
        const userRows = Array.from(document.querySelectorAll('.user-row')); 
        const notFoundRow = document.getElementById('notFoundRow');
        const paginationControls = document.getElementById('paginationControls');
        const pageInfo = document.getElementById('pageInfo');

        let currentPage = 1;
        const rowsPerPage = 5; 

        function renderTable() {
            const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
            
            // 1. Filter data
            const filteredRows = userRows.filter(row => {
                const userName = row.querySelector('.user-name').textContent.toLowerCase();
                const userEmail = row.querySelector('.user-email').textContent.toLowerCase();
                return userName.includes(searchTerm) || userEmail.includes(searchTerm);
            });

            // 2. Sembunyikan semua baris
            userRows.forEach(row => row.style.display = 'none');

            // 3. Tampilkan pesan jika kosong
            if (filteredRows.length === 0 && userRows.length > 0) {
                notFoundRow.style.display = '';
                paginationControls.innerHTML = '';
                pageInfo.textContent = 'Menampilkan 0 data';
                return;
            } else {
                notFoundRow.style.display = 'none';
            }

            // 4. Hitung struktur halaman
            const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
            if (currentPage > totalPages) currentPage = totalPages || 1;

            const startIndex = (currentPage - 1) * rowsPerPage;
            const endIndex = startIndex + rowsPerPage;

            // 5. Munculkan baris dan berikan NOMOR URUT dinamis
            const rowsToShow = filteredRows.slice(startIndex, endIndex);
            rowsToShow.forEach((row, index) => {
                row.style.display = ''; // Tampilkan baris
                
                // Setel nomor urut (berdasarkan urutan filter)
                const numberCell = row.querySelector('.row-number');
                if(numberCell) {
                    numberCell.textContent = startIndex + index + 1;
                }
            });

            // 6. Update info teks
            const endItem = Math.min(endIndex, filteredRows.length);
            pageInfo.textContent = `Menampilkan ${startIndex + 1} - ${endItem} dari total ${filteredRows.length} akun`;

            // 7. Render tombol halaman
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

        if(searchInput) {
            searchInput.addEventListener('keyup', function() {
                currentPage = 1;
                renderTable();
            });
        }

        renderTable();
    });
</script>
@endsection