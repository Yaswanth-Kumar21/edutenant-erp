@extends('layouts.app')
@section('title', 'Attendance Analytics')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-chart-line me-2" style="color:#4f46e5;"></i>Attendance Analytics</h1>
        <p class="mb-0" style="color:var(--muted);font-size:.875rem;">Visual attendance insights and trends</p>
    </div>
</div>

{{-- Month/Year Filter --}}
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
                    <i class="fa-solid fa-filter me-1"></i> Apply
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Overall Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card blue">
            <div class="stat-label mb-1">Total Records</div>
            <div class="stat-value">{{ number_format($overallStats['total_records']) }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card green">
            <div class="stat-label mb-1">Present</div>
            <div class="stat-value">{{ number_format($overallStats['present']) }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#dc2626,#ef4444);">
            <div class="stat-label mb-1">Absent</div>
            <div class="stat-value">{{ number_format($overallStats['absent']) }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card orange">
            <div class="stat-label mb-1">Avg Attendance</div>
            <div class="stat-value">{{ $overallStats['percentage'] }}%</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- Daily Trend Chart --}}
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="mb-0 fw-600" style="font-weight:600;">Daily Attendance Trend</h6>
                <small style="color:var(--muted);">
                    {{ \Carbon\Carbon::create()->month($month)->format('F') }} {{ $year }}
                </small>
            </div>
            <div class="card-body">
                @if($dailyTrend->isEmpty())
                    <div class="empty-state py-4">
                        <i class="fa-solid fa-chart-line" style="font-size:2.5rem;opacity:.2;"></i>
                        <p class="mt-2 mb-0" style="color:var(--muted);">No data for this period</p>
                    </div>
                @else
                    <canvas id="dailyChart" height="100"></canvas>
                @endif
            </div>
        </div>
    </div>

    {{-- Branch-wise Chart --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="mb-0 fw-600" style="font-weight:600;">Branch-wise Attendance</h6>
            </div>
            <div class="card-body">
                @if($branchStats->isEmpty())
                    <div class="empty-state py-4">
                        <i class="fa-solid fa-chart-pie" style="font-size:2.5rem;opacity:.2;"></i>
                        <p class="mt-2 mb-0" style="color:var(--muted);">No data available</p>
                    </div>
                @else
                    <canvas id="branchChart" style="max-height:220px;"></canvas>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Branch Stats Table --}}
@if($branchStats->isNotEmpty())
<div class="card">
    <div class="card-header">
        <h6 class="mb-0 fw-600" style="font-weight:600;">Branch-wise Summary</h6>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Branch</th>
                    <th class="text-center">Total</th>
                    <th class="text-center">Present</th>
                    <th class="text-center">Absent</th>
                    <th class="text-center">Attendance %</th>
                </tr>
            </thead>
            <tbody>
                @foreach($branchStats as $row)
                @php $pct = $row->total > 0 ? round(($row->present / $row->total) * 100, 1) : 0; @endphp
                <tr>
                    <td style="font-weight:500;">{{ $row->branch }}</td>
                    <td class="text-center">{{ $row->total }}</td>
                    <td class="text-center"><span class="badge" style="background:#dcfce7;color:#166534;">{{ $row->present }}</span></td>
                    <td class="text-center"><span class="badge" style="background:#fee2e2;color:#991b1b;">{{ $row->absent }}</span></td>
                    <td class="text-center">
                        <div class="d-flex align-items-center gap-2 justify-content-center">
                            <div class="progress" style="width:70px;height:6px;background:#e5e7eb;">
                                <div class="progress-bar {{ $pct >= 75 ? 'bg-success' : 'bg-danger' }}"
                                     style="width:{{ $pct }}%"></div>
                            </div>
                            <span style="font-size:.82rem;font-weight:600;">{{ $pct }}%</span>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
@if(!$dailyTrend->isEmpty())
const dailyData = @json($dailyTrend);
new Chart(document.getElementById('dailyChart'), {
    type: 'bar',
    data: {
        labels: dailyData.map(d => d.date.slice(8)),
        datasets: [
            {
                label: 'Present',
                data: dailyData.map(d => d.present),
                backgroundColor: 'rgba(5,150,105,0.7)',
                borderRadius: 4,
            },
            {
                label: 'Absent',
                data: dailyData.map(d => d.absent),
                backgroundColor: 'rgba(220,38,38,0.6)',
                borderRadius: 4,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: {
            x: { stacked: false, grid: { display: false } },
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } }
        }
    }
});
@endif

@if(!$branchStats->isEmpty())
const branchData = @json($branchStats);
new Chart(document.getElementById('branchChart'), {
    type: 'doughnut',
    data: {
        labels: branchData.map(d => d.branch),
        datasets: [{
            data: branchData.map(d => d.present),
            backgroundColor: ['#4f46e5','#059669','#d97706','#dc2626','#7c3aed','#0891b2'],
            borderWidth: 2,
            borderColor: '#fff',
        }]
    },
    options: {
        responsive: true,
        cutout: '60%',
        plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } }
    }
});
@endif
</script>
@endpush

