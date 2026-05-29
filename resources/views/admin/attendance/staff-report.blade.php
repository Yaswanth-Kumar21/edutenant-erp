@extends('layouts.app')
@section('title', 'Staff Attendance Report')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-chart-bar me-2" style="color:#059669;"></i>Staff Attendance Report</h1>
        <p class="mb-0" style="color:var(--muted);font-size:.875rem;">Monthly staff attendance summary</p>
    </div>
    <a href="{{ route('admin.attendance.staff') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Mark
    </a>
</div>

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
                    <i class="fa-solid fa-filter me-1"></i> Generate
                </button>
            </div>
        </form>
    </div>
</div>

@if($report->isNotEmpty())
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span style="font-weight:600;">
            Staff Report — {{ \Carbon\Carbon::create()->month($month)->format('F') }} {{ $year }}
            <span class="badge ms-2" style="background:rgba(5,150,105,.1);color:#059669;">
                {{ $workingDays }} Working Days
            </span>
        </span>
        <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
            <i class="fa-solid fa-print me-1"></i> Print
        </button>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Staff Member</th>
                    <th>Type</th>
                    <th class="text-center">Present</th>
                    <th class="text-center">Absent</th>
                    <th class="text-center">Half Day</th>
                    <th class="text-center">Leave</th>
                    <th class="text-center">Holiday</th>
                    <th class="text-center">Attendance %</th>
                    <th class="text-right">Est. Salary</th>
                </tr>
            </thead>
            <tbody>
                @foreach($report as $i => $row)
                @php
                    $staff = $row['staff'];
                    $pct   = $row['percentage'];
                    $estSalary = $staff->calculateMonthlySalary($row['present'], $month, $year);
                @endphp
                <tr>
                    <td style="color:var(--muted);font-size:.82rem;">{{ $i + 1 }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $staff->photo_url }}" class="rounded-circle"
                                 style="width:30px;height:30px;object-fit:cover;" alt="">
                            <div>
                                <div style="font-weight:500;font-size:.875rem;">{{ $staff->name }}</div>
                                <div style="font-size:.72rem;color:var(--muted);">{{ $staff->designation ?? '—' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($staff->isTeaching())
                            <span class="badge" style="background:rgba(79,70,229,.1);color:#4f46e5;">Teaching</span>
                        @else
                            <span class="badge" style="background:rgba(217,119,6,.1);color:#d97706;">Non-Teaching</span>
                        @endif
                    </td>
                    <td class="text-center"><span class="badge" style="background:#dcfce7;color:#166534;">{{ $row['present'] }}</span></td>
                    <td class="text-center"><span class="badge" style="background:#fee2e2;color:#991b1b;">{{ $row['absent'] }}</span></td>
                    <td class="text-center"><span class="badge" style="background:#e0f2fe;color:#0369a1;">{{ $row['half_day'] }}</span></td>
                    <td class="text-center"><span class="badge" style="background:#fef3c7;color:#92400e;">{{ $row['leave'] }}</span></td>
                    <td class="text-center"><span class="badge bg-secondary">{{ $row['holiday'] }}</span></td>
                    <td class="text-center">
                        <div class="d-flex align-items-center gap-1 justify-content-center">
                            <div class="progress" style="width:50px;height:5px;background:#e5e7eb;">
                                <div class="progress-bar {{ $pct >= 75 ? 'bg-success' : 'bg-danger' }}"
                                     style="width:{{ $pct }}%"></div>
                            </div>
                            <span style="font-size:.8rem;font-weight:600;">{{ $pct }}%</span>
                        </div>
                    </td>
                    <td class="text-end" style="font-weight:600;color:#059669;font-size:.875rem;">
                        ₹{{ number_format($estSalary) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@else
<div class="card">
    <div class="card-body">
        <div class="empty-state py-5">
            <i class="fa-solid fa-chart-bar" style="font-size:3rem;opacity:.2;"></i>
            <h5 class="mt-3" style="color:var(--muted);">No attendance data for this period</h5>
        </div>
    </div>
</div>
@endif

@endsection

