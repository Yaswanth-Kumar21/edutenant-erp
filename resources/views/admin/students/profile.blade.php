@extends('layouts.app')

@section('title', 'Profile — ' . $student->full_name)

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.students.index') }}" style="color:#4f46e5;text-decoration:none;">Students</a>
    </li>
    <li class="breadcrumb-item active">{{ $student->full_name }}</li>
@endsection

@section('content')

{{-- Profile Header --}}
<div class="card mb-4 border-0"
     style="background:linear-gradient(135deg,#4f46e5,#7c3aed);border-radius:1rem;overflow:hidden;">
    <div class="card-body p-4">
        <div class="d-flex align-items-center gap-4 flex-wrap">
            <img src="{{ $student->photo_url }}" alt="{{ $student->full_name }}"
                 class="rounded-circle"
                 style="width:100px;height:100px;object-fit:cover;border:3px solid rgba(255,255,255,0.4);">
            <div class="flex-1">
                <h2 class="text-white fw-bold mb-1" style="font-size:1.6rem;">{{ $student->full_name }}</h2>
                <div class="d-flex gap-2 flex-wrap mb-1">
                    <span class="badge" style="background:rgba(255,255,255,0.2);color:#fff;font-family:monospace;">
                        {{ $student->admission_number }}
                    </span>
                    <span class="badge" style="background:#dcfce7;color:#166534;">{{ ucfirst($student->status) }}</span>
                    <span class="badge" style="background:rgba(255,255,255,0.15);color:#fff;">{{ $student->category }}</span>
                </div>
                <p class="mb-0" style="color:rgba(255,255,255,0.75);font-size:0.875rem;">
                    {{ $student->branch?->name ?? '—' }}
                    @if($student->branch?->course) &bull; {{ $student->branch->course->name }} @endif
                    @if($student->branch?->course?->stream) &bull; {{ $student->branch->course->stream->name }} @endif
                    &bull; Semester {{ $student->current_semester }}
                    &bull; {{ $student->academicYear?->name ?? '—' }}
                </p>
            </div>
            <div class="d-flex gap-2 ms-auto flex-wrap">
                <a href="{{ route('admin.admissions.receipt', $student) }}"
                   class="btn btn-sm" style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.3);">
                    <i class="fa-solid fa-receipt me-1"></i> Receipt
                </a>
                <a href="{{ route('admin.students.edit', $student) }}"
                   class="btn btn-sm btn-light" style="font-weight:500;">
                    <i class="fa-solid fa-pen me-1"></i> Edit
                </a>
                <a href="{{ route('admin.students.index') }}"
                   class="btn btn-sm" style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.3);">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div style="font-size:1.5rem;font-weight:800;color:#4f46e5;">?{{ number_format($student->total_fees_paid) }}</div>
            <div style="font-size:0.78rem;color:var(--muted);">Fees Paid</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div style="font-size:1.5rem;font-weight:800;color:#059669;">{{ $student->attendance_percentage }}%</div>
            <div style="font-size:0.78rem;color:var(--muted);">Attendance</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div style="font-size:1.5rem;font-weight:800;color:#d97706;">{{ $student->certificates->count() }}</div>
            <div style="font-size:0.78rem;color:var(--muted);">Certificates</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div style="font-size:1.5rem;font-weight:800;color:#7c3aed;">{{ $student->feePayments->count() }}</div>
            <div style="font-size:0.78rem;color:var(--muted);">Fee Transactions</div>
        </div>
    </div>
</div>

{{-- Tabs --}}
<ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist"
    style="border-color:var(--border);">
    <li class="nav-item">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-personal"
                style="font-size:0.875rem;">
            <i class="fa-solid fa-user me-1"></i> Personal
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-academic"
                style="font-size:0.875rem;">
            <i class="fa-solid fa-graduation-cap me-1"></i> Academic
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-guardian"
                style="font-size:0.875rem;">
            <i class="fa-solid fa-users me-1"></i> Guardian
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-certs"
                style="font-size:0.875rem;">
            <i class="fa-solid fa-file-check me-1"></i> Certificates
            <span class="badge ms-1" style="background:rgba(79,70,229,0.1);color:#4f46e5;font-size:0.7rem;">
                {{ $student->certificates->count() }}
            </span>
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-fees"
                style="font-size:0.875rem;">
            <i class="fa-solid fa-receipt me-1"></i> Fees
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-attendance"
                style="font-size:0.875rem;">
            <i class="fa-solid fa-calendar-check me-1"></i> Attendance
        </button>
    </li>
</ul>

<div class="tab-content" id="profileTabsContent">

    {{-- Personal Tab --}}
    <div class="tab-pane fade show active" id="tab-personal">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><i class="fa-solid fa-user me-2" style="color:#4f46e5;"></i>Personal Details</div>
                    <div class="card-body">
                        <dl class="row mb-0" style="font-size:0.875rem;">
                            <dt class="col-5" style="color:var(--muted);">Full Name</dt>
                            <dd class="col-7 mb-2">{{ $student->full_name }}</dd>
                            <dt class="col-5" style="color:var(--muted);">Date of Birth</dt>
                            <dd class="col-7 mb-2">{{ $student->date_of_birth?->format('d M Y') ?? '—' }}</dd>
                            <dt class="col-5" style="color:var(--muted);">Gender</dt>
                            <dd class="col-7 mb-2">{{ ucfirst($student->gender ?? '—') }}</dd>
                            <dt class="col-5" style="color:var(--muted);">Blood Group</dt>
                            <dd class="col-7 mb-2">{{ $student->blood_group ?? '—' }}</dd>
                            <dt class="col-5" style="color:var(--muted);">Aadhaar</dt>
                            <dd class="col-7 mb-2">{{ $student->aadhaar_number ? '****'.substr($student->aadhaar_number,-4) : '—' }}</dd>
                            <dt class="col-5" style="color:var(--muted);">Phone</dt>
                            <dd class="col-7 mb-2">{{ $student->phone ?? '—' }}</dd>
                            <dt class="col-5" style="color:var(--muted);">Email</dt>
                            <dd class="col-7 mb-2">{{ $student->email ?? '—' }}</dd>
                            <dt class="col-5" style="color:var(--muted);">Address</dt>
                            <dd class="col-7 mb-0">
                                {{ $student->address ?? '—' }}
                                @if($student->city), {{ $student->city }}@endif
                                @if($student->state), {{ $student->state }}@endif
                                @if($student->pincode) — {{ $student->pincode }}@endif
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><i class="fa-solid fa-id-card me-2" style="color:#d97706;"></i>Admission Info</div>
                    <div class="card-body">
                        <dl class="row mb-0" style="font-size:0.875rem;">
                            <dt class="col-5" style="color:var(--muted);">Adm. Number</dt>
                            <dd class="col-7 mb-2" style="font-family:monospace;color:#4f46e5;font-weight:600;">{{ $student->admission_number }}</dd>
                            <dt class="col-5" style="color:var(--muted);">Adm. Date</dt>
                            <dd class="col-7 mb-2">{{ $student->admission_date?->format('d M Y') ?? '—' }}</dd>
                            <dt class="col-5" style="color:var(--muted);">Category</dt>
                            <dd class="col-7 mb-2">
                                <span class="badge" style="background:rgba(79,70,229,0.1);color:#4f46e5;">{{ $student->category }}</span>
                            </dd>
                            <dt class="col-5" style="color:var(--muted);">Scholarship</dt>
                            <dd class="col-7 mb-2">
                                @if($student->scholarship_eligible)
                                    <span class="badge" style="background:#dcfce7;color:#166534;">Eligible</span>
                                @else
                                    <span style="color:var(--muted);">Not Eligible</span>
                                @endif
                            </dd>
                            <dt class="col-5" style="color:var(--muted);">Vehicle</dt>
                            <dd class="col-7 mb-2">
                                @if($student->vehicle_opted)
                                    <span class="badge" style="background:#dcfce7;color:#166534;"><i class="fa-solid fa-bus me-1"></i>Yes</span>
                                @else
                                    <span style="color:var(--muted);">No</span>
                                @endif
                            </dd>
                            <dt class="col-5" style="color:var(--muted);">Status</dt>
                            <dd class="col-7 mb-0">
                                <span class="badge" style="background:#dcfce7;color:#166534;">{{ ucfirst($student->status) }}</span>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Academic Tab --}}
    <div class="tab-pane fade" id="tab-academic">
        <div class="card">
            <div class="card-header"><i class="fa-solid fa-graduation-cap me-2" style="color:#059669;"></i>Academic Details</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <dl class="row mb-0" style="font-size:0.875rem;">
                            <dt class="col-5" style="color:var(--muted);">Branch</dt>
                            <dd class="col-7 mb-2">{{ $student->branch?->name ?? '—' }}</dd>
                            <dt class="col-5" style="color:var(--muted);">Course</dt>
                            <dd class="col-7 mb-2">{{ $student->branch?->course?->name ?? '—' }}</dd>
                            <dt class="col-5" style="color:var(--muted);">Stream</dt>
                            <dd class="col-7 mb-2">{{ $student->branch?->course?->stream?->name ?? '—' }}</dd>
                            <dt class="col-5" style="color:var(--muted);">Academic Year</dt>
                            <dd class="col-7 mb-2">{{ $student->academicYear?->name ?? '—' }}</dd>
                            <dt class="col-5" style="color:var(--muted);">Semester</dt>
                            <dd class="col-7 mb-2">Semester {{ $student->current_semester }}</dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl class="row mb-0" style="font-size:0.875rem;">
                            <dt class="col-5" style="color:var(--muted);">10th Marks</dt>
                            <dd class="col-7 mb-2">{{ $student->marks_10th ? $student->marks_10th.'%' : '—' }}</dd>
                            <dt class="col-5" style="color:var(--muted);">12th Marks</dt>
                            <dd class="col-7 mb-2">{{ $student->marks_12th ? $student->marks_12th.'%' : '—' }}</dd>
                            <dt class="col-5" style="color:var(--muted);">Prev. Institution</dt>
                            <dd class="col-7 mb-2">{{ $student->previous_institution ?? '—' }}</dd>
                            <dt class="col-5" style="color:var(--muted);">Univ. Reg. No</dt>
                            <dd class="col-7 mb-2">{{ $student->university_reg_number ?? '—' }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Guardian Tab --}}
    <div class="tab-pane fade" id="tab-guardian">
        @if($student->guardian)
        <div class="card">
            <div class="card-header"><i class="fa-solid fa-users me-2" style="color:#7c3aed;"></i>Guardian Details</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <h6 class="section-title">Father</h6>
                        <dl class="row mb-0" style="font-size:0.875rem;">
                            <dt class="col-5" style="color:var(--muted);">Name</dt>
                            <dd class="col-7 mb-2">{{ $student->guardian->father_name ?? '—' }}</dd>
                            <dt class="col-5" style="color:var(--muted);">Phone</dt>
                            <dd class="col-7 mb-2">{{ $student->guardian->father_phone ?? '—' }}</dd>
                            <dt class="col-5" style="color:var(--muted);">Occupation</dt>
                            <dd class="col-7 mb-2">{{ $student->guardian->father_occupation ?? '—' }}</dd>
                            <dt class="col-5" style="color:var(--muted);">Email</dt>
                            <dd class="col-7 mb-0">{{ $student->guardian->father_email ?? '—' }}</dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <h6 class="section-title">Mother</h6>
                        <dl class="row mb-0" style="font-size:0.875rem;">
                            <dt class="col-5" style="color:var(--muted);">Name</dt>
                            <dd class="col-7 mb-2">{{ $student->guardian->mother_name ?? '—' }}</dd>
                            <dt class="col-5" style="color:var(--muted);">Phone</dt>
                            <dd class="col-7 mb-2">{{ $student->guardian->mother_phone ?? '—' }}</dd>
                            <dt class="col-5" style="color:var(--muted);">Occupation</dt>
                            <dd class="col-7 mb-0">{{ $student->guardian->mother_occupation ?? '—' }}</dd>
                        </dl>
                    </div>
                    <div class="col-12">
                        <h6 class="section-title">Financial</h6>
                        <dl class="row mb-0" style="font-size:0.875rem;">
                            <dt class="col-4" style="color:var(--muted);">Annual Income</dt>
                            <dd class="col-8 mb-2">{{ $student->guardian->annual_income ? '?'.number_format($student->guardian->annual_income) : '—' }}</dd>
                            <dt class="col-4" style="color:var(--muted);">Scholarship</dt>
                            <dd class="col-8 mb-0">
                                @if($student->guardian->scholarship_eligible)
                                    <span class="badge" style="background:#dcfce7;color:#166534;">Eligible</span>
                                    @if($student->guardian->scholarship_details)
                                        <span style="font-size:0.8rem;color:var(--muted);margin-left:0.5rem;">{{ $student->guardian->scholarship_details }}</span>
                                    @endif
                                @else
                                    <span style="color:var(--muted);">Not Eligible</span>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="empty-state">
            <i class="fa-solid fa-users"></i>
            <h5 style="color:var(--muted);">No guardian details recorded</h5>
            <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-primary btn-sm mt-2">
                <i class="fa-solid fa-plus me-1"></i> Add Guardian Details
            </a>
        </div>
        @endif
    </div>

    {{-- Certificates Tab --}}
    <div class="tab-pane fade" id="tab-certs">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="fa-solid fa-file-check me-2" style="color:#d97706;"></i>Certificates</span>
                <button type="button" class="btn btn-sm btn-outline-primary"
                        data-bs-toggle="modal" data-bs-target="#uploadCertModal">
                    <i class="fa-solid fa-upload me-1"></i> Upload
                </button>
            </div>
            <div class="card-body">
                @if($student->certificates->isEmpty())
                    <div class="empty-state py-3">
                        <i class="fa-solid fa-file-circle-xmark d-block mb-2" style="font-size:2rem;"></i>
                        <p class="mb-0 small">No certificates uploaded</p>
                    </div>
                @else
                    <div class="row g-2">
                        @foreach($student->certificates as $cert)
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-2 p-2 rounded"
                                 style="background:rgba(5,150,105,0.05);border:1px solid rgba(5,150,105,0.15);">
                                <div class="rounded d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width:32px;height:32px;background:rgba(5,150,105,0.1);">
                                    <i class="fa-solid fa-{{ $cert->is_pdf ? 'file-pdf' : 'file-image' }}"
                                       style="color:#059669;font-size:0.85rem;"></i>
                                </div>
                                <div class="flex-1 overflow-hidden">
                                    <div style="font-size:0.82rem;font-weight:500;">{{ $cert->certificate_label }}</div>
                                    <div style="font-size:0.72rem;color:var(--muted);">{{ $cert->file_size_human }}</div>
                                </div>
                                <a href="{{ $cert->file_url }}" target="_blank"
                                   class="btn btn-sm btn-outline-primary"
                                   style="padding:0.2rem 0.4rem;font-size:0.72rem;">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Fees Tab --}}
    <div class="tab-pane fade" id="tab-fees">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="fa-solid fa-receipt me-2" style="color:#7c3aed;"></i>Fee Payments</span>
                <a href="{{ route('admin.fees.payments.create') }}?student_id={{ $student->id }}"
                   class="btn btn-sm btn-primary">
                    <i class="fa-solid fa-plus me-1"></i> Collect Fee
                </a>
            </div>
            @if($student->feePayments->isEmpty())
                <div class="empty-state py-4">
                    <i class="fa-solid fa-receipt d-block mb-2"></i>
                    <p class="mb-0 small">No fee payments recorded</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table mb-0" style="font-size:0.82rem;">
                        <thead>
                            <tr><th>Receipt</th><th>Fee Type</th><th>Amount</th><th>Date</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            @foreach($student->feePayments as $payment)
                            <tr>
                                <td><a href="{{ route('admin.fees.receipt', $payment) }}" style="color:#4f46e5;font-family:monospace;">{{ $payment->receipt_number }}</a></td>
                                <td>{{ $payment->feeType?->name ?? '—' }}</td>
                                <td style="font-weight:600;color:#059669;">?{{ number_format($payment->amount_paid) }}</td>
                                <td style="color:var(--muted);">{{ $payment->payment_date?->format('d M Y') }}</td>
                                <td>
                                    @if($payment->status === 'paid')
                                        <span class="badge" style="background:#dcfce7;color:#166534;">Paid</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($payment->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="background:var(--bg);">
                                <td colspan="2" style="font-weight:600;">Total Paid</td>
                                <td style="font-weight:700;color:#059669;">?{{ number_format($student->total_fees_paid) }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Attendance Tab --}}
    <div class="tab-pane fade" id="tab-attendance">
        <div class="card">
            <div class="card-header"><i class="fa-solid fa-calendar-check me-2" style="color:#0891b2;"></i>Attendance Summary</div>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-4 text-center">
                        <div style="font-size:2.5rem;font-weight:800;color:#4f46e5;">{{ $student->attendance_percentage }}%</div>
                        <div style="font-size:0.82rem;color:var(--muted);">Overall Attendance</div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div style="font-size:2.5rem;font-weight:800;color:#059669;">{{ $student->attendance->where('status','present')->count() }}</div>
                        <div style="font-size:0.82rem;color:var(--muted);">Days Present</div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div style="font-size:2.5rem;font-weight:800;color:#dc2626;">{{ $student->attendance->where('status','absent')->count() }}</div>
                        <div style="font-size:0.82rem;color:var(--muted);">Days Absent</div>
                    </div>
                </div>
                @if($student->attendance->isEmpty())
                    <div class="empty-state py-3">
                        <i class="fa-solid fa-calendar-xmark d-block mb-2" style="font-size:2rem;"></i>
                        <p class="mb-0 small">No attendance records yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

{{-- Certificate Upload Modal --}}
<div class="modal fade" id="uploadCertModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:0.75rem;background:var(--surface);">
            <div class="modal-header" style="border-color:var(--border);">
                <h5 class="modal-title" style="font-size:1rem;font-weight:600;">
                    <i class="fa-solid fa-upload me-2" style="color:#4f46e5;"></i>Upload Certificate
                </h5>
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

