@extends('layouts.app')

@section('title', 'Siklus & Strategi Pengawasan')

@section('content')
    <div class="mb-4">
        <h2 class="display-6 fw-extrabold font-headline mb-0">Strategi Pendampingan</h2>
        <p class="text-muted small mb-0">Manajemen pendekatan pengawasan berdasarkan skor performa sekolah.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 px-3 small mb-4 shadow-sm border-0" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close pb-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- PANEL REKAPITULASI KESELURUHAN (SELURUH SEKOLAH) --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-3 gap-2">
        <h5 class="fw-bold mb-0 text-secondary" style="font-size: 1rem;">Rekapitulasi Seluruh Sekolah Binaan</h5>
        @if($selectedSchool)
            <a href="{{ route('strategy.export', ['school_id' => $selectedSchool->id]) }}" class="btn btn-success btn-sm fw-bold d-flex align-items-center gap-1 shadow-sm">
                <span class="material-symbols-outlined fs-6">download</span> Download File Rekap
            </a>
        @else
            <button type="button" class="btn btn-success btn-sm fw-bold d-flex align-items-center gap-1 shadow-sm" disabled title="Pilih sekolah binaan terlebih dahulu">
                <span class="material-symbols-outlined fs-6">download</span> Download File Rekap
            </button>
        @endif
    </div>
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-dark h-100 d-flex justify-content-center">
                <small class="text-cyan-400 fw-bold text-uppercase tracking-widest text-white" style="font-size: 0.65rem;">Total Intervensi</small>
                <h2 class="display-5 fw-bold mb-0 text-white">{{ $recapAll['total'] }}</h2>
            </div>
        </div>
        <div class="col-12 col-md-9">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="row g-2 text-center align-items-center h-100">
                    <div class="col-4 col-md-2 border-end">
                        <h4 class="fw-bold text-primary mb-0">{{ $recapAll['seeding'] }}</h4>
                        <small class="text-muted d-block" style="font-size: 0.65rem;">Penyemaian</small>
                    </div>
                    <div class="col-4 col-md-2 border-end">
                        <h4 class="fw-bold text-danger mb-0">{{ $recapAll['rapid'] }}</h4>
                        <small class="text-muted d-block" style="font-size: 0.65rem;">Segera</small>
                    </div>
                    <div class="col-4 col-md-2 border-end">
                        <h4 class="fw-bold text-success mb-0">{{ $recapAll['reinforcing'] }}</h4>
                        <small class="text-muted d-block" style="font-size: 0.65rem;">Penguatan</small>
                    </div>
                    <div class="col-4 col-md-2 border-end">
                        <h4 class="fw-bold text-warning mb-0">{{ $recapAll['gradual'] }}</h4>
                        <small class="text-muted d-block" style="font-size: 0.65rem;">Berangsur</small>
                    </div>
                    <div class="col-4 col-md-2 border-end">
                        <h4 class="fw-bold text-info mb-0">{{ $recapAll['triggering'] }}</h4>
                        <small class="text-muted d-block" style="font-size: 0.65rem;">Pemicu</small>
                    </div>
                    <div class="col-4 col-md-2">
                        <h4 class="fw-bold text-secondary mb-0">{{ $recapAll['sustainable'] }}</h4>
                        <small class="text-muted d-block" style="font-size: 0.65rem;">Berkelanjutan</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- KOTAK FILTER: PILIH SEKOLAH TERLEBIH DAHULU --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-primary bg-opacity-10 border-start border-4 border-primary">
        <div class="card-body p-4">
            <form action="{{ request()->url() }}" method="GET" id="formPilihSekolah">
                <label class="form-label small fw-bold text-primary mb-2">Pilih Sekolah Binaan</label>
                <select name="school_id" id="schoolSelect" class="form-select border-primary" onchange="document.getElementById('formPilihSekolah').submit();">
                    <option value="">-- Ketik atau Pilih Nama Sekolah --</option>
                    @foreach($schools as $s)
                        <option value="{{ $s->id }}" {{ request('school_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->name }} - (Skor Performa: {{ $s->skor_performa ?? '0' }}%)
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    {{-- KONTEN UTAMA MUNCUL JIKA SEKOLAH SUDAH DIPILIH --}}
        {{-- BANNER PROFIL SEKOLAH & TOMBOL TAMBAH STRATEGI --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 p-4 bg-white rounded-4 shadow-sm border border-light">
            <div class="d-flex align-items-center gap-3">
                <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                    <span class="material-symbols-outlined fs-2">{{ $selectedSchool ? 'school' : 'domain' }}</span>
                </div>
                <div>
                    <h4 class="font-headline fw-bold mb-1">{{ $selectedSchool ? $selectedSchool->name : 'Rekapitulasi Seluruh Sekolah Binaan' }}</h4>
                    @if($selectedSchool)
                        @php
                            $skor = $selectedSchool->skor_performa ?? 0;
                            $badgeColor = $skor >= 75 ? 'success' : ($skor >= 50 ? 'warning' : 'danger');
                        @endphp
                        <span class="badge bg-{{ $badgeColor }} bg-opacity-10 text-{{ $badgeColor }} border border-{{ $badgeColor }} rounded-pill px-3">
                            Skor Performa: {{ $skor }}%
                        </span>
                    @else
                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary rounded-pill px-3">
                            Menampilkan data dari {{ $schools->count() }} sekolah
                        </span>
                    @endif
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2 align-items-center">

            @if($selectedSchool)
            <button type="button" class="btn btn-primary fw-bold shadow-sm px-4 py-2" data-bs-toggle="modal" data-bs-target="#modalStrategi">
                <span class="d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined fs-5">add_circle</span> Input Strategi
                </span>
            </button>
            @endif
            </div>
        </div>

        {{-- PANEL REKAPITULASI KHUSUS SEKOLAH INI --}}
        @if($selectedSchool && $recapSchool)
        <h5 class="fw-bold mb-3 text-secondary" style="font-size: 1rem;">Rekapitulasi Khusus {{ $selectedSchool->name }}</h5>
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-primary bg-opacity-10 h-100 d-flex justify-content-center border border-primary border-opacity-25">
                    <small class="text-primary fw-bold text-uppercase tracking-widest" style="font-size: 0.65rem;">Intervensi Sekolah Ini</small>
                    <h2 class="display-5 fw-bold mb-0 text-primary">{{ $recapSchool['total'] }}</h2>
                </div>
            </div>
            <div class="col-12 col-md-9">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border border-light">
                    <div class="row g-2 text-center align-items-center h-100">
                        <div class="col-4 col-md-2 border-end">
                            <h4 class="fw-bold text-primary mb-0">{{ $recapSchool['seeding'] }}</h4>
                            <small class="text-muted d-block" style="font-size: 0.65rem;">Penyemaian</small>
                        </div>
                        <div class="col-4 col-md-2 border-end">
                            <h4 class="fw-bold text-danger mb-0">{{ $recapSchool['rapid'] }}</h4>
                            <small class="text-muted d-block" style="font-size: 0.65rem;">Segera</small>
                        </div>
                        <div class="col-4 col-md-2 border-end">
                            <h4 class="fw-bold text-success mb-0">{{ $recapSchool['reinforcing'] }}</h4>
                            <small class="text-muted d-block" style="font-size: 0.65rem;">Penguatan</small>
                        </div>
                        <div class="col-4 col-md-2 border-end">
                            <h4 class="fw-bold text-warning mb-0">{{ $recapSchool['gradual'] }}</h4>
                            <small class="text-muted d-block" style="font-size: 0.65rem;">Berangsur</small>
                        </div>
                        <div class="col-4 col-md-2 border-end">
                            <h4 class="fw-bold text-info mb-0">{{ $recapSchool['triggering'] }}</h4>
                            <small class="text-muted d-block" style="font-size: 0.65rem;">Pemicu</small>
                        </div>
                        <div class="col-4 col-md-2">
                            <h4 class="fw-bold text-secondary mb-0">{{ $recapSchool['sustainable'] }}</h4>
                            <small class="text-muted d-block" style="font-size: 0.65rem;">Berkelanjutan</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif


        {{-- RIWAYAT STRATEGI: CARD LIST --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-3 px-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <h5 class="font-headline fw-bold mb-0">Riwayat Strategi</h5>
                <div class="d-flex flex-column flex-sm-row gap-2 align-items-sm-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="small text-muted fw-bold d-none d-sm-inline">Tampilkan</span>
                        <select id="entriesStrategy" class="form-select form-select-sm bg-light border-0 shadow-sm" style="width: auto; cursor: pointer;">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                        </select>
                    </div>
                    <div class="input-group input-group-sm shadow-sm" style="max-width: 200px;">
                        <span class="input-group-text bg-white border-end-0"><span class="material-symbols-outlined fs-6 text-muted">search</span></span>
                        <input type="text" id="searchStrategy" class="form-control border-start-0 ps-0" placeholder="Cari strategi...">
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div id="strategyListContainer" class="px-4 pb-4 pt-2">

                    @forelse($strategies as $str)
                    <div class="strategy-row strategy-history-card border rounded-4 p-0 mb-3 overflow-hidden">

                        {{-- Header card: Nama Sekolah + Nomor + Tombol Aksi --}}
                        <div class="strategy-history-header d-flex align-items-center justify-content-between px-4 py-3" style="border-bottom: 1px solid;">
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge bg-primary rounded-pill px-3 py-2 fs-6 fw-bold">{{ $loop->iteration }}</span>
                                <div>
                                    <div class="fw-bold text-primary" style="font-size: 0.97rem;">{{ $str->school->name ?? '-' }}</div>
                                    <small class="strategy-meta">Dibuat: {{ $str->created_at->format('d M Y') }}</small>
                                </div>
                            </div>
                            <div class="strategy-actions d-flex gap-2">
                                <button class="btn btn-sm btn-outline-primary rounded-3 d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#editModal{{ $str->id }}" title="Edit">
                                    <span class="material-symbols-outlined" style="font-size:1rem;">edit</span>
                                    <span class="d-none d-sm-inline">Edit</span>
                                </button>
                                <form action="{{ route('strategy.destroy', $str->id) }}" method="POST" onsubmit="return confirm('Hapus riwayat strategi ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-3 d-flex align-items-center gap-1" title="Hapus">
                                        <span class="material-symbols-outlined" style="font-size:1rem;">delete</span>
                                        <span class="d-none d-sm-inline">Hapus</span>
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Body card: 7 field dalam 2 kolom --}}
                        <div class="px-4 py-3">
                            <div class="row g-3">

                                {{-- Kiri: Strategi, Tahap, Metode, Jadwal --}}
                                <div class="col-12 col-lg-4 d-flex flex-column gap-3">

                                    {{-- 1. Strategi Pendampingan --}}
                                    <div>
                                        <div class="strategy-label text-uppercase fw-bold mb-1" style="font-size:0.6rem;letter-spacing:.08em;">Strategi Pendampingan</div>
                                        <span class="badge strategy-badge-dark fw-semibold px-3 py-2 rounded-pill border" style="font-size:.78rem;">
                                            {{ $str->strategy }}
                                        </span>
                                    </div>

                                    {{-- 2. Tahap Pendampingan --}}
                                    <div>
                                        <div class="strategy-label text-uppercase fw-bold mb-1" style="font-size:0.6rem;letter-spacing:.08em;">Tahap Pendampingan</div>
                                        @if($str->tahap_pendampingan)
                                            <span class="badge strategy-badge-purple fw-semibold px-3 py-2 rounded-pill border" style="font-size:.78rem;">
                                                {{ $str->tahap_pendampingan }}
                                            </span>
                                        @else
                                            <span class="strategy-meta small">-</span>
                                        @endif
                                    </div>

                                    {{-- 4. Metode Pendampingan --}}
                                    <div>
                                        <div class="strategy-label text-uppercase fw-bold mb-1" style="font-size:0.6rem;letter-spacing:.08em;">Metode Pendampingan</div>
                                        @if($str->metode_pendampingan)
                                            <span class="badge strategy-badge-info fw-semibold px-3 py-2 rounded-pill border" style="font-size:.78rem;">
                                                {{ $str->metode_pendampingan }}
                                            </span>
                                        @else
                                            <span class="strategy-meta small">-</span>
                                        @endif
                                    </div>

                                    {{-- 5. Jadwal Pendampingan --}}
                                    <div>
                                        <div class="strategy-label text-uppercase fw-bold mb-1" style="font-size:0.6rem;letter-spacing:.08em;">Jadwal Pendampingan</div>
                                        @if($str->jadwal_pendampingan)
                                            <span class="badge strategy-badge-date fw-semibold px-3 py-2 rounded-pill border" style="font-size:.78rem;">
                                                <span class="material-symbols-outlined align-middle" style="font-size:.9rem;">calendar_month</span>
                                                {{ \Carbon\Carbon::parse($str->jadwal_pendampingan)->translatedFormat('d F Y') }}
                                            </span>
                                        @else
                                            <span class="strategy-meta small">-</span>
                                        @endif
                                    </div>

                                </div>

                                {{-- Kanan: Fokus, Langkah, Output --}}
                                <div class="col-12 col-lg-8 d-flex flex-column gap-3">

                                    {{-- 3. Fokus/Sasaran Pendampingan --}}
                                    <div>
                                        <div class="strategy-label text-uppercase fw-bold mb-1" style="font-size:0.6rem;letter-spacing:.08em;">Fokus / Sasaran Pendampingan</div>
                                        <div class="strategy-value small lh-base" style="white-space:pre-line;">{{ $str->fokus_sasaran ?? '-' }}</div>
                                    </div>

                                    {{-- 6. Langkah/Rencana Aksi --}}
                                    <div>
                                        <div class="strategy-label text-uppercase fw-bold mb-1" style="font-size:0.6rem;letter-spacing:.08em;">Langkah / Rencana Aksi</div>
                                        <div class="strategy-value small lh-base" style="white-space:pre-line;">{{ $str->langkah_rencana_aksi ?? '-' }}</div>
                                    </div>

                                    {{-- 7. Output dan Indikator Keberhasilan --}}
                                    <div>
                                        <div class="strategy-label text-uppercase fw-bold mb-1" style="font-size:0.6rem;letter-spacing:.08em;">Output dan Indikator Keberhasilan</div>
                                        <div class="strategy-value small lh-base" style="white-space:pre-line;">{{ $str->output_indikator ?? '-' }}</div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Modal Edit data ini --}}
                    <div class="modal fade strategy-edit-modal" id="editModal{{ $str->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable text-start modal-lg">
                            <form action="{{ route('strategy.update', $str->id) }}" method="POST" class="modal-content border-0 shadow rounded-4">
                                @csrf @method('PUT')
                                <div class="modal-header border-bottom py-3 px-4">
                                    <div>
                                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 mb-1">Edit Data</span>
                                        <h1 class="modal-title fs-5 font-headline fw-bold mb-0">Edit Strategi Pendampingan</h1>
                                        <small class="strategy-meta">{{ $str->school->name ?? '-' }}</small>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4" style="max-height: calc(85vh - 140px); overflow-y: auto;">
                                    <div class="row g-3">
                                        <div class="col-12 col-md-6">
                                            <label class="form-label small fw-bold">1. Strategi Pendampingan</label>
                                            <select name="strategy" class="form-select" required>
                                                <option value="Penyemaian Perubahan (Seeding Change)" {{ $str->strategy == 'Penyemaian Perubahan (Seeding Change)' ? 'selected' : '' }}>1. Penyemaian</option>
                                                <option value="Perubahan Segera (Rapid Change)" {{ $str->strategy == 'Perubahan Segera (Rapid Change)' ? 'selected' : '' }}>2. Segera</option>
                                                <option value="Penguatan Perubahan (Reinforcing Change)" {{ $str->strategy == 'Penguatan Perubahan (Reinforcing Change)' ? 'selected' : '' }}>3. Penguatan</option>
                                                <option value="Perubahan Berangsur (Gradual Change)" {{ $str->strategy == 'Perubahan Berangsur (Gradual Change)' ? 'selected' : '' }}>4. Berangsur</option>
                                                <option value="Pemicu Perubahan (Triggering Change)" {{ $str->strategy == 'Pemicu Perubahan (Triggering Change)' ? 'selected' : '' }}>5. Pemicu</option>
                                                <option value="Perubahan Berkelanjutan (Sustainable Change)" {{ $str->strategy == 'Perubahan Berkelanjutan (Sustainable Change)' ? 'selected' : '' }}>6. Berkelanjutan</option>
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="form-label small fw-bold">2. Tahap Pendampingan</label>
                                            <select name="tahap_pendampingan" class="form-select">
                                                <option value="">-- Pilih --</option>
                                                <option value="Pendampingan Perencanaan Program" {{ $str->tahap_pendampingan == 'Pendampingan Perencanaan Program' ? 'selected' : '' }}>1. Pendampingan Perencanaan Program</option>
                                                <option value="Pelaksanaan Program" {{ $str->tahap_pendampingan == 'Pelaksanaan Program' ? 'selected' : '' }}>2. Pelaksanaan Program</option>
                                                <option value="Evaluasi dan Umpan Balik" {{ $str->tahap_pendampingan == 'Evaluasi dan Umpan Balik' ? 'selected' : '' }}>3. Evaluasi dan Umpan Balik</option>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small fw-bold">3. Fokus / Sasaran Pendampingan</label>
                                            <textarea name="fokus_sasaran" class="form-control" rows="2">{{ $str->fokus_sasaran }}</textarea>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="form-label small fw-bold">4. Metode Pendampingan</label>
                                            <select name="metode_pendampingan" class="form-select">
                                                <option value="">-- Pilih --</option>
                                                <option value="Training" {{ $str->metode_pendampingan == 'Training' ? 'selected' : '' }}>1. Training</option>
                                                <option value="Consulting" {{ $str->metode_pendampingan == 'Consulting' ? 'selected' : '' }}>2. Consulting</option>
                                                <option value="Mentoring" {{ $str->metode_pendampingan == 'Mentoring' ? 'selected' : '' }}>3. Mentoring</option>
                                                <option value="Coaching" {{ $str->metode_pendampingan == 'Coaching' ? 'selected' : '' }}>4. Coaching</option>
                                                <option value="Fasilitating" {{ $str->metode_pendampingan == 'Fasilitating' ? 'selected' : '' }}>5. Fasilitating</option>
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="form-label small fw-bold">5. Jadwal / Waktu Pendampingan</label>
                                            <input type="date" name="jadwal_pendampingan" class="form-control" value="{{ $str->jadwal_pendampingan ? \Carbon\Carbon::parse($str->jadwal_pendampingan)->format('Y-m-d') : '' }}">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small fw-bold">6. Langkah / Rencana Aksi</label>
                                            <textarea name="langkah_rencana_aksi" class="form-control" rows="2">{{ $str->langkah_rencana_aksi }}</textarea>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small fw-bold">7. Output dan Indikator Keberhasilan</label>
                                            <textarea name="output_indikator" class="form-control" rows="2">{{ $str->output_indikator }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer border-top py-3 px-4 d-flex justify-content-between align-items-center">
                                    <button type="button" class="btn btn-outline-secondary px-3 py-2 fw-semibold" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary px-4 py-2 fw-bold d-flex align-items-center gap-1 shadow-sm">
                                        <span class="material-symbols-outlined fs-6">save</span> Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Empty State --}}
                    <div id="emptyRow" class="{{ $strategies->isEmpty() ? '' : 'd-none' }} text-center py-5 text-muted small">
                        Belum ada strategi yang diinputkan.
                    </div>
                    <div id="notFoundRow" class="d-none text-center py-4 text-muted small">
                        Data tidak ditemukan.
                    </div>

                    @empty
                    @endforelse
                </div>

                {{-- PAGINATION FOOTER --}}
                <div class="px-4 pb-3 pt-0 d-flex justify-content-between align-items-center border-top">
                    <small class="text-muted fw-semibold" id="strategyPageInfo"></small>
                    <nav id="strategyPagination"></nav>
                </div>
            </div>
        </div>

        @if($selectedSchool)
            {{-- MODAL TAMBAH DATA BARU (SUDAH OTOMATIS TERKUNCI KE SEKOLAH YANG DIPILIH) --}}
            <div class="modal fade strategy-edit-modal" id="modalStrategi" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable text-start modal-lg">
                    <form action="{{ route('strategy.store') }}" method="POST" class="modal-content border-0 shadow rounded-4">
                        @csrf
                        {{-- KUNCI INPUTAN KE SEKOLAH YANG SEDANG DIPILIH --}}
                        <input type="hidden" name="school_id" value="{{ $selectedSchool->id }}">

                        <div class="modal-header border-bottom py-3 px-4">
                            <div>
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 mb-1">Input Data</span>
                                <h1 class="modal-title fs-5 font-headline fw-bold mb-0">Input Siklus Strategi</h1>
                                <small class="strategy-meta">{{ $selectedSchool->name }} (Skor: {{ $skor }}%)</small>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4" style="max-height: calc(85vh - 140px); overflow-y: auto;">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Sekolah Binaan</label>
                                <input type="text" class="form-control text-muted fw-bold" value="{{ $selectedSchool->name }} (Skor: {{ $skor }}%)" disabled>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">1. Strategi Pendampingan</label>
                                <select name="strategy" id="strategyInput" class="form-select" required>
                                    <option value="">-- Pilih Strategi --</option>
                                    <option value="Penyemaian Perubahan (Seeding Change)">1. Penyemaian</option>
                                    <option value="Perubahan Segera (Rapid Change)">2. Segera</option>
                                    <option value="Penguatan Perubahan (Reinforcing Change)">3. Penguatan</option>
                                    <option value="Perubahan Berangsur (Gradual Change)">4. Berangsur</option>
                                    <option value="Pemicu Perubahan (Triggering Change)">5. Pemicu</option>
                                    <option value="Perubahan Berkelanjutan (Sustainable Change)">6. Berkelanjutan</option>
                                </select>
                            </div>
                            <div class="mb-3 strategy-detail-field d-none">
                                <label class="form-label small fw-bold">2. Tahap Pendampingan</label>
                                <select name="tahap_pendampingan" id="tahapPendampinganInput" class="form-select" required disabled>
                                    <option value="">-- Pilih Tahap Pendampingan --</option>
                                    <option value="Pendampingan Perencanaan Program">1. Pendampingan Perencanaan Program</option>
                                    <option value="Pelaksanaan Program">2. Pelaksanaan Program</option>
                                    <option value="Evaluasi dan Umpan Balik">3. Evaluasi dan Umpan Balik</option>
                                </select>
                            </div>
                            <div class="mb-3 strategy-detail-field d-none">
                                <label class="form-label small fw-bold">3. Fokus/Sasaran Pendampingan</label>
                                <textarea name="fokus_sasaran" id="fokusSasaranInput" class="form-control" rows="3" required disabled></textarea>
                            </div>
                            <div class="mb-3 strategy-detail-field d-none">
                                <label class="form-label small fw-bold">4. Metode Pendampingan</label>
                                <select name="metode_pendampingan" id="metodePendampinganInput" class="form-select" required disabled>
                                    <option value="">-- Pilih Metode Pendampingan --</option>
                                    <option value="Training">1. Training</option>
                                    <option value="Consulting">2. Consulting</option>
                                    <option value="Mentoring">3. Mentoring</option>
                                    <option value="Coaching">4. Coaching</option>
                                    <option value="Fasilitating">5. Fasilitating</option>
                                </select>
                            </div>
                            <div class="mb-3 strategy-detail-field d-none">
                                <label class="form-label small fw-bold">5. Langkah/Rencana Aksi</label>
                                <textarea name="langkah_rencana_aksi" id="langkahAksiInput" class="form-control" rows="3" required disabled></textarea>
                            </div>
                            <div class="mb-3 strategy-detail-field d-none">
                                <label class="form-label small fw-bold">6. Output dan Indikator Keberhasilan</label>
                                <textarea name="output_indikator" id="outputIndikatorInput" class="form-control" rows="3" required disabled></textarea>
                            </div>
                            <div class="mb-3 strategy-detail-field d-none">
                                <label class="form-label small fw-bold">7. Jadwal/Waktu Pendampingan</label>
                                <input type="date" name="jadwal_pendampingan" id="jadwalPendampinganInput" class="form-control" required disabled>
                            </div>
                        </div>
                        <div class="modal-footer border-top py-3 px-4 d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-outline-secondary px-3 py-2 fw-semibold" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary px-4 py-2 fw-bold d-flex align-items-center gap-1 shadow-sm">
                                <span class="material-symbols-outlined fs-6">save</span> Simpan Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    {{-- ========================================================================= --}}
    {{-- SCRIPT: CHOICES.JS (Mobile-First, Pencarian Akurat & Blok Biru) --}}
    {{-- ========================================================================= --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    
    <style>
        /* Riwayat strategi mengikuti light/dark mode aplikasi */
        .strategy-history-card {
            background: var(--surface) !important;
            border-color: var(--line) !important;
            color: var(--text-main);
        }

        .strategy-history-header {
            background: linear-gradient(90deg, var(--surface-soft), var(--surface-muted)) !important;
            border-color: var(--line) !important;
        }

        .strategy-history-card .strategy-label {
            color: var(--text-soft) !important;
        }

        .strategy-history-card .strategy-value {
            color: var(--text-main) !important;
        }

        .strategy-history-card .strategy-meta {
            color: var(--text-soft) !important;
        }

        .strategy-history-card .strategy-badge-dark {
            background: rgba(128, 145, 151, .16) !important;
            color: var(--text-main) !important;
            border-color: var(--line) !important;
        }

        .strategy-history-card .strategy-badge-purple {
            background: rgba(155, 110, 220, .16) !important;
            color: #b995ed !important;
            border-color: rgba(155, 110, 220, .35) !important;
        }

        .strategy-history-card .strategy-badge-info {
            background: rgba(13, 202, 240, .14) !important;
            color: #63d8ed !important;
            border-color: rgba(13, 202, 240, .35) !important;
        }

        .strategy-history-card .strategy-badge-date {
            background: rgba(13, 110, 253, .14) !important;
            color: #76adff !important;
            border-color: rgba(13, 110, 253, .35) !important;
        }

        .strategy-edit-modal .modal-content {
            background: var(--surface) !important;
            color: var(--text-main);
        }

        .strategy-edit-modal .modal-header,
        .strategy-edit-modal .modal-footer {
            background: var(--surface-soft) !important;
            border-color: var(--line) !important;
        }

        .strategy-edit-modal .form-control,
        .strategy-edit-modal .form-select {
            background: var(--surface-muted) !important;
            color: var(--text-main) !important;
            border-color: var(--line) !important;
        }

        .strategy-edit-modal .form-control::placeholder {
            color: var(--text-soft) !important;
        }

        .strategy-edit-modal .btn-close {
            filter: none;
        }

        html[data-theme="dark"] .strategy-edit-modal .btn-close {
            filter: invert(1) grayscale(1) brightness(2);
        }

        @media (max-width: 575.98px) {
            .strategy-history-card .strategy-history-header {
                align-items: flex-start !important;
                gap: .75rem;
                flex-direction: column;
            }

            .strategy-history-card .strategy-actions {
                width: 100%;
            }

            .strategy-history-card .strategy-actions form,
            .strategy-history-card .strategy-actions button {
                flex: 1;
            }

            .strategy-edit-modal .modal-dialog {
                margin: .5rem;
            }

            .strategy-edit-modal .modal-body {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }
        }

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
            // Inisiasi Dropdown Pencarian Akurat & Besar
            const schoolSelect = document.getElementById('schoolSelect');
            if (schoolSelect) {
                new Choices(schoolSelect, { 
                    searchEnabled: true, // FITUR CARI TETAP AKTIF DI HP
                    searchPlaceholderValue: 'Ketik nama sekolah yang dicari...',
                    itemSelectText: '',
                    noResultsText: 'Sekolah tidak ditemukan',
                    noChoicesText: 'Tidak ada pilihan tersisa',
                    shouldSort: false,
                    searchFuzzy: false,       // Mematikan pencarian samar agar akurat
                    searchFields: ['label'],  // Hanya cari berdasarkan nama sekolah
                    searchResultLimit: 15,
                    placeholder: true,
                    placeholderValue: '-- Ketik atau Pilih Nama Sekolah --'
                });
            }

            // Tampilkan isian pendampingan secara bertahap setelah isian sebelumnya selesai.
            const progressiveFields = [
                { trigger: document.getElementById('strategyInput'), field: document.getElementById('tahapPendampinganInput') },
                { trigger: document.getElementById('tahapPendampinganInput'), field: document.getElementById('fokusSasaranInput') },
                { trigger: document.getElementById('fokusSasaranInput'), field: document.getElementById('metodePendampinganInput') },
                { trigger: document.getElementById('metodePendampinganInput'), field: document.getElementById('langkahAksiInput') },
                { trigger: document.getElementById('langkahAksiInput'), field: document.getElementById('outputIndikatorInput') },
                { trigger: document.getElementById('outputIndikatorInput'), field: document.getElementById('jadwalPendampinganInput') },
            ];

            progressiveFields.forEach(({ trigger, field }) => {
                if (!trigger || !field) return;

                const container = field.closest('.strategy-detail-field');
                const updateVisibility = () => {
                    const isCompleted = trigger.value.trim() !== '';
                    container.classList.toggle('d-none', !isCompleted);
                    field.disabled = !isCompleted;
                };

                trigger.addEventListener('input', updateVisibility);
                trigger.addEventListener('change', updateVisibility);
                updateVisibility();
            });

            // Inisiasi pencarian dan pagination daftar riwayat strategi.
            const strategyListContainer = document.getElementById('strategyListContainer');
            if(!strategyListContainer) return;

            let allRows = Array.from(strategyListContainer.querySelectorAll('.strategy-row'));
            const searchInput = document.getElementById('searchStrategy');
            const entriesSelect = document.getElementById('entriesStrategy');
            const pageInfo = document.getElementById('strategyPageInfo');
            const paginationNav = document.getElementById('strategyPagination');
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