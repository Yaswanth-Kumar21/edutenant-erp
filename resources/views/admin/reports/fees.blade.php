@extends('layouts.app')
@section('title', 'Fees Report')

@section('breadcrumb')
    <li class="breadcrumb-item active">Fees Report</li>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-file-invoice-dollar me-2" style="color:#d97706;"></i>Fees Report</h1>
        <p class="mb-0" style="color:var(--muted);font-size:.875rem;">Complete fee payment history and analysis</p>
    </div>
    <div class="d-flex gap-2">
        <div class="dropdown">
            <button class="btn btn-outline-success btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fa-solid fa-file-excel me-1"></i> Export
            </button>
            <ul class="dropdown-menu shadow border-0" style="border-radius:.75rem;background:var(--surface);">
                <li><a class="dropdown-item" style="font-size:.85rem;" href="{{ route('admin.exports.fee-payments', ['format'=>'xlsx']) }}"><i class="fa-solid fa-file-excel me-2" style="color:#059669;"></i> Excel (.xlsx)</a></li>
                <li><a class="dropdown-item" style="font-size:.85rem;" href="{{ route('admin.exports.fee-payments', ['format'=>'csv']) }}"><i class="fa-solid fa-file-csv me-2" style="color:#0891b2;"></i> CSV</a></li>
            </ul>
        </div>
    </div>
</div>

{{-- Summary --}}
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="stat-card orange">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label mb-2">Total Collected</div>
                    <div class="stat-value" style="font-size:1.5rem;">?{{ number_format($payments->where('status','paid')->sum('amount_paid')) }}</div>
                    <div class="mt-2" style="font-size:.78rem;opacity:.8;">All time</div>
                </div>
                <div class="stat-icon"><i class="fa-solid fa-circle-check"></i></div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card" style="background:linear-gradient(135deg,#dc2626,#ef4444);">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label mb-2">Total Pending</div>
                    <div class="stat-value" style="font-size:1.5rem;">?{{ number_format($payments->whereIn('status',['pending','partial'])->sum('amount_due')) }}</div>
                    <div class="mt-2" style="font-size:.78rem;opacity:.8;">Outstanding dues</div>
                </div>
                <div class="stat-icon"><i class="fa-solid fa-clock"></i></div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card purple">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label mb-2">Total Records</div>
                    <div class="stat-value">{{ $payments->total() }}</div>
                    <div class="mt-2" style="font-size:.78rem;opacity:.8;">All transactions</div>
                </div>
                <div class="stat-icon"><i class="fa-solid fa-receipt"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span style="font-weight:600;"><i class="fa-solid fa-list me-2" style="color:#d97706;"></i>All Fee Payments</span>
        <span class="badge" style="background:rgba(217,119,6,.1);color:#d97706;">{{ $payments->total() }} records</span>
    </div>
    <div class="card-body p-0">
        @if($payments->isEmpty())
        <div class="empty-state py-5">
            <i class="fa-solid fa-receipt d-block mb-2" style="font-size:2.5rem;opacity:.3;"></i>
            <div>No fee payments found.</div>
        </div>
        @else
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Receipt No</th>
                        <th>Student</th>
                        <th>Fee Type</th>
                        <th>Amount Due</th>
                        <th>Amount Paid</th>
                        <th>Balance</th>
                        <th>Date</th>
                        <th>Mode</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                    <tr>
                        <td><span style="font-family:monospace;font-size:.8rem;color:#4f46e5;font-weight:600;">{{ $payment->receipt_number }}</span></td>
                        <td>
                            <div style="font-weight:500;font-size:.875rem;">{{ $payment->student?->full_name ?? '—' }}</div>
                            <div style="font-size:.72rem;color:var(--muted);">{{ $payment->student?->admission_number }}</div>
                        </td>
                        <td style="font-size:.82rem;">{{ $payment->feeType?->name ?? '—' }}</td>
                        <td style="font-size:.85rem;">?{{ number_format($payment->amount_due, 2) }}</td>
                        <td style="font-weight:700;color:#059669;">?{{ number_format($payment->amount_paid, 2) }}</td>
                        <td style="font-size:.85rem;color:{{ $payment->balance > 0 ? '#dc2626' : '#059669' }};">
                            {{ $payment->balance > 0 ? '?'.number_format($payment->balance,2) : 'Nil' }}
                        </td>
                        <td style="font-size:.78rem;color:var(--muted);">{{ $payment->payment_date?->format('d M Y') }}</td>
                        <td><span class="badge" style="background:rgba(79,70,229,.1);color:#4f46e5;font-size:.7rem;">{{ ucfirst($payment->payment_mode) }}</span></td>
                        <td>
                            @php $sc = ['paid'=>['#dcfce7','#166534'],'partial'=>['#fef3c7','#92400e'],'pending'=>['#fee2e2','#991b1b']][$payment->status] ?? ['#f3f4f6','#374151']; @endphp
                            <span class="badge" style="background:{{ $sc[0] }};color:{{ $sc[1] }};font-size:.7rem;">{{ ucfirst($payment->status) }}</span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.fees.payments.receipt', $payment) }}" class="btn btn-sm btn-outline-primary" style="padding:.2rem .5rem;" title="Receipt"><i class="fa-solid fa-receipt" style="font-size:.72rem;"></i></a>
                                <a href="{{ route('admin.pdf.fee-receipt', $payment) }}" class="btn btn-sm btn-outline-danger" style="padding:.2rem .5rem;" title="PDF" target="_blank"><i class="fa-solid fa-file-pdf" style="font-size:.72rem;"></i></a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
        <div class="d-flex justify-content-center py-3">{{ $payments->links('pagination::bootstrap-5') }}</div>
        @endif
        @endif
    </div>
</div>

@endsection

