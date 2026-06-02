@extends('layouts.app')
@section('title', '360° — ' . $student->full_name)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}" style="color:#4f46e5;text-decoration:none;">Students</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.students.360.search') }}" style="color:#4f46e5;text-decoration:none;">360° View</a></li>
    <li class="breadcrumb-item active">{{ $student->full_name }}</li>
@endsection
@push('styles')
<style>
.s360-tab{padding:.6rem 1rem;font-size:.82rem;font-weight:500;color:var(--muted);border:none;background:transparent;cursor:pointer;border-bottom:2px solid transparent;margin-bottom:-1px;transition:all .15s;display:flex;align-items:center;gap:.4rem;border-radius:6px 6px 0 0;white-space:nowrap;}
.s360-tab:hover{color:var(--text);background:var(--surface2);}
.s360-tab.active{color:#4f46e5;border-bottom-color:#4f46e5;background:transparent;}
.s360-tabs{display:flex;gap:2px;border-bottom:1px solid var(--border);margin-bottom:1.5rem;overflow-x:auto;padding-bottom:0;}
.s360-panel{display:none;}.s360-panel.active{display:block;animation:fadeUp .25s ease;}
.stat-widget{background:var(--surface);border:1px solid var(--border);border-radius:.75rem;padding:1.25rem;text-align:center;}
.stat-widget .val{font-size:1.75rem;font-weight:800;line-height:1;}
.stat-widget .lbl{font-size:.72rem;color:var(--muted);margin-top:.25rem;text-transform:uppercase;letter-spacing:.04em;}
.progress-bar-custom{height:8px;background:var(--border);border-radius:4px;overflow:hidden;}
.progress-fill{height:100%;border-radius:4px;transition:width .5s ease;}
.timeline-item{display:flex;gap:1rem;padding:.75rem 0;border-bottom:1px solid var(--border);}
.timeline-item:last-child{border-bottom:none;}
.timeline-dot{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:.8rem;}
.info-row{display:flex;justify-content:space-between;align-items:center;padding:.5rem 0;border-bottom:1px solid var(--border);font-size:.875rem;}
.info-row:last-child{border-bottom:none;}
.info-label{color:var(--muted);font-weight:500;min-width:140px;}
.info-value{font-weight:600;color:var(--text);text-align:right;}
.sch-card{border:1px solid var(--border);border-radius:.75rem;padding:1rem;margin-bottom:.75rem;}
</style>
@endpush
@section('content')

{{-- ── Profile Header ─────────────────────────────────────────────────── --}}
<div class="card mb-4 border-0" style="background:linear-gradient(135deg,#4f46e5,#7c3aed);border-radius:1rem;overflow:hidden;">
    <div class="card-body p-4">
        <div class="d-flex align-items-center gap-4 flex-wrap">
            <img src="{{ $student->photo_url }}" alt="{{ $student->full_name }}" class="rounded-circle"
                 style="width:90px;height:90px;object-fit:cover;border:3px solid rgba(255,255,255,.4);">
            <div class="flex-1">
                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                    <h2 class="text-white fw-bold mb-0" style="font-size:1.4rem;">{{ $student->full_name }}</h2>
                    <span class="badge" style="background:rgba(255,255,255,.2);color:#fff;font-family:monospace;">{{ $student->admission_number }}</span>
                    <span class="badge" style="background:{{ $currentStatus['bg'] }};color:{{ $currentStatus['color'] }};">
                        <i class="fa-solid {{ $currentStatus['icon'] }} me-1"></i>{{ $currentStatus['label'] }}
                    </span>
                </div>
                <p class="mb-1" style="color:rgba(255,255,255,.8);font-size:.875rem;">
                    {{ $student->branch?->name ?? '—' }}
                    @if($student->branch?->course) &bull; {{ $student->branch->course->name }} @endif
                    @if($student->branch?->course?->stream) &bull; {{ $student->branch->course->stream->name }} @endif
                    &bull; Semester {{ $student->current_semester }}
                    &bull; {{ $student->category }}
                </p>
                <p class="mb-0" style="color:rgba(255,255,255,.65);font-size:.8rem;">
                    @if($student->phone)<i class="fa-solid fa-phone me-1"></i>{{ $student->phone }}@endif
                    @if($student->email) &nbsp;&bull;&nbsp;<i class="fa-solid fa-envelope me-1"></i>{{ $student->email }}@endif
                    &nbsp;&bull;&nbsp;<i class="fa-solid fa-calendar me-1"></i>Admitted {{ $student->admission_date?->format('d M Y') }}
                </p>
            </div>
            <div class="d-flex gap-2 ms-auto flex-wrap">
                <a href="{{ route('admin.students.360.search') }}" class="btn btn-sm" style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.3);">
                    <i class="fa-solid fa-search me-1"></i>New Search
                </a>
                @if(auth()->user()->isCollegeAdmin() || auth()->user()->isSuperAdmin())
                <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-sm btn-light" style="font-weight:500;">
                    <i class="fa-solid fa-pen me-1"></i>Edit
                </a>
                @endif
                <a href="{{ route('admin.admissions.receipt', $student) }}" class="btn btn-sm" style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.3);">
                    <i class="fa-solid fa-receipt me-1"></i>Receipt
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ── KPI Widgets ─────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-widget">
            <div class="val" style="color:#4f46e5;">₹{{ number_format($totalPaid) }}</div>
            <div class="lbl">Total Fees Paid</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-widget">
            <div class="val" style="color:{{ $attendancePct >= 75 ? '#059669' : '#dc2626' }};">{{ $attendancePct }}%</div>
            <div class="lbl">Attendance</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-widget">
            <div class="val" style="color:#059669;">₹{{ number_format($totalScholarship) }}</div>
            <div class="lbl">Total Scholarship</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-widget">
            <div class="val" style="color:{{ $totalDue > 0 ? '#dc2626' : '#059669' }};">₹{{ number_format($totalDue) }}</div>
            <div class="lbl">Fee Due</div>
        </div>
    </div>
</div>

{{-- ── Tabs ─────────────────────────────────────────────────────────────── --}}
<div class="s360-tabs" id="s360-tabs">
    @php $tabs = [
        ['id'=>'overview',     'icon'=>'fa-user',           'label'=>'Overview'],
        ['id'=>'academic',     'icon'=>'fa-graduation-cap', 'label'=>'Academic'],
        ['id'=>'attendance',   'icon'=>'fa-calendar-check', 'label'=>'Attendance'],
        ['id'=>'fees',         'icon'=>'fa-receipt',        'label'=>'Fees'],
        ['id'=>'scholarships', 'icon'=>'fa-award',          'label'=>'Scholarships'],
        ['id'=>'transport',    'icon'=>'fa-bus',             'label'=>'Transport'],
        ['id'=>'documents',    'icon'=>'fa-file-check',     'label'=>'Documents'],
        ['id'=>'timeline',     'icon'=>'fa-timeline',       'label'=>'Timeline'],
    ]; @endphp
    @foreach($tabs as $tab)
    <button class="s360-tab {{ $loop->first?'active':'' }}" onclick="switchTab('{{ $tab['id'] }}',this)">
        <i class="fa-solid {{ $tab['icon'] }}"></i>{{ $tab['label'] }}
    </button>
    @endforeach
</div>

{{-- ════ TAB: Overview ════ --}}
<div class="s360-panel active" id="panel-overview">
<div class="row g-3">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header"><i class="fa-solid fa-user me-2" style="color:#4f46e5;"></i>Basic Information</div>
            <div class="card-body p-0">
                @php $basic = [
                    ['Full Name',     $student->full_name],
                    ['Date of Birth', $student->date_of_birth?->format('d M Y') ?? '—'],
                    ['Gender',        ucfirst($student->gender ?? '—')],
                    ['Blood Group',   $student->blood_group ?? '—'],
                    ['Phone',         $student->phone ?? '—'],
                    ['Email',         $student->email ?? '—'],
                    ['Aadhaar',       $student->aadhaar_number ? '****'.substr($student->aadhaar_number,-4) : '—'],
                    ['Address',       trim(implode(', ', array_filter([$student->address, $student->city, $student->state, $student->pincode]))) ?: '—'],
                ]; @endphp
                @foreach($basic as $i=>[$lbl,$val])
                <div class="info-row px-4">
                    <span class="info-label">{{ $lbl }}</span>
                    <span class="info-value">{{ $val }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card mb-3">
            <div class="card-header"><i class="fa-solid fa-id-card me-2" style="color:#d97706;"></i>Admission Info</div>
            <div class="card-body p-0">
                @php $adm = [
                    ['Admission No',   $student->admission_number],
                    ['Admission Date', $student->admission_date?->format('d M Y') ?? '—'],
                    ['Category',       $student->category],
                    ['Status',         ucfirst(str_replace('_',' ',$student->status))],
                    ['Vehicle',        $student->vehicle_opted ? 'Yes' : 'No'],
                    ['Scholarship',    $student->scholarship_eligible ? 'Eligible' : 'Not Eligible'],
                ]; @endphp
                @foreach($adm as [$lbl,$val])
                <div class="info-row px-4">
                    <span class="info-label">{{ $lbl }}</span>
                    <span class="info-value">{{ $val }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @if($student->guardian)
        <div class="card">
            <div class="card-header"><i class="fa-solid fa-users me-2" style="color:#7c3aed;"></i>Parent / Guardian</div>
            <div class="card-body p-0">
                @php $grd = [
                    ['Father',         $student->guardian->father_name ?? '—'],
                    ['Father Phone',   $student->guardian->father_phone ?? '—'],
                    ['Mother',         $student->guardian->mother_name ?? '—'],
                    ['Mother Phone',   $student->guardian->mother_phone ?? '—'],
                    ['Annual Income',  $student->guardian->annual_income ? '₹'.number_format($student->guardian->annual_income) : '—'],
                ]; @endphp
                @foreach($grd as [$lbl,$val])
                <div class="info-row px-4">
                    <span class="info-label">{{ $lbl }}</span>
                    <span class="info-value">{{ $val }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
</div>

{{-- ════ TAB: Academic ════ --}}
<div class="s360-panel" id="panel-academic">
<div class="row g-3">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><i class="fa-solid fa-graduation-cap me-2" style="color:#059669;"></i>Academic Details</div>
            <div class="card-body p-0">
                @php $acad = [
                    ['Branch',          $student->branch?->name ?? '—'],
                    ['Course',          $student->branch?->course?->name ?? '—'],
                    ['Stream',          $student->branch?->course?->stream?->name ?? '—'],
                    ['Academic Year',   $student->academicYear?->name ?? '—'],
                    ['Current Semester','Semester '.$student->current_semester],
                    ['Admission Date',  $student->admission_date?->format('d M Y') ?? '—'],
                    ['10th Marks',      $student->marks_10th ? $student->marks_10th.'%' : '—'],
                    ['12th Marks',      $student->marks_12th ? $student->marks_12th.'%' : '—'],
                    ['Prev. Institution',$student->previous_institution ?? '—'],
                    ['Univ. Reg. No',   $student->university_reg_number ?? '—'],
                ]; @endphp
                @foreach($acad as [$lbl,$val])
                <div class="info-row px-4">
                    <span class="info-label">{{ $lbl }}</span>
                    <span class="info-value">{{ $val }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><i class="fa-solid fa-chart-bar me-2" style="color:#4f46e5;"></i>Academic Progress</div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div style="font-size:2.5rem;font-weight:800;color:#4f46e5;">{{ $student->current_semester }}</div>
                    <div style="font-size:.82rem;color:var(--muted);">Current Semester</div>
                </div>
                @php $totalSem = $student->branch?->course?->total_semesters ?? 8; @endphp
                <div class="progress-bar-custom mb-2">
                    <div class="progress-fill" style="width:{{ ($student->current_semester/$totalSem)*100 }}%;background:#4f46e5;"></div>
                </div>
                <div style="font-size:.78rem;color:var(--muted);text-align:center;">{{ $student->current_semester }} of {{ $totalSem }} semesters completed</div>
                <div class="mt-3 p-3 rounded" style="background:var(--bg);border:1px solid var(--border);">
                    <div style="font-size:.78rem;color:var(--muted);margin-bottom:.5rem;">Attendance Overview</div>
                    <div class="d-flex justify-content-between mb-1" style="font-size:.82rem;">
                        <span>{{ $attendancePct }}% Present</span>
                        <span style="color:{{ $attendancePct>=75?'#059669':'#dc2626' }};">{{ $attendancePct>=75?'✓ Good':'⚠ Low' }}</span>
                    </div>
                    <div class="progress-bar-custom">
                        <div class="progress-fill" style="width:{{ $attendancePct }}%;background:{{ $attendancePct>=75?'#059669':'#dc2626' }};"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

{{-- ════ TAB: Attendance ════ --}}
<div class="s360-panel" id="panel-attendance">
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-widget">
            <div class="val" style="color:{{ $attendancePct>=75?'#059669':'#dc2626' }};">{{ $attendancePct }}%</div>
            <div class="lbl">Overall Attendance</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-widget">
            <div class="val" style="color:#059669;">{{ $presentDays }}</div>
            <div class="lbl">Days Present</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-widget">
            <div class="val" style="color:#dc2626;">{{ $absentDays }}</div>
            <div class="lbl">Days Absent</div>
        </div>
    </div>
</div>
<div class="card mb-4">
    <div class="card-header"><i class="fa-solid fa-chart-line me-2" style="color:#0891b2;"></i>Overall Attendance</div>
    <div class="card-body">
        <div class="d-flex align-items-center gap-3 mb-2">
            <div style="font-size:2rem;font-weight:800;color:{{ $attendancePct>=75?'#059669':'#dc2626' }};">{{ $attendancePct }}%</div>
            <div>
                <div style="font-size:.82rem;color:var(--muted);">{{ $presentDays }} present / {{ $totalDays }} total days</div>
                @if($attendancePct < 75)
                <div style="font-size:.78rem;color:#dc2626;font-weight:600;"><i class="fa-solid fa-triangle-exclamation me-1"></i>Below 75% — Attendance Warning</div>
                @endif
            </div>
        </div>
        <div class="progress-bar-custom" style="height:12px;">
            <div class="progress-fill" style="width:{{ $attendancePct }}%;background:{{ $attendancePct>=75?'#059669':'#dc2626' }};"></div>
        </div>
    </div>
</div>
@if($semesterAttendance->isNotEmpty())
<div class="card">
    <div class="card-header"><i class="fa-solid fa-table me-2" style="color:#4f46e5;"></i>Semester-wise Attendance</div>
    <div class="table-responsive">
        <table class="table mb-0" style="font-size:.82rem;">
            <thead><tr><th>Semester</th><th>Present</th><th>Absent</th><th>Total</th><th>Percentage</th><th>Status</th></tr></thead>
            <tbody>
                @foreach($semesterAttendance as $sa)
                <tr>
                    <td style="font-weight:600;">Semester {{ $sa['semester'] }}</td>
                    <td style="color:#059669;font-weight:600;">{{ $sa['present'] }}</td>
                    <td style="color:#dc2626;font-weight:600;">{{ $sa['absent'] }}</td>
                    <td>{{ $sa['total'] }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress-bar-custom flex-1" style="height:6px;min-width:60px;">
                                <div class="progress-fill" style="width:{{ $sa['pct'] }}%;background:{{ $sa['pct']>=75?'#059669':'#dc2626' }};"></div>
                            </div>
                            <span style="font-weight:600;color:{{ $sa['pct']>=75?'#059669':'#dc2626' }};min-width:40px;">{{ $sa['pct'] }}%</span>
                        </div>
                    </td>
                    <td>
                        @if($sa['pct']>=75)
                        <span class="badge" style="background:#dcfce7;color:#166534;">Good</span>
                        @elseif($sa['pct']>=60)
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
<div class="card"><div class="card-body"><div class="empty-state py-4"><i class="fa-solid fa-calendar-xmark d-block mb-2" style="font-size:2rem;opacity:.2;"></i><h5>No Attendance Records</h5><p>No attendance has been recorded yet.</p></div></div></div>
@endif
</div>

{{-- ════ TAB: Fees ════ --}}
<div class="s360-panel" id="panel-fees">
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3"><div class="stat-widget"><div class="val" style="color:#4f46e5;">₹{{ number_format($totalPaid) }}</div><div class="lbl">Total Paid</div></div></div>
    <div class="col-6 col-md-3"><div class="stat-widget"><div class="val" style="color:#dc2626;">₹{{ number_format($totalDue) }}</div><div class="lbl">Total Due</div></div></div>
    <div class="col-6 col-md-3"><div class="stat-widget"><div class="val" style="color:#059669;">₹{{ number_format($totalScholarship) }}</div><div class="lbl">Scholarship</div></div></div>
    <div class="col-6 col-md-3"><div class="stat-widget"><div class="val" style="color:#0891b2;">₹{{ number_format($transportFeeTotal) }}</div><div class="lbl">Transport Fee</div></div></div>
</div>
<div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="fa-solid fa-receipt me-2" style="color:#7c3aed;"></i>Payment History</span>
        @if(auth()->user()->isCollegeAdmin() || auth()->user()->isSuperAdmin())
        <a href="{{ route('admin.fees.payments.create') }}?student_id={{ $student->id }}" class="btn btn-sm btn-primary" style="font-size:.78rem;">
            <i class="fa-solid fa-plus me-1"></i>Collect Fee
        </a>
        @endif
    </div>
    @if($student->feePayments->isEmpty())
    <div class="card-body"><div class="empty-state py-3"><i class="fa-solid fa-receipt d-block mb-2" style="font-size:2rem;opacity:.2;"></i><p class="mb-0 small">No fee payments recorded</p></div></div>
    @else
    <div class="table-responsive">
        <table class="table mb-0" style="font-size:.82rem;">
            <thead><tr><th>Receipt No</th><th>Fee Type</th><th>Amount Due</th><th>Amount Paid</th><th>Date</th><th>Mode</th><th>Collected By</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @foreach($student->feePayments->sortByDesc('payment_date') as $payment)
                <tr>
                    <td><a href="{{ route('admin.fees.receipt', $payment) }}" style="color:#4f46e5;font-family:monospace;text-decoration:none;">{{ $payment->receipt_number }}</a></td>
                    <td>{{ $payment->feeType?->name ?? '—' }}</td>
                    <td>₹{{ number_format($payment->amount_due) }}</td>
                    <td style="font-weight:600;color:#059669;">₹{{ number_format($payment->amount_paid) }}</td>
                    <td style="color:var(--muted);">{{ $payment->payment_date?->format('d M Y') }}</td>
                    <td>{{ ucfirst($payment->payment_mode ?? '—') }}</td>
                    <td style="color:var(--muted);">{{ $payment->collectedBy?->name ?? '—' }}</td>
                    <td>
                        @if($payment->status==='paid')<span class="badge" style="background:#dcfce7;color:#166534;">Paid</span>
                        @elseif($payment->status==='partial')<span class="badge" style="background:#fef3c7;color:#92400e;">Partial</span>
                        @else<span class="badge" style="background:#fee2e2;color:#991b1b;">Pending</span>@endif
                    </td>
                    <td><a href="{{ route('admin.fees.receipt', $payment) }}" class="btn btn-sm btn-outline-primary" style="font-size:.72rem;padding:.2rem .5rem;"><i class="fa-solid fa-eye"></i></a></td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:var(--bg);">
                    <td colspan="3" style="font-weight:600;padding:.75rem 1rem;">Total Paid</td>
                    <td style="font-weight:700;color:#059669;padding:.75rem 1rem;">₹{{ number_format($totalPaid) }}</td>
                    <td colspan="5"></td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif
</div>
</div>

{{-- ════ TAB: Scholarships ════ --}}
<div class="s360-panel" id="panel-scholarships">
<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="stat-widget"><div class="val" style="color:#4f46e5;">₹{{ number_format($govtScholarshipTotal) }}</div><div class="lbl">Govt Scholarship</div></div></div>
    <div class="col-md-4"><div class="stat-widget"><div class="val" style="color:#d97706;">₹{{ number_format($collegeScholarshipTotal) }}</div><div class="lbl">College Scholarship</div></div></div>
    <div class="col-md-4"><div class="stat-widget"><div class="val" style="color:#059669;">₹{{ number_format($totalScholarship) }}</div><div class="lbl">Total Scholarship</div></div></div>
</div>
@if($student->scholarships->isEmpty())
<div class="card"><div class="card-body"><div class="empty-state py-4"><i class="fa-solid fa-award d-block mb-2" style="font-size:2rem;opacity:.2;"></i><h5>No Scholarships</h5><p>No scholarship records found for this student.</p></div></div></div>
@else
@foreach($student->scholarships->groupBy('scholarship_category') as $category => $scholarships)
<div class="card mb-3">
    <div class="card-header">
        <i class="fa-solid {{ $category==='government'?'fa-landmark':'fa-school' }} me-2" style="color:{{ $category==='government'?'#4f46e5':'#d97706' }};"></i>
        {{ ucfirst($category) }} Scholarship
        <span class="badge ms-2" style="background:{{ $category==='government'?'rgba(79,70,229,.1)':'rgba(217,119,6,.1)' }};color:{{ $category==='government'?'#4f46e5':'#d97706' }};">
            {{ $scholarships->count() }} record(s)
        </span>
    </div>
    <div class="card-body">
        @foreach($scholarships as $sch)
        <div class="sch-card">
            <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
                <div>
                    <div style="font-weight:600;font-size:.9rem;">{{ $sch->display_name }}</div>
                    <div style="font-size:.78rem;color:var(--muted);margin-top:2px;">
                        Semesters: {{ $sch->semesters_label }}
                        @if($sch->start_date) &bull; From {{ $sch->start_date->format('d M Y') }} @endif
                    </div>
                </div>
                <div class="text-end">
                    <div style="font-size:1.25rem;font-weight:800;color:#059669;">₹{{ number_format($sch->amount) }}</div>
                    @if($sch->status==='active')<span class="badge" style="background:#dcfce7;color:#166534;">Active</span>
                    @else<span class="badge" style="background:#fee2e2;color:#991b1b;">{{ ucfirst($sch->status) }}</span>@endif
                </div>
            </div>
        </div>
        @endforeach
        <div class="d-flex justify-content-between align-items-center pt-2" style="border-top:1px solid var(--border);">
            <span style="font-size:.82rem;color:var(--muted);">Total {{ ucfirst($category) }} Scholarship</span>
            <span style="font-weight:700;color:#059669;">₹{{ number_format($scholarships->sum('amount')) }}</span>
        </div>
    </div>
</div>
@endforeach
@endif
</div>

{{-- ════ TAB: Transport ════ --}}
<div class="s360-panel" id="panel-transport">
@if(!$student->vehicle_opted)
<div class="card"><div class="card-body"><div class="empty-state py-4"><i class="fa-solid fa-bus d-block mb-2" style="font-size:2rem;opacity:.2;"></i><h5>Transport Not Opted</h5><p>This student has not opted for college transport.</p></div></div></div>
@elseif($student->transportHistory->isEmpty())
<div class="card"><div class="card-body"><div class="empty-state py-4"><i class="fa-solid fa-bus d-block mb-2" style="font-size:2rem;opacity:.2;"></i><h5>No Transport Records</h5><p>Transport was opted but no details recorded.</p></div></div></div>
@else
@foreach($student->transportHistory as $tr)
<div class="card mb-3">
    <div class="card-header">
        <div class="d-flex align-items-center gap-2">
            <i class="fa-solid fa-bus" style="color:#0891b2;"></i>
            <span>Transport Assignment</span>
            @if($tr->status==='active')<span class="badge ms-2" style="background:#dcfce7;color:#166534;">Active</span>
            @else<span class="badge ms-2" style="background:#fee2e2;color:#991b1b;">Inactive</span>@endif
        </div>
    </div>
    <div class="card-body p-0">
        @php $tinfo = [
            ['Route',          $tr->route ?? '—'],
            ['Boarding Point', $tr->boarding_point ?? '—'],
            ['Transport Fee',  $tr->transport_fee ? '₹'.number_format($tr->transport_fee).'/semester' : '—'],
            ['Semesters',      $tr->semesters_label],
            ['Notes',          $tr->notes ?? '—'],
        ]; @endphp
        @foreach($tinfo as [$lbl,$val])
        <div class="info-row px-4"><span class="info-label">{{ $lbl }}</span><span class="info-value">{{ $val }}</span></div>
        @endforeach
    </div>
</div>
@endforeach
@endif
</div>

{{-- ════ TAB: Documents ════ --}}
<div class="s360-panel" id="panel-documents">
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="fa-solid fa-file-check me-2" style="color:#d97706;"></i>Certificates &amp; Documents</span>
        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#uploadCertModal360" style="font-size:.78rem;">
            <i class="fa-solid fa-upload me-1"></i>Upload
        </button>
    </div>
    @if($student->certificates->isEmpty())
    <div class="card-body">
        <div class="empty-state py-4">
            <i class="fa-solid fa-file-circle-xmark d-block mb-2" style="font-size:2rem;opacity:.2;"></i>
            <h5>No Documents Uploaded</h5>
            <p>No certificates have been uploaded for this student yet.</p>
        </div>
    </div>
    @else
    <div class="card-body">
        <div class="row g-2">
            @foreach($student->certificates as $cert)
            <div class="col-md-6">
                <div class="d-flex align-items-center gap-3 p-3 rounded" style="background:rgba(5,150,105,.04);border:1px solid rgba(5,150,105,.15);">
                    <div class="rounded d-flex align-items-center justify-content-center flex-shrink-0" style="width:40px;height:40px;background:rgba(5,150,105,.1);">
                        <i class="fa-solid fa-{{ $cert->is_pdf ? 'file-pdf' : 'file-image' }}" style="color:#059669;font-size:1rem;"></i>
                    </div>
                    <div class="flex-1 overflow-hidden">
                        <div style="font-size:.85rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $cert->certificate_label }}</div>
                        <div style="font-size:.72rem;color:var(--muted);">{{ $cert->original_filename }} &bull; {{ $cert->file_size_human }}</div>
                        @if($cert->is_verified)
                        <span style="font-size:.7rem;color:#059669;font-weight:600;"><i class="fa-solid fa-circle-check me-1"></i>Verified</span>
                        @endif
                    </div>
                    <div class="d-flex gap-1 flex-shrink-0">
                        <a href="{{ $cert->file_url }}" target="_blank" class="btn btn-sm btn-outline-primary" style="padding:.2rem .5rem;font-size:.72rem;" title="View"><i class="fa-solid fa-eye"></i></a>
                        @if(auth()->user()->isCollegeAdmin() || auth()->user()->isSuperAdmin())
                        <form method="POST" action="{{ route('admin.admissions.certificates.delete', $cert) }}" id="del-cert-360-{{ $cert->id }}">@csrf @method('DELETE')</form>
                        <button type="button" class="btn btn-sm btn-outline-danger" style="padding:.2rem .5rem;font-size:.72rem;" data-confirm-delete="del-cert-360-{{ $cert->id }}" data-name="{{ $cert->certificate_label }}" title="Remove"><i class="fa-solid fa-trash"></i></button>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
</div>

{{-- ════ TAB: Timeline ════ --}}
<div class="s360-panel" id="panel-timeline">
<div class="card">
    <div class="card-header"><i class="fa-solid fa-timeline me-2" style="color:#4f46e5;"></i>Student Lifecycle Timeline</div>
    <div class="card-body">
        @if(empty($timeline))
        <div class="empty-state py-4">
            <i class="fa-solid fa-timeline d-block mb-2" style="font-size:2rem;opacity:.2;"></i>
            <h5>No Events Yet</h5>
            <p>Timeline events will appear as the student progresses.</p>
        </div>
        @else
        <div style="position:relative;padding-left:1.5rem;">
            <div style="position:absolute;left:.75rem;top:0;bottom:0;width:2px;background:var(--border);transform:translateX(-50%);"></div>
            @foreach($timeline as $event)
            <div class="timeline-item" style="position:relative;padding-left:1rem;">
                <div class="timeline-dot flex-shrink-0" style="background:{{ $event['bg'] }};position:absolute;left:-1.75rem;top:.25rem;">
                    <i class="fa-solid {{ $event['icon'] }}" style="color:{{ $event['color'] }};font-size:.75rem;"></i>
                </div>
                <div class="flex-1">
                    <div class="d-flex align-items-start justify-content-between flex-wrap gap-1">
                        <div>
                            <div style="font-weight:600;font-size:.875rem;">{{ $event['title'] }}</div>
                            <div style="font-size:.78rem;color:var(--muted);margin-top:2px;">{{ $event['desc'] }}</div>
                        </div>
                        <div class="text-end flex-shrink-0">
                            <span class="badge" style="background:{{ $event['bg'] }};color:{{ $event['color'] }};font-size:.7rem;">{{ $event['badge'] }}</span>
                            <div style="font-size:.72rem;color:var(--muted);margin-top:3px;">
                                {{ isset($event['date']) ? (is_string($event['date']) ? $event['date'] : (method_exists($event['date'],'format') ? $event['date']->format('d M Y') : '')) : '' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
</div>

{{-- ── Certificate Upload Modal ─────────────────────────────────────────── --}}
<div class="modal fade" id="uploadCertModal360" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:.75rem;background:var(--surface);">
            <div class="modal-header" style="border-color:var(--border);">
                <h5 class="modal-title" style="font-size:1rem;font-weight:600;"><i class="fa-solid fa-upload me-2" style="color:#4f46e5;"></i>Upload Certificate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.admissions.certificates.upload', $student) }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Certificate Type <span class="text-danger">*</span></label>
                        <select name="certificate_type" class="form-select" required>
                            <option value="">— Select Type —</option>
                            @foreach(\App\Models\StudentCertificate::TYPES as $type => $label)
                            <option value="{{ $type }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">File <span class="text-danger">*</span></label>
                        <input type="file" name="certificate_file" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                        <div class="form-text">JPG, PNG, or PDF. Max 5MB.</div>
                    </div>
                </div>
                <div class="modal-footer" style="border-color:var(--border);">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-upload me-2"></i>Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function(){
'use strict';

/* ── Tab Switching ─────────────────────────────────────────────────── */
window.switchTab = function(id, btn) {
    document.querySelectorAll('.s360-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.s360-tab').forEach(b => b.classList.remove('active'));
    const panel = document.getElementById('panel-' + id);
    if (panel) panel.classList.add('active');
    if (btn) btn.classList.add('active');
    // Persist active tab in URL hash
    history.replaceState(null, '', '#' + id);
};

// Restore tab from URL hash on load
const hash = window.location.hash.replace('#', '');
if (hash) {
    const btn = document.querySelector('.s360-tab[onclick*="' + hash + '"]');
    if (btn) switchTab(hash, btn);
}

/* ── Confirm Delete ────────────────────────────────────────────────── */
document.querySelectorAll('[data-confirm-delete]').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const formId = this.dataset.confirmDelete;
        const name   = this.dataset.name || 'this record';
        Swal.fire({
            title: 'Remove ' + name + '?',
            text: 'This cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#DC2626',
            cancelButtonColor: '#64748B',
            confirmButtonText: 'Remove',
        }).then(r => { if (r.isConfirmed) document.getElementById(formId).submit(); });
    });
});

})();
</script>
@endpush
