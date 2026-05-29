@extends('layouts.student-app')

@section('title', 'My Attendance')

@section('breadcrumb')
    <li class="breadcrumb-item active">My Attendance</li>
@endsection

@push('styles')
<style>
.calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; }
.cal-header { text-align: center; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: var(--muted); padding: 6px 0; }
.cal-day {
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-size: 0.78rem;
    font-weight: 500;
    cursor: default;
    transition: all 0.15s;
}
.cal-day.present { background: #dcfce7; color: #166534; font-weight: 700; }
.cal-day.absent  { background: #fee2e2; color: #991b1b; font-weight: 700; }
.cal-day.late    { background: #fef3c7; color: #92400e; font-weight: 700; }
.cal-day.today   { border: 2px solid #4f46e5; }
.cal-day.empty   { opacity: 0; pointer-events: none; }
</style>
@endpush

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-calendar-check me-2" style="color:#059669;"></i>My Attendance</h1>
        <p style="color:var(--muted);font-size:0.875rem;margin:0;">Your attendance calendar and history</p>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="stat-card blue">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-value">{{ $percentage }}%</div>
                    <div class="stat-label mt-1">Overall %</div>
                </div>
                <i class="fa-solid fa-chart-pie stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card green">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-value">{{ $presentDays }}</div>
                    <div class="stat-label mt-1">Present Days</div>
                </div>
                <i class="fa-solid fa-circle-check stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#dc2626,#ef4444);">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-value">{{ $absentDays }}</div>
                    <div class="stat-label mt-1">Absent Days</div>
                </div>
                <i class="fa-solid fa-circle-xmark stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card orange">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-value">{{ $totalDays }}</div>
                    <div class="stat-label mt-1">Total Days</div>
                </div>
                <i class="fa-solid fa-calendar stat-icon"></i>
            </div>
        </div>
    </div>
</div>

@if($percentage < 75)
<div class="alert alert-warning d-flex align-items-center gap-2 mb-4" style="border-radius:0.75rem;">
    <i class="fa-solid fa-triangle-exclamation fa-lg"></i>
    <div>
        <strong>Low Attendance Warning!</strong> Your attendance is {{ $percentage }}%. Minimum 75% is required to appear in examinations.
    </div>
</div>
@endif

<div class="row g-3">
    {{-- Calendar --}}
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="fa-solid fa-calendar me-2" style="color:#059669;"></i>Attendance Calendar</span>
                <form method="GET" action="{{ route('student.attendance.index') }}" class="d-flex gap-2 align-items-center">
                    <input type="month" name="month" value="{{ $currentMonth }}"
                           class="form-control form-control-sm" style="width:auto;"
                           onchange="this.form.submit()">
                </form>
            </div>
            <div class="card-body">
                {{-- Legend --}}
                <div class="d-flex gap-3 mb-3 flex-wrap" style="font-size:0.78rem;">
                    <span><span style="display:inline-block;width:12px;height:12px;background:#dcfce7;border-radius:50%;margin-right:4px;"></span>Present</span>
                    <span><span style="display:inline-block;width:12px;height:12px;background:#fee2e2;border-radius:50%;margin-right:4px;"></span>Absent</span>
                    <span><span style="display:inline-block;width:12px;height:12px;background:#fef3c7;border-radius:50%;margin-right:4px;"></span>Late</span>
                </div>

                @php
                    $firstDay = \Carbon\Carbon::createFromDate($year, $month, 1);
                    $daysInMonth = $firstDay->daysInMonth;
                    $startDow = $firstDay->dayOfWeek; // 0=Sun
                    $today = now()->format('Y-m-d');
                @endphp

                <div class="calendar-grid">
                    @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $d)
                        <div class="cal-header">{{ $d }}</div>
                    @endforeach

                    @for($i = 0; $i < $startDow; $i++)
                        <div class="cal-day empty">.</div>
                    @endfor

                    @for($d = 1; $d <= $daysInMonth; $d++)
                        @php
                            $dateKey = sprintf('%04d-%02d-%02d', $year, $month, $d);
                            $rec = $calendarRecords->get($dateKey);
                            $cls = $rec ? $rec->status : '';
                            $isToday = $dateKey === $today ? 'today' : '';
                        @endphp
                        <div class="cal-day {{ $cls }} {{ $isToday }}" title="{{ $rec ? ucfirst($rec->status) : '' }}">
                            {{ $d }}
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    {{-- Monthly Stats + Recent --}}
    <div class="col-lg-5">
        {{-- Monthly Breakdown --}}
        <div class="card mb-3">
            <div class="card-header">
                <i class="fa-solid fa-chart-bar me-2" style="color:#4f46e5;"></i>Monthly Breakdown
            </div>
            <div class="card-body p-0">
                @foreach($monthlyStats as $ms)
                <div class="d-flex align-items-center gap-3 px-3 py-2" style="border-bottom:1px solid var(--border);">
                    <div style="width:60px;font-size:0.78rem;font-weight:600;color:var(--muted);">{{ $ms['month'] }}</div>
                    <div class="flex-1">
                        @if($ms['total'] > 0)
                        <div style="height:6px;background:var(--border);border-radius:3px;overflow:hidden;">
                            <div style="height:100%;width:{{ round(($ms['present']/$ms['total'])*100) }}%;background:#059669;border-radius:3px;"></div>
                        </div>
                        @else
                        <div style="height:6px;background:var(--border);border-radius:3px;"></div>
                        @endif
                    </div>
                    <div style="font-size:0.75rem;font-weight:700;color:#059669;width:35px;text-align:right;">
                        {{ $ms['total'] > 0 ? round(($ms['present']/$ms['total'])*100) . '%' : '—' }}
                    </div>
                    <div style="font-size:0.7rem;color:var(--muted);width:60px;text-align:right;">
                        {{ $ms['present'] }}/{{ $ms['total'] }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Recent Records --}}
        <div class="card">
            <div class="card-header">
                <i class="fa-solid fa-clock-rotate-left me-2" style="color:#d97706;"></i>Recent Records
            </div>
            <div class="card-body p-0">
                @forelse($recentRecords as $rec)
                <div class="d-flex align-items-center justify-content-between px-3 py-2" style="border-bottom:1px solid var(--border);font-size:0.82rem;">
                    <div>
                        <div style="font-weight:500;">{{ $rec->attendance_date?->format('d M Y') }}</div>
                        @if($rec->subject)
                        <div style="font-size:0.72rem;color:var(--muted);">{{ $rec->subject }}</div>
                        @endif
                    </div>
                    @php
                        $sc = ['present'=>['#dcfce7','#166534'],'absent'=>['#fee2e2','#991b1b'],'late'=>['#fef3c7','#92400e']][$rec->status] ?? ['#f3f4f6','#374151'];
                    @endphp
                    <span class="badge" style="background:{{ $sc[0] }};color:{{ $sc[1] }};font-size:0.72rem;">
                        {{ ucfirst($rec->status) }}
                    </span>
                </div>
                @empty
                <div class="text-center py-4" style="color:var(--muted);font-size:0.85rem;">No records yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection

