@extends('layouts.app')
@section('title', 'Payroll Summary')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-chart-bar me-2" style="color:#d97706;"></i>Payroll Summary</h1>
        <p class="mb-0" style="color:var(--muted);font-size:.875rem;">
            {{ \Carbon\Carbon::create()->month($month)->format('F') }} {{ $year }}
        </p>
    </div>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-print me-1"></i> Print
        </button>
        <a href="{{ route('admin.payroll.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

{{-- Month Filter --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-6 col-md-2">
                <label class="form-label mb-1" style="font-size:.8rem;">Month</label>
                <select name="month" class="form-select form-select-sm">
                    @foreach(range(1,12) as $m)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1" style="font-size:.8rem;">Year</label>
                <select name="year" class="form-select form-select-sm">
                    @foreach(range(now()->year, now()->year - 3) as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-2">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-filter me-1"></i> View
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card blue">
            <div class="stat-label mb-1">Total Staff</div>
            <div class="stat-value">{{ $totals['count'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card green">
            <div class="stat-label mb-1">Total Gross</div>
            <div class="stat-value" style="font-size:1.4rem;">₹{{ number_format($totals['gross']) }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#dc2626,#ef4444);">
            <div class="stat-label mb-1">Total Deductions</div>
            <div class="stat-value" style="font-size:1.4rem;">₹{{ number_format($totals['deductions']) }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card orange">
            <div class="stat-label mb-1">Net Payable</div>
            <div class="stat-value" style="font-size:1.4rem;">₹{{ number_format($totals['net']) }}</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card p-3 text-center">
            <div style="font-size:1.4rem;font-weight:800;color:#059669;">₹{{ number_format($totals['paid']) }}</div>
            <div style="font-size:.78rem;color:var(--muted);">Already Paid</div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card p-3 text-center">
            <div style="font-size:1.4rem;font-weight:800;color:#d97706;">₹{{ number_format($totals['pending']) }}</div>
            <div style="font-size:.78rem;color:var(--muted);">Pending Payment</div>
        </div>
    </div>
</div>

{{-- Payroll Table --}}
@if($payrolls->isNotEmpty())
<div class="card">
    <div class="card-header">
        <span style="font-weight:600;">
            Payroll Details — {{ \Carbon\Carbon::create()->month($month)->format('F') }} {{ $year }}
        </span>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Staff Member</th>
                    <th class="text-center">Present</th>
                    <th class="text-center">Absent</th>
                    <th class="text-center">Gross</th>
                    <th class="text-center">Deductions</th>
                    <th class="text-center">Net Salary</th>
                    <th class="text-center">Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($payrolls as $i => $payroll)
                <tr>
                    <td style="color:var(--muted);font-size:.82rem;">{{ $i + 1 }}</td>
                    <td>
                        <div style="font-weight:500;font-size:.875rem;">{{ $payroll->staff->name }}</div>
                        <div style="font-size:.72rem;color:var(--muted);">{{ $payroll->staff->designation ?? '—' }}</div>
                    </td>
                    <td class="text-center"><span class="badge" style="background:#dcfce7;color:#166534;">{{ $payroll->present_days }}</span></td>
                    <td class="text-center"><span class="badge" style="background:#fee2e2;color:#991b1b;">{{ $payroll->absent_days }}</span></td>
                    <td class="text-center" style="font-size:.85rem;">₹{{ number_format($payroll->gross_salary) }}</td>
                    <td class="text-center" style="font-size:.85rem;color:#dc2626;">-₹{{ number_format($payroll->total_deductions) }}</td>
                    <td class="text-center" style="font-weight:700;color:#059669;">₹{{ number_format($payroll->net_salary) }}</td>
                    <td class="text-center">
                        @if($payroll->isPaid())
                            <span class="badge" style="background:#dcfce7;color:#166534;">Paid</span>
                        @elseif($payroll->isApproved())
                            <span class="badge" style="background:#dbeafe;color:#1e40af;">Approved</span>
                        @else
                            <span class="badge bg-secondary">Draft</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.payroll.slip', $payroll) }}"
                           class="btn btn-sm btn-outline-secondary" style="padding:.2rem .5rem;" target="_blank">
                            <i class="fa-solid fa-file-pdf" style="font-size:.75rem;"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:var(--bg);font-weight:700;">
                    <td colspan="4" class="text-end" style="padding:.75rem 1rem;">Totals:</td>
                    <td class="text-center">₹{{ number_format($totals['gross']) }}</td>
                    <td class="text-center" style="color:#dc2626;">-₹{{ number_format($totals['deductions']) }}</td>
                    <td class="text-center" style="color:#059669;">₹{{ number_format($totals['net']) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@else
<div class="card">
    <div class="card-body">
        <div class="empty-state py-4">
            <i class="fa-solid fa-file-invoice-dollar" style="font-size:3rem;opacity:.2;"></i>
            <h5 class="mt-3" style="color:var(--muted);">No payroll records for this period</h5>
            <a href="{{ route('admin.payroll.generate', ['month' => $month, 'year' => $year]) }}"
               class="btn btn-primary btn-sm mt-2">Generate Payroll</a>
        </div>
    </div>
</div>
@endif

@endsection

