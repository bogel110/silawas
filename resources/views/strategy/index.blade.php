@extends('layouts.app')

@section('title', 'Siklus & Strategi Pengawasan')

@section('content')
    <div class="mb-4">
        <h2 class="display-6 fw-extrabold font-headline mb-0">Strategi Pendampingan</h2>
        <p class="text-muted small mb-0">Manajemen pendekatan pengawasan berdasarkan skor performa sekolah.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 px-3 small mb-4 shadow-sm border-0" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close pb-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- PANEL REKAPITULASI KESELURUHAN (SELURUH SEKOLAH) --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-3 gap-2">
        <h5 class="fw-bold mb-0 text-secondary" style="font-size: 1rem;">Rekapitulasi Seluruh Sekolah Binaan</h5>
        <a href="{{ route('strategy.export') }}" class="btn btn-success btn-sm fw-bold d-flex align-items-center gap-1 shadow-sm">
            <span class="material-symbols-outlined fs-6">download</span> Download File Rekap
        </a>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-dark h-100 d-flex justify-content-center">
                <small class="text-cyan-400 fw-bold text-uppercase tracking-widest text-white" style="font-size: 0.65rem;">Total Intervensi</small>
                <h2 class="display-5 fw-bold mb-0 text-white">{{ $recapAll['total'] }}</h2>
            </div>
        </div>
        <div class="col-12 col-md-9">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="row g-2 text-center align-items-center h-100">
                    <div class="col-4 col-md-2 border-end">
                        <h4 class="fw-bold text-primary mb-0">{{ $recapAll['seeding'] }}</h4>
                        <small class="text-muted d-block" style="font-size: 0.65rem;">Penyemaian</small>
                    </div>
                    <div class="col-4 col-md-2 border-end">
                        <h4 class="fw-bold text-danger mb-0">{{ $recapAll['rapid'] }}</h4>
                        <small class="text-muted d-block" style="font-size: 0.65rem;">Segera</small>
                    </div>
                    <div class="col-4 col-md-2 border-end">
                        <h4 class="fw-bold text-success mb-0">{{ $recapAll['reinforcing'] }}</h4>
                        <small class="text-muted d-block" style="font-size: 0.65rem;">Penguatan</small>
                    </div>
                    <div class="col-4 col-md-2 border-end">
                        <h4 class="fw-bold text-warning mb-0">{{ $recapAll['gradual'] }}</h4>
                        <small class="text-muted d-block" style="font-size: 0.65rem;">Berangsur</small>
                    </div>
                    <div class="col-4 col-md-2 border-end">
                        <h4 class="fw-bold text-info mb-0">{{ $recapAll['triggering'] }}</h4>
                        <small class="text-muted d-block" style="font-size: 0.65rem;">Pemicu</small>
                    </div>
                    <div class="col-4 col-md-2">
                        <h4 class="fw-bold text-secondary mb-0">{{ $recapAll['sustainable'] }}</h4>
                        <small class="text-muted d-block" style="font-size: 0.65rem;">Berkelanjutan</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- KOTAK FILTER: PILIH SEKOLAH TERLEBIH DAHULU --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-primary bg-opacity-10 border-start border-4 border-primary">
        <div class="card-body p-4">
            <form action="{{ request()->url() }}" method="GET" id="formPilihSekolah">
                <label class="form-label small fw-bold text-primary mb-2">Pilih Sekolah Binaan</label>
                <select name="school_id" id="schoolSelect" class="form-select border-primary" onchange="document.getElementById('formPilihSekolah').submit();">
                    <option value="">-- Ketik atau Pilih Nama Sekolah --</option>
                    @foreach($schools as $s)
                        <option value="{{ $s->id }}" {{ request('school_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->name }} - (Skor Performa: {{ $s->skor_performa ?? '0' }}%)
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    {{-- KONTEN UTAMA MUNCUL JIKA SEKOLAH SUDAH DIPILIH --}}
        {{-- BANNER PROFIL SEKOLAH & TOMBOL TAMBAH STRATEGI --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 p-4 bg-white rounded-4 shadow-sm border border-light">
            <div class="d-flex align-items-center gap-3">
                <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                    <span class="material-symbols-outlined fs-2">{{ $selectedSchool ? 'school' : 'domain' }}</span>
                </div>
                <div>
                    <h4 class="font-headline fw-bold mb-1">{{ $selectedSchool ? $selectedSchool->name : 'Rekapitulasi Seluruh Sekolah Binaan' }}</h4>
                    @if($selectedSchool)
                        @php
                            $skor = $selectedSchool->skor_performa ?? 0;
                            $badgeColor = $skor >= 75 ? 'success' : ($skor >= 50 ? 'warning' : 'danger');
                        @endphp
                        <span class="badge bg-{{ $badgeColor }} bg-opacity-10 text-{{ $badgeColor }} border border-{{ $badgeColor }} rounded-pill px-3">
                            Skor Performa: {{ $skor }}%
                        </span>
                    @else
                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary rounded-pill px-3">
                            Menampilkan data dari {{ $schools->count() }} sekolah
                        </span>
                    @endif
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2 align-items-center">

            @if($selectedSchool)
            <button type="button" class="btn btn-primary fw-bold shadow-sm px-4 py-2" data-bs-toggle="modal" data-bs-target="#modalStrategi">
                <span class="d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined fs-5">add_circle</span> Input Strategi
                </span>
            </button>
            @endif
            </div>
        </div>

        {{-- PANEL REKAPITULASI KHUSUS SEKOLAH INI --}}
        @if($selectedSchool && $recapSchool)
        <h5 class="fw-bold mb-3 text-secondary" style="font-size: 1rem;">Rekapitulasi Khusus {{ $selectedSchool->name }}</h5>
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-primary bg-opacity-10 h-100 d-flex justify-content-center border border-primary border-opacity-25">
                    <small class="text-primary fw-bold text-uppercase tracking-widest" style="font-size: 0.65rem;">Intervensi Sekolah Ini</small>
                    <h2 class="display-5 fw-bold mb-0 text-primary">{{ $recapSchool['total'] }}</h2>
                </div>
            </div>
            <div class="col-12 col-md-9">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border border-light">
                    <div class="row g-2 text-center align-items-center h-100">
                        <div class="col-4 col-md-2 border-end">
                            <h4 class="fw-bold text-primary mb-0">{{ $recapSchool['seeding'] }}</h4>
                            <small class="text-muted d-block" style="font-size: 0.65rem;">Penyemaian</small>
                        </div>
                        <div class="col-4 col-md-2 border-end">
                            <h4 class="fw-bold text-danger mb-0">{{ $recapSchool['rapid'] }}</h4>
                            <small class="text-muted d-block" style="font-size: 0.65rem;">Segera</small>
                        </div>
                        <div class="col-4 col-md-2 border-end">
                            <h4 class="fw-bold text-success mb-0">{{ $recapSchool['reinforcing'] }}</h4>
                            <small class="text-muted d-block" style="font-size: 0.65rem;">Penguatan</small>
                        </div>
                        <div class="col-4 col-md-2 border-end">
                            <h4 class="fw-bold text-warning mb-0">{{ $recapSchool['gradual'] }}</h4>
                            <small class="text-muted d-block" style="font-size: 0.65rem;">Berangsur</small>
                        </div>
                        <div class="col-4 col-md-2 border-end">
                            <h4 class="fw-bold text-info mb-0">{{ $recapSchool['triggering'] }}</h4>
                            <small class="text-muted d-block" style="font-size: 0.65rem;">Pemicu</small>
                        </div>
                        <div class="col-4 col-md-2">
                            <h4 class="fw-bold text-secondary mb-0">{{ $recapSchool['sustainable'] }}</h4>
                            <small class="text-muted d-block" style="font-size: 0.65rem;">Berkelanjutan</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif


        {{-- TABEL DATA STRATEGI DENGAN PAGINATION & SEARCH --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-3 px-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <h5 class="font-headline fw-bold mb-0">Riwayat Strategi</h5>
                
                <div class="d-flex flex-column flex-sm-row gap-2 align-items-sm-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="small text-muted fw-bold d-none d-sm-inline">Tampilkan</span>
                        <select id="entriesStrategy" class="form-select form-select-sm bg-light border-0 shadow-sm" style="width: auto; cursor: pointer;">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                        </select>
                    </div>
                    <div class="input-group input-group-sm shadow-sm" style="max-width: 200px;">
                        <span class="input-group-text bg-white border-end-0"><span class="material-symbols-outlined fs-6 text-muted">search</span></span>
                        <input type="text" id="searchStrategy" class="form-control border-start-0 ps-0" placeholder="Cari strategi...">
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="p-4 pt-3 table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="bg-light text-muted small">
                            <tr>
                                <th class="ps-2">Tanggal</th>
                                <th>Nama Sekolah</th>
                                <th>Strategi Pendampingan</th>
                                <th>Keterangan Tambahan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="strategyTableBody">
                            @forelse($strategies as $str)
                            <tr class="strategy-row small">
                                <td class="fw-bold text-nowrap ps-2">{{ $str->created_at->format('d M Y') }}</td>
                                <td class="fw-bold text-primary">{{ $str->school->name ?? '-' }}</td>
                                <td><span class="badge bg-dark bg-opacity-10 text-dark border border-dark">{{ $str->strategy }}</span></td>
                                <td class="text-muted" style="max-width: 300px; white-space: normal;">{{ $str->keterangan ?? '-' }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-link text-primary p-0" data-bs-toggle="modal" data-bs-target="#editModal{{ $str->id }}"><span class="material-symbols-outlined fs-6">edit</span></button>
                                        <form action="{{ route('strategy.destroy', $str->id) }}" method="POST" onsubmit="return confirm('Hapus riwayat strategi ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger p-0"><span class="material-symbols-outlined fs-6">delete</span></button>
                                        </form>
                                    </div>

                                    {{-- MODAL EDIT (Disembunyikan di dalam loop) --}}
                                    <div class="modal fade" id="editModal{{ $str->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered text-start">
                                            <div class="modal-content border-0 shadow rounded-4">
                                                <form action="{{ route('strategy.update', $str->id) }}" method="POST">
                                                    @csrf @method('PUT')
                                                    <div class="modal-header border-bottom-0">
                                                        <h1 class="modal-title fs-5 font-headline fw-bold">Edit Strategi</h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body p-4 pt-0">
                                                        <div class="mb-3">
                                                            <label class="small fw-bold">Strategi Pendampingan</label>
                                                            <select name="strategy" class="form-select bg-light" required>
                                                                <option value="Penyemaian Perubahan (Seeding Change)" {{ $str->strategy == 'Penyemaian Perubahan (Seeding Change)' ? 'selected' : '' }}>Penyemaian Perubahan (Seeding Change)</option>
                                                                <option value="Perubahan Segera (Rapid Change)" {{ $str->strategy == 'Perubahan Segera (Rapid Change)' ? 'selected' : '' }}>Perubahan Segera (Rapid Change)</option>
                                                                <option value="Penguatan Perubahan (Reinforcing Change)" {{ $str->strategy == 'Penguatan Perubahan (Reinforcing Change)' ? 'selected' : '' }}>Penguatan Perubahan (Reinforcing Change)</option>
                                                                <option value="Perubahan Berangsur (Gradual Change)" {{ $str->strategy == 'Perubahan Berangsur (Gradual Change)' ? 'selected' : '' }}>Perubahan Berangsur (Gradual Change)</option>
                                                                <option value="Pemicu Perubahan (Triggering Change)" {{ $str->strategy == 'Pemicu Perubahan (Triggering Change)' ? 'selected' : '' }}>Pemicu Perubahan (Triggering Change)</option>
                                                                <option value="Perubahan Berkelanjutan (Sustainable Change)" {{ $str->strategy == 'Perubahan Berkelanjutan (Sustainable Change)' ? 'selected' : '' }}>Perubahan Berkelanjutan (Sustainable Change)</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="small fw-bold">Keterangan / Alasan</label>
                                                            <textarea name="keterangan" class="form-control bg-light" rows="3">{{ $str->keterangan }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer bg-light border-top-0 rounded-bottom-4">
                                                        <button type="button" class="btn btn-outline-secondary btn-sm fw-bold" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary btn-sm fw-bold">Update Data</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr id="emptyRow"><td colspan="5" class="text-center small text-muted py-5">Belum ada strategi yang diinputkan.</td></tr>
                            @endforelse
                            <tr id="notFoundRow" style="display: none;"><td colspan="5" class="text-center small text-muted py-4">Data tidak ditemukan.</td></tr>
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION FOOTER --}}
                <div class="p-3 bg-light bg-opacity-25 border-top d-flex justify-content-between align-items-center rounded-bottom-4">
                    <small class="text-muted fw-semibold" id="strategyPageInfo"></small>
                    <nav id="strategyPagination"></nav>
                </div>
            </div>
        </div>

        @if($selectedSchool)
            {{-- MODAL TAMBAH DATA BARU (SUDAH OTOMATIS TERKUNCI KE SEKOLAH YANG DIPILIH) --}}
            <div class="modal fade" id="modalStrategi" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow rounded-4 text-start">
                        <form action="{{ route('strategy.store') }}" method="POST">
                            @csrf
                            {{-- KUNCI INPUTAN KE SEKOLAH YANG SEDANG DIPILIH --}}
                            <input type="hidden" name="school_id" value="{{ $selectedSchool->id }}">
                            
                            <div class="modal-header border-bottom-0">
                                <h1 class="modal-title fs-5 font-headline fw-bold">Input Siklus Strategi</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-4 pt-0">
                                <div class="mb-3">
                                    <label class="small fw-bold">Sekolah Binaan</label>
                                    <input type="text" class="form-control bg-light text-muted fw-bold" value="{{ $selectedSchool->name }} (Skor: {{ $skor }}%)" disabled>
                                </div>
                                <div class="mb-3">
                                    <label class="small fw-bold">Rekomendasi Strategi</label>
                                    <select name="strategy" class="form-select" required>
                                        <option value="">-- Pilih Strategi --</option>
                                        <option value="Penyemaian Perubahan (Seeding Change)">1. Penyemaian Perubahan (Seeding Change)</option>
                                        <option value="Perubahan Segera (Rapid Change)">2. Perubahan Segera (Rapid Change)</option>
                                        <option value="Penguatan Perubahan (Reinforcing Change)">3. Penguatan Perubahan (Reinforcing Change)</option>
                                        <option value="Perubahan Berangsur (Gradual Change)">4. Perubahan Berangsur (Gradual Change)</option>
                                        <option value="Pemicu Perubahan (Triggering Change)">5. Pemicu Perubahan (Triggering Change)</option>
                                        <option value="Perubahan Berkelanjutan (Sustainable Change)">6. Perubahan Berkelanjutan (Sustainable Change)</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="small fw-bold">Keterangan / Alasan</label>
                                    <textarea name="keterangan" class="form-control" rows="3" placeholder="Tuliskan alasan mengapa strategi ini dipilih..."></textarea>
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

    {{-- ========================================================================= --}}
    {{-- SCRIPT: CHOICES.JS (Mobile-First, Pencarian Akurat & Blok Biru) --}}
    {{-- ========================================================================= --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    
    <style>
        /* 1. Pengaturan Dasar */
        .choices { 
            font-size: 1rem; 
            margin-bottom: 0;
        }

        .choices__inner {
            background-color: #fff !important;
            border: 1px solid #0d6efd !important;
            border-radius: 0.5rem !important;
            padding: 0.5rem 1rem !important;
            min-height: 50px !important;
            display: flex;
            align-items: center;
            box-shadow: 0 .125rem .25rem rgba(0,0,0,.075);
        }

        /* 2. Kotak Pencarian agar Panjang & Jelas */
        .choices[data-type*="select-one"] .choices__input {
            width: 100% !important;
            max-width: 100% !important;
            background-color: #f8f9fa !important;
            border: 1px solid #dee2e6 !important;
            border-radius: 0.375rem !important;
            padding: 10px !important;
            margin: 5px 0 10px 0 !important;
            font-size: 1rem !important;
        }

        /* 3. Daftar Dropdown (Sangat penting untuk HP) */
        .choices__list--dropdown {
            border-radius: 0.5rem !important;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
            z-index: 1000 !important;
        }

        .choices__list--dropdown .choices__item {
            padding: 15px 20px !important;
            font-size: 1rem !important;
            border-bottom: 1px solid #f1f3f5;
        }

        /* 4. Efek Blok Biru Solid saat dipilih */
        .choices__list--dropdown .choices__item--selectable.is-highlighted {
            background-color: #0d6efd !important;
            color: #ffffff !important;
            font-weight: 600;
        }

        /* 5. KHUSUS TAMPILAN HP (Layar di bawah 768px) */
        @media (max-width: 768px) {
            .choices__list--dropdown {
                position: fixed !important;
                top: 20% !important;
                left: 5% !important;
                right: 5% !important;
                width: 90% !important;
                max-height: 60vh !important;
                border: 1px solid #dee2e6 !important;
            }
            
            /* Menghitamkan latar belakang saat dropdown terbuka agar lebih fokus */
            .choices.is-open::after {
                content: "";
                position: fixed;
                top: 0; left: 0; right: 0; bottom: 0;
                background: rgba(0,0,0,0.2);
                z-index: 999;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inisiasi Dropdown Pencarian Akurat & Besar
            const schoolSelect = document.getElementById('schoolSelect');
            if (schoolSelect) {
                new Choices(schoolSelect, { 
                    searchEnabled: true, // FITUR CARI TETAP AKTIF DI HP
                    searchPlaceholderValue: 'Ketik nama sekolah yang dicari...',
                    itemSelectText: '',
                    noResultsText: 'Sekolah tidak ditemukan',
                    noChoicesText: 'Tidak ada pilihan tersisa',
                    shouldSort: false,
                    searchFuzzy: false,       // Mematikan pencarian samar agar akurat
                    searchFields: ['label'],  // Hanya cari berdasarkan nama sekolah
                    searchResultLimit: 15,
                    placeholder: true,
                    placeholderValue: '-- Ketik atau Pilih Nama Sekolah --'
                });
            }

            // Inisiasi Paginasi & Pencarian Tabel
            const tableBody = document.getElementById('strategyTableBody');
            if(!tableBody) return;

            let allRows = Array.from(document.querySelectorAll('.strategy-row'));
            const searchInput = document.getElementById('searchStrategy');
            const entriesSelect = document.getElementById('entriesStrategy');
            const pageInfo = document.getElementById('strategyPageInfo');
            const paginationNav = document.getElementById('strategyPagination');
            const notFoundRow = document.getElementById('notFoundRow');
            const emptyRow = document.getElementById('emptyRow');

            let currentPage = 1;
            let rowsPerPage = parseInt(entriesSelect.value);

            function updateTable() {
                const searchTerm = searchInput.value.toLowerCase();
                const filteredRows = allRows.filter(row => row.textContent.toLowerCase().includes(searchTerm));

                const totalRows = filteredRows.length;
                const totalPages = Math.ceil(totalRows / rowsPerPage);

                if(totalRows === 0) {
                    if(notFoundRow) notFoundRow.style.display = searchTerm ? '' : 'none';
                    if(emptyRow && !searchTerm) emptyRow.style.display = '';
                    pageInfo.textContent = '';
                    paginationNav.innerHTML = '';
                } else {
                    if(notFoundRow) notFoundRow.style.display = 'none';
                    if(emptyRow) emptyRow.style.display = 'none';
                }

                allRows.forEach(row => row.style.display = 'none');

                const start = (currentPage - 1) * rowsPerPage;
                const end = start + rowsPerPage;

                filteredRows.slice(start, end).forEach(row => row.style.display = '');

                if(totalRows > 0) {
                    pageInfo.textContent = `Menampilkan ${start + 1} - ${Math.min(end, totalRows)} dari ${totalRows} data`;
                    renderPagination(totalPages);
                }
            }

            function renderPagination(totalPages) {
                if(totalPages <= 1) { paginationNav.innerHTML = ''; return; }
                let html = '<ul class="pagination pagination-sm mb-0 shadow-sm">';
                html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><a class="page-link" href="#" onclick="changePage(${currentPage - 1}, event)">&laquo;</a></li>`;
                for(let i=1; i<=totalPages; i++) {
                    html += `<li class="page-item ${i === currentPage ? 'active' : ''}"><a class="page-link" href="#" onclick="changePage(${i}, event)">${i}</a></li>`;
                }
                html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><a class="page-link" href="#" onclick="changePage(${currentPage + 1}, event)">&raquo;</a></li>`;
                html += '</ul>';
                paginationNav.innerHTML = html;
            }

            window.changePage = function(page, event) {
                event.preventDefault();
                currentPage = page;
                updateTable();
            };

            searchInput.addEventListener('keyup', () => { currentPage = 1; updateTable(); });
            entriesSelect.addEventListener('change', (e) => { rowsPerPage = parseInt(e.target.value); currentPage = 1; updateTable(); });

            updateTable();
        });
    </script>
@endsection