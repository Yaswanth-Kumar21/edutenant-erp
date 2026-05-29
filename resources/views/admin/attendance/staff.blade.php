@extends('layouts.app')
@section('title', 'Staff Attendance')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-user-tie me-2" style="color:#059669;"></i>Staff Attendance</h1>
        <p class="mb-0" style="color:var(--muted);font-size:.875rem;">Mark daily staff attendance</p>
    </div>
    <a href="{{ route('admin.attendance.staff.report') }}" class="btn btn-outline-primary btn-sm">
        <i class="fa-solid fa-chart-bar me-1"></i> Monthly Report
    </a>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div style="font-size:1.8rem;font-weight:800;color:#059669;">{{ $todayStats['staff_present'] }}</div>
            <div style="font-size:.78rem;color:var(--muted);">Present Today</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div style="font-size:1.8rem;font-weight:800;color:#dc2626;">{{ $todayStats['staff_absent'] }}</div>
            <div style="font-size:.78rem;color:var(--muted);">Absent Today</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div style="font-size:1.8rem;font-weight:800;color:#4f46e5;">{{ $todayStats['staff_total'] }}</div>
            <div style="font-size:.78rem;color:var(--muted);">Total Staff</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            @php
                $pct = $todayStats['staff_total'] > 0
                    ? round(($todayStats['staff_present'] / $todayStats['staff_total']) * 100)
                    : 0;
            @endphp
            <div style="font-size:1.8rem;font-weight:800;color:#d97706;">{{ $pct }}%</div>
            <div style="font-size:.78rem;color:var(--muted);">Attendance Rate</div>
        </div>
    </div>
</div>

{{-- Date Selector --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.attendance.staff') }}" class="row g-2 align-items-end">
            <div class="col-12 col-md-3">
                <label class="form-label mb-1" style="font-size:.8rem;">Date</label>
                <input type="date" name="date" class="form-control form-control-sm"
                       value="{{ $date }}" max="{{ today()->toDateString() }}">
            </div>
            <div class="col-12 col-md-2">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-search me-1"></i> Load
                </button>
            </div>
        </form>
    </div>
</div>

@if($staffList->isNotEmpty())
<form method="POST" action="{{ route('admin.attendance.staff.mark') }}">
    @csrf
    <input type="hidden" name="attendance_date" value="{{ $date }}">

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span style="font-weight:600;">
                <i class="fa-solid fa-users me-2" style="color:#059669;"></i>
                {{ $staffList->count() }} Staff Members —
                <span class="badge" style="background:rgba(5,150,105,.1);color:#059669;">
                    {{ \Carbon\Carbon::parse($date)->format('d M Y, l') }}
                </span>
            </span>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-success" onclick="markAllStaff('present')">
                    <i class="fa-solid fa-check me-1"></i> All Present
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="markAllStaff('absent')">
                    <i class="fa-solid fa-xmark me-1"></i> All Absent
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Staff Member</th>
                        <th>Type</th>
                        <th class="text-center"><span class="badge bg-success">Present</span></th>
                        <th class="text-center"><span class="badge bg-danger">Absent</span></th>
                        <th class="text-center"><span class="badge bg-info">Half Day</span></th>
                        <th class="text-center"><span class="badge bg-warning text-dark">Leave</span></th>
                        <th class="text-center"><span class="badge bg-secondary">Holiday</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($staffList as $i => $member)
                    @php $current = $existingAttendance[$member->id] ?? 'present'; @endphp
                    <tr id="staff-row-{{ $member->id }}"
                        class="{{ $current === 'absent' ? 'table-danger' : ($current === 'present' ? 'table-success bg-opacity-25' : '') }}">
                        <td style="color:var(--muted);font-size:.82rem;">{{ $i + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $member->photo_url }}" class="rounded-circle"
                                     style="width:32px;height:32px;object-fit:cover;" alt="">
                                <div>
                                    <div style="font-weight:500;font-size:.875rem;">{{ $member->name }}</div>
                                    <div style="font-size:.72rem;color:var(--muted);">
                                        {{ $member->designation ?? $member->staff_type }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($member->isTeaching())
                                <span class="badge" style="background:rgba(79,70,229,.1);color:#4f46e5;">Teaching</span>
                            @else
                                <span class="badge" style="background:rgba(217,119,6,.1);color:#d97706;">Non-Teaching</span>
                            @endif
                        </td>
                        @foreach(['present','absent','half_day','leave','holiday'] as $status)
                        <td class="text-center">
                            <input class="form-check-input staff-radio"
                                   type="radio"
                                   name="attendance[{{ $member->id }}]"
                                   value="{{ $status }}"
                                   data-staff="{{ $member->id }}"
                                   data-status="{{ $status }}"
                                   {{ $current === $status ? 'checked' : '' }}
                                   style="width:1.2rem;height:1.2rem;cursor:pointer;">
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="card-footer d-flex justify-content-end"
             style="background:transparent;border-top:1px solid var(--border);">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-save me-2"></i> Save Staff Attendance
            </button>
        </div>
    </div>
</form>
@else
<div class="card">
    <div class="card-body">
        <div class="empty-state py-5">
            <i class="fa-solid fa-users" style="font-size:3rem;opacity:.2;"></i>
            <h5 class="mt-3" style="color:var(--muted);">No active staff members found</h5>
            <a href="{{ route('admin.staff.create') }}" class="btn btn-primary btn-sm mt-2">Add Staff</a>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
function markAllStaff(status) {
    document.querySelectorAll('.staff-radio[data-status="' + status + '"]').forEach(r => r.checked = true);
}
</script>
@endpush

