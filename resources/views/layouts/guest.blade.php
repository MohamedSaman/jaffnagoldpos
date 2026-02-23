<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
         <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
         <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Scripts -->
        <style>
            :root {
                --primary: #D4AF37;
                --primary-dark: #B8860B;
                --black: #000000;
                --white: #ffffff;
            }

            /* Login page styling */
            .login-container {
                height: 100vh;
                width: 100vw;
                position: relative;
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                background-color: #fcfcfc;
            }

            .background-image {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-image: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('{{ asset('/images/jgold.png') }}');
                background-size: cover;
                background-position: center;
                z-index: 0;
            }

            .login-form-overlay {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border-radius: 20px;
                padding: 40px;
                width: 100%;
                max-width: 450px;
                z-index: 1;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
                border: 1px solid var(--primary);
            }

            .user-icon-container {
                display: flex;
                justify-content: center;
                margin-bottom: 30px;
            }

            .user-icon-container i {
                font-size: 2.5rem;
                color: var(--primary);
                background: var(--black);
                border-radius: 50%;
                width: 80px;
                height: 80px;
                display: flex;
                align-items: center;
                justify-content: center;
                border: 2px solid var(--primary);
                box-shadow: 0 0 20px rgba(212, 175, 55, 0.3);
            }

            .form-group {
                margin-bottom: 25px;
            }

            .form-control {
                border-radius: 10px;
                padding: 12px 20px;
                border: 1px solid #ddd;
                transition: all 0.3s;
            }

            .form-control:focus {
                border-color: var(--primary);
                box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.1);
            }

            .login-btn {
                width: 100%;
                border-radius: 10px;
                padding: 12px;
                background-color: var(--black);
                color: var(--primary);
                border: 1px solid var(--primary);
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 1px;
                transition: all 0.3s;
            }

            .login-btn:hover {
                background-color: var(--primary);
                color: var(--black);
                transform: translateY(-2px);
                box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            }

            .forgot-link {
                color: var(--black);
                text-decoration: none;
                font-weight: 500;
                transition: color 0.3s;
            }

            .forgot-link:hover {
                color: var(--primary-dark);
            }
        </style>
        <!-- Styles -->

    </head>
    <body>
        <div class="font-sans text-gray-900 antialiased">
            {{ $slot }}
        </div>

       <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    </body>
</html>
