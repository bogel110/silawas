<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>SILAWAS - @yield('title', 'School Supervision System')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    <script>
        (function () {
            try {
                const savedTheme = localStorage.getItem('silawas-theme');
                const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';

                document.documentElement.dataset.theme = savedTheme || systemTheme;
            } catch (error) {
                document.documentElement.dataset.theme = 'light';
            }
        })();
    </script>

    <style>
        :root {
            color-scheme: light;
            --brand-900: #0b3c49;
            --brand-800: #0f5564;
            --brand-700: #0f6b7d;
            --brand-100: #dff3f5;
            --accent: #f4a261;
            --accent-soft: #fff0de;
            --surface: #ffffff;
            --surface-soft: #f5f8fb;
            --surface-muted: #eef3f7;
            --line: rgba(11, 60, 73, 0.08);
            --text-main: #18323a;
            --text-soft: #667d85;
            --shadow-soft: 0 16px 40px rgba(15, 85, 100, 0.08);
            --shadow-card: 0 12px 30px rgba(24, 50, 58, 0.07);
            --radius-xl: 28px;
            --radius-lg: 22px;
            --radius-md: 16px;
            --bs-primary: var(--brand-700);
            --bs-primary-rgb: 15, 107, 125;
            --bs-body-font-family: 'Plus Jakarta Sans', sans-serif;
            --bs-body-color: var(--text-main);
            --bs-body-bg: linear-gradient(180deg, #f8fbfc 0%, #f3f7fa 100%);
        }

        html[data-theme="dark"] {
            color-scheme: dark;
            --brand-900: #d7f5f8;
            --brand-800: #9ae7ef;
            --brand-700: #63c7d2;
            --brand-100: rgba(99, 199, 210, 0.14);
            --accent-soft: rgba(244, 162, 97, 0.14);
            --surface: #0f2329;
            --surface-soft: #112a31;
            --surface-muted: #173740;
            --line: rgba(180, 221, 227, 0.12);
            --text-main: #e7f3f5;
            --text-soft: #9fb8bf;
            --shadow-soft: 0 18px 44px rgba(0, 0, 0, 0.28);
            --shadow-card: 0 14px 34px rgba(0, 0, 0, 0.24);
            --bs-primary: #63c7d2;
            --bs-primary-rgb: 99, 199, 210;
            --bs-body-color: var(--text-main);
            --bs-body-bg: linear-gradient(180deg, #07191e 0%, #0b2026 100%);
        }

        body {
            min-height: 100vh;
            font-family: var(--bs-body-font-family);
            color: var(--text-main);
            background: var(--bs-body-bg);
            overflow-x: hidden;
        }

        .font-headline {
            font-family: 'Manrope', sans-serif;
        }

        .sidebar {
            width: 292px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1030;
            display: flex;
            flex-direction: column;
            padding: 1.5rem;
            background:
                radial-gradient(circle at top left, rgba(244, 162, 97, 0.18), transparent 36%),
                linear-gradient(180deg, #f8fbfc 0%, #eef4f7 100%);
            border-right: 1px solid var(--line);
            transition: transform 0.3s ease-in-out;
        }

        .sidebar-shell {
            height: 100%;
            display: flex;
            flex-direction: column;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.85);
            border-radius: 30px;
            box-shadow: var(--shadow-soft);
            backdrop-filter: blur(18px);
            overflow-y: auto; /* Kunci agar bisa di-scroll ke bawah */
        }
        /* ===== INI TAMBAHAN NOMOR 2 MULAI DARI SINI ===== */
        .sidebar-shell::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar-shell::-webkit-scrollbar-track {
            background: transparent;
            margin: 15px 0; 
        }
        .sidebar-shell::-webkit-scrollbar-thumb {
            background-color: rgba(15, 107, 125, 0.15); 
            border-radius: 10px;
        }
        .sidebar-shell:hover::-webkit-scrollbar-thumb {
            background-color: rgba(15, 107, 125, 0.4); 
        }
        /* ===== AKHIR TAMBAHAN NOMOR 2 ===== */

        .sidebar-brand {
            padding: 0.85rem 0.95rem 1.25rem;
            border-bottom: 1px solid var(--line);
        }

        .sidebar-brand {
            padding: 0.85rem 0.95rem 1.25rem;
            border-bottom: 1px solid var(--line);
        }

        .brand-mark {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--brand-800), var(--brand-700));
            color: #fff;
            box-shadow: 0 14px 24px rgba(15, 107, 125, 0.22);
        }

        .nav-section-label {
            margin: 1.4rem 0 0.8rem;
            padding: 0 0.95rem;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: #86a0a8;
        }

        .sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: 0.9rem;
            margin-bottom: 0.35rem;
            padding: 0.95rem 1rem;
            border-radius: 18px;
            color: var(--text-soft);
            font-size: 0.92rem;
            font-weight: 700;
            transition: 0.2s ease;
        }

        .sidebar .nav-link .material-symbols-outlined {
            font-size: 1.3rem;
        }

        .sidebar .nav-link:hover {
            color: var(--brand-700);
            background: rgba(15, 107, 125, 0.08);
            transform: translateX(2px);
        }

        .sidebar .nav-link.active {
            color: #fff;
            background: linear-gradient(135deg, var(--brand-800), var(--brand-700));
            box-shadow: 0 14px 22px rgba(15, 107, 125, 0.2);
        }

        .sidebar-footer {
            margin-top: auto;
            padding: 1rem;
            background: linear-gradient(135deg, rgba(15, 107, 125, 0.08), rgba(244, 162, 97, 0.12));
            border-radius: 22px;
        }

        .main-content {
            margin-left: 292px;
            min-height: 100vh;
            transition: margin-left 0.3s ease-in-out;
        }

        .top-navbar {
            position: sticky;
            top: 0;
            z-index: 1020;
            padding: 1.15rem 1.5rem 0;
            background: transparent;
        }

        .top-navbar-inner {
            min-height: 78px;
            padding: 0.95rem 1.25rem;
            background: rgba(255, 255, 255, 0.74);
            border: 1px solid rgba(255, 255, 255, 0.9);
            border-radius: 26px;
            box-shadow: var(--shadow-soft);
            backdrop-filter: blur(18px);
        }

        .navbar-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.45rem 0.8rem;
            border-radius: 999px;
            background: var(--brand-100);
            color: var(--brand-800);
            font-size: 0.76rem;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .avatar-img {
            width: 46px;
            height: 46px;
            object-fit: cover;
            border-radius: 16px;
            border: 3px solid rgba(255, 255, 255, 0.9);
            box-shadow: 0 10px 24px rgba(15, 107, 125, 0.16);
        }

        .profile-toggle {
            display: inline-flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.45rem 0.55rem 0.45rem 0.7rem;
            border: 1px solid rgba(15, 107, 125, 0.08);
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 8px 22px rgba(15, 107, 125, 0.08);
            color: var(--text-main);
        }

        .profile-toggle:hover,
        .profile-toggle:focus,
        .profile-toggle.show {
            color: var(--text-main);
            background: #fff;
            border-color: rgba(15, 107, 125, 0.14);
        }

        .profile-toggle::after {
            margin-left: 0.1rem;
            color: var(--text-soft);
        }

        .profile-menu {
            min-width: 240px;
            margin-top: 0.8rem !important;
            padding: 0.6rem;
            border: 1px solid rgba(15, 107, 125, 0.08);
            border-radius: 18px;
            background: #ffffff;
            box-shadow: 0 18px 36px rgba(24, 50, 58, 0.12);
            z-index: 1090;
        }

        .profile-menu .dropdown-item,
        .profile-menu .dropdown-header {
            border-radius: 14px;
        }

        .profile-menu .dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            padding: 0.75rem 0.85rem;
            font-weight: 600;
            color: var(--text-main);
        }

        .profile-menu .dropdown-item:hover,
        .profile-menu .dropdown-item:focus {
            background: rgba(15, 107, 125, 0.07);
            color: var(--brand-700);
        }

        .profile-menu .dropdown-divider {
            margin: 0.45rem 0;
            border-color: rgba(15, 107, 125, 0.08);
        }

        .btn-toggle-sidebar,
        .btn-soft,
        .btn-outline-soft {
            border-radius: 16px;
        }

        .btn-toggle-sidebar,
        .btn-soft {
            border: 1px solid rgba(15, 107, 125, 0.08);
            background: #fff;
            color: var(--brand-700);
            box-shadow: 0 8px 22px rgba(15, 107, 125, 0.08);
        }

        .btn-soft:hover,
        .btn-toggle-sidebar:hover {
            color: var(--brand-800);
            background: #fff;
        }

        .btn-primary {
            border: none;
            border-radius: 999px;
            padding: 0.7rem 1.3rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--brand-800), var(--brand-700));
            box-shadow: 0 14px 26px rgba(15, 107, 125, 0.2);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--brand-900), var(--brand-800));
        }

        .btn-outline-primary,
        .btn-outline-secondary,
        .btn-outline-danger {
            border-radius: 999px;
            font-weight: 700;
        }

        .page-shell {
            padding: 1.25rem 1.5rem 2rem;
        }

        .content-panel,
        .metric-card,
        .card,
        .table-panel {
            border: 1px solid rgba(255, 255, 255, 0.95) !important;
            border-radius: var(--radius-lg) !important;
            box-shadow: var(--shadow-card) !important;
        }

        .content-panel,
        .metric-card,
        .table-panel {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(10px);
        }

        .metric-card {
            position: relative;
            overflow: hidden;
            height: 100%;
            padding: 1.5rem;
        }

        .metric-card::after {
            content: '';
            position: absolute;
            inset: auto -40px -50px auto;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(244, 162, 97, 0.16), rgba(15, 107, 125, 0.08));
        }

        .metric-card.highlight-card {
            background: linear-gradient(135deg, var(--brand-900), var(--brand-700));
            color: #fff;
        }

        .metric-card.highlight-card .text-muted,
        .metric-card.highlight-card .metric-meta {
            color: rgba(255, 255, 255, 0.72) !important;
        }

        .icon-box {
            width: 52px;
            height: 52px;
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(15, 107, 125, 0.14), rgba(15, 107, 125, 0.06));
            color: var(--brand-700);
        }

        .hero-panel {
            position: relative;
            overflow: hidden;
            padding: 1.75rem;
            border-radius: var(--radius-xl);
            background:
                radial-gradient(circle at top right, rgba(244, 162, 97, 0.22), transparent 24%),
                linear-gradient(135deg, rgba(255, 255, 255, 0.96), rgba(240, 247, 250, 0.94));
            border: 1px solid rgba(255, 255, 255, 0.9);
            box-shadow: var(--shadow-soft);
        }

        .hero-panel::before {
            content: '';
            position: absolute;
            right: -80px;
            top: -90px;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            background: rgba(15, 107, 125, 0.06);
        }

        .hero-panel > * {
            position: relative;
            z-index: 1;
        }

        .section-kicker {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.45rem 0.8rem;
            border-radius: 999px;
            background: rgba(15, 107, 125, 0.08);
            color: var(--brand-800);
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .eyebrow-muted {
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #8ba0a6;
        }

        .text-soft {
            color: var(--text-soft) !important;
        }

        .card,
        .table-panel {
            background: rgba(255, 255, 255, 0.92);
        }

        .card-header {
            background: transparent !important;
        }

        .table-panel-header {
            padding: 1.4rem;
            border-bottom: 1px solid rgba(15, 107, 125, 0.08);
            background: linear-gradient(180deg, rgba(245, 248, 251, 0.92), rgba(255, 255, 255, 0.96));
        }

        .table {
            --bs-table-bg: transparent;
            margin-bottom: 0;
        }

        .table thead th {
            padding-top: 1rem;
            padding-bottom: 1rem;
            border-bottom-width: 1px;
            border-color: rgba(15, 107, 125, 0.08);
            color: #789098;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .table tbody td {
            padding-top: 1rem;
            padding-bottom: 1rem;
            border-color: rgba(15, 107, 125, 0.06);
        }

        .table tbody tr:hover {
            --bs-table-hover-bg: rgba(15, 107, 125, 0.03);
        }

        .form-control,
        .form-select,
        .input-group-text,
        textarea.form-control {
            border-color: rgba(15, 107, 125, 0.1);
            border-radius: 14px;
            min-height: 46px;
            background-color: #fff;
            box-shadow: none !important;
        }

        textarea.form-control {
            min-height: 120px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: rgba(15, 107, 125, 0.3);
            box-shadow: 0 0 0 0.2rem rgba(15, 107, 125, 0.08) !important;
        }

        .input-group-text {
            color: #85a0a8;
        }

        .badge {
            border-radius: 999px;
            padding: 0.48rem 0.85rem;
            font-weight: 700;
        }

        .alert {
            border: none;
            border-radius: 18px;
            box-shadow: var(--shadow-card);
        }

        .list-group-item {
            padding-top: 0.95rem;
            padding-bottom: 0.95rem;
            border-color: rgba(15, 107, 125, 0.08);
        }

        .progress {
            border-radius: 999px;
            overflow: hidden;
        }

        .pagination .page-link {
            border: none;
            border-radius: 12px;
            margin: 0 0.15rem;
            color: var(--brand-700);
            font-weight: 700;
            box-shadow: 0 6px 18px rgba(15, 107, 125, 0.08);
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, var(--brand-800), var(--brand-700));
            color: #fff;
        }

        .modal {
            z-index: 1080;
        }

        .modal-backdrop {
            --bs-backdrop-zindex: 1070;
            --bs-backdrop-bg: #173a43;
            --bs-backdrop-opacity: 0.52;
        }

        .modal-backdrop.show {
            backdrop-filter: blur(3px);
        }

        .modal-content {
            background-color: rgba(255, 255, 255, 0.98);
        }

        .dropdown-menu {
            --bs-dropdown-zindex: 1090;
        }

        .sidebar-overlay {
            position: fixed;
            inset: 0;
            z-index: 1025;
            display: none;
            opacity: 0;
            background: rgba(11, 60, 73, 0.38);
            transition: opacity 0.3s ease-in-out;
        }

        .sidebar-overlay.show {
            display: block;
            opacity: 1;
        }

        .theme-switch {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            min-height: 46px;
            padding: 0.45rem 0.85rem;
            border: 1px solid rgba(15, 107, 125, 0.08);
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 8px 22px rgba(15, 107, 125, 0.08);
            color: var(--brand-700);
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .theme-switch:hover,
        .theme-switch:focus {
            color: var(--brand-800);
            background: #fff;
            border-color: rgba(15, 107, 125, 0.14);
        }

        html[data-theme="dark"] .sidebar {
            background:
                radial-gradient(circle at top left, rgba(244, 162, 97, 0.1), transparent 36%),
                linear-gradient(180deg, #081b20 0%, #0d242b 100%);
        }

        html[data-theme="dark"] .sidebar-shell,
        html[data-theme="dark"] .top-navbar-inner {
            background: rgba(12, 35, 41, 0.86);
            border-color: rgba(180, 221, 227, 0.12);
        }

        html[data-theme="dark"] .sidebar-brand .text-primary {
            color: var(--brand-800) !important;
        }

        html[data-theme="dark"] .sidebar .nav-link:hover {
            background: rgba(99, 199, 210, 0.11);
        }

        html[data-theme="dark"] .sidebar-footer {
            background: linear-gradient(135deg, rgba(99, 199, 210, 0.12), rgba(244, 162, 97, 0.1));
        }

        html[data-theme="dark"] .profile-toggle,
        html[data-theme="dark"] .profile-toggle:hover,
        html[data-theme="dark"] .profile-toggle:focus,
        html[data-theme="dark"] .profile-toggle.show,
        html[data-theme="dark"] .theme-switch,
        html[data-theme="dark"] .theme-switch:hover,
        html[data-theme="dark"] .theme-switch:focus,
        html[data-theme="dark"] .btn-toggle-sidebar,
        html[data-theme="dark"] .btn-soft {
            background: rgba(17, 42, 49, 0.96);
            border-color: rgba(180, 221, 227, 0.14);
            color: var(--text-main);
        }

        html[data-theme="dark"] .profile-menu,
        html[data-theme="dark"] .dropdown-menu,
        html[data-theme="dark"] .bg-white,
        html[data-theme="dark"] .bg-light {
            background-color: #10262d !important;
            color: var(--text-main) !important;
        }

        html[data-theme="dark"] .content-panel,
        html[data-theme="dark"] .metric-card,
        html[data-theme="dark"] .card,
        html[data-theme="dark"] .table-panel {
            background: rgba(15, 35, 41, 0.92);
            border-color: rgba(180, 221, 227, 0.12) !important;
            color: var(--text-main);
        }

        html[data-theme="dark"] .hero-panel {
            background:
                radial-gradient(circle at top right, rgba(244, 162, 97, 0.12), transparent 24%),
                linear-gradient(135deg, rgba(15, 35, 41, 0.96), rgba(11, 32, 38, 0.94));
            border-color: rgba(180, 221, 227, 0.12);
        }

        html[data-theme="dark"] .table-panel-header {
            border-color: rgba(180, 221, 227, 0.12);
            background: linear-gradient(180deg, rgba(17, 42, 49, 0.95), rgba(15, 35, 41, 0.96));
        }

        html[data-theme="dark"] .table {
            --bs-table-bg: transparent;
            --bs-table-color: var(--text-main);
            --bs-table-hover-bg: rgba(99, 199, 210, 0.08);
            --bs-table-hover-color: var(--text-main);
            color: var(--text-main);
        }

        html[data-theme="dark"] .table thead th,
        html[data-theme="dark"] .table tbody td {
            border-color: rgba(180, 221, 227, 0.12);
        }

        html[data-theme="dark"] .form-control,
        html[data-theme="dark"] .form-select,
        html[data-theme="dark"] .input-group-text,
        html[data-theme="dark"] textarea.form-control {
            border-color: rgba(180, 221, 227, 0.15);
            background-color: #112a31;
            color: var(--text-main) !important;
            -webkit-text-fill-color: var(--text-main);
        }

        html[data-theme="dark"] .form-control::placeholder,
        html[data-theme="dark"] textarea.form-control::placeholder {
            color: #78939a;
            -webkit-text-fill-color: #78939a;
            opacity: 1;
        }

        html[data-theme="dark"] .form-label,
        html[data-theme="dark"] .form-check-label,
        html[data-theme="dark"] label,
        html[data-theme="dark"] legend,
        html[data-theme="dark"] .input-group-text {
            color: var(--text-main) !important;
        }

        html[data-theme="dark"] .form-control:disabled,
        html[data-theme="dark"] .form-control[readonly],
        html[data-theme="dark"] .form-select:disabled,
        html[data-theme="dark"] textarea.form-control:disabled,
        html[data-theme="dark"] textarea.form-control[readonly] {
            background-color: #173740 !important;
            color: #cde3e7 !important;
            -webkit-text-fill-color: #cde3e7;
            opacity: 1;
        }

        html[data-theme="dark"] .form-select option,
        html[data-theme="dark"] select option {
            background-color: #112a31;
            color: var(--text-main);
        }

        html[data-theme="dark"] input[type="date"],
        html[data-theme="dark"] input[type="month"],
        html[data-theme="dark"] input[type="time"],
        html[data-theme="dark"] input[type="datetime-local"] {
            color-scheme: dark;
        }

        html[data-theme="dark"] .form-control:focus,
        html[data-theme="dark"] .form-select:focus {
            border-color: rgba(99, 199, 210, 0.42);
            box-shadow: 0 0 0 0.2rem rgba(99, 199, 210, 0.12) !important;
        }

        html[data-theme="dark"] .list-group-item {
            background: transparent;
            border-color: rgba(180, 221, 227, 0.12);
            color: var(--text-main);
        }

        html[data-theme="dark"] .modal-content {
            background-color: rgba(15, 35, 41, 0.98);
            color: var(--text-main);
        }

        html[data-theme="dark"] .text-dark,
        html[data-theme="dark"] .text-muted,
        html[data-theme="dark"] .text-soft {
            color: var(--text-soft) !important;
        }

        html[data-theme="dark"] .text-primary {
            color: var(--brand-800) !important;
        }

        html[data-theme="dark"] input.form-control,
        html[data-theme="dark"] select.form-select,
        html[data-theme="dark"] textarea.form-control {
            background-color: #112a31 !important;
            color: var(--text-main) !important;
            -webkit-text-fill-color: var(--text-main) !important;
        }

        html[data-theme="dark"] input.form-control.text-muted,
        html[data-theme="dark"] select.form-select.text-muted,
        html[data-theme="dark"] textarea.form-control.text-muted,
        html[data-theme="dark"] input.form-control:disabled,
        html[data-theme="dark"] select.form-select:disabled,
        html[data-theme="dark"] textarea.form-control:disabled,
        html[data-theme="dark"] input.form-control[readonly],
        html[data-theme="dark"] textarea.form-control[readonly] {
            background-color: #173740 !important;
            color: #d7eaee !important;
            -webkit-text-fill-color: #d7eaee !important;
            opacity: 1;
        }

        html[data-theme="dark"] input.form-control::placeholder,
        html[data-theme="dark"] textarea.form-control::placeholder {
            color: #9fb8bf !important;
            -webkit-text-fill-color: #9fb8bf !important;
            opacity: 1;
        }

        html[data-theme="dark"] select,
        html[data-theme="dark"] select.form-select,
        html[data-theme="dark"] .modal select,
        html[data-theme="dark"] .modal select.form-select {
            background-color: #112a31 !important;
            color: #e7f3f5 !important;
            -webkit-text-fill-color: #e7f3f5 !important;
            border-color: rgba(180, 221, 227, 0.18) !important;
            color-scheme: dark;
        }

        html[data-theme="dark"] select option,
        html[data-theme="dark"] select.form-select option,
        html[data-theme="dark"] .modal select option,
        html[data-theme="dark"] .modal select.form-select option {
            background-color: #112a31 !important;
            color: #e7f3f5 !important;
            -webkit-text-fill-color: #e7f3f5 !important;
        }

        html[data-theme="dark"] select option:checked,
        html[data-theme="dark"] select option:hover {
            background-color: #1d4b56 !important;
            color: #ffffff !important;
        }

        html[data-theme="dark"] .choices,
        html[data-theme="dark"] .choices__inner,
        html[data-theme="dark"] .choices__list,
        html[data-theme="dark"] .choices__list--single,
        html[data-theme="dark"] .choices__list--dropdown,
        html[data-theme="dark"] .choices__list[aria-expanded] {
            background-color: #112a31 !important;
            color: #e7f3f5 !important;
        }

        html[data-theme="dark"] .choices__inner,
        html[data-theme="dark"] .choices__list--dropdown,
        html[data-theme="dark"] .choices__list[aria-expanded] {
            border-color: rgba(180, 221, 227, 0.18) !important;
        }

        html[data-theme="dark"] .choices__item,
        html[data-theme="dark"] .choices__list--single .choices__item,
        html[data-theme="dark"] .choices__list--dropdown .choices__item,
        html[data-theme="dark"] .choices__list[aria-expanded] .choices__item,
        html[data-theme="dark"] .choices__placeholder {
            color: #e7f3f5 !important;
            opacity: 1 !important;
        }

        html[data-theme="dark"] .choices__input,
        html[data-theme="dark"] .choices[data-type*="select-one"] .choices__input {
            background-color: #173740 !important;
            border-color: rgba(180, 221, 227, 0.18) !important;
            color: #e7f3f5 !important;
            -webkit-text-fill-color: #e7f3f5 !important;
        }

        html[data-theme="dark"] .choices__input::placeholder {
            color: #9fb8bf !important;
            -webkit-text-fill-color: #9fb8bf !important;
            opacity: 1 !important;
        }

        html[data-theme="dark"] .choices__list--dropdown .choices__item--selectable.is-highlighted,
        html[data-theme="dark"] .choices__list[aria-expanded] .choices__item--selectable.is-highlighted {
            background-color: #1d7784 !important;
            color: #ffffff !important;
        }

        html[data-theme="dark"] .choices[data-type*="select-one"]::after {
            border-color: #e7f3f5 transparent transparent transparent !important;
        }

        html[data-theme="dark"] .choices[data-type*="select-one"].is-open::after {
            border-color: transparent transparent #e7f3f5 transparent !important;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .top-navbar {
                padding: 1rem 1rem 0;
            }

            .page-shell {
                padding: 1rem 1rem 1.5rem;
            }

            .top-navbar-inner {
                padding: 0.9rem 1rem;
                border-radius: 22px;
            }

            .hero-panel {
                padding: 1.35rem;
            }
        }

        @media (min-width: 992px) {
            .btn-toggle-sidebar {
                display: none !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-shell">
            <div class="sidebar-brand d-flex justify-content-between align-items-start gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="brand-mark">
                        <span class="material-symbols-outlined">shield_person</span>
                    </div>
                    <div>
                        <h1 class="h4 font-headline fw-bold text-primary mb-0">SILAWAS</h1>
                        <small class="text-soft text-uppercase fw-semibold" style="font-size: 0.68rem; letter-spacing: 0.18em;">School Supervision</small>
                    </div>
                </div>
                <button class="btn btn-link text-dark p-0 d-lg-none" id="btnCloseSidebar" type="button">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="nav-section-label">Navigasi</div>
            <ul class="nav flex-column mb-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('/') || request()->is('dashboard') ? 'active' : '' }}" href="{{ url('/') }}">
                        <span class="material-symbols-outlined">dashboard</span>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('jurnal.index') ? 'active' : '' }}" href="{{ route('jurnal.index') }}">
                        <span class="material-symbols-outlined">book</span>
                        <span>Jurnal Harian Kepsek</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('kbm.index') ? 'active' : '' }}" href="{{ route('kbm.index') }}">
                        <span class="material-symbols-outlined">library_books</span>
                        <span>Perangkat KBM</span>
                    </a>
                </li>
                @if(auth()->user()->role === 'admin_sekolah')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('achievement.admin') ? 'active' : '' }}" href="{{ route('achievement.admin') }}">
                            <span class="material-symbols-outlined">emoji_events</span>
                            <span>Prestasi Sekolah</span>
                        </a>
                    </li>
                @endif
                @if(auth()->user()->role === 'pengawas')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('achievement.pengawas') ? 'active' : '' }}" href="{{ route('achievement.pengawas') }}">
                            <span class="material-symbols-outlined">social_leaderboard</span>
                            <span>Rekap Prestasi</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('strategy.*') ? 'active' : '' }}" href="{{ route('strategy.index') }}">
                            <span class="material-symbols-outlined">model_training</span>
                            <span>Strategi Pendampingan</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('mentoring.*') ? 'active' : '' }}" href="{{ route('mentoring.index') }}">
                            <div class="sb-nav-link-icon"><span class="material-symbols-outlined fs-6">sync</span></div>
                            Siklus Pendampingan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                            <span class="material-symbols-outlined">manage_accounts</span>
                            <span>Administrator</span>
                        </a>
                    </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('profile.password.edit') ? 'active' : '' }}" href="{{ route('profile.password.edit') }}">
                        <span class="material-symbols-outlined">lock_person</span>
                        <span>Ubah Password</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-footer">
                <div class="eyebrow-muted mb-2">Akun Aktif</div>
                <div class="fw-bold">{{ auth()->user()->name }}</div>
                <div class="text-soft small text-uppercase">{{ str_replace('_', ' ', auth()->user()->role) }}</div>
            </div>
            <div class="sidebar-footer text-soft small text-uppercase">
                <span>Copyright © SYP_2026</span>
            </div>
        </div>
    </aside>

    <main class="main-content">
        <header class="top-navbar">
            <div class="top-navbar-inner d-flex align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-toggle-sidebar p-2 d-flex align-items-center justify-content-center" id="btnOpenSidebar" type="button">
                        <span class="material-symbols-outlined fs-5">menu</span>
                    </button>
                    <div>
                        <div class="navbar-badge">
                            <span class="material-symbols-outlined" style="font-size: 1rem;">monitoring</span>
                            Panel Supervisi
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <button class="theme-switch" id="themeToggle" type="button" aria-label="Ganti tema tampilan">
                        <span class="material-symbols-outlined fs-5" id="themeToggleIcon">dark_mode</span>
                        <span class="d-none d-md-inline" id="themeToggleText">Dark</span>
                    </button>

                    <div class="dropdown">
                        <button class="btn profile-toggle dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="text-end d-none d-sm-block">
                                <p class="mb-0 fw-bold small text-dark lh-1">{{ auth()->user()->name }}</p>
                                <small class="text-soft text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.08em;">
                                    {{ str_replace('_', ' ', auth()->user()->role) }}
                                </small>
                            </div>
                            <img alt="Profile" class="avatar-img" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=0f6b7d&color=fff"/>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end profile-menu border-0">
                            <li class="dropdown-header px-3 py-2">
                                <div class="fw-bold">{{ auth()->user()->name }}</div>
                                <div class="text-soft small text-uppercase">{{ str_replace('_', ' ', auth()->user()->role) }}</div>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.password.edit') }}">
                                    <span class="material-symbols-outlined fs-6">lock_person</span>
                                    Ubah Password
                                </a>
                            </li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="m-0">
                                    @csrf
                                    <button class="dropdown-item text-danger" type="submit">
                                        <span class="material-symbols-outlined fs-6">logout</span>
                                        Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </header>

        <div class="page-shell">
            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const btnOpen = document.getElementById('btnOpenSidebar');
            const btnClose = document.getElementById('btnCloseSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const themeToggle = document.getElementById('themeToggle');
            const themeToggleIcon = document.getElementById('themeToggleIcon');
            const themeToggleText = document.getElementById('themeToggleText');

            function setTheme(theme) {
                const nextTheme = theme === 'dark' ? 'dark' : 'light';

                document.documentElement.dataset.theme = nextTheme;

                if (themeToggleIcon) {
                    themeToggleIcon.textContent = nextTheme === 'dark' ? 'light_mode' : 'dark_mode';
                }

                if (themeToggleText) {
                    themeToggleText.textContent = nextTheme === 'dark' ? 'Light' : 'Dark';
                }

                if (themeToggle) {
                    themeToggle.setAttribute('aria-label', nextTheme === 'dark' ? 'Aktifkan light mode' : 'Aktifkan dark mode');
                }

                try {
                    localStorage.setItem('silawas-theme', nextTheme);
                } catch (error) {
                    // Browser tertentu dapat memblokir localStorage.
                }
            }

            setTheme(document.documentElement.dataset.theme || 'light');

            if (themeToggle) {
                themeToggle.addEventListener('click', function() {
                    setTheme(document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark');
                });
            }

            function setSidebarState(show) {
                if (!sidebar || !overlay) {
                    return;
                }

                sidebar.classList.toggle('show', show);
                overlay.classList.toggle('show', show);
            }

            if (btnOpen) {
                btnOpen.addEventListener('click', function() {
                    setSidebarState(true);
                });
            }

            if (btnClose) {
                btnClose.addEventListener('click', function() {
                    setSidebarState(false);
                });
            }

            if (overlay) {
                overlay.addEventListener('click', function() {
                    setSidebarState(false);
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
