@extends('layouts.app')

@section('title', 'Fee Payments')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.fees.dashboard') }}" style="color:#4f46e5;text-decoration:none;">Fees</a>
    </li>
    <li class="breadcrumb-item active">Payments</li>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fa-solid fa-receipt me-2" style="color:#4f46e5;"></i>
            Fee Payments
        </h1>
        <p class="mb-0" style="color:var(--muted);font-size:0.875rem;">
            All fee transactions and payment history
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <div class="dropdown">
            <button class="btn btn-outline-success btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fa-solid fa-file-excel me-1"></i> Export
            </button>
            <ul class="dropdown-menu shadow border-0" style="border-radius:.75rem;background:var(--surface);">
                <li>
                    <a class="dropdown-item" style="font-size:.85rem;"
                       href="{{ route('admin.exports.fee-payments', array_merge(request()->query(), ['format'=>'xlsx'])) }}">
                        <i class="fa-solid fa-file-excel me-2" style="color:#059669;"></i> Excel (.xlsx)
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" style="font-size:.85rem;"
                       href="{{ route('admin.exports.fee-payments', array_merge(request()->query(), ['format'=>'csv'])) }}">
                        <i class="fa-solid fa-file-csv me-2" style="color:#0891b2;"></i> CSV
                    </a>
                </li>
            </ul>
        </div>
        <a href="{{ route('admin.fees.payments.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-2"></i> Collect Fee
        </a>
    </div>
</div>

{{-- Summary Totals --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="card p-3 text-center">
            <div style="font-size:1.5rem;font-weight:800;color:#059669;">₹{{ number_format($totals['collected']) }}</div>
            <div style="font-size:0.78rem;color:var(--muted);">Collected (filtered)</div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card p-3 text-center">
            <div style="font-size:1.5rem;font-weight:800;color:#dc2626;">₹{{ number_format($totals['pending']) }}</div>
            <div style="font-size:0.78rem;color:var(--muted);">Pending (filtered)</div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card p-3 text-center">
            <div style="font-size:1.5rem;font-weight:800;color:#4f46e5;">{{ number_format($totals['count']) }}</div>
            <div style="font-size:0.78rem;color:var(--muted);">Total Records</div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-3">
                <label class="form-label mb-1" style="font-size:0.8rem;">Search</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text" style="background:var(--bg);border-color:var(--border);">
                        <i class="fa-solid fa-search" style="color:var(--muted);font-size:0.8rem;"></i>
                    </span>
                    <input type="text" name="search" class="form-control"
                           placeholder="Student name, receipt no..."
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1" style="font-size:0.8rem;">Fee Type</label>
                <select name="fee_type_id" class="form-select form-select-sm">
                    <option value="">All Types</option>
                    @foreach($feeTypes as $ft)
                        <option value="{{ $ft->id }}" {{ request('fee_type_id') == $ft->id ? 'selected' : '' }}>
                            {{ $ft->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1" style="font-size:0.8rem;">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="paid"     {{ request('status') === 'paid'     ? 'selected' : '' }}>Paid</option>
                    <option value="partial"  {{ request('status') === 'partial'  ? 'selected' : '' }}>Partial</option>
                    <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                    <option value="exempted" {{ request('status') === 'exempted' ? 'selected' : '' }}>Exempted</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1" style="font-size:0.8rem;">Mode</label>
                <select name="payment_mode" class="form-select form-select-sm">
                    <option value="">All Modes</option>
                    @foreach(\App\Models\FeePayment::PAYMENT_MODES as $mode => $label)
                        <option value="{{ $mode }}" {{ request('payment_mode') === $mode ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-1">
                <label class="form-label mb-1" style="font-size:0.8rem;">From</label>
                <input type="date" name="date_from" class="form-control form-control-sm"
                       value="{{ request('date_from') }}">
            </div>
            <div class="col-6 col-md-1">
                <label class="form-label mb-1" style="font-size:0.8rem;">To</label>
                <input type="date" name="date_to" class="form-control form-control-sm"
                       value="{{ request('date_to') }}">
            </div>
            <div class="col-12 col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm flex-1">
                    <i class="fa-solid fa-filter"></i>
                </button>
                @if(request()->hasAny(['search','fee_type_id','status','payment_mode','date_from','date_to']))
                    <a href="{{ route('admin.fees.payments.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Payments Table --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span style="font-weight:600;">
            Payments
            <span class="badge ms-2" style="background:rgba(79,70,229,0.1);color:#4f46e5;">
                {{ $payments->total() }}
            </span>
        </span>
        <small style="color:var(--muted);">
            {{ $payments->firstItem() ?? 0 }}–{{ $payments->lastItem() ?? 0 }} of {{ $payments->total() }}
        </small>
    </div>

    @if($payments->isEmpty())
        <div class="empty-state">
            <i class="fa-solid fa-receipt"></i>
            <h5 style="color:var(--muted);">No payments found</h5>
            <p style="color:var(--muted);font-size:0.875rem;">
                @if(request()->hasAny(['search','fee_type_id','status']))
                    Try adjusting your filters.
                @else
                    <a href="{{ route('admin.fees.payments.create') }}" style="color:#4f46e5;">Collect the first fee payment</a>.
                @endif
            </p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Receipt No</th>
                        <th>Student</th>
                        <th>Fee Type</th>
                        <th>Due</th>
                        <th>Paid</th>
                        <th>Balance</th>
                        <th>Mode</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                    <tr>
                        <td>
                            <a href="{{ route('admin.fees.payments.receipt', $payment) }}"
                               style="font-family:monospace;font-size:0.82rem;color:#4f46e5;text-decoration:none;font-weight:500;">
                                {{ $payment->receipt_number }}
                            </a>
                        </td>
                        <td>
                        <a href="{{ $payment->student ? route('admin.students.show', $payment->student) : '#' }}"
                               style="color:var(--text);text-decoration:none;font-weight:500;font-size:0.875rem;">
                                {{ $payment->student?->full_name ?? '—' }}
                            </a>
                            <div style="font-size:0.72rem;color:var(--muted);">
                                {{ $payment->student?->admission_number }}
                            </div>
                        </td>
                        <td style="font-size:0.85rem;">{{ $payment->feeType?->name ?? '—' }}</td>
                        <td style="font-size:0.85rem;">₹{{ number_format($payment->amount_due) }}</td>
                        <td style="font-weight:600;color:#059669;font-size:0.85rem;">
                            ₹{{ number_format($payment->amount_paid) }}
                        </td>
                        <td style="font-size:0.85rem;color:{{ $payment->balance > 0 ? '#dc2626' : '#059669' }};">
                            ₹{{ number_format(max(0, $payment->balance)) }}
                        </td>
                        <td>
                            <span class="badge" style="background:rgba(79,70,229,0.1);color:#4f46e5;font-size:0.7rem;">
                                {{ strtoupper($payment->payment_mode) }}
                            </span>
                        </td>
                        <td style="font-size:0.82rem;color:var(--muted);">
                            {{ $payment->payment_date?->format('d M Y') }}
                        </td>
                        <td>
                            @switch($payment->status)
                                @case('paid')
                                    <span class="badge" style="background:#dcfce7;color:#166534;">Paid</span>
                                    @break
                                @case('partial')
                                    <span class="badge" style="background:#fef3c7;color:#92400e;">Partial</span>
                                    @break
                                @case('exempted')
                                    <span class="badge" style="background:#dbeafe;color:#1e40af;">Exempted</span>
                                    @break
                                @default
                                    <span class="badge" style="background:#fee2e2;color:#991b1b;">Pending</span>
                            @endswitch
                        </td>
                        <td class="text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="{{ route('admin.fees.payments.receipt', $payment) }}"
                                   class="btn btn-sm btn-outline-primary"
                                   style="padding:0.25rem 0.5rem;" title="Receipt">
                                    <i class="fa-solid fa-receipt" style="font-size:0.75rem;"></i>
                                </a>
                                <a href="{{ route('admin.fees.payments.edit', $payment) }}"
                                   class="btn btn-sm btn-outline-secondary"
                                   style="padding:0.25rem 0.5rem;" title="Edit">
                                    <i class="fa-solid fa-pen" style="font-size:0.75rem;"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
            <div class="card-footer d-flex align-items-center justify-content-between flex-wrap gap-2"
                 style="background:transparent;border-top:1px solid var(--border);">
                <small style="color:var(--muted);">
                    Page {{ $payments->currentPage() }} of {{ $payments->lastPage() }}
                </small>
                {{ $payments->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        @endif
    @endif
</div>

@endsection

