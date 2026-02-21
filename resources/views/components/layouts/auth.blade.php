<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – Five Finger</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @livewireStyles
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --gold: #C9A84C; --gold-light: #E8C96A; --gold-dark: #9A7A2E;
            --bg: #F8F9FA; --bg2: #FFFFFF; --bg3: #F1F5F9;
            --text: #1E293B; --text-muted: #64748B; --border: #E2E8F0;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .auth-card {
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 48px 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 24px 64px rgba(0,0,0,0.1);
        }
        .auth-logo {
            text-align: center;
            margin-bottom: 32px;
        }
        .auth-logo .icon {
            font-size: 48px;
            display: block;
            margin-bottom: 12px;
        }
        .auth-logo h1 {
            font-size: 22px;
            font-weight: 700;
            color: var(--gold);
        }
        .auth-logo p {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 4px;
        }
        .form-group { margin-bottom: 18px; }
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
            border-radius: 10px;
            color: var(--text);
            padding: 12px 16px;
            font-size: 14px;
            font-family: inherit;
            outline: none;
            transition: border-color .18s, box-shadow .18s;
        }
        .form-control:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(201,168,76,0.15);
        }
        .form-control::placeholder { color: var(--text-muted); }
        .btn-gold {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all .18s;
            margin-top: 8px;
        }
        .btn-gold:hover {
            background: linear-gradient(135deg, var(--gold-light), var(--gold));
            box-shadow: 0 4px 20px rgba(201,168,76,0.4);
        }
        .error-msg {
            color: #F87171;
            font-size: 12px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    {{ $slot }}
    @livewireScripts
</body>
</html>
