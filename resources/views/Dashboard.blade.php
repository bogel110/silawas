@extends('layouts.app')

@section('title', 'Dashboard Utama')

@section('content')
    <div class="row align-items-end mb-5">
        <div class="col">
            <span class="text-primary fw-bold text-uppercase small tracking-widest" style="letter-spacing: 2px;">Analytical Overview</span>
            <h2 class="display-6 fw-extrabold font-headline mb-0 mt-1">Dashboard</h2>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="metric-card">
                <div class="d-flex justify-content-between mb-3">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary">
                        <span class="material-symbols-outlined">school</span>
                    </div>
                </div>
                <h3 class="font-headline fw-bold text-dark mb-0">{{ $totalSchools }}</h3>
                <small class="text-muted text-uppercase tracking-wider fw-semibold">Total Schools</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="metric-card">
                <div class="d-flex justify-content-between mb-3">
                    <div class="icon-box bg-success bg-opacity-10 text-success">
                        <span class="material-symbols-outlined">verified</span>
                    </div>
                </div>
                <h3 class="font-headline fw-bold text-dark mb-0">{{ number_format($avgCompletion, 1) }}%</h3>
                <small class="text-muted text-uppercase tracking-wider fw-semibold">Avg. Completion</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="metric-card bg-primary text-white shadow-lg shadow-sm">
                <small class="text-uppercase opacity-75 fw-bold" style="font-size: 0.6rem; letter-spacing: 1px;">Global Health Index</small>
                <h3 class="font-headline fw-bold fst-italic mb-3">Grade A-</h3>
                <div class="d-flex align-items-center gap-2">
                    <div class="progress flex-grow-1 bg-white bg-opacity-25" style="height: 6px;">
                        <div class="progress-bar bg-info" style="width: {{ $avgCompletion }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    {{-- Header Tabel dan Kontrol --}}
    <div class="p-4 bg-light bg-opacity-50 d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-3">
        <div>
            <h4 class="font-headline fw-bold mb-1">Performa Sekolaan Binaan</h4>
            <p class="text-muted small mb-0">Top ranked</p>
        </div>
        
        {{-- Kumpulan Kontrol Tabel (Entries, Search, Export) --}}
        <div class="d-flex flex-column flex-md-row gap-3 align-items-md-center">
            
            {{-- 1. Dropdown Entries Per Page --}}
            <div class="d-flex align-items-center gap-2">
                <span class="small text-muted fw-bold">Tampilkan</span>
                <select id="entriesPerPage" class="form-select form-select-sm shadow-sm" style="width: auto; cursor: pointer;">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span class="small text-muted fw-bold d-none d-sm-inline">data</span>
            </div>

            <div class="d-flex flex-column flex-sm-row gap-2">
                {{-- 2. Kotak Input Pencarian --}}
                <div class="input-group input-group-sm shadow-sm" style="max-width: 250px;">
                    <span class="input-group-text bg-white border-end-0">
                        <span class="material-symbols-outlined fs-6 text-muted">search</span>
                    </span>
                    <input type="text" id="searchSchool" class="form-control border-start-0 ps-0" placeholder="Cari nama sekolah...">
                </div>

                {{-- 3. Tombol Download Excel --}}
                <a href="{{ route('school.export') }}" class="btn btn-success btn-sm fw-bold d-flex align-items-center justify-content-center gap-1 shadow-sm">
                    <span class="material-symbols-outlined fs-6">download</span> Excel
                </a>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="schoolTable">
            <thead class="bg-light bg-opacity-25">
                <tr>
                    <th class="px-4 py-3 text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Rank</th>
                    <th class="py-3 text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">School Name</th>
                    <th class="py-3 text-center text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Score</th>
                    <th class="py-3 text-center text-uppercase fw-bold" style="font-size: 0.65rem;">Status</th>
                    <th class="py-3 text-center text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schools as $index => $school)
                <tr class="school-row">
                    <td class="px-4 fw-bold font-headline text-dark rank-number">
                        #{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div>
                                <a href="{{ route('school.show', $school->id) }}" class="text-decoration-none">
                                    <p class="mb-0 fw-bold small text-primary school-name">{{ $school->name }}</p>
                                </a>
                                <p class="mb-0 text-muted" style="font-size: 0.65rem;">{{ $school->level }} • {{ $school->status }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="fw-bold text-primary">{{ number_format($school->score, 1) }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge rounded-pill bg-{{ $school->status_color }}-subtle text-{{ $school->status_color }} text-uppercase fw-bold" style="font-size: 0.65rem; padding: 0.35rem 0.8rem;">
                            {{ $school->status_text }}
                        </span>
                    </td>
                    <td class="text-center">
                        <form action="{{ route('school.destroy', $school->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus sekolah {{ $school->name }}? Semua data terkait juga akan terhapus.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger border-0 rounded-circle p-2 d-inline-flex align-items-center justify-content-center" title="Hapus Sekolah">
                                <span class="material-symbols-outlined" style="font-size: 1.1rem;">delete</span>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr id="emptyRow">
                    <td colspan="5" class="text-center text-muted py-4">Belum ada data sekolah.</td>
                </tr>
                @endforelse
                
                <tr id="notFoundRow" style="display: none;">
                    <td colspan="5" class="text-center text-muted py-4">Sekolah yang dicari tidak ditemukan.</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div class="p-3 bg-light bg-opacity-25 border-top d-flex justify-content-between align-items-center">
        <small class="text-muted fw-semibold" id="pageInfo">Menampilkan data...</small>
        <nav id="paginationControls"></nav>
    </div>
</div>

{{-- SCRIPT PAGINATION & SEARCH --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchSchool');
        const entriesSelect = document.getElementById('entriesPerPage');
        const schoolRows = Array.from(document.querySelectorAll('.school-row')); 
        const notFoundRow = document.getElementById('notFoundRow');
        const paginationControls = document.getElementById('paginationControls');
        const pageInfo = document.getElementById('pageInfo');

        let currentPage = 1;
        let rowsPerPage = entriesSelect ? parseInt(entriesSelect.value) : 5; 

        if (entriesSelect) {
            entriesSelect.addEventListener('change', function() {
                rowsPerPage = parseInt(this.value); 
                currentPage = 1; 
                renderTable(); 
            });
        }

        function renderTable() {
            const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
            
            const filteredRows = schoolRows.filter(row => {
                const schoolName = row.querySelector('.school-name').textContent.toLowerCase();
                return schoolName.includes(searchTerm);
            });

            schoolRows.forEach(row => row.style.display = 'none');

            if (filteredRows.length === 0 && schoolRows.length > 0) {
                if(notFoundRow) notFoundRow.style.display = '';
                if(paginationControls) paginationControls.innerHTML = '';
                if(pageInfo) pageInfo.textContent = 'Menampilkan 0 data';
                return;
            } else {
                if(notFoundRow) notFoundRow.style.display = 'none';
            }

            const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
            if (currentPage > totalPages) currentPage = totalPages || 1;

            const startIndex = (currentPage - 1) * rowsPerPage;
            const endIndex = startIndex + rowsPerPage;

            const rowsToShow = filteredRows.slice(startIndex, endIndex);
            rowsToShow.forEach((row, index) => {
                row.style.display = ''; 
                
                const rankCell = row.querySelector('.rank-number');
                if(rankCell) {
                    const rankNum = startIndex + index + 1;
                    rankCell.textContent = '#' + rankNum.toString().padStart(2, '0');
                }
            });

            const endItem = Math.min(endIndex, filteredRows.length);
            if(pageInfo) pageInfo.textContent = `Menampilkan ${startIndex + 1} - ${endItem} dari total ${filteredRows.length} data`;

            renderPaginationUI(totalPages);
        }

        function renderPaginationUI(totalPages) {
            if (totalPages <= 1) {
                if(paginationControls) paginationControls.innerHTML = '';
                return;
            }

            let html = '<ul class="pagination pagination-sm mb-0 shadow-sm">';
            
            html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                        <a class="page-link text-primary fw-medium" href="#" data-page="${currentPage - 1}">Prev</a>
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
                        <a class="page-link text-primary fw-medium" href="#" data-page="${currentPage + 1}">Next</a>
                     </li>`;
            
            html += '</ul>';
            if(paginationControls) paginationControls.innerHTML = html;

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