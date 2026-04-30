@extends('layouts.app')

@section('title', 'Dashboard Utama')

@section('content')
    <div class="hero-panel mb-4 mb-lg-5">
        <div class="row g-4 align-items-center">
            <div class="col-lg-8">
                <span class="section-kicker mb-3">
                    <span class="material-symbols-outlined" style="font-size: 1rem;">analytics</span>
                    Ringkasan Supervisi
                </span>
                <h2 class="display-6 font-headline fw-bold mb-2">Halo, {{ auth()->user()->name }}</h2>
                <h4 class="isplay-6 font-headline fw-bold mb-2">
                    Dashboard Pemantauan Sekolah Binaan
                </h4>
                <p class="text-soft small mb-0" style="text-align: justify;">
                    Selamat Datang , pada sistem layanan administrasi komprehensif yang memberikan rekomendasi pengawasan dan pendampingan satuan pendidikan.
                    Akses dashboard, jurnal kepsek, KBM, dan administrasi sekolah melalui satu pintu yang holistik, komprehensif dan profesional.
                </p>
            </div>
            <div class="col-lg-4">
                <div class="content-panel p-4 h-100">
                    <div class="eyebrow-muted mb-2">Rata-rata Capaian</div>
                    <div class="d-flex align-items-end justify-content-between gap-3">
                        <div>
                            <h3 class="font-headline fw-bold mb-1">{{ number_format($avgCompletion, 1) }}%</h3>
                            <p class="text-soft small mb-0">Akumulasi progres seluruh sekolah.</p>
                        </div>
                        <div class="icon-box">
                            <span class="material-symbols-outlined">monitoring</span>
                        </div>
                    </div>
                    <div class="progress mt-4" style="height: 10px;">
                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ $avgCompletion }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4 mb-lg-5">
        <div class="col-md-6 col-xl-4">
            <div class="metric-card">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <div class="eyebrow-muted mb-2">Total Sekolah</div>
                        <h3 class="font-headline fw-bold mb-0">{{ $totalSchools }}</h3>
                    </div>
                    <div class="icon-box">
                        <span class="material-symbols-outlined">school</span>
                    </div>
                </div>
                <p class="metric-meta text-soft small mb-0">Jumlah sekolah binaan yang terdaftar di sistem supervisi.</p>
            </div>
        </div>

        <div class="col-md-6 col-xl-4">
            <div class="metric-card">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <div class="eyebrow-muted mb-2">Rata-rata Progres</div>
                        <h3 class="font-headline fw-bold mb-0">{{ number_format($avgCompletion, 1) }}%</h3>
                    </div>
                    <div class="icon-box" style="background: linear-gradient(135deg, rgba(34, 197, 94, 0.14), rgba(34, 197, 94, 0.05)); color: #16a34a;">
                        <span class="material-symbols-outlined">verified</span>
                    </div>
                </div>
                <p class="metric-meta text-soft small mb-0">Gambaran umum tingkat kelengkapan dokumen sekolah.</p>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="metric-card highlight-card">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <div class="eyebrow-muted mb-2 text-white-50">Indeks Supervisi</div>
                        <h3 class="font-headline fw-bold mb-0">{{ $avgCompletion >= 85 ? 'Sangat Baik' : ($avgCompletion >= 70 ? 'Baik' : ($avgCompletion >= 50 ? 'Cukup' : 'Perlu Tindak Lanjut')) }}</h3>
                    </div>
                    <div class="icon-box" style="background: rgba(255, 255, 255, 0.14); color: #fff;">
                        <span class="material-symbols-outlined">workspace_premium</span>
                    </div>
                </div>
                <p class="metric-meta small mb-3">Label ringkas berdasarkan capaian rata-rata keseluruhan sekolah.</p>
                <div class="progress bg-white bg-opacity-25" style="height: 10px;">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $avgCompletion }}%"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODUL 2: PROGRES LAPORAN BULANAN --}}
    <div class="card border-0 shadow-sm rounded-4 mb-5 overflow-hidden border-top border-4 border-primary">
        <div class="card-header bg-white border-0 p-4 pb-0">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <div class="eyebrow-muted mb-1 text-uppercase ls-1" style="letter-spacing: 0.05em; font-size: 0.7rem;">Pemantauan</div>
                    <h4 class="font-headline fw-bold mb-1">Capaian Laporan Kegiatan Sekolah ( Tahun Pelajaran {{ $currentTahunPelajaran }} )</h4>
                    <p class="text-soft small mb-0">Total akumulasi unggahan link per kategori untuk <strong>Tahun Pelajaran {{ $currentTahunPelajaran }}</strong></p>
                </div>
                <div class="d-flex align-items-center gap-2 bg-light px-3 py-2 rounded-pill">
                    <span class="material-symbols-outlined text-primary fs-5">domain</span>
                    <span class="fw-bold text-dark small">{{ $totalSchools }} Sekolah Binaan</span>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                @php
                    $categories = [
                        'kurikulum' => ['label' => 'Kurikulum', 'color' => 'primary', 'icon' => 'menu_book', 'bg' => 'rgba(13, 110, 253, 0.05)'],
                        'kesiswaan' => ['label' => 'Kesiswaan', 'color' => 'info', 'icon' => 'groups', 'bg' => 'rgba(13, 202, 240, 0.05)'],
                        'sarpras' => ['label' => 'Sarpras', 'color' => 'warning', 'icon' => 'home_work', 'bg' => 'rgba(255, 193, 7, 0.05)'],
                        'humas' => ['label' => 'Humas', 'color' => 'success', 'icon' => 'campaign', 'bg' => 'rgba(25, 135, 84, 0.05)'],
                    ];
                @endphp

                @foreach($categories as $key => $cat)
                    @php
                        $count = $modul2Stats[$key] ?? 0;
                        $percent = $totalSchools > 0 ? ($count / $totalSchools) * 100 : 0;
                    @endphp
                    <div class="col-md-6 col-lg-3">
                        <div class="p-4 rounded-4 h-100 border transition-all hover-shadow" style="background: {{ $cat['bg'] }}; border-color: rgba(0,0,0,0.05) !important;">
                            <div class="d-flex align-items-center gap-3 mb-4">
                                <div class="icon-box" style="width: 40px; height: 40px; border-radius: 12px; background: #fff; color: var(--bs-{{ $cat['color'] }}); box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                    <span class="material-symbols-outlined fs-5">{{ $cat['icon'] }}</span>
                                </div>
                                <span class="fw-bold text-dark">{{ $cat['label'] }}</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-end mb-2">
                                <div class="d-flex align-items-baseline gap-1">
                                    <h3 class="mb-0 fw-bold font-headline">{{ $count }}</h3>
                                    <span class="text-soft small">Laporan</span>
                                </div>
                                <small class="fw-bold text-{{ $cat['color'] }}">{{ $count }} Total</small>
                            </div>
                            
                            <div class="progress rounded-pill shadow-sm" style="height: 10px; background: #fff;">
                                <div class="progress-bar bg-{{ $cat['color'] }} progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $totalSchools > 0 ? min(($count / ($totalSchools * 12)) * 100, 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="table-panel overflow-hidden">
        <div class="table-panel-header d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-3">
            <div>
                <div class="eyebrow-muted mb-2">Daftar Pemantauan</div>
                <h4 class="font-headline fw-bold mb-1">Performa Sekolah Binaan</h4>
                <p class="text-soft small mb-0">Pantau skor, status, dan akses cepat ke tiap sekolah.</p>
            </div>

            <div class="d-flex flex-column flex-lg-row gap-2 gap-lg-3 align-items-lg-center">
                <div class="d-flex align-items-center gap-2">
                    <span class="small text-soft fw-semibold">Tampilkan</span>
                    <select id="entriesPerPage" class="form-select form-select-sm" style="width: 88px; min-height: 40px;">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                <div class="input-group input-group-sm" style="max-width: 280px;">
                    <span class="input-group-text bg-white border-end-0">
                        <span class="material-symbols-outlined fs-6">search</span>
                    </span>
                    <input type="text" id="searchSchool" class="form-control border-start-0 ps-0" placeholder="Cari nama sekolah...">
                </div>

                <a href="{{ route('school.export') }}" class="btn btn-primary btn-sm d-flex align-items-center justify-content-center gap-1 px-3">
                    <span class="material-symbols-outlined fs-6">download</span>
                    Export Excel
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle" id="schoolTable">
                <thead>
                    <tr>
                        <th class="ps-4">Rank</th>
                        <th>Nama Sekolah</th>
                        <th class="text-center">Skor</th>
                        <th class="text-center">Status</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schools as $school)
                        <tr class="school-row">
                            <td class="ps-4 fw-bold font-headline rank-number">
                                #{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="icon-box flex-shrink-0" style="width: 44px; height: 44px; border-radius: 14px;">
                                        <span class="material-symbols-outlined">apartment</span>
                                    </div>
                                    <div>
                                        <a href="{{ route('school.show', $school->id) }}" class="text-decoration-none school-name">
                                            <div class="fw-bold text-dark">{{ $school->name }}</div>
                                        </a>
                                        <div class="text-soft small">{{ $school->level }} &middot; {{ $school->status }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="fw-bold text-primary">{{ number_format($school->skor_performa, 1) }}</div>
                                <div class="text-soft" style="font-size: 0.75rem;">dari 100</div>
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill bg-{{ $school->status_color }}-subtle text-{{ $school->status_color }}">
                                    {{ $school->status_label }}
                                </span>
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-inline-flex align-items-center gap-2">
                                    <a href="{{ route('school.show', $school->id) }}" class="btn btn-outline-primary btn-sm px-3">
                                        Detail
                                    </a>
                                    <form action="{{ route('school.destroy', $school->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus sekolah {{ $school->name }}? Semua data terkait juga akan terhapus.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm px-3" title="Hapus Sekolah">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="emptyRow">
                            <td colspan="5" class="text-center text-soft py-5">Belum ada data sekolah.</td>
                        </tr>
                    @endforelse

                    <tr id="notFoundRow" style="display: none;">
                        <td colspan="5" class="text-center text-soft py-5">Sekolah yang dicari tidak ditemukan.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 px-4 py-3 border-top" style="border-color: rgba(15, 107, 125, 0.08) !important;">
            <small class="text-soft fw-semibold" id="pageInfo">Menampilkan data...</small>
            <nav id="paginationControls"></nav>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchSchool');
            const entriesSelect = document.getElementById('entriesPerPage');
            const schoolRows = Array.from(document.querySelectorAll('.school-row'));
            const notFoundRow = document.getElementById('notFoundRow');
            const paginationControls = document.getElementById('paginationControls');
            const pageInfo = document.getElementById('pageInfo');

            let currentPage = 1;
            let rowsPerPage = entriesSelect ? parseInt(entriesSelect.value, 10) : 5;

            if (entriesSelect) {
                entriesSelect.addEventListener('change', function() {
                    rowsPerPage = parseInt(this.value, 10);
                    currentPage = 1;
                    renderTable();
                });
            }

            function renderTable() {
                const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';

                const filteredRows = schoolRows.filter(function(row) {
                    const schoolName = row.querySelector('.school-name').textContent.toLowerCase();
                    return schoolName.includes(searchTerm);
                });

                schoolRows.forEach(function(row) {
                    row.style.display = 'none';
                });

                if (filteredRows.length === 0 && schoolRows.length > 0) {
                    if (notFoundRow) {
                        notFoundRow.style.display = '';
                    }
                    if (paginationControls) {
                        paginationControls.innerHTML = '';
                    }
                    if (pageInfo) {
                        pageInfo.textContent = 'Menampilkan 0 data';
                    }
                    return;
                }

                if (notFoundRow) {
                    notFoundRow.style.display = 'none';
                }

                const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
                if (currentPage > totalPages) {
                    currentPage = totalPages || 1;
                }

                const startIndex = (currentPage - 1) * rowsPerPage;
                const endIndex = startIndex + rowsPerPage;
                const rowsToShow = filteredRows.slice(startIndex, endIndex);

                rowsToShow.forEach(function(row, index) {
                    row.style.display = '';

                    const rankCell = row.querySelector('.rank-number');
                    if (rankCell) {
                        const rankNum = startIndex + index + 1;
                        rankCell.textContent = '#' + rankNum.toString().padStart(2, '0');
                    }
                });

                const endItem = Math.min(endIndex, filteredRows.length);
                if (pageInfo) {
                    pageInfo.textContent = 'Menampilkan ' + (startIndex + 1) + ' - ' + endItem + ' dari total ' + filteredRows.length + ' data';
                }

                renderPaginationUI(totalPages);
            }

            function renderPaginationUI(totalPages) {
                if (totalPages <= 1) {
                    if (paginationControls) {
                        paginationControls.innerHTML = '';
                    }
                    return;
                }

                let html = '<ul class="pagination pagination-sm mb-0">';

                html += '<li class="page-item ' + (currentPage === 1 ? 'disabled' : '') + '">';
                html += '<a class="page-link" href="#" data-page="' + (currentPage - 1) + '">Prev</a>';
                html += '</li>';

                let startPage = Math.max(1, currentPage - 2);
                let endPage = Math.min(totalPages, startPage + 4);

                if (endPage - startPage < 4) {
                    startPage = Math.max(1, endPage - 4);
                }

                for (let i = startPage; i <= endPage; i++) {
                    html += '<li class="page-item ' + (currentPage === i ? 'active' : '') + '">';
                    html += '<a class="page-link" href="#" data-page="' + i + '">' + i + '</a>';
                    html += '</li>';
                }

                html += '<li class="page-item ' + (currentPage === totalPages ? 'disabled' : '') + '">';
                html += '<a class="page-link" href="#" data-page="' + (currentPage + 1) + '">Next</a>';
                html += '</li>';
                html += '</ul>';

                if (paginationControls) {
                    paginationControls.innerHTML = html;
                }

                paginationControls.querySelectorAll('.page-link').forEach(function(link) {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const page = parseInt(this.getAttribute('data-page'), 10);
                        if (!isNaN(page) && page >= 1 && page <= totalPages) {
                            currentPage = page;
                            renderTable();
                        }
                    });
                });
            }

            if (searchInput) {
                searchInput.addEventListener('keyup', function() {
                    currentPage = 1;
                    renderTable();
                });
            }

            renderTable();
        });
    </script>
@endsection
