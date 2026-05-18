@extends('layouts.app')

@section('title', 'Laporan Kegiatan')

@push('styles')
    <style>
        .school-picker-panel {
            position: relative;
            overflow: visible;
            z-index: 20;
        }

        .school-picker {
            position: relative;
        }

        .school-picker-toggle {
            width: 100%;
            min-height: 52px;
            border: 1px solid rgba(15, 107, 125, 0.24);
            border-radius: 10px;
            background: var(--surface);
            color: var(--text-main);
            font-weight: 700;
            text-align: left;
        }

        .school-picker-menu {
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            right: 0;
            z-index: 1095;
            overflow: hidden;
            border: 1px solid rgba(15, 107, 125, 0.22);
            border-radius: 10px;
            background: var(--surface);
            box-shadow: 0 18px 36px rgba(24, 50, 58, 0.16);
        }

        .school-picker-search {
            border: 0;
            border-bottom: 1px solid rgba(15, 107, 125, 0.12);
            border-radius: 0;
            background: var(--surface-muted);
        }

        .school-picker-list {
            max-height: 260px;
            overflow-y: auto;
        }

        .school-option {
            width: 100%;
            padding: 0.95rem 1.2rem;
            border: 0;
            border-bottom: 1px solid rgba(15, 107, 125, 0.1);
            background: transparent;
            color: var(--text-main);
            font-weight: 700;
            text-align: left;
        }

        .school-option:hover,
        .school-option.active {
            background: rgba(15, 107, 125, 0.9);
            color: #fff;
        }
    </style>
@endpush

@section('content')
    @php
        $isPengawasArea = in_array(auth()->user()->role, ['pengawas', 'super_admin'], true);
    @endphp

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show py-2 small" role="alert">
            <strong>Gagal menyimpan!</strong>
            <ul class="mb-0 ps-3 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close pb-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 small" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close pb-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="hero-panel mb-4" style="overflow: visible;">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3">
            <div>
                <span class="section-kicker mb-3">
                    <span class="material-symbols-outlined" style="font-size: 1rem;">assignment</span>
                    Rekap Laporan
                </span>
                <h2 class="display-6 font-headline fw-bold mb-2">Laporan Kegiatan</h2>
                <p class="text-soft small mb-0">
                    Rekap unggahan laporan kegiatan sekolah dan capaian Tahun Ajaran {{ $currentTahunPelajaran }}.
                </p>
            </div>

        </div>
    </div>

    @if($isPengawasArea)
        <div class="content-panel school-picker-panel p-4 mb-4">
            <form action="{{ route('reports.index') }}" method="GET" id="schoolPickerForm">
                <div class="row g-3 align-items-end">
                    <div class="col-lg">
                        <label class="form-label small fw-bold text-primary">Pilih Sekolah Binaan</label>
                        <div class="school-picker">
                            <button class="school-picker-toggle d-flex align-items-center justify-content-between gap-3 px-3" type="button" id="schoolDropdownToggle">
                                <span id="schoolSelectedText">{{ optional($school)->name ?? '-- Silakan Pilih Sekolah Terlebih Dahulu --' }}</span>
                                <span class="material-symbols-outlined fs-5">expand_more</span>
                            </button>
                            <input type="hidden" name="school_id" id="schoolIdField" value="{{ optional($school)->id }}">
                            <div id="schoolDropdownMenu" class="school-picker-menu d-none">
                                <input type="text" id="schoolSearchField" class="form-control school-picker-search" placeholder="Ketik nama sekolah..." autocomplete="off">
                                <div class="school-picker-list">
                                    <button type="button" class="school-option" data-id="" data-name="-- Silakan Pilih Sekolah Terlebih Dahulu --">
                                        -- Silakan Pilih Sekolah Terlebih Dahulu --
                                    </button>
                                    @foreach($schools as $schoolOption)
                                        <button type="button" class="school-option {{ optional($school)->id === $schoolOption->id ? 'active' : '' }}" data-id="{{ $schoolOption->id }}" data-name="{{ $schoolOption->name }}">
                                            {{ $schoolOption->name }}
                                        </button>
                                    @endforeach
                                    <div id="schoolNoResult" class="px-3 py-3 text-muted small d-none">Sekolah tidak ditemukan.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-auto">
                        <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center gap-1 px-4" style="min-height: 52px;">
                            <span class="material-symbols-outlined fs-6">search</span>
                            Tampilkan Data
                        </button>
                    </div>
                </div>
            </form>
        </div>
    @endif

    @if(!$school)
        <div class="content-panel p-5 text-center">
            <div class="icon-box mx-auto mb-3">
                <span class="material-symbols-outlined">domain</span>
            </div>
            <h5 class="font-headline fw-bold mb-1">Pilih sekolah terlebih dahulu</h5>
            <p class="text-soft small mb-0">Rekap dan capaian laporan kegiatan akan tampil setelah sekolah dipilih.</p>
        </div>
    @else
        @php
            $canManageLaporan = auth()->user()->role === 'admin_sekolah' && auth()->user()->school_id == $school->id;
            $reportColspan = $canManageLaporan ? 8 : 7;
        @endphp

        <div class="card border-0 shadow-sm rounded-4 mb-4" id="laporan-kegiatan">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h5 class="font-headline fw-bold mb-1">Laporan Kegiatan - {{ $school->name }}</h5>
                    <p class="text-soft small mb-0">{{ $school->level }} &middot; {{ $school->status }}</p>
                </div>
                <div class="d-flex flex-column flex-sm-row gap-2 align-items-sm-center">
                    @if($canManageLaporan)
                        <button type="button" class="btn btn-sm btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#modalLaporan">
                            + Tambah Laporan
                        </button>
                    @endif
                    <div class="d-flex align-items-center gap-2">
                        <span class="small text-muted fw-bold d-none d-md-inline">Tampilkan</span>
                        <select id="entriesLaporan" class="form-select form-select-sm bg-light border-0 shadow-sm" style="width: auto; cursor: pointer;">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                    <div class="input-group input-group-sm shadow-sm" style="max-width: 220px;">
                        <span class="input-group-text bg-white border-end-0">
                            <span class="material-symbols-outlined fs-6 text-muted">search</span>
                        </span>
                        <input type="text" id="searchLaporan" class="form-control border-start-0 ps-0" placeholder="Cari bulan/tahun...">
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="p-4 pt-3">
                    <div class="rounded-4 border bg-primary bg-opacity-10 p-4 mb-4">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                            <div>
                                <h5 class="font-headline fw-bold mb-1">Capaian Laporan Kegiatan ( Tahun Ajaran : {{ $currentTahunPelajaran }} )</h5>
                                <p class="text-soft small mb-0">Total akumulasi laporan kegiatan yang telah diunggah untuk <strong>Tahun Ajaran {{ $currentTahunPelajaran }}</strong></p>
                            </div>
                        </div>

                        <div class="row g-3">
                            @php
                                $categories = [
                                    'kurikulum' => ['label' => 'Kurikulum', 'color' => 'primary'],
                                    'kesiswaan' => ['label' => 'Kesiswaan', 'color' => 'info'],
                                    'sarpras' => ['label' => 'Sarpras', 'color' => 'warning'],
                                    'humas' => ['label' => 'Humas', 'color' => 'success'],
                                ];
                            @endphp

                            @foreach($categories as $key => $cat)
                                @php
                                    $isFilled = $modul2Stats[$key] ?? 0;
                                @endphp
                                <div class="col-md-3">
                                    <div class="p-3 rounded-3 bg-white shadow-sm border h-100 transition-all hover-shadow">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="fw-bold small text-muted text-uppercase tracking-wider" style="font-size: 0.65rem;">{{ $cat['label'] }}</span>
                                            <span class="material-symbols-outlined fs-5 text-{{ $isFilled ? $cat['color'] : 'danger' }}">
                                                {{ $isFilled ? 'check_circle' : 'cancel' }}
                                            </span>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-{{ $isFilled > 0 ? $cat['color'] : 'danger' }} progress-bar-striped {{ $isFilled > 0 ? 'progress-bar-animated' : '' }}" role="progressbar" style="width: {{ min(($isFilled / 12) * 100, 100) }}%"></div>
                                        </div>
                                        <div class="text-end mt-1 d-flex justify-content-between align-items-center">
                                            <small class="fw-bold text-{{ $isFilled > 0 ? $cat['color'] : 'dark' }}" style="font-size: 0.75rem;">
                                                {{ $isFilled }} / 12 Laporan
                                            </small>
                                            <small class="fw-bold text-{{ $isFilled > 0 ? $cat['color'] : 'danger' }}" style="font-size: 0.7rem;">
                                                {{ $isFilled > 0 ? 'Aktif' : 'Kosong' }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light text-muted small">
                                <tr>
                                    <th>Bulan</th>
                                    <th>Tahun Pelajaran</th>
                                    <th>Semester</th>
                                    <th>Kurikulum</th>
                                    <th>Kesiswaan</th>
                                    <th>Sarpras</th>
                                    <th>Humas</th>
                                    @if($canManageLaporan)
                                        <th class="text-center">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($school->monthlyReports as $report)
                                    <tr class="laporan-row">
                                        <td class="fw-bold small text-dark">{{ \Carbon\Carbon::create()->month($report->bulan)->translatedFormat('F') }}</td>
                                        <td class="small text-muted fw-bold">{{ $report->tahun_pelajaran ?? '-' }}</td>
                                        <td class="small fw-bold text-primary">{{ $report->semester ?? '-' }}</td>
                                        <td>@if($report->kurikulum_link) <a href="{{ $report->kurikulum_link }}" target="_blank" class="badge bg-success text-decoration-none">Cek Berkas</a> @else - @endif</td>
                                        <td>@if($report->kesiswaan_link) <a href="{{ $report->kesiswaan_link }}" target="_blank" class="badge bg-success text-decoration-none">Cek Berkas</a> @else - @endif</td>
                                        <td>@if($report->sarpras_link) <a href="{{ $report->sarpras_link }}" target="_blank" class="badge bg-success text-decoration-none">Cek Berkas</a> @else - @endif</td>
                                        <td>@if($report->humas_link) <a href="{{ $report->humas_link }}" target="_blank" class="badge bg-success text-decoration-none">Cek Berkas</a> @else - @endif</td>
                                        @if($canManageLaporan)
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <button class="btn btn-link text-primary p-0" data-bs-toggle="modal" data-bs-target="#editModalLaporan{{ $report->id }}">
                                                        <span class="material-symbols-outlined fs-6">edit</span>
                                                    </button>
                                                    <form action="{{ route('school.destroy_monthly_report', $report->id) }}" method="POST" onsubmit="return confirm('Hapus laporan bulan ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-link text-danger p-0">
                                                            <span class="material-symbols-outlined fs-6">delete</span>
                                                        </button>
                                                    </form>
                                                </div>

                                                <div class="modal fade" id="editModalLaporan{{ $report->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content border-0 shadow rounded-4 text-start">
                                                            <div class="modal-header border-bottom-0">
                                                                <h1 class="modal-title fs-6 font-headline fw-bold">Edit Laporan: {{ \Carbon\Carbon::create()->month($report->bulan)->translatedFormat('F') }}</h1>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form action="{{ route('school.update_monthly_report', $report->id) }}" method="POST">
                                                                @csrf @method('PUT')
                                                                <div class="modal-body py-0">
                                                                    <div class="mb-3">
                                                                        <label class="small fw-bold">Tahun Pelajaran</label>
                                                                        <select name="tahun_pelajaran" class="form-select form-select-sm" required>
                                                                            @php $currentYear = date('Y'); @endphp
                                                                            @for($i = $currentYear - 2; $i <= $currentYear + 1; $i++)
                                                                                <option value="{{ $i }}/{{ $i+1 }}" {{ $report->tahun_pelajaran == "$i/".($i+1) ? 'selected' : '' }}>{{ $i }}/{{ $i+1 }}</option>
                                                                            @endfor
                                                                        </select>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="small fw-bold">Semester</label>
                                                                        <select name="semester" class="form-select form-select-sm" required>
                                                                            <option value="Ganjil" {{ $report->semester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                                                            <option value="Genap" {{ $report->semester == 'Genap' ? 'selected' : '' }}>Genap</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="mb-2">
                                                                        <label class="small fw-bold">Link Kurikulum</label>
                                                                        <input type="text" name="kurikulum_link" class="form-control form-control-sm" value="{{ $report->kurikulum_link }}">
                                                                    </div>
                                                                    <div class="mb-2">
                                                                        <label class="small fw-bold">Link Kesiswaan</label>
                                                                        <input type="text" name="kesiswaan_link" class="form-control form-control-sm" value="{{ $report->kesiswaan_link }}">
                                                                    </div>
                                                                    <div class="mb-2">
                                                                        <label class="small fw-bold">Link Sarpras</label>
                                                                        <input type="text" name="sarpras_link" class="form-control form-control-sm" value="{{ $report->sarpras_link }}">
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="small fw-bold">Link Humas</label>
                                                                        <input type="text" name="humas_link" class="form-control form-control-sm" value="{{ $report->humas_link }}">
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer border-top-0">
                                                                    <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">Simpan Perubahan</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr><td colspan="{{ $reportColspan }}" class="text-center small text-muted py-4">Laporan bulanan belum tersedia.</td></tr>
                                @endforelse

                                <tr id="notFoundLaporan" style="display: none;">
                                    <td colspan="{{ $reportColspan }}" class="text-center small text-muted py-3">Data tidak ditemukan.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="p-3 bg-light bg-opacity-25 border-top d-flex justify-content-between align-items-center rounded-bottom-4">
                    <small class="text-muted fw-semibold" id="laporanPageInfo">Menampilkan data...</small>
                    <nav id="laporanPagination"></nav>
                </div>
            </div>
        </div>

        @if($canManageLaporan)
            <div class="modal fade" id="modalLaporan" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content border-0 shadow rounded-4">
                        <div class="modal-header border-bottom-0">
                            <h1 class="modal-title fs-5 font-headline fw-bold">Input Laporan Bulanan</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('school.store_monthly_report', $school->id) }}" method="POST">
                            @csrf
                            <div class="modal-body p-4 pt-0">
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-primary">Tahun Pelajaran</label>
                                        <select name="tahun_pelajaran" class="form-select bg-light" required>
                                            <option value="">-- Pilih Tahun --</option>
                                            @php $currentYear = date('Y'); @endphp
                                            @for($i = $currentYear - 1; $i <= $currentYear + 1; $i++)
                                                <option value="{{ $i }}/{{ $i+1 }}">{{ $i }}/{{ $i+1 }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-primary">Semester</label>
                                        <select name="semester" class="form-select bg-light" required>
                                            <option value="">-- Pilih Semester --</option>
                                            <option value="Ganjil">Ganjil</option>
                                            <option value="Genap">Genap</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-primary">Pilih Bulan Laporan</label>
                                        <select name="bulan" class="form-select bg-light" required>
                                            <option value="">-- Pilih Bulan --</option>
                                            <option value="1">Januari</option>
                                            <option value="2">Februari</option>
                                            <option value="3">Maret</option>
                                            <option value="4">April</option>
                                            <option value="5">Mei</option>
                                            <option value="6">Juni</option>
                                            <option value="7">Juli</option>
                                            <option value="8">Agustus</option>
                                            <option value="9">September</option>
                                            <option value="10">Oktober</option>
                                            <option value="11">November</option>
                                            <option value="12">Desember</option>
                                        </select>
                                    </div>
                                </div>
                                <p class="small text-muted mb-3 border-bottom pb-2">Masukkan Tautan (Link) Google Drive untuk masing-masing bidang. Kosongkan jika belum ada.</p>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Link Laporan Kurikulum</label>
                                        <input type="text" name="kurikulum_link" class="form-control" placeholder="https://drive.google.com/...">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Link Laporan Kesiswaan</label>
                                        <input type="text" name="kesiswaan_link" class="form-control" placeholder="https://drive.google.com/...">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Link Laporan Sarpras</label>
                                        <input type="text" name="sarpras_link" class="form-control" placeholder="https://drive.google.com/...">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Link Laporan Humas</label>
                                        <input type="text" name="humas_link" class="form-control" placeholder="https://drive.google.com/...">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer bg-light border-top-0 rounded-bottom-4">
                                <button type="button" class="btn btn-outline-secondary btn-sm fw-bold" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary btn-sm fw-bold">Simpan Laporan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const schoolSearchField = document.getElementById('schoolSearchField');
            const schoolIdField = document.getElementById('schoolIdField');
            const schoolDropdownMenu = document.getElementById('schoolDropdownMenu');
            const schoolDropdownToggle = document.getElementById('schoolDropdownToggle');
            const schoolNoResult = document.getElementById('schoolNoResult');
            const schoolSelectedText = document.getElementById('schoolSelectedText');

            if (schoolSearchField && schoolIdField && schoolDropdownMenu && schoolDropdownToggle && schoolSelectedText) {
                const schoolOptions = Array.from(schoolDropdownMenu.querySelectorAll('.school-option'));

                function showSchoolDropdown() {
                    schoolDropdownMenu.classList.remove('d-none');
                }

                function hideSchoolDropdown() {
                    schoolDropdownMenu.classList.add('d-none');
                }

                function showAllSchools() {
                    schoolSearchField.value = '';

                    schoolOptions.forEach(function(option) {
                        option.classList.remove('d-none');
                    });

                    if (schoolNoResult) {
                        schoolNoResult.classList.add('d-none');
                    }

                    showSchoolDropdown();
                }

                function filterSchools() {
                    const keyword = schoolSearchField.value.toLowerCase();
                    let visibleCount = 0;

                    schoolOptions.forEach(function(option) {
                        if (!option.dataset.id) {
                            option.classList.toggle('d-none', keyword.length > 0);
                            return;
                        }

                        const isVisible = option.dataset.name.toLowerCase().includes(keyword);
                        option.classList.toggle('d-none', !isVisible);

                        if (isVisible) {
                            visibleCount++;
                        }
                    });

                    if (schoolNoResult) {
                        schoolNoResult.classList.toggle('d-none', visibleCount > 0);
                    }

                    showSchoolDropdown();
                }

                function selectSchool(option) {
                    schoolIdField.value = option.dataset.id || '';
                    schoolSelectedText.textContent = option.dataset.name;

                    schoolOptions.forEach(function(item) {
                        item.classList.toggle('active', item === option && !!option.dataset.id);
                    });

                    hideSchoolDropdown();
                }

                schoolOptions.forEach(function(option) {
                    option.addEventListener('click', function() {
                        selectSchool(this);
                    });
                });

                schoolDropdownToggle.addEventListener('click', function() {
                    if (schoolDropdownMenu.classList.contains('d-none')) {
                        showAllSchools();
                        schoolSearchField.focus();
                    } else {
                        hideSchoolDropdown();
                    }
                });

                schoolSearchField.addEventListener('input', filterSchools);

                document.addEventListener('click', function(event) {
                    const clickedToggle = schoolDropdownToggle && schoolDropdownToggle.contains(event.target);

                    if (!schoolDropdownMenu.contains(event.target)
                        && event.target !== schoolSearchField
                        && !clickedToggle) {
                        hideSchoolDropdown();
                    }
                });

                schoolDropdownToggle.form.addEventListener('submit', function(event) {
                    if (!schoolIdField.value) {
                        event.preventDefault();
                        showAllSchools();
                        schoolSearchField.focus();
                    }
                });
            }

            const rows = Array.from(document.querySelectorAll('.laporan-row'));
            const paginationControls = document.getElementById('laporanPagination');
            const pageInfo = document.getElementById('laporanPageInfo');
            const searchInput = document.getElementById('searchLaporan');
            const notFoundRow = document.getElementById('notFoundLaporan');
            const entriesSelect = document.getElementById('entriesLaporan');

            let currentPage = 1;
            let rowsPerPage = entriesSelect ? parseInt(entriesSelect.value, 10) : 5;

            if (entriesSelect) {
                entriesSelect.addEventListener('change', function(e) {
                    rowsPerPage = parseInt(e.target.value, 10);
                    currentPage = 1;
                    renderTable();
                });
            }

            if (searchInput) {
                searchInput.addEventListener('keyup', function() {
                    currentPage = 1;
                    renderTable();
                });
            }

            if (rows.length === 0) {
                if (pageInfo) {
                    pageInfo.textContent = 'Menampilkan 0 data';
                }
                return;
            }

            function renderTable() {
                const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
                const filteredRows = rows.filter(function(row) {
                    return row.textContent.toLowerCase().includes(searchTerm);
                });

                rows.forEach(function(row) {
                    row.style.display = 'none';
                });

                if (filteredRows.length === 0) {
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

                filteredRows.slice(startIndex, endIndex).forEach(function(row) {
                    row.style.display = '';
                });

                if (pageInfo) {
                    pageInfo.textContent = `Menampilkan ${startIndex + 1} - ${Math.min(endIndex, filteredRows.length)} dari total ${filteredRows.length} data`;
                }

                renderPagination(totalPages);
            }

            function renderPagination(totalPages) {
                if (!paginationControls) {
                    return;
                }

                if (totalPages <= 1) {
                    paginationControls.innerHTML = '';
                    return;
                }

                let html = '<ul class="pagination pagination-sm mb-0 shadow-sm">';
                html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><a class="page-link text-primary fw-medium" href="#" data-page="${currentPage - 1}">Prev</a></li>`;

                for (let page = 1; page <= totalPages; page++) {
                    html += `<li class="page-item ${currentPage === page ? 'active' : ''}"><a class="page-link" href="#" data-page="${page}">${page}</a></li>`;
                }

                html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><a class="page-link text-primary fw-medium" href="#" data-page="${currentPage + 1}">Next</a></li>`;
                html += '</ul>';
                paginationControls.innerHTML = html;

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

            renderTable();
        });
    </script>
@endsection
