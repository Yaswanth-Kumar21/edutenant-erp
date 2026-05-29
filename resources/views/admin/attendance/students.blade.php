@extends('layouts.app')
@section('title', 'Student Attendance')

@section('content')

{{-- Page Header --}}
<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-calendar-check me-2" style="color:#4f46e5;"></i>Student Attendance</h1>
        <p class="mb-0" style="color:var(--muted);font-size:.875rem;">Mark daily class-wise attendance</p>
    </div>
    <div class="d-flex gap-2">
        <div class="dropdown">
            <button class="btn btn-outline-success btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fa-solid fa-file-excel me-1"></i> Export
            </button>
            <ul class="dropdown-menu shadow border-0" style="border-radius:.75rem;background:var(--surface);">
                <li>
                    <a class="dropdown-item" style="font-size:.85rem;"
                       href="{{ route('admin.exports.attendance', ['format'=>'xlsx','month'=>now()->format('Y-m')]) }}">
                        <i class="fa-solid fa-file-excel me-2" style="color:#059669;"></i> Excel (.xlsx)
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" style="font-size:.85rem;"
                       href="{{ route('admin.exports.attendance', ['format'=>'csv','month'=>now()->format('Y-m')]) }}">
                        <i class="fa-solid fa-file-csv me-2" style="color:#0891b2;"></i> CSV
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item" style="font-size:.85rem;"
                       href="{{ route('admin.pdf.attendance-report', ['month'=>now()->format('Y-m')]) }}" target="_blank">
                        <i class="fa-solid fa-file-pdf me-2" style="color:#dc2626;"></i> PDF Report
                    </a>
                </li>
            </ul>
        </div>
        <a href="{{ route('admin.attendance.students.report') }}" class="btn btn-outline-primary btn-sm">
            <i class="fa-solid fa-chart-bar me-1"></i> Monthly Report
        </a>
        <a href="{{ route('admin.attendance.students.analytics') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-chart-line me-1"></i> Analytics
        </a>
    </div>
</div>

{{-- Today's Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div style="font-size:1.8rem;font-weight:800;color:#4f46e5;">{{ $todayStats['students_present'] }}</div>
            <div style="font-size:.78rem;color:var(--muted);">Present Today</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div style="font-size:1.8rem;font-weight:800;color:#dc2626;">{{ $todayStats['students_absent'] }}</div>
            <div style="font-size:.78rem;color:var(--muted);">Absent Today</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div style="font-size:1.8rem;font-weight:800;color:#059669;">{{ $todayStats['staff_present'] }}</div>
            <div style="font-size:.78rem;color:var(--muted);">Staff Present</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            @php
                $pct = $todayStats['students_total'] > 0
                    ? round(($todayStats['students_present'] / $todayStats['students_total']) * 100)
                    : 0;
            @endphp
            <div style="font-size:1.8rem;font-weight:800;color:#d97706;">{{ $pct }}%</div>
            <div style="font-size:.78rem;color:var(--muted);">Attendance Rate</div>
        </div>
    </div>
</div>

{{-- Filter Form --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.attendance.students') }}" class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label mb-1" style="font-size:.8rem;">Branch / Class</label>
                <select name="branch_id" class="form-select form-select-sm" required>
                    <option value="">Select Branch</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ $branchId == $branch->id ? 'selected' : '' }}>
                            {{ $branch->course->name ?? '' }} — {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label mb-1" style="font-size:.8rem;">Date</label>
                <input type="date" name="date" class="form-control form-control-sm"
                       value="{{ $date }}" max="{{ today()->toDateString() }}">
            </div>
            <div class="col-12 col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="fa-solid fa-search me-1"></i> Load Students
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Attendance Form --}}
@if($students->isNotEmpty())
<form method="POST" action="{{ route('admin.attendance.students.mark') }}" id="attendanceForm">
    @csrf
    <input type="hidden" name="branch_id" value="{{ $branchId }}">
    <input type="hidden" name="attendance_date" value="{{ $date }}">

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <span style="font-weight:600;">
                    <i class="fa-solid fa-users me-2" style="color:#4f46e5;"></i>
                    {{ $students->count() }} Students
                </span>
                <span class="badge ms-2" style="background:rgba(79,70,229,.1);color:#4f46e5;">
                    {{ \Carbon\Carbon::parse($date)->format('d M Y, l') }}
                </span>
            </div>
            {{-- Quick Mark All --}}
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-success" onclick="markAll('present')">
                    <i class="fa-solid fa-check me-1"></i> All Present
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="markAll('absent')">
                    <i class="fa-solid fa-xmark me-1"></i> All Absent
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th style="width:50px;">#</th>
                        <th>Student</th>
                        <th>Adm. No</th>
                        <th class="text-center" style="width:120px;">
                            <span class="badge bg-success">Present</span>
                        </th>
                        <th class="text-center" style="width:120px;">
                            <span class="badge bg-danger">Absent</span>
                        </th>
                        <th class="text-center" style="width:120px;">
                            <span class="badge bg-warning text-dark">Late</span>
                        </th>
                        <th class="text-center" style="width:120px;">
                            <span class="badge bg-secondary">Holiday</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $i => $student)
                    @php $current = $existingAttendance[$student->id] ?? 'present'; @endphp
                    <tr id="row-{{ $student->id }}" class="attendance-row {{ $current === 'absent' ? 'table-danger' : ($current === 'present' ? 'table-success bg-opacity-25' : '') }}">
                        <td style="color:var(--muted);font-size:.82rem;">{{ $i + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $student->photo_url }}" class="rounded-circle"
                                     style="width:32px;height:32px;object-fit:cover;" alt="">
                                <div>
                                    <div style="font-weight:500;font-size:.875rem;">{{ $student->full_name }}</div>
                                    <div style="font-size:.72rem;color:var(--muted);">
                                        Sem {{ $student->current_semester }} | {{ $student->gender }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:.82rem;color:var(--muted);">{{ $student->admission_number }}</td>
                        @foreach(['present','absent','late','holiday'] as $status)
                        <td class="text-center">
                            <div class="form-check d-flex justify-content-center">
                                <input class="form-check-input attendance-radio"
                                       type="radio"
                                       name="attendance[{{ $student->id }}]"
                                       value="{{ $status }}"
                                       data-student="{{ $student->id }}"
                                       data-status="{{ $status }}"
                                       {{ $current === $status ? 'checked' : '' }}
                                       style="width:1.2rem;height:1.2rem;cursor:pointer;">
                            </div>
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="card-footer d-flex justify-content-between align-items-center"
             style="background:transparent;border-top:1px solid var(--border);">
            <div id="attendance-summary" style="font-size:.85rem;color:var(--muted);">
                <span class="text-success fw-600" id="count-present">0</span> Present &nbsp;|&nbsp;
                <span class="text-danger fw-600" id="count-absent">0</span> Absent &nbsp;|&nbsp;
                <span class="text-warning fw-600" id="count-late">0</span> Late
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-save me-2"></i> Save Attendance
            </button>
        </div>
    </div>
</form>

@elseif($branchId)
<div class="card">
    <div class="card-body">
        <div class="empty-state py-4">
            <i class="fa-solid fa-user-graduate" style="font-size:3rem;opacity:.3;"></i>
            <h5 class="mt-3">No active students found in this branch</h5>
        </div>
    </div>
</div>
@else
<div class="card">
    <div class="card-body">
        <div class="empty-state py-5">
            <i class="fa-solid fa-calendar-check" style="font-size:3.5rem;opacity:.2;"></i>
            <h5 class="mt-3" style="color:var(--muted);">Select a branch and date to mark attendance</h5>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
function markAll(status) {
    document.querySelectorAll('.attendance-radio[data-status="' + status + '"]').forEach(r => {
        r.checked = true;
        updateRow(r.dataset.student, status);
    });
    updateSummary();
}

function updateRow(studentId, status) {
    const row = document.getElementById('row-' + studentId);
    if (!row) return;
    row.classList.remove('table-success', 'table-danger', 'table-warning', 'bg-opacity-25');
    if (status === 'present') row.classList.add('table-success', 'bg-opacity-25');
    else if (status === 'absent') row.classList.add('table-danger');
    else if (status === 'late') row.classList.add('table-warning');
}

function updateSummary() {
    const counts = { present: 0, absent: 0, late: 0, holiday: 0 };
    document.querySelectorAll('.attendance-radio:checked').forEach(r => {
        counts[r.dataset.status] = (counts[r.dataset.status] || 0) + 1;
    });
    document.getElementById('count-present').textContent = counts.present;
    document.getElementById('count-absent').textContent  = counts.absent;
    document.getElementById('count-late').textContent    = counts.late;
}

document.querySelectorAll('.attendance-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        updateRow(this.dataset.student, this.dataset.status);
        updateSummary();
    });
});

updateSummary();
</script>
@endpush

