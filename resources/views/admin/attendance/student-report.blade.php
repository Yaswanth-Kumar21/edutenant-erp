@extends('layouts.app')
@section('title', 'Student Attendance Report')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-chart-bar me-2" style="color:#4f46e5;"></i>Student Attendance Report</h1>
        <p class="mb-0" style="color:var(--muted);font-size:.875rem;">Monthly class-wise attendance summary</p>
    </div>
    <a href="{{ route('admin.attendance.students') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Mark
    </a>
</div>

{{-- Filters --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.attendance.students.report') }}" class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label mb-1" style="font-size:.8rem;">Branch</label>
                <select name="branch_id" class="form-select form-select-sm">
                    <option value="">All Branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ $branchId == $branch->id ? 'selected' : '' }}>
                            {{ $branch->course->name ?? '' }} — {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
            </div>
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
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="fa-solid fa-filter me-1"></i> Generate
                </button>
            </div>
        </form>
    </div>
</div>

@if(!empty($report))
{{-- Summary Cards --}}
@php
    $avgPct = count($report) > 0 ? round(collect($report)->avg('percentage'), 1) : 0;
    $below75 = collect($report)->filter(fn($r) => $r['percentage'] < 75)->count();
@endphp
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div style="font-size:1.8rem;font-weight:800;color:#4f46e5;">{{ count($report) }}</div>
            <div style="font-size:.78rem;color:var(--muted);">Total Students</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div style="font-size:1.8rem;font-weight:800;color:#059669;">{{ $workingDays }}</div>
            <div style="font-size:.78rem;color:var(--muted);">Working Days</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div style="font-size:1.8rem;font-weight:800;color:#d97706;">{{ $avgPct }}%</div>
            <div style="font-size:.78rem;color:var(--muted);">Avg Attendance</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div style="font-size:1.8rem;font-weight:800;color:#dc2626;">{{ $below75 }}</div>
            <div style="font-size:.78rem;color:var(--muted);">Below 75%</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span style="font-weight:600;">
            Attendance Report —
            {{ \Carbon\Carbon::create()->month($month)->format('F') }} {{ $year }}
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
                    <th>Student</th>
                    <th>Adm. No</th>
                    <th class="text-center">Present</th>
                    <th class="text-center">Absent</th>
                    <th class="text-center">Late</th>
                    <th class="text-center">Total Days</th>
                    <th class="text-center">Percentage</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($report as $i => $row)
                <tr>
                    <td style="color:var(--muted);font-size:.82rem;">{{ $i + 1 }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $row['student']->photo_url }}" class="rounded-circle"
                                 style="width:28px;height:28px;object-fit:cover;" alt="">
                            <span style="font-size:.875rem;font-weight:500;">{{ $row['student']->full_name }}</span>
                        </div>
                    </td>
                    <td style="font-size:.82rem;color:var(--muted);">{{ $row['student']->admission_number }}</td>
                    <td class="text-center">
                        <span class="badge" style="background:#dcfce7;color:#166534;">{{ $row['present'] }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge" style="background:#fee2e2;color:#991b1b;">{{ $row['absent'] }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge" style="background:#fef3c7;color:#92400e;">{{ $row['late'] }}</span>
                    </td>
                    <td class="text-center" style="font-size:.85rem;">{{ $row['total'] }}</td>
                    <td class="text-center">
                        <div class="d-flex align-items-center gap-2 justify-content-center">
                            <div class="progress" style="width:60px;height:6px;background:#e5e7eb;">
                                <div class="progress-bar {{ $row['percentage'] >= 75 ? 'bg-success' : 'bg-danger' }}"
                                     style="width:{{ $row['percentage'] }}%"></div>
                            </div>
                            <span style="font-size:.82rem;font-weight:600;">{{ $row['percentage'] }}%</span>
                        </div>
                    </td>
                    <td class="text-center">
                        @if($row['percentage'] >= 75)
                            <span class="badge" style="background:#dcfce7;color:#166534;">Good</span>
                        @elseif($row['percentage'] >= 60)
                            <span class="badge" style="background:#fef3c7;color:#92400e;">Average</span>
                        @else
                            <span class="badge" style="background:#fee2e2;color:#991b1b;">Low</span>
                        @endif
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
            <h5 class="mt-3" style="color:var(--muted);">Select a branch and month to generate report</h5>
        </div>
    </div>
</div>
@endif

@endsection

