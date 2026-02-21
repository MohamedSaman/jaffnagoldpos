<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jewellery POS – {{ config('app.name') }}</title>
    <meta name="description" content="Jewellery Shop Management System – POS, Inventory, Sales & Reports">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @livewireStyles
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --gold: #C9A84C;
            --gold-light: #E8C96A;
            --gold-dark: #9A7A2E;
            --bg: #F8F9FA;
            --bg2: #FFFFFF;
            --bg3: #F1F5F9;
            --bg4: #E2E8F0;
            --sidebar-w: 240px;
            --sidebar-collapsed: 64px;
            --text: #1E293B;
            --text-muted: #64748B;
            --border: #E2E8F0;
            --success: #10B981;
            --danger: #EF4444;
            --warning: #F59E0B;
            --info: #3B82F6;
            --radius: 12px;
            --radius-sm: 8px;
            --shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── SIDEBAR ── */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--bg2);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
            transition: width .25s ease;
            overflow: hidden;
        }

        .sidebar-logo {
            padding: 20px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--border);
            min-height: 68px;
        }

        .sidebar-logo .logo-icon {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .sidebar-logo .logo-text {
            font-size: 15px;
            font-weight: 700;
            color: var(--gold);
            white-space: nowrap;
            line-height: 1.2;
        }

        .sidebar-logo .logo-sub {
            font-size: 10px;
            color: var(--text-muted);
            font-weight: 400;
        }

        .sidebar-nav {
            flex: 1;
            padding: 12px 8px;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .nav-section {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: .08em;
            color: var(--text-muted);
            padding: 12px 10px 6px;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: var(--radius-sm);
            color: var(--text-muted);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            transition: all .18s;
            white-space: nowrap;
            cursor: pointer;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }

        .nav-item:hover {
            background: var(--bg3);
            color: var(--text);
        }

        .nav-item.active {
            background: rgba(201,168,76,0.1);
            color: var(--gold-dark);
            border: 1px solid rgba(201,168,76,0.2);
        }

        .nav-item .nav-icon {
            font-size: 17px;
            flex-shrink: 0;
            width: 20px;
            text-align: center;
        }

        .nav-item .nav-label { white-space: nowrap; }

        .sidebar-footer {
            padding: 12px 8px;
            border-top: 1px solid var(--border);
        }

        /* ── MAIN ── */
        .main-wrap {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            transition: margin-left .25s ease;
        }

        .topbar {
            height: 68px;
            background: var(--bg2);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .topbar-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .topbar-date {
            font-size: 12px;
            color: var(--text-muted);
        }

        .avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            display: flex; align-items: center; justify-content: center;
            font-size: 14px;
            font-weight: 700;
            color: #fff;
            cursor: pointer;
        }

        .page-content {
            flex: 1;
            padding: 28px;
        }

        /* ── CARDS ── */
        .card {
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 24px;
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--text);
        }

        /* ── STAT CARDS ── */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px;
            position: relative;
            overflow: hidden;
            transition: transform .2s, box-shadow .2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
        }

        .stat-card.gold::before { background: linear-gradient(90deg, var(--gold), var(--gold-light)); }
        .stat-card.green::before { background: linear-gradient(90deg, #22C55E, #4ADE80); }
        .stat-card.blue::before { background: linear-gradient(90deg, #3B82F6, #60A5FA); }
        .stat-card.red::before { background: linear-gradient(90deg, #EF4444, #F87171); }
        .stat-card.purple::before { background: linear-gradient(90deg, #8B5CF6, #A78BFA); }

        .stat-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 26px;
            font-weight: 700;
            color: var(--text);
            line-height: 1;
        }

        .stat-sub {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 6px;
        }

        .stat-icon {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 36px;
            opacity: .12;
        }

        /* ── TABLE ── */
        .table-wrap {
            overflow-x: auto;
            border-radius: var(--radius-sm);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
        }

        thead th {
            background: var(--bg3);
            color: var(--text-muted);
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .06em;
            padding: 12px 16px;
            text-align: left;
            white-space: nowrap;
        }

        tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background .15s;
        }

        tbody tr:hover { background: var(--bg3); }
        tbody tr:last-child { border-bottom: none; }

        tbody td {
            padding: 13px 16px;
            color: var(--text);
            vertical-align: middle;
        }

        /* ── BUTTONS ── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all .18s;
            text-decoration: none;
            white-space: nowrap;
        }

        .btn-gold {
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            color: #fff;
        }

        .btn-gold:hover {
            background: linear-gradient(135deg, var(--gold-light), var(--gold));
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(201,168,76,0.35);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text-muted);
        }

        .btn-outline:hover {
            border-color: var(--gold);
            color: var(--gold);
        }

        .btn-danger {
            background: rgba(239,68,68,0.15);
            color: #F87171;
            border: 1px solid rgba(239,68,68,0.25);
        }

        .btn-danger:hover {
            background: rgba(239,68,68,0.25);
        }

        .btn-success {
            background: rgba(34,197,94,0.15);
            color: #4ADE80;
            border: 1px solid rgba(34,197,94,0.25);
        }

        .btn-sm { padding: 6px 12px; font-size: 12px; }
        .btn-icon { padding: 8px; border-radius: 8px; }

        /* ── FORMS ── */
        .form-group { margin-bottom: 16px; }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .form-control {
            width: 100%;
            background: var(--bg3);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--text);
            padding: 10px 14px;
            font-size: 13.5px;
            font-family: inherit;
            transition: border-color .18s, box-shadow .18s;
            outline: none;
        }

        .form-control:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(201,168,76,0.15);
        }

        .form-control::placeholder { color: var(--text-muted); }

        select.form-control option { background: var(--bg3); }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
        }

        /* ── MODAL ── */
        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.4);
            backdrop-filter: blur(4px);
            z-index: 200;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal {
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: 16px;
            width: 100%;
            max-width: 560px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 24px 64px rgba(0,0,0,0.15);
            animation: modalIn .2s ease;
        }

        .modal-lg { max-width: 800px; }
        .modal-xl { max-width: 1100px; }

        @keyframes modalIn {
            from { opacity: 0; transform: translateY(20px) scale(.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text);
        }

        .modal-body { padding: 24px; }

        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn-close {
            background: var(--bg3);
            border: 1px solid var(--border);
            color: var(--text-muted);
            width: 32px; height: 32px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            font-size: 16px;
            transition: all .18s;
        }

        .btn-close:hover { background: var(--bg4); color: var(--text); }

        /* ── BADGES ── */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-gold { background: rgba(201,168,76,0.12); color: var(--gold-dark); }
        .badge-green { background: rgba(34,197,94,0.15); color: #4ADE80; }
        .badge-red { background: rgba(239,68,68,0.15); color: #F87171; }
        .badge-blue { background: rgba(59,130,246,0.15); color: #60A5FA; }
        .badge-gray { background: rgba(0,0,0,0.06); color: var(--text-muted); }

        /* ── SEARCH ── */
        .search-wrap {
            position: relative;
        }

        .search-wrap .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 15px;
        }

        .search-wrap .form-control {
            padding-left: 38px;
        }

        /* ── ALERTS ── */
        .alert {
            padding: 12px 16px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success { background: rgba(34,197,94,0.12); border: 1px solid rgba(34,197,94,0.25); color: #4ADE80; }
        .alert-danger  { background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.25); color: #F87171; }
        .alert-warning { background: rgba(245,158,11,0.12); border: 1px solid rgba(245,158,11,0.25); color: #FCD34D; }

        /* ── DIVIDER ── */
        .divider {
            border: none;
            border-top: 1px solid var(--border);
            margin: 20px 0;
        }

        /* ── EMPTY STATE ── */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
        }

        .empty-state .empty-icon { font-size: 48px; margin-bottom: 16px; opacity: .5; }
        .empty-state p { font-size: 14px; }

        /* ── PAGINATION ── */
        .pagination {
            display: flex;
            align-items: center;
            gap: 6px;
            justify-content: flex-end;
            margin-top: 16px;
        }

        /* ── SCROLLBAR ── */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--bg4); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--gold-dark); }

        /* ── GOLD RATE BADGE ── */
        .rate-badge {
            display: inline-flex;
            background: linear-gradient(135deg, rgba(201,168,76,0.1), #fff);
            border: 1px solid rgba(201,168,76,0.4);
            border-radius: 100px;
            padding: 0 18px;
            font-size: 13px;
            font-weight: 600;
            color: var(--gold-dark);
            height: 38px;
            line-height: 38px;
            overflow: hidden;
            min-width: 240px;
            box-shadow: 0 2px 8px rgba(201,168,76,0.05);
            position: relative;
        }

        .rate-ticker {
            display: block;
            width: 100%;
        }

        .rate-item {
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        /* ── POS SPECIFIC ── */
        .pos-grid {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 20px;
            height: calc(100vh - 68px - 56px);
        }

        .pos-left, .pos-right {
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .pos-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .pos-items {
            flex: 1;
            overflow-y: auto;
            padding: 12px;
        }

        .pos-item {
            background: var(--bg3);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 14px;
            margin-bottom: 10px;
            transition: border-color .18s;
        }

        .pos-item:hover { border-color: rgba(201,168,76,0.3); }

        .pos-footer {
            padding: 16px 20px;
            border-top: 1px solid var(--border);
        }

        .pos-total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 0;
            font-size: 13.5px;
        }

        .pos-total-row.grand {
            font-size: 18px;
            font-weight: 700;
            color: var(--gold-dark);
            border-top: 1px solid var(--border);
            padding-top: 12px;
            margin-top: 6px;
        }

        /* ── PRINT ── */
        @media print {
            .sidebar, .topbar, .no-print { display: none !important; }
            .main-wrap { margin-left: 0; }
            body { background: #fff; color: #000; }
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            .sidebar { width: var(--sidebar-collapsed); }
            .sidebar .nav-label, .sidebar .logo-text, .sidebar .logo-sub, .sidebar .nav-section { display: none; }
            .main-wrap { margin-left: var(--sidebar-collapsed); }
            .pos-grid { grid-template-columns: 1fr; }
            .page-content { padding: 16px; }
        }
    </style>
</head>
<body>
    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <div class="logo-icon">💎</div>
            <div>
                <div class="logo-text">Five Finger</div>
                <div class="logo-sub">Shop Management</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">Main</div>
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span>
                <span class="nav-label">Dashboard</span>
            </a>
            <a href="{{ route('pos') }}" target="_blank" class="nav-item {{ request()->routeIs('pos') ? 'active' : '' }}">
                <span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg></span>
                <span class="nav-label">POS</span>
            </a>
            <a href="{{ route('sales') }}" class="nav-item {{ request()->routeIs('sales') ? 'active' : '' }}">
                <span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg></span>
                <span class="nav-label">Sales History</span>
            </a>
            <a href="{{ route('sales.dues') }}" class="nav-item {{ request()->routeIs('sales.dues') ? 'active' : '' }}">
                <span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></span>
                <span class="nav-label">Customer Dues</span>
            </a>

            <div class="nav-section">Inventory</div>
            <a href="{{ route('products') }}" class="nav-item {{ request()->routeIs('products') ? 'active' : '' }}">
                <span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22.08l-9-5.12a2 2 0 0 1-1-1.73V7.27a2 2 0 0 1 1-1.74l9-5.2a2 2 0 0 1 2 0l9 5.2a2 2 0 0 1 1 1.74v7.96a2 2 0 0 1-1 1.73l-9 5.12a2 2 0 0 1-2 0z"/><polyline points="3.29 7 12 12 20.71 7"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg></span>
                <span class="nav-label">Products</span>
            </a>
            <a href="{{ route('categories') }}" class="nav-item {{ request()->routeIs('categories') ? 'active' : '' }}">
                <span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg></span>
                <span class="nav-label">Categories</span>
            </a>
            <a href="{{ route('purities') }}" class="nav-item {{ request()->routeIs('purities') ? 'active' : '' }}">
                <span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v22"/><path d="M17 5H7a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2z"/><path d="M19 15.5V10"/><path d="M5 10v5.5"/><path d="M12 22c4.42 0 8-1.79 8-4s-3.58-4-8-4-8 1.79-8 4 3.58 4 8 4z"/></svg></span>
                <span class="nav-label">Purities</span>
            </a>
            <a href="{{ route('rates') }}" class="nav-item {{ request()->routeIs('rates') ? 'active' : '' }}">
                <span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg></span>
                <span class="nav-label">Gold Rates</span>
            </a>

            <div class="nav-section">People</div>
            <a href="{{ route('customers') }}" class="nav-item {{ request()->routeIs('customers') ? 'active' : '' }}">
                <span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
                <span class="nav-label">Customers</span>
            </a>
            <a href="{{ route('suppliers') }}" class="nav-item {{ request()->routeIs('suppliers') ? 'active' : '' }}">
                <span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><line x1="9" y1="22" x2="9" y2="2"/><line x1="15" y1="22" x2="15" y2="2"/><line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="10" x2="20" y2="10"/><line x1="4" y1="14" x2="20" y2="14"/><line x1="4" y1="18" x2="20" y2="18"/></svg></span>
                <span class="nav-label">Suppliers</span>
            </a>

            <div class="nav-section">Finance</div>
            <a href="{{ route('purchases') }}" class="nav-item {{ request()->routeIs('purchases') ? 'active' : '' }}">
                <span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg></span>
                <span class="nav-label">Purchases</span>
            </a>
            <a href="{{ route('purchases.dues') }}" class="nav-item {{ request()->routeIs('purchases.dues') ? 'active' : '' }}">
                <span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg></span>
                <span class="nav-label">Supplier Dues</span>
            </a>
            <a href="{{ route('expenses') }}" class="nav-item {{ request()->routeIs('expenses') ? 'active' : '' }}">
                <span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/><line x1="7" y1="15" x2="7.01" y2="15"/><line x1="12" y1="15" x2="12.01" y2="15"/></svg></span>
                <span class="nav-label">Expenses</span>
            </a>
            <a href="{{ route('reports') }}" class="nav-item {{ request()->routeIs('reports') ? 'active' : '' }}">
                <span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></span>
                <span class="nav-label">Reports</span>
            </a>
            <a href="{{ route('reports.sessions') }}" class="nav-item {{ request()->routeIs('reports.sessions') ? 'active' : '' }}">
                <span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
                <span class="nav-label">POS Sessions</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-item" style="color:var(--danger)">
                    <span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg></span>
                    <span class="nav-label">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN -->
    <div class="main-wrap">
        <header class="topbar">
            <div class="topbar-title">{{ $title ?? 'Dashboard' }}</div>
            <div class="topbar-right">
                <div class="topbar-date">{{ now()->format('D, d M Y') }}</div>
                @php 
                    $currDate = \App\Models\JewelleryRate::max('date');
                    $rates = \App\Models\JewelleryRate::with('purity')
                        ->where('date', $currDate)
                        ->get();
                @endphp
                @if($rates->count() > 0)
                    <div class="rate-badge">
                        <div class="rate-ticker" id="rateTicker">
                            @foreach($rates as $r)
                                <div class="rate-item">
                                    <svg style="width:14px;height:14px;display:inline-block;vertical-align:middle;margin-right:6px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg> {{ $r->purity->name }} – Rs.{{ number_format($r->rate_per_gram, 2) }}/g
                                </div>
                            @endforeach
                            @if($rates->count() > 1)
                                <div class="rate-item">
                                    <svg style="width:14px;height:14px;display:inline-block;vertical-align:middle;margin-right:6px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg> {{ $rates[0]->purity->name }} – Rs.{{ number_format($rates[0]->rate_per_gram, 2) }}/g
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
                <div class="avatar">{{ substr(auth()->user()->name ?? 'U', 0, 1) }}</div>
            </div>
        </header>

        <main class="page-content">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const ticker = document.getElementById('rateTicker');
            if (!ticker) return;
            const items = ticker.querySelectorAll('.rate-item');
            if (items.length <= 1) return;
            
            let current = 0;
            const itemHeight = 38;
            
            function moveNext() {
                current++;
                ticker.style.transition = 'transform 0.8s cubic-bezier(0.65, 0, 0.35, 1)';
                ticker.style.transform = `translateY(-${current * itemHeight}px)`;
                
                if (current >= items.length - 1) {
                    setTimeout(() => {
                        ticker.style.transition = 'none';
                        current = 0;
                        ticker.style.transform = `translateY(0)`;
                    }, 800);
                }
            }
            setInterval(moveNext, 4000);
        });
    </script>
</body>
</html>
