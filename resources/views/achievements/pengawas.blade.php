@extends('layouts.app')
@section('title', 'Rekap Prestasi Sekolah')
@section('content')
    <div class="mb-4">
        <h2 class="display-6 fw-extrabold font-headline mb-0">Pantau Prestasi</h2>
        <p class="text-muted small mb-0">Grafik dan rekapitulasi pencapaian sekolah binaan.</p>
    </div>

    {{-- KOTAK FILTER PILIH SEKOLAH (Dibuat otomatis submit tanpa tombol) --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-primary bg-opacity-10 border-start border-4 border-primary">
        <div class="card-body p-4">
            <form action="{{ route('achievement.pengawas') }}" method="GET" id="formPilihSekolah">
                <label class="form-label small fw-bold text-primary mb-2">Pilih Sekolah Binaan</label>
                <select name="school_id" id="schoolSelect" class="form-select border-primary" onchange="document.getElementById('formPilihSekolah').submit();" required>
                    <option value="">-- Ketik atau Pilih Nama Sekolah --</option>
                    @foreach($schools as $s)
                        <option value="{{ $s->id }}" {{ request('school_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    @if($selectedSchool)
        <div class="row g-4 mb-4">
            {{-- BAGIAN GRAFIK --}}
            <div class="col-12 col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-4 bg-white">
                    <h6 class="font-headline fw-bold mb-4">Sebaran Tingkat Prestasi</h6>
                    <div style="position: relative; height: 250px; width: 100%;">
                        <canvas id="prestasiChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- BAGIAN REKAP ANGKA --}}
            <div class="col-12 col-lg-7">
                
                {{-- Baris 1: Total & Tipe Peserta --}}
                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-bottom border-4 border-primary h-100">
                            <small class="text-muted fw-bold text-uppercase" style="font-size: 0.68rem;">Total Prestasi</small>
                            <h2 class="display-6 fw-bold mb-0 text-primary">{{ $achievements->count() }}</h2>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-bottom border-4 border-info h-100">
                            <small class="text-muted fw-bold text-uppercase" style="font-size: 0.68rem;">Oleh Siswa</small>
                            <h2 class="display-6 fw-bold mb-0 text-info">{{ $achievements->where('tipe_peserta', 'Siswa')->count() }}</h2>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-bottom border-4 border-secondary h-100">
                            <small class="text-muted fw-bold text-uppercase" style="font-size: 0.68rem;">Oleh Guru/Tendik</small>
                            <h2 class="display-6 fw-bold mb-0 text-secondary">{{ $achievements->whereIn('tipe_peserta', ['Guru', 'Tendik'])->count() }}</h2>
                        </div>
                    </div>
                </div>

                {{-- Baris 2: Rincian Berdasarkan Tingkat (Warna selaras dengan Grafik) --}}
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-bottom border-4 h-100" style="border-color: #f4a261 !important;">
                            <small class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem;">Kota/Kab</small>
                            <h3 class="fw-bold mb-0 mt-1" style="color: #f4a261;">{{ $chartData[0] }}</h3>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-bottom border-4 h-100" style="border-color: #2a9d8f !important;">
                            <small class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem;">Provinsi</small>
                            <h3 class="fw-bold mb-0 mt-1" style="color: #2a9d8f;">{{ $chartData[1] }}</h3>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-bottom border-4 h-100" style="border-color: #e76f51 !important;">
                            <small class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem;">Nasional</small>
                            <h3 class="fw-bold mb-0 mt-1" style="color: #e76f51;">{{ $chartData[2] }}</h3>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-bottom border-4 h-100" style="border-color: #e9c46a !important;">
                            <small class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem;">Internasional</small>
                            <h3 class="fw-bold mb-0 mt-1" style="color: #e9c46a;">{{ $chartData[3] }}</h3>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- TABEL DATA BAWAHNYA --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            {{-- Header Tabel & Tombol --}}
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <h5 class="font-headline fw-bold mb-0">Rincian Prestasi: <span class="text-primary">{{ $selectedSchool->name }}</span></h5>
                
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    {{-- Tombol Export Excel --}}
                    <a href="{{ route('achievement.export.pengawas', ['school_id' => $selectedSchool->id]) }}" class="btn btn-success btn-sm fw-bold d-flex align-items-center gap-1 shadow-sm">
                        <span class="material-symbols-outlined fs-6">download</span> Export
                    </a>
                    
                    {{-- Pilihan Entries (Data Entry Tampil) --}}
                    <select id="entriesPrestasi" class="form-select form-select-sm bg-light border-0 shadow-sm" style="width: auto;">
                        <option value="5" selected>5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>

                    {{-- Kotak Pencarian --}}
                    <div class="input-group input-group-sm shadow-sm" style="max-width: 150px;">
                        <input type="text" id="searchPrestasi" class="form-control border-0 bg-light" placeholder="Cari...">
                    </div>
                </div>
            </div>

            {{-- Isi Tabel --}}
            <div class="card-body p-0">
                <div class="p-4 pt-3 table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="bg-light small text-muted">
                            <tr>
                                <th>Tgl</th>
                                <th>Peringkat/Tingkat</th>
                                <th>Kategori</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody id="prestasiTableBody">
                            @forelse($achievements as $ach)
                            <tr class="prestasi-row small">
                                <td class="fw-bold">{{ \Carbon\Carbon::parse($ach->tanggal)->translatedFormat('d M Y') }}</td>
                                <td><span class="text-dark">{{ $ach->peringkat }}</span> <br><span class="text-muted">{{ $ach->tingkat }}</span></td>
                                <td>
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary me-1">{{ $ach->tipe_peserta }}</span>
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info">{{ $ach->kategori }}</span>
                                </td>
                                <td class="text-muted">{{ $ach->keterangan }}</td>
                            </tr>
                            @empty
                            <tr id="emptyRow"><td colspan="4" class="text-center py-5 text-muted small">Belum ada riwayat prestasi untuk sekolah ini.</td></tr>
                            @endforelse
                            <tr id="notFoundRow" style="display:none;"><td colspan="4" class="text-center py-4 text-muted small">Data tidak ditemukan.</td></tr>
                        </tbody>
                    </table>
                </div>

                {{-- Footer Paginasi --}}
                <div class="p-3 bg-light bg-opacity-25 border-top d-flex justify-content-between align-items-center rounded-bottom-4">
                    <small class="text-muted fw-semibold" id="prestasiPageInfo"></small>
                    <nav id="prestasiPagination"></nav>
                </div>
            </div>
        </div>

        {{-- SCRIPT PAGINATION & SEARCH --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
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
        
        {{-- SCRIPT UNTUK MEMUNCULKAN GRAFIK CHART.JS --}}
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('prestasiChart').getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Kota/Kab', 'Provinsi', 'Nasional', 'Internasional'],
                        datasets: [{
                            data: {{ json_encode($chartData) }},
                            backgroundColor: ['#f4a261', '#2a9d8f', '#e76f51', '#e9c46a'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' }
                        }
                    }
                });
            });
        </script>
    @else
        <div class="text-center py-5">
            <span class="material-symbols-outlined display-1 text-muted opacity-25 mb-3">trophy</span>
            <h5 class="fw-bold text-muted">Pilih Sekolah</h5>
            <p class="small text-muted">Pilih sekolah untuk melihat grafik dan rekapitulasi prestasinya.</p>
        </div>
    @endif
    
    {{-- ========================================================================= --}}
    {{-- SCRIPT: CHOICES.JS (Gaya Lega, Pencarian Akurat, Kotak Full & Blok Biru) --}}
    {{-- ========================================================================= --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    
    <style>
        /* 1. Pengaturan Dasar (Berlaku di semua perangkat) */
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
            padding: 15px 20px !important; /* Area klik lebih luas untuk jari */
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
                position: fixed !important; /* Terlihat seperti 'popup' modal */
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
            const schoolSelect = document.getElementById('schoolSelect');
            
            if (schoolSelect) {
                new Choices(schoolSelect, {
                    searchEnabled: true, // FITUR CARI TETAP AKTIF DI HP
                    searchPlaceholderValue: 'Ketik nama sekolah...',
                    itemSelectText: '',
                    noResultsText: 'Sekolah tidak ditemukan',
                    noChoicesText: 'Tidak ada pilihan',
                    shouldSort: false,
                    searchFuzzy: false,       // Pencarian spesifik
                    searchFields: ['label'],  // Hanya cari berdasarkan nama sekolah (bukan ID)
                    searchResultLimit: 15,
                    placeholder: true,
                    placeholderValue: '-- Ketik atau Pilih Nama Sekolah --'
                });
            }
        });
    </script>
@endsection