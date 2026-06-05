@extends('layouts.app')
@section('title', 'Payments Register')

@section('breadcrumb')
    <li class="breadcrumb-item">Finance</li>
    <li class="breadcrumb-item active">Payments Register</li>
@endsection

@push('styles')
<style>
.dash-card {
    border-radius: 12px;
    padding: 1.25rem 1.5rem;
    color: #fff;
    position: relative;
    overflow: hidden;
    transition: transform .2s, box-shadow .2s;
}
.dash-card:hover { transform: translateY(-3px); box-shadow: 0 10px 28px rgba(0,0,0,.18); }
.dash-card::after {
    content:''; position:absolute; top:-30px; right:-30px;
    width:110px; height:110px; border-radius:50%;
    background:rgba(255,255,255,.1);
}
.dash-card .dc-value { font-size: 1.75rem; font-weight: 800; line-height: 1.1; }
.dash-card .dc-label { font-size: .75rem; opacity: .85; font-weight: 500; margin-top:.2rem; }
.dash-card .dc-icon  { font-size: 2rem; opacity: .7; position:absolute; right:1.25rem; bottom:1rem; }
.dc-blue    { background: linear-gradient(135deg,#1e40af,#3b82f6); }
.dc-red     { background: linear-gradient(135deg,#dc2626,#ef4444); }
.dc-green   { background: linear-gradient(135deg,#059669,#10b981); }
.dc-orange  { background: linear-gradient(135deg,#d97706,#f59e0b); }
.dc-purple  { background: linear-gradient(135deg,#7c3aed,#a855f7); }
.dc-profit  { background: linear-gradient(135deg,#0f766e,#14b8a6); }

.profit-negative { background: linear-gradient(135deg,#991b1b,#dc2626) !important; }

.running-table { font-size:.8rem; }
.running-table th { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--muted); background:var(--surface2); }
.running-table td { padding:8px 12px; vertical-align:middle; }
.balance-positive { color:#059669; font-weight:700; }
.balance-negative { color:#dc2626; font-weight:700; }
</style>
@endpush

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fa-solid fa-book-open me-2" style="color:var(--primary);"></i>Payments Register
        </h1>
        <p class="mb-0" style="color:var(--muted);font-size:.875rem;">
            {{ $selectedYear?->name ?? 'All Years' }} — Financial Overview &amp; Daily Ledger
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
                       href="{{ route('admin.exports.payment-register', ['format'=>'xlsx','academic_year_id'=>$selectedYearId]) }}">
                        <i class="fa-solid fa-file-excel me-2" style="color:#059669;"></i> Excel (.xlsx)
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" style="font-size:.85rem;"
                       href="{{ route('admin.exports.payment-register', ['format'=>'csv','academic_year_id'=>$selectedYearId]) }}">
                        <i class="fa-solid fa-file-csv me-2" style="color:#0891b2;"></i> CSV
                    </a>
                </li>
            </ul>
        </div>
        <a href="{{ route('admin.payment-register.report', ['academic_year_id'=>$selectedYearId]) }}"
           class="btn btn-outline-secondary btn-sm" target="_blank">
            <i class="fa-solid fa-print me-1"></i> Print Register
        </a>
        <a href="{{ route('admin.payment-register.create') }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus me-1"></i> New Entry
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- ── Financial Dashboard Cards ─────────────────────────────────────────── --}}
<div class="row g-3 mb-4">

    {{-- Total Income --}}
    <div class="col-6 col-md-4 col-lg-2">
        <div class="dash-card dc-blue">
            <div class="dc-label">Total Income</div>
            <div class="dc-value">₹{{ number_format($totalIncome/1000, 1) }}K</div>
            <div style="font-size:.68rem;opacity:.75;margin-top:.2rem;">Fees + Other</div>
            <i class="fa-solid fa-arrow-trend-up dc-icon"></i>
        </div>
    </div>

    {{-- Total Spent --}}
    <div class="col-6 col-md-4 col-lg-2">
        <div class="dash-card dc-red">
            <div class="dc-label">Total Spent</div>
            <div class="dc-value">₹{{ number_format($totalSpentYear/1000, 1) }}K</div>
            <div style="font-size:.68rem;opacity:.75;margin-top:.2rem;">This Year</div>
            <i class="fa-solid fa-arrow-trend-down dc-icon"></i>
        </div>
    </div>

    {{-- Net Profit / Balance --}}
    <div class="col-6 col-md-4 col-lg-2">
        <div class="dash-card dc-profit {{ $netProfit < 0 ? 'profit-negative' : '' }}">
            <div class="dc-label">{{ $netProfit >= 0 ? 'Net Profit' : 'Net Loss' }}</div>
            <div class="dc-value">₹{{ number_format(abs($netProfit)/1000, 1) }}K</div>
            <div style="font-size:.68rem;opacity:.75;margin-top:.2rem;">Income − Spend</div>
            <i class="fa-solid fa-scale-balanced dc-icon"></i>
        </div>
    </div>

    {{-- Yesterday Spend --}}
    <div class="col-6 col-md-4 col-lg-2">
        <div class="dash-card dc-orange">
            <div class="dc-label">Yesterday</div>
            <div class="dc-value">₹{{ number_format($yesterdaySpend/1000, 1) }}K</div>
            <div style="font-size:.68rem;opacity:.75;margin-top:.2rem;">{{ now()->subDay()->format('d M') }}</div>
            <i class="fa-solid fa-calendar-day dc-icon"></i>
        </div>
    </div>

    {{-- Today Spend --}}
    <div class="col-6 col-md-4 col-lg-2">
        <div class="dash-card dc-purple">
            <div class="dc-label">Today</div>
            <div class="dc-value">₹{{ number_format($todaySpend/1000, 1) }}K</div>
            <div style="font-size:.68rem;opacity:.75;margin-top:.2rem;">{{ now()->format('d M') }}</div>
            <i class="fa-solid fa-calendar-check dc-icon"></i>
        </div>
    </div>

    {{-- Entries Count --}}
    <div class="col-6 col-md-4 col-lg-2">
        <div class="dash-card dc-green">
            <div class="dc-label">Entries</div>
            <div class="dc-value">{{ $entries->total() }}</div>
            <div style="font-size:.68rem;opacity:.75;margin-top:.2rem;">{{ $selectedYear?->name ?? 'All' }}</div>
            <i class="fa-solid fa-list dc-icon"></i>
        </div>
    </div>

</div>

{{-- ── Profit/Balance Progress Bar ──────────────────────────────────────────── --}}
@if($totalIncome > 0)
<div class="card mb-4">
    <div class="card-body py-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span style="font-size:.8rem;font-weight:600;color:var(--text);">
                Budget Utilization — {{ $selectedYear?->name ?? 'This Year' }}
            </span>
            <span style="font-size:.8rem;color:var(--muted);">
                ₹{{ number_format($totalSpentYear) }} spent of ₹{{ number_format($totalIncome) }} income
                ({{ $totalIncome > 0 ? number_format(($totalSpentYear / $totalIncome) * 100, 1) : 0 }}%)
            </span>
        </div>
        @php $pct = $totalIncome > 0 ? min(100, ($totalSpentYear / $totalIncome) * 100) : 0; @endphp
        <div class="progress" style="height:12px;border-radius:6px;">
            <div class="progress-bar {{ $pct > 90 ? 'bg-danger' : ($pct > 70 ? 'bg-warning' : 'bg-success') }}"
                 style="width:{{ $pct }}%;border-radius:6px;transition:width .6s ease;">
            </div>
        </div>
        <div class="d-flex justify-content-between mt-1">
            <span style="font-size:.72rem;color:#059669;">
                <i class="fa-solid fa-circle-check me-1"></i>
                Remaining: ₹{{ number_format(max(0, $netProfit)) }}
            </span>
            <span style="font-size:.72rem;color:{{ $pct > 90 ? '#dc2626' : 'var(--muted)' }};">
                {{ number_format($pct, 1) }}% utilized
            </span>
        </div>
    </div>
</div>
@endif

{{-- ── Running Daily Balance Table ──────────────────────────────────────────── --}}
@if(count($runningBalance) > 0)
<div class="card mb-4">
    <div class="card-header">
        <i class="fa-solid fa-chart-line me-2" style="color:var(--primary);"></i>
        Daily Spend &amp; Running Balance
        <span style="font-size:.72rem;color:var(--muted);font-weight:400;margin-left:.5rem;">
            (income ₹{{ number_format($totalIncome) }} − cumulative spend)
        </span>
    </div>
    <div class="card-body p-0">
        <div style="max-height:280px;overflow-y:auto;">
            <table class="table running-table mb-0">
                <thead style="position:sticky;top:0;z-index:1;">
                    <tr>
                        <th style="padding:8px 12px;">Date</th>
                        <th style="padding:8px 12px;">Day's Spend</th>
                        <th style="padding:8px 12px;">Cumulative Spend</th>
                        <th style="padding:8px 12px;">Remaining Balance</th>
                        <th style="padding:8px 12px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php $cumSpend = 0; @endphp
                    @foreach($runningBalance as $row)
                    @php
                        $cumSpend += $row['spend'];
                        $bal = $totalIncome - $cumSpend;
                    @endphp
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($row['date'])->format('d M Y') }}</td>
                        <td style="color:#dc2626;font-weight:600;">
                            ₹{{ number_format($row['spend'], 2) }}
                        </td>
                        <td style="color:var(--muted);">
                            ₹{{ number_format($cumSpend, 2) }}
                        </td>
                        <td class="{{ $bal >= 0 ? 'balance-positive' : 'balance-negative' }}">
                            ₹{{ number_format(abs($bal), 2) }}
                        </td>
                        <td>
                            @if($bal >= 0)
                                <span class="badge" style="background:rgba(5,150,105,.1);color:#059669;font-size:.68rem;">
                                    <i class="fa-solid fa-circle-check me-1"></i>Surplus
                                </span>
                            @else
                                <span class="badge" style="background:rgba(220,38,38,.1);color:#dc2626;font-size:.68rem;">
                                    <i class="fa-solid fa-triangle-exclamation me-1"></i>Deficit
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- ── Filters ───────────────────────────────────────────────────────────────── --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-3">
                <label class="form-label" style="font-size:.78rem;">Financial Year</label>
                <select name="academic_year_id" class="form-select form-select-sm">
                    <option value="">All Years</option>
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}" {{ $selectedYearId == $year->id ? 'selected' : '' }}>
                            {{ $year->name }} {{ $year->is_current ? '(Current)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label" style="font-size:.78rem;">From Date</label>
                <input type="date" name="date_from" class="form-control form-control-sm"
                       value="{{ request('date_from') }}">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label" style="font-size:.78rem;">To Date</label>
                <input type="date" name="date_to" class="form-control form-control-sm"
                       value="{{ request('date_to') }}">
            </div>
            <div class="col-12 col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100 mt-1">
                    <i class="fa-solid fa-filter me-1"></i> Apply
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── Entries Table ─────────────────────────────────────────────────────────── --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span style="font-weight:600;">
            <i class="fa-solid fa-table me-2" style="color:var(--primary);"></i>Payment Entries
        </span>
        <span class="badge" style="background:rgba(37,99,235,.1);color:var(--primary);">
            {{ $entries->total() }} records
        </span>
    </div>
    <div class="card-body p-0">
        @if($entries->count())
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Date</th>
                            <th>Staff Salary</th>
                            <th>Diesel</th>
                            <th>Electricity</th>
                            <th>Stationary</th>
                            <th>Travelling</th>
                            <th>Misc. Exp.</th>
                            <th style="color:var(--primary);">Row Total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($entries as $i => $entry)
                        <tr>
                            <td style="font-size:.78rem;color:var(--muted);">
                                {{ ($entries->currentPage() - 1) * $entries->perPage() + $i + 1 }}
                            </td>
                            <td style="font-size:.85rem;font-weight:500;white-space:nowrap;">
                                {{ $entry->entry_date?->format('d M Y') }}
                            </td>
                            <td style="font-size:.82rem;">₹{{ number_format($entry->staff_salary, 0) }}</td>
                            <td style="font-size:.82rem;">₹{{ number_format($entry->diesel_charges, 0) }}</td>
                            <td style="font-size:.82rem;">₹{{ number_format($entry->current_bill_charges, 0) }}</td>
                            <td style="font-size:.82rem;">₹{{ number_format($entry->stationary, 0) }}</td>
                            <td style="font-size:.82rem;">₹{{ number_format($entry->travelling_charges, 0) }}</td>
                            <td style="font-size:.82rem;">₹{{ number_format($entry->miscellaneous_expenditures, 0) }}</td>
                            <td style="font-weight:700;color:var(--primary);">
                                ₹{{ number_format($entry->total, 2) }}
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.payment-register.show', $entry) }}"
                                       class="btn btn-sm btn-outline-secondary" style="padding:.2rem .5rem;" title="View">
                                        <i class="fa-solid fa-eye" style="font-size:.72rem;"></i>
                                    </a>
                                    <a href="{{ route('admin.payment-register.edit', $entry) }}"
                                       class="btn btn-sm btn-outline-secondary" style="padding:.2rem .5rem;" title="Edit">
                                        <i class="fa-solid fa-pen" style="font-size:.72rem;"></i>
                                    </a>
                                    <form method="POST"
                                          action="{{ route('admin.payment-register.destroy', $entry) }}">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger"
                                                style="padding:.2rem .5rem;"
                                                onclick="return confirm('Delete this entry?')"
                                                title="Delete">
                                            <i class="fa-solid fa-trash" style="font-size:.72rem;"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background:var(--surface2);font-weight:700;">
                            <td colspan="8" style="text-align:right;font-size:.82rem;padding:10px 16px;">
                                Year Total
                            </td>
                            <td style="color:var(--primary);font-size:.9rem;padding:10px 16px;">
                                ₹{{ number_format($grandTotal, 2) }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @if($entries->hasPages())
                <div class="d-flex justify-content-center py-3">
                    {{ $entries->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            @endif
        @else
            <div class="empty-state">
                <i class="fa-solid fa-book-open"></i>
                <h5>No entries found</h5>
                <p>Start recording daily payments for this financial year.</p>
                <a href="{{ route('admin.payment-register.create') }}" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-plus me-1"></i> Add First Entry
                </a>
            </div>
        @endif
    </div>
</div>

@endsection
