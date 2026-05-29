@extends('layouts.app')
@section('title', 'Daily Report')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.reports.annual') }}" style="color:var(--primary);text-decoration:none;">Reports</a></li>
    <li class="breadcrumb-item active">Daily Report</li>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-calendar-day me-2" style="color:#4f46e5;"></i>Daily Report</h1>
        <p class="mb-0" style="color:var(--muted);font-size:.875rem;">Fee collections and activity for a specific date</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.pdf.attendance-report', ['month' => now()->format('Y-m')]) }}"
           class="btn btn-outline-danger btn-sm" target="_blank">
            <i class="fa-solid fa-file-pdf me-1"></i> PDF
        </a>
    </div>
</div>

{{-- Date Picker --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="d-flex align-items-end gap-3 flex-wrap">
            <div>
                <label class="form-label mb-1" style="font-size:.8rem;">Select Date</label>
                <input type="date" name="date" class="form-control form-control-sm"
                       value="{{ $date }}" max="{{ today()->toDateString() }}"
                       onchange="this.form.submit()">
            </div>
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-search me-1"></i> View Report
            </button>
        </form>
    </div>
</div>

{{-- Summary --}}
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="stat-card blue">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label mb-2">Total Collected</div>
                    <div class="stat-value" style="font-size:1.6rem;">?{{ number_format($totalToday) }}</div>
                    <div class="mt-2" style="font-size:.78rem;opacity:.8;">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</div>
                </div>
                <div class="stat-icon"><i class="fa-solid fa-indian-rupee-sign"></i></div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card green">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label mb-2">Transactions</div>
                    <div class="stat-value">{{ $dailyFees->count() }}</div>
                    <div class="mt-2" style="font-size:.78rem;opacity:.8;">Payments recorded</div>
                </div>
                <div class="stat-icon"><i class="fa-solid fa-receipt"></i></div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card orange">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label mb-2">Avg per Transaction</div>
                    <div class="stat-value" style="font-size:1.6rem;">
                        ?{{ $dailyFees->count() > 0 ? number_format($totalToday / $dailyFees->count()) : 0 }}
                    </div>
                    <div class="mt-2" style="font-size:.78rem;opacity:.8;">Average amount</div>
                </div>
                <div class="stat-icon"><i class="fa-solid fa-chart-bar"></i></div>
            </div>
        </div>
    </div>
</div>

{{-- Transactions Table --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span style="font-weight:600;">
            <i class="fa-solid fa-list me-2" style="color:#4f46e5;"></i>
            Fee Transactions — {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
        </span>
        <span class="badge" style="background:rgba(79,70,229,.1);color:#4f46e5;">{{ $dailyFees->count() }} records</span>
    </div>
    <div class="card-body p-0">
        @if($dailyFees->isEmpty())
        <div class="empty-state py-5">
            <i class="fa-solid fa-calendar-day d-block mb-2" style="font-size:2.5rem;opacity:.3;"></i>
            <div style="font-size:.9rem;">No fee collections recorded for {{ \Carbon\Carbon::parse($date)->format('d M Y') }}.</div>
        </div>
        @else
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Receipt No</th>
                        <th>Student</th>
                        <th>Fee Type</th>
                        <th>Payment Mode</th>
                        <th>Amount Paid</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dailyFees as $payment)
                    <tr>
                        <td>
                            <span style="font-family:monospace;font-size:.82rem;color:#4f46e5;font-weight:600;">
                                {{ $payment->receipt_number }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $payment->student?->photo_url }}" class="rounded-circle"
                                     style="width:28px;height:28px;object-fit:cover;">
                                <div>
                                    <div style="font-size:.875rem;font-weight:500;">{{ $payment->student?->full_name }}</div>
                                    <div style="font-size:.72rem;color:var(--muted);">{{ $payment->student?->admission_number }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:.85rem;">{{ $payment->feeType?->name ?? '—' }}</td>
                        <td>
                            <span class="badge" style="background:rgba(79,70,229,.1);color:#4f46e5;font-size:.72rem;">
                                {{ ucfirst($payment->payment_mode) }}
                            </span>
                        </td>
                        <td style="font-weight:700;color:#059669;">?{{ number_format($payment->amount_paid, 2) }}</td>
                        <td>
                            <span class="badge" style="background:#dcfce7;color:#166534;font-size:.72rem;">Paid</span>
                        </td>
                        <td>
                            <a href="{{ route('admin.fees.payments.receipt', $payment) }}"
                               class="btn btn-sm btn-outline-primary" style="padding:.2rem .5rem;" title="View Receipt">
                                <i class="fa-solid fa-receipt" style="font-size:.72rem;"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background:var(--bg);">
                        <td colspan="4" style="font-weight:700;padding:.875rem 1rem;">Total</td>
                        <td style="font-weight:800;color:#059669;font-size:1rem;padding:.875rem 1rem;">
                            ?{{ number_format($totalToday, 2) }}
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>
</div>

@endsection

