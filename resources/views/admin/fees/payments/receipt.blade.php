@extends('layouts.app')

@section('title', 'Receipt — ' . $payment->receipt_number)

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.fees.dashboard') }}" style="color:#4f46e5;text-decoration:none;">Fees</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.fees.payments.index') }}" style="color:#4f46e5;text-decoration:none;">Payments</a>
    </li>
    <li class="breadcrumb-item active">Receipt</li>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fa-solid fa-receipt me-2" style="color:#4f46e5;"></i>
            Fee Receipt
        </h1>
        <p class="mb-0" style="color:var(--muted);font-size:0.875rem;">
            {{ $payment->receipt_number }}
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.pdf.fee-receipt', $payment) }}"
           class="btn btn-outline-danger" title="Download PDF">
            <i class="fa-solid fa-file-pdf me-2"></i> Download PDF
        </a>
        <a href="{{ route('admin.fees.payments.receipt.print', $payment) }}"
           target="_blank" class="btn btn-outline-primary">
            <i class="fa-solid fa-print me-2"></i> Print
        </a>
        @if($payment->student?->email || $payment->student?->user?->email)
        <form method="POST" action="{{ route('admin.notifications.send.fee-receipt', $payment) }}">
            @csrf
            <button type="submit" class="btn btn-outline-success" title="Email receipt to student">
                <i class="fa-solid fa-envelope me-2"></i> Email Receipt
            </button>
        </form>
        @endif
        <a href="{{ route('admin.fees.payments.create') }}?student_id={{ $payment->student_id }}"
           class="btn btn-primary">
            <i class="fa-solid fa-plus me-2"></i> Collect Another
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert d-flex align-items-center gap-3 mb-4"
     style="background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.25);border-radius:0.75rem;">
    <i class="fa-solid fa-circle-check" style="color:#059669;font-size:1.25rem;"></i>
    <div style="font-weight:500;color:#065f46;">{{ session('success') }}</div>
</div>
@endif

<div class="card" style="max-width:750px;margin:0 auto;">
    {{-- Receipt Header --}}
    <div style="background:linear-gradient(135deg,#4f46e5,#7c3aed);padding:1.75rem 2rem;border-radius:0.75rem 0.75rem 0 0;">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <div class="d-flex align-items-center gap-3 mb-1">
                    <div class="rounded-2 d-flex align-items-center justify-content-center"
                         style="width:44px;height:44px;background:rgba(255,255,255,0.2);">
                        <i class="fa-solid fa-graduation-cap text-white" style="font-size:1.2rem;"></i>
                    </div>
                    <div>
                        <div class="text-white fw-bold" style="font-size:1.05rem;">
                            {{ $tenant->name ?? 'EduTenant ERP' }}
                        </div>
                        <div style="color:rgba(255,255,255,0.65);font-size:0.78rem;">
                            {{ $tenant->address ?? '' }}
                            @if($tenant->city) &bull; {{ $tenant->city }} @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-end">
                <div style="color:rgba(255,255,255,0.6);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em;">
                    Fee Receipt
                </div>
                <div style="font-size:1rem;font-weight:800;font-family:monospace;color:#fff;margin-top:0.25rem;">
                    {{ $payment->receipt_number }}
                </div>
                <div style="color:rgba(255,255,255,0.65);font-size:0.78rem;margin-top:0.25rem;">
                    {{ $payment->payment_date?->format('d M Y') }}
                </div>
            </div>
        </div>
    </div>

    <div class="p-4">
        {{-- Student Info --}}
        <div class="d-flex align-items-center gap-3 mb-4">
            <img src="{{ $payment->student?->photo_url }}"
                 alt="{{ $payment->student?->full_name }}"
                 class="rounded-circle"
                 style="width:56px;height:56px;object-fit:cover;border:2px solid var(--border);">
            <div>
                <div style="font-weight:700;font-size:1rem;">{{ $payment->student?->full_name }}</div>
                <div style="font-size:0.82rem;color:var(--muted);">
                    <span style="font-family:monospace;color:#4f46e5;">{{ $payment->student?->admission_number }}</span>
                    &bull; {{ $payment->student?->branch?->name }}
                    &bull; {{ $payment->student?->category }}
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="p-3 rounded" style="background:var(--bg);border:1px solid var(--border);">
                    <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:var(--muted);margin-bottom:0.75rem;">
                        Payment Details
                    </div>
                    <dl class="row mb-0" style="font-size:0.82rem;">
                        <dt class="col-5" style="color:var(--muted);">Fee Type</dt>
                        <dd class="col-7 mb-1">{{ $payment->feeType?->name ?? '—' }}</dd>
                        <dt class="col-5" style="color:var(--muted);">Academic Year</dt>
                        <dd class="col-7 mb-1">{{ $payment->academicYear?->name ?? '—' }}</dd>
                        @if($payment->semester)
                        <dt class="col-5" style="color:var(--muted);">Semester</dt>
                        <dd class="col-7 mb-1">Semester {{ $payment->semester }}</dd>
                        @endif
                        <dt class="col-5" style="color:var(--muted);">Payment Mode</dt>
                        <dd class="col-7 mb-1">{{ \App\Models\FeePayment::PAYMENT_MODES[$payment->payment_mode] ?? ucfirst($payment->payment_mode) }}</dd>
                        @if($payment->transaction_reference)
                        <dt class="col-5" style="color:var(--muted);">Reference</dt>
                        <dd class="col-7 mb-0">{{ $payment->transaction_reference }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 rounded" style="background:var(--bg);border:1px solid var(--border);">
                    <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:var(--muted);margin-bottom:0.75rem;">
                        Amount Breakdown
                    </div>
                    <div class="d-flex justify-content-between mb-1" style="font-size:0.82rem;">
                        <span style="color:var(--muted);">Amount Due</span>
                        <span>₹{{ number_format($payment->amount_due, 2) }}</span>
                    </div>
                    @if($payment->discount > 0)
                    <div class="d-flex justify-content-between mb-1" style="font-size:0.82rem;">
                        <span style="color:var(--muted);">Discount</span>
                        <span style="color:#059669;">- ₹{{ number_format($payment->discount, 2) }}</span>
                    </div>
                    @endif
                    @if($payment->fine > 0)
                    <div class="d-flex justify-content-between mb-1" style="font-size:0.82rem;">
                        <span style="color:var(--muted);">Fine</span>
                        <span style="color:#dc2626;">+ ₹{{ number_format($payment->fine, 2) }}</span>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between mb-1 pt-1"
                         style="font-size:0.875rem;font-weight:700;border-top:1px solid var(--border);">
                        <span>Amount Paid</span>
                        <span style="color:#059669;">₹{{ number_format($payment->amount_paid, 2) }}</span>
                    </div>
                    @if($payment->balance > 0)
                    <div class="d-flex justify-content-between" style="font-size:0.82rem;">
                        <span style="color:var(--muted);">Balance Due</span>
                        <span style="color:#dc2626;font-weight:600;">₹{{ number_format($payment->balance, 2) }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Status --}}
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-3"
             style="border-top:1px solid var(--border);">
            <div style="font-size:0.75rem;color:var(--muted);">
                Collected by {{ $payment->collectedBy?->name ?? 'System' }}
                &bull; {{ $payment->created_at?->format('d M Y, h:i A') }}
            </div>
            @php
                $statusColors = ['paid' => ['#dcfce7','#166534'], 'partial' => ['#fef3c7','#92400e'], 'pending' => ['#fee2e2','#991b1b'], 'exempted' => ['#dbeafe','#1e40af']];
                [$bg, $fg] = $statusColors[$payment->status] ?? ['#f3f4f6','#374151'];
            @endphp
            <span class="badge" style="background:{{ $bg }};color:{{ $fg }};font-size:0.82rem;padding:0.4em 0.8em;">
                {{ ucfirst($payment->status) }}
            </span>
        </div>

        @if($payment->remarks)
        <div class="mt-3 p-2 rounded" style="background:var(--bg);font-size:0.82rem;color:var(--muted);">
            <i class="fa-solid fa-note-sticky me-1"></i> {{ $payment->remarks }}
        </div>
        @endif
    </div>
</div>

@endsection

