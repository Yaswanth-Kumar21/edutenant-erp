@extends('layouts.student-app')

@section('title', 'My Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')

{{-- ── Welcome Banner ── --}}
<div class="mb-4 rounded-3 overflow-hidden position-relative"
     style="background:linear-gradient(135deg,#4F46E5 0%,#7C3AED 50%,#6D28D9 100%);padding:2rem;">
    <div class="position-absolute" style="top:-50px;right:-50px;width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,.08);pointer-events:none;"></div>
    <div class="position-absolute" style="bottom:-30px;left:30%;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,.05);pointer-events:none;"></div>
    <div class="d-flex align-items-center gap-4 flex-wrap position-relative">
        <img src="{{ $student->photo_url }}" alt="{{ $student->full_name }}"
             class="rounded-circle flex-shrink-0"
             style="width:80px;height:80px;object-fit:cover;border:3px solid rgba(255,255,255,.4);box-shadow:0 4px 15px rgba(0,0,0,.2);">
        <div class="flex-1">
            <h2 class="text-white fw-bold mb-1" style="font-size:1.5rem;">
                Welcome back, {{ $student->first_name }}! 👋
            </h2>
            <p class="mb-2" style="color:rgba(255,255,255,.75);font-size:.875rem;">
                {{ $student->branch?->name ?? '—' }}
                @if($student->branch?->course) &bull; {{ $student->branch->course->name }} @endif
                &bull; Semester {{ $student->current_semester }}
                &bull; {{ $student->academicYear?->name ?? '—' }}
            </p>
            <div class="d-flex gap-2 flex-wrap">
                <span class="badge" style="background:rgba(255,255,255,.2);color:#fff;font-family:monospace;font-size:.78rem;">
                    {{ $student->admission_number }}
                </span>
                <span class="badge" style="background:#DCFCE7;color:#166534;">{{ ucfirst($student->status) }}</span>
                <span class="badge" style="background:rgba(255,255,255,.15);color:#fff;">{{ $student->category }}</span>
            </div>
        </div>
        <div class="text-end d-none d-md-block">
            <div style="color:rgba(255,255,255,.6);font-size:.75rem;">Institution</div>
            <div class="text-white fw-bold" style="font-size:.9rem;">{{ $tenant->name ?? 'EduTenant ERP' }}</div>
            <div style="color:rgba(255,255,255,.6);font-size:.75rem;margin-top:.5rem;">Admission Date</div>
            <div class="text-white fw-bold">{{ $student->admission_date?->format('d M Y') ?? '—' }}</div>
        </div>
    </div>
</div>

{{-- ── Stats ── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="stat-card blue">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label mb-2">Attendance</div>
                    <div class="stat-value">{{ $attendancePercent }}%</div>
                    <div class="mt-1" style="font-size:.75rem;opacity:.85;">{{ $presentDays }}/{{ $totalDays }} days</div>
                </div>
                <i class="fa-solid fa-calendar-check stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card green">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label mb-2">Fees Paid</div>
                    <div class="stat-value" style="font-size:1.5rem;">₹{{ number_format($totalFeesPaid) }}</div>
                    <div class="mt-1" style="font-size:.75rem;opacity:.85;">Total paid</div>
                </div>
                <i class="fa-solid fa-circle-check stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card orange">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label mb-2">Fees Pending</div>
                    <div class="stat-value" style="font-size:1.5rem;">₹{{ number_format($totalFeesDue) }}</div>
                    <div class="mt-1" style="font-size:.75rem;opacity:.85;">Outstanding</div>
                </div>
                <i class="fa-solid fa-clock stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card purple">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label mb-2">Certificates</div>
                    <div class="stat-value">{{ $certCount }}</div>
                    <div class="mt-1" style="font-size:.75rem;opacity:.85;">Uploaded</div>
                </div>
                <i class="fa-solid fa-file-certificate stat-icon"></i>
            </div>
        </div>
    </div>
</div>

{{-- ── Attendance Alert ── --}}
@if($attendancePercent < 75 && $totalDays > 0)
<div class="alert d-flex align-items-center gap-3 mb-4" style="background:rgba(220,38,38,.08);border:1px solid rgba(220,38,38,.2);border-radius:12px;color:#B91C1C;">
    <i class="fa-solid fa-triangle-exclamation fa-lg flex-shrink-0"></i>
    <div>
        <strong>Low Attendance Warning!</strong> Your attendance is {{ $attendancePercent }}%.
        Minimum 75% is required to appear in examinations. Please attend classes regularly.
    </div>
</div>
@endif

<div class="row g-3">
    {{-- Profile Card --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="fa-solid fa-user me-2" style="color:var(--sp,#7C3AED);"></i>My Profile</span>
                <a href="{{ route('student.profile.index') }}" style="font-size:.78rem;color:var(--sp,#7C3AED);text-decoration:none;">View →</a>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <img src="{{ $student->photo_url }}" alt="{{ $student->full_name }}"
                         class="rounded-circle mb-2"
                         style="width:72px;height:72px;object-fit:cover;border:3px solid var(--border);">
                    <div style="font-weight:700;font-size:1rem;color:var(--text);">{{ $student->full_name }}</div>
                    <div style="font-size:.78rem;color:var(--muted);">{{ $student->branch?->course?->name ?? '—' }}</div>
                </div>
                <dl class="row mb-0" style="font-size:.82rem;">
                    <dt class="col-5" style="color:var(--muted);font-weight:500;">Phone</dt>
                    <dd class="col-7 mb-2">{{ $student->phone ?? '—' }}</dd>
                    <dt class="col-5" style="color:var(--muted);font-weight:500;">Father</dt>
                    <dd class="col-7 mb-2">{{ $student->guardian?->father_name ?? $student->father_name ?? '—' }}</dd>
                    <dt class="col-5" style="color:var(--muted);font-weight:500;">Blood Group</dt>
                    <dd class="col-7 mb-2">{{ $student->blood_group ?? '—' }}</dd>
                    <dt class="col-5" style="color:var(--muted);font-weight:500;">Category</dt>
                    <dd class="col-7 mb-0">
                        <span class="badge" style="background:rgba(124,58,237,.1);color:#7C3AED;">{{ $student->category }}</span>
                    </dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- Attendance Chart --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="fa-solid fa-calendar-check me-2" style="color:#059669;"></i>Attendance</span>
                <a href="{{ route('student.attendance.index') }}" style="font-size:.78rem;color:#059669;text-decoration:none;">Details →</a>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div style="position:relative;display:inline-block;">
                        <canvas id="attChart" width="140" height="140"></canvas>
                        <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;">
                            <div style="font-size:1.5rem;font-weight:800;color:var(--text);">{{ $attendancePercent }}%</div>
                            <div style="font-size:.68rem;color:var(--muted);">Present</div>
                        </div>
                    </div>
                </div>
                <div class="row g-2 text-center">
                    <div class="col-4">
                        <div style="font-size:1.25rem;font-weight:700;color:#059669;">{{ $presentDays }}</div>
                        <div style="font-size:.7rem;color:var(--muted);">Present</div>
                    </div>
                    <div class="col-4">
                        <div style="font-size:1.25rem;font-weight:700;color:#DC2626;">{{ $absentDays }}</div>
                        <div style="font-size:.7rem;color:var(--muted);">Absent</div>
                    </div>
                    <div class="col-4">
                        <div style="font-size:1.25rem;font-weight:700;color:var(--muted);">{{ $totalDays }}</div>
                        <div style="font-size:.7rem;color:var(--muted);">Total</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Fee Summary --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="fa-solid fa-receipt me-2" style="color:#7C3AED;"></i>My Fees</span>
                <a href="{{ route('student.fees.index') }}" style="font-size:.78rem;color:#7C3AED;text-decoration:none;">History →</a>
            </div>
            <div class="card-body">
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="p-3 rounded-2 text-center" style="background:rgba(5,150,105,.08);border:1px solid rgba(5,150,105,.15);">
                            <div style="font-size:1.1rem;font-weight:800;color:#059669;">₹{{ number_format($totalFeesPaid) }}</div>
                            <div style="font-size:.7rem;color:#065F46;">Paid</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-2 text-center" style="background:rgba(220,38,38,.08);border:1px solid rgba(220,38,38,.15);">
                            <div style="font-size:1.1rem;font-weight:800;color:#DC2626;">₹{{ number_format($totalFeesDue) }}</div>
                            <div style="font-size:.7rem;color:#991B1B;">Pending</div>
                        </div>
                    </div>
                </div>
                @if($recentPayments->isEmpty())
                <div class="text-center py-2" style="color:var(--muted);font-size:.82rem;">No payments yet.</div>
                @else
                <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--muted);margin-bottom:.5rem;">Recent</div>
                @foreach($recentPayments as $payment)
                <div class="d-flex align-items-center justify-content-between py-2" style="border-bottom:1px solid var(--border);font-size:.82rem;">
                    <div>
                        <div style="font-weight:500;color:var(--text);">{{ $payment->feeType?->name ?? 'Fee' }}</div>
                        <div style="font-size:.7rem;color:var(--muted);">{{ $payment->payment_date?->format('d M Y') }}</div>
                    </div>
                    <div class="text-end">
                        <div style="font-weight:700;color:#059669;">₹{{ number_format($payment->amount_paid) }}</div>
                        @php $sc = ['paid'=>['#DCFCE7','#166534'],'partial'=>['#FEF3C7','#92400E'],'pending'=>['#FEE2E2','#991B1B']][$payment->status] ?? ['#F3F4F6','#374151']; @endphp
                        <span class="badge" style="background:{{ $sc[0] }};color:{{ $sc[1] }};font-size:.65rem;">{{ ucfirst($payment->status) }}</span>
                    </div>
                </div>
                @endforeach
                @endif
            </div>
        </div>
    </div>

    {{-- Admission Info --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="fa-solid fa-id-card me-2" style="color:#D97706;"></i>Admission Details</span>
                @if($student->admissionReceipts->first())
                <a href="{{ route('admin.admissions.receipt', $student) }}" class="btn btn-sm btn-outline-primary" style="font-size:.72rem;">
                    <i class="fa-solid fa-receipt me-1"></i> Receipt
                </a>
                @endif
            </div>
            <div class="card-body">
                <div class="row g-0" style="font-size:.82rem;">
                    <div class="col-6">
                        <dl>
                            <dt style="color:var(--muted);font-weight:500;">Adm. Number</dt>
                            <dd class="mb-2" style="font-family:monospace;color:#7C3AED;font-weight:700;">{{ $student->admission_number }}</dd>
                            <dt style="color:var(--muted);font-weight:500;">Adm. Date</dt>
                            <dd class="mb-2">{{ $student->admission_date?->format('d M Y') ?? '—' }}</dd>
                            <dt style="color:var(--muted);font-weight:500;">Branch</dt>
                            <dd class="mb-0">{{ $student->branch?->name ?? '—' }}</dd>
                        </dl>
                    </div>
                    <div class="col-6">
                        <dl>
                            <dt style="color:var(--muted);font-weight:500;">Course</dt>
                            <dd class="mb-2">{{ $student->branch?->course?->name ?? '—' }}</dd>
                            <dt style="color:var(--muted);font-weight:500;">Semester</dt>
                            <dd class="mb-2">Semester {{ $student->current_semester }}</dd>
                            <dt style="color:var(--muted);font-weight:500;">Academic Year</dt>
                            <dd class="mb-0">{{ $student->academicYear?->name ?? '—' }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Certificates + Notifications --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="fa-solid fa-bell me-2" style="color:#7C3AED;"></i>Notifications</span>
            </div>
            <div class="card-body p-0">
                <div class="d-flex align-items-start gap-3 p-3" style="border-bottom:1px solid var(--border);">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;background:rgba(79,70,229,.1);">
                        <i class="fa-solid fa-graduation-cap" style="color:#4F46E5;font-size:.85rem;"></i>
                    </div>
                    <div>
                        <div style="font-size:.875rem;font-weight:500;color:var(--text);">Welcome to {{ $tenant->name ?? 'EduTenant ERP' }}</div>
                        <div style="font-size:.75rem;color:var(--muted);">Your admission has been confirmed.</div>
                        <div style="font-size:.7rem;color:var(--muted);margin-top:.2rem;">{{ $student->admission_date?->format('d M Y') }}</div>
                    </div>
                </div>
                @if($totalFeesDue > 0)
                <div class="d-flex align-items-start gap-3 p-3" style="border-bottom:1px solid var(--border);">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;background:rgba(220,38,38,.1);">
                        <i class="fa-solid fa-triangle-exclamation" style="color:#DC2626;font-size:.85rem;"></i>
                    </div>
                    <div>
                        <div style="font-size:.875rem;font-weight:500;color:var(--text);">Fee Due</div>
                        <div style="font-size:.75rem;color:var(--muted);">₹{{ number_format($totalFeesDue) }} pending. Please pay at the office.</div>
                    </div>
                </div>
                @endif
                @if($attendancePercent < 75 && $totalDays > 0)
                <div class="d-flex align-items-start gap-3 p-3" style="border-bottom:1px solid var(--border);">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;background:rgba(217,119,6,.1);">
                        <i class="fa-solid fa-calendar-xmark" style="color:#D97706;font-size:.85rem;"></i>
                    </div>
                    <div>
                        <div style="font-size:.875rem;font-weight:500;color:var(--text);">Low Attendance</div>
                        <div style="font-size:.75rem;color:var(--muted);">Your attendance is {{ $attendancePercent }}%. Minimum 75% required.</div>
                    </div>
                </div>
                @endif
                <div class="d-flex align-items-start gap-3 p-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;background:rgba(5,150,105,.1);">
                        <i class="fa-solid fa-file-check" style="color:#059669;font-size:.85rem;"></i>
                    </div>
                    <div>
                        <div style="font-size:.875rem;font-weight:500;color:var(--text);">Certificates</div>
                        <div style="font-size:.75rem;color:var(--muted);">{{ $certCount }} document(s) uploaded.
                            @if($certCount < 3) Submit remaining documents to the office. @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function(){
    const ctx = document.getElementById('attChart');
    if(ctx){
        const present = {{ $presentDays }};
        const absent  = {{ $absentDays }};
        const total   = present + absent;
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: total > 0 ? [present, absent] : [1, 0],
                    backgroundColor: ['#059669', total > 0 ? '#FEE2E2' : '#E5E7EB'],
                    borderColor:     ['#059669', total > 0 ? '#FCA5A5' : '#E5E7EB'],
                    borderWidth: 2,
                }]
            },
            options: {
                cutout: '72%',
                plugins: { legend:{display:false}, tooltip:{enabled:total>0} },
                animation: { animateRotate:true, duration:1000 },
            }
        });
    }
})();
</script>
@endpush
