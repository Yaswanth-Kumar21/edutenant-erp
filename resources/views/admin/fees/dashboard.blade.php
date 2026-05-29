@extends('layouts.app')

@section('title', 'Fee Management Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Fee Dashboard</li>
@endsection

@push('styles')
<style>
.fee-stat-card {
    border-radius: 0.875rem;
    padding: 1.5rem;
    position: relative;
    overflow: hidden;
    transition: all 0.25s ease;
    border: 1px solid var(--border);
    background: var(--surface);
}
.fee-stat-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }
.fee-stat-card .icon-wrap {
    width: 48px; height: 48px;
    border-radius: 0.75rem;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}
.fee-stat-card .stat-num {
    font-size: 1.75rem; font-weight: 800; line-height: 1;
    margin-top: 0.75rem;
}
.fee-stat-card .stat-lbl {
    font-size: 0.78rem; color: var(--muted); margin-top: 0.25rem; font-weight: 500;
}
.fee-stat-card .trend {
    font-size: 0.75rem; font-weight: 600; margin-top: 0.5rem;
}
.pending-row { cursor: pointer; transition: background 0.15s; }
.pending-row:hover { background: rgba(220,38,38,0.04) !important; }
</style>
@endpush

@section('content')

{{-- ── Page Header ──────────────────────────────────────────────────────── --}}
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fa-solid fa-indian-rupee-sign me-2" style="color:#4f46e5;"></i>
            Fee Management
        </h1>
        <p class="mb-0" style="color:var(--muted);font-size:0.875rem;">
            Analytics, collections, and pending dues
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.fees.payments.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-2"></i> Collect Fee
        </a>
        <a href="{{ route('admin.fees.structures.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-layer-group me-2"></i> Fee Structures
        </a>
    </div>
</div>

{{-- ── Year Filter ──────────────────────────────────────────────────────── --}}
<div class="card mb-4">
    <div class="card-body py-2">
        <form method="GET" class="d-flex align-items-center gap-3 flex-wrap">
            <label class="form-label mb-0" style="font-size:0.82rem;white-space:nowrap;">Academic Year:</label>
            <select name="academic_year_id" class="form-select form-select-sm" style="width:auto;"
                    onchange="this.form.submit()">
                <option value="">All Years</option>
                @foreach($academicYears as $yr)
                    <option value="{{ $yr->id }}" {{ $yearId == $yr->id ? 'selected' : '' }}>
                        {{ $yr->name }} @if($yr->is_current) (Current) @endif
                    </option>
                @endforeach
            </select>
            @if($yearId)
                <a href="{{ route('admin.fees.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-xmark me-1"></i> Clear
                </a>
            @endif
        </form>
    </div>
</div>

{{-- ── Stats Cards ──────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl-2">
        <div class="fee-stat-card">
            <div class="icon-wrap" style="background:rgba(79,70,229,0.1);">
                <i class="fa-solid fa-indian-rupee-sign" style="color:#4f46e5;"></i>
            </div>
            <div class="stat-num" style="color:#4f46e5;">
                ₹{{ number_format($stats['totalCollected']) }}
            </div>
            <div class="stat-lbl">Total Collected</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="fee-stat-card">
            <div class="icon-wrap" style="background:rgba(220,38,38,0.1);">
                <i class="fa-solid fa-clock" style="color:#dc2626;"></i>
            </div>
            <div class="stat-num" style="color:#dc2626;">
                ₹{{ number_format($stats['totalPending']) }}
            </div>
            <div class="stat-lbl">Total Pending</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="fee-stat-card">
            <div class="icon-wrap" style="background:rgba(5,150,105,0.1);">
                <i class="fa-solid fa-calendar-day" style="color:#059669;"></i>
            </div>
            <div class="stat-num" style="color:#059669;">
                ₹{{ number_format($stats['todayCollected']) }}
            </div>
            <div class="stat-lbl">Today's Collection</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="fee-stat-card">
            <div class="icon-wrap" style="background:rgba(217,119,6,0.1);">
                <i class="fa-solid fa-calendar" style="color:#d97706;"></i>
            </div>
            <div class="stat-num" style="color:#d97706;">
                ₹{{ number_format($stats['monthCollected']) }}
            </div>
            <div class="stat-lbl">This Month</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="fee-stat-card">
            <div class="icon-wrap" style="background:rgba(124,58,237,0.1);">
                <i class="fa-solid fa-receipt" style="color:#7c3aed;"></i>
            </div>
            <div class="stat-num" style="color:#7c3aed;">
                {{ number_format($stats['totalTransactions']) }}
            </div>
            <div class="stat-lbl">Transactions</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="fee-stat-card">
            <div class="icon-wrap" style="background:rgba(8,145,178,0.1);">
                <i class="fa-solid fa-hand-holding-heart" style="color:#0891b2;"></i>
            </div>
            <div class="stat-num" style="color:#0891b2;">
                {{ $stats['exemptedCount'] }}
            </div>
            <div class="stat-lbl">Exemptions</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- ── Monthly Collection Chart ───────────────────────────────────────── --}}
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span>
                    <i class="fa-solid fa-chart-bar me-2" style="color:#4f46e5;"></i>
                    Monthly Fee Collection
                </span>
                <span style="font-size:0.75rem;color:var(--muted);">Last 12 months</span>
            </div>
            <div class="card-body">
                <canvas id="monthlyChart" height="100"></canvas>
            </div>
        </div>
    </div>

    {{-- ── Branch Collection ──────────────────────────────────────────────── --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="fa-solid fa-chart-pie me-2" style="color:#7c3aed;"></i>
                Collection by Branch
            </div>
            <div class="card-body">
                @if(empty($branchCollection))
                    <div class="empty-state py-3">
                        <i class="fa-solid fa-chart-pie d-block mb-2" style="font-size:2rem;"></i>
                        <p class="mb-0 small">No data yet</p>
                    </div>
                @else
                    <canvas id="branchChart" height="200"></canvas>
                    <div class="mt-3">
                        @foreach(array_slice($branchCollection, 0, 5) as $item)
                        <div class="d-flex justify-content-between align-items-center py-1"
                             style="font-size:0.82rem;border-bottom:1px solid var(--border);">
                            <span>{{ $item['branch'] }}</span>
                            <strong style="color:#4f46e5;">₹{{ number_format($item['total']) }}</strong>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- ── Recent Payments ────────────────────────────────────────────────── --}}
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span>
                    <i class="fa-solid fa-clock-rotate-left me-2" style="color:#059669;"></i>
                    Recent Payments
                </span>
                <a href="{{ route('admin.fees.payments.index') }}"
                   style="font-size:0.8rem;color:#4f46e5;text-decoration:none;">
                    View All <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
            </div>
            @if($recentPayments->isEmpty())
                <div class="empty-state py-4">
                    <i class="fa-solid fa-receipt d-block mb-2"></i>
                    <p class="mb-0 small">No payments recorded yet</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table mb-0" style="font-size:0.82rem;">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Fee Type</th>
                                <th>Amount</th>
                                <th>Mode</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentPayments as $payment)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.students.show', $payment->student) }}"
                                       style="color:var(--text);text-decoration:none;font-weight:500;">
                                        {{ $payment->student?->full_name ?? '—' }}
                                    </a>
                                    <div style="font-size:0.72rem;color:var(--muted);">
                                        {{ $payment->student?->admission_number }}
                                    </div>
                                </td>
                                <td>{{ $payment->feeType?->name ?? '—' }}</td>
                                <td style="font-weight:700;color:#059669;">
                                    ₹{{ number_format($payment->amount_paid) }}
                                </td>
                                <td>
                                    <span class="badge" style="background:rgba(79,70,229,0.1);color:#4f46e5;font-size:0.7rem;">
                                        {{ strtoupper($payment->payment_mode) }}
                                    </span>
                                </td>
                                <td style="color:var(--muted);">
                                    {{ $payment->payment_date?->format('d M') }}
                                </td>
                                <td>
                                    @if($payment->status === 'paid')
                                        <span class="badge" style="background:#dcfce7;color:#166534;">Paid</span>
                                    @elseif($payment->status === 'partial')
                                        <span class="badge" style="background:#fef3c7;color:#92400e;">Partial</span>
                                    @else
                                        <span class="badge" style="background:#fee2e2;color:#991b1b;">Pending</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- ── Pending Dues ───────────────────────────────────────────────────── --}}
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span>
                    <i class="fa-solid fa-triangle-exclamation me-2" style="color:#dc2626;"></i>
                    Pending Dues
                </span>
                <span class="badge" style="background:rgba(220,38,38,0.1);color:#dc2626;">
                    {{ $pendingDues->count() }} records
                </span>
            </div>
            @if($pendingDues->isEmpty())
                <div class="empty-state py-4">
                    <i class="fa-solid fa-circle-check d-block mb-2" style="color:#059669;"></i>
                    <p class="mb-0 small" style="color:#059669;">All fees are cleared!</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table mb-0" style="font-size:0.82rem;">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Fee</th>
                                <th>Due</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingDues as $due)
                            <tr class="pending-row"
                                onclick="window.location='{{ route('admin.fees.student.profile', $due->student) }}'">
                                <td>
                                    <div style="font-weight:500;">{{ $due->student?->full_name ?? '—' }}</div>
                                    <div style="font-size:0.72rem;color:var(--muted);">
                                        {{ $due->student?->branch?->name }}
                                    </div>
                                </td>
                                <td style="color:var(--muted);">{{ $due->feeType?->name ?? '—' }}</td>
                                <td style="font-weight:700;color:#dc2626;">
                                    ₹{{ number_format($due->amount_due - $due->amount_paid) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    // ── Monthly Collection Bar Chart ──────────────────────────────────────
    const monthlyData = @json($monthlyCollection);
    const monthCtx = document.getElementById('monthlyChart');
    if (monthCtx && monthlyData.length) {
        new Chart(monthCtx, {
            type: 'bar',
            data: {
                labels: monthlyData.map(d => d.month),
                datasets: [{
                    label: 'Fee Collection (₹)',
                    data: monthlyData.map(d => d.total),
                    backgroundColor: 'rgba(79,70,229,0.7)',
                    borderColor: '#4f46e5',
                    borderWidth: 1,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => '₹' + ctx.raw.toLocaleString('en-IN')
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: v => '₹' + (v/1000).toFixed(0) + 'K',
                            color: getComputedStyle(document.documentElement)
                                .getPropertyValue('--text-muted').trim() || '#6b7280'
                        },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: {
                        ticks: {
                            color: getComputedStyle(document.documentElement)
                                .getPropertyValue('--text-muted').trim() || '#6b7280'
                        },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // ── Branch Doughnut Chart ─────────────────────────────────────────────
    const branchData = @json($branchCollection);
    const branchCtx = document.getElementById('branchChart');
    if (branchCtx && branchData.length) {
        const colors = ['#4f46e5','#7c3aed','#059669','#d97706','#0891b2','#dc2626','#6b7280'];
        new Chart(branchCtx, {
            type: 'doughnut',
            data: {
                labels: branchData.map(d => d.branch),
                datasets: [{
                    data: branchData.map(d => d.total),
                    backgroundColor: colors.slice(0, branchData.length),
                    borderWidth: 2,
                    borderColor: getComputedStyle(document.documentElement)
                        .getPropertyValue('--surface').trim() || '#fff',
                }]
            },
            options: {
                cutout: '60%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ctx.label + ': ₹' + ctx.raw.toLocaleString('en-IN')
                        }
                    }
                }
            }
        });
    }
})();
</script>
@endpush

