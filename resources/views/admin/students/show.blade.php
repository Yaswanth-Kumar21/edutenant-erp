@extends('layouts.app')

@section('title', $student->full_name)

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.students.index') }}" style="color:#4f46e5;text-decoration:none;">Students</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">{{ $student->full_name }}</li>
@endsection

@section('content')

{{-- ── Profile Header ───────────────────────────────────────────────────── --}}
<div class="card mb-4 border-0"
     style="background:linear-gradient(135deg,#4f46e5,#7c3aed);border-radius:1rem;overflow:hidden;">
    <div class="card-body p-4">
        <div class="d-flex align-items-center gap-4 flex-wrap">
            <img src="{{ $student->photo_url }}"
                 alt="{{ $student->full_name }}"
                 class="rounded-circle"
                 style="width:90px;height:90px;object-fit:cover;border:3px solid rgba(255,255,255,0.4);">
            <div class="flex-1">
                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                    <h2 class="text-white fw-bold mb-0" style="font-size:1.5rem;">
                        {{ $student->full_name }}
                    </h2>
                    <span class="badge"
                          style="background:rgba(255,255,255,0.2);color:#fff;font-size:0.8rem;font-family:monospace;">
                        {{ $student->admission_number }}
                    </span>
                    @switch($student->status)
                        @case('active')
                            <span class="badge" style="background:#dcfce7;color:#166534;">Active</span>
                            @break
                        @case('inactive')
                            <span class="badge" style="background:#fee2e2;color:#991b1b;">Inactive</span>
                            @break
                        @default
                            <span class="badge bg-secondary">{{ ucfirst($student->status) }}</span>
                    @endswitch
                </div>
                <p class="mb-0" style="color:rgba(255,255,255,0.8);font-size:0.9rem;">
                    {{ $student->branch?->name ?? '—' }}
                    @if($student->branch?->course)
                        &bull; {{ $student->branch->course->name }}
                    @endif
                    @if($student->branch?->course?->stream)
                        &bull; {{ $student->branch->course->stream->name }}
                    @endif
                    &bull; Semester {{ $student->current_semester ?? '—' }}
                    &bull; {{ $student->category }}
                </p>
                @if($student->phone || $student->email)
                <p class="mb-0 mt-1" style="color:rgba(255,255,255,0.65);font-size:0.82rem;">
                    @if($student->phone)
                        <i class="fa-solid fa-phone me-1"></i>{{ $student->phone }}
                    @endif
                    @if($student->email)
                        &nbsp;&bull;&nbsp;<i class="fa-solid fa-envelope me-1"></i>{{ $student->email }}
                    @endif
                </p>
                @endif
            </div>
            <div class="d-flex gap-2 ms-auto flex-wrap">
                <a href="{{ route('admin.pdf.student-report', $student) }}"
                   class="btn btn-sm"
                   style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.3);"
                   title="Download PDF Report">
                    <i class="fa-solid fa-file-pdf me-1"></i> PDF Report
                </a>
                <a href="{{ route('admin.admissions.receipt', $student) }}"
                   class="btn btn-sm"
                   style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.3);">
                    <i class="fa-solid fa-receipt me-1"></i> Receipt
                </a>
                @if(!$student->user_id)
                <form method="POST" action="{{ route('admin.students.create-login', $student) }}"
                      onsubmit="return confirm('Create a login account for this student?\n\nEmail: {{ $student->email ?: 'No email — cannot create login' }}\nDefault Password: {{ $student->phone ?? $student->admission_number }}')">
                    @csrf
                    <button type="submit"
                            class="btn btn-sm"
                            style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.3);"
                            {{ empty($student->email) ? 'disabled title=No email address' : '' }}>
                        <i class="fa-solid fa-key me-1"></i> Create Login
                    </button>
                </form>
                @else
                <span class="btn btn-sm"
                      style="background:rgba(5,150,105,0.3);color:#fff;border:1px solid rgba(5,150,105,0.4);cursor:default;"
                      title="Login account exists — Email: {{ $student->user?->email }}">
                    <i class="fa-solid fa-circle-check me-1"></i> Login Active
                </span>
                @endif
                <a href="{{ route('admin.students.edit', $student) }}"
                   class="btn btn-sm btn-light fw-500" style="font-weight:500;">
                    <i class="fa-solid fa-pen me-1"></i> Edit
                </a>
                <a href="{{ route('admin.students.index') }}"
                   class="btn btn-sm"
                   style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.3);">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ── Summary Cards ────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div style="font-size:1.5rem;font-weight:800;color:#4f46e5;">
                ₹{{ number_format($student->total_fees_paid) }}
            </div>
            <div style="font-size:0.78rem;color:var(--muted);margin-top:4px;">Total Fees Paid</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div style="font-size:1.5rem;font-weight:800;color:#059669;">
                {{ $student->attendance_percentage }}%
            </div>
            <div style="font-size:0.78rem;color:var(--muted);margin-top:4px;">Attendance</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div style="font-size:1.5rem;font-weight:800;color:#d97706;">
                {{ $student->certificates->count() }}
            </div>
            <div style="font-size:0.78rem;color:var(--muted);margin-top:4px;">Certificates</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div style="font-size:1.5rem;font-weight:800;color:#7c3aed;">
                {{ $student->admission_date?->format('Y') ?? '—' }}
            </div>
            <div style="font-size:0.78rem;color:var(--muted);margin-top:4px;">Admission Year</div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- ── Personal Info ──────────────────────────────────────────────────── --}}
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <i class="fa-solid fa-user me-2" style="color:#4f46e5;"></i>
                Personal Information
            </div>
            <div class="card-body">
                <dl class="row mb-0" style="font-size:0.875rem;">
                    <dt class="col-5" style="color:var(--muted);font-weight:500;">Father's Name</dt>
                    <dd class="col-7 mb-2">{{ $student->guardian?->father_name ?? $student->father_name ?? '—' }}</dd>

                    <dt class="col-5" style="color:var(--muted);font-weight:500;">Mother's Name</dt>
                    <dd class="col-7 mb-2">{{ $student->guardian?->mother_name ?? $student->mother_name ?? '—' }}</dd>

                    <dt class="col-5" style="color:var(--muted);font-weight:500;">Date of Birth</dt>
                    <dd class="col-7 mb-2">{{ $student->date_of_birth?->format('d M Y') ?? '—' }}</dd>

                    <dt class="col-5" style="color:var(--muted);font-weight:500;">Gender</dt>
                    <dd class="col-7 mb-2">{{ ucfirst($student->gender ?? '—') }}</dd>

                    <dt class="col-5" style="color:var(--muted);font-weight:500;">Blood Group</dt>
                    <dd class="col-7 mb-2">{{ $student->blood_group ?? '—' }}</dd>

                    <dt class="col-5" style="color:var(--muted);font-weight:500;">Aadhaar</dt>
                    <dd class="col-7 mb-2">{{ $student->aadhaar_number ? '****' . substr($student->aadhaar_number, -4) : '—' }}</dd>

                    <dt class="col-5" style="color:var(--muted);font-weight:500;">Phone</dt>
                    <dd class="col-7 mb-2">{{ $student->phone ?? '—' }}</dd>

                    <dt class="col-5" style="color:var(--muted);font-weight:500;">Email</dt>
                    <dd class="col-7 mb-2">{{ $student->email ?? '—' }}</dd>

                    <dt class="col-5" style="color:var(--muted);font-weight:500;">Address</dt>
                    <dd class="col-7 mb-2">
                        {{ $student->address ?? '—' }}
                        @if($student->city) , {{ $student->city }} @endif
                        @if($student->state) , {{ $student->state }} @endif
                        @if($student->pincode) — {{ $student->pincode }} @endif
                    </dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- ── Academic Info ──────────────────────────────────────────────────── --}}
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <i class="fa-solid fa-graduation-cap me-2" style="color:#059669;"></i>
                Academic Information
            </div>
            <div class="card-body">
                <dl class="row mb-0" style="font-size:0.875rem;">
                    <dt class="col-5" style="color:var(--muted);font-weight:500;">Branch</dt>
                    <dd class="col-7 mb-2">{{ $student->branch?->name ?? '—' }}</dd>

                    <dt class="col-5" style="color:var(--muted);font-weight:500;">Course</dt>
                    <dd class="col-7 mb-2">{{ $student->branch?->course?->name ?? '—' }}</dd>

                    <dt class="col-5" style="color:var(--muted);font-weight:500;">Stream</dt>
                    <dd class="col-7 mb-2">{{ $student->branch?->course?->stream?->name ?? '—' }}</dd>

                    <dt class="col-5" style="color:var(--muted);font-weight:500;">Academic Year</dt>
                    <dd class="col-7 mb-2">{{ $student->academicYear?->name ?? '—' }}</dd>

                    <dt class="col-5" style="color:var(--muted);font-weight:500;">Semester</dt>
                    <dd class="col-7 mb-2">Semester {{ $student->current_semester ?? '—' }}</dd>

                    <dt class="col-5" style="color:var(--muted);font-weight:500;">Category</dt>
                    <dd class="col-7 mb-2">
                        <span class="badge" style="background:rgba(79,70,229,0.1);color:#4f46e5;">
                            {{ $student->category }}
                        </span>
                    </dd>

                    <dt class="col-5" style="color:var(--muted);font-weight:500;">10th Marks</dt>
                    <dd class="col-7 mb-2">{{ $student->marks_10th ? $student->marks_10th.'%' : '—' }}</dd>

                    <dt class="col-5" style="color:var(--muted);font-weight:500;">12th Marks</dt>
                    <dd class="col-7 mb-2">{{ $student->marks_12th ? $student->marks_12th.'%' : '—' }}</dd>

                    <dt class="col-5" style="color:var(--muted);font-weight:500;">Prev. Institution</dt>
                    <dd class="col-7 mb-2">{{ $student->previous_institution ?? '—' }}</dd>

                    <dt class="col-5" style="color:var(--muted);font-weight:500;">Admission Date</dt>
                    <dd class="col-7 mb-2">{{ $student->admission_date?->format('d M Y') ?? '—' }}</dd>

                    <dt class="col-5" style="color:var(--muted);font-weight:500;">Univ. Reg. No</dt>
                    <dd class="col-7 mb-2">{{ $student->university_reg_number ?? '—' }}</dd>

                    <dt class="col-5" style="color:var(--muted);font-weight:500;">Scholarship</dt>
                    <dd class="col-7 mb-2">
                        @if($student->scholarship_eligible)
                            <span class="badge" style="background:#dcfce7;color:#166534;">Eligible</span>
                        @else
                            <span style="color:var(--muted);">Not Eligible</span>
                        @endif
                    </dd>

                    <dt class="col-5" style="color:var(--muted);font-weight:500;">Vehicle</dt>
                    <dd class="col-7 mb-0">
                        @if($student->vehicle_opted)
                            <span class="badge" style="background:#dcfce7;color:#166534;">
                                <i class="fa-solid fa-bus me-1"></i>Yes
                            </span>
                        @else
                            <span style="color:var(--muted);">No</span>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- ── Guardian Details ───────────────────────────────────────────────── --}}
    @if($student->guardian)
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <i class="fa-solid fa-users me-2" style="color:#7c3aed;"></i>
                Guardian Details
            </div>
            <div class="card-body">
                <dl class="row mb-0" style="font-size:0.875rem;">
                    <dt class="col-5" style="color:var(--muted);font-weight:500;">Father</dt>
                    <dd class="col-7 mb-2">{{ $student->guardian->father_name ?? '—' }}</dd>

                    <dt class="col-5" style="color:var(--muted);font-weight:500;">Father's Phone</dt>
                    <dd class="col-7 mb-2">{{ $student->guardian->father_phone ?? '—' }}</dd>

                    <dt class="col-5" style="color:var(--muted);font-weight:500;">Father's Occupation</dt>
                    <dd class="col-7 mb-2">{{ $student->guardian->father_occupation ?? '—' }}</dd>

                    <dt class="col-5" style="color:var(--muted);font-weight:500;">Mother</dt>
                    <dd class="col-7 mb-2">{{ $student->guardian->mother_name ?? '—' }}</dd>

                    <dt class="col-5" style="color:var(--muted);font-weight:500;">Mother's Phone</dt>
                    <dd class="col-7 mb-2">{{ $student->guardian->mother_phone ?? '—' }}</dd>

                    <dt class="col-5" style="color:var(--muted);font-weight:500;">Annual Income</dt>
                    <dd class="col-7 mb-2">
                        {{ $student->guardian->annual_income ? '₹'.number_format($student->guardian->annual_income) : '—' }}
                    </dd>

                    <dt class="col-5" style="color:var(--muted);font-weight:500;">Scholarship</dt>
                    <dd class="col-7 mb-0">
                        @if($student->guardian->scholarship_eligible)
                            <span class="badge" style="background:#dcfce7;color:#166534;">Eligible</span>
                            @if($student->guardian->scholarship_details)
                                <div style="font-size:0.78rem;color:var(--muted);margin-top:0.25rem;">
                                    {{ $student->guardian->scholarship_details }}
                                </div>
                            @endif
                        @else
                            <span style="color:var(--muted);">Not Eligible</span>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Certificates ───────────────────────────────────────────────────── --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span>
                    <i class="fa-solid fa-file-check me-2" style="color:#d97706;"></i>
                    Certificates
                </span>
                <button type="button" class="btn btn-sm btn-outline-primary"
                        data-bs-toggle="modal" data-bs-target="#uploadCertModal"
                        style="font-size:0.78rem;">
                    <i class="fa-solid fa-upload me-1"></i> Upload
                </button>
            </div>
            <div class="card-body">
                @if($student->certificates->isEmpty())
                    <div class="empty-state py-3">
                        <i class="fa-solid fa-file-circle-xmark d-block mb-2" style="font-size:2rem;"></i>
                        <p class="mb-0 small">No certificates uploaded yet</p>
                    </div>
                @else
                    <div class="row g-2">
                        @foreach($student->certificates as $cert)
                        <div class="col-12">
                            <div class="d-flex align-items-center gap-2 p-2 rounded"
                                 style="background:rgba(5,150,105,0.05);border:1px solid rgba(5,150,105,0.15);">
                                <div class="rounded d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width:32px;height:32px;background:rgba(5,150,105,0.1);">
                                    <i class="fa-solid fa-{{ $cert->is_pdf ? 'file-pdf' : 'file-image' }}"
                                       style="color:#059669;font-size:0.85rem;"></i>
                                </div>
                                <div class="flex-1 overflow-hidden">
                                    <div style="font-size:0.82rem;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                        {{ $cert->certificate_label }}
                                    </div>
                                    <div style="font-size:0.72rem;color:var(--muted);">
                                        {{ $cert->original_filename }} &bull; {{ $cert->file_size_human }}
                                        @if($cert->is_verified)
                                            &bull; <span style="color:#059669;">Verified</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex gap-1">
                                    <a href="{{ $cert->file_url }}" target="_blank"
                                       class="btn btn-sm btn-outline-primary"
                                       style="padding:0.2rem 0.4rem;font-size:0.72rem;"
                                       title="View">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <form method="POST"
                                          action="{{ route('admin.admissions.certificates.delete', $cert) }}"
                                          id="del-cert-{{ $cert->id }}">
                                        @csrf @method('DELETE')
                                    </form>
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            style="padding:0.2rem 0.4rem;font-size:0.72rem;"
                                            data-confirm-delete="del-cert-{{ $cert->id }}"
                                            data-name="{{ $cert->certificate_label }}"
                                            title="Remove">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Fee Payments ───────────────────────────────────────────────────── --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span>
                    <i class="fa-solid fa-receipt me-2" style="color:#7c3aed;"></i>
                    Fee Payments
                </span>
                <a href="{{ route('admin.fees.payments.create') }}?student_id={{ $student->id }}"
                   class="btn btn-sm btn-primary" style="font-size:0.78rem;">
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
                            <tr>
                                <th>Receipt No</th>
                                <th>Fee Type</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Mode</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($student->feePayments as $payment)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.fees.receipt', $payment) }}"
                                       style="color:#4f46e5;text-decoration:none;font-family:monospace;">
                                        {{ $payment->receipt_number }}
                                    </a>
                                </td>
                                <td>{{ $payment->feeType?->name ?? '—' }}</td>
                                <td style="font-weight:600;color:#059669;">
                                    ₹{{ number_format($payment->amount_paid) }}
                                </td>
                                <td style="color:var(--muted);">
                                    {{ $payment->payment_date?->format('d M Y') }}
                                </td>
                                <td>{{ ucfirst($payment->payment_mode ?? '—') }}</td>
                                <td>
                                    @if($payment->status === 'paid')
                                        <span class="badge" style="background:#dcfce7;color:#166534;">Paid</span>
                                    @elseif($payment->status === 'partial')
                                        <span class="badge" style="background:#fef3c7;color:#92400e;">Partial</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($payment->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="background:var(--bg);">
                                <td colspan="2" style="font-weight:600;padding:0.75rem 1rem;">Total Paid</td>
                                <td style="font-weight:700;color:#059669;padding:0.75rem 1rem;">
                                    ₹{{ number_format($student->total_fees_paid) }}
                                </td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- ── Certificate Upload Modal ─────────────────────────────────────────── --}}
<div class="modal fade" id="uploadCertModal" tabindex="-1" aria-labelledby="uploadCertModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:0.75rem;background:var(--surface);">
            <div class="modal-header" style="border-color:var(--border);">
                <h5 class="modal-title" id="uploadCertModalLabel" style="font-size:1rem;font-weight:600;">
                    <i class="fa-solid fa-upload me-2" style="color:#4f46e5;"></i>
                    Upload Certificate
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST"
                  action="{{ route('admin.admissions.certificates.upload', $student) }}"
                  enctype="multipart/form-data">
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
                        <input type="file" name="certificate_file" class="form-control"
                               accept=".jpg,.jpeg,.png,.pdf" required>
                        <div class="form-text">JPG, PNG, or PDF. Max 5MB.</div>
                    </div>
                </div>
                <div class="modal-footer" style="border-color:var(--border);">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-upload me-2"></i> Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

