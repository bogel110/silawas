<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>SILAWAS - @yield('title', 'School Supervision System')</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <style>
        :root {
            --bs-primary: #005F73;
            --bs-body-font-family: 'Inter', sans-serif;
            --bs-body-bg: #f8f9ff;
            --tertiary: #004930;
        }

        body { font-family: var(--bs-body-font-family); background-color: var(--bs-body-bg); }
        .font-headline { font-family: 'Manrope', sans-serif; }

        .sidebar {
            width: 280px; height: 100vh; position: fixed; left: 0; top: 0; z-index: 1030;
            background-color: #f1f5f9; border-right: 1px solid rgba(0,0,0,0.05); display: flex; flex-direction: column;
        }
        .nav-link {
            color: #64748b; font-weight: 500; padding: 0.75rem 1.5rem; text-transform: uppercase;
            letter-spacing: 0.05em; font-size: 0.85rem; display: flex; align-items: center; transition: all 0.2s ease;
        }
        .nav-link:hover { color: var(--bs-primary); background-color: rgba(0, 95, 115, 0.05); }
        .nav-link.active {
            color: var(--bs-primary); font-weight: 700; background-color: rgba(0, 95, 115, 0.08); border-right: 4px solid var(--bs-primary);
        }

        .main-content { margin-left: 280px; min-height: 100vh; }
        .top-navbar { height: 70px; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); z-index: 1020; }
        .avatar-img { width: 40px; height: 40px; object-fit: cover; border-radius: 50%; border: 2px solid rgba(0, 95, 115, 0.1); }
        
        .metric-card { border: none; border-radius: 1rem; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.03); padding: 1.5rem; background: #fff; }
        .btn-primary { background-color: var(--bs-primary); border-color: var(--bs-primary); border-radius: 50px; padding: 0.5rem 1.5rem; font-weight: 600; }
        .btn-outline-primary { color: var(--bs-primary); border-color: rgba(0, 95, 115, 0.2); border-radius: 50px; padding: 0.5rem 1.5rem; }
        .icon-box { width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; }
        .chart-container { height: 250px; display: flex; align-items: flex-end; gap: 1.5rem; padding: 1rem; }
        .bar { flex: 1; border-radius: 8px 8px 0 0; transition: opacity 0.2s; }
        .report-item { border-left: 4px solid var(--bs-primary); transition: transform 0.2s; cursor: pointer; }
    </style>
</head>
<body>

    <aside class="sidebar p-4">
        <div class="mb-5 px-3">
            <h1 class="h4 font-headline fw-bold text-primary mb-0">SILAWAS</h1>
            <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.65rem; letter-spacing: 1.5px;">School Supervision</small>
        </div>
        <nav class="nav flex-column gap-1">
            <a class="nav-link {{ request()->is('/') || request()->is('dashboard') ? 'active' : '' }}" href="{{ url('/') }}">
                <span class="material-symbols-outlined me-3">dashboard</span> Dashboard
            </a>
            <a class="nav-link" href="#">
                <span class="material-symbols-outlined me-3">domain</span> Data Sekolah
            </a>
        </nav>
    </aside>

    <main class="main-content">
        <header class="top-navbar sticky-top border-bottom d-flex align-items-center px-4">
            <div class="container-fluid d-flex align-items-center justify-content-between">
                <div class="search-bar w-50">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><span class="material-symbols-outlined text-muted fs-5">search</span></span>
                        <input class="form-control bg-light border-0 shadow-none rounded-end" placeholder="Cari data..." type="text"/>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="text-end d-none d-md-block">
                        <p class="mb-0 fw-bold small text-dark">{{ auth()->user()->name }}</p>
                        <small class="text-muted text-uppercase" style="font-size: 0.65rem;">
                            {{ str_replace('_', ' ', auth()->user()->role) }}
                        </small>
                    </div>
                    <img alt="Profile" class="avatar-img" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=005F73&color=fff"/>
                    
                    <form method="POST" action="{{ route('logout') }}" class="ms-2">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1 border-0" title="Logout">
                            <span class="material-symbols-outlined fs-5">logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <div class="p-5 container-fluid">
            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>