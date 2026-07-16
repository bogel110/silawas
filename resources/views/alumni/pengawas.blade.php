@extends('layouts.app')

@section('title', 'Peta Alumni')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h1 class="h3 font-headline fw-bold mb-1">Peta Alumni</h1>
        <p class="text-soft mb-0">Lihat data alumni dari sekolah binaan Anda</p>
    </div>
</div>

<!-- Filter Sekolah -->
<div class="content-panel mb-4">
    <div class="p-4">
        <form method="GET" action="{{ route('alumni.pengawas') }}" class="row g-3 align-items-end">
            <div class="col-12 col-md-8">
                <label for="schoolSelect" class="form-label fw-600">Pilih Sekolah Binaan</label>
                <select class="form-select" id="schoolSelect" name="school_id" onchange="this.form.submit()">
                    <option value="">-- Pilih Sekolah --</option>
                    @foreach($schools as $school)
                        <option value="{{ $school->id }}" {{ $selectedSchool && $selectedSchool->id === $school->id ? 'selected' : '' }}>
                            {{ $school->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @if($selectedSchool)
                <div class="col-12 col-md-4">
                    <a href="{{ route('alumni.pengawas') }}" class="btn btn-outline-secondary w-100">
                        <span class="material-symbols-outlined" style="font-size: 1rem; margin-right: 0.5rem;">close</span>
                        Hapus Filter
                    </a>
                </div>
            @endif
        </form>
    </div>
</div>

<!-- Export Buttons -->
@if($selectedSchool || count($schools) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                @if($selectedSchool)
                    <a href="{{ route('alumni.export.pengawas', ['school_id' => $selectedSchool->id]) }}" 
                       class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-2" 
                       title="Ekspor data alumni sekolah terpilih">
                        <span class="material-symbols-outlined fs-6">download</span>
                        <span class="d-none d-md-inline">Ekspor Sekolah</span>
                    </a>
                @endif
                <a href="{{ route('alumni.export.pengawas') }}" 
                   class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-2" 
                   title="Ekspor data alumni semua sekolah binaan">
                    <span class="material-symbols-outlined fs-6">download</span>
                    <span class="d-none d-md-inline">Ekspor Semua</span>
                </a>
            </div>
        </div>
    </div>
@endif

@if($selectedSchool)
    <!-- Statistik Cards -->
    <div class="row mb-4">
        <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="metric-card">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <p class="text-soft mb-1 small">Total Alumni</p>
                        <h3 class="h2 fw-bold mb-0">{{ $stats['total'] }}</h3>
                    </div>
                    <div class="icon-box">
                        <span class="material-symbols-outlined">people</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="metric-card">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <p class="text-soft mb-1 small">Melanjutkan Studi</p>
                        <h3 class="h2 fw-bold mb-0">{{ $stats['melanjutkan_studi'] }}</h3>
                    </div>
                    <div class="icon-box" style="background: linear-gradient(135deg, rgba(13, 202, 240, 0.14), rgba(13, 202, 240, 0.06)); color: #0dcaf0;">
                        <span class="material-symbols-outlined">school</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="metric-card">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <p class="text-soft mb-1 small">Bekerja</p>
                        <h3 class="h2 fw-bold mb-0">{{ $stats['bekerja'] }}</h3>
                    </div>
                    <div class="icon-box" style="background: linear-gradient(135deg, rgba(25, 135, 84, 0.14), rgba(25, 135, 84, 0.06)); color: #198754;">
                        <span class="material-symbols-outlined">work</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="metric-card">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <p class="text-soft mb-1 small">Persentase</p>
                        <h3 class="h2 fw-bold mb-0">{{ $stats['total'] > 0 ? round(($stats['melanjutkan_studi'] / $stats['total']) * 100) : 0 }}%</h3>
                    </div>
                    <div class="icon-box" style="background: linear-gradient(135deg, rgba(255, 193, 7, 0.14), rgba(255, 193, 7, 0.06)); color: #ffc107;">
                        <span class="material-symbols-outlined">percent</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pie Charts -->
    <div class="row mb-4">
        <div class="col-12 col-md-6 col-lg-4 mb-3">
            <div class="content-panel">
                <div class="p-4">
                    <h5 class="fw-bold mb-4">Status Alumni</h5>
                    <div style="position: relative; height: 300px;">
                        <canvas id="chartStatus"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-4 mb-3">
            <div class="content-panel">
                <div class="p-4">
                    <h5 class="fw-bold mb-4">Klasifikasi Studi</h5>
                    <div style="position: relative; height: 300px;">
                        <canvas id="chartStudi"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-4 mb-3">
            <div class="content-panel">
                <div class="p-4">
                    <h5 class="fw-bold mb-4">Klasifikasi Pekerjaan</h5>
                    <div style="position: relative; height: 300px;">
                        <canvas id="chartKerja"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Alumni -->
    <div class="table-panel">
        <div class="table-panel-header">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="mb-0 fw-bold">Daftar Alumni - {{ $selectedSchool->name }}</h5>
            </div>
            <!-- Search Bar - Live Search -->
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="input-group">
                        <input type="text" class="form-control" id="liveSearchInputPengawas" 
                               placeholder="Cari alumni..." 
                               style="min-height: 42px;">
                        <button class="btn btn-soft px-3" type="button" style="min-height: 42px;">
                            <span class="material-symbols-outlined fs-5" style="font-size: 1.2rem;">search</span>
                        </button>
                        <button class="btn btn-outline-secondary px-3" id="clearSearchBtnPengawas" type="button" style="min-height: 42px; display: none;">
                            <span class="material-symbols-outlined fs-5" style="font-size: 1.2rem;">close</span>
                        </button>
                    </div>
                </div>
                <div class="col-12 col-md-auto">
                    <small class="text-soft" id="searchCounterPengawas" style="display: none;">
                        <span id="resultCountPengawas">0</span> data ditemukan
                    </small>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama Lengkap</th>
                        <th>Tahun Lulus</th>
                        <th>Status</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody id="alumniTableBodyPengawas">
                    @forelse($alumni as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td class="fw-600">{{ $item->nama_lengkap }}</td>
                            <td>{{ $item->tahun_lulus }}</td>
                            <td>
                                @if($item->status === 'Melanjutkan Studi')
                                    <span class="badge bg-primary">Melanjutkan Studi</span>
                                @else
                                    <span class="badge bg-success">Bekerja</span>
                                @endif
                            </td>
                            <td>
                                @if($item->status === 'Melanjutkan Studi')
                                    <small class="text-soft">
                                        <strong>{{ $item->jenis_studi }}</strong> ({{ $item->jalur_penerimaan }})<br>
                                        {{ Str::limit($item->keterangan ?? '-', 50) }}
                                    </small>
                                @else
                                    <small class="text-soft">
                                        <strong>{{ $item->jenis_pekerjaan }}</strong><br>
                                        {{ Str::limit($item->keterangan ?? '-', 50) }}
                                    </small>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-soft py-5">
                                <div>
                                    <span class="material-symbols-outlined" style="font-size: 3rem; opacity: 0.3;">person_off</span>
                                    <p class="mt-3 mb-0">Belum ada data alumni untuk sekolah ini</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                 </tbody>
             </table>
         </div>

         <!-- Pagination Section -->
         <div class="pagination-footer">
             <div class="row align-items-center g-3">
                 <div class="col-12 col-md-auto">
                     <div class="d-flex align-items-center gap-2">
                         <label for="perPageSelectorPengawas" class="form-label mb-0 small fw-600">Tampilkan:</label>
                         <select id="perPageSelectorPengawas" class="form-select form-select-sm" style="width: auto;">
                             <option value="5">5</option>
                             <option value="10" selected>10</option>
                             <option value="25">25</option>
                             <option value="50">50</option>
                             <option value="100">100</option>
                         </select>
                         <span class="small text-soft">data per halaman</span>
                     </div>
                 </div>
                 <div class="col-12 col-md">
                     <div class="d-flex justify-content-center justify-content-md-end" id="paginationContainerPengawas">
                         <!-- Pagination links akan di-render via AJAX -->
                     </div>
                 </div>
             </div>
         </div>
     </div>

@else
    <!-- Empty State -->
    <div class="content-panel py-5">
        <div class="text-center">
            <span class="material-symbols-outlined" style="font-size: 4rem; opacity: 0.3;">select_check_box</span>
            <p class="mt-4 text-soft">Pilih sekolah binaan untuk melihat data alumni</p>
        </div>
    </div>
@endif

<style>
    .fw-600 {
        font-weight: 600;
    }

    .metric-card {
        position: relative;
        overflow: hidden;
        height: 100%;
        padding: 1.5rem;
        border: 1px solid rgba(255, 255, 255, 0.95);
        border-radius: 22px;
        box-shadow: 0 12px 30px rgba(24, 50, 58, 0.07);
        background: rgba(255, 255, 255, 0.92);
        backdrop-filter: blur(10px);
    }

    .icon-box {
        width: 52px;
        height: 52px;
        border-radius: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(15, 107, 125, 0.14), rgba(15, 107, 125, 0.06));
        color: var(--brand-700);
    }

    html[data-theme="dark"] .metric-card {
        background: rgba(15, 35, 41, 0.92);
        border-color: rgba(180, 221, 227, 0.12);
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
<script>
    @if($selectedSchool)
        // Deteksi tema saat ini
        function getTheme() {
            return document.documentElement.dataset.theme || 'light';
        }

        // Fungsi untuk mendapatkan warna berdasarkan tema
        function getChartColors() {
            const theme = getTheme();
            if (theme === 'dark') {
                return {
                    textColor: '#e7f3f5',
                    borderColor: 'rgba(15, 35, 41, 0.95)'
                };
            } else {
                return {
                    textColor: '#18323a',
                    borderColor: 'rgba(255, 255, 255, 0.95)'
                };
            }
        }

        let chartStatus, chartStudi, chartKerja;

        function createCharts() {
            const colors = getChartColors();

            // Destroy existing charts jika ada
            if (chartStatus) chartStatus.destroy();
            if (chartStudi) chartStudi.destroy();
            if (chartKerja) chartKerja.destroy();

            // Chart Status Alumni
            const ctxStatus = document.getElementById('chartStatus').getContext('2d');
            chartStatus = new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: ['Melanjutkan Studi', 'Bekerja'],
                    datasets: [{
                        data: [{{ $stats['melanjutkan_studi'] }}, {{ $stats['bekerja'] }}],
                        backgroundColor: ['#0dcaf0', '#198754'],
                        borderColor: colors.borderColor,
                        borderWidth: 3,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: colors.textColor,
                                font: { size: 12, weight: '600' },
                                padding: 15,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        }
                    }
                }
            });

            // Chart Klasifikasi Studi
            const ctxStudi = document.getElementById('chartStudi').getContext('2d');
            chartStudi = new Chart(ctxStudi, {
                type: 'doughnut',
                data: {
                    labels: ['PTN', 'PTS', 'KEDINASAN'],
                    datasets: [{
                        data: [{{ $stats['ptn'] }}, {{ $stats['pts'] }}, {{ $stats['kedinasan'] }}],
                        backgroundColor: ['#0f6b7d', '#f4a261', '#20c997'],
                        borderColor: colors.borderColor,
                        borderWidth: 3,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: colors.textColor,
                                font: { size: 12, weight: '600' },
                                padding: 15,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        }
                    }
                }
            });

            // Chart Klasifikasi Pekerjaan
            const ctxKerja = document.getElementById('chartKerja').getContext('2d');
            chartKerja = new Chart(ctxKerja, {
                type: 'doughnut',
                data: {
                    labels: ['ASN', 'TNI', 'POLRI', 'SWASTA'],
                    datasets: [{
                        data: [{{ $stats['asn'] }}, {{ $stats['tni'] }}, {{ $stats['polri'] }}, {{ $stats['swasta'] }}],
                        backgroundColor: ['#6f42c1', '#e83e8c', '#fd7e14', '#0dcaf0'],
                        borderColor: colors.borderColor,
                        borderWidth: 3,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: colors.textColor,
                                font: { size: 12, weight: '600' },
                                padding: 15,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        }
                    }
                }
            });
        }

        // Buat chart saat halaman load
        createCharts();

        // Listen untuk perubahan theme
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'attributes' && mutation.attributeName === 'data-theme') {
                    createCharts();
                }
            });
        });

        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['data-theme']
        });
    @endif

    // ===== LIVE SEARCH PENGAWAS =====
    let searchTimeoutPengawas;

    function loadSearchResultsPengawas(query) {
        const schoolId = document.getElementById('schoolSelect').value;
        if (!schoolId) return;

        const tbody = document.getElementById('alumniTableBodyPengawas');
        const counter = document.getElementById('searchCounterPengawas');
        const countSpan = document.getElementById('resultCountPengawas');
        const clearBtn = document.getElementById('clearSearchBtnPengawas');

        // Build URL
        const url = '{{ route("alumni.search_pengawas") }}?school_id=' + schoolId + '&q=' + encodeURIComponent(query.trim());

        fetch(url)
            .then(res => res.json())
            .then(data => {
                // Update counter
                if (query.trim()) {
                    counter.style.display = 'inline';
                    countSpan.textContent = data.count;
                } else {
                    counter.style.display = 'none';
                }

                // Tampilkan/sembunyikan clear button
                clearBtn.style.display = query.trim() ? 'flex' : 'none';

                // Render table rows
                if (data.data.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="5" class="text-center text-soft py-5">
                                <div>
                                    <span class="material-symbols-outlined" style="font-size: 3rem; opacity: 0.3;">person_off</span>
                                    <p class="mt-3 mb-0">${query.trim() ? 'Tidak ada hasil pencarian untuk "<strong>' + query.trim() + '</strong>"' : 'Belum ada data alumni'}</p>
                                </div>
                            </td>
                        </tr>
                    `;
                    return;
                }

                let html = '';
                data.data.forEach((item, index) => {
                    const statusBadge = item.status === 'Melanjutkan Studi' 
                        ? '<span class="badge bg-primary">Melanjutkan Studi</span>'
                        : '<span class="badge bg-success">Bekerja</span>';

                    let detailHtml = '';
                    if (item.status === 'Melanjutkan Studi') {
                        detailHtml = '<small class="text-soft"><strong>' + (item.jenis_studi || '') + '</strong> (' + (item.jalur_penerimaan || '') + ')<br>' + (item.keterangan ? item.keterangan.substring(0, 50) : '-') + '</small>';
                    } else {
                        detailHtml = '<small class="text-soft"><strong>' + (item.jenis_pekerjaan || '') + '</strong><br>' + (item.keterangan ? item.keterangan.substring(0, 50) : '-') + '</small>';
                    }

                    html += `
                        <tr>
                            <td>${index + 1}</td>
                            <td class="fw-600">${escapeHtmlPengawas(item.nama_lengkap)}</td>
                            <td>${item.tahun_lulus}</td>
                            <td>${statusBadge}</td>
                            <td>${detailHtml}</td>
                        </tr>
                    `;
                });
                tbody.innerHTML = html;
            })
            .catch(err => console.error('Search error:', err));
    }

    function escapeHtmlPengawas(text) {
        const div = document.createElement('div');
        div.textContent = text || '';
        return div.innerHTML;
    }

    // Live search on input
    const liveSearchInputPengawas = document.getElementById('liveSearchInputPengawas');
    if (liveSearchInputPengawas) {
        liveSearchInputPengawas.addEventListener('input', function() {
            clearTimeout(searchTimeoutPengawas);
            const val = this.value;
            searchTimeoutPengawas = setTimeout(function() {
                loadSearchResultsPengawas(val);
            }, 300); // Debounce 300ms
        });
    }

    // Clear search
    const clearSearchBtnPengawas = document.getElementById('clearSearchBtnPengawas');
    if (clearSearchBtnPengawas) {
        clearSearchBtnPengawas.addEventListener('click', function() {
            if (liveSearchInputPengawas) {
                liveSearchInputPengawas.value = '';
                clearSearchBtnPengawas.style.display = 'none';
                document.getElementById('searchCounterPengawas').style.display = 'none';
                loadSearchResultsPengawas('');
                liveSearchInputPengawas.focus();
            }
        });
     }
     // ===== END LIVE SEARCH PENGAWAS =====

     // ===== PAGINATION PENGAWAS - AJAX =====
     function loadTableDataPengawas(perPage = 10, page = 1) {
         const schoolId = document.getElementById('schoolSelect').value;
         if (!schoolId) return;

         fetch('{{ route("alumni.table_data_pengawas") }}?school_id=' + schoolId + '&per_page=' + perPage + '&page=' + page)
             .then(res => res.json())
             .then(data => {
                 const tbody = document.getElementById('alumniTableBodyPengawas');
                 tbody.innerHTML = data.html;
                 
                 // Update pagination container
                 const paginationContainer = document.getElementById('paginationContainerPengawas');
                 if (paginationContainer) {
                     paginationContainer.innerHTML = data.pagination;
                     // Re-attach event listeners untuk pagination links baru
                     attachPaginationListenersPengawas();
                 }
             })
             .catch(err => console.error('Error loading table data:', err));
     }

     function attachPaginationListenersPengawas() {
         // Select semua pagination links (kecuali disabled/span)
         const paginationLinks = document.querySelectorAll('#paginationContainerPengawas .pagination a.page-link');
         paginationLinks.forEach(link => {
             link.removeEventListener('click', handlePaginationClickPengawas); // Remove listener lama
             link.addEventListener('click', handlePaginationClickPengawas);
         });
     }

     function handlePaginationClickPengawas(e) {
         e.preventDefault();
         const url = new URL(this.href, window.location.origin);
         const page = url.searchParams.get('page') || 1;
         const perPage = document.getElementById('perPageSelectorPengawas').value;
         loadTableDataPengawas(perPage, page);
         // Scroll ke top tabel
         document.querySelector('.table-panel').scrollIntoView({ behavior: 'smooth' });
     }

     document.addEventListener('DOMContentLoaded', function() {
         const perPageSelectorPengawas = document.getElementById('perPageSelectorPengawas');
         if (perPageSelectorPengawas) {
             perPageSelectorPengawas.addEventListener('change', function() {
                 const perPage = this.value;
                 loadTableDataPengawas(perPage, 1); // Reset ke halaman 1
                 // Scroll ke top tabel
                 document.querySelector('.table-panel').scrollIntoView({ behavior: 'smooth' });
             });
         }

         // Attach pagination listeners saat pertama kali load
         attachPaginationListenersPengawas();
     });
     // ===== END PAGINATION PENGAWAS =====

</script>
@endsection