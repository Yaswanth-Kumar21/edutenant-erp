@extends('layouts.app')

@section('title', 'Payment � ' . $payment->receipt_number)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.fees.dashboard') }}" style="color:#4f46e5;text-decoration:none;">Fees</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.fees.payments.index') }}" style="color:#4f46e5;text-decoration:none;">Payments</a></li>
    <li class="breadcrumb-item active">{{ $payment->receipt_number }}</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-receipt me-2" style="color:#4f46e5;"></i>Payment Details</h1>
        <p class="mb-0" style="color:var(--muted);font-size:0.875rem;">{{ $payment->receipt_number }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.fees.payments.receipt', $payment) }}" class="btn btn-outline-primary">
            <i class="fa-solid fa-receipt me-2"></i> View Receipt
        </a>
        <a href="{{ route('admin.pdf.fee-receipt', $payment) }}" class="btn btn-outline-danger">
            <i class="fa-solid fa-file-pdf me-2"></i> PDF
        </a>
        @if($payment->status !== 'paid' && $payment->balance > 0)
        <a href="{{ route('admin.payments.pay-online', $payment) }}" class="btn btn-success">
            <i class="fa-solid fa-credit-card me-2"></i> Pay Online
        </a>
        @endif
        <a href="{{ route('admin.fees.payments.edit', $payment) }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-pen me-2"></i> Edit
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header"><i class="fa-solid fa-user-graduate me-2" style="color:#4f46e5;"></i>Student</div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ $payment->student?->photo_url }}" class="rounded-circle" style="width:52px;height:52px;object-fit:cover;">
                    <div>
                        <div style="font-weight:600;">{{ $payment->student?->full_name }}</div>
                        <div style="font-size:0.82rem;color:var(--muted);">
                            {{ $payment->student?->admission_number }} &bull; {{ $payment->student?->branch?->name }}
                        </div>
                    </div>
                    @if($payment->student)
                    <a href="{{ route('admin.students.show', $payment->student) }}" class="btn btn-sm btn-outline-primary ms-auto">
                        View Profile
                    </a>
                    @endif
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><i class="fa-solid fa-tags me-2" style="color:#059669;"></i>Fee Details</div>
            <div class="card-body">
                <dl class="row mb-0" style="font-size:0.875rem;">
                    <dt class="col-4" style="color:var(--muted);">Fee Type</dt>
                    <dd class="col-8 mb-2">{{ $payment->feeType?->name ?? '�' }}</dd>
                    <dt class="col-4" style="color:var(--muted);">Academic Year</dt>
                    <dd class="col-8 mb-2">{{ $payment->academicYear?->name ?? '�' }}</dd>
                    <dt class="col-4" style="color:var(--muted);">Semester</dt>
                    <dd class="col-8 mb-2">{{ $payment->semester ? 'Semester '.$payment->semester : '�' }}</dd>
                    <dt class="col-4" style="color:var(--muted);">Payment Mode</dt>
                    <dd class="col-8 mb-2">{{ \App\Models\FeePayment::PAYMENT_MODES[$payment->payment_mode] ?? ucfirst($payment->payment_mode) }}</dd>
                    <dt class="col-4" style="color:var(--muted);">Transaction Ref</dt>
                    <dd class="col-8 mb-2">{{ $payment->transaction_reference ?? '�' }}</dd>
                    <dt class="col-4" style="color:var(--muted);">Payment Date</dt>
                    <dd class="col-8 mb-2">{{ $payment->payment_date?->format('d M Y') }}</dd>
                    <dt class="col-4" style="color:var(--muted);">Collected By</dt>
                    <dd class="col-8 mb-2">{{ $payment->collectedBy?->name ?? '�' }}</dd>
                    @if($payment->remarks)
                    <dt class="col-4" style="color:var(--muted);">Remarks</dt>
                    <dd class="col-8 mb-0">{{ $payment->remarks }}</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><i class="fa-solid fa-calculator me-2" style="color:#7c3aed;"></i>Amount Summary</div>
            <div class="card-body">
                <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid var(--border);font-size:0.875rem;">
                    <span style="color:var(--muted);">Amount Due</span>
                    <strong>?{{ number_format($payment->amount_due, 2) }}</strong>
                </div>
                @if($payment->discount > 0)
                <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid var(--border);font-size:0.875rem;">
                    <span style="color:var(--muted);">Discount</span>
                    <strong style="color:#059669;">- ?{{ number_format($payment->discount, 2) }}</strong>
                </div>
                @endif
                @if($payment->fine > 0)
                <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid var(--border);font-size:0.875rem;">
                    <span style="color:var(--muted);">Fine</span>
                    <strong style="color:#dc2626;">+ ?{{ number_format($payment->fine, 2) }}</strong>
                </div>
                @endif
                <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid var(--border);font-size:0.875rem;">
                    <span style="color:var(--muted);">Amount Paid</span>
                    <strong style="color:#059669;">?{{ number_format($payment->amount_paid, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between py-3" style="font-size:1rem;font-weight:700;">
                    <span>Balance</span>
                    <span style="color:{{ $payment->balance > 0 ? '#dc2626' : '#059669' }};">
                        ?{{ number_format(max(0, $payment->balance), 2) }}
                    </span>
                </div>
                @php $sc = ['paid'=>['#dcfce7','#166534'],'partial'=>['#fef3c7','#92400e'],'pending'=>['#fee2e2','#991b1b'],'exempted'=>['#dbeafe','#1e40af']]; [$bg,$fg] = $sc[$payment->status] ?? ['#f3f4f6','#374151']; @endphp
                <div class="text-center p-2 rounded" style="background:{{ $bg }};color:{{ $fg }};font-weight:600;">
                    {{ ucfirst($payment->status) }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

