@extends('layouts.app')

@section('title', 'Fee Profile — ' . $student->full_name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.fees.dashboard') }}" style="color:#4f46e5;text-decoration:none;">Fees</a></li>
    <li class="breadcrumb-item active">{{ $student->full_name }}</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-user-graduate me-2" style="color:#4f46e5;"></i>Student Fee Profile</h1>
        <p class="mb-0" style="color:var(--muted);font-size:0.875rem;">{{ $student->admission_number }} — {{ $student->full_name }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.fees.payments.create') }}?student_id={{ $student->id }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-2"></i> Collect Fee
        </a>
        <a href="{{ route('admin.students.show', $student) }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-user me-2"></i> Student Profile
        </a>
    </div>
</div>

{{-- Student Header --}}
<div class="card mb-4 border-0" style="background:linear-gradient(135deg,#4f46e5,#7c3aed);border-radius:1rem;">
    <div class="card-body p-4">
        <div class="d-flex align-items-center gap-4 flex-wrap">
            <img src="{{ $student->photo_url }}" class="rounded-circle"
                 style="width:72px;height:72px;object-fit:cover;border:3px solid rgba(255,255,255,0.4);">
            <div class="flex-1">
                <h3 class="text-white fw-bold mb-1">{{ $student->full_name }}</h3>
                <p class="mb-0" style="color:rgba(255,255,255,0.8);font-size:0.875rem;">
                    {{ $student->branch?->name }} &bull; {{ $student->branch?->course?->name }}
                    &bull; Sem {{ $student->current_semester }} &bull; {{ $student->category }}
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Fee Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div style="font-size:1.5rem;font-weight:800;color:#4f46e5;">₹{{ number_format($feeSummary['total_due']) }}</div>
            <div style="font-size:0.78rem;color:var(--muted);">Total Due</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div style="font-size:1.5rem;font-weight:800;color:#059669;">₹{{ number_format($feeSummary['total_paid']) }}</div>
            <div style="font-size:0.78rem;color:var(--muted);">Total Paid</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div style="font-size:1.5rem;font-weight:800;color:#dc2626;">₹{{ number_format($feeSummary['total_pending']) }}</div>
            <div style="font-size:0.78rem;color:var(--muted);">Pending</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div style="font-size:1.5rem;font-weight:800;color:#d97706;">{{ $feeSummary['payment_count'] }}</div>
            <div style="font-size:0.78rem;color:var(--muted);">Transactions</div>
        </div>
    </div>
</div>

{{-- Pending Dues --}}
@if($pendingPayments->isNotEmpty())
<div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="fa-solid fa-triangle-exclamation me-2" style="color:#dc2626;"></i>Pending Dues</span>
        <span class="badge" style="background:rgba(220,38,38,0.1);color:#dc2626;">{{ $pendingPayments->count() }}</span>
    </div>
    <div class="table-responsive">
        <table class="table mb-0" style="font-size:0.875rem;">
            <thead><tr><th>Fee Type</th><th>Semester</th><th>Due</th><th>Paid</th><th>Balance</th><th>Status</th><th class="text-end">Action</th></tr></thead>
            <tbody>
                @foreach($pendingPayments as $p)
                <tr>
                    <td style="font-weight:500;">{{ $p->feeType?->name ?? '—' }}</td>
                    <td>{{ $p->semester ? 'Sem '.$p->semester : '—' }}</td>
                    <td>₹{{ number_format($p->amount_due) }}</td>
                    <td style="color:#059669;">₹{{ number_format($p->amount_paid) }}</td>
                    <td style="color:#dc2626;font-weight:600;">₹{{ number_format(max(0,$p->balance)) }}</td>
                    <td><span class="badge" style="background:#fef3c7;color:#92400e;">{{ ucfirst($p->status) }}</span></td>
                    <td class="text-end">
                        <a href="{{ route('admin.fees.payments.create') }}?student_id={{ $student->id }}"
                           class="btn btn-sm btn-primary" style="font-size:0.75rem;padding:0.25rem 0.6rem;">
                            Collect
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Payment History --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="fa-solid fa-clock-rotate-left me-2" style="color:#7c3aed;"></i>Payment History</span>
        <span class="badge" style="background:rgba(124,58,237,0.1);color:#7c3aed;">{{ $student->feePayments->count() }}</span>
    </div>
    @if($student->feePayments->isEmpty())
        <div class="empty-state py-4">
            <i class="fa-solid fa-receipt d-block mb-2"></i>
            <p class="mb-0 small">No payments recorded</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table mb-0" style="font-size:0.875rem;">
                <thead><tr><th>Receipt</th><th>Fee Type</th><th>Amount</th><th>Mode</th><th>Date</th><th>Status</th><th class="text-end">Receipt</th></tr></thead>
                <tbody>
                    @foreach($student->feePayments as $p)
                    <tr>
                        <td style="font-family:monospace;font-size:0.82rem;color:#4f46e5;">{{ $p->receipt_number }}</td>
                        <td>{{ $p->feeType?->name ?? '—' }}</td>
                        <td style="font-weight:600;color:#059669;">₹{{ number_format($p->amount_paid) }}</td>
                        <td><span class="badge" style="background:rgba(79,70,229,0.1);color:#4f46e5;font-size:0.7rem;">{{ strtoupper($p->payment_mode) }}</span></td>
                        <td style="color:var(--muted);">{{ $p->payment_date?->format('d M Y') }}</td>
                        <td>
                            @if($p->status === 'paid') <span class="badge" style="background:#dcfce7;color:#166534;">Paid</span>
                            @elseif($p->status === 'partial') <span class="badge" style="background:#fef3c7;color:#92400e;">Partial</span>
                            @elseif($p->status === 'exempted') <span class="badge" style="background:#dbeafe;color:#1e40af;">Exempted</span>
                            @else <span class="badge" style="background:#fee2e2;color:#991b1b;">Pending</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.fees.payments.receipt', $p) }}" class="btn btn-sm btn-outline-primary" style="padding:0.2rem 0.5rem;font-size:0.72rem;">
                                <i class="fa-solid fa-receipt"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background:var(--bg);">
                        <td colspan="2" style="font-weight:600;padding:0.75rem 1rem;">Total Paid</td>
                        <td style="font-weight:700;color:#059669;padding:0.75rem 1rem;">₹{{ number_format($feeSummary['total_paid']) }}</td>
                        <td colspan="4"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif
</div>
@endsection

