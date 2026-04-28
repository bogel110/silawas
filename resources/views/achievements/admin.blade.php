@extends('layouts.app')

@section('title', 'Data Prestasi Sekolah')

@section('content')
    <div class="mb-4">
        <h2 class="display-6 fw-extrabold font-headline mb-0">Prestasi Sekolah</h2>
        <p class="text-muted small mb-0">Kelola riwayat pencapaian dan prestasi siswa maupun GTK.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success py-2 px-3 small mb-4 shadow-sm border-0">
            {{ session('success') }}
        </div>
    @endif

    {{-- KOTAK TABEL UTAMA --}}
    <div class="card border-0 shadow-sm rounded-4 mb-2">
        {{-- HEADER TABEL: Judul, Tombol, Entries, dan Search --}}
        <div class="card-header bg-white border-bottom-0 pt-4 pb-3 px-4">
            
            {{-- BARIS 1: Judul dan Tombol Aksi --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                <h5 class="font-headline fw-bold mb-0">Daftar Prestasi</h5>
                
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    {{-- Tombol Export Excel --}}
                    <a href="{{ route('achievement.export') }}" class="btn btn-success btn-sm fw-bold d-flex align-items-center gap-1 shadow-sm">
                        <span class="material-symbols-outlined fs-6">download</span> Download Excel
                    </a>
                    
                    {{-- Tombol Tambah Data --}}
                    <button type="button" class="btn btn-primary btn-sm fw-bold shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#modalPrestasi">
                        <span class="d-flex align-items-center gap-1">
                            <span class="material-symbols-outlined fs-6">add</span> Tambah Prestasi
                        </span>
                    </button>
                </div>
            </div>

            {{-- BARIS 2: Pilihan Entries (Kiri) dan Kotak Pencarian (Kanan) --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                
                {{-- Dropdown Tampil Data (Entries) --}}
                <div class="d-flex align-items-center gap-2">
                    <span class="small text-muted fw-bold d-none d-sm-inline">Tampil</span>
                    <select id="entriesAdmin" class="form-select form-select-sm bg-light border-0 shadow-sm" style="width: auto; cursor: pointer;">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span class="small text-muted fw-bold d-none d-sm-inline">Data</span>
                </div>

                {{-- Kotak Pencarian Pintar --}}
                <div class="input-group input-group-sm shadow-sm" style="max-width: 250px;">
                    <span class="input-group-text bg-light border-0"><span class="material-symbols-outlined fs-6 text-muted">search</span></span>
                    <input type="text" id="searchAdmin" class="form-control border-0 bg-light ps-0" placeholder="Cari data prestasi...">
                </div>

            </div>
        </div>

        {{-- ISI TABEL --}}
        <div class="card-body p-0">
            <div class="table-responsive p-4 pt-3">
                <table class="table table-sm align-middle mb-0">
                    <thead class="bg-light small text-muted">
                        <tr>
                            <th class="ps-2">Tanggal Perolehan</th>
                            <th>Peringkat & Tingkat</th>
                            <th>Nama Lengkap Peserta & Kategori</th>
                            <th>Keterangan Lomba</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="prestasiTableBody">
                        @forelse($achievements as $ach)
                        <tr class="prestasi-row">
                            <td class="small ps-2">{{ \Carbon\Carbon::parse($ach->tanggal)->translatedFormat('d M Y') }}</td>
                            <td >
                                <span class="fw-bold small">{{ $ach->peringkat }}</span><br>
                                <small class="text-muted">{{ $ach->tingkat }}</small>
                            </td>
                            <td>
                                {{-- PERBAIKAN: Menampilkan Nama Lengkap di Tabel --}}
                                <div class="fw-bold mb-1">{{ $ach->nama_peserta }}</div>
                                <span class="badge bg-info bg-opacity-10 text-info border border-info-subtle">{{ $ach->kategori }}</span>
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle">{{ $ach->tipe_peserta }}</span>
                            </td>
                            <td class="small text-muted">{{ $ach->keterangan }}</td>
                            
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <button type="button" class="btn btn-link text-primary p-0" data-bs-toggle="modal" data-bs-target="#editModal{{ $ach->id }}">
                                        <span class="material-symbols-outlined fs-6">edit</span>
                                    </button>
                                    
                                    <form action="{{ route('achievement.destroy', $ach->id) }}" method="POST" onsubmit="return confirm('Hapus data prestasi?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-link text-danger p-0">
                                            <span class="material-symbols-outlined fs-6">delete</span>
                                        </button>
                                    </form>
                                </div>

                                {{-- MODAL EDIT PRESTASI --}}
                                <div class="modal fade text-start" id="editModal{{ $ach->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <form action="{{ route('achievement.update', $ach->id) }}" method="POST" class="modal-content border-0 shadow">
                                            @csrf 
                                            @method('PUT')
                                            
                                            <div class="modal-header pt-4 px-4 border-0">
                                                <div>
                                                    <span class="badge bg-warning bg-opacity-10 text-warning mb-2">Edit Data</span>
                                                    <h4 class="modal-title font-headline fw-bold mb-0">Edit Prestasi Sekolah</h4>
                                                </div>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            
                                            <div class="modal-body px-4">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="small fw-bold text-muted mb-1">Tanggal Perolehan</label>
                                                        <input type="date" name="tanggal" class="form-control bg-light border-0" value="{{ $ach->tanggal }}" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="small fw-bold text-muted mb-1">Peringkat</label>
                                                        <select name="peringkat" class="form-select bg-light border-0" required>
                                                            <option value="Juara 1" {{ $ach->peringkat == 'Juara 1' ? 'selected' : '' }}>Juara 1</option>
                                                            <option value="Juara 2" {{ $ach->peringkat == 'Juara 2' ? 'selected' : '' }}>Juara 2</option>
                                                            <option value="Juara 3" {{ $ach->peringkat == 'Juara 3' ? 'selected' : '' }}>Juara 3</option>
                                                            <option value="Juara Harapan 1" {{ $ach->peringkat == 'Juara Harapan 1' ? 'selected' : '' }}>Juara Harapan 1</option>
                                                            <option value="Juara Harapan 2" {{ $ach->peringkat == 'Juara Harapan 2' ? 'selected' : '' }}>Juara Harapan 2</option>
                                                            <option value="Juara Harapan 3" {{ $ach->peringkat == 'Juara Harapan 3' ? 'selected' : '' }}>Juara Harapan 3</option>
                                                            <option value="Juara Favorit" {{ $ach->peringkat == 'Juara Favorit' ? 'selected' : '' }}>Juara Favorit</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="small fw-bold text-muted mb-1">Tingkat</label>
                                                        <select name="tingkat" class="form-select bg-light border-0" required>
                                                            <option value="Kota/Kabupaten" {{ $ach->tingkat == 'Kota/Kabupaten' ? 'selected' : '' }}>Kota/Kabupaten</option>
                                                            <option value="Provinsi" {{ $ach->tingkat == 'Provinsi' ? 'selected' : '' }}>Provinsi</option>
                                                            <option value="Nasional" {{ $ach->tingkat == 'Nasional' ? 'selected' : '' }}>Nasional</option>
                                                            <option value="Internasional" {{ $ach->tingkat == 'Internasional' ? 'selected' : '' }}>Internasional</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="small fw-bold text-muted mb-1">Kategori</label>
                                                        <select name="kategori" class="form-select bg-light border-0" required>
                                                            <option value="Individu" {{ $ach->kategori == 'Individu' ? 'selected' : '' }}>Individu</option>
                                                            <option value="Tim" {{ $ach->kategori == 'Tim' ? 'selected' : '' }}>Tim/Kelompok</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="small fw-bold text-muted mb-1">Tipe Peserta</label>
                                                        <select name="tipe_peserta" class="form-select bg-light border-0" required>
                                                            <option value="Siswa" {{ $ach->tipe_peserta == 'Siswa' ? 'selected' : '' }}>Siswa</option>
                                                            <option value="Guru" {{ $ach->tipe_peserta == 'Guru' ? 'selected' : '' }}>Guru</option>
                                                            <option value="Tendik" {{ $ach->tipe_peserta == 'Tendik' ? 'selected' : '' }}>Tendik</option>
                                                        </select>
                                                    </div>

                                                    {{-- PERBAIKAN: Input Edit Nama Peserta --}}
                                                    <div class="col-12">
                                                        <label class="small fw-bold text-muted mb-1">Nama Lengkap (Peraih Juara)</label>
                                                        <input type="text" name="nama_peserta" class="form-control bg-light border-0" value="{{ $ach->nama_peserta }}" placeholder="Contoh: Andi Wijaya" required>
                                                    </div>

                                                    <div class="col-12">
                                                        <label class="small fw-bold text-muted mb-1">Keterangan / Nama Lomba</label>
                                                        <textarea name="keterangan" class="form-control bg-light border-0" rows="3" required>{{ $ach->keterangan }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="modal-footer bg-light border-0 px-4 py-3 rounded-bottom-4">
                                                <button type="button" class="btn btn-outline-secondary btn-sm fw-bold" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary btn-sm fw-bold px-4">Update Data</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr id="emptyRow"><td colspan="5" class="text-center py-5 text-muted small">Belum ada data prestasi.</td></tr>
                        @endforelse
                        <tr id="notFoundRow" style="display:none;"><td colspan="5" class="text-center py-4 text-muted small">Data tidak ditemukan.</td></tr>
                    </tbody>
                </table>
            </div>
            
            {{-- FOOTER PAGINASI --}}
            <div class="p-3 bg-light bg-opacity-25 border-top d-flex justify-content-between align-items-center rounded-bottom-4">
                <small class="text-muted fw-semibold" id="pageInfo"></small>
                <nav id="paginationNav"></nav>
            </div>
        </div>
    </div>

    {{-- MODAL DATA ENTRY (TAMBAH) --}}
    <div class="modal fade" id="modalPrestasi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form action="{{ route('achievement.store') }}" method="POST" class="modal-content border-0 shadow">
                @csrf
                <div class="modal-header pt-4 px-4 border-0">
                    <div>
                        <span class="badge bg-primary bg-opacity-10 text-primary mb-2">Input Baru</span>
                        <h4 class="modal-title font-headline fw-bold mb-0">Tambah Prestasi Sekolah</h4>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted mb-1">Tanggal Perolehan</label>
                            <input type="date" name="tanggal" class="form-control bg-light border-0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted mb-1">Peringkat</label>
                            <select name="peringkat" class="form-select bg-light border-0" required>
                                <option value="">-- Pilih Juara--</option>
                                <option value="Juara 1">Juara 1</option>
                                <option value="Juara 2">Juara 2</option>
                                <option value="Juara 3">Juara 3</option>
                                <option value="Juara Harapan 1">Juara Harapan 1</option>
                                <option value="Juara Harapan 2">Juara Harapan 2</option>
                                <option value="Juara Harapan 3">Juara Harapan 3</option>
                                <option value="Juara Favorit">Juara Favorit</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold text-muted mb-1">Tingkat</label>
                            <select name="tingkat" class="form-select bg-light border-0" required>
                                <option value="">-- Pilih Tingkat --</option>
                                <option value="Kota/Kabupaten">Kota/Kabupaten</option>
                                <option value="Provinsi">Provinsi</option>
                                <option value="Nasional">Nasional</option>
                                <option value="Internasional">Internasional</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold text-muted mb-1">Kategori</label>
                            <select name="kategori" class="form-select bg-light border-0" required>
                                <option value="">-- Pilih Kategori --</option>
                                <option value="Individu">Individu</option>
                                <option value="Tim">Tim/Kelompok</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold text-muted mb-1">Tipe Peserta</label>
                            <select name="tipe_peserta" class="form-select bg-light border-0" required>
                                <option value="">-- Pilih Tipe Peserta --</option>
                                <option value="Siswa">Siswa</option>
                                <option value="Guru">Guru</option>
                                <option value="Tendik">Tendik</option>
                            </select>
                        </div>

                        {{-- PERBAIKAN: Input Tambah Nama Peserta --}}
                        <div class="col-12">
                            <label class="small fw-bold text-muted mb-1">Nama Lengkap (Peraih Juara)</label>
                            <input type="text" name="nama_peserta" class="form-control bg-light border-0" placeholder="Contoh: Andi Wijaya / Tim Basket" required>
                        </div>

                        <div class="col-12">
                            <label class="small fw-bold text-muted mb-1">Keterangan / Nama Lomba</label>
                            <textarea name="keterangan" class="form-control bg-light border-0" rows="3" placeholder="Contoh: Lomba Pidato Bahasa Inggris Tingkat Provinsi" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 px-4 py-3 rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold px-4">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    {{-- SCRIPT: SEARCHING & PAGINATION --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tableBody = document.getElementById('prestasiTableBody');
            if(!tableBody) return;

            let allRows = Array.from(document.querySelectorAll('.prestasi-row'));
            const searchInput = document.getElementById('searchAdmin');
            const entriesSelect = document.getElementById('entriesAdmin');
            const pageInfo = document.getElementById('pageInfo');
            const paginationNav = document.getElementById('paginationNav');
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