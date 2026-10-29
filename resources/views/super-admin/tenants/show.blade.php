@extends('layouts.super-admin-app')
@section('title', $tenant->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('super.tenants.index') }}" style="color:var(--blue);">Institutions</a></li>
    <li class="breadcrumb-item active" style="color:var(--muted);">{{ Str::limit($tenant->name, 30) }}</li>
@endsection

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">{{ $tenant->name }}</h1>
        <p class="page-sub">Institution details and onboarding progress</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('super.tenants.edit', $tenant) }}" class="btn-primary"><i class="fa-solid fa-pen"></i> Edit</a>
        <a href="{{ route('super.tenants.index') }}" class="btn-secondary"><i class="fa-solid fa-arrow-left"></i> Back</a>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════
     CREDENTIALS PANEL — shown once after institution creation / password reset
     ══════════════════════════════════════════════════════════════════════════ --}}
@php $creds = session('new_admin_credentials'); @endphp

{{-- Always show current admin info if admin exists (even without session) --}}
@if(isset($collegeAdmin) && $collegeAdmin && !$creds)
<div class="card mb-4" style="border:1px solid rgba(37,99,235,.2);background:rgba(37,99,235,.03);">
    <div class="card-header" style="background:rgba(37,99,235,.06);">
        <div class="d-flex align-items-center gap-2">
            <i class="fa-solid fa-user-shield" style="color:var(--blue);"></i>
            <span style="font-weight:700;color:var(--blue);">College Admin Account</span>
        </div>
        <span class="badge badge-active"><i class="fa-solid fa-circle-check me-1"></i>Active</span>
    </div>
    <div class="card-body">
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px;">Login Email</div>
                <code style="font-size:14px;background:var(--surface2);padding:8px 12px;border-radius:6px;border:1px solid var(--border);display:block;">{{ $collegeAdmin->email }}</code>
            </div>
            <div class="col-md-6">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px;">Admin Name</div>
                <div style="font-size:14px;font-weight:600;padding:8px 0;">{{ $collegeAdmin->name }}</div>
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <form method="POST" action="{{ route('super.tenants.resend-credentials', $tenant) }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn-secondary">
                    <i class="fa-solid fa-key"></i> Reset Password &amp; Resend Email
                </button>
            </form>
            <form method="POST" action="{{ route('super.tenants.impersonate', $tenant) }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn-secondary" style="border-color:var(--purple);color:var(--purple);">
                    <i class="fa-solid fa-right-to-bracket"></i> Login As College Admin
                </button>
            </form>
        </div>
    </div>
</div>
@endif

@if($creds && $creds['tenant_id'] == $tenant->id)
{{-- ── Creation status banner ──────────────────────────────────────────── --}}
<div class="d-flex flex-wrap align-items-center gap-3 mb-3 p-3 rounded"
     style="background:var(--surface2);border:1px solid var(--border);font-size:13px;">
    <span style="font-weight:700;color:var(--muted);">Creation Status:</span>
    <span style="color:var(--green);"><i class="fa-solid fa-circle-check me-1"></i>✓ Institution Created</span>
    <span style="color:var(--muted);">·</span>
    <span style="color:var(--green);"><i class="fa-solid fa-circle-check me-1"></i>✓ College Admin Created</span>
    <span style="color:var(--muted);">·</span>
    @if($creds['email_sent'])
        <span style="color:var(--green);">
            <i class="fa-solid fa-circle-check me-1"></i>
            ✓ Email Sent to <strong>{{ $creds['recipient_email'] ?? $creds['admin_email'] }}</strong>
        </span>
    @else
        <span style="color:var(--red);">
            <i class="fa-solid fa-circle-xmark me-1"></i>❌ Email Failed —
            @php $latestLog = \App\Models\EmailLog::where('tenant_id',$tenant->id)->where('status','failed')->latest()->first(); @endphp
            {{ $latestLog?->error_message ? Str::limit($latestLog->error_message, 80) : 'Check SMTP config' }}
        </span>
    @endif
</div>

{{-- ── Credentials Panel ────────────────────────────────────────────────── --}}
<div class="card mb-4" style="border:2px solid rgba(37,99,235,.3);background:linear-gradient(135deg,rgba(37,99,235,.04),rgba(79,70,229,.04));" id="credentialsPanel">
    <div class="card-header" style="background:linear-gradient(135deg,rgba(37,99,235,.08),rgba(79,70,229,.08));border-bottom:1px solid rgba(37,99,235,.15);">
        <div class="d-flex align-items-center gap-2">
            <div style="width:32px;height:32px;border-radius:8px;background:var(--blue);display:flex;align-items:center;justify-content:center;">
                <i class="fa-solid fa-key" style="color:#fff;font-size:13px;"></i>
            </div>
            <div>
                <span style="font-weight:700;color:var(--blue);font-size:14px;">Institution Created — College Admin Credentials</span>
                <div style="font-size:11px;color:var(--muted);">Auto-generated on {{ now()->format('d M Y, h:i A') }}</div>
            </div>
        </div>
        <span class="badge badge-active"><i class="fa-solid fa-circle-check me-1"></i>Admin Provisioned</span>
    </div>
    <div class="card-body">

        <div class="d-flex align-items-start gap-3 p-3 rounded mb-4"
             style="background:rgba(217,119,6,.08);border:1px solid rgba(217,119,6,.25);">
            <i class="fa-solid fa-triangle-exclamation mt-1 flex-shrink-0" style="color:var(--orange);"></i>
            <div style="font-size:13px;color:#92400E;">
                <strong>Save these credentials now.</strong> The temporary password will not be shown again.
                The College Admin must change their password on first login.
            </div>
        </div>

        <div class="row g-3 mb-4">
            {{-- Login Email --}}
            <div class="col-md-6">
                <div class="p-3 rounded" style="background:var(--surface2);border:1px solid var(--border);">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:4px;">Login Email</div>
                    <div style="font-size:10px;color:var(--muted);margin-bottom:8px;">Admin types this at /login to sign in</div>
                    <div class="d-flex align-items-center gap-2">
                        <code style="font-size:13px;flex:1;background:var(--surface);padding:8px 12px;border-radius:6px;border:1px solid var(--border);word-break:break-all;">{{ $creds['admin_email'] }}</code>
                        <button class="btn-icon" onclick="copyText('{{ $creds['admin_email'] }}', this)" title="Copy">
                            <i class="fa-solid fa-copy" style="font-size:12px;"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Credentials Delivered To --}}
            <div class="col-md-6">
                <div class="p-3 rounded" style="background:rgba(37,99,235,.05);border:2px solid rgba(37,99,235,.25);">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--blue);margin-bottom:4px;">
                        <i class="fa-solid fa-envelope-circle-check me-1"></i>Credentials Delivered To
                    </div>
                    <div style="font-size:10px;color:var(--muted);margin-bottom:8px;">Welcome email was sent to this Principal Email inbox</div>
                    <div class="d-flex align-items-center gap-2">
                        <code style="font-size:13px;flex:1;background:var(--surface);padding:8px 12px;border-radius:6px;border:1px solid rgba(37,99,235,.2);color:var(--blue);font-weight:700;word-break:break-all;">{{ $creds['recipient_email'] ?? $creds['admin_email'] }}</code>
                        <button class="btn-icon" onclick="copyText('{{ $creds['recipient_email'] ?? $creds['admin_email'] }}', this)" title="Copy">
                            <i class="fa-solid fa-copy" style="font-size:12px;"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Temporary Password --}}
            <div class="col-md-6">
                <div class="p-3 rounded" style="background:var(--surface2);border:1px solid var(--border);">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:8px;">Temporary Password</div>
                    <div class="d-flex align-items-center gap-2">
                        <code id="tempPwDisplay" style="font-size:14px;flex:1;background:var(--surface);padding:8px 12px;border-radius:6px;border:1px solid var(--border);">{{ $creds['temporary_password'] }}</code>
                        <button class="btn-icon" onclick="copyText('{{ $creds['temporary_password'] }}', this)" title="Copy password">
                            <i class="fa-solid fa-copy" style="font-size:12px;"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Email Delivery Status --}}
            <div class="col-md-6">
                <div class="p-3 rounded" style="background:var(--surface2);border:1px solid var(--border);">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:8px;">Email Delivery Status</div>
                    <div style="padding:4px 0;">
                        @if($creds['email_sent'])
                            <span class="badge badge-active" style="font-size:12px;padding:6px 10px;">
                                <i class="fa-solid fa-envelope-circle-check me-1"></i>✓ Delivered to Principal Inbox
                            </span>
                            <div style="font-size:11px;color:var(--muted);margin-top:6px;">
                                Log #{{ $creds['email_log_id'] ?? '—' }} · See Delivery Verification below
                            </div>
                        @else
                            <span style="background:rgba(239,68,68,.1);color:var(--red);border:1px solid rgba(239,68,68,.25);border-radius:6px;padding:5px 10px;font-size:12px;font-weight:700;display:inline-block;">
                                <i class="fa-solid fa-circle-xmark me-1"></i>❌ Delivery Failed
                            </span>
                            @php $lastFailedLog = \App\Models\EmailLog::where('tenant_id',$tenant->id)->where('status','failed')->latest()->first(); @endphp
                            @if($lastFailedLog?->error_message)
                            <div style="font-size:11px;color:var(--red);margin-top:6px;">{{ Str::limit($lastFailedLog->error_message, 100) }}</div>
                            @endif
                            <a href="/debug-email-delivery" target="_blank" style="font-size:11px;color:var(--blue);display:inline-block;margin-top:4px;">
                                <i class="fa-solid fa-stethoscope me-1"></i>Run SMTP Diagnostic →
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <button class="btn-primary" onclick="copyAllCredentials()">
                <i class="fa-solid fa-clipboard"></i> Copy All Credentials
            </button>
            <form method="POST" action="{{ route('super.tenants.resend-credentials', $tenant) }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn-secondary">
                    <i class="fa-solid fa-paper-plane"></i> Resend Email
                </button>
            </form>
            <form method="POST" action="{{ route('super.tenants.impersonate', $tenant) }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn-secondary" style="border-color:var(--purple);color:var(--purple);">
                    <i class="fa-solid fa-right-to-bracket"></i> Login As College Admin
                </button>
            </form>
        </div>
    </div>
</div>
@endif

{{-- KPI Row --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="kpi-card blue"><div class="kpi-icon blue mb-2"><i class="fa-solid fa-user-graduate"></i></div><div class="kpi-value">{{ $tenant->students_count ?? 0 }}</div><div class="kpi-label">Students</div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card green"><div class="kpi-icon green mb-2"><i class="fa-solid fa-users"></i></div><div class="kpi-value">{{ $tenant->staff_count ?? 0 }}</div><div class="kpi-label">Staff</div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card {{ $onboarding['percentage'] === 100 ? 'green' : ($onboarding['percentage'] >= 60 ? 'orange' : 'red') }}">
            <div class="kpi-icon {{ $onboarding['percentage'] === 100 ? 'green' : ($onboarding['percentage'] >= 60 ? 'orange' : 'red') }} mb-2">
                <i class="fa-solid fa-{{ $onboarding['is_complete'] ? 'circle-check' : 'circle-half-stroke' }}"></i>
            </div>
            <div class="kpi-value">{{ $onboarding['percentage'] }}%</div>
            <div class="kpi-label">Setup Complete</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card {{ $tenant->status === 'active' ? 'green' : 'red' }}">
            <div class="kpi-icon {{ $tenant->status === 'active' ? 'green' : 'red' }} mb-2">
                <i class="fa-solid fa-circle-{{ $tenant->status === 'active' ? 'check' : 'xmark' }}"></i>
            </div>
            <div class="kpi-value" style="font-size:18px;">{{ ucfirst($tenant->status) }}</div>
            <div class="kpi-label">Status</div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Left: Onboarding Checklist + Quick Actions --}}
    <div class="col-lg-5">

        {{-- Onboarding Progress Card --}}
        <div class="card mb-4">
            <div class="card-header">
                <span style="font-weight:700;">Setup Progress</span>
                <span style="font-size:13px;font-weight:700;color:{{ $onboarding['percentage'] === 100 ? 'var(--green)' : ($onboarding['percentage'] >= 60 ? 'var(--orange)' : 'var(--red)') }};">
                    {{ $onboarding['completed_count'] }}/{{ $onboarding['total'] }} Complete
                </span>
            </div>
            <div class="card-body">
                {{-- Progress Bar --}}
                <div class="mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span style="font-size:13px;font-weight:600;color:var(--text);">
                            {{ $onboarding['percentage'] }}% Complete
                        </span>
                        @if($onboarding['is_complete'])
                        <span class="badge badge-active"><i class="fa-solid fa-circle-check me-1"></i>Fully Configured</span>
                        @else
                        <span style="font-size:12px;color:var(--muted);">{{ $onboarding['total'] - $onboarding['completed_count'] }} step(s) remaining</span>
                        @endif
                    </div>
                    <div style="height:8px;background:var(--border);border-radius:4px;overflow:hidden;">
                        <div style="height:100%;width:{{ $onboarding['percentage'] }}%;background:{{ $onboarding['percentage'] === 100 ? 'var(--green)' : ($onboarding['percentage'] >= 60 ? 'var(--orange)' : 'var(--red)') }};border-radius:4px;transition:width .5s ease;"></div>
                    </div>
                </div>

                {{-- Steps --}}
                <div class="d-flex flex-column gap-1">
                    @foreach($onboarding['steps'] as $step)
                    <div class="d-flex align-items-start gap-3 p-3 rounded"
                         style="background:{{ $step['done'] ? 'rgba(16,185,129,.06)' : 'rgba(239,68,68,.04)' }};border:1px solid {{ $step['done'] ? 'rgba(16,185,129,.15)' : 'rgba(239,68,68,.12)' }};">
                        <div style="width:28px;height:28px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;
                            background:{{ $step['done'] ? 'var(--green)' : 'rgba(239,68,68,.15)' }};
                            color:{{ $step['done'] ? '#fff' : 'var(--red)' }};">
                            @if($step['done'])
                                <i class="fa-solid fa-check" style="font-size:11px;"></i>
                            @else
                                {{ $step['number'] }}
                            @endif
                        </div>
                        <div class="flex-1">
                            <div style="font-size:13px;font-weight:600;color:var(--text);line-height:1.3;">{{ $step['label'] }}</div>
                            <div style="font-size:11px;color:var(--muted);margin-top:2px;">{{ $step['detail'] }}</div>
                        </div>
                        <div style="flex-shrink:0;">
                            @if($step['done'])
                                <i class="fa-solid fa-circle-check" style="color:var(--green);font-size:14px;"></i>
                            @else
                                <i class="fa-solid fa-triangle-exclamation" style="color:var(--orange);font-size:14px;"></i>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                @if(!$onboarding['is_complete'])
                <div class="mt-3 p-3 rounded" style="background:rgba(37,99,235,.06);border:1px solid rgba(37,99,235,.15);">
                    <div style="font-size:12px;color:var(--blue);display:flex;align-items:flex-start;gap:8px;">
                        <i class="fa-solid fa-circle-info mt-1 flex-shrink-0"></i>
                        <div>Steps 3–5 are completed by the College Admin after logging in. Share the admin credentials to continue setup.</div>
                    </div>
                </div>
                @else
                <div class="mt-3 p-3 rounded" style="background:rgba(16,185,129,.06);border:1px solid rgba(16,185,129,.15);">
                    <div style="font-size:12px;color:var(--green);display:flex;align-items:center;gap:8px;">
                        <i class="fa-solid fa-circle-check flex-shrink-0"></i>
                        <div>This institution is fully configured and operational.</div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="card">
            <div class="card-header"><span style="font-weight:700;font-size:13px;">Quick Actions</span></div>
            <div class="card-body d-flex flex-column gap-2">
                <a href="{{ route('super.tenants.edit', $tenant) }}" class="btn-primary w-100" style="justify-content:center;">
                    <i class="fa-solid fa-pen"></i> Edit Institution
                </a>

                {{-- Add / Manage College Admin --}}
                <a href="{{ route('super.tenants.admin.create', $tenant) }}"
                   class="w-100 d-flex align-items-center justify-content-center gap-2"
                   style="background:var(--green);border:1px solid #047857;color:#fff;font-weight:600;font-size:13px;border-radius:var(--radius);padding:8px 16px;transition:var(--tr);"
                   onmouseover="this.style.background='#047857'" onmouseout="this.style.background='var(--green)'">
                    <i class="fa-solid fa-user-plus"></i>
                    {{ $onboarding['has_admin'] ? 'Manage College Admin' : 'Add College Admin' }}
                </a>

                {{-- Reset Password --}}
                @if($onboarding['has_admin'])
                <form method="POST" action="{{ route('super.tenants.resend-credentials', $tenant) }}">
                    @csrf
                    <button type="submit" class="btn-secondary w-100" style="justify-content:center;">
                        <i class="fa-solid fa-key"></i> Reset Admin Password & Resend Email
                    </button>
                </form>
                @endif

                {{-- Login As College Admin (Impersonation) --}}
                @if($onboarding['has_admin'])
                <form method="POST" action="{{ route('super.tenants.impersonate', $tenant) }}">
                    @csrf
                    <button type="submit" class="w-100 d-flex align-items-center justify-content-center gap-2"
                            style="background:var(--purple);border:1px solid #5B21B6;color:#fff;font-weight:600;font-size:13px;border-radius:var(--radius);padding:8px 16px;transition:var(--tr);"
                            onmouseover="this.style.background='#5B21B6'" onmouseout="this.style.background='var(--purple)'">
                        <i class="fa-solid fa-right-to-bracket"></i> Login As College Admin
                    </button>
                </form>
                @endif

                {{-- Delete --}}
                <form id="del-tenant" method="POST" action="{{ route('super.tenants.destroy', $tenant) }}">@csrf @method('DELETE')</form>
                <button type="button" class="btn-danger w-100" style="justify-content:center;" data-confirm-delete="del-tenant" data-name="{{ $tenant->name }}">
                    <i class="fa-solid fa-trash"></i> Delete Institution
                </button>
            </div>
        </div>
    </div>

    {{-- Right: Institution Details + Delivery Verification --}}
    <div class="col-lg-7">
        {{-- ── Email Delivery Verification ─────────────────────────────── --}}
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-envelope-open-text" style="color:var(--blue);"></i>
                    <span style="font-weight:700;">Email Delivery Verification</span>
                </div>
                @php
                    $dvStatus = $emailDelivery['status'];
                    $dvColor  = match($dvStatus) {
                        'sent'       => 'var(--green)',
                        'failed'     => 'var(--red)',
                        'queued'     => 'var(--orange)',
                        'never_sent' => 'var(--muted)',
                        default      => 'var(--muted)',
                    };
                    $dvIcon = match($dvStatus) {
                        'sent'       => 'fa-circle-check',
                        'failed'     => 'fa-circle-xmark',
                        'queued'     => 'fa-clock',
                        'never_sent' => 'fa-minus-circle',
                        default      => 'fa-minus-circle',
                    };
                    $dvLabel = match($dvStatus) {
                        'sent'       => 'Delivered',
                        'failed'     => 'Failed',
                        'queued'     => 'Queued (not sent)',
                        'never_sent' => 'Never Sent',
                        default      => 'Unknown',
                    };
                @endphp
                <span style="font-size:12px;font-weight:700;color:{{ $dvColor }};">
                    <i class="fa-solid {{ $dvIcon }} me-1"></i>{{ $dvLabel }}
                </span>
            </div>
            <div class="card-body p-0">
                @php
                    $dvRows = [
                        [
                            'label' => 'Recipient Email',
                            'value' => $emailDelivery['recipient_email'] ?? '—',
                            'note'  => 'Where the welcome email was sent',
                            'mono'  => true,
                            'highlight' => true,
                        ],
                        [
                            'label' => 'Login Email',
                            'value' => $emailDelivery['login_email'] ?? '—',
                            'note'  => 'What the admin types at /login',
                            'mono'  => true,
                            'highlight' => false,
                        ],
                        [
                            'label' => 'Last Email Sent',
                            'value' => $emailDelivery['last_sent_at']
                                ? \Carbon\Carbon::parse($emailDelivery['last_sent_at'])->format('d M Y, h:i A')
                                : '—',
                            'note'  => $emailDelivery['last_sent_at']
                                ? \Carbon\Carbon::parse($emailDelivery['last_sent_at'])->diffForHumans()
                                : 'No delivery recorded',
                            'mono'  => false,
                            'highlight' => false,
                        ],
                        [
                            'label' => 'Delivery Status',
                            'value' => $dvLabel,
                            'note'  => 'From email_logs table (log #' . ($emailDelivery['log_id'] ?? 'n/a') . ', ' . ($emailDelivery['total_attempts'] ?? 0) . ' attempt(s))',
                            'mono'  => false,
                            'highlight' => false,
                            'status' => $dvStatus,
                        ],
                    ];
                @endphp
                @foreach($dvRows as $i => $r)
                <div class="px-4 py-3" style="border-bottom:{{ $i < count($dvRows)-1 ? '1px solid var(--border)' : 'none' }};background:{{ ($r['highlight'] ?? false) ? 'rgba(37,99,235,.03)' : 'transparent' }};">
                    <div class="d-flex align-items-start justify-content-between gap-3">
                        <div style="min-width:130px;">
                            <div style="font-size:11px;color:var(--muted);font-weight:600;">{{ $r['label'] }}</div>
                            @if(isset($r['note']))
                            <div style="font-size:10px;color:var(--muted);margin-top:2px;">{{ $r['note'] }}</div>
                            @endif
                        </div>
                        <div style="text-align:right;">
                            @if(isset($r['status']))
                                <span style="font-size:13px;font-weight:700;color:{{ $dvColor }};">
                                    <i class="fa-solid {{ $dvIcon }} me-1"></i>{{ $r['value'] }}
                                </span>
                            @elseif($r['mono'] ?? false)
                                <code style="font-size:12px;background:var(--surface2);padding:3px 8px;border-radius:4px;border:1px solid var(--border);">{{ $r['value'] }}</code>
                            @else
                                <span style="font-size:13px;font-weight:600;color:var(--text);">{{ $r['value'] }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach

                {{-- Error message row --}}
                @if($emailDelivery['error_message'])
                <div class="px-4 py-3" style="border-top:1px solid rgba(239,68,68,.15);background:rgba(239,68,68,.03);">
                    <div style="font-size:11px;font-weight:700;color:var(--red);margin-bottom:4px;">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i>Error Message
                    </div>
                    <code style="font-size:11px;color:var(--red);word-break:break-all;">{{ $emailDelivery['error_message'] }}</code>
                </div>
                @endif

                {{-- Action row --}}
                <div class="px-4 py-3 d-flex flex-wrap gap-2" style="border-top:1px solid var(--border);background:var(--surface2);">
                    @if($emailDelivery['status'] !== 'sent')
                    <form method="POST" action="{{ route('super.tenants.resend-credentials', $tenant) }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn-secondary" style="font-size:12px;padding:6px 12px;">
                            <i class="fa-solid fa-paper-plane me-1"></i>Send Email Now
                        </button>
                    </form>
                    @endif
                    <a href="/debug-email?to={{ urlencode($emailDelivery['recipient_email'] ?? '') }}"
                       target="_blank"
                       style="font-size:12px;padding:6px 12px;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);color:var(--text);font-weight:600;display:inline-flex;align-items:center;gap:6px;text-decoration:none;">
                        <i class="fa-solid fa-stethoscope"></i>Run SMTP Diagnostic
                    </a>
                    <a href="/test-email?to={{ urlencode($emailDelivery['recipient_email'] ?? '') }}"
                       target="_blank"
                       style="font-size:12px;padding:6px 12px;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);color:var(--text);font-weight:600;display:inline-flex;align-items:center;gap:6px;text-decoration:none;">
                        <i class="fa-solid fa-flask"></i>Quick SMTP Test
                    </a>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><span style="font-weight:700;">Institution Details</span></div>
            <div class="card-body p-0">
                @php $details = [
                    ['label'=>'Institution Name', 'value'=>$tenant->name],
                    ['label'=>'Slug',             'value'=>$tenant->slug, 'mono'=>true],
                    ['label'=>'Email',            'value'=>$tenant->email ?? '—'],
                    ['label'=>'Phone',            'value'=>$tenant->phone ?? '—'],
                    ['label'=>'Principal',        'value'=>$tenant->principal_name ?? '—'],
                    ['label'=>'Affiliation No',   'value'=>$tenant->affiliation_number ?? '—', 'mono'=>true],
                    ['label'=>'Website',          'value'=>$tenant->website ?? '—'],
                    ['label'=>'Custom Domain',    'value'=>$tenant->domain ?? '—'],
                ]; @endphp
                @foreach($details as $i => $d)
                <div class="d-flex align-items-center justify-content-between px-4 py-3" style="border-bottom:{{ $i < count($details)-1 ? '1px solid var(--border)' : 'none' }};">
                    <span style="font-size:12px;color:var(--muted);min-width:140px;">{{ $d['label'] }}</span>
                    <span style="font-size:13px;font-weight:600;color:var(--text);{{ isset($d['mono']) ? 'font-family:monospace;' : '' }}">{{ $d['value'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><span style="font-weight:700;">Address</span></div>
            <div class="card-body" style="font-size:13px;color:var(--text2);line-height:1.8;">
                {{ $tenant->address ?? '—' }}<br>
                @if($tenant->city){{ $tenant->city }}{{ $tenant->state ? ', '.$tenant->state : '' }}{{ $tenant->pincode ? ' — '.$tenant->pincode : '' }}@endif
            </div>
        </div>

        <div class="card">
            <div class="card-header"><span style="font-weight:700;">Subscription & Metadata</span></div>
            <div class="card-body p-0">
                @php $meta = [
                    ['label'=>'Subscription Start', 'value'=>$tenant->subscription_start?->format('d M Y') ?? 'Not set'],
                    ['label'=>'Subscription End',   'value'=>$tenant->subscription_end?->format('d M Y') ?? 'Not set'],
                    ['label'=>'Created',            'value'=>$tenant->created_at?->format('d M Y, h:i A')],
                    ['label'=>'Last Updated',       'value'=>$tenant->updated_at?->diffForHumans()],
                ]; @endphp
                @foreach($meta as $i => $m)
                <div class="d-flex align-items-center justify-content-between px-4 py-3" style="border-bottom:{{ $i < count($meta)-1 ? '1px solid var(--border)' : 'none' }};">
                    <span style="font-size:12px;color:var(--muted);">{{ $m['label'] }}</span>
                    <span style="font-size:12px;font-weight:600;color:var(--text);">{{ $m['value'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function copyText(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        const icon = btn.querySelector('i');
        icon.className = 'fa-solid fa-check';
        icon.style.color = 'var(--green)';
        setTimeout(() => { icon.className = 'fa-solid fa-copy'; icon.style.color = ''; }, 2000);
    });
}

function copyAllCredentials() {
    @if($creds && $creds['tenant_id'] == $tenant->id)
    const text = `EduTenant ERP — College Admin Credentials
Institution   : {{ $tenant->name }}
Login Email   : {{ $creds['admin_email'] ?? '' }}
Delivered To  : {{ $creds['recipient_email'] ?? $creds['admin_email'] ?? '' }}
Temp Password : {{ $creds['temporary_password'] ?? '' }}
Login URL     : {{ url('/login') }}

IMPORTANT: Admin must change password on first login.
Use "Login Email" to sign in at /login.
"Delivered To" is the inbox that received the welcome email.`;
    navigator.clipboard.writeText(text).then(() => {
        Swal.fire({ icon:'success', title:'Copied!', text:'Credentials copied to clipboard.',
            timer:2000, showConfirmButton:false, toast:true, position:'top-end' });
    });
    @endif
}

// ── Auto-fire success toast on page load when email was just sent ────────────
@if($creds && $creds['tenant_id'] == $tenant->id && $creds['email_sent'])
document.addEventListener('DOMContentLoaded', function () {
    setTimeout(function () {
        Swal.fire({
            icon: 'success',
            title: 'Welcome email sent!',
            html: 'Credentials delivered to:<br><strong style="font-size:15px;color:#059669;">{{ $creds['recipient_email'] ?? $creds['admin_email'] }}</strong>',
            timer: 6000,
            timerProgressBar: true,
            showConfirmButton: true,
            confirmButtonText: 'Got it',
            confirmButtonColor: '#2563EB',
            toast: false,
            position: 'center',
        });
    }, 600);
});
@elseif($creds && $creds['tenant_id'] == $tenant->id && !$creds['email_sent'])
document.addEventListener('DOMContentLoaded', function () {
    setTimeout(function () {
        Swal.fire({
            icon: 'warning',
            title: 'Institution created — email failed',
            html: 'Credentials were generated but the welcome email could not be delivered.<br><br>' +
                  'Use <strong>Resend Email</strong> below or check the <strong>Delivery Verification</strong> section.',
            confirmButtonText: 'OK',
            confirmButtonColor: '#dc2626',
        });
    }, 600);
});
@endif
</script>
@endpush
