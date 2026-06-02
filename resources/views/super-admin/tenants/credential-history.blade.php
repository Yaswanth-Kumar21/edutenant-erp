@extends('layouts.super-admin-app')
@section('title', 'Credential History — ' . $tenant->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('super.tenants.index') }}" style="color:var(--blue);">Institutions</a></li>
    <li class="breadcrumb-item"><a href="{{ route('super.tenants.show', $tenant) }}" style="color:var(--blue);">{{ Str::limit($tenant->name, 25) }}</a></li>
    <li class="breadcrumb-item active" style="color:var(--muted);">Credential History</li>
@endsection

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">Credential History</h1>
        <p class="page-sub">Password generation and reset audit trail for {{ $tenant->name }}</p>
    </div>
    <a href="{{ route('super.tenants.show', $tenant) }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Back to Institution
    </a>
</div>

{{-- Security Notice --}}
<div class="alert alert-info mb-4">
    <i class="fa-solid fa-shield-halved flex-shrink-0"></i>
    <div>
        <strong>Security Notice:</strong> This page shows password event metadata only. Plain-text passwords are never stored in the database.
        Only Super Admins can view this page.
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <span style="font-weight:700;">Password Event Log</span>
                @if($collegeAdmin)
                <span style="font-size:12px;color:var(--muted);">{{ $collegeAdmin->email }}</span>
                @endif
            </div>

            @if(!$collegeAdmin)
            <div class="card-body">
                <div class="empty-state">
                    <div class="empty-state-icon"><i class="fa-solid fa-user-slash"></i></div>
                    <h5>No College Admin Found</h5>
                    <p>This institution does not have a College Admin account yet.</p>
                    <a href="{{ route('super.tenants.admin.create', $tenant) }}" class="btn-primary">
                        <i class="fa-solid fa-user-plus"></i> Create College Admin
                    </a>
                </div>
            </div>
            @elseif($histories->isEmpty())
            <div class="card-body">
                <div class="empty-state">
                    <div class="empty-state-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
                    <h5>No History Yet</h5>
                    <p>No password events have been recorded for this admin.</p>
                </div>
            </div>
            @else
            <div class="card-body p-0">
                <table class="sa-table">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Action By</th>
                            <th>Email Sent</th>
                            <th>Date</th>
                            <th>Reset At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($histories as $h)
                        <tr>
                            <td>
                                @php
                                    $actionConfig = [
                                        'generated' => ['badge-blue',   'fa-wand-magic-sparkles', 'Generated'],
                                        'reset'     => ['badge-pending','fa-rotate-right',         'Reset'],
                                        'changed'   => ['badge-active', 'fa-check',                'Changed by Admin'],
                                    ][$h->action] ?? ['badge-blue', 'fa-circle', ucfirst($h->action)];
                                @endphp
                                <span class="badge {{ $actionConfig[0] }}">
                                    <i class="fa-solid {{ $actionConfig[1] }}"></i>
                                    {{ $actionConfig[2] }}
                                </span>
                            </td>
                            <td style="font-size:12px;color:var(--text2);">{{ $h->generated_by ?? '—' }}</td>
                            <td>
                                @if($h->email_sent)
                                    <span class="badge badge-active"><i class="fa-solid fa-envelope-circle-check"></i> Sent</span>
                                @else
                                    <span class="badge badge-inactive"><i class="fa-solid fa-envelope"></i> Not Sent</span>
                                @endif
                            </td>
                            <td style="font-size:12px;color:var(--muted);">{{ $h->created_at->format('d M Y, h:i A') }}</td>
                            <td style="font-size:12px;color:var(--muted);">{{ $h->reset_at?->format('d M Y, h:i A') ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($histories->hasPages())
            <div class="card-body border-top">
                {{ $histories->links() }}
            </div>
            @endif
            @endif
        </div>
    </div>

    <div class="col-lg-4">
        {{-- Admin Info --}}
        @if($collegeAdmin)
        <div class="card mb-3">
            <div class="card-header"><span style="font-weight:700;font-size:13px;">College Admin</span></div>
            <div class="card-body p-0">
                @php $info = [
                    ['label' => 'Name',   'value' => $collegeAdmin->name],
                    ['label' => 'Email',  'value' => $collegeAdmin->email, 'mono' => true],
                    ['label' => 'Status', 'value' => ucfirst($collegeAdmin->status)],
                    ['label' => 'Must Change PW', 'value' => $collegeAdmin->must_change_password ? 'Yes' : 'No'],
                    ['label' => 'PW Changed At', 'value' => $collegeAdmin->password_changed_at?->format('d M Y') ?? 'Never'],
                ]; @endphp
                @foreach($info as $i => $row)
                <div class="d-flex align-items-center justify-content-between px-4 py-3"
                     style="border-bottom:{{ $i < count($info)-1 ? '1px solid var(--border)' : 'none' }};">
                    <span style="font-size:12px;color:var(--muted);">{{ $row['label'] }}</span>
                    <span style="font-size:12px;font-weight:600;color:var(--text);{{ isset($row['mono']) ? 'font-family:monospace;' : '' }}">{{ $row['value'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Reset Password Action --}}
        <div class="card">
            <div class="card-header"><span style="font-weight:700;font-size:13px;">Actions</span></div>
            <div class="card-body d-flex flex-column gap-2">
                <form method="POST" action="{{ route('super.tenants.resend-credentials', $tenant) }}">
                    @csrf
                    <button type="submit" class="btn-primary w-100" style="justify-content:center;">
                        <i class="fa-solid fa-key"></i> Reset Password & Resend Email
                    </button>
                </form>
                <a href="{{ route('super.tenants.admin.create', $tenant) }}" class="btn-secondary w-100" style="justify-content:center;">
                    <i class="fa-solid fa-user-pen"></i> Manage Admin Account
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

@endsection
