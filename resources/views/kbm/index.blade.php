@extends('layouts.app')

@section('title', 'Modul KBM (Kurikulum)')

@section('content')
    <div class="mb-4">
        <h2 class="display-6 fw-extrabold font-headline mb-0">PERANGKAT KBM (Kurikulum)</h2>
        <p class="text-muted small mb-0">Kelengkapan dokumen Kegiatan Belajar Mengajar.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 small mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close pb-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- KOTAK PENCARIAN KHUSUS PENGAWAS --}}
    @if(auth()->user()->role === 'pengawas')
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-primary bg-opacity-10 border-start border-4 border-primary">
            <div class="card-body p-4">
                <form action="{{ route('kbm.index') }}" method="GET" class="d-flex flex-column flex-md-row align-items-md-end gap-3">
                    <div class="flex-grow-1">
                        <label class="form-label small fw-bold text-primary">Pilih Sekolah Binaan</label>
                        <select name="school_id" class="form-select border-primary shadow-sm" required>
                            <option value="">-- Silakan Pilih Sekolah Terlebih Dahulu --</option>
                            @foreach($schools as $s)
                                <option value="{{ $s->id }}" {{ request('school_id') == $s->id ? 'selected' : '' }}>
                                    {{ $s->name }} ({{ $s->level }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm">
                        <span class="d-flex align-items-center gap-1">
                            <span class="material-symbols-outlined fs-6">search</span> Tampilkan Data
                        </span>
                    </button>
                </form>
            </div>
        </div>
    @endif

    {{-- TAMPILAN DATA & REKAP KBM (Muncul Jika Admin Login ATAU Pengawas Sudah Memilih Sekolah) --}}
    @if($selectedSchool)
        
        {{-- ==========================================
        {{-- PANEL REKAPITULASI DOKUMEN KBM --}}
        {{-- ========================================== --}}
        {{-- @php
            $totalKbm = $kbms->count();
            $intraCount = $kbms->filter(function($kbm) { return !empty($kbm->intra_link); })->count();
            $koCount = $kbms->filter(function($kbm) { return !empty($kbm->ko_link); })->count();
            $extraCount = $kbms->filter(function($kbm) { return !empty($kbm->extra_link); })->count();
        @endphp
        
        @if($totalKbm > 0)
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-bottom border-4 border-dark">
                        <small class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem;">Total Tahun Ajaran</small>
                        <h4 class="fw-bold mb-0 text-dark">{{ $totalKbm }} Data</h4>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-bottom border-4 border-primary">
                        <small class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem;">Intrakurikuler Terisi</small>
                        <h4 class="fw-bold mb-0 text-primary">{{ $intraCount }} / {{ $totalKbm }}</h4>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-bottom border-4 border-success">
                        <small class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem;">Kokurikuler Terisi</small>
                        <h4 class="fw-bold mb-0 text-success">{{ $koCount }} / {{ $totalKbm }}</h4>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-bottom border-4 border-info">
                        <small class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem;">Ekstrakurikuler Terisi</small>
                        <h4 class="fw-bold mb-0 text-info">{{ $extraCount }} / {{ $totalKbm }}</h4>
                    </div>
                </div>
            </div>
        @endif --}}

        {{-- TABEL DATA KBM --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <h5 class="font-headline fw-bold mb-0">
                        @if(auth()->user()->role === 'pengawas')
                            Dokumen KBM: <span class="text-primary">{{ $selectedSchool->name }}</span>
                        @else
                            Dokumen KBM Sekolah
                        @endif
                    </h5>
                    
                    {{-- Tombol Tambah KBM (Hanya Admin Sekolah) --}}
                    @if(auth()->user()->role === 'admin_sekolah' && auth()->user()->school_id == $selectedSchool->id)
                        <button type="button" class="btn btn-sm btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#modalKbm">
                            + Input Link KBM
                        </button>
                    @endif
                </div>

                <div class="d-flex flex-column flex-sm-row gap-2 align-items-sm-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="small text-muted fw-bold d-none d-md-inline">Tampilkan</span>
                        <select id="entriesKbm" class="form-select form-select-sm bg-light border-0 shadow-sm" style="width: auto; cursor: pointer;">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                    <div class="input-group input-group-sm shadow-sm" style="max-width: 200px;">
                        <span class="input-group-text bg-white border-end-0">
                            <span class="material-symbols-outlined fs-6 text-muted">search</span>
                        </span>
                        <input type="text" id="searchKbm" class="form-control border-start-0 ps-0" placeholder="Cari data...">
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="p-4 pt-3">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0" id="kbmTable">
                            <thead class="bg-light text-muted small">
                                <tr>
                                    <th class="ps-2 cursor-pointer sortable user-select-none hover-bg-light" title="Klik untuk mengurutkan Tahun">
                                        <div class="d-flex align-items-center gap-1">
                                            Tahun Pelajaran <span class="material-symbols-outlined fs-6 sort-icon text-primary">unfold_more</span>
                                        </div>
                                    </th>
                                    <th>Intrakurikuler</th>
                                    <th>Kokurikuler</th>
                                    <th>Ekstrakurikuler</th>
                                    
                                    {{-- Kolom Aksi Hanya untuk Admin Sekolah --}}
                                    @if(auth()->user()->role === 'admin_sekolah' && auth()->user()->school_id == $selectedSchool->id)
                                        <th class="text-center">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody id="kbmTableBody">
                                @forelse($kbms as $kbm)
                                <tr class="kbm-row small">
                                    <td class="fw-bold ps-2">{{ $kbm->tahun_pelajaran }}</td>
                                    
                                    <td>
                                        @if($kbm->intra_link) <a href="{{ $kbm->intra_link }}" target="_blank" class="badge bg-success text-decoration-none">Cek Berkas</a>
                                        @else <span class="badge bg-danger">Kosong</span> @endif
                                    </td>
                                    <td>
                                        @if($kbm->ko_link) <a href="{{ $kbm->ko_link }}" target="_blank" class="badge bg-success text-decoration-none">Cek Berkas</a>
                                        @else <span class="badge bg-danger">Kosong</span> @endif
                                    </td>
                                    <td>
                                        @if($kbm->extra_link) <a href="{{ $kbm->extra_link }}" target="_blank" class="badge bg-success text-decoration-none">Cek Berkas</a>
                                        @else <span class="badge bg-danger">Kosong</span> @endif
                                    </td>
                                    
                                    {{-- Aksi (Edit & Hapus) --}}
                                    @if(auth()->user()->role === 'admin_sekolah' && auth()->user()->school_id == $selectedSchool->id)
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button class="btn btn-link text-primary p-0" data-bs-toggle="modal" data-bs-target="#editModalKbm{{ $kbm->id }}" title="Edit Data">
                                                    <span class="material-symbols-outlined fs-6">edit</span>
                                                </button>
                                                <form action="{{ route('school.destroy_kbm', $kbm->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data KBM Tahun Pelajaran {{ $kbm->tahun_pelajaran }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-link text-danger p-0" title="Hapus Data">
                                                        <span class="material-symbols-outlined fs-6">delete</span>
                                                    </button>
                                                </form>
                                            </div>

                                            {{-- MODAL EDIT KBM --}}
                                            <div class="modal fade" id="editModalKbm{{ $kbm->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 shadow rounded-4 text-start">
                                                        <form action="{{ route('school.update_kbm', $kbm->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-header border-bottom-0">
                                                                <h1 class="modal-title fs-5 font-headline fw-bold">Edit Link KBM</h1>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body p-4 pt-0">
                                                                <div class="mb-3">
                                                                    <label class="small fw-bold">Pilih Tahun Pelajaran</label>
                                                                    <select name="tahun_pelajaran" class="form-select" required>
                                                                        <option value="2023/2024" {{ $kbm->tahun_pelajaran == '2023/2024' ? 'selected' : '' }}>2023/2024</option>
                                                                        <option value="2024/2025" {{ $kbm->tahun_pelajaran == '2024/2025' ? 'selected' : '' }}>2024/2025</option>
                                                                        <option value="2025/2026" {{ $kbm->tahun_pelajaran == '2025/2026' ? 'selected' : '' }}>2025/2026</option>
                                                                        <option value="2026/2027" {{ $kbm->tahun_pelajaran == '2026/2027' ? 'selected' : '' }}>2026/2027</option>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="small fw-bold">1. Intrakurikuler (RPP/Modul Ajar)</label>
                                                                    <input type="url" name="intra_link" class="form-control" value="{{ $kbm->intra_link }}">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="small fw-bold">2. Kokurikuler</label>
                                                                    <input type="url" name="ko_link" class="form-control" value="{{ $kbm->ko_link }}">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="small fw-bold">3. Ekstrakurikuler</label>
                                                                    <input type="url" name="extra_link" class="form-control" value="{{ $kbm->extra_link }}">
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer bg-light border-top-0 rounded-bottom-4">
                                                                <button type="button" class="btn btn-outline-secondary btn-sm fw-bold" data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-primary btn-sm fw-bold">Simpan Perubahan</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                                @empty
                                <tr id="emptyKbmRow"><td colspan="5" class="text-center small text-muted py-5">Belum ada data KBM untuk sekolah ini.</td></tr>
                                @endforelse

                                <tr id="notFoundKbm" style="display: none;">
                                    <td colspan="5" class="text-center small text-muted py-3">Data tidak ditemukan.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="p-3 bg-light bg-opacity-25 border-top d-flex justify-content-between align-items-center rounded-bottom-4">
                    <small class="text-muted fw-semibold" id="kbmPageInfo">Menampilkan data...</small>
                    <nav id="kbmPagination"></nav>
                </div>
            </div>
        </div>

        {{-- MODAL TAMBAH KBM BARU (Hanya Admin Sekolah) --}}
        @if(auth()->user()->role === 'admin_sekolah' && auth()->user()->school_id == $selectedSchool->id)
            <div class="modal fade" id="modalKbm" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow rounded-4 text-start">
                        <form action="{{ route('school.store_kbm', $selectedSchool->id) }}" method="POST">
                            @csrf
                            <div class="modal-header border-bottom-0">
                                <h1 class="modal-title fs-5 font-headline fw-bold">Input Link KBM Baru</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-4 pt-0">
                                <div class="mb-3">
                                    <label class="small fw-bold">Pilih Tahun Pelajaran</label>
                                    <select name="tahun_pelajaran" class="form-select" required>
                                        <option value="">-- Pilih Tahun --</option>
                                        <option value="2023/2024">2023/2024</option>
                                        <option value="2024/2025">2024/2025</option>
                                        <option value="2025/2026">2025/2026</option>
                                        <option value="2026/2027">2026/2027</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="small fw-bold">1. Intrakurikuler (Link G-Drive)</label>
                                    <input type="url" name="intra_link" class="form-control" placeholder="https://drive.google.com/...">
                                </div>
                                <div class="mb-3">
                                    <label class="small fw-bold">2. Kokurikuler (Link G-Drive)</label>
                                    <input type="url" name="ko_link" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label class="small fw-bold">3. Ekstrakurikuler (Link G-Drive)</label>
                                    <input type="url" name="extra_link" class="form-control">
                                </div>
                            </div>
                            <div class="modal-footer bg-light border-top-0 rounded-bottom-4">
                                <button type="button" class="btn btn-outline-secondary btn-sm fw-bold" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary btn-sm fw-bold">Simpan Data</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

    @elseif(auth()->user()->role === 'pengawas')
        {{-- ========================================== --}}
        {{-- EMPTY STATE (Jika Pengawas Belum Pilih Sekolah) --}}
        {{-- ========================================== --}}
        <div class="text-center py-5">
            <span class="material-symbols-outlined display-1 text-muted opacity-25 mb-3">auto_stories</span>
            <h5 class="fw-bold text-muted">Belum Ada Sekolah yang Dipilih</h5>
            <p class="small text-muted">Silakan gunakan menu dropdown di atas untuk melihat dokumen KBM sekolah binaan Anda.</p>
        </div>
    @endif

    {{-- SCRIPT: SEARCH, PAGINATION & SORTING --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tbody = document.getElementById('kbmTableBody');
            if(!tbody) return; // Mencegah error jika tbody tidak ter-render (saat empty state)
            
            let rows = Array.from(document.querySelectorAll('.kbm-row'));
            const paginationControls = document.getElementById('kbmPagination');
            const pageInfo = document.getElementById('kbmPageInfo');
            const searchInput = document.getElementById('searchKbm');
            const notFoundRow = document.getElementById('notFoundKbm');
            const entriesSelect = document.getElementById('entriesKbm'); 
            
            if(rows.length === 0) return;

            let currentPage = 1;
            let rowsPerPage = entriesSelect ? parseInt(entriesSelect.value) : 10; 

            if (entriesSelect) {
                entriesSelect.addEventListener('change', function(e) {
                    rowsPerPage = parseInt(e.target.value);
                    currentPage = 1; 
                    renderTable();
                });
            }

            if (searchInput) {
                searchInput.addEventListener('keyup', function() { currentPage = 1; renderTable(); });
            }

            function renderTable() {
                const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
                const filteredRows = rows.filter(row => row.textContent.toLowerCase().includes(searchTerm));

                rows.forEach(row => row.style.display = 'none');

                if (filteredRows.length === 0) {
                    if (notFoundRow) notFoundRow.style.display = '';
                    if (paginationControls) paginationControls.innerHTML = '';
                    if (pageInfo) pageInfo.textContent = 'Menampilkan 0 data';
                    return;
                } else {
                    if (notFoundRow) notFoundRow.style.display = 'none';
                }

                const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
                if (currentPage > totalPages) currentPage = totalPages || 1;

                const startIndex = (currentPage - 1) * rowsPerPage;
                const endIndex = startIndex + rowsPerPage;

                filteredRows.slice(startIndex, endIndex).forEach(row => row.style.display = '');

                if (pageInfo) pageInfo.textContent = `Menampilkan ${startIndex + 1} - ${Math.min(endIndex, filteredRows.length)} dari total ${filteredRows.length} data`;
                
                if (paginationControls) {
                    let html = '<ul class="pagination pagination-sm mb-0 shadow-sm">';
                    html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${currentPage - 1}">&laquo;</a></li>`;
                    for (let i = 1; i <= totalPages; i++) {
                        html += `<li class="page-item ${currentPage === i ? 'active' : ''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
                    }
                    html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${currentPage + 1}">&raquo;</a></li>`;
                    html += '</ul>';
                    paginationControls.innerHTML = html;
                    
                    paginationControls.querySelectorAll('.page-link').forEach(link => {
                        link.addEventListener('click', function(e) {
                            e.preventDefault();
                            currentPage = parseInt(this.getAttribute('data-page'));
                            renderTable();
                        });
                    });
                }
            }

            let currentSort = { index: -1, direction: 'asc' };
            const headers = document.querySelectorAll('th.sortable');

            headers.forEach(header => {
                header.addEventListener('click', function() {
                    const index = Array.from(this.parentElement.children).indexOf(this);

                    if (currentSort.index === index) {
                        currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
                    } else {
                        currentSort.index = index;
                        currentSort.direction = 'asc';
                    }

                    headers.forEach(h => {
                        const icon = h.querySelector('.sort-icon');
                        if(icon) icon.textContent = 'unfold_more';
                    });
                    
                    const activeIcon = this.querySelector('.sort-icon');
                    if(activeIcon) activeIcon.textContent = currentSort.direction === 'asc' ? 'expand_less' : 'expand_more';

                    rows.sort((a, b) => {
                        let valA = a.children[index].textContent.trim();
                        let valB = b.children[index].textContent.trim();

                        if (currentSort.direction === 'desc') {
                            return valB.localeCompare(valA);
                        } else {
                            return valA.localeCompare(valB);
                        }
                    });

                    rows.forEach(row => tbody.appendChild(row));
                    currentPage = 1;
                    renderTable();
                });
            });

            renderTable();
        });
    </script>
@endsection