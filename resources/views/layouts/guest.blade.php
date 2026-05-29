<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sign In') — EduTenant ERP</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            min-height: 100vh;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #6d28d9 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        /* Decorative background circles */
        body::before {
            content: '';
            position: fixed;
            top: -120px; left: -120px;
            width: 400px; height: 400px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
            pointer-events: none;
        }

        body::after {
            content: '';
            position: fixed;
            bottom: -100px; right: -100px;
            width: 350px; height: 350px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
            pointer-events: none;
        }

        .auth-card {
            background: #fff;
            border-radius: 1.25rem;
            box-shadow: 0 25px 50px rgba(0,0,0,0.25), 0 10px 20px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 440px;
            padding: 2.5rem;
            position: relative;
            z-index: 1;
            animation: slideUp 0.4s ease forwards;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .auth-logo {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
            box-shadow: 0 8px 20px rgba(79,70,229,0.4);
        }

        .form-control {
            border-radius: 0.625rem;
            border: 1.5px solid #e5e7eb;
            padding: 0.625rem 0.875rem;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79,70,229,0.15);
        }

        .btn-auth {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border: none;
            border-radius: 0.625rem;
            padding: 0.75rem;
            font-weight: 600;
            font-size: 0.95rem;
            color: #fff;
            width: 100%;
            transition: all 0.2s ease;
            letter-spacing: 0.01em;
        }

        .btn-auth:hover {
            background: linear-gradient(135deg, #3730a3, #6d28d9);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(79,70,229,0.45);
            color: #fff;
        }

        .btn-auth:active {
            transform: translateY(0);
        }

        .input-group-text {
            background: #f9fafb;
            border: 1.5px solid #e5e7eb;
            color: #6b7280;
            border-radius: 0.625rem 0 0 0.625rem;
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 0.625rem 0.625rem 0;
        }

        .input-group .form-control:focus {
            border-color: #4f46e5;
        }

        .input-group:focus-within .input-group-text {
            border-color: #4f46e5;
        }

        .form-label {
            font-weight: 500;
            font-size: 0.875rem;
            color: #374151;
            margin-bottom: 0.375rem;
        }

        .invalid-feedback {
            font-size: 0.8rem;
        }

        .is-invalid {
            border-color: #dc2626 !important;
        }

        .is-invalid:focus {
            box-shadow: 0 0 0 3px rgba(220,38,38,0.15) !important;
        }
    </style>

    @stack('styles')
</head>
<body>
    @yield('content')

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    @stack('scripts')
</body>
</html>
