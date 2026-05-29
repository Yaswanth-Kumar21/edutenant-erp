@extends('layouts.app')
@section('title', 'Notifications & Messages')

@section('breadcrumb')
    <li class="breadcrumb-item active">Notifications</li>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-bell me-2" style="color:#7c3aed;"></i>Notifications & Messages</h1>
        <p class="mb-0" style="color:var(--muted);font-size:.875rem;">Send emails and manage college communications</p>
    </div>
    <a href="{{ route('admin.messages.create') }}" class="btn btn-primary btn-sm">
        <i class="fa-solid fa-plus me-1"></i> New Message
    </a>
</div>

{{-- Quick Email Actions --}}
<div class="row g-3 mb-4">
    {{-- Attendance Alerts --}}
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-2 d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;background:rgba(217,119,6,.1);flex-shrink:0;">
                        <i class="fa-solid fa-calendar-xmark" style="color:#d97706;font-size:1.3rem;"></i>
                    </div>
                    <div>
                        <div style="font-weight:700;">Attendance Alerts</div>
                        <div style="font-size:.78rem;color:var(--muted);">Email students below threshold</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.notifications.send.attendance-alerts') }}">
                    @csrf
                    <div class="input-group input-group-sm mb-2">
                        <span class="input-group-text">Below</span>
                        <input type="number" name="threshold" class="form-control" value="75" min="1" max="100">
                        <span class="input-group-text">%</span>
                    </div>
                    <button type="submit" class="btn btn-warning btn-sm w-100">
                        <i class="fa-solid fa-paper-plane me-1"></i> Send Alerts
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Email Info --}}
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-2 d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;background:rgba(79,70,229,.1);flex-shrink:0;">
                        <i class="fa-solid fa-envelope" style="color:#4f46e5;font-size:1.3rem;"></i>
                    </div>
                    <div>
                        <div style="font-weight:700;">Email Configuration</div>
                        <div style="font-size:.78rem;color:var(--muted);">Current mail settings</div>
                    </div>
                </div>
                <dl class="mb-0" style="font-size:.82rem;">
                    <div class="d-flex justify-content-between mb-1">
                        <span style="color:var(--muted);">Mailer</span>
                        <strong>{{ strtoupper(config('mail.default')) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span style="color:var(--muted);">From</span>
                        <strong>{{ config('mail.from.address') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span style="color:var(--muted);">Queue</span>
                        <span class="badge" style="background:{{ config('queue.default') === 'database' ? '#dcfce7' : '#fef3c7' }};color:{{ config('queue.default') === 'database' ? '#166534' : '#92400e' }};">
                            {{ strtoupper(config('queue.default')) }}
                        </span>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    {{-- SMS/WhatsApp Status --}}
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-2 d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;background:rgba(5,150,105,.1);flex-shrink:0;">
                        <i class="fa-solid fa-mobile-screen" style="color:#059669;font-size:1.3rem;"></i>
                    </div>
                    <div>
                        <div style="font-weight:700;">SMS / WhatsApp</div>
                        <div style="font-size:.78rem;color:var(--muted);">Twilio integration status</div>
                    </div>
                </div>
                <dl class="mb-0" style="font-size:.82rem;">
                    <div class="d-flex justify-content-between mb-1">
                        <span style="color:var(--muted);">SMS</span>
                        <span class="badge" style="background:{{ config('services.twilio.enabled') ? '#dcfce7' : '#fee2e2' }};color:{{ config('services.twilio.enabled') ? '#166534' : '#991b1b' }};">
                            {{ config('services.twilio.enabled') ? 'ENABLED' : 'DISABLED' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span style="color:var(--muted);">WhatsApp</span>
                        <span class="badge" style="background:{{ config('services.whatsapp.enabled') ? '#dcfce7' : '#fee2e2' }};color:{{ config('services.whatsapp.enabled') ? '#166534' : '#991b1b' }};">
                            {{ config('services.whatsapp.enabled') ? 'ENABLED' : 'DISABLED' }}
                        </span>
                    </div>
                    <div style="font-size:.72rem;color:var(--muted);">
                        Set <code>SMS_ENABLED=true</code> and Twilio credentials in <code>.env</code> to activate.
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>

{{-- Messages Table --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span style="font-weight:600;"><i class="fa-solid fa-list me-2" style="color:#7c3aed;"></i>Message History</span>
        <span class="badge" style="background:rgba(124,58,237,.1);color:#7c3aed;">{{ $messages->total() ?? 0 }}</span>
    </div>
    <div class="card-body p-0">
        @if(isset($messages) && $messages->count())
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Channel</th>
                        <th>Recipients</th>
                        <th>Status</th>
                        <th>Sent At</th>
                        <th>Sent By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($messages as $msg)
                    <tr>
                        <td>
                            <div style="font-weight:500;font-size:.875rem;">{{ $msg->subject }}</div>
                            <div style="font-size:.72rem;color:var(--muted);">{{ Str::limit($msg->body, 60) }}</div>
                        </td>
                        <td>
                            <span class="badge" style="background:rgba(79,70,229,.1);color:#4f46e5;font-size:.72rem;">
                                {{ strtoupper($msg->channel) }}
                            </span>
                        </td>
                        <td style="font-size:.82rem;">
                            {{ ucfirst($msg->recipient_type) }}
                            @if($msg->branch) &bull; {{ $msg->branch->name }} @endif
                        </td>
                        <td>
                            @php $sc = ['sent'=>['#dcfce7','#166534'],'pending'=>['#fef3c7','#92400e'],'failed'=>['#fee2e2','#991b1b']][$msg->status] ?? ['#f3f4f6','#374151']; @endphp
                            <span class="badge" style="background:{{ $sc[0] }};color:{{ $sc[1] }};font-size:.72rem;">
                                {{ ucfirst($msg->status) }}
                            </span>
                        </td>
                        <td style="font-size:.78rem;color:var(--muted);">
                            {{ $msg->sent_at?->format('d M Y, h:i A') ?? $msg->created_at?->format('d M Y') }}
                        </td>
                        <td style="font-size:.82rem;">{{ $msg->sentBy?->name ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($messages->hasPages())
        <div class="d-flex justify-content-center py-3">
            {{ $messages->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
        @endif
        @else
        <div class="text-center py-5" style="color:var(--muted);">
            <i class="fa-solid fa-envelope d-block mb-2" style="font-size:2.5rem;opacity:.3;"></i>
            <div>No messages sent yet.</div>
        </div>
        @endif
    </div>
</div>

@endsection

