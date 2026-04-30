@extends('layouts.app')

@section('title', 'Rekap Prestasi Sekolah')

@section('content')
    {{-- ========================================== --}}
    {{-- HEADER HALAMAN & TOMBOL DOWNLOAD UTAMA --}}
    {{-- ========================================== --}}
    <div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3">
        <div>
            <h2 class="display-6 fw-extrabold font-headline mb-0">Pantau Prestasi</h2>
            <p class="text-muted small mb-0">Grafik global dan rekapitulasi pencapaian seluruh sekolah binaan.</p>
        </div>

        {{-- MENU DOWNLOAD MANDIRI --}}
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('achievement.export.pengawas') }}" class="btn btn-outline-success fw-bold d-flex align-items-center gap-1 shadow-sm">
                <span class="material-symbols-outlined fs-6">inventory_2</span> 
                Export Semua Sekolah
            </a>

            @if($selectedSchool)
                <a href="{{ route('achievement.export.pengawas', ['school_id' => $selectedSchool->id]) }}" class="btn btn-success fw-bold d-flex align-items-center gap-1 shadow-sm">
                    <span class="material-symbols-outlined fs-6">download</span> 
                    Export {{ $selectedSchool->name }}
                </a>
            @endif
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- 1. GRAFIK & STATISTIK GLOBAL (Keseluruhan) --}}
    {{-- ========================================== --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-primary h-100">
                <small class="text-muted fw-bold text-uppercase" style="font-size: 0.68rem;">Total Seluruh Prestasi</small>
                <h2 class="display-6 fw-bold mb-0 text-primary">{{ $totalPrestasi }}</h2>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-info h-100">
                <small class="text-muted fw-bold text-uppercase" style="font-size: 0.68rem;">Oleh Siswa</small>
                <h2 class="display-6 fw-bold mb-0 text-info">{{ $totalSiswa }}</h2>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-secondary h-100">
                <small class="text-muted fw-bold text-uppercase" style="font-size: 0.68rem;">Oleh Guru/Tendik</small>
                <h2 class="display-6 fw-bold mb-0 text-secondary">{{ $totalGuruTendik }}</h2>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-md-5">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-4 bg-white border-top border-4 border-primary">
                <h6 class="font-headline fw-bold mb-4 text-center">Berdasarkan Tingkat</h6>
                <div style="position: relative; height: 220px; width: 100%;">
                    <canvas id="globalTingkatChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-7">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-4 bg-white border-top border-4 border-info">
                <h6 class="font-headline fw-bold mb-4 text-center">Kategori &amp; Tipe Peserta</h6>
                <div style="position: relative; height: 220px; width: 100%;">
                    <canvas id="globalKategoriTipeChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- 2. KOTAK FILTER PILIH SEKOLAH --}}
    {{-- ========================================== --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-primary bg-opacity-10 border-start border-4 border-primary">
        <div class="card-body p-4">
            <form action="{{ route('achievement.pengawas') }}" method="GET" id="formPilihSekolah">
                <label class="form-label small fw-bold text-primary mb-2">Pilih Sekolah Binaan untuk Melihat Rincian</label>
                <select name="school_id" id="schoolSelect" class="form-select border-primary" onchange="document.getElementById('formPilihSekolah').submit();">
                    <option value="">-- Menampilkan Semua Sekolah Binaan --</option>
                    @foreach($schools as $s)
                        <option value="{{ $s->id }}" {{ request('school_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- 3. STATISTIK KHUSUS SEKOLAH TERPILIH --}}
    {{-- ========================================== --}}
    @if($selectedSchool)
    <div class="card border-0 shadow-sm rounded-4 mb-4 border-top border-4 border-success">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
            <h5 class="font-headline fw-bold mb-0">Statistik Spesifik: <span class="text-success">{{ $selectedSchool->name }}</span></h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-3 mb-4">
                <div class="col-12 col-sm-4">
                    <div class="p-3 bg-light rounded-4 border border-success border-opacity-25 text-center h-100">
                        <small class="text-muted fw-bold text-uppercase d-block mb-1" style="font-size: 0.65rem;">Total Prestasi</small>
                        <h3 class="fw-bold text-success mb-0">{{ $schoolTotalPrestasi }}</h3>
                    </div>
                </div>
                <div class="col-6 col-sm-4">
                    <div class="p-3 bg-light rounded-4 text-center h-100">
                        <small class="text-muted fw-bold text-uppercase d-block mb-1" style="font-size: 0.65rem;">Siswa</small>
                        <h3 class="fw-bold text-dark mb-0">{{ $schoolTotalSiswa }}</h3>
                    </div>
                </div>
                <div class="col-6 col-sm-4">
                    <div class="p-3 bg-light rounded-4 text-center h-100">
                        <small class="text-muted fw-bold text-uppercase d-block mb-1" style="font-size: 0.65rem;">Guru/Tendik</small>
                        <h3 class="fw-bold text-dark mb-0">{{ $schoolTotalGuruTendik }}</h3>
                    </div>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-12 col-md-5">
                    <h6 class="fw-bold text-center small text-muted mb-3">Tingkat</h6>
                    <div style="position: relative; height: 200px; width: 100%;">
                        <canvas id="schoolTingkatChart"></canvas>
                    </div>
                </div>
                <div class="col-12 col-md-7">
                    <h6 class="fw-bold text-center small text-muted mb-3">Kategori &amp; Tipe Peserta</h6>
                    <div style="position: relative; height: 200px; width: 100%;">
                        <canvas id="schoolKategoriTipeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ========================================== --}}
    {{-- 4. TABEL DATA PRESTASI --}}
    {{-- ========================================== --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <h5 class="font-headline fw-bold mb-0">
                Data Rincian: 
                <span class="text-primary">{{ $selectedSchool ? $selectedSchool->name : 'Seluruh Sekolah Binaan' }}</span>
            </h5>
            
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <select id="entriesPrestasi" class="form-select form-select-sm bg-light border-0 shadow-sm" style="width: auto;">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>

                <div class="input-group input-group-sm shadow-sm" style="max-width: 200px;">
                    <span class="input-group-text bg-light border-0"><span class="material-symbols-outlined fs-6 text-muted">search</span></span>
                    <input type="text" id="searchPrestasi" class="form-control border-0 bg-light ps-0" placeholder="Cari data...">
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="p-4 pt-3 table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead class="bg-light small text-muted">
                        <tr>
                            <th class="ps-2">Tgl</th>
                            <th>Asal Sekolah</th> 
                            <th>Peringkat/Tingkat</th>
                            <th>Nama Lengkap Peserta & Kategori</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody id="prestasiTableBody">
                        @forelse($achievements as $ach)
                        <tr class="prestasi-row small">
                            <td class="fw-bold text-nowrap ps-2">{{ \Carbon\Carbon::parse($ach->tanggal)->translatedFormat('d M Y') }}</td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary">{{ $ach->school->name ?? '-' }}</span>
                            </td>
                            <td><span class="text-dark fw-semibold">{{ $ach->peringkat }}</span> <br><span class="text-muted">{{ $ach->tingkat }}</span></td>
                            <td>
                                <div class="fw-bold mb-1 text-dark">{{ $ach->nama_peserta }}</div>
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary me-1">{{ $ach->tipe_peserta }}</span>
                                <span class="badge bg-info bg-opacity-10 text-info border border-info">{{ $ach->kategori }}</span>
                            </td>
                            <td class="text-muted">{{ $ach->keterangan }}</td>
                        </tr>
                        @empty
                        <tr id="emptyRow"><td colspan="5" class="text-center py-5 text-muted small">Belum ada riwayat prestasi yang tercatat.</td></tr>
                        @endforelse
                        <tr id="notFoundRow" style="display:none;"><td colspan="5" class="text-center py-4 text-muted small">Data tidak ditemukan.</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="p-3 bg-light bg-opacity-25 border-top d-flex justify-content-between align-items-center rounded-bottom-4">
                <small class="text-muted fw-semibold" id="prestasiPageInfo"></small>
                <nav id="prestasiPagination"></nav>
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- SCRIPTS: CHART.JS & CHOICES.JS & LOGIKA PENCARIAN --}}
    {{-- ========================================================================= --}}
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Chart Global (Semua Sekolah)
            // 1. Chart Global (Semua Sekolah)
            const globalTingkatTipeData = @json($globalTingkatTipeChart);
            const globalTingkatCtx = document.getElementById('globalTingkatChart');
            if(globalTingkatCtx) {
                new Chart(globalTingkatCtx.getContext('2d'), {
                    type: 'pie',
                    data: {
                        labels: ['Kota/Kab', 'Provinsi', 'Nasional', 'Internasional'],
                        datasets: [{
                            data: @json($globalTingkatChart),
                            backgroundColor: ['#f4a261', '#2a9d8f', '#e76f51', '#e9c46a'],
                            borderWidth: 0
                        }]
                    },
                    options: { 
                        responsive: true, 
                        maintainAspectRatio: false, 
                        plugins: { 
                            legend: { position: 'bottom' },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var index = context.dataIndex;
                                        var total = context.raw || 0;
                                        var siswa = 0;
                                        var guruTendik = 0;
                                        
                                        if (globalTingkatTipeData && globalTingkatTipeData.siswa) {
                                            siswa = globalTingkatTipeData.siswa[index] || 0;
                                        }
                                        if (globalTingkatTipeData && globalTingkatTipeData.guru_tendik) {
                                            guruTendik = globalTingkatTipeData.guru_tendik[index] || 0;
                                        }
                                        
                                        return [
                                            context.label + ': ' + total,
                                            ' - Siswa: ' + siswa,
                                            ' - Guru/Tendik: ' + guruTendik
                                        ];
                                    }
                                }
                            }
                        } 
                    }
                });
            }

            const globalKategoriTipeCtx = document.getElementById('globalKategoriTipeChart');
            if(globalKategoriTipeCtx) {
                new Chart(globalKategoriTipeCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: ['Siswa', 'Guru', 'Tendik'],
                        datasets: [
                            {
                                label: 'Individu',
                                data: @json($globalKategoriTipeChart['individu']),
                                backgroundColor: '#457b9d',
                                borderRadius: 4, borderWidth: 0
                            },
                            {
                                label: 'Tim/Kelompok',
                                data: @json($globalKategoriTipeChart['tim']),
                                backgroundColor: '#e63946',
                                borderRadius: 4, borderWidth: 0
                            }
                        ]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { position: 'bottom' } },
                        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                    }
                });
            }

            // 2. Chart Spesifik Sekolah
            @if($selectedSchool)
                const schoolTingkatTipeData = @json($schoolTingkatTipeChart);
                const schoolTingkatCtx = document.getElementById('schoolTingkatChart');
                if(schoolTingkatCtx) {
                    new Chart(schoolTingkatCtx.getContext('2d'), {
                        type: 'pie',
                        data: {
                            labels: ['Kota/Kab', 'Prov', 'Nas', 'Intl'],
                            datasets: [{
                                data: @json($schoolTingkatChart),
                                backgroundColor: ['#f4a261', '#2a9d8f', '#e76f51', '#e9c46a'],
                                borderWidth: 0
                            }]
                        },
                        options: { 
                            responsive: true, 
                            maintainAspectRatio: false, 
                            plugins: { 
                                legend: { position: 'bottom' },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            var index = context.dataIndex;
                                            var total = context.raw || 0;
                                            var siswa = 0;
                                            var guruTendik = 0;
                                            
                                            if (schoolTingkatTipeData && schoolTingkatTipeData.siswa) {
                                                siswa = schoolTingkatTipeData.siswa[index] || 0;
                                            }
                                            if (schoolTingkatTipeData && schoolTingkatTipeData.guru_tendik) {
                                                guruTendik = schoolTingkatTipeData.guru_tendik[index] || 0;
                                            }
                                            
                                            return [
                                                context.label + ': ' + total,
                                                ' - Siswa: ' + siswa,
                                                ' - Guru/Tendik: ' + guruTendik
                                            ];
                                        }
                                    }
                                }
                            } 
                        }
                    });
                }

                const schoolKategoriTipeCtx = document.getElementById('schoolKategoriTipeChart');
                if(schoolKategoriTipeCtx) {
                    new Chart(schoolKategoriTipeCtx.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: ['Siswa', 'Guru', 'Tendik'],
                            datasets: [
                                {
                                    label: 'Individu',
                                    data: @json($schoolKategoriTipeChart['individu']),
                                    backgroundColor: '#457b9d',
                                    borderRadius: 4, borderWidth: 0
                                },
                                {
                                    label: 'Tim/Kelompok',
                                    data: @json($schoolKategoriTipeChart['tim']),
                                    backgroundColor: '#e63946',
                                    borderRadius: 4, borderWidth: 0
                                }
                            ]
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false,
                            plugins: { legend: { position: 'bottom' } },
                            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                        }
                    });
                }
            @endif
        });
    </script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <style>
        .choices { font-size: 1rem; margin-bottom: 0; }
        .choices__inner {
            background-color: #fff !important; border: 1px solid #0d6efd !important;
            border-radius: 0.5rem !important; padding: 0.5rem 1rem !important;
            min-height: 50px !important; display: flex; align-items: center;
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
        @media (max-width: 768px) {
            .choices__list--dropdown {
                position: fixed !important; top: 20% !important; left: 5% !important; right: 5% !important;
                width: 90% !important; max-height: 60vh !important; border: 1px solid #dee2e6 !important;
            }
            .choices.is-open::after {
                content: ""; position: fixed; top: 0; left: 0; right: 0; bottom: 0;
                background: rgba(0,0,0,0.4); z-index: 999;
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
                    placeholder: true,
                    placeholderValue: '-- Menampilkan Semua Sekolah Binaan --'
                });
            }

            const tableBody = document.getElementById('prestasiTableBody');
            if(!tableBody) return;

            let allRows = Array.from(document.querySelectorAll('.prestasi-row'));
            const searchInput = document.getElementById('searchPrestasi');
            const entriesSelect = document.getElementById('entriesPrestasi');
            const pageInfo = document.getElementById('prestasiPageInfo');
            const paginationNav = document.getElementById('prestasiPagination');
            const notFoundRow = document.getElementById('notFoundRow');

            if(allRows.length === 0) return;

            let currentPage = 1;
            let rowsPerPage = parseInt(entriesSelect.value);

            function updateTable() {
                const searchTerm = searchInput.value.toLowerCase();
                const filteredRows = allRows.filter(row => row.textContent.toLowerCase().includes(searchTerm));

                const totalRows = filteredRows.length;
                const totalPages = Math.ceil(totalRows / rowsPerPage);

                if(totalRows === 0) {
                    notFoundRow.style.display = searchTerm ? '' : 'none';
                    pageInfo.textContent = '';
                    paginationNav.innerHTML = '';
                } else {
                    notFoundRow.style.display = 'none';
                }

                allRows.forEach(row => row.style.display = 'none');

                const start = (currentPage - 1) * rowsPerPage;
                const end = start + rowsPerPage;

                filteredRows.slice(start, end).forEach(row => row.style.display = '');

                pageInfo.textContent = totalRows > 0 ? `Menampilkan ${start + 1} - ${Math.min(end, totalRows)} dari total ${totalRows} data` : '';
                renderPagination(totalPages);
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