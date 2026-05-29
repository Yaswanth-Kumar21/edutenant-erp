@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')

{{-- ── Welcome Banner ── --}}
<div class="mb-4 rounded-3 overflow-hidden position-relative"
     style="background:linear-gradient(135deg,var(--primary-d) 0%,var(--primary) 60%,#4F46E5 100%);padding:2rem;">
    <div class="position-absolute" style="top:-60px;right:-60px;width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,.08);pointer-events:none;"></div>
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 position-relative">
        <div>
            <h2 class="text-white fw-bold mb-1" style="font-size:1.4rem;">
                Welcome back, {{ auth()->user()->name }} 👋
            </h2>
            <p class="mb-0" style="color:rgba(255,255,255,.75);font-size:.875rem;">
                {{ isset($tenant) ? $tenant->name : 'EduTenant ERP' }} &mdash; {{ now()->format('l, d F Y') }}
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.admissions.create') }}" class="btn btn-sm btn-light fw-600" style="font-weight:600;border-radius:8px;">
                <i class="fa-solid fa-user-plus me-1"></i> New Admission
            </a>
            <a href="{{ route('admin.fees.payments.create') }}" class="btn btn-sm" style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:8px;font-weight:500;">
                <i class="fa-solid fa-plus me-1"></i> Collect Fee
            </a>
        </div>
    </div>
</div>

{{-- ── Stats ── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="stat-card blue">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label mb-2">Total Students</div>
                    <div class="stat-value">{{ number_format($stats['total_students'] ?? 0) }}</div>
                    <div class="mt-1" style="font-size:.75rem;opacity:.85;">Active enrollments</div>
                </div>
                <i class="fa-solid fa-user-graduate stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card green">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label mb-2">Total Staff</div>
                    <div class="stat-value">{{ number_format($stats['total_staff'] ?? 0) }}</div>
                    <div class="mt-1" style="font-size:.75rem;opacity:.85;">{{ $stats['total_teaching'] ?? 0 }} Teaching</div>
                </div>
                <i class="fa-solid fa-users stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card orange">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label mb-2">Fees Today</div>
                    <div class="stat-value" style="font-size:1.5rem;">₹{{ number_format($stats['fees_today'] ?? 0) }}</div>
                    <div class="mt-1" style="font-size:.75rem;opacity:.85;">{{ now()->format('d M Y') }}</div>
                </div>
                <i class="fa-solid fa-indian-rupee-sign stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card purple">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label mb-2">Fees This Month</div>
                    <div class="stat-value" style="font-size:1.5rem;">₹{{ number_format($stats['fees_this_month'] ?? 0) }}</div>
                    <div class="mt-1" style="font-size:.75rem;opacity:.85;">{{ now()->format('F Y') }}</div>
                </div>
                <i class="fa-solid fa-chart-line stat-icon"></i>
            </div>
        </div>
    </div>
</div>

{{-- ── Quick Stats Row ── --}}
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="card p-3 text-center">
            <div style="font-size:1.6rem;font-weight:800;color:var(--primary);">₹{{ number_format($stats['pending_fees'] ?? 0) }}</div>
            <div style="font-size:.78rem;color:var(--muted);margin-top:4px;">Pending Fees</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card p-3 text-center">
            <div style="font-size:1.6rem;font-weight:800;color:#059669;">{{ $stats['new_admissions'] ?? 0 }}</div>
            <div style="font-size:.78rem;color:var(--muted);margin-top:4px;">New Admissions (This Month)</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card p-3 text-center">
            <div style="font-size:1.6rem;font-weight:800;color:#D97706;">{{ $stats['pending_leaves'] ?? 0 }}</div>
            <div style="font-size:.78rem;color:var(--muted);margin-top:4px;">Pending Leave Requests</div>
        </div>
    </div>
</div>

{{-- ── Charts ── --}}
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <div style="font-weight:700;color:var(--text);">Fee Collection Trend</div>
                    <div style="font-size:.75rem;color:var(--muted);">Last 6 months</div>
                </div>
                <span class="badge" style="background:rgba(37,99,235,.1);color:var(--primary);font-size:.7rem;">Monthly</span>
            </div>
            <div class="card-body">
                @if($monthlyFees->isEmpty())
                <div class="empty-state py-4"><i class="fa-solid fa-chart-line"></i><p class="mb-0 small">No fee data yet</p></div>
                @else
                <canvas id="feeChart" height="100"></canvas>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <div style="font-weight:700;color:var(--text);">Students by Branch</div>
                    <div style="font-size:.75rem;color:var(--muted);">Distribution</div>
                </div>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                @if($studentsByBranch->isEmpty())
                <div class="empty-state py-4"><i class="fa-solid fa-chart-pie"></i><p class="mb-0 small">No data</p></div>
                @else
                <canvas id="branchChart" style="max-height:200px;"></canvas>
                <div id="branch-legend" class="mt-3 w-100" style="font-size:.78rem;"></div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ── Recent Tables ── --}}
<div class="row g-3">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span style="font-weight:700;color:var(--text);"><i class="fa-solid fa-user-plus me-2" style="color:var(--primary);"></i>Recent Admissions</span>
                <a href="{{ route('admin.students.index') }}" style="font-size:.78rem;color:var(--primary);text-decoration:none;">View all →</a>
            </div>
            <div class="card-body p-0">
                @if($recentAdmissions->isEmpty())
                <div class="empty-state py-4"><i class="fa-solid fa-user-graduate"></i><p class="mb-0 small">No admissions yet</p></div>
                @else
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead><tr><th>Student</th><th>Branch</th><th>Date</th><th>Status</th></tr></thead>
                        <tbody>
                            @foreach($recentAdmissions as $student)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $student->photo_url }}" class="rounded-circle" style="width:30px;height:30px;object-fit:cover;" alt="">
                                        <div>
                                            <a href="{{ route('admin.students.show', $student) }}" style="font-size:.875rem;font-weight:500;color:var(--text);text-decoration:none;">{{ $student->full_name }}</a>
                                            <div style="font-size:.7rem;color:var(--muted);font-family:monospace;">{{ $student->admission_number }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="font-size:.82rem;color:var(--muted);">{{ $student->branch?->name ?? '—' }}</td>
                                <td style="font-size:.78rem;color:var(--muted);">{{ $student->admission_date?->format('d M') }}</td>
                                <td>
                                    @php $sc = ['active'=>['#DCFCE7','#166534'],'inactive'=>['#FEE2E2','#991B1B'],'passed_out'=>['#DBEAFE','#1E40AF'],'dropped'=>['#FEF3C7','#92400E']][$student->status] ?? ['#F3F4F6','#374151']; @endphp
                                    <span class="badge" style="background:{{ $sc[0] }};color:{{ $sc[1] }};font-size:.7rem;">{{ ucfirst($student->status) }}</span>
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
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span style="font-weight:700;color:var(--text);"><i class="fa-solid fa-money-bill-wave me-2" style="color:#059669;"></i>Recent Payments</span>
                <a href="{{ route('admin.fees.payments.index') }}" style="font-size:.78rem;color:var(--primary);text-decoration:none;">View all →</a>
            </div>
            <div class="card-body p-0">
                @if($recentPayments->isEmpty())
                <div class="empty-state py-4"><i class="fa-solid fa-receipt"></i><p class="mb-0 small">No payments yet</p></div>
                @else
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead><tr><th>Receipt</th><th>Student</th><th>Fee Type</th><th>Amount</th></tr></thead>
                        <tbody>
                            @foreach($recentPayments as $payment)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.fees.payments.receipt', $payment) }}" style="font-size:.78rem;font-family:monospace;color:var(--primary);text-decoration:none;font-weight:600;">{{ $payment->receipt_number }}</a>
                                </td>
                                <td style="font-size:.85rem;color:var(--text);">{{ $payment->student?->full_name ?? '—' }}</td>
                                <td style="font-size:.78rem;color:var(--muted);">{{ $payment->feeType?->name ?? '—' }}</td>
                                <td style="font-size:.875rem;font-weight:700;color:#059669;">₹{{ number_format($payment->amount_paid) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function(){
    @if(!$monthlyFees->isEmpty())
    const feeCtx = document.getElementById('feeChart');
    if(feeCtx){
        const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        const data   = @json($monthlyFees);
        const primary = getComputedStyle(document.body).getPropertyValue('--primary').trim() || '#2563EB';
        new Chart(feeCtx, {
            type: 'line',
            data: {
                labels: data.map(d => months[d.month-1]+' '+d.year),
                datasets: [{
                    label: 'Fee Collection (₹)',
                    data: data.map(d => parseFloat(d.total)),
                    borderColor: primary,
                    backgroundColor: primary.replace(')',',0.08)').replace('rgb','rgba'),
                    borderWidth: 2.5,
                    pointBackgroundColor: primary,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend:{display:false}, tooltip:{callbacks:{label:c=>'₹'+c.parsed.y.toLocaleString('en-IN')}} },
                scales: {
                    y: { beginAtZero:true, grid:{color:'rgba(0,0,0,0.04)'}, ticks:{callback:v=>'₹'+(v>=1000?(v/1000).toFixed(0)+'K':v),font:{size:11}} },
                    x: { grid:{display:false}, ticks:{font:{size:11}} }
                }
            }
        });
    }
    @endif

    @if(!$studentsByBranch->isEmpty())
    const branchCtx = document.getElementById('branchChart');
    if(branchCtx){
        const bd = @json($studentsByBranch);
        const colors = ['#2563EB','#059669','#D97706','#DC2626','#7C3AED','#0891B2','#DB2777','#65A30D'];
        new Chart(branchCtx, {
            type: 'doughnut',
            data: {
                labels: bd.map(d => d.branch ? d.branch.name : 'Unknown'),
                datasets: [{ data: bd.map(d => d.count), backgroundColor: colors.slice(0,bd.length), borderWidth: 2, borderColor: '#fff', hoverOffset: 6 }]
            },
            options: { responsive:true, cutout:'65%', plugins:{legend:{display:false}} }
        });
        const legend = document.getElementById('branch-legend');
        if(legend) legend.innerHTML = bd.map((d,i) =>
            `<div class="d-flex align-items-center gap-2 mb-1">
                <div style="width:10px;height:10px;border-radius:50%;background:${colors[i]};flex-shrink:0;"></div>
                <span style="color:var(--muted);flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${d.branch?d.branch.name:'Unknown'}</span>
                <strong style="color:var(--text);">${d.count}</strong>
            </div>`
        ).join('');
    }
    @endif
})();
</script>
@endpush

