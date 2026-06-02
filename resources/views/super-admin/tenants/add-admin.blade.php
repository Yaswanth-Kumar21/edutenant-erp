@extends('layouts.super-admin-app')
@section('title', 'Add College Admin — ' . $tenant->name)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('super.tenants.index') }}" style="color:var(--blue);">Institutions</a></li>
    <li class="breadcrumb-item"><a href="{{ route('super.tenants.show', $tenant) }}" style="color:var(--blue);">{{ Str::limit($tenant->name, 25) }}</a></li>
    <li class="breadcrumb-item active" style="color:var(--muted);">Add College Admin</li>
@endsection

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">Add College Admin</h1>
        <p class="page-sub">Create a login account for {{ $tenant->name }}</p>
    </div>
    <a href="{{ route('super.tenants.show', $tenant) }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Back
    </a>
</div>

{{-- Validation Errors --}}
@if($errors->any())
<div class="alert alert-danger mb-4">
    <i class="fa-solid fa-circle-exclamation flex-shrink-0"></i>
    <div>
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-1 ps-3" style="font-size:12px;">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
</div>
@endif

<div class="row g-4">
    <div class="col-lg-7">

        {{-- ── If admin already exists, show their info + reset password ── --}}
        @if($existingAdmin)
        <div class="alert alert-success mb-4">
            <i class="fa-solid fa-circle-check flex-shrink-0"></i>
            <div>
                <strong>College Admin already exists</strong><br>
                <span style="font-size:12px;">
                    <strong>{{ $existingAdmin->name }}</strong> ({{ $existingAdmin->email }}) is the current admin.
                    You can reset their password below.
                </span>
            </div>
        </div>

        {{-- Reset Password Form --}}
        <div class="card mb-4">
            <div class="card-header">
                <span style="font-weight:700;">Reset Admin Password</span>
                <span class="badge badge-active">{{ $existingAdmin->name }}</span>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('super.tenants.admin.reset-password', [$tenant, $existingAdmin]) }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">New Password <span style="color:var(--red);">*</span></label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Min 8 characters" required>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm Password <span style="color:var(--red);">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat password" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn-primary">
                            <i class="fa-solid fa-key"></i> Reset Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Divider --}}
        <div class="d-flex align-items-center gap-3 mb-4">
            <hr style="flex:1;border-color:var(--border);">
            <span style="font-size:12px;color:var(--muted);white-space:nowrap;">OR CREATE A NEW ADMIN</span>
            <hr style="flex:1;border-color:var(--border);">
        </div>
        @endif

        {{-- ── Create New Admin Form ── --}}
        <div class="card">
            <div class="card-header">
                <span style="font-weight:700;">
                    {{ $existingAdmin ? 'Create Additional Admin' : 'Create College Admin Account' }}
                </span>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('super.tenants.admin.store', $tenant) }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Full Name <span style="color:var(--red);">*</span></label>
                            <input type="text" name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}"
                                   placeholder="e.g. Dr. Rajesh Kumar"
                                   required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone"
                                   class="form-control"
                                   value="{{ old('phone') }}"
                                   placeholder="+91 XXXXX XXXXX">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Email Address <span style="color:var(--red);">*</span></label>
                            <input type="email" name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', 'admin@' . $tenant->slug . '.edu') }}"
                                   placeholder="admin@college.edu"
                                   required>
                            <div class="form-hint">This will be the login email for the college admin.</div>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password <span style="color:var(--red);">*</span></label>
                            <input type="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Min 8 characters"
                                   required>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm Password <span style="color:var(--red);">*</span></label>
                            <input type="password" name="password_confirmation"
                                   class="form-control"
                                   placeholder="Repeat password"
                                   required>
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn-primary">
                            <i class="fa-solid fa-user-plus"></i> Create College Admin
                        </button>
                        <a href="{{ route('super.tenants.show', $tenant) }}" class="btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

    </div>

    {{-- ── Right Panel: Info ── --}}
    <div class="col-lg-5">

        {{-- Institution Info --}}
        <div class="card mb-3">
            <div class="card-header"><span style="font-weight:700;font-size:13px;">Institution</span></div>
            <div class="card-body p-0">
                @php $info = [
                    ['label' => 'Name',   'value' => $tenant->name],
                    ['label' => 'Slug',   'value' => $tenant->slug, 'mono' => true],
                    ['label' => 'Status', 'value' => ucfirst($tenant->status)],
                ]; @endphp
                @foreach($info as $i => $row)
                <div class="d-flex align-items-center justify-content-between px-4 py-3"
                     style="border-bottom:{{ $i < count($info)-1 ? '1px solid var(--border)' : 'none' }};">
                    <span style="font-size:12px;color:var(--muted);">{{ $row['label'] }}</span>
                    <span style="font-size:12px;font-weight:600;color:var(--text);{{ isset($row['mono']) ? 'font-family:monospace;' : '' }}">
                        {{ $row['value'] }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- What happens next --}}
        <div class="card mb-3">
            <div class="card-header"><span style="font-weight:700;font-size:13px;">What happens after creation?</span></div>
            <div class="card-body p-0">
                @php $steps = [
                    ['icon' => 'fa-user-check',   'color' => 'var(--green)',  'text' => 'Admin account is created with college_admin role'],
                    ['icon' => 'fa-building',      'color' => 'var(--blue)',   'text' => 'Account is linked to ' . $tenant->name],
                    ['icon' => 'fa-circle-check',  'color' => 'var(--green)',  'text' => 'Onboarding Step 2 auto-marks as complete'],
                    ['icon' => 'fa-right-to-bracket','color'=> 'var(--purple)', 'text' => 'Admin can log in and configure academics, fees, students'],
                ]; @endphp
                @foreach($steps as $i => $s)
                <div class="d-flex align-items-start gap-3 px-4 py-3"
                     style="border-bottom:{{ $i < count($steps)-1 ? '1px solid var(--border)' : 'none' }};">
                    <i class="fa-solid {{ $s['icon'] }} mt-1 flex-shrink-0" style="color:{{ $s['color'] }};font-size:13px;width:16px;"></i>
                    <span style="font-size:12px;color:var(--text2);">{{ $s['text'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Credentials reminder --}}
        <div class="card">
            <div class="card-header"><span style="font-weight:700;font-size:13px;">Share Credentials</span></div>
            <div class="card-body" style="font-size:12px;color:var(--muted);line-height:2;">
                After creating the admin, share these details with the college:
                <ul class="ps-3 mt-2 mb-0">
                    <li>Login URL: <code>{{ url('/login') }}</code></li>
                    <li>Email: the email you entered above</li>
                    <li>Password: the password you set above</li>
                </ul>
                <div class="mt-3 p-2 rounded" style="background:var(--orange-l);border:1px solid rgba(217,119,6,.2);">
                    <i class="fa-solid fa-triangle-exclamation" style="color:var(--orange);"></i>
                    <span style="color:#92400E;"> Ask the admin to change their password after first login.</span>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
