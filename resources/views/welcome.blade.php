<!DOCTYPE html>

<html lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>SILAWAS - School Supervision System</title>
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
<!-- Google Fonts: Manrope & Inter -->
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&amp;family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
<!-- Material Symbols -->
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<style>
        :root {
            --bs-primary: #005F73;
            --bs-primary-rgb: 0, 95, 115;
            --bs-body-font-family: 'Inter', sans-serif;
            --bs-body-bg: #f8f9ff;
            --bs-body-color: #0b1c30;
            --surface-container: #eff4ff;
            --tertiary: #004930;
            --error: #ba1a1a;
        }

        body {
            font-family: var(--bs-body-font-family);
        }

        .font-headline {
            font-family: 'Manrope', sans-serif;
        }

        /* Sidebar Custom Styles */
        .sidebar {
            width: 280px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1030;
            background-color: #f1f5f9;
            border-right: 1px solid rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
        }

        .nav-link {
            color: #64748b;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            color: var(--bs-primary);
            background-color: rgba(0, 95, 115, 0.05);
        }

        .nav-link.active {
            color: var(--bs-primary);
            font-weight: 700;
            background-color: rgba(0, 95, 115, 0.08);
            border-right: 4px solid var(--bs-primary);
        }

        /* Main Content Adjustments */
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
        }

        .top-navbar {
            height: 70px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            z-index: 1020;
        }

        /* Dashboard Components */
        .metric-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.03);
            padding: 1.5rem;
            background: #fff;
        }

        .btn-primary {
            background-color: var(--bs-primary);
            border-color: var(--bs-primary);
            border-radius: 50px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
        }

        .btn-outline-primary {
            color: var(--bs-primary);
            border-color: rgba(0, 95, 115, 0.2);
            border-radius: 50px;
            padding: 0.5rem 1.5rem;
        }

        .icon-box {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .progress-slim {
            height: 6px;
        }

        .chart-container {
            height: 250px;
            display: flex;
            align-items: flex-end;
            gap: 1.5rem;
            padding: 1rem;
        }

        .bar {
            flex: 1;
            border-radius: 8px 8px 0 0;
            transition: opacity 0.2s;
        }

        .bar:hover {
            opacity: 0.8;
        }

        .report-item {
            border-left: 4px solid var(--bs-primary);
            transition: transform 0.2s;
            cursor: pointer;
        }

        .report-item:hover {
            transform: translateX(5px);
            background-color: #f8f9fa !important;
        }

        .avatar-img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid rgba(0, 95, 115, 0.1);
        }
    </style>
</head>
<body>
<!-- Sidebar -->
<aside class="sidebar p-4">
<div class="mb-5 px-3">
<h1 class="h4 font-headline fw-bold text-primary mb-0">SILAWAS</h1>
<small class="text-muted text-uppercase fw-semibold" style="font-size: 0.65rem; letter-spacing: 1.5px;">School Supervision System</small>
</div>
<nav class="nav flex-column gap-1">
<a class="nav-link active" href="#">
<span class="material-symbols-outlined me-3">dashboard</span>
                Dashboard
            </a>
<a class="nav-link" href="#">
<span class="material-symbols-outlined me-3">domain</span>
                Schools
            </a>
<a class="nav-link" href="#">
<span class="material-symbols-outlined me-3">assessment</span>
                Reports
            </a>
<a class="nav-link" href="#">
<span class="material-symbols-outlined me-3">settings</span>
                Settings
            </a>
</nav>
<div class="mt-auto px-2">
<button class="btn btn-primary w-100 d-flex align-items-center justify-center gap-2 py-2">
<span class="material-symbols-outlined fs-6">add</span>
                Create Audit
            </button>
</div>
</aside>
<!-- Main Content -->
<main class="main-content">
<!-- Top Navbar -->
<header class="top-navbar sticky-top border-bottom d-flex align-items-center px-4">
<div class="container-fluid d-flex align-items-center justify-content-between">
<div class="search-bar w-50">
<div class="input-group">
<span class="input-group-text bg-light border-0"><span class="material-symbols-outlined text-muted fs-5">search</span></span>
<input class="form-control bg-light border-0 shadow-none rounded-end" placeholder="Search schools, audits, or reports..." type="text"/>
</div>
</div>
<div class="d-flex align-items-center gap-4">
<div class="d-flex align-items-center gap-3 border-end pe-4">
<button class="btn btn-link text-decoration-none text-muted p-0 d-flex align-items-center gap-1">
<span class="material-symbols-outlined">help</span>
<small class="fw-medium">Support</small>
</button>
<button class="btn btn-link text-decoration-none text-muted p-0 position-relative">
<span class="material-symbols-outlined">notifications</span>
<span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
</button>
</div>
<div class="d-flex align-items-center gap-3">
<div class="text-end d-none d-md-block">
<p class="mb-0 fw-bold small text-dark">Supervisor Profile</p>
<small class="text-muted text-uppercase" style="font-size: 0.65rem;">Main Supervisor</small>
</div>
<img alt="Profile" class="avatar-img" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDlmaBRJM5SP7Eqw-W8AqZDzJMT4xl24dGlI1BztDaXTLKmMEhtzD1S0P9VxGhjtzrPIqXMHaUQw4aYvvnCPoTr9I_bweCwP6fxOz7vctd0hH47LMslf9Q5g84kRAL9XM2KpHoUX178rKX-qTVj_7jrJB5HFT2M-UvcgZLIcSyRG47NTKim6QJVZ9Givv3wzWarqW2B59lIBwcg3K2-PjSewHwUCzrozbzte8PfyYjpPo1XNcIu_N1KWGfoeJujbZahhWOtXRranNWA"/>
</div>
</div>
</div>
</header>
<div class="p-5 container-fluid">
<!-- Heading -->
<div class="row align-items-end mb-5">
<div class="col">
<span class="text-primary fw-bold text-uppercase small tracking-widest" style="letter-spacing: 2px;">Analytical Overview</span>
<h2 class="display-6 fw-extrabold font-headline mb-0 mt-1">Dashboard</h2>
</div>
<div class="col-auto">
<div class="d-flex gap-2">
<button class="btn btn-outline-primary fw-bold">Export Report</button>
<button class="btn btn-primary fw-bold">Audit Schedule</button>
</div>
</div>
</div>
<!-- Metric Cards -->
<div class="row g-4 mb-5">
<div class="col-md-3">
<div class="metric-card">
<div class="d-flex justify-content-between mb-3">
<div class="icon-box bg-primary bg-opacity-10 text-primary">
<span class="material-symbols-outlined">school</span>
</div>
<span class="text-success small fw-bold">+12% vs LY</span>
</div>
<h3 class="font-headline fw-bold text-dark mb-0">1,482</h3>
<small class="text-muted text-uppercase tracking-wider fw-semibold">Total Schools</small>
</div>
</div>
<div class="col-md-3">
<div class="metric-card">
<div class="d-flex justify-content-between mb-3">
<div class="icon-box bg-success bg-opacity-10 text-success">
<span class="material-symbols-outlined">verified</span>
</div>
<div class="d-flex">
<span class="badge rounded-pill bg-light text-muted border px-2 py-1">8.4k Verified</span>
</div>
</div>
<h3 class="font-headline fw-bold text-dark mb-0">84.2%</h3>
<small class="text-muted text-uppercase tracking-wider fw-semibold">Avg. Completion</small>
</div>
</div>
<div class="col-md-3">
<div class="metric-card">
<div class="d-flex justify-content-between mb-3">
<div class="icon-box bg-danger bg-opacity-10 text-danger">
<span class="material-symbols-outlined">pending_actions</span>
</div>
<span class="badge bg-danger text-uppercase p-1 px-2" style="font-size: 0.6rem;">Urgent</span>
</div>
<h3 class="font-headline fw-bold text-dark mb-0">24</h3>
<small class="text-muted text-uppercase tracking-wider fw-semibold">Pending Reviews</small>
</div>
</div>
<div class="col-md-3">
<div class="metric-card bg-primary text-white shadow-lg shadow-sm">
<small class="text-uppercase opacity-75 fw-bold" style="font-size: 0.6rem; letter-spacing: 1px;">Global Health Index</small>
<h3 class="font-headline fw-bold fst-italic mb-3">Grade A-</h3>
<div class="d-flex align-items-center gap-2">
<div class="progress flex-grow-1 progress-slim bg-white bg-opacity-25">
<div class="progress-bar bg-info" style="width: 92%"></div>
</div>
<span class="small fw-bold">92%</span>
</div>
</div>
</div>
</div>
<!-- Charts & Lists Section -->
<div class="row g-4 mb-5">
<!-- Compliance Distribution -->
<div class="col-lg-8">
<div class="card border-0 rounded-4 shadow-sm bg-light p-4 h-100">
<div class="d-flex justify-content-between align-items-start mb-4">
<div>
<h4 class="font-headline fw-bold mb-1">Compliance Distribution</h4>
<p class="text-muted small mb-0">School health categorization based on latest audits.</p>
</div>
<select class="form-select form-select-sm w-auto bg-white border-0 shadow-sm fw-bold">
<option>Last 30 Days</option>
<option>Last Quarter</option>
</select>
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
<!-- Report Insights -->
<div class="col-lg-4">
<div class="d-flex flex-column gap-3 h-100">
<h5 class="font-headline fw-bold mb-1">Report Insights</h5>
<div class="card border-0 shadow-sm p-3 report-item bg-white" style="border-left-color: var(--bs-primary) !important;">
<div class="d-flex align-items-center gap-3">
<span class="material-symbols-outlined text-primary">account_balance</span>
<span class="fw-bold small">Infrastructure Audit</span>
</div>
</div>
<div class="card border-0 shadow-sm p-3 report-item bg-white" style="border-left-color: var(--tertiary) !important;">
<div class="d-flex align-items-center gap-3">
<span class="material-symbols-outlined" style="color: var(--tertiary);">menu_book</span>
<span class="fw-bold small">Curriculum Compliance</span>
</div>
</div>
<div class="card border-0 shadow-sm p-3 report-item bg-white" style="border-left-color: #64748b !important;">
<div class="d-flex align-items-center gap-3">
<span class="material-symbols-outlined text-secondary">groups</span>
<span class="fw-bold small">Faculty Performance</span>
</div>
</div>
<!-- Pro Tip -->
<div class="card border-0 rounded-4 overflow-hidden position-relative p-4 mt-auto">
<img alt="Desk" class="position-absolute w-100 h-100 top-0 start-0 opacity-10 object-fit-cover grayscale" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBZ-cVFhwX0EzwG7W-3l8_8Q8qlFmwf9YFwLYvKIduohKUMJda35VHXshbpdQPGogyHP8fXWQA1XEnAqLZ-H2Qj6iUY-khZAkn83He3BIZtNBiDilDDW3PbPhnP6U0eD-yeV6YoOukSiPuv766pTUB-ZANiDVbrxQFMCYJq0IYoRoJ_Piwc08EyhkcduL_kB7FC2f2qfV7LfMyNp76S_LQnE7pH4UOIEDioqbX1Np1Wz2BBX-VM_m3VCOOyuzfWgC9zGML9O7puvc-i" style="z-index: 0;"/>
<div class="position-relative" style="z-index: 1;">
<span class="badge bg-primary-subtle text-primary mb-2 fw-bold" style="font-size: 0.65rem;">PRO TIP</span>
<p class="small text-dark fw-medium mb-0">Annual comparisons for regional schools are now available in the expanded analytics suite.</p>
</div>
</div>
</div>
</div>
</div>
<!-- Table Section -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
<div class="p-4 bg-light bg-opacity-50 d-flex justify-content-between align-items-center">
<div>
<h4 class="font-headline fw-bold mb-1">Performance Leaderboard</h4>
<p class="text-muted small mb-0">Top ranked institutions by composite audit scores.</p>
</div>
<a class="text-primary fw-bold text-decoration-none small d-flex align-items-center gap-2" href="#">
                        View Full Ranking
                        <span class="material-symbols-outlined fs-6">arrow_forward</span>
</a>
</div>
<div class="table-responsive">
<table class="table table-hover align-middle mb-0">
<thead class="bg-light bg-opacity-25">
<tr>
<th class="px-4 py-3 text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">Rank</th>
<th class="py-3 text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">School Name</th>
<th class="py-3 text-center text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">Score</th>
<th class="py-3 text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">Status</th>
<th class="px-4 py-3 text-center text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">Trend</th>
</tr>
</thead>
<tbody>
<tr>
<td class="px-4 fw-bold font-headline text-dark">#01</td>
<td>
<div class="d-flex align-items-center gap-3">
<div class="icon-box bg-primary bg-opacity-10 text-primary small fw-bold">SV</div>
<div>
<p class="mb-0 fw-bold small text-dark">St. Valley International</p>
<p class="mb-0 text-muted" style="font-size: 0.65rem;">High School • Jakarta</p>
</div>
</div>
</td>
<td class="text-center"><span class="fw-bold text-primary">98.4</span></td>
<td><span class="badge rounded-pill bg-success-subtle text-success text-uppercase fw-bold" style="font-size: 0.65rem; padding: 0.35rem 0.8rem;">Exemplary</span></td>
<td class="text-center"><span class="material-symbols-outlined text-success">trending_up</span></td>
</tr>
<tr>
<td class="px-4 fw-bold font-headline text-dark">#02</td>
<td>
<div class="d-flex align-items-center gap-3">
<div class="icon-box bg-info bg-opacity-10 text-info small fw-bold">NH</div>
<div>
<p class="mb-0 fw-bold small text-dark">North Hill Academy</p>
<p class="mb-0 text-muted" style="font-size: 0.65rem;">Middle School • Bandung</p>
</div>
</div>
</td>
<td class="text-center"><span class="fw-bold text-primary">94.1</span></td>
<td><span class="badge rounded-pill bg-success-subtle text-success text-uppercase fw-bold" style="font-size: 0.65rem; padding: 0.35rem 0.8rem;">Exemplary</span></td>
<td class="text-center"><span class="material-symbols-outlined text-success">trending_up</span></td>
</tr>
<tr>
<td class="px-4 fw-bold font-headline text-dark">#03</td>
<td>
<div class="d-flex align-items-center gap-3">
<div class="icon-box bg-secondary bg-opacity-10 text-secondary small fw-bold">CM</div>
<div>
<p class="mb-0 fw-bold small text-dark">Crescent Moon Primary</p>
<p class="mb-0 text-muted" style="font-size: 0.65rem;">Elementary • Surabaya</p>
</div>
</div>
</td>
<td class="text-center"><span class="fw-bold text-primary">89.8</span></td>
<td><span class="badge rounded-pill bg-primary-subtle text-primary text-uppercase fw-bold" style="font-size: 0.65rem; padding: 0.35rem 0.8rem;">Proficient</span></td>
<td class="text-center"><span class="material-symbols-outlined text-muted">trending_flat</span></td>
</tr>
</tbody>
</table>
</div>
</div>
</div>
</main>
<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body></html>