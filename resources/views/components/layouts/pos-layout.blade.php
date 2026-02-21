<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS – {{ config('app.name') }}</title>
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
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .pos-topbar {
            height: 60px;
            background: #fff;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .pos-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .pos-logo-icon {
            width: 32px; height: 32px;
            background: var(--gold-dark);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: #fff;
        }

        .pos-logo-text {
            font-weight: 700;
            color: var(--gold-dark);
            font-size: 18px;
        }

        .pos-content {
            flex: 1;
            padding: 15px;
            width: 100%;
        }

        /* ── BUTTONS ── */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: var(--radius-sm);
            font-size: 13.5px;
            font-weight: 600;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all .2s;
            text-decoration: none;
        }

        .btn-gold { background: var(--gold-dark); color: #fff; }
        .btn-gold:hover { background: #856927; }
        .btn-outline { background: #fff; border-color: var(--border); color: var(--text); }
        .btn-outline:hover { background: var(--bg3); }
        .btn-danger { background: rgba(239,68,68,0.1); color: var(--danger); border-color: rgba(239,68,68,0.2); }
        .btn-danger:hover { background: var(--danger); color: #fff; }

        /* ── MODALS ── */
        .modal-backdrop {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15,23,42,0.6);
            backdrop-filter: blur(4px);
            display: flex; align-items: center; justify-content: center;
            z-index: 1000;
            padding: 20px;
        }

        .modal {
            background: #fff;
            border-radius: var(--radius);
            width: 100%;
            max-width: 500px;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
            overflow: hidden;
            display: flex; flex-direction: column;
            max-height: 90vh;
        }

        .modal-lg { max-width: 800px; }

        .modal-header {
            padding: 18px 24px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
        }

        .modal-title { font-weight: 700; font-size: 18px; color: var(--text); }

        .modal-body { padding: 24px; overflow-y: auto; }

        .modal-footer {
            padding: 18px 24px;
            background: var(--bg);
            border-top: 1px solid var(--border);
            display: flex; justify-content: flex-end; gap: 12px;
        }

        .btn-close {
            background: none; border: none; font-size: 20px; color: var(--text-muted); cursor: pointer;
        }

        /* ── FORMS ── */
        .form-group { margin-bottom: 16px; }
        .form-label { display: block; font-size: 12px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px; text-transform: uppercase; letter-spacing: .04em; }
        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            font-size: 14px;
            transition: all .2s;
            background: #fff;
            color: var(--text);
        }
        .form-control:focus { outline: none; border-color: var(--gold); box-shadow: 0 0 0 3px rgba(201,168,76,0.15); }

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
        .badge-green { background: rgba(34,197,94,0.15); color: #10B981; }
        .badge-red { background: rgba(239,68,68,0.15); color: #EF4444; }

        /* ── SCROLLBAR ── */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--bg4); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--gold-dark); }

        /* ── TABLES ── */
        .table-wrap { width: 100%; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { padding: 12px 16px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); border-bottom: 2px solid var(--border); background: var(--bg); }
        td { padding: 14px 16px; font-size: 13.5px; border-bottom: 1px solid var(--border); }

        /* ── PRINTING ── */
        @media print {
            .no-print { display: none !important; }
            body { background: #fff; padding: 0; }
            .modal-backdrop { position: relative; background: none; backdrop-filter: none; padding: 0; display: block; overflow: visible; }
            .modal { box-shadow: none; border: none; max-width: 100% !important; max-height: none; overflow: visible; display: block; }
            .modal-footer, .modal-header .btn-close { display: none !important; }
        }
    </style>
</head>
<body>
    <header class="pos-topbar no-print">
        <a href="{{ route('dashboard') }}" class="pos-logo">
            <div class="pos-logo-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:18px;height:18px;"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            </div>
            <span class="pos-logo-text">Five Finger POS</span>
        </a>
        <div style="display:flex;align-items:center;gap:20px;">
            <div style="font-size:13px;font-weight:600;color:var(--text-muted);">{{ now()->format('l, d M Y') }}</div>
            <button onclick="location.reload()" class="btn btn-outline btn-sm">Refresh POS</button>
            <a href="{{ route('dashboard') }}" class="btn btn-gold btn-sm">Return to CMS</a>
        </div>
    </header>

    <main class="pos-content">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
