@extends('layouts.app')

@section('title', 'Pengawas Binaan')

@push('styles')
    <style>
        .assignment-school-grid {
            display: flex;
            flex-direction: column;
            gap: 0.55rem;
            max-height: 360px;
            overflow-y: auto;
            padding-right: 0.35rem;
        }

        .assignment-school-option {
            min-height: 58px;
            border: 1px solid rgba(15, 107, 125, 0.14);
            border-radius: 10px;
            background: var(--surface);
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .assignment-school-option:has(input:checked) {
            border-color: rgba(13, 110, 253, 0.45);
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.08);
        }

        .assigned-school-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .assigned-school-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.5rem 0.7rem;
            border: 1px solid rgba(15, 107, 125, 0.14);
            border-radius: 10px;
            background: var(--surface);
            color: var(--text-main);
            font-weight: 700;
            font-size: 0.82rem;
        }

        .assigned-school-count {
            min-width: 132px;
        }

        .assigned-school-modal-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
            gap: 0.65rem;
            max-height: 420px;
            overflow-y: auto;
            padding-right: 0.25rem;
        }

        .pengawas-binaan-table {
            table-layout: fixed;
        }

        .pengawas-binaan-table th,
        .pengawas-binaan-table td {
            vertical-align: middle;
            padding-top: 1.25rem;
            padding-bottom: 1.25rem;
        }

        .pengawas-binaan-table .col-pengawas {
            width: 34%;
            text-align: center;
        }

        .pengawas-binaan-table .col-binaan {
            width: 34%;
            text-align: center;
        }

        .pengawas-binaan-table .col-aksi {
            width: 24%;
            text-align: center;
        }

        .pengawas-binaan-table .col-no {
            width: 6%;
            text-align: center;
        }

        .pengawas-binaan-table .pengawas-cell {
            padding-top: 1.35rem;
            padding-bottom: 1.35rem;
        }

        .pengawas-binaan-table .binaan-cell,
        .pengawas-binaan-table .aksi-cell {
            padding-top: 1.35rem;
            padding-bottom: 1.35rem;
        }

        .pengawas-profile {
            max-width: 440px;
            margin-inline: auto;
        }

        .pengawas-profile .pengawas-email {
            overflow-wrap: anywhere;
        }

        .binaan-cell,
        .aksi-cell {
            text-align: center;
        }

        .binaan-cell > *,
        .aksi-cell > * {
            margin-inline: auto;
        }

        .assignment-toolbar {
            border: 1px solid rgba(15, 107, 125, 0.12);
            border-radius: 12px;
            background: var(--surface-muted);
        }

        .assignment-empty {
            display: none;
            border: 1px dashed rgba(15, 107, 125, 0.25);
            border-radius: 12px;
            background: var(--surface);
        }

        .assignment-form.has-no-visible-schools .assignment-empty {
            display: block;
        }
    </style>
@endpush

@section('content')
    <div class="hero-panel mb-4">
        <div class="row g-4 align-items-center">
            <div class="col-lg-8">
                <span class="section-kicker mb-3">
                    <span class="material-symbols-outlined" style="font-size: 1rem;">supervisor_account</span>
                    Super Admin
                </span>
                <h2 class="display-6 font-headline fw-bold mb-2">Pengawas dan Sekolah Binaan</h2>
                <p class="text-soft small mb-0">Atur sekolah yang boleh dipantau oleh setiap akun pengawas.</p>
            </div>
            <div class="col-lg-4">
                <div class="content-panel p-4 h-100">
                    <div class="eyebrow-muted mb-2">Ringkasan</div>
                    <div class="d-flex justify-content-between align-items-end gap-3">
                        <div>
                            <h3 class="font-headline fw-bold mb-1">{{ $pengawas->count() }}</h3>
                            <p class="text-soft small mb-0">Akun pengawas aktif</p>
                        </div>
                        <div class="icon-box">
                            <span class="material-symbols-outlined">groups</span>
                        </div>
                    </div>
                    <div class="border-top mt-3 pt-3" style="border-color: rgba(15, 107, 125, 0.08) !important;">
                        <div class="fw-bold">{{ $schools->count() }}</div>
                        <div class="text-soft small">Sekolah tersedia untuk ditugaskan</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 px-3 small mb-4 shadow-sm border-0" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="table-panel overflow-hidden">
        <div class="table-panel-header d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div>
                <div class="eyebrow-muted mb-2">Penugasan</div>
                <h4 class="font-headline fw-bold mb-1">Daftar Pengawas</h4>
                <p class="text-soft small mb-0">Tabel ini hanya menampilkan sekolah binaan yang sudah ditetapkan.</p>
            </div>
            <div class="input-group input-group-sm" style="max-width: 320px;">
                <span class="input-group-text bg-white border-end-0">
                    <span class="material-symbols-outlined fs-6">search</span>
                </span>
                <input type="text" id="searchPengawas" class="form-control border-start-0 ps-0" placeholder="Cari pengawas atau sekolah...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0 pengawas-binaan-table" id="pengawasTable">
                <thead>
                    <tr>
                        <th class="col-no">No</th>
                        <th class="col-pengawas text-center">Pengawas</th>
                        <th class="col-binaan">Sekolah Binaan</th>
                        <th class="col-aksi">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengawas as $item)
                        @php
                            $selectedSchoolIds = $item->supervisedSchools->pluck('id')->all();
                            $assignedNames = $item->supervisedSchools->pluck('name')->implode(' ');
                        @endphp
                        <tr class="pengawas-row" data-search="{{ strtolower($item->name . ' ' . $item->email . ' ' . $assignedNames) }}">
                            <td class="text-center fw-bold text-soft">
                                {{ $loop->iteration }}
                            </td>
                            <td class="pengawas-cell text-center">
                                <div class="pengawas-profile text-center">
                                    <div class="fw-bold text-dark">{{ $item->name }}</div>
                                    <div class="text-soft small pengawas-email">{{ $item->email }}</div>
                                </div>
                            </td>
                            <td class="binaan-cell">
                                <button type="button" class="btn btn-info btn-sm rounded-pill fw-bold assigned-school-count" data-bs-toggle="modal" data-bs-target="#modalDaftarBinaan{{ $item->id }}">
                                    {{ $item->supervisedSchools->count() }} Sekolah Binaan
                                </button>
                            </td>
                            <td class="aksi-cell">
                                <button type="button" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-1 px-3" data-bs-toggle="modal" data-bs-target="#modalAturBinaan{{ $item->id }}">
                                    <span class="material-symbols-outlined fs-6">tune</span>
                                    Atur
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-soft py-5">Belum ada akun pengawas.</td>
                        </tr>
                    @endforelse
                    <tr id="notFoundPengawasRow" style="display: none;">
                        <td colspan="4" class="text-center text-soft py-5">Data pengawas tidak ditemukan.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    @foreach($pengawas as $item)
        @php
            $selectedSchoolIds = $item->supervisedSchools->pluck('id')->all();
        @endphp
        <div class="modal fade" id="modalDaftarBinaan{{ $item->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                <div class="modal-content border-0 shadow rounded-4">
                    <div class="modal-header border-bottom-0 align-items-start">
                        <div>
                            <div class="eyebrow-muted mb-2">Sekolah Binaan</div>
                            <h5 class="modal-title font-headline fw-bold">{{ $item->name }}</h5>
                            <div class="text-soft small mt-1">{{ $item->supervisedSchools->count() }} sekolah binaan terdaftar</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-0">
                        @if($item->supervisedSchools->isNotEmpty())
                            <div class="assigned-school-modal-list">
                                @foreach($item->supervisedSchools->sortBy('name') as $school)
                                    <span class="assigned-school-pill">
                                        <span class="material-symbols-outlined fs-6">school</span>
                                        {{ $school->name }}
                                        <span class="text-soft fw-semibold">({{ $school->level }} &middot; {{ $school->status }})</span>
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-soft small p-4">
                                Belum ada sekolah binaan untuk pengawas ini.
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer border-top-0 bg-light rounded-bottom-4">
                        <button type="button" class="btn btn-outline-secondary btn-sm fw-bold" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalAturBinaan{{ $item->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
                <form class="modal-content border-0 shadow rounded-4 assignment-form" data-pengawas-id="{{ $item->id }}" action="{{ route('super-admin.pengawas-binaan.update', $item->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header border-bottom-0 align-items-start">
                        <div>
                            <div class="eyebrow-muted mb-2">Pengaturan</div>
                            <h5 class="modal-title font-headline fw-bold">Atur Sekolah Binaan</h5>
                            <div class="text-soft small mt-1">{{ $item->name }} &middot; {{ $item->email }}</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body pt-0">
                        <div class="assignment-toolbar p-3 mb-3">
                            <div class="row g-2 align-items-end">
                                <div class="col-lg">
                                    <label class="form-label small fw-bold text-primary">Cari Sekolah</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0">
                                            <span class="material-symbols-outlined fs-6">search</span>
                                        </span>
                                        <input type="text" class="form-control border-start-0 assignment-school-search" placeholder="Ketik nama sekolah...">
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-2">
                                    <label class="form-label small fw-bold text-primary">Level</label>
                                    <select class="form-select assignment-level-filter">
                                        <option value="">Semua</option>
                                        @foreach($schools->pluck('level')->filter()->unique()->sort()->values() as $level)
                                            <option value="{{ strtolower($level) }}">{{ $level }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-6 col-lg-2">
                                    <label class="form-label small fw-bold text-primary">Status</label>
                                    <select class="form-select assignment-status-filter">
                                        <option value="">Semua</option>
                                        @foreach($schools->pluck('status')->filter()->unique()->sort()->values() as $status)
                                            <option value="{{ strtolower($status) }}">{{ $status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2 mt-3">
                                <div class="text-soft small assignment-count-text">
                                    {{ count($selectedSchoolIds) }} dipilih dari {{ $schools->count() }} sekolah
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-outline-primary btn-sm assignment-check-visible">
                                        Pilih hasil tampil
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm assignment-uncheck-visible">
                                        Hapus pilihan tampil
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="assignment-school-grid">
                            @forelse($schools as $school)
                                <label class="assignment-school-option d-flex align-items-center gap-2 p-3" data-name="{{ strtolower($school->name) }}" data-level="{{ strtolower($school->level) }}" data-status="{{ strtolower($school->status) }}">
                                    <input class="form-check-input mt-1" type="checkbox" name="school_ids[]" value="{{ $school->id }}" {{ in_array($school->id, $selectedSchoolIds) ? 'checked' : '' }}>
                                    <span class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-1 flex-grow-1">
                                        <span class="fw-semibold small text-dark">{{ $school->name }}</span>
                                        <span class="text-soft" style="font-size: 0.75rem;">{{ $school->level }} &middot; {{ $school->status }}</span>
                                    </span>
                                </label>
                            @empty
                                <div class="text-soft small py-3">Belum ada sekolah yang dapat ditugaskan.</div>
                            @endforelse
                        </div>
                        <div class="assignment-empty text-center text-soft small p-4 mt-3">
                            Sekolah tidak ditemukan pada filter ini.
                        </div>
                    </div>

                    <div class="modal-footer border-top-0 bg-light rounded-bottom-4">
                        <button type="button" class="btn btn-outline-secondary btn-sm fw-bold" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm fw-bold px-4">
                            <span class="material-symbols-outlined fs-6 align-middle">save</span>
                            Simpan Sekolah Binaan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchPengawas');
            const rows = Array.from(document.querySelectorAll('.pengawas-row'));
            const notFoundRow = document.getElementById('notFoundPengawasRow');
            const assignmentForms = Array.from(document.querySelectorAll('.assignment-form'));

            function refreshAssignmentForm(form) {
                const keyword = (form.querySelector('.assignment-school-search')?.value || '').toLowerCase();
                const level = form.querySelector('.assignment-level-filter')?.value || '';
                const status = form.querySelector('.assignment-status-filter')?.value || '';
                const options = Array.from(form.querySelectorAll('.assignment-school-option'));
                let visibleCount = 0;
                let checkedCount = 0;

                options.forEach(function(option) {
                    const checkbox = option.querySelector('input[type="checkbox"]');
                    const matchesKeyword = option.dataset.name.includes(keyword);
                    const matchesLevel = !level || option.dataset.level === level;
                    const matchesStatus = !status || option.dataset.status === status;
                    const isVisible = matchesKeyword && matchesLevel && matchesStatus;

                    option.classList.toggle('d-none', !isVisible);

                    if (isVisible) {
                        visibleCount++;
                    }

                    if (checkbox && checkbox.checked) {
                        checkedCount++;
                    }
                });

                form.classList.toggle('has-no-visible-schools', visibleCount === 0 && options.length > 0);

                const countText = form.querySelector('.assignment-count-text');
                if (countText) {
                    countText.textContent = checkedCount + ' dipilih dari ' + options.length + ' sekolah, ' + visibleCount + ' tampil';
                }
            }

            assignmentForms.forEach(function(form) {
                const filterFields = form.querySelectorAll('.assignment-school-search, .assignment-level-filter, .assignment-status-filter');
                filterFields.forEach(function(field) {
                    field.addEventListener('input', function() {
                        refreshAssignmentForm(form);
                    });
                    field.addEventListener('change', function() {
                        refreshAssignmentForm(form);
                    });
                });

                form.querySelectorAll('input[type="checkbox"]').forEach(function(checkbox) {
                    checkbox.addEventListener('change', function() {
                        refreshAssignmentForm(form);
                    });
                });

                const checkVisibleButton = form.querySelector('.assignment-check-visible');
                if (checkVisibleButton) {
                    checkVisibleButton.addEventListener('click', function() {
                        form.querySelectorAll('.assignment-school-option:not(.d-none) input[type="checkbox"]').forEach(function(checkbox) {
                            checkbox.checked = true;
                        });
                        refreshAssignmentForm(form);
                    });
                }

                const uncheckVisibleButton = form.querySelector('.assignment-uncheck-visible');
                if (uncheckVisibleButton) {
                    uncheckVisibleButton.addEventListener('click', function() {
                        form.querySelectorAll('.assignment-school-option:not(.d-none) input[type="checkbox"]').forEach(function(checkbox) {
                            checkbox.checked = false;
                        });
                        refreshAssignmentForm(form);
                    });
                }

                refreshAssignmentForm(form);
            });

            if (searchInput) {
                searchInput.addEventListener('keyup', function() {
                    const keyword = this.value.toLowerCase();
                    let visibleCount = 0;

                    rows.forEach(function(row) {
                        const isMatch = row.dataset.search.includes(keyword);
                        row.style.display = isMatch ? '' : 'none';
                        if (isMatch) {
                            visibleCount++;
                        }
                    });

                    if (notFoundRow) {
                        notFoundRow.style.display = visibleCount === 0 && rows.length > 0 ? '' : 'none';
                    }
                });
            }
        });
    </script>
@endsection
