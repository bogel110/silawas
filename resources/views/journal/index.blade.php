@extends('layouts.app')

@section('title', 'Jurnal Kepala Sekolah')

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

    {{-- TAMPILAN DATA JURNAL (Jika Sekolah Sudah Dipilih / Login sbg Admin) --}}
    @if($selectedSchool)
        
        {{-- PANEL REKAPITULASI
        @if($selectedSchool->attendances->count() > 0)
            @php
                $totalDays = $selectedSchool->attendances->count();
                $avgSiswa = round($selectedSchool->attendances->avg('siswa_hadir'));
                $avgGuru = round($selectedSchool->attendances->avg('guru_hadir'));
                $persenKepsek = round(($selectedSchool->attendances->where('kepsek_hadir', 1)->count() / $totalDays) * 100);
            @endphp
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-bottom border-4 border-dark">
                        <small class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem;">Total Jurnal</small>
                        <h4 class="fw-bold mb-0 text-dark">{{ $totalDays }} Hari</h4>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-bottom border-4 border-primary">
                        <small class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem;">Rata-rata Siswa</small>
                        <h4 class="fw-bold mb-0 text-primary">{{ $avgSiswa }} Siswa</h4>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-bottom border-4 border-success">
                        <small class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem;">Rata-rata Guru</small>
                        <h4 class="fw-bold mb-0 text-success">{{ $avgGuru }} Guru</h4>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-bottom border-4 border-info">
                        <small class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem;">Kehadiran Kepsek</small>
                        <h4 class="fw-bold mb-0 text-info">{{ $persenKepsek }}%</h4>
                    </div>
                </div>
            </div>
        @endif --}}

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
                        <span class="material-symbols-outlined fs-6">download</span> Excel
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
            <div class="modal fade" id="modalAbsensi" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow rounded-4">
                        <div class="modal-header border-bottom-0">
                            <h1 class="modal-title fs-5 font-headline fw-bold">Input Jurnal Kepsek</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('school.store_attendance', $selectedSchool->id) }}" method="POST">
                            @csrf
                            <div class="modal-body p-4 pt-0">
                                <p class="small text-muted mb-4">Silakan masukkan data kehadiran & jurnal untuk hari ini ({{ now()->format('d M Y') }}).</p>
                                
                                <div class="row g-3 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Siswa Hadir</label>
                                        <input type="number" name="siswa_hadir" class="form-control" placeholder="Contoh: 360" required min="0">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Guru Hadir</label>
                                        <input type="number" name="guru_hadir" class="form-control" placeholder="Contoh: 50" required min="0">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Kehadiran Kepsek</label>
                                    <select name="kepsek_hadir" class="form-select" required>
                                        <option value="1">Hadir (Ada di tempat)</option>
                                        <option value="0">Tidak Hadir (Dinas Luar / Izin)</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Tupoksi Kepsek</label>
                                    <select name="tupoksi" class="form-select" required>
                                        <option value="">-- Pilih Tupoksi --</option>
                                        <option value="Manajerial">1. Manajerial</option>
                                        <option value="Educator">2. Educator</option>
                                        <option value="Supervisor">3. Supervisor</option>
                                        <option value="Leader">4. Leader</option>
                                        <option value="Entrepreneur">5. Entrepreneur</option>
                                        <option value="Pengelola Sistem Informasi">6. Pengelola Sistem Informasi</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Keterangan</label>
                                    <textarea name="keterangan" class="form-control" rows="2"></textarea>
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
@endsection