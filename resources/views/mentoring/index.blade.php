@extends('layouts.app')

@section('title', 'Siklus Pendampingan')

@section('content')
    {{-- ========================================================================= --}}
    {{-- LIBRARY & STYLES: CHOICES.JS UNTUK PENCARIAN DROPDOWN & KALENDER --}}
    {{-- ========================================================================= --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <link rel="stylesheet" href="{{ asset('tmp/plugins/fullcalendar/fullcalendar.min.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="{{ asset('tmp/plugins/jQuery/jQuery-2.1.4.min.js') }}"></script>
    <script src="{{ asset('tmp/plugins/daterangepicker/moment.min.js') }}"></script>
    <script src="{{ asset('tmp/plugins/fullcalendar/fullcalendar.min.js') }}"></script>
    
    <style>
        /* Pengaturan Dasar Choices.js */
        .choices { font-size: 1rem; margin-bottom: 0; }
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
        .choices__list--dropdown .choices__item--selectable.is-selected,
        .choices__list--dropdown .choices__item--selectable[aria-selected="true"] {
            background-color: #e8f6f8 !important;
            color: #0b3c49 !important;
            font-weight: 700;
        }
        .choices__list--dropdown .choices__item--selectable.is-highlighted {
            background-color: #0d6efd !important; color: #ffffff !important; font-weight: 600;
        }

        html[data-theme="dark"] .choices__list--dropdown .choices__item,
        html[data-theme="dark"] .choices__list[aria-expanded] .choices__item {
            border-bottom-color: rgba(180, 221, 227, 0.14) !important;
        }
        html[data-theme="dark"] .choices__list--dropdown .choices__item--selectable.is-selected,
        html[data-theme="dark"] .choices__list--dropdown .choices__item--selectable[aria-selected="true"],
        html[data-theme="dark"] .choices__list[aria-expanded] .choices__item--selectable.is-selected,
        html[data-theme="dark"] .choices__list[aria-expanded] .choices__item--selectable[aria-selected="true"] {
            background-color: #1d4b56 !important;
            color: #ffffff !important;
            -webkit-text-fill-color: #ffffff !important;
            font-weight: 800;
        }
        html[data-theme="dark"] .choices__list--dropdown .choices__item--selectable.is-highlighted,
        html[data-theme="dark"] .choices__list[aria-expanded] .choices__item--selectable.is-highlighted {
            background-color: #1d7784 !important;
            color: #ffffff !important;
            -webkit-text-fill-color: #ffffff !important;
            font-weight: 800;
        }

        /* Tampilan Khusus Mobile */
        @media (max-width: 768px) {
            .choices__list--dropdown {
                position: fixed !important; top: 20% !important; left: 5% !important; right: 5% !important;
                width: 90% !important; max-height: 60vh !important; border: 1px solid #dee2e6 !important;
            }
            .choices.is-open::after {
                content: ""; position: fixed; top: 0; left: 0; right: 0; bottom: 0;
                background: rgba(0,0,0,0.2); z-index: 999;
            }
        }

        .calendar-shell {
            overflow: hidden;
            border-radius: 1.35rem;
            background: rgba(255, 255, 255, 0.96);
        }
        .calendar-summary {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 0.65rem;
        }
        .calendar-stat {
            min-height: 72px;
            border: 1px solid rgba(15, 107, 125, 0.08);
            border-radius: 0.85rem;
            background: rgba(255, 255, 255, 0.86);
            padding: 0.8rem;
        }
        .calendar-stat strong {
            display: block;
            color: var(--text-main);
            font-size: 1.15rem;
            line-height: 1;
        }
        .calendar-stat span {
            display: block;
            color: var(--text-soft);
            font-size: 0.68rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .calendar-toolbar-panel {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.85rem;
            border: 1px solid rgba(15, 107, 125, 0.08);
            border-radius: 1rem;
            background: rgba(245, 248, 251, 0.74);
        }
        .calendar-school-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.65rem;
        }
        .calendar-month-control {
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }
        .calendar-month-control label {
            margin: 0;
            color: var(--text-soft);
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            white-space: nowrap;
        }
        .calendar-date-field {
            display: flex;
            align-items: center;
            gap: 0.45rem;
        }
        .calendar-month-control .form-select,
        .calendar-month-control input[type="date"] {
            min-height: 40px;
            border-radius: 999px;
            font-size: 0.84rem;
            font-weight: 700;
        }
        #calendarMonthSelect {
            min-width: 150px;
        }
        #calendarYearSelect {
            min-width: 104px;
        }
        #calendarDayPicker {
            min-width: 148px;
        }
        .cycle-dot {
            width: 0.65rem;
            height: 0.65rem;
            border-radius: 999px;
            display: inline-flex;
            flex: 0 0 auto;
        }
        #siklusCalendar {
            min-height: 620px;
            padding: 0.85rem 0 0;
        }
        #siklusCalendar .fc-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }
        #siklusCalendar .fc-toolbar h2 {
            margin: 0;
            color: var(--text-main);
            font-family: 'Manrope', sans-serif;
            font-size: 1.1rem;
            font-weight: 800;
        }
        #siklusCalendar .fc-button {
            border: 1px solid rgba(15, 107, 125, 0.12);
            border-radius: 999px;
            background: #fff;
            color: var(--brand-700);
            box-shadow: 0 8px 18px rgba(15, 107, 125, 0.08);
            text-shadow: none;
            font-weight: 800;
        }
        #siklusCalendar .fc-button:hover,
        #siklusCalendar .fc-state-active {
            background: var(--brand-700);
            color: #fff;
        }
        #siklusCalendar .fc-widget-header,
        #siklusCalendar .fc-widget-content {
            border-color: rgba(15, 107, 125, 0.08);
        }
        #siklusCalendar .fc-day-header {
            padding: 0.75rem 0.4rem;
            color: var(--text-soft);
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        #siklusCalendar .fc-day-number {
            padding: 0.45rem 0.55rem;
            color: var(--text-main);
            font-weight: 800;
        }
        #siklusCalendar .fc-today {
            background: rgba(15, 107, 125, 0.06);
        }
        #siklusCalendar .fc-event {
            border: 0;
            border-radius: 0.55rem;
            padding: 0.18rem 0.35rem;
            font-size: 0.72rem;
            font-weight: 800;
            cursor: pointer;
        }
        #siklusCalendar .fc-event .fc-title {
            white-space: normal;
        }
        .calendar-list {
            max-height: 620px;
            overflow-y: auto;
        }
        .calendar-side-panel {
            border-left: 1px solid rgba(15, 107, 125, 0.08);
            padding-left: 1.1rem;
        }
        .cycle-timeline {
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
            padding-left: 0.75rem;
        }
        .cycle-timeline::before {
            content: '';
            position: absolute;
            top: 0.35rem;
            bottom: 0.35rem;
            left: 0.32rem;
            width: 2px;
            background: rgba(15, 107, 125, 0.1);
        }
        .cycle-timeline-item {
            position: relative;
            width: 100%;
            border: 1px solid rgba(15, 107, 125, 0.08);
            border-radius: 1rem;
            background: #fff;
            padding: 0.85rem 0.85rem 0.85rem 1rem;
            text-align: left;
            transition: transform 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease;
        }
        .cycle-timeline-item:hover,
        .cycle-timeline-item:focus {
            border-color: rgba(15, 107, 125, 0.2);
            box-shadow: 0 10px 22px rgba(15, 107, 125, 0.08);
            transform: translateX(2px);
        }
        .cycle-timeline-dot {
            position: absolute;
            top: 1rem;
            left: -0.78rem;
            width: 0.72rem;
            height: 0.72rem;
            border: 2px solid #fff;
            border-radius: 999px;
            box-shadow: 0 0 0 2px rgba(15, 107, 125, 0.12);
        }
        .cycle-timeline-note {
            display: -webkit-box;
            overflow: hidden;
            color: var(--text-soft);
            font-size: 0.72rem;
            line-height: 1.45;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        html[data-theme="dark"] .calendar-shell,
        html[data-theme="dark"] .calendar-stat,
        html[data-theme="dark"] .calendar-toolbar-panel,
        html[data-theme="dark"] .cycle-timeline-item,
        html[data-theme="dark"] #siklusCalendar .fc-button {
            background: #112a31;
            color: var(--text-main);
            border-color: rgba(180, 221, 227, 0.14);
        }
        html[data-theme="dark"] .cycle-timeline::before {
            background: rgba(180, 221, 227, 0.16);
        }
        html[data-theme="dark"] .cycle-timeline-dot {
            border-color: #112a31;
        }
        html[data-theme="dark"] #siklusCalendar .fc-widget-header,
        html[data-theme="dark"] #siklusCalendar .fc-widget-content {
            border-color: rgba(180, 221, 227, 0.12);
        }
        html[data-theme="dark"] #siklusCalendar .fc-today {
            background: rgba(99, 199, 210, 0.1);
        }
        @media (max-width: 991.98px) {
            .calendar-summary {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            #siklusCalendar {
                min-height: 560px;
                padding: 0.75rem;
            }
            #siklusCalendar .fc-toolbar {
                align-items: flex-start;
                flex-direction: column;
            }
            .calendar-toolbar-panel,
            .calendar-month-control {
                align-items: stretch;
                flex-direction: column;
            }
            .calendar-date-field,
            .calendar-month-control .form-select,
            .calendar-month-control input[type="date"] {
                width: 100%;
            }
            .calendar-side-panel {
                border-left: 0;
                border-top: 1px solid rgba(15, 107, 125, 0.08);
                padding-left: 0;
                padding-top: 1rem;
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
                    searchFuzzy: false,
                    searchFields: ['label'],
                    searchResultLimit: 10,
                    placeholder: true,
                    placeholderValue: '-- Ketik atau Pilih Nama Sekolah --'
                });
            }
        });
    </script>

    {{-- ========================================================================= --}}
    {{-- HEADER HALAMAN & FILTER SEKOLAH --}}
    {{-- ========================================================================= --}}
    <div class="mb-4">
        <h2 class="display-6 fw-extrabold font-headline mb-0">Siklus Pendampingan</h2>
        <p class="text-muted small mb-0">Catatan tahapan dan proses pendampingan sekolah binaan.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 small mb-4 shadow-sm" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close pb-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-primary bg-opacity-10 border-start border-4 border-primary">
        <div class="card-body p-4">
            <form action="{{ route('mentoring.index') }}" method="GET" id="formPilihSekolah" >
                <label class="form-label small fw-bold text-primary mb-2">Pilih Sekolah Binaan</label>
                <select name="school_id" id="schoolSelect" class="form-select border-primary shadow-sm" onchange="document.getElementById('formPilihSekolah').submit();" required>
                    <option value="">-- Ketik atau Pilih Nama Sekolah --</option>
                    @foreach($schools as $s)
                        <option value="{{ $s->id }}" {{ request('school_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->name }} - (Skor: {{ $s->skor_performa ?? '0' }}%)
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- TAMPILAN JIKA SEKOLAH SUDAH DIPILIH --}}
    {{-- ========================================================================= --}}
    @if($selectedSchool)
        @php
            $skor = $selectedSchool->skor_performa ?? 0;
            $badgeColor = $skor >= 75 ? 'success' : ($skor >= 40 ? 'warning' : 'danger');
        @endphp
        
        {{-- REKAPITULASI KALENDER --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4 calendar-shell">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-3 px-4">
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start gap-3">
                    <div>
                        <h5 class="font-headline fw-bold mb-1">Kalender Siklus: <span class="text-primary">{{ $selectedSchool->name }}</span></h5>
                        <p class="text-muted small mb-0">Rekap pendampingan tampil sebagai event bulanan agar jadwal dan riwayat mudah dipindai.</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <a href="{{ route('mentoring.export', ['school_id' => $selectedSchool->id]) }}" class="btn btn-success btn-sm fw-bold d-flex align-items-center gap-1 shadow-sm">
                            <span class="material-symbols-outlined fs-6">download</span> Download File Siklus
                        </a>
                        <button type="button" class="btn btn-primary btn-sm fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalSiklus">
                            <span class="d-flex align-items-center gap-1">
                                <span class="material-symbols-outlined fs-6">add_circle</span> Input Siklus
                            </span>
                        </button>
                    </div>
                </div>

                <div class="calendar-toolbar-panel mt-4">
                    <div class="calendar-school-meta">
                        <span class="badge bg-{{ $badgeColor }} bg-opacity-10 text-{{ $badgeColor }} border border-{{ $badgeColor }} rounded-pill px-3">
                            Skor Performa: {{ $skor }}%
                        </span>
                        <span class="small text-muted fw-bold" id="calendarCurrentMonthLabel"></span>
                    </div>
                    <div class="calendar-month-control">
                        @php
                            [$initialCalendarYear, $initialCalendarMonthNumber] = explode('-', $initialCalendarMonth);
                            $monthOptions = [
                                1 => 'Januari',
                                2 => 'Februari',
                                3 => 'Maret',
                                4 => 'April',
                                5 => 'Mei',
                                6 => 'Juni',
                                7 => 'Juli',
                                8 => 'Agustus',
                                9 => 'September',
                                10 => 'Oktober',
                                11 => 'November',
                                12 => 'Desember',
                            ];
                        @endphp
                        <div class="calendar-date-field">
                            <label for="calendarMonthSelect">Bulan</label>
                            <select id="calendarMonthSelect" class="form-select form-select-sm">
                                @foreach($monthOptions as $monthNumber => $monthName)
                                    <option value="{{ str_pad($monthNumber, 2, '0', STR_PAD_LEFT) }}" {{ (int) $initialCalendarMonthNumber === $monthNumber ? 'selected' : '' }}>
                                        {{ $monthName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="calendar-date-field">
                            <label for="calendarYearSelect">Tahun</label>
                            <select id="calendarYearSelect" class="form-select form-select-sm">
                                @foreach($availableYears as $year)
                                    <option value="{{ $year }}" {{ (int) $initialCalendarYear === (int) $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="calendar-date-field">
                            <label for="calendarDayPicker">Tanggal</label>
                            <input type="date" id="calendarDayPicker" class="form-control form-control-sm" value="{{ $initialCalendarMonth }}-01">
                        </div>
                    </div>
                </div>

                <div class="calendar-summary mt-3">
                    <div class="calendar-stat">
                        <span>Total Intervensi</span>
                        <strong>{{ $recap['total'] }}</strong>
                    </div>
                    <div class="calendar-stat">
                        <span>Perencanaan</span>
                        <strong class="text-info">{{ $recap['perencanaan'] }}</strong>
                    </div>
                    <div class="calendar-stat">
                        <span>Pend. Program</span>
                        <strong class="text-warning">{{ $recap['perencanaan_prog'] }}</strong>
                    </div>
                    <div class="calendar-stat">
                        <span>Pelaksanaan</span>
                        <strong class="text-primary">{{ $recap['pelaksanaan_prog'] }}</strong>
                    </div>
                    <div class="calendar-stat">
                        <span>Pelaporan</span>
                        <strong class="text-success">{{ $recap['pelaporan'] }}</strong>
                    </div>
                </div>
            </div>

            <div class="card-body pt-0 px-4 pb-4">
                <div class="row g-4">
                    <div class="col-lg-9">
                        <div id="siklusCalendar"></div>
                    </div>
                    <div class="col-lg-3">
                        <div class="d-flex flex-column gap-3 calendar-list calendar-side-panel">
                            <div>
                                <h6 class="fw-bold mb-2">Legenda Siklus</h6>
                                <div class="d-flex flex-column gap-2 small text-muted">
                                    <div class="d-flex align-items-center gap-2"><span class="cycle-dot" style="background:#0dcaf0;"></span> Perencanaan Pendampingan</div>
                                    <div class="d-flex align-items-center gap-2"><span class="cycle-dot" style="background:#ffc107;"></span> Pendampingan Perencanaan Program</div>
                                    <div class="d-flex align-items-center gap-2"><span class="cycle-dot" style="background:#0d6efd;"></span> Pendampingan Pelaksanaan Program</div>
                                    <div class="d-flex align-items-center gap-2"><span class="cycle-dot" style="background:#198754;"></span> Pelaporan Pendampingan</div>
                                </div>
                            </div>
                            <hr class="my-0">
                            <div>
                                <div class="d-flex justify-content-between align-items-center gap-2 mb-3">
                                    <h6 class="fw-bold mb-0">Time Line</h6>
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary rounded-pill px-2">
                                        {{ $cycles->count() }} Event
                                    </span>
                                </div>
                                <div class="cycle-timeline">
                                    @forelse($cycles as $timelineCycle)
                                        @php
                                            $cycleColor = match($timelineCycle->siklus) {
                                                'Perencanaan Pendampingan' => '#0dcaf0',
                                                'Pendampingan Perencanaan Program' => '#ffc107',
                                                'Pendampingan Pelaksanaan Program' => '#0d6efd',
                                                'Pelaporan Pendampingan' => '#198754',
                                                default => '#6c757d',
                                            };
                                        @endphp
                                        <button type="button" class="cycle-timeline-item" data-bs-toggle="modal" data-bs-target="#editModal{{ $timelineCycle->id }}">
                                            <span class="cycle-timeline-dot" style="background:{{ $cycleColor }};"></span>
                                            <div class="fw-bold small">{{ \Carbon\Carbon::parse($timelineCycle->tanggal)->translatedFormat('d M Y') }}</div>
                                            <div class="small fw-semibold text-dark mt-1">{{ $timelineCycle->siklus }}</div>
                                            <div class="cycle-timeline-note mt-1">
                                                {{ $timelineCycle->keterangan ?? 'Tidak ada keterangan.' }}
                                            </div>
                                        </button>
                                    @empty
                                        <p class="small text-muted mb-0">Belum ada event siklus pada timeline.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL EDIT / HAPUS DATA SIKLUS --}}
        @foreach($cycles as $cycle)
            <div class="modal fade text-start" id="editModal{{ $cycle->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <form action="{{ route('mentoring.update', $cycle->id) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="modal-header border-bottom-0 pt-4 px-4 pb-0">
                                <div>
                                    <h5 class="modal-title font-headline fw-bold mb-1">Edit Siklus</h5>
                                    <p class="text-muted small mb-0">Ubah atau hapus event siklus pendampingan.</p>
                                </div>
                                <button type="button" class="btn-close mt-1" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body px-4">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted mb-1">Tanggal Pendampingan</label>
                                    <input type="date" name="tanggal" class="form-control bg-light border-secondary-subtle" value="{{ $cycle->tanggal }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted mb-1">Tahapan Siklus</label>
                                    <select name="siklus" class="form-select bg-light border-secondary-subtle" required>
                                        <option value="Perencanaan Pendampingan" {{ $cycle->siklus == 'Perencanaan Pendampingan' ? 'selected' : '' }}>1. Perencanaan Pendampingan</option>
                                        <option value="Pendampingan Perencanaan Program" {{ $cycle->siklus == 'Pendampingan Perencanaan Program' ? 'selected' : '' }}>2. Pendampingan Perencanaan Program</option>
                                        <option value="Pendampingan Pelaksanaan Program" {{ $cycle->siklus == 'Pendampingan Pelaksanaan Program' ? 'selected' : '' }}>3. Pendampingan Pelaksanaan Program</option>
                                        <option value="Pelaporan Pendampingan" {{ $cycle->siklus == 'Pelaporan Pendampingan' ? 'selected' : '' }}>4. Pelaporan Pendampingan</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted mb-1">Keterangan / Catatan</label>
                                    <textarea name="keterangan" class="form-control bg-light border-secondary-subtle" rows="3" required>{{ $cycle->keterangan }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer bg-light border-top-0 px-4 py-3 d-flex justify-content-between">
                                <button type="submit" class="btn btn-primary btn-sm fw-bold">Update Data</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm fw-bold" data-bs-dismiss="modal">Batal</button>
                            </div>
                        </form>
                        <form action="{{ route('mentoring.destroy', $cycle->id) }}" method="POST" class="px-4 pb-4" onsubmit="return confirm('Hapus data siklus ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm fw-bold w-100 d-flex align-items-center justify-content-center gap-1">
                                <span class="material-symbols-outlined fs-6">delete</span>
                                Hapus Siklus Pendampingan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- MODAL INPUT DATA SIKLUS BARU --}}
        <div class="modal fade" id="modalSiklus" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form action="{{ route('mentoring.store') }}" method="POST" class="modal-content border-0 shadow">
                    @csrf
                    <input type="hidden" name="school_id" value="{{ $selectedSchool->id }}">
                    <div class="modal-header border-bottom-0 pt-4 px-4 pb-0">
                        <div class="w-100 pe-3">
                            <span class="badge bg-primary bg-opacity-10 text-primary mb-2 px-3 py-2 rounded-pill fw-semibold">Input Baru</span>
                            <h4 class="modal-title font-headline fw-bold mb-1">Siklus Pendampingan</h4>
                            <p class="text-muted small mb-0">
                                {{ $selectedSchool->name }} &bull; <strong class="text-{{ $badgeColor }}">Skor: {{ $selectedSchool->skor_performa ?? 0 }}%</strong>
                            </p>
                        </div>
                        <button type="button" class="btn-close mt-1" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body px-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted mb-1">Tanggal Pelaksanaan</label>
                            <input type="date" name="tanggal" class="form-control bg-light border-secondary-subtle rounded-3 px-3 py-2" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted mb-1">Pilih Tahapan Siklus</label>
                            <select name="siklus" class="form-select bg-light border-secondary-subtle rounded-3 px-3 py-2" required>
                                <option value="">-- Pilih Siklus --</option>
                                <option value="Perencanaan Pendampingan">1. Perencanaan Pendampingan</option>
                                <option value="Pendampingan Perencanaan Program">2. Pendampingan Perencanaan Program</option>
                                <option value="Pendampingan Pelaksanaan Program">3. Pendampingan Pelaksanaan Program</option>
                                <option value="Pelaporan Pendampingan">4. Pelaporan Pendampingan</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted mb-1">Keterangan Aktivitas</label>
                            <textarea name="keterangan" class="form-control bg-light border-secondary-subtle rounded-3 px-3 py-2" rows="4" placeholder="Tulis catatan, temuan, atau progres pendampingan di sini..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top-0 rounded-bottom-4 px-4 py-3">
                        <button type="button" class="btn btn-outline-secondary btn-sm fw-bold px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm fw-bold px-4">Simpan Siklus</button>
                    </div>
                </form>
            </div>
        </div>

    @else
        {{-- TAMPILAN KOSONG JIKA SEKOLAH BELUM DIPILIH --}}
        <div class="text-center py-5">
            <span class="material-symbols-outlined display-1 text-muted opacity-25 mb-3">timeline</span>
            <h5 class="fw-bold text-muted">Belum Ada Sekolah yang Dipilih</h5>
            <p class="small text-muted">Pilih nama sekolah pada kotak pencarian di atas untuk melihat rekapitulasi dan menambah catatan siklus pendampingan.</p>
        </div>
    @endif

    {{-- ========================================================================= --}}
    {{-- SCRIPT: KALENDER SIKLUS (REAL-TIME) --}}
    {{-- ========================================================================= --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarElement = document.getElementById('siklusCalendar');
            if (calendarElement && window.jQuery && typeof jQuery(calendarElement).fullCalendar === 'function') {
                const siklusEvents = @json($calendarEvents);
                const siklusCalendar = jQuery(calendarElement);
                const calendarMonthSelect = document.getElementById('calendarMonthSelect');
                const calendarYearSelect = document.getElementById('calendarYearSelect');
                const calendarDayPicker = document.getElementById('calendarDayPicker');
                const calendarCurrentMonthLabel = document.getElementById('calendarCurrentMonthLabel');
                const calendarMonthNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

                function updateCalendarMonthMeta(dateValue) {
                    if (!dateValue || typeof moment === 'undefined') return;

                    const activeDate = moment(dateValue);
                    const monthValue = activeDate.format('MM');
                    const yearValue = activeDate.format('YYYY');

                    if (calendarMonthSelect && calendarMonthSelect.value !== monthValue) {
                        calendarMonthSelect.value = monthValue;
                    }

                    if (calendarYearSelect && calendarYearSelect.value !== yearValue) {
                        calendarYearSelect.value = yearValue;
                    }

                    if (calendarDayPicker && calendarDayPicker.value !== activeDate.format('YYYY-MM-DD')) {
                        calendarDayPicker.value = activeDate.format('YYYY-MM-DD');
                    }

                    if (calendarCurrentMonthLabel) {
                        calendarCurrentMonthLabel.textContent = `Bulan aktif: ${calendarMonthNames[activeDate.month()]} ${activeDate.year()}`;
                    }
                }

                function gotoSelectedMonthYear() {
                    if (!calendarMonthSelect || !calendarYearSelect) return;

                    const selectedDate = `${calendarYearSelect.value}-${calendarMonthSelect.value}-01`;
                    siklusCalendar.fullCalendar('gotoDate', selectedDate);
                    updateCalendarMonthMeta(selectedDate);
                }

                function gotoSelectedDay() {
                    if (!calendarDayPicker || !calendarDayPicker.value) return;

                    siklusCalendar.fullCalendar('gotoDate', calendarDayPicker.value);
                    siklusCalendar.fullCalendar('changeView', 'agendaDay');
                    updateCalendarMonthMeta(calendarDayPicker.value);
                }

                function forceCalendarView(viewName) {
                    siklusCalendar.fullCalendar('changeView', viewName);
                    updateCalendarMonthMeta(siklusCalendar.fullCalendar('getDate'));
                }

                siklusCalendar.fullCalendar({
                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'month,agendaWeek,agendaDay'
                    },
                    buttonText: {
                        today: 'Hari Ini',
                        month: 'Bulan',
                        week: 'Minggu',
                        day: 'Hari'
                    },
                    monthNames: ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'],
                    monthNamesShort: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'],
                    dayNames: ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'],
                    dayNamesShort: ['Min','Sen','Sel','Rab','Kam','Jum','Sab'],
                    firstDay: 1,
                    height: 'auto',
                    defaultDate: '{{ $initialCalendarMonth }}-01',
                    editable: false,
                    allDaySlot: true,
                    allDayText: 'Event',
                    axisFormat: 'HH:mm',
                    timeFormat: 'HH:mm',
                    minTime: '06:00:00',
                    maxTime: '18:00:00',
                    events: siklusEvents,
                    viewRender: function(view) {
                        updateCalendarMonthMeta(view.intervalStart || siklusCalendar.fullCalendar('getDate'));
                    },
                    eventRender: function(event, element) {
                        element.attr('title', event.description);
                    },
                    eventClick: function(event) {
                        if (!event.modalTarget) return;

                        const modalElement = document.querySelector(event.modalTarget);
                        if (modalElement && window.bootstrap) {
                            bootstrap.Modal.getOrCreateInstance(modalElement).show();
                        }
                    }
                });

                if (calendarMonthSelect) calendarMonthSelect.addEventListener('change', gotoSelectedMonthYear);
                if (calendarYearSelect) calendarYearSelect.addEventListener('change', gotoSelectedMonthYear);
                if (calendarDayPicker) calendarDayPicker.addEventListener('change', gotoSelectedDay);

                calendarElement.querySelector('.fc-month-button')?.addEventListener('click', function(event) {
                    event.preventDefault();
                    forceCalendarView('month');
                });
                calendarElement.querySelector('.fc-agendaWeek-button')?.addEventListener('click', function(event) {
                    event.preventDefault();
                    forceCalendarView('agendaWeek');
                });
                calendarElement.querySelector('.fc-agendaDay-button')?.addEventListener('click', function(event) {
                    event.preventDefault();
                    forceCalendarView('agendaDay');
                });
            }
        });
    </script>
@endsection
