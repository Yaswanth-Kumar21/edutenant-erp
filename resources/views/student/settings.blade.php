@extends('layouts.student-app')

@section('title', 'Settings')

@section('breadcrumb')
    <li class="breadcrumb-item active">Settings</li>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-gear me-2" style="color:var(--primary);"></i>Settings</h1>
        <p style="color:var(--muted);font-size:.875rem;margin:0;">Manage your preferences and account security</p>
    </div>
</div>

<div class="row g-4">

    {{-- Theme Preference --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><i class="fa-solid fa-palette me-2" style="color:var(--primary);"></i>Theme Preference</div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-6">
                        <div id="theme-light-btn" class="p-3 rounded text-center cursor-pointer"
                             style="border:2px solid var(--primary);background:#fff;cursor:pointer;transition:all .2s;">
                            <div style="width:100%;height:50px;background:linear-gradient(135deg,#f8f7ff,#e0e7ff);border-radius:.5rem;margin-bottom:.5rem;"></div>
                            <div style="font-size:.82rem;font-weight:600;color:#1e1b4b;">Light Mode</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div id="theme-dark-btn" class="p-3 rounded text-center"
                             style="border:2px solid var(--border);background:var(--surface);cursor:pointer;transition:all .2s;">
                            <div style="width:100%;height:50px;background:linear-gradient(135deg,#0f0e1a,#1a1830);border-radius:.5rem;margin-bottom:.5rem;"></div>
                            <div style="font-size:.82rem;font-weight:600;color:var(--text);">Dark Mode</div>
                        </div>
                    </div>
                </div>
                <div class="mt-3 p-3 rounded" style="background:rgba(139,92,246,.06);border:1px solid rgba(139,92,246,.15);font-size:.82rem;color:var(--muted);">
                    <i class="fa-solid fa-circle-info me-1" style="color:var(--primary);"></i>
                    Theme preference is saved locally in your browser.
                </div>
            </div>
        </div>
    </div>

    {{-- Notification Preferences --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><i class="fa-solid fa-bell me-2" style="color:var(--primary);"></i>Notification Preferences</div>
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between py-3" style="border-bottom:1px solid var(--border);">
                    <div>
                        <div style="font-weight:500;font-size:.875rem;">Fee Due Alerts</div>
                        <div style="font-size:.75rem;color:var(--muted);">Get notified when fees are due</div>
                    </div>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" checked>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between py-3" style="border-bottom:1px solid var(--border);">
                    <div>
                        <div style="font-weight:500;font-size:.875rem;">Attendance Alerts</div>
                        <div style="font-size:.75rem;color:var(--muted);">Alert when attendance drops below 75%</div>
                    </div>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" checked>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between py-3">
                    <div>
                        <div style="font-weight:500;font-size:.875rem;">College Announcements</div>
                        <div style="font-size:.75rem;color:var(--muted);">Messages from college administration</div>
                    </div>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" checked>
                    </div>
                </div>
                <div class="mt-3">
                    <button class="btn btn-primary btn-sm"><i class="fa-solid fa-save me-1"></i> Save Preferences</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Password Change --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><i class="fa-solid fa-lock me-2" style="color:var(--primary);"></i>Change Password</div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('student.profile.password.update') }}">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label" style="font-size:.875rem;font-weight:500;">Current Password</label>
                        <div class="input-group">
                            <input type="password" name="current_password" id="s_cur_pw"
                                   class="form-control @error('current_password') is-invalid @enderror"
                                   placeholder="Enter current password" required>
                            <button type="button" class="btn btn-outline-secondary toggle-pw" data-target="s_cur_pw">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        @error('current_password')<div class="text-danger mt-1" style="font-size:.78rem;">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:.875rem;font-weight:500;">New Password</label>
                        <div class="input-group">
                            <input type="password" name="password" id="s_new_pw"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Minimum 8 characters" required>
                            <button type="button" class="btn btn-outline-secondary toggle-pw" data-target="s_new_pw">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        @error('password')<div class="text-danger mt-1" style="font-size:.78rem;">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label" style="font-size:.875rem;font-weight:500;">Confirm New Password</label>
                        <div class="input-group">
                            <input type="password" name="password_confirmation" id="s_conf_pw"
                                   class="form-control" placeholder="Re-enter new password" required>
                            <button type="button" class="btn btn-outline-secondary toggle-pw" data-target="s_conf_pw">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-key me-2"></i> Update Password
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Account Info --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><i class="fa-solid fa-shield-halved me-2" style="color:var(--primary);"></i>Account Security</div>
            <div class="card-body p-4">
                <dl class="mb-0" style="font-size:.875rem;">
                    <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid var(--border);">
                        <span style="color:var(--muted);">Email</span>
                        <strong>{{ auth()->user()->email }}</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid var(--border);">
                        <span style="color:var(--muted);">Account Status</span>
                        <span class="badge" style="background:#dcfce7;color:#166534;">Active</span>
                    </div>
                    <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid var(--border);">
                        <span style="color:var(--muted);">Member Since</span>
                        <strong>{{ auth()->user()->created_at?->format('d M Y') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span style="color:var(--muted);">Last Updated</span>
                        <strong>{{ auth()->user()->updated_at?->diffForHumans() }}</strong>
                    </div>
                </dl>
                <div class="mt-3 p-3 rounded" style="background:rgba(5,150,105,.06);border:1px solid rgba(5,150,105,.15);font-size:.78rem;color:var(--muted);">
                    <i class="fa-solid fa-circle-check me-1" style="color:#059669;"></i>
                    Your account is secure. Use a strong password and never share your credentials.
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
// Theme toggle buttons
document.getElementById('theme-light-btn')?.addEventListener('click', () => {
    document.documentElement.setAttribute('data-theme', 'light');
    localStorage.setItem('theme', 'light');
    document.getElementById('theme-light-btn').style.borderColor = 'var(--primary)';
    document.getElementById('theme-dark-btn').style.borderColor = 'var(--border)';
});
document.getElementById('theme-dark-btn')?.addEventListener('click', () => {
    document.documentElement.setAttribute('data-theme', 'dark');
    localStorage.setItem('theme', 'dark');
    document.getElementById('theme-dark-btn').style.borderColor = 'var(--primary)';
    document.getElementById('theme-light-btn').style.borderColor = 'var(--border)';
});

// Password toggle
document.querySelectorAll('.toggle-pw').forEach(btn => {
    btn.addEventListener('click', () => {
        const input = document.getElementById(btn.dataset.target);
        const icon  = btn.querySelector('i');
        input.type  = input.type === 'password' ? 'text' : 'password';
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    });
});
</script>
@endpush

