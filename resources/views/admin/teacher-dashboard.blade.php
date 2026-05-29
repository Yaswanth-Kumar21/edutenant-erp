@extends('layouts.app')

@section('title', 'Faculty Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')

{{-- Welcome Banner --}}
<div class="mb-4 rounded-3 overflow-hidden position-relative"
     style="background:linear-gradient(135deg,#3D2B1F 0%,#6B4C35 50%,#8B6B4A 100%);padding:2rem;">
    <div class="position-absolute" style="top:-60px;right:-60px;width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,.06);pointer-events:none;"></div>
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 position-relative">
        <div>
            <h2 class="text-white fw-bold mb-1" style="font-size:1.4rem;">
                <i class="fa-solid fa-chalkboard-user me-2" style="opacity:.8;"></i>
                Welcome, {{ auth()->user()->name }}
            </h2>
            <p class="mb-0" style="color:rgba(255,255,255,.7);font-size:.875rem;">
                {{ $tenant->name }} &mdash; {{ now()->format('l, d F Y') }}
            </p>
        </div>
        <a href="{{ route('admin.attendance.students') }}" class="btn btn-sm btn-light fw-600" style="font-weight:600;border-radius:8px;">
            <i class="fa-solid fa-calendar-check me-1"></i> Mark Today's Attendance
        </a>
    </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="stat-card amber">
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
                    <div class="stat-label mb-2">Present Today</div>
                    <div class="stat-value">{{ $stats['present_today'] }}</div>
                    <div class="mt-1" style="font-size:.75rem;opacity:.85;">{{ now()->format('d M') }}</div>
                </div>
                <i class="fa-solid fa-circle-check stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card red">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label mb-2">Absent Today</div>
                    <div class="stat-value">{{ $stats['absent_today'] }}</div>
                    <div class="mt-1" style="font-size:.75rem;opacity:.85;">Needs follow-up</div>
                </div>
                <i class="fa-solid fa-circle-xmark stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card blue">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label mb-2">Low Attendance</div>
                    <div class="stat-value">{{ $stats['low_attendance'] }}</div>
                    <div class="mt-1" style="font-size:.75rem;opacity:.85;">Below 75%</div>
                </div>
                <i class="fa-solid fa-triangle-exclamation stat-icon"></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Attendance Trend --}}
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span style="font-weight:700;color:var(--text);"><i class="fa-solid fa-chart-bar me-2" style="color:var(--primary);"></i>Attendance Trend</span>
                <span class="badge" style="background:rgba(139,107,74,.15);color:#8B6B4A;font-size:.7rem;">Last 6 months</span>
            </div>
            <div class="card-body">
                @if($monthlyAttendance->isEmpty())
                <div class="empty-state py-3"><i class="fa-solid fa-chart-bar"></i><p class="mb-0 small">No data yet</p></div>
                @else
                <canvas id="teacherAttChart" height="110"></canvas>
                @endif
            </div>
        </div>
    </div>

    {{-- Students by Branch --}}
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header" style="font-weight:700;color:var(--text);">
                <i class="fa-solid fa-chart-pie me-2" style="color:var(--primary);"></i>Students by Branch
            </div>
            <div class="card-body d-flex flex-column align-items-center">
                @if($studentsByBranch->isEmpty())
                <div class="empty-state py-3"><i class="fa-solid fa-chart-pie"></i><p class="mb-0 small">No data</p></div>
                @else
                <canvas id="teacherBranchChart" style="max-height:180px;"></canvas>
                <div id="teacher-branch-legend" class="mt-3 w-100" style="font-size:.78rem;"></div>
                @endif
            </div>
        </div>
    </div>

    {{-- Recent Attendance --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span style="font-weight:700;color:var(--text);"><i class="fa-solid fa-clock-rotate-left me-2" style="color:var(--primary);"></i>Recent Attendance Records</span>
                <a href="{{ route('admin.attendance.students.report') }}" style="font-size:.78rem;color:var(--primary);text-decoration:none;">Full Report →</a>
            </div>
            <div class="card-body p-0">
                @if($recentAttendance->isEmpty())
                <div class="empty-state py-4"><i class="fa-solid fa-calendar"></i><p class="mb-0 small">No records yet</p></div>
                @else
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead><tr><th>Student</th><th>Branch</th><th>Date</th><th>Status</th></tr></thead>
                        <tbody>
                            @foreach($recentAttendance as $rec)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $rec->student?->photo_url }}" class="rounded-circle" style="width:28px;height:28px;object-fit:cover;">
                                        <span style="font-size:.875rem;font-weight:500;color:var(--text);">{{ $rec->student?->full_name }}</span>
                                    </div>
                                </td>
                                <td style="font-size:.82rem;color:var(--muted);">{{ $rec->branch?->name ?? '—' }}</td>
                                <td style="font-size:.78rem;color:var(--muted);">{{ $rec->attendance_date?->format('d M Y') }}</td>
                                <td>
                                    @php $sc = ['present'=>['#DCFCE7','#166534'],'absent'=>['#FEE2E2','#991B1B'],'late'=>['#FEF3C7','#92400E']][$rec->status] ?? ['#F3F4F6','#374151']; @endphp
                                    <span class="badge" style="background:{{ $sc[0] }};color:{{ $sc[1] }};font-size:.7rem;">{{ ucfirst($rec->status) }}</span>
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
</div>

@endsection

@push('scripts')
<script>
(function(){
    @if(!$monthlyAttendance->isEmpty())
    const ctx = document.getElementById('teacherAttChart');
    if(ctx){
        const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        const data = @json($monthlyAttendance);
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(d => months[d.month-1]+' '+d.year),
                datasets: [
                    { label:'Present', data:data.map(d=>d.present_count), backgroundColor:'rgba(5,150,105,0.7)', borderRadius:4 },
                    { label:'Absent',  data:data.map(d=>d.total_count-d.present_count), backgroundColor:'rgba(220,38,38,0.5)', borderRadius:4 }
                ]
            },
            options: { responsive:true, plugins:{legend:{position:'top',labels:{font:{size:11}}}}, scales:{x:{stacked:true,grid:{display:false},ticks:{font:{size:11}}},y:{stacked:true,beginAtZero:true,grid:{color:'rgba(0,0,0,0.04)'},ticks:{font:{size:11}}}} }
        });
    }
    @endif

    @if(!$studentsByBranch->isEmpty())
    const bCtx = document.getElementById('teacherBranchChart');
    if(bCtx){
        const bd = @json($studentsByBranch);
        const colors = ['#8B6B4A','#059669','#2563EB','#7C3AED','#DC2626','#0891B2'];
        new Chart(bCtx, {
            type: 'doughnut',
            data: { labels:bd.map(d=>d.branch?d.branch.name:'Unknown'), datasets:[{data:bd.map(d=>d.count),backgroundColor:colors.slice(0,bd.length),borderWidth:2,borderColor:'#fff',hoverOffset:4}] },
            options: { responsive:true, cutout:'65%', plugins:{legend:{display:false}} }
        });
        const legend = document.getElementById('teacher-branch-legend');
        if(legend) legend.innerHTML = bd.map((d,i)=>`<div class="d-flex align-items-center gap-2 mb-1"><div style="width:10px;height:10px;border-radius:50%;background:${colors[i]};flex-shrink:0;"></div><span style="color:var(--muted);flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${d.branch?d.branch.name:'Unknown'}</span><strong style="color:var(--text);">${d.count}</strong></div>`).join('');
    }
    @endif
})();
</script>
@endpush
