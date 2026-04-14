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

        body { font-family: var(--bs-body-font-family); background-color: var(--bs-body-bg); overflow-x: hidden; }
        .font-headline { font-family: 'Manrope', sans-serif; }

        /* SIDEBAR DEFAULT (DESKTOP) */
        .sidebar {
            width: 280px; height: 100vh; position: fixed; left: 0; top: 0; z-index: 1030;
            background-color: #f1f5f9; border-right: 1px solid rgba(0,0,0,0.05); display: flex; flex-direction: column;
            transition: transform 0.3s ease-in-out;
        }
        .nav-link {
            color: #64748b; font-weight: 500; padding: 0.75rem 1.5rem; text-transform: uppercase;
            letter-spacing: 0.05em; font-size: 0.85rem; display: flex; align-items: center; transition: all 0.2s ease;
        }
        .nav-link:hover { color: var(--bs-primary); background-color: rgba(0, 95, 115, 0.05); }
        .nav-link.active {
            color: var(--bs-primary); font-weight: 700; background-color: rgba(0, 95, 115, 0.08); border-right: 4px solid var(--bs-primary);
        }

        /* MAIN CONTENT DEFAULT (DESKTOP) */
        .main-content { 
            margin-left: 280px; 
            min-height: 100vh; 
            transition: margin-left 0.3s ease-in-out;
        }
        
        .top-navbar { height: 70px; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); z-index: 1020; }
        .avatar-img { width: 40px; height: 40px; object-fit: cover; border-radius: 50%; border: 2px solid rgba(0, 95, 115, 0.1); }
        
        .metric-card { border: none; border-radius: 1rem; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.03); padding: 1.5rem; background: #fff; }
        .btn-primary { background-color: var(--bs-primary); border-color: var(--bs-primary); border-radius: 50px; padding: 0.5rem 1.5rem; font-weight: 600; }
        .btn-outline-primary { color: var(--bs-primary); border-color: rgba(0, 95, 115, 0.2); border-radius: 50px; padding: 0.5rem 1.5rem; }
        .icon-box { width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; }
        
        /* OVERLAY UNTUK HP */
        .sidebar-overlay {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
            background-color: rgba(0, 0, 0, 0.5); z-index: 1025;
            display: none; opacity: 0; transition: opacity 0.3s ease-in-out;
        }
        .sidebar-overlay.show { display: block; opacity: 1; }

        /* RESPONSIVE CSS (HP & TABLET) */
        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); } /* Sembunyikan sidebar ke kiri */
            .sidebar.show { transform: translateX(0); box-shadow: 4px 0 10px rgba(0,0,0,0.1); } /* Tampilkan saat ditoggle */
            
            .main-content { margin-left: 0; } /* Konten utama penuh layarnya */
            
            .p-5.container-fluid { padding: 1.5rem !important; } /* Kurangi padding di HP */
        }
        
        /* SEMBUNYIKAN TOMBOL TOGGLE DI DESKTOP */
        @media (min-width: 992px) {
            .btn-toggle-sidebar { display: none !important; }
        }
    </style>
</head>
<body>

    {{-- Latar belakang gelap saat menu terbuka di HP --}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <aside class="sidebar p-4" id="sidebar">
        <div class="mb-5 px-3 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h4 font-headline fw-bold text-primary mb-0">SILAWAS</h1>
                <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.65rem; letter-spacing: 1.5px;">School Supervision</small>
            </div>
            {{-- Tombol Close X hanya tampil di HP --}}
            <button class="btn btn-link text-dark p-0 d-lg-none" id="btnCloseSidebar">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <ul class="nav flex-column gap-1 mb-auto">
            <li class="nav-item">
                <a class="nav-link {{ request()->is('/') || request()->is('dashboard') ? 'active' : '' }}" href="{{ url('/') }}">
                    <span class="material-symbols-outlined me-3">dashboard</span> Dashboard
                </a>
            </li>
            @if(auth()->user()->role === 'pengawas')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active fw-bold' : '' }}" href="{{ route('admin.users.index') }}">
                        <span class="material-symbols-outlined me-3">manage_accounts</span> Administrator
                    </a>
                </li>
            @endif
        </ul>
    </aside>

    <main class="main-content">
        <header class="top-navbar sticky-top border-bottom d-flex align-items-center px-3 px-md-4">
            <div class="container-fluid d-flex align-items-center justify-content-between p-0">
                
                {{-- Kiri: Tombol Hamburger Menu (Hanya di HP) + Search --}}
                <div class="d-flex align-items-center gap-2 w-50">
                    <button class="btn btn-light border-0 p-2 d-flex align-items-center justify-content-center btn-toggle-sidebar" id="btnOpenSidebar">
                        <span class="material-symbols-outlined fs-5">menu</span>
                    </button>
                    
                    <div class="search-bar w-100 d-none d-md-block">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><span class=""></span></span>
                        </div>
                    </div>
                </div>

                {{-- Kanan: Profil User & Logout --}}
                <div class="d-flex align-items-center gap-2 gap-md-3">
                    <div class="text-end d-none d-sm-block">
                        <p class="mb-0 fw-bold small text-dark lh-1">{{ auth()->user()->name }}</p>
                        <small class="text-muted text-uppercase" style="font-size: 0.65rem;">
                            {{ str_replace('_', ' ', auth()->user()->role) }}
                        </small>
                    </div>
                    <img alt="Profile" class="avatar-img" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=005F73&color=fff"/>
                    
                    <form method="POST" action="{{ route('logout') }}" class="ms-1 ms-md-2 m-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1 border-0 px-2" title="Logout">
                            <span class="material-symbols-outlined fs-5">logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <div class="p-4 p-md-5 container-fluid">
            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    {{-- Script untuk mengaktifkan fungsi buka-tutup Sidebar di Mobile --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const btnOpen = document.getElementById('btnOpenSidebar');
            const btnClose = document.getElementById('btnCloseSidebar');
            const overlay = document.getElementById('sidebarOverlay');

            function toggleSidebar() {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            }

            if(btnOpen) btnOpen.addEventListener('click', toggleSidebar);
            if(btnClose) btnClose.addEventListener('click', toggleSidebar);
            if(overlay) overlay.addEventListener('click', toggleSidebar);
        });
    </script>
</body>
</html>