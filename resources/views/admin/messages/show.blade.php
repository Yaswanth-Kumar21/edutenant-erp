@extends('layouts.app')
@section('title', 'Message Details')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.messages.index') }}" style="color:var(--primary);text-decoration:none;">Messages</a></li>
    <li class="breadcrumb-item active">{{ Str::limit($message->subject ?? 'Message', 30) }}</li>
@endsection
@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fa-solid fa-envelope me-2" style="color:#7c3aed;"></i>Message Details</h1>
    <a href="{{ route('admin.messages.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Back</a>
</div>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span style="font-weight:600;">{{ $message->subject ?? '(No Subject)' }}</span>
                @php $sc = ['sent'=>['#dcfce7','#166534'],'pending'=>['#fef3c7','#92400e'],'failed'=>['#fee2e2','#991b1b']][$message->status] ?? ['#f3f4f6','#374151']; @endphp
                <span class="badge" style="background:{{ $sc[0] }};color:{{ $sc[1] }};">{{ ucfirst($message->status) }}</span>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-4" style="font-size:.875rem;">
                    <div class="col-md-4">
                        <div style="color:var(--muted);font-size:.75rem;margin-bottom:2px;">Channel</div>
                        <span class="badge" style="background:rgba(124,58,237,.1);color:#7c3aed;">{{ strtoupper($message->channel) }}</span>
                    </div>
                    <div class="col-md-4">
                        <div style="color:var(--muted);font-size:.75rem;margin-bottom:2px;">Recipients</div>
                        <div style="font-weight:500;">{{ ucfirst($message->recipient_type) }}</div>
                    </div>
                    <div class="col-md-4">
                        <div style="color:var(--muted);font-size:.75rem;margin-bottom:2px;">Sent At</div>
                        <div>{{ $message->sent_at?->format('d M Y, h:i A') ?? $message->created_at?->format('d M Y') }}</div>
                    </div>
                    <div class="col-md-4">
                        <div style="color:var(--muted);font-size:.75rem;margin-bottom:2px;">Sent By</div>
                        <div>{{ $message->sentBy?->name ?? '—' }}</div>
                    </div>
                    @if($message->branch)
                    <div class="col-md-4">
                        <div style="color:var(--muted);font-size:.75rem;margin-bottom:2px;">Branch</div>
                        <div>{{ $message->branch->name }}</div>
                    </div>
                    @endif
                </div>
                <div class="p-3 rounded" style="background:var(--bg);border:1px solid var(--border);font-size:.875rem;line-height:1.7;">
                    {!! nl2br(e($message->body)) !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

