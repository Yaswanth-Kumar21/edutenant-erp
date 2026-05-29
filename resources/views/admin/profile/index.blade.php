@extends('layouts.super-admin-app')
@section('title', 'My Profile')
@section('breadcrumb')
    <li class="breadcrumb-item active" style="color:var(--muted);">My Profile</li>
@endsection
@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">My Profile</h1>
        <p class="page-sub">Manage your personal information and account security</p>
    </div>
    <a href="{{ route('admin.settings.index') }}" class="btn-secondary">
        <i class="fa-solid fa-gear"></i> Settings
    </a>
</div>

<div class="row g-4">
    {{-- Left Column --}}
    <div class="col-lg-4">
        {{-- Profile Card --}}
        <div class="card mb-4">
            <div class="card-body text-center" style="padding:32px 24px;">
                <div class="position-relative d-inline-block mb-3">
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" id="avatar-preview"
                         class="rounded-circle" style="width:88px;height:88px;object-fit:cover;border:3px solid var(--border);box-shadow:var(--shadow-md);">
                    <label for="avatar-input" style="position:absolute;bottom:0;right:0;width:28px;height:28px;border-radius:50%;background:var(--blue);color:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;border:2px solid var(--surface);box-shadow:var(--shadow);" title="Change photo">
                        <i class="fa-solid fa-camera" style="font-size:11px;"></i>
                    </label>
                </div>
                <div style="font-size:16px;font-weight:700;color:var(--text);margin-bottom:4px;">{{ $user->name }}</div>
                <div style="font-size:12px;color:var(--muted);margin-bottom:12px;">{{ $user->email }}</div>
                <span class="badge badge-blue" style="font-size:11px;">{{ $user->getRoleDisplayName() }}</span>
                @if($tenant)
                <div style="margin-top:8px;"><span class="badge badge-active" style="font-size:11px;">{{ $tenant->name }}</span></div>
                @endif
                <form method="POST" action="{{ route('admin.profile.photo') }}" enctype="multipart/form-data" id="photo-form" class="mt-3">
                    @csrf
                    <input type="file" id="avatar-input" name="avatar" accept="image/*" class="d-none">
                    <button type="submit" id="photo-submit" class="btn-primary d-none" style="font-size:12px;padding:6px 14px;">
                        <i class="fa-solid fa-upload"></i> Upload Photo
                    </button>
                </form>
            </div>
        </div>

        {{-- Account Info --}}
        <div class="card mb-4">
            <div class="card-header"><span style="font-weight:700;font-size:13px;">Account Information</span></div>
            <div class="card-body p-0">
                @php $info = [
                    ['label'=>'Role',         'value'=>$user->getRoleDisplayName()],
                    ['label'=>'Status',       'value'=>ucfirst($user->status ?? 'active'), 'badge'=>true, 'color'=>'badge-active'],
                    ['label'=>'Member Since', 'value'=>$user->created_at?->format('d M Y')],
                    ['label'=>'Email Verified','value'=>$user->email_verified_at ? 'Verified' : 'Not Verified', 'badge'=>true, 'color'=>$user->email_verified_at ? 'badge-active' : 'badge-inactive'],
                ]; @endphp
                @if($tenant)
                    @php array_splice($info, 1, 0, [['label'=>'Institution','value'=>$tenant->name]]); @endphp
                @endif
                @foreach($info as $i => $item)
                <div class="d-flex align-items-center justify-content-between px-4 py-3" style="border-bottom:{{ $i < count($info)-1 ? '1px solid var(--border)' : 'none' }};">
                    <span style="font-size:12px;color:var(--muted);">{{ $item['label'] }}</span>
                    @if(isset($item['badge']))
                        <span class="badge {{ $item['color'] }}" style="font-size:11px;">{{ $item['value'] }}</span>
                    @else
                        <span style="font-size:12px;font-weight:600;color:var(--text);">{{ $item['value'] }}</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="card">
            <div class="card-header"><span style="font-weight:700;font-size:13px;">Recent Activity</span></div>
            <div class="card-body p-0">
                @foreach($recentActivity as $i => $act)
                <div class="d-flex align-items-center gap-3 px-4 py-3" style="border-bottom:{{ $i < count($recentActivity)-1 ? '1px solid var(--border)' : 'none' }};">
                    <div style="width:32px;height:32px;border-radius:8px;background:var(--surface2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fa-solid {{ $act['icon'] }}" style="color:{{ $act['color'] }};font-size:12px;"></i>
                    </div>
                    <div>
                        <div style="font-size:12px;font-weight:600;color:var(--text);">{{ $act['title'] }}</div>
                        <div style="font-size:11px;color:var(--muted);">{{ $act['detail'] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Right Column --}}
    <div class="col-lg-8">
        {{-- Personal Information --}}
        <div class="card mb-4">
            <div class="card-header"><span style="font-weight:700;">Personal Information</span></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.profile.info') }}">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name <span style="color:var(--red);">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Address <span style="color:var(--red);">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control"
                                   value="{{ old('phone', $user->phone) }}" placeholder="+91 XXXXX XXXXX">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Role</label>
                            <input type="text" class="form-control" value="{{ $user->getRoleDisplayName() }}" readonly style="background:var(--surface2);cursor:not-allowed;">
                        </div>
                        @if($staffProfile)
                        <div class="col-md-6">
                            <label class="form-label">Designation</label>
                            <input type="text" class="form-control" value="{{ $staffProfile->designation ?? '—' }}" readonly style="background:var(--surface2);">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Department</label>
                            <input type="text" class="form-control" value="{{ $staffProfile->department ?? '—' }}" readonly style="background:var(--surface2);">
                        </div>
                        @endif
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn-primary"><i class="fa-solid fa-save"></i> Save Changes</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Change Password --}}
        <div class="card mb-4">
            <div class="card-header"><span style="font-weight:700;">Change Password</span></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.profile.password') }}">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Current Password <span style="color:var(--red);">*</span></label>
                            <div class="d-flex gap-2">
                                <input type="password" name="current_password" id="cur_pw"
                                       class="form-control @error('current_password') is-invalid @enderror"
                                       placeholder="Enter current password" required>
                                <button type="button" class="btn-icon flex-shrink-0" onclick="togglePw('cur_pw',this)"><i class="fa-solid fa-eye" style="font-size:12px;"></i></button>
                            </div>
                            @error('current_password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">New Password <span style="color:var(--red);">*</span></label>
                            <div class="d-flex gap-2">
                                <input type="password" name="password" id="new_pw"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Minimum 8 characters" required>
                                <button type="button" class="btn-icon flex-shrink-0" onclick="togglePw('new_pw',this)"><i class="fa-solid fa-eye" style="font-size:12px;"></i></button>
                            </div>
                            @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm New Password <span style="color:var(--red);">*</span></label>
                            <div class="d-flex gap-2">
                                <input type="password" name="password_confirmation" id="conf_pw"
                                       class="form-control" placeholder="Re-enter new password" required>
                                <button type="button" class="btn-icon flex-shrink-0" onclick="togglePw('conf_pw',this)"><i class="fa-solid fa-eye" style="font-size:12px;"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn-primary"><i class="fa-solid fa-key"></i> Update Password</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Notification Preferences --}}
        <div class="card mb-4">
            <div class="card-header"><span style="font-weight:700;">Notification Preferences</span></div>
            <div class="card-body p-0">
                @php $notifs = [
                    ['label'=>'Admission Confirmations','sub'=>'Email when a new student is admitted'],
                    ['label'=>'Fee Payment Receipts','sub'=>'Email after fee collection'],
                    ['label'=>'Low Attendance Alerts','sub'=>'Alert students below 75%'],
                    ['label'=>'Payroll Notifications','sub'=>'Salary slip emails to staff'],
                ]; @endphp
                @foreach($notifs as $i => $n)
                <div class="d-flex align-items-center justify-content-between px-4 py-3" style="border-bottom:{{ $i < count($notifs)-1 ? '1px solid var(--border)' : 'none' }};">
                    <div>
                        <div style="font-size:13px;font-weight:500;color:var(--text);">{{ $n['label'] }}</div>
                        <div style="font-size:11px;color:var(--muted);">{{ $n['sub'] }}</div>
                    </div>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" checked style="cursor:pointer;">
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Institution Info (if applicable) --}}
        @if($tenant)
        <div class="card">
            <div class="card-header">
                <span style="font-weight:700;">Institution Information</span>
                @if(auth()->user()->isCollegeAdmin())
                <a href="{{ route('admin.settings.index') }}" class="btn-ghost" style="font-size:12px;padding:4px 10px;">
                    <i class="fa-solid fa-pen"></i> Edit
                </a>
                @endif
            </div>
            <div class="card-body">
                <div class="row g-3" style="font-size:13px;">
                    <div class="col-md-6"><span style="color:var(--muted);">Name</span><div style="font-weight:600;color:var(--text);margin-top:2px;">{{ $tenant->name }}</div></div>
                    <div class="col-md-6"><span style="color:var(--muted);">Email</span><div style="font-weight:600;color:var(--text);margin-top:2px;">{{ $tenant->email ?? '—' }}</div></div>
                    <div class="col-md-6"><span style="color:var(--muted);">Phone</span><div style="font-weight:600;color:var(--text);margin-top:2px;">{{ $tenant->phone ?? '—' }}</div></div>
                    <div class="col-md-6"><span style="color:var(--muted);">Principal</span><div style="font-weight:600;color:var(--text);margin-top:2px;">{{ $tenant->principal_name ?? '—' }}</div></div>
                    <div class="col-md-6"><span style="color:var(--muted);">City</span><div style="font-weight:600;color:var(--text);margin-top:2px;">{{ $tenant->city ?? '—' }}{{ $tenant->state ? ', '.$tenant->state : '' }}</div></div>
                    <div class="col-md-6"><span style="color:var(--muted);">Affiliation No</span><div style="font-weight:600;color:var(--text);margin-top:2px;font-family:monospace;font-size:12px;">{{ $tenant->affiliation_number ?? '—' }}</div></div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@endsection
@push('scripts')
<script>
document.getElementById('avatar-input')?.addEventListener('change', function() {
    const file = this.files[0]; if(!file) return;
    const reader = new FileReader();
    reader.onload = e => { document.getElementById('avatar-preview').src = e.target.result; document.getElementById('photo-submit').classList.remove('d-none'); };
    reader.readAsDataURL(file);
});
function togglePw(id, btn) {
    const input = document.getElementById(id);
    const icon  = btn.querySelector('i');
    input.type  = input.type === 'password' ? 'text' : 'password';
    icon.classList.toggle('fa-eye'); icon.classList.toggle('fa-eye-slash');
}
</script>
@endpush
