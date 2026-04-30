@extends('layouts.app')

@section('title', 'Jurnal Kepala Sekolah')

@push('styles')
    <style>
        .journal-modal .modal-dialog {
            max-width: 760px;
        }

        .journal-modal .modal-content {
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 28px;
            box-shadow: var(--shadow-soft);
            background-color: var(--surface);
        }

        .journal-modal .modal-header {
            padding: 1.5rem 1.5rem 0.75rem;
            background:
                radial-gradient(circle at top right, rgba(244, 162, 97, 0.12), transparent 28%),
                linear-gradient(180deg, var(--surface-soft), var(--surface));
            border-bottom: none;
        }

        .journal-modal .modal-title {
            font-size: 1.55rem;
            line-height: 1.2;
            color: var(--text-main);
        }

        .journal-modal .modal-body {
            padding: 0 1.5rem 1.5rem;
            background-color: var(--surface);
        }

        .journal-modal .modal-intro {
            margin-bottom: 1.25rem;
            padding: 1rem 1.1rem;
            border-radius: 18px;
            background: var(--brand-100);
            color: var(--text-soft);
        }

        .journal-modal .modal-section {
            padding: 1.1rem;
            border: 1px solid var(--line);
            border-radius: 20px;
            background: var(--surface-soft);
        }

        .journal-modal textarea.form-control {
            min-height: 132px;
            resize: vertical;
        }

        .journal-modal .modal-footer {
            padding: 1rem 1.5rem 1.5rem;
            background: var(--surface-muted);
            border-top: none;
        }

        @media (max-width: 575.98px) {
            .journal-modal .modal-dialog {
                margin: 0.5rem;
                max-width: calc(100vw - 1rem);
                min-height: calc(100vh - 1rem);
            }

            .journal-modal .modal-content {
                max-height: calc(100vh - 1rem);
            }

            .journal-modal .modal-header,
            .journal-modal .modal-body,
            .journal-modal .modal-footer {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .journal-modal .modal-title {
                font-size: 1.3rem;
            }

            .journal-modal .modal-body {
                padding-bottom: 5.5rem;
            }

            .journal-modal .modal-footer {
                position: sticky;
                bottom: 0;
                z-index: 2;
                display: flex;
                justify-content: space-between;
                gap: 0.75rem;
                padding-top: 0.85rem;
                padding-bottom: calc(0.85rem + env(safe-area-inset-bottom, 0px));
                background: var(--surface-muted);
                box-shadow: 0 -10px 24px rgba(0, 0, 0, 0.08);
            }

            .journal-modal .modal-footer .btn {
                flex: 1 1 0;
                min-height: 44px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="mb-4">
        <h2 class="display-6 fw-extrabold font-headline mb-0">Jurnal Kepala Sekolah</h2>
        <p class="text-muted small mb-0">Pantau kehadiran dan aktivitas harian Kepala Sekolah.</p>
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
                <form action="{{ route('jurnal.index') }}" method="GET" class="d-flex flex-column flex-md-row align-items-md-end gap-3">
                    <div class="flex-grow-1">
                        <label class="form-label small fw-bold text-primary">Pilih Sekolah Binaan</label>
                        {{-- SAYA MENGUBAH BARIS INI: Menambahkan id="schoolSelect" --}}
                        <select name="school_id" id="schoolSelect" class="form-select border-primary shadow-sm" required>
                            <option value="">-- Silakan Pilih Sekolah Terlebih Dahulu --</option>
                            @foreach($schools as $s)
                                <option value="{{ $s->id }}" {{ request('school_id') == $s->id ? 'selected' : '' }}>
                                    {{ $s->name }} ({{ $s->level }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm" style="min-height: 50px;">
                        <span class="d-flex align-items-center gap-1">
                            <span class="material-symbols-outlined fs-6">search</span> Tampilkan Data
                        </span>
                    </button>
                </form>
            </div>
        </div>
    @endif

    {{-- TAMPILAN DATA JURNAL (Jika Sekolah Sudah Dipilih / Login sbg Admin) --}}
    @if($selectedSchool)
        
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <h5 class="font-headline fw-bold mb-0">
                        @if(auth()->user()->role === 'pengawas')
                            Data Jurnal: <span class="text-primary">{{ $selectedSchool->name }}</span>
                        @else
                            Rekap Jurnal Harian
                        @endif
                    </h5>
                    
                    @if(auth()->user()->role === 'admin_sekolah' && auth()->user()->school_id == $selectedSchool->id)
                        <button type="button" class="btn btn-sm btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#modalAbsensi">
                            + Isi Jurnal
                        </button>
                    @endif
                </div>

                <div class="d-flex flex-column flex-sm-row gap-2 align-items-sm-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="small text-muted fw-bold d-none d-md-inline">Tampilkan</span>
                        <select id="entriesAbsensi" class="form-select form-select-sm bg-light border-0 shadow-sm" style="width: auto; cursor: pointer;">
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
                        <input type="text" id="searchAbsensi" class="form-control border-start-0 ps-0" placeholder="Cari data...">
                    </div>
                    <a href="{{ route('school.export_attendance', $selectedSchool->id) }}" class="btn btn-success btn-sm fw-bold d-flex align-items-center justify-content-center gap-1 shadow-sm" title="Download Excel">
                        <span class="material-symbols-outlined fs-6">download</span> Download Excel
                    </a>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="p-4 pt-3">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0" id="journalTable">
                            <thead class="bg-light text-muted small">
                                <tr>
                                    <th class="ps-2 cursor-pointer sortable user-select-none hover-bg-light" title="Klik untuk mengurutkan Tanggal">
                                        <div class="d-flex align-items-center gap-1">
                                            Tanggal <span class="material-symbols-outlined fs-6 sort-icon text-primary">unfold_more</span>
                                        </div>
                                    </th>
                                    <th class="text-center">Siswa Hadir</th>
                                    <th class="text-center">Guru Hadir</th>
                                    <th class="text-center">Kepsek Hadir</th>
                                    <th>Tupoksi</th>
                                    <th>Keterangan</th>
                                    
                                    {{-- KEPALA KOLOM AKSI (Hanya muncul untuk pengawas) --}}
                                    @if(auth()->user()->role === 'pengawas')
                                        <th class="text-center">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody id="journalTableBody">
                                @forelse($selectedSchool->attendances as $absen)
                                <tr class="absensi-row">
                                    <td class="small text-nowrap fw-bold ps-2" data-sort="{{ $absen->tanggal }}">{{ \Carbon\Carbon::parse($absen->tanggal)->format('d / m / Y') }}</td>
                                    <td class="text-center small">{{ $absen->siswa_hadir }}</td>
                                    <td class="text-center small">{{ $absen->guru_hadir }}</td>
                                    <td class="text-center">
                                        <span class="material-symbols-outlined fs-6 {{ $absen->kepsek_hadir ? 'text-success' : 'text-danger' }} d-block mb-1">
                                            {{ $absen->kepsek_hadir ? 'check_circle' : 'cancel' }}
                                        </span>
                                        <div class="text-muted" style="font-size: 0.65rem; font-family: monospace;">
                                            {{ \Carbon\Carbon::parse($absen->created_at)->format('H:i:s') }}
                                        </div>
                                    </td>
                                    <td class="small fw-medium">{{ $absen->tupoksi ?? '-' }}</td>
                                    <td class="small text-muted" style="max-width: 200px; white-space: normal;">{{ $absen->keterangan ?? '-' }}</td>
                                    
                                    {{-- TOMBOL HAPUS (Hanya muncul untuk pengawas) --}}
                                    @if(auth()->user()->role === 'pengawas')
                                        <td class="text-center">
                                            <form action="{{ route('attendance.destroy', $absen->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data jurnal tanggal {{ \Carbon\Carbon::parse($absen->tanggal)->format('d/m/Y') }} ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link text-danger p-0" title="Hapus Data">
                                                    <span class="material-symbols-outlined fs-6">delete</span>
                                                </button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                                @empty
                                <tr id="emptyAbsensiRow"><td colspan="8" class="text-center small text-muted py-4">Belum ada data jurnal harian untuk sekolah ini.</td></tr>
                                @endforelse

                                <tr id="notFoundAbsensi" style="display: none;">
                                    <td colspan="8" class="text-center small text-muted py-3">Data tidak ditemukan.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="p-3 bg-light bg-opacity-25 border-top d-flex justify-content-between align-items-center rounded-bottom-4">
                    <small class="text-muted fw-semibold" id="absensiPageInfo">Menampilkan data...</small>
                    <nav id="absensiPagination"></nav>
                </div>
            </div>
        </div>
        
        {{-- MODAL ISI JURNAL --}}
        @if(auth()->user()->role === 'admin_sekolah' && auth()->user()->school_id == $selectedSchool->id)
            <div class="modal fade journal-modal" id="modalAbsensi" tabindex="-1" aria-labelledby="modalAbsensiLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                    
                    {{-- PERBAIKAN FINAL: Tag <form> LANGSUNG menjadi modal-content --}}
                    <form action="{{ route('school.store_attendance', $selectedSchool->id) }}" method="POST" class="modal-content border-0 shadow">
                        @csrf
                        
                        {{-- HEADER MODAL --}}
                        <div class="modal-header border-bottom-0 align-items-start pt-4 px-4 pb-0">
                            <div class="w-100 pe-3">
                                <span class="badge bg-primary bg-opacity-10 text-primary mb-2 px-3 py-2 rounded-pill fw-semibold">Input Harian</span>
                                <h4 class="modal-title font-headline fw-bold mb-1" id="modalAbsensiLabel">Input Jurnal Kepsek</h4>
                                <p class="text-muted small mb-0" style="line-height: 1.5;">Masukkan data kehadiran dan aktivitas kepala sekolah untuk hari ini.</p>
                            </div>
                            <button type="button" class="btn-close mt-1" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        {{-- BODY MODAL --}}
                        <div class="modal-body px-4">
                            <div class="alert alert-info bg-info bg-opacity-10 border-0 small mb-4 py-2 px-3 rounded-3">
                                <span class="fw-bold text-info">Tanggal Jurnal:</span> <span class="text-info">{{ now()->translatedFormat('d F Y') }}</span>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-bold text-muted mb-1">Siswa Hadir</label>
                                    <input type="number" name="siswa_hadir" class="form-control rounded-3 px-3 py-2" style="font-size: 0.9rem;" placeholder="Contoh: 360" required min="0">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-bold text-muted mb-1">Guru Hadir</label>
                                    <input type="number" name="guru_hadir" class="form-control rounded-3 px-3 py-2" style="font-size: 0.9rem;" placeholder="Contoh: 50" required min="0">
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-bold text-muted mb-1">Kehadiran Kepsek</label>
                                    <select name="kepsek_hadir" class="form-select rounded-3 px-3 py-2" style="font-size: 0.9rem;" required>
                                        <option value="">-- Silakan Pilih --</option>
                                        <option value="1">Hadir (Ada di tempat)</option>
                                        <option value="0">Tidak Hadir (Dinas Luar / Izin)</option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-bold text-muted mb-1">Tupoksi Kepsek</label>
                                    <select name="tupoksi" class="form-select rounded-3 px-3 py-2" style="font-size: 0.9rem;" required>
                                        <option value="">-- Pilih Tupoksi --</option>
                                        <option value="Manajerial">1. Manajerial</option>
                                        <option value="Educator">2. Educator</option>
                                        <option value="Supervisor">3. Supervisor</option>
                                        <option value="Leader">4. Leader</option>
                                        <option value="Entrepreneur">5. Entrepreneur</option>
                                        <option value="Pengelola Sistem Informasi">6. Pengelola Sistem Informasi</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted mb-1">Keterangan Aktivitas</label>
                                    <textarea name="keterangan" class="form-control rounded-3 px-3 py-2" style="font-size: 0.9rem;" rows="3" placeholder="Tulis ringkasan kegiatan, agenda, atau catatan penting hari ini..." required></textarea>
                                </div>
                            </div>
                        </div>

                        {{-- FOOTER MODAL --}}
                        <div class="modal-footer bg-light border-top-0 rounded-bottom-4 px-4 py-3 mt-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm fw-bold px-3" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary btn-sm fw-bold px-4 shadow-sm">Simpan Jurnal</button>
                        </div>
                        
                    </form>
                </div>
            </div>
        @endif

    @elseif(auth()->user()->role === 'pengawas')
        <div class="text-center py-5">
            <span class="material-symbols-outlined display-1 text-muted opacity-25 mb-3">folder_open</span>
            <h5 class="fw-bold text-muted">Belum Ada Sekolah yang Dipilih</h5>
            <p class="small text-muted">Silakan gunakan menu dropdown di atas untuk melihat rekap dan jurnal harian sekolah binaan Anda.</p>
        </div>
    @endif

    {{-- SCRIPT: SEARCH, PAGINATION & SORTING --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tbody = document.getElementById('journalTableBody');
            let rows = Array.from(document.querySelectorAll('.absensi-row'));
            const paginationControls = document.getElementById('absensiPagination');
            const pageInfo = document.getElementById('absensiPageInfo');
            const searchInput = document.getElementById('searchAbsensi');
            const notFoundRow = document.getElementById('notFoundAbsensi');
            const entriesSelect = document.getElementById('entriesAbsensi'); 
            
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
                            const page = parseInt(this.getAttribute('data-page'));
                            if(!isNaN(page) && page >= 1 && page <= totalPages) {
                                currentPage = page;
                                renderTable();
                            }
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
                        let valA = a.children[index].getAttribute('data-sort') || a.children[index].textContent.trim();
                        let valB = b.children[index].getAttribute('data-sort') || b.children[index].textContent.trim();

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
                    searchResultLimit: 15,
                    placeholder: true,
                    placeholderValue: '-- Silakan Pilih Sekolah Terlebih Dahulu --'
                });
            }
        });
    </script>
@endsection