@extends('layouts.app')

@section('title', 'Staff Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')

{{-- Welcome Banner --}}
<div class="mb-4 rounded-3 overflow-hidden position-relative"
     style="background:linear-gradient(135deg,#0F766E 0%,#14B8A6 60%,#06B6D4 100%);padding:2rem;">
    <div class="position-absolute" style="top:-60px;right:-60px;width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,.08);pointer-events:none;"></div>
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 position-relative">
        <div>
            <h2 class="text-white fw-bold mb-1" style="font-size:1.4rem;">
                Good {{ now()->hour < 12 ? 'Morning' : (now()->hour < 17 ? 'Afternoon' : 'Evening') }}, {{ auth()->user()->name }} 👋
            </h2>
            <p class="mb-0" style="color:rgba(255,255,255,.75);font-size:.875rem;">
                {{ $tenant->name }} &mdash; {{ now()->format('l, d F Y') }}
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.admissions.create') }}" class="btn btn-sm btn-light fw-600" style="font-weight:600;border-radius:8px;">
                <i class="fa-solid fa-user-plus me-1"></i> New Admission
            </a>
            <a href="{{ route('admin.fees.payments.create') }}" class="btn btn-sm" style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:8px;">
                <i class="fa-solid fa-hand-holding-dollar me-1"></i> Collect Fee
            </a>
        </div>
    </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="stat-card teal">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label mb-2">Total Students</div>
                    <div class="stat-value">{{ number_format($stats['total_students']) }}</div>
                    <div class="mt-1" style="font-size:.75rem;opacity:.85;">Active</div>
                </div>
                <i class="fa-solid fa-user-graduate stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card green">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label mb-2">Fees Today</div>
                    <div class="stat-value" style="font-size:1.5rem;">₹{{ number_format($stats['fees_today']) }}</div>
                    <div class="mt-1" style="font-size:.75rem;opacity:.85;">{{ now()->format('d M') }}</div>
                </div>
                <i class="fa-solid fa-indian-rupee-sign stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card orange">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label mb-2">Fees This Month</div>
                    <div class="stat-value" style="font-size:1.5rem;">₹{{ number_format($stats['fees_this_month']) }}</div>
                    <div class="mt-1" style="font-size:.75rem;opacity:.85;">{{ now()->format('F') }}</div>
                </div>
                <i class="fa-solid fa-chart-line stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card purple">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label mb-2">New Admissions</div>
                    <div class="stat-value">{{ $stats['new_admissions'] }}</div>
                    <div class="mt-1" style="font-size:.75rem;opacity:.85;">This month</div>
                </div>
                <i class="fa-solid fa-user-plus stat-icon"></i>
            </div>
        </div>
    </div>
</div>

{{-- Quick Actions --}}
<div class="card mb-4">
    <div class="card-header" style="font-weight:700;color:var(--text);">
        <i class="fa-solid fa-bolt me-2" style="color:var(--primary);"></i>Quick Actions
    </div>
    <div class="card-body">
        <div class="row g-3">
            @php $actions = [
                ['route'=>'admin.admissions.create','icon'=>'fa-user-plus','color'=>'#14B8A6','label'=>'New Admission'],
                ['route'=>'admin.fees.payments.create','icon'=>'fa-hand-holding-dollar','color'=>'#059669','label'=>'Collect Fee'],
                ['route'=>'admin.attendance.students','icon'=>'fa-calendar-check','color'=>'#D97706','label'=>'Mark Attendance'],
                ['route'=>'admin.students.index','icon'=>'fa-user-graduate','color'=>'#7C3AED','label'=>'View Students'],
                ['route'=>'admin.fees.payments.index','icon'=>'fa-receipt','color'=>'#0891B2','label'=>'Payment History'],
                ['route'=>'admin.messages.index','icon'=>'fa-envelope','color'=>'#DC2626','label'=>'Messages'],
            ]; @endphp
            @foreach($actions as $action)
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route($action['route']) }}"
                   class="d-block p-3 rounded-2 text-center text-decoration-none"
                   style="background:var(--bg);border:1px solid var(--border);transition:all .2s;"
                   onmouseover="this.style.borderColor='{{ $action['color'] }}';this.style.transform='translateY(-2px)';"
                   onmouseout="this.style.borderColor='var(--border)';this.style.transform='translateY(0)';">
                    <i class="fa-solid {{ $action['icon'] }} d-block mb-2" style="font-size:1.5rem;color:{{ $action['color'] }};"></i>
                    <div style="font-size:.78rem;font-weight:600;color:var(--text);">{{ $action['label'] }}</div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Fee Trend --}}
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span style="font-weight:700;color:var(--text);"><i class="fa-solid fa-chart-line me-2" style="color:var(--primary);"></i>Fee Collection Trend</span>
                <span class="badge" style="background:rgba(20,184,166,.1);color:#14B8A6;font-size:.7rem;">Last 6 months</span>
            </div>
            <div class="card-body">
                @if($monthlyFees->isEmpty())
                <div class="empty-state py-3"><i class="fa-solid fa-chart-line"></i><p class="mb-0 small">No data yet</p></div>
                @else
                <canvas id="staffFeeChart" height="110"></canvas>
                @endif
            </div>
        </div>
    </div>
    {{-- Recent Admissions --}}
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span style="font-weight:700;color:var(--text);"><i class="fa-solid fa-user-plus me-2" style="color:#059669;"></i>Recent Admissions</span>
                <a href="{{ route('admin.students.index') }}" style="font-size:.78rem;color:var(--primary);text-decoration:none;">View all →</a>
            </div>
            <div class="card-body p-0">
                @forelse($recentAdmissions as $student)
                <div class="d-flex align-items-center gap-3 px-3 py-2" style="border-bottom:1px solid var(--border);">
                    <img src="{{ $student->photo_url }}" class="rounded-circle flex-shrink-0" style="width:34px;height:34px;object-fit:cover;">
                    <div class="flex-1 overflow-hidden">
                        <div style="font-weight:500;font-size:.875rem;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $student->full_name }}</div>
                        <div style="font-size:.72rem;color:var(--muted);">{{ $student->branch?->name }} &bull; {{ $student->admission_date?->format('d M') }}</div>
                    </div>
                    <span class="badge" style="background:#DCFCE7;color:#166534;font-size:.65rem;">Active</span>
                </div>
                @empty
                <div class="text-center py-4" style="color:var(--muted);font-size:.85rem;">No admissions yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
@if(!$monthlyFees->isEmpty())
const ctx = document.getElementById('staffFeeChart');
if(ctx){
    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    const data = @json($monthlyFees);
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.map(d => months[d.month-1]),
            datasets: [{ label:'Fee Collection (₹)', data:data.map(d=>parseFloat(d.total)), borderColor:'#14B8A6', backgroundColor:'rgba(20,184,166,0.08)', borderWidth:2.5, pointBackgroundColor:'#14B8A6', pointRadius:4, fill:true, tension:0.4 }]
        },
        options: { responsive:true, plugins:{legend:{display:false},tooltip:{callbacks:{label:c=>'₹'+c.parsed.y.toLocaleString('en-IN')}}}, scales:{y:{beginAtZero:true,grid:{color:'rgba(0,0,0,0.04)'},ticks:{callback:v=>'₹'+(v>=1000?(v/1000).toFixed(0)+'K':v),font:{size:11}}},x:{grid:{display:false},ticks:{font:{size:11}}}} }
    });
}
@endif
</script>
@endpush
