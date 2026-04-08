@extends('layouts.app')

@section('title', 'Dashboard Utama')

@section('content')
    <div class="row align-items-end mb-5">
        <div class="col">
            <span class="text-primary fw-bold text-uppercase small tracking-widest" style="letter-spacing: 2px;">Analytical Overview</span>
            <h2 class="display-6 fw-extrabold font-headline mb-0 mt-1">Dashboard</h2>
        </div>
        <div class="col-auto">
            <div class="d-flex gap-2">
                <button class="btn btn-primary fw-bold">Export Report</button>
            </div>
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

    <div class="row g-4 mb-5">
        <div class="col-lg-8">
            <div class="card border-0 rounded-4 shadow-sm bg-light p-4 h-100">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h4 class="font-headline fw-bold mb-1">Compliance Distribution</h4>
                        <p class="text-muted small mb-0">School health categorization based on latest audits.</p>
                    </div>
                </div>
                <div class="chart-container align-items-end">
                    <div class="bar bg-info opacity-75" style="height: 65%;"></div>
                    <div class="bar bg-primary" style="height: 85%;"></div>
                    <div class="bar bg-secondary" style="height: 45%;"></div>
                    <div class="bar bg-danger" style="height: 15%;"></div>
                </div>
                <div class="d-flex justify-content-between mt-3 px-1 text-center">
                    <div class="flex-grow-1"><small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Exemplary</small></div>
                    <div class="flex-grow-1"><small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Proficient</small></div>
                    <div class="flex-grow-1"><small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Emerging</small></div>
                    <div class="flex-grow-1"><small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Critical</small></div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="d-flex flex-column gap-3 h-100">
                <h5 class="font-headline fw-bold mb-1">Report Insights</h5>
                <div class="card border-0 shadow-sm p-3 report-item bg-white">
                    <div class="d-flex align-items-center gap-3">
                        <span class="material-symbols-outlined text-primary">account_balance</span>
                        <span class="fw-bold small">Administrasi (Modul 1)</span>
                    </div>
                </div>
                <div class="card border-0 shadow-sm p-3 report-item bg-white" style="border-left-color: var(--tertiary) !important;">
                    <div class="d-flex align-items-center gap-3">
                        <span class="material-symbols-outlined" style="color: var(--tertiary);">menu_book</span>
                        <span class="fw-bold small">Kontrol KBM (Modul 2)</span>
                    </div>
                </div>
                <div class="card border-0 rounded-4 overflow-hidden position-relative p-4 mt-auto" style="background-color: #eff4ff;">
                    <span class="badge bg-primary-subtle text-primary mb-2 fw-bold" style="font-size: 0.65rem;">PRO TIP</span>
                    <p class="small text-dark fw-medium mb-0">Pastikan tautan Google Drive dari sekolah diatur ke "Anyone with the link".</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="p-4 bg-light bg-opacity-50 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="font-headline fw-bold mb-1">Performance Leaderboard</h4>
                <p class="text-muted small mb-0">Top ranked institutions by composite audit scores.</p>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light bg-opacity-25">
                    <tr>
                        <th class="px-4 py-3 text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Rank</th>
                        <th class="py-3 text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">School Name</th>
                        <th class="py-3 text-center text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Score</th>
                        <th class="py-3 text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schools as $index => $school)
                    <tr>
                        <td class="px-4 fw-bold font-headline text-dark">
                            #{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="icon-box bg-{{ $school->status_color }} bg-opacity-10 text-{{ $school->status_color }} small fw-bold">
                                    {{ substr($school->name, 0, 2) }}
                                </div>
                                <div>
                                    <a href="{{ route('school.show', $school->id) }}" class="text-decoration-none">
                                        <p class="mb-0 fw-bold small text-primary">{{ $school->name }}</p>
                                    </a>
                                    <p class="mb-0 text-muted" style="font-size: 0.65rem;">{{ $school->level }} • {{ $school->status }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="fw-bold text-primary">{{ number_format($school->score, 1) }}</span>
                        </td>
                        <td>
                            <span class="badge rounded-pill bg-{{ $school->status_color }}-subtle text-{{ $school->status_color }} text-uppercase fw-bold" style="font-size: 0.65rem; padding: 0.35rem 0.8rem;">
                                {{ $school->status_text }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">Belum ada data sekolah.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection@extends('layouts.app')

@section('title', 'Dashboard Utama')

@section('content')
    <div class="row align-items-end mb-5">
        <div class="col">
            <span class="text-primary fw-bold text-uppercase small tracking-widest" style="letter-spacing: 2px;">Analytical Overview</span>
            <h2 class="display-6 fw-extrabold font-headline mb-0 mt-1">Dashboard</h2>
        </div>
        <div class="col-auto">
            <div class="d-flex gap-2">
                <button class="btn btn-primary fw-bold">Export Report</button>
            </div>
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

    <div class="row g-4 mb-5">
        <div class="col-lg-8">
            <div class="card border-0 rounded-4 shadow-sm bg-light p-4 h-100">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h4 class="font-headline fw-bold mb-1">Compliance Distribution</h4>
                        <p class="text-muted small mb-0">School health categorization based on latest audits.</p>
                    </div>
                </div>
                <div class="chart-container align-items-end">
                    <div class="bar bg-info opacity-75" style="height: 65%;"></div>
                    <div class="bar bg-primary" style="height: 85%;"></div>
                    <div class="bar bg-secondary" style="height: 45%;"></div>
                    <div class="bar bg-danger" style="height: 15%;"></div>
                </div>
                <div class="d-flex justify-content-between mt-3 px-1 text-center">
                    <div class="flex-grow-1"><small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Exemplary</small></div>
                    <div class="flex-grow-1"><small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Proficient</small></div>
                    <div class="flex-grow-1"><small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Emerging</small></div>
                    <div class="flex-grow-1"><small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Critical</small></div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="d-flex flex-column gap-3 h-100">
                <h5 class="font-headline fw-bold mb-1">Report Insights</h5>
                <div class="card border-0 shadow-sm p-3 report-item bg-white">
                    <div class="d-flex align-items-center gap-3">
                        <span class="material-symbols-outlined text-primary">account_balance</span>
                        <span class="fw-bold small">Administrasi (Modul 1)</span>
                    </div>
                </div>
                <div class="card border-0 shadow-sm p-3 report-item bg-white" style="border-left-color: var(--tertiary) !important;">
                    <div class="d-flex align-items-center gap-3">
                        <span class="material-symbols-outlined" style="color: var(--tertiary);">menu_book</span>
                        <span class="fw-bold small">Kontrol KBM (Modul 2)</span>
                    </div>
                </div>
                <div class="card border-0 rounded-4 overflow-hidden position-relative p-4 mt-auto" style="background-color: #eff4ff;">
                    <span class="badge bg-primary-subtle text-primary mb-2 fw-bold" style="font-size: 0.65rem;">PRO TIP</span>
                    <p class="small text-dark fw-medium mb-0">Pastikan tautan Google Drive dari sekolah diatur ke "Anyone with the link".</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="p-4 bg-light bg-opacity-50 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="font-headline fw-bold mb-1">Performance Leaderboard</h4>
                <p class="text-muted small mb-0">Top ranked institutions by composite audit scores.</p>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light bg-opacity-25">
                    <tr>
                        <th class="px-4 py-3 text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Rank</th>
                        <th class="py-3 text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">School Name</th>
                        <th class="py-3 text-center text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Score</th>
                        <th class="py-3 text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schools as $index => $school)
                    <tr>
                        <td class="px-4 fw-bold font-headline text-dark">
                            #{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="icon-box bg-{{ $school->status_color }} bg-opacity-10 text-{{ $school->status_color }} small fw-bold">
                                    {{ substr($school->name, 0, 2) }}
                                </div>
                                <div>
                                    <p class="mb-0 fw-bold small text-dark">{{ $school->name }}</p>
                                    <p class="mb-0 text-muted" style="font-size: 0.65rem;">{{ $school->level }} • {{ $school->status }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="fw-bold text-primary">{{ number_format($school->score, 1) }}</span>
                        </td>
                        <td>
                            <span class="badge rounded-pill bg-{{ $school->status_color }}-subtle text-{{ $school->status_color }} text-uppercase fw-bold" style="font-size: 0.65rem; padding: 0.35rem 0.8rem;">
                                {{ $school->status_text }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">Belum ada data sekolah.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection