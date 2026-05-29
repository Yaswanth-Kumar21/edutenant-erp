@extends('layouts.guest')

@section('title', 'Sign In')

@section('content')
<div class="auth-card">

    <!-- Logo -->
    <div class="auth-logo">
        <i class="fa-solid fa-graduation-cap text-white" style="font-size:1.75rem;"></i>
    </div>

    <!-- Title -->
    <div class="text-center mb-4">
        <h1 style="font-size:1.5rem;font-weight:800;color:#1e1b4b;margin-bottom:0.25rem;">
            EduTenant ERP
        </h1>
        <p style="color:#6b7280;font-size:0.875rem;margin:0;">
            Sign in to your account
        </p>
    </div>

    <!-- Session Status -->
    @if(session('status'))
        <div class="alert alert-info d-flex align-items-center gap-2 mb-3 py-2"
             style="border-radius:0.625rem;font-size:0.85rem;">
            <i class="fa-solid fa-circle-info"></i>
            {{ session('status') }}
        </div>
    @endif

    <!-- Login Form -->
    <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fa-solid fa-envelope" style="font-size:0.85rem;"></i>
                </span>
                <input type="email"
                       id="email"
                       name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}"
                       placeholder="you@college.edu"
                       required
                       autofocus
                       autocomplete="username">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Password -->
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="password" class="form-label mb-0">Password</label>
                @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       style="font-size:0.8rem;color:#4f46e5;text-decoration:none;">
                        Forgot password?
                    </a>
                @endif
            </div>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fa-solid fa-lock" style="font-size:0.85rem;"></i>
                </span>
                <input type="password"
                       id="password"
                       name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       placeholder="••••••••"
                       required
                       autocomplete="current-password">
                <button type="button"
                        class="btn border"
                        id="toggle-password"
                        style="border-left:none;border-radius:0 0.625rem 0.625rem 0;background:#f9fafb;color:#6b7280;border-color:#e5e7eb;"
                        tabindex="-1">
                    <i class="fa-solid fa-eye" id="pw-icon" style="font-size:0.85rem;"></i>
                </button>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Remember Me -->
        <div class="mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox"
                       id="remember_me" name="remember"
                       style="border-color:#d1d5db;">
                <label class="form-check-label" for="remember_me"
                       style="font-size:0.875rem;color:#374151;">
                    Remember me for 30 days
                </label>
            </div>
        </div>

        <!-- Submit -->
        <button type="submit" class="btn btn-auth mb-3">
            <i class="fa-solid fa-right-to-bracket me-2"></i>
            Sign In
        </button>

    </form>

    <!-- Demo Credentials -->
    <div class="alert mb-0 py-2 px-3"
         style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:0.625rem;">
        <div class="d-flex align-items-center gap-2 mb-1">
            <i class="fa-solid fa-circle-info" style="color:#3b82f6;font-size:0.85rem;"></i>
            <span style="font-size:0.8rem;font-weight:600;color:#1e40af;">Demo Credentials</span>
        </div>
        <div style="font-size:0.78rem;color:#1e40af;font-family:monospace;">
            <div><strong>Email:</strong> superadmin@erp.com</div>
            <div><strong>Password:</strong> password</div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Password show/hide toggle
    const toggleBtn = document.getElementById('toggle-password');
    const pwInput   = document.getElementById('password');
    const pwIcon    = document.getElementById('pw-icon');

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            const isPassword = pwInput.type === 'password';
            pwInput.type = isPassword ? 'text' : 'password';
            pwIcon.className = isPassword ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye';
            pwIcon.style.fontSize = '0.85rem';
        });
    }

    // Auto-fill demo credentials on click
    document.querySelector('.alert [style*="monospace"]')?.addEventListener('click', function () {
        document.getElementById('email').value = 'superadmin@erp.com';
        document.getElementById('password').value = 'password';
    });
</script>
@endpush
