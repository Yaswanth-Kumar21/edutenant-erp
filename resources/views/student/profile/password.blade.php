@extends('layouts.student-app')

@section('title', 'Change Password')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('student.profile.index') }}" style="color:var(--primary);text-decoration:none;">My Profile</a></li>
    <li class="breadcrumb-item active">Change Password</li>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-key me-2" style="color:#4f46e5;"></i>Change Password</h1>
        <p style="color:var(--muted);font-size:0.875rem;margin:0;">Update your account password</p>
    </div>
    <a href="{{ route('student.profile.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Profile
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('student.profile.password.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;font-size:0.875rem;">Current Password</label>
                        <div class="input-group">
                            <input type="password" name="current_password" id="current_password"
                                   class="form-control @error('current_password') is-invalid @enderror"
                                   placeholder="Enter current password" required>
                            <button type="button" class="btn btn-outline-secondary toggle-pw" data-target="current_password">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        @error('current_password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;font-size:0.875rem;">New Password</label>
                        <div class="input-group">
                            <input type="password" name="password" id="new_password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Minimum 8 characters" required>
                            <button type="button" class="btn btn-outline-secondary toggle-pw" data-target="new_password">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label" style="font-weight:600;font-size:0.875rem;">Confirm New Password</label>
                        <div class="input-group">
                            <input type="password" name="password_confirmation" id="confirm_password"
                                   class="form-control"
                                   placeholder="Re-enter new password" required>
                            <button type="button" class="btn btn-outline-secondary toggle-pw" data-target="confirm_password">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="p-3 rounded mb-4" style="background:rgba(79,70,229,0.06);border:1px solid rgba(79,70,229,0.15);font-size:0.8rem;color:var(--muted);">
                        <i class="fa-solid fa-shield-halved me-1" style="color:#4f46e5;"></i>
                        Password must be at least 8 characters long.
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fa-solid fa-lock me-2"></i> Update Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.querySelectorAll('.toggle-pw').forEach(btn => {
    btn.addEventListener('click', () => {
        const input = document.getElementById(btn.dataset.target);
        const icon  = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });
});
</script>
@endpush

