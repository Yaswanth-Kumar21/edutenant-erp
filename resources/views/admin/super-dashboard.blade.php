@extends('layouts.super-admin-app')
@section('title', 'Dashboard')
@section('breadcrumb')
    <li class="breadcrumb-item active" style="color:var(--muted);">Dashboard</li>
@endsection
@section('content')

{{-- Page Header --}}
<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">Platform Overview</h1>
        <p class="page-sub">{{ now()->format('l, d F Y') }} &mdash; EduTenant Admin Center</p>
    </div>
    <a href="{{ route('super.tenants.create') }}" class="btn-primary">
        <i class="fa-solid fa-plus"></i> Add Institution
    </a>
</div>

{{-- KPI Cards --}}
<div class="row g-3 mb-4">
    @php $kpis = [
        ['label'=>'Total Institutions','value'=>$stats['total_tenants'],'icon'=>'fa-building','color'=>'blue','change'=>null],
        ['label'=>'Active Institutions','value'=>$stats['active_tenants'],'icon'=>'fa-circle-check','color'=>'green','change'=>null],
        ['label'=>'Total Students','value'=>number_format($stats['total_students']),'icon'=>'fa-user-graduate','color'=>'purple','change'=>null],
        ['label'=>'Total Staff','value'=>number_format($stats['total_staff']),'icon'=>'fa-users','color'=>'cyan','change'=>null],
        ['label'=>'Platform Revenue','value'=>'₹'.number_format($stats['total_fees']/1000,1).'K','icon'=>'fa-indian-rupee-sign','color'=>'orange','change'=>null],
        ['label'=>'Inactive','value'=>$stats['inactive_tenants'],'icon'=>'fa-circle-xmark','color'=>'red','change'=>null],
    ]; @endphp
    @foreach($kpis as $kpi)
    <div class="col-6 col-md-4 col-xl-2">
        <div class="kpi-card {{ $kpi['color'] }}">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="kpi-icon {{ $kpi['color'] }}"><i class="fa-solid {{ $kpi['icon'] }}"></i></div>
            </div>
            <div class="kpi-value">{{ $kpi['value'] }}</div>
            <div class="kpi-label">{{ $kpi['label'] }}</div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-3 mb-4">
    {{-- Revenue Chart --}}
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header">
                <div>
                    <div style="font-weight:700;font-size:14px;color:var(--text);">Revenue Trend</div>
                    <div style="font-size:12px;color:var(--muted);margin-top:2px;">Platform-wide fee collection — last 6 months</div>
                </div>
                <span class="badge badge-blue"><i class="fa-solid fa-circle" style="font-size:6px;"></i> Live</span>
            </div>
            <div class="card-body">
                @if($platformFees->isEmpty())
                <div class="empty-state py-3">
                    <div class="empty-state-icon"><i class="fa-solid fa-chart-bar"></i></div>
                    <p>No revenue data yet</p>
                </div>
                @else
                <canvas id="revenueChart" height="80"></canvas>
                @endif
            </div>
        </div>
    </div>
    {{-- Status + Quick Stats --}}
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header"><span style="font-weight:600;font-size:13px;">Institution Status</span></div>
            <div class="card-body">
                <canvas id="statusChart" style="max-height:140px;" class="mb-3"></canvas>
                <div class="d-flex justify-content-around" style="font-size:12px;">
                    <div class="text-center">
                        <div style="font-size:20px;font-weight:800;color:var(--green);">{{ $stats['active_tenants'] }}</div>
                        <div style="color:var(--muted);">Active</div>
                    </div>
                    <div style="width:1px;background:var(--border);"></div>
                    <div class="text-center">
                        <div style="font-size:20px;font-weight:800;color:var(--red);">{{ $stats['inactive_tenants'] }}</div>
                        <div style="color:var(--muted);">Inactive</div>
                    </div>
                    <div style="width:1px;background:var(--border);"></div>
                    <div class="text-center">
                        <div style="font-size:20px;font-weight:800;color:var(--blue);">{{ $stats['total_tenants'] }}</div>
                        <div style="color:var(--muted);">Total</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><span style="font-weight:600;font-size:13px;">Platform Health</span></div>
            <div class="card-body p-0">
                @php $health = [
                    ['label'=>'System Status','value'=>'Operational','color'=>'var(--green)'],
                    ['label'=>'Queue','value'=>strtoupper(config('queue.default')),'color'=>'var(--blue)'],
                    ['label'=>'Mail','value'=>strtoupper(config('mail.default')),'color'=>'var(--orange)'],
                    ['label'=>'Storage','value'=>'Linked','color'=>'var(--green)'],
                ]; @endphp
                @foreach($health as $h)
                <div class="d-flex align-items-center justify-content-between px-4 py-2" style="border-bottom:1px solid var(--border);font-size:12px;">
                    <span style="color:var(--muted);">{{ $h['label'] }}</span>
                    <span style="font-weight:600;color:{{ $h['color'] }};">{{ $h['value'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Institutions Table --}}
<div class="card">
    <div class="card-header">
        <div style="font-weight:700;font-size:14px;color:var(--text);">All Institutions</div>
        <div class="d-flex gap-2">
            <a href="{{ route('super.tenants.index') }}" class="btn-ghost" style="font-size:12px;">View All <i class="fa-solid fa-arrow-right ms-1"></i></a>
            <a href="{{ route('super.tenants.create') }}" class="btn-primary" style="font-size:12px;padding:6px 12px;">
                <i class="fa-solid fa-plus"></i> Add
            </a>
        </div>
    </div>
    @if($tenants->isEmpty())
    <div class="card-body">
        <div class="empty-state">
            <div class="empty-state-icon"><i class="fa-solid fa-building"></i></div>
            <h5>No institutions yet</h5>
            <p>Get started by adding your first institution.</p>
            <a href="{{ route('super.tenants.create') }}" class="btn-primary">Add Institution</a>
        </div>
    </div>
    @else
    <div class="table-responsive">
        <table class="sa-table">
            <thead>
                <tr>
                    <th>Institution</th>
                    <th>Location</th>
                    <th style="text-align:center;">Students</th>
                    <th style="text-align:center;">Staff</th>
                    <th style="text-align:center;">Setup</th>
                    <th style="text-align:right;">Revenue</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tenants as $tenant)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div style="width:36px;height:36px;border-radius:8px;background:var(--blue-xl);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="fa-solid fa-graduation-cap" style="color:var(--blue);font-size:14px;"></i>
                            </div>
                            <div>
                                <div style="font-weight:600;font-size:13px;color:var(--text);">{{ $tenant->name }}</div>
                                <div style="font-size:11px;color:var(--muted);font-family:monospace;">{{ $tenant->slug }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="color:var(--muted);font-size:12px;">{{ $tenant->city ?? '—' }}{{ $tenant->state ? ', '.$tenant->state : '' }}</td>
                    <td style="text-align:center;"><span class="badge badge-blue">{{ $tenant->students_count ?? 0 }}</span></td>
                    <td style="text-align:center;"><span class="badge" style="background:var(--green-l);color:#065F46;">{{ $tenant->staff_count ?? 0 }}</span></td>
                    <td style="text-align:center;">
                        @php $ob = $onboardingBadges[$tenant->id] ?? ['percentage'=>0,'color'=>'red','label'=>'0/5']; @endphp
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <div style="width:36px;height:5px;background:var(--border);border-radius:3px;overflow:hidden;">
                                <div style="height:100%;width:{{ $ob['percentage'] }}%;background:{{ $ob['color']==='green' ? 'var(--green)' : ($ob['color']==='orange' ? 'var(--orange)' : 'var(--red)') }};border-radius:3px;"></div>
                            </div>
                            <span style="font-size:11px;font-weight:700;color:{{ $ob['color']==='green' ? 'var(--green)' : ($ob['color']==='orange' ? 'var(--orange)' : 'var(--red)') }};">{{ $ob['label'] }}</span>
                        </div>
                    </td>
                    <td style="text-align:right;font-weight:700;font-size:13px;color:var(--green);">₹{{ number_format(($tenantFees[$tenant->id] ?? 0)/1000, 1) }}K</td>
                    <td style="text-align:center;">
                        @if($tenant->status === 'active')
                            <span class="badge badge-active"><i class="fa-solid fa-circle" style="font-size:6px;"></i> Active</span>
                        @else
                            <span class="badge badge-inactive">{{ ucfirst($tenant->status) }}</span>
                        @endif
                    </td>
                    <td style="text-align:right;">
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="{{ route('super.tenants.show', $tenant) }}" class="btn-icon" title="View"><i class="fa-solid fa-eye" style="font-size:12px;"></i></a>
                            <a href="{{ route('super.tenants.edit', $tenant) }}" class="btn-icon" title="Edit"><i class="fa-solid fa-pen" style="font-size:12px;"></i></a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($tenants->hasPages())
    <div class="card-body pt-3 pb-2">{{ $tenants->links('pagination::bootstrap-5') }}</div>
    @endif
    @endif
</div>

@endsection
@push('scripts')
<script>
(function(){
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
    const textColor = isDark ? '#64748B' : '#94A3B8';
    Chart.defaults.color = textColor;
    Chart.defaults.borderColor = gridColor;
    @if(!$platformFees->isEmpty())
    const rc = document.getElementById('revenueChart');
    if(rc){
        const months=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        const data=@json($platformFees);
        new Chart(rc,{type:'bar',data:{labels:data.map(d=>months[d.month-1]+' '+d.year),datasets:[{label:'Revenue (₹)',data:data.map(d=>parseFloat(d.total)),backgroundColor:'rgba(37,99,235,0.15)',borderColor:'#2563EB',borderWidth:2,borderRadius:6,hoverBackgroundColor:'rgba(37,99,235,0.3)'}]},options:{responsive:true,plugins:{legend:{display:false},tooltip:{callbacks:{label:c=>'₹'+c.parsed.y.toLocaleString('en-IN')}}},scales:{y:{beginAtZero:true,grid:{color:gridColor},ticks:{callback:v=>'₹'+(v>=1000?(v/1000).toFixed(0)+'K':v),font:{size:11}}},x:{grid:{display:false},ticks:{font:{size:11}}}}}});
    }
    @endif
    const sc=document.getElementById('statusChart');
    if(sc){new Chart(sc,{type:'doughnut',data:{labels:['Active','Inactive'],datasets:[{data:[{{$stats['active_tenants']}},{{$stats['inactive_tenants']}}],backgroundColor:['rgba(5,150,105,0.8)','rgba(220,38,38,0.6)'],borderColor:['#059669','#DC2626'],borderWidth:2,hoverOffset:3}]},options:{responsive:true,cutout:'70%',plugins:{legend:{display:false}}}});}
})();
</script>
@endpush
