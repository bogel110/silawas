@extends('layouts.app')

@section('title', 'Siklus & Strategi Pengawasan')

@section('content')
    <div class="mb-4 d-flex justify-content-between align-items-end">
        <div>
            <h2 class="display-6 fw-extrabold font-headline mb-0">Siklus & Strategi</h2>
            <p class="text-muted small mb-0">Manajemen pendekatan pengawasan berdasarkan skor performa sekolah.</p>
        </div>
        <button type="button" class="btn btn-primary fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalStrategi">
            + Tambah Siklus Strategi
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 small mb-4 shadow-sm" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close pb-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ========================================== --}}
    {{-- PANEL REKAPITULASI --}}
    {{-- ========================================== --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-dark h-100 d-flex justify-content-center">
                <small class="text-cyan-400 fw-bold text-uppercase tracking-widest" style="font-size: 0.65rem;">Total Intervensi</small>
                <h2 class="display-5 fw-bold mb-0 text-white">{{ $recap['total'] }}</h2>
            </div>
        </div>
        <div class="col-12 col-md-9">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="row g-2 text-center align-items-center h-100">
                    <div class="col-4 col-md-2 border-end">
                        <h4 class="fw-bold text-primary mb-0">{{ $recap['seeding'] }}</h4>
                        <small class="text-muted d-block" style="font-size: 0.65rem;">Penyemaian</small>
                    </div>
                    <div class="col-4 col-md-2 border-end">
                        <h4 class="fw-bold text-danger mb-0">{{ $recap['rapid'] }}</h4>
                        <small class="text-muted d-block" style="font-size: 0.65rem;">Segera</small>
                    </div>
                    <div class="col-4 col-md-2 border-end">
                        <h4 class="fw-bold text-success mb-0">{{ $recap['reinforcing'] }}</h4>
                        <small class="text-muted d-block" style="font-size: 0.65rem;">Penguatan</small>
                    </div>
                    <div class="col-4 col-md-2 border-end">
                        <h4 class="fw-bold text-warning mb-0">{{ $recap['gradual'] }}</h4>
                        <small class="text-muted d-block" style="font-size: 0.65rem;">Berangsur</small>
                    </div>
                    <div class="col-4 col-md-2 border-end">
                        <h4 class="fw-bold text-info mb-0">{{ $recap['triggering'] }}</h4>
                        <small class="text-muted d-block" style="font-size: 0.65rem;">Pemicu</small>
                    </div>
                    <div class="col-4 col-md-2">
                        <h4 class="fw-bold text-secondary mb-0">{{ $recap['sustainable'] }}</h4>
                        <small class="text-muted d-block" style="font-size: 0.65rem;">Berkelanjutan</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- TABEL DATA STRATEGI --}}
    {{-- ========================================== --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <h5 class="font-headline fw-bold mb-0">Riwayat Penentuan Strategi</h5>
            
            <div class="d-flex flex-column flex-sm-row gap-2 align-items-sm-center">
            
                {{-- DROPDOWN ENTRIES (PAGINATION) --}}
                <div class="d-flex align-items-center gap-2">
                    <span class="small text-muted fw-bold d-none d-md-inline">Tampil</span>
                    <select id="entriesStrategy" class="form-select form-select-sm bg-light border-0 shadow-sm" style="width: auto; cursor: pointer;">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
                {{-- KOTAK PENCARIAN --}}
                <div class="input-group input-group-sm shadow-sm" style="max-width: 200px;">
                    <span class="input-group-text bg-white border-end-0"><span class="material-symbols-outlined fs-6 text-muted">search</span></span>
                    <input type="text" id="searchStrategy" class="form-control border-start-0 ps-0" placeholder="Cari data...">
                </div>
                {{-- TOMBOL EXCEL --}}
                <a href="{{ route('strategy.export') }}" class="btn btn-success btn-sm fw-bold d-flex align-items-center justify-content-center gap-1 shadow-sm" title="Download Laporan">
                    <span class="material-symbols-outlined fs-6">download</span> Download Excel
                </a>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="p-4 pt-3 table-responsive">
                <table class="table table-sm align-middle mb-0" id="strategyTable">
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
                            <td class="text-primary fw-bold">{{ $str->school->name }}</td>
                            <td><span class="badge bg-dark bg-opacity-10 text-dark border border-dark">{{ $str->strategy }}</span></td>
                            <td class="text-muted" style="max-width: 300px; white-space: normal;">{{ $str->keterangan ?? '-' }}</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-link text-primary p-0" data-bs-toggle="modal" data-bs-target="#editModal{{ $str->id }}" title="Edit Data"><span class="material-symbols-outlined fs-6">edit</span></button>
                                    <form action="{{ route('strategy.destroy', $str->id) }}" method="POST" onsubmit="return confirm('Hapus riwayat strategi ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-link text-danger p-0" title="Hapus Data"><span class="material-symbols-outlined fs-6">delete</span></button>
                                    </form>
                                </div>

                                {{-- MODAL EDIT --}}
                                <div class="modal fade" id="editModal{{ $str->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow rounded-4 text-start">
                                            <form action="{{ route('strategy.update', $str->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <div class="modal-header border-bottom-0">
                                                    <h1 class="modal-title fs-5 font-headline fw-bold">Edit Strategi</h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4 pt-0">
                                                    <div class="mb-3">
                                                        <label class="small fw-bold">Sekolah (Skor Performa)</label>
                                                        <input type="text" class="form-control bg-light" value="{{ $str->school->name }}" disabled>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="small fw-bold">Strategi Pendampingan</label>
                                                        <select name="strategy" class="form-select" required>
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
                                                        <textarea name="keterangan" class="form-control" rows="3">{{ $str->keterangan }}</textarea>
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
                        <tr id="notFoundRow" style="display: none;"><td colspan="5" class="text-center small text-muted py-3">Data tidak ditemukan.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- MODAL TAMBAH DATA BARU --}}
    {{-- ========================================== --}}
    <div class="modal fade" id="modalStrategi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4 text-start">
                <form action="{{ route('strategy.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom-0">
                        <h1 class="modal-title fs-5 font-headline fw-bold">Input Siklus Strategi</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 pt-0">
                        <div class="mb-3">
                            <label class="small fw-bold text-primary">Pilih Sekolah & Skor Performa</label>
                            <select name="school_id" class="form-select border-primary" required>
                                <option value="">-- Pilih Sekolah --</option>
                                @foreach($schools as $s)
                                    {{-- GANTI "skor_performa" DENGAN NAMA KOLOM ASLI DI DATABASE ANDA JIKA BERBEDA --}}
                                    <option value="{{ $s->id }}">{{ $s->name }} - (Skor: {{ $s->skor_performa ?? '0'}}%)</option>
                                @endforeach
                            </select>
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

    {{-- SIMPLE SEARCH SCRIPT --}}
    <script>
        document.getElementById('searchStrategy')?.addEventListener('keyup', function() {
            const term = this.value.toLowerCase();
            const rows = document.querySelectorAll('.strategy-row');
            let hasVisible = false;
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if(text.includes(term)) {
                    row.style.display = '';
                    hasVisible = true;
                } else {
                    row.style.display = 'none';
                }
            });
            document.getElementById('notFoundRow').style.display = hasVisible || rows.length === 0 ? 'none' : '';
            if(document.getElementById('emptyRow')) document.getElementById('emptyRow').style.display = term ? 'none' : '';
        });
    </script>
@endsection