@extends('layouts.app')

@section('title', 'Siklus Pendampingan')

@section('content')
    {{-- ========================================================================= --}}
    {{-- LIBRARY & STYLES: CHOICES.JS UNTUK PENCARIAN DROPDOWN --}}
    {{-- ========================================================================= --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    
    <style>
        /* Pengaturan Dasar Choices.js */
        .choices { font-size: 1rem; margin-bottom: 0; }
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
        .choices[data-type*="select-one"] .choices__input {
            width: 100% !important; max-width: 100% !important; background-color: #f8f9fa !important;
            border: 1px solid #dee2e6 !important; border-radius: 0.375rem !important;
            padding: 10px !important; margin: 5px 0 10px 0 !important; font-size: 1rem !important;
        }
        .choices__list--dropdown {
            border-radius: 0.5rem !important; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important; z-index: 1000 !important;
        }
        .choices__list--dropdown .choices__item {
            padding: 15px 20px !important; font-size: 1rem !important; border-bottom: 1px solid #f1f3f5;
        }
        .choices__list--dropdown .choices__item--selectable.is-highlighted {
            background-color: #0d6efd !important; color: #ffffff !important; font-weight: 600;
        }

        /* Tampilan Khusus Mobile */
        @media (max-width: 768px) {
            .choices__list--dropdown {
                position: fixed !important; top: 20% !important; left: 5% !important; right: 5% !important;
                width: 90% !important; max-height: 60vh !important; border: 1px solid #dee2e6 !important;
            }
            .choices.is-open::after {
                content: ""; position: fixed; top: 0; left: 0; right: 0; bottom: 0;
                background: rgba(0,0,0,0.2); z-index: 999;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const schoolSelect = document.getElementById('schoolSelect');
            if (schoolSelect) {
                new Choices(schoolSelect, {
                    searchEnabled: true, 
                    searchPlaceholderValue: 'Ketik nama sekolah...',
                    itemSelectText: '',
                    noResultsText: 'Sekolah tidak ditemukan',
                    noChoicesText: 'Tidak ada pilihan',
                    shouldSort: false,
                    searchFuzzy: false,
                    searchFields: ['label'],
                    searchResultLimit: 10,
                    placeholder: true,
                    placeholderValue: '-- Ketik atau Pilih Nama Sekolah --'
                });
            }
        });
    </script>

    {{-- ========================================================================= --}}
    {{-- HEADER HALAMAN & FILTER SEKOLAH --}}
    {{-- ========================================================================= --}}
    <div class="mb-4">
        <h2 class="display-6 fw-extrabold font-headline mb-0">Siklus Pendampingan</h2>
        <p class="text-muted small mb-0">Catatan tahapan dan proses pendampingan sekolah binaan.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 small mb-4 shadow-sm" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close pb-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-primary bg-opacity-10 border-start border-4 border-primary">
        <div class="card-body p-4">
            <form action="{{ route('mentoring.index') }}" method="GET" id="formPilihSekolah" >
                <label class="form-label small fw-bold text-primary mb-2">Pilih Sekolah Binaan</label>
                <select name="school_id" id="schoolSelect" class="form-select border-primary shadow-sm" onchange="document.getElementById('formPilihSekolah').submit();" required>
                    <option value="">-- Ketik atau Pilih Nama Sekolah --</option>
                    @foreach($schools as $s)
                        <option value="{{ $s->id }}" {{ request('school_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->name }} - (Skor: {{ $s->skor_performa ?? '0' }}%)
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- TAMPILAN JIKA SEKOLAH SUDAH DIPILIH --}}
    {{-- ========================================================================= --}}
    @if($selectedSchool)
        
        {{-- PANEL REKAPITULASI ANGKA --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-dark h-100 d-flex justify-content-center">
                    <small class="text-cyan-400 fw-bold text-uppercase tracking-widest text-white" style="font-size: 0.65rem;">Total Intervensi</small>
                    <h2 class="display-5 fw-bold mb-0 text-white">{{ $recap['total'] }}</h2>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-bottom border-4 border-info h-100">
                    <small class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem;">1. Perencanaan</small>
                    <h4 class="fw-bold mb-0 text-info mt-1">{{ $recap['perencanaan'] }} <span class="small text-muted fs-6">Aktv</span></h4>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-bottom border-4 border-warning h-100">
                    <small class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem;">2. Pend. Program</small>
                    <h4 class="fw-bold mb-0 text-warning mt-1">{{ $recap['perencanaan_prog'] }} <span class="small text-muted fs-6">Aktv</span></h4>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-bottom border-4 border-primary h-100">
                    <small class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem;">3. Pelaksanaan</small>
                    <h4 class="fw-bold mb-0 text-primary mt-1">{{ $recap['pelaksanaan_prog'] }} <span class="small text-muted fs-6">Aktv</span></h4>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-bottom border-4 border-success h-100">
                    <small class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem;">4. Pelaporan</small>
                    <h4 class="fw-bold mb-0 text-success mt-1">{{ $recap['pelaporan'] }} <span class="small text-muted fs-6">Aktv</span></h4>
                </div>
            </div>
        </div>

        {{-- TABEL DATA SIKLUS PENDAMPINGAN --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            
            {{-- HEADER TABEL: Terbagi 2 Baris agar rapi --}}
            <div class="card-header bg-white border-bottom-0 pt-4 pb-3 px-4">
                
                {{-- Baris 1: Judul Riwayat & Tombol Aksi --}}
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3 mb-3">
                    <div>
                        <h5 class="font-headline fw-bold mb-1">Riwayat: <span class="text-primary">{{ $selectedSchool->name }}</span></h5>
                        @php
                            $skor = $selectedSchool->skor_performa ?? 0;
                            $badgeColor = $skor >= 75 ? 'success' : ($skor >= 40 ? 'warning' : 'danger');
                        @endphp
                        <span class="badge bg-{{ $badgeColor }} bg-opacity-10 text-{{ $badgeColor }} border border-{{ $badgeColor }} rounded-pill px-3">
                            Skor Performa: {{ $skor }}%
                        </span>
                    </div>
                    
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <a href="{{ route('mentoring.export', ['school_id' => $selectedSchool->id]) }}" class="btn btn-success btn-sm fw-bold d-flex align-items-center gap-1 shadow-sm">
                            <span class="material-symbols-outlined fs-6">download</span> Download File Siklus
                        </a>
                        <button type="button" class="btn btn-primary btn-sm fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalSiklus">
                            <span class="d-flex align-items-center gap-1">    
                                <span class="material-symbols-outlined fs-6">add_circle</span> Input Siklus
                            </span>
                        </button>
                    </div>
                </div>

                {{-- Baris 2: Data Entry (Tampil) & Search (Pencarian) --}}
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="small text-muted fw-bold d-none d-sm-inline">Tampilkan</span>
                        <select id="entriesSiklus" class="form-select form-select-sm bg-light border-0 shadow-sm" style="width: auto; cursor: pointer;">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                        <span class="small text-muted fw-bold d-none d-sm-inline">Data</span>
                    </div>

                    <div class="input-group input-group-sm shadow-sm" style="max-width: 250px;">
                        <span class="input-group-text bg-light border-0"><span class="material-symbols-outlined fs-6 text-muted">search</span></span>
                        <input type="text" id="searchSiklus" class="form-control border-0 bg-light ps-0" placeholder="Cari catatan...">
                    </div>
                </div>
            </div>

            {{-- ISI TABEL --}}
            <div class="card-body p-0">
                <div class="p-4 pt-0 table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="bg-light text-muted small">
                            <tr>
                                <th class="ps-2" style="width: 15%">Tanggal</th>
                                <th class="text-center" style="width: 30%">Tahapan Siklus</th>
                                <th class="text-center" style="width: 45%">Keterangan</th>
                                <th class="text-center" style="width: 10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="siklusTableBody">
                            @forelse($cycles as $cycle)
                            <tr class="siklus-row small">
                                <td class="fw-bold ps-2">{{ \Carbon\Carbon::parse($cycle->tanggal)->translatedFormat('d M Y') }}</td>
                                <td class="text-center">
                                    <span class="badge bg-dark bg-opacity-10 text-dark border border-dark rounded-pill px-3">{{ $cycle->siklus }}</span>
                                </td>
                                <td class="text-center" style="white-space: normal;">{{ $cycle->keterangan ?? '-' }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-link text-primary p-0" data-bs-toggle="modal" data-bs-target="#editModal{{ $cycle->id }}"><span class="material-symbols-outlined fs-6">edit</span></button>
                                        <form action="{{ route('mentoring.destroy', $cycle->id) }}" method="POST" onsubmit="return confirm('Hapus data siklus ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger p-0"><span class="material-symbols-outlined fs-6">delete</span></button>
                                        </form>
                                    </div>
                                    
                                    {{-- MODAL EDIT DATA SIKLUS --}}
                                    <div class="modal fade text-start" id="editModal{{ $cycle->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <form action="{{ route('mentoring.update', $cycle->id) }}" method="POST" class="modal-content border-0 shadow">
                                                @csrf @method('PUT')
                                                <div class="modal-header border-bottom-0 pt-4 px-4 pb-0">
                                                    <h5 class="modal-title font-headline fw-bold mb-1">Edit Siklus</h5>
                                                    <button type="button" class="btn-close mt-1" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body px-4">
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-muted mb-1">Tanggal Pendampingan</label>
                                                        <input type="date" name="tanggal" class="form-control bg-light border-secondary-subtle" value="{{ $cycle->tanggal }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-muted mb-1">Tahapan Siklus</label>
                                                        <select name="siklus" class="form-select bg-light border-secondary-subtle" required>
                                                            <option value="Perencanaan Pendampingan" {{ $cycle->siklus == 'Perencanaan Pendampingan' ? 'selected' : '' }}>1. Perencanaan Pendampingan</option>
                                                            <option value="Pendampingan Perencanaan Program" {{ $cycle->siklus == 'Pendampingan Perencanaan Program' ? 'selected' : '' }}>2. Pendampingan Perencanaan Program</option>
                                                            <option value="Pendampingan Pelaksanaan Program" {{ $cycle->siklus == 'Pendampingan Pelaksanaan Program' ? 'selected' : '' }}>3. Pendampingan Pelaksanaan Program</option>
                                                            <option value="Pelaporan Pendampingan" {{ $cycle->siklus == 'Pelaporan Pendampingan' ? 'selected' : '' }}>4. Pelaporan Pendampingan</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-muted mb-1">Keterangan / Catatan</label>
                                                        <textarea name="keterangan" class="form-control bg-light border-secondary-subtle" rows="3" required>{{ $cycle->keterangan }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light border-top-0 px-4 py-3">
                                                    <button type="button" class="btn btn-outline-secondary btn-sm fw-bold" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary btn-sm fw-bold">Update Data</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr id="emptyRow"><td colspan="4" class="text-center small text-muted py-5">Belum ada riwayat pendampingan untuk sekolah ini.</td></tr>
                            @endforelse
                            
                            <tr id="notFoundRow" style="display:none;"><td colspan="4" class="text-center py-4 text-muted small">Data tidak ditemukan.</td></tr>
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION --}}
                <div class="p-3 bg-light bg-opacity-25 border-top d-flex justify-content-between align-items-center rounded-bottom-4">
                    <small class="text-muted fw-semibold" id="siklusPageInfo"></small>
                    <nav id="siklusPagination"></nav>
                </div>
            </div>
        </div>

        {{-- MODAL INPUT DATA SIKLUS BARU --}}
        <div class="modal fade" id="modalSiklus" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form action="{{ route('mentoring.store') }}" method="POST" class="modal-content border-0 shadow">
                    @csrf
                    <input type="hidden" name="school_id" value="{{ $selectedSchool->id }}">
                    <div class="modal-header border-bottom-0 pt-4 px-4 pb-0">
                        <div class="w-100 pe-3">
                            <span class="badge bg-primary bg-opacity-10 text-primary mb-2 px-3 py-2 rounded-pill fw-semibold">Input Baru</span>
                            <h4 class="modal-title font-headline fw-bold mb-1">Siklus Pendampingan</h4>
                            <p class="text-muted small mb-0">
                                {{ $selectedSchool->name }} &bull; <strong class="text-{{ $badgeColor }}">Skor: {{ $selectedSchool->skor_performa ?? 0 }}%</strong>
                            </p>
                        </div>
                        <button type="button" class="btn-close mt-1" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body px-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted mb-1">Tanggal Pelaksanaan</label>
                            <input type="date" name="tanggal" class="form-control bg-light border-secondary-subtle rounded-3 px-3 py-2" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted mb-1">Pilih Tahapan Siklus</label>
                            <select name="siklus" class="form-select bg-light border-secondary-subtle rounded-3 px-3 py-2" required>
                                <option value="">-- Pilih Siklus --</option>
                                <option value="Perencanaan Pendampingan">1. Perencanaan Pendampingan</option>
                                <option value="Pendampingan Perencanaan Program">2. Pendampingan Perencanaan Program</option>
                                <option value="Pendampingan Pelaksanaan Program">3. Pendampingan Pelaksanaan Program</option>
                                <option value="Pelaporan Pendampingan">4. Pelaporan Pendampingan</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted mb-1">Keterangan Aktivitas</label>
                            <textarea name="keterangan" class="form-control bg-light border-secondary-subtle rounded-3 px-3 py-2" rows="4" placeholder="Tulis catatan, temuan, atau progres pendampingan di sini..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top-0 rounded-bottom-4 px-4 py-3">
                        <button type="button" class="btn btn-outline-secondary btn-sm fw-bold px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm fw-bold px-4">Simpan Siklus</button>
                    </div>
                </form>
            </div>
        </div>

    @else
        {{-- TAMPILAN KOSONG JIKA SEKOLAH BELUM DIPILIH --}}
        <div class="text-center py-5">
            <span class="material-symbols-outlined display-1 text-muted opacity-25 mb-3">timeline</span>
            <h5 class="fw-bold text-muted">Belum Ada Sekolah yang Dipilih</h5>
            <p class="small text-muted">Pilih nama sekolah pada kotak pencarian di atas untuk melihat rekapitulasi dan menambah catatan siklus pendampingan.</p>
        </div>
    @endif

    {{-- ========================================================================= --}}
    {{-- SCRIPT: PENCARIAN & PAGINATION TABEL SIKLUS (REAL-TIME) --}}
    {{-- ========================================================================= --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tableBody = document.getElementById('siklusTableBody');
            if(!tableBody) return;

            let allRows = Array.from(document.querySelectorAll('.siklus-row'));
            const searchInput = document.getElementById('searchSiklus');
            const entriesSelect = document.getElementById('entriesSiklus');
            const pageInfo = document.getElementById('siklusPageInfo');
            const paginationNav = document.getElementById('siklusPagination');
            const notFoundRow = document.getElementById('notFoundRow');
            const emptyRow = document.getElementById('emptyRow');

            if(allRows.length === 0) return;

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
                    if(pageInfo) pageInfo.textContent = '';
                    if(paginationNav) paginationNav.innerHTML = '';
                } else {
                    if(notFoundRow) notFoundRow.style.display = 'none';
                    if(emptyRow) emptyRow.style.display = 'none';
                }

                allRows.forEach(row => row.style.display = 'none');

                const start = (currentPage - 1) * rowsPerPage;
                const end = start + rowsPerPage;

                filteredRows.slice(start, end).forEach(row => row.style.display = '');

                if(totalRows > 0 && pageInfo) {
                    pageInfo.textContent = `Menampilkan ${start + 1} - ${Math.min(end, totalRows)} dari total ${totalRows} data`;
                    renderPagination(totalPages);
                }
            }

            function renderPagination(totalPages) {
                if(!paginationNav) return;
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

            if(searchInput) searchInput.addEventListener('keyup', () => { currentPage = 1; updateTable(); });
            if(entriesSelect) entriesSelect.addEventListener('change', (e) => { rowsPerPage = parseInt(e.target.value); currentPage = 1; updateTable(); });

            updateTable();
        });
    </script>
@endsection