@extends('layouts.app')

@section('title', 'Admission Receipt — ' . $student->admission_number)

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.students.index') }}" style="color:#4f46e5;text-decoration:none;">Students</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.students.show', $student) }}" style="color:#4f46e5;text-decoration:none;">
            {{ $student->full_name }}
        </a>
    </li>
    <li class="breadcrumb-item active">Admission Receipt</li>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fa-solid fa-receipt me-2" style="color:#4f46e5;"></i>
            Admission Receipt
        </h1>
        <p class="mb-0" style="color:var(--muted);font-size:0.875rem;">
            {{ $student->admission_number }} &bull; {{ $student->full_name }}
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.pdf.admission-receipt', $student) }}"
           class="btn btn-outline-danger">
            <i class="fa-solid fa-file-pdf me-2"></i> Download PDF
        </a>
        <a href="{{ route('admin.admissions.receipt.print', $student) }}"
           target="_blank"
           class="btn btn-outline-primary">
            <i class="fa-solid fa-print me-2"></i> Print
        </a>
        @if($student->email || $student->user?->email)
        <form method="POST" action="{{ route('admin.notifications.send.admission', $student) }}">
            @csrf
            <button type="submit" class="btn btn-outline-success">
                <i class="fa-solid fa-envelope me-2"></i> Email Student
            </button>
        </form>
        @endif
        <a href="{{ route('admin.students.show', $student) }}"
           class="btn btn-outline-secondary">
            <i class="fa-solid fa-user me-2"></i> Student Profile
        </a>
        <a href="{{ route('admin.admissions.create') }}"
           class="btn btn-primary">
            <i class="fa-solid fa-user-plus me-2"></i> New Admission
        </a>
    </div>
</div>

{{-- Success Banner --}}
@if(session('success'))
<div class="alert d-flex align-items-center gap-3 mb-4"
     style="background:linear-gradient(135deg,rgba(5,150,105,0.1),rgba(16,185,129,0.05));
            border:1px solid rgba(5,150,105,0.3);border-radius:0.75rem;">
    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
         style="width:44px;height:44px;background:rgba(5,150,105,0.15);">
        <i class="fa-solid fa-circle-check" style="color:#059669;font-size:1.25rem;"></i>
    </div>
    <div>
        <div style="font-weight:600;color:#065f46;">Admission Successful!</div>
        <div style="font-size:0.875rem;color:#047857;">{{ session('success') }}</div>
    </div>
</div>
@endif

{{-- Student Login Credentials Card --}}
@if($student->user)
<div class="card mb-4 border-0"
     style="background:linear-gradient(135deg,rgba(79,70,229,0.08),rgba(124,58,237,0.05));
            border:1px solid rgba(79,70,229,0.2) !important;border-radius:0.75rem;">
    <div class="card-body p-4">
        <div class="d-flex align-items-start gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width:44px;height:44px;background:rgba(79,70,229,0.15);">
                <i class="fa-solid fa-key" style="color:#4f46e5;font-size:1.1rem;"></i>
            </div>
            <div class="flex-1">
                <div style="font-weight:700;color:#3730a3;font-size:1rem;margin-bottom:0.25rem;">
                    Student Login Credentials Created
                </div>
                <div style="font-size:0.85rem;color:#4338ca;margin-bottom:1rem;">
                    Share these credentials with the student. They can log in to view their dashboard, fees, and attendance.
                </div>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="p-3 rounded" style="background:rgba(255,255,255,0.7);border:1px solid rgba(79,70,229,0.15);">
                            <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#6366f1;margin-bottom:0.4rem;">
                                Login URL
                            </div>
                            <div style="font-family:monospace;font-size:0.85rem;color:#1e1b4b;word-break:break-all;">
                                {{ url('/login') }}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="p-3 rounded" style="background:rgba(255,255,255,0.7);border:1px solid rgba(79,70,229,0.15);">
                            <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#6366f1;margin-bottom:0.4rem;">
                                Email
                            </div>
                            <div style="font-family:monospace;font-size:0.85rem;color:#1e1b4b;word-break:break-all;">
                                {{ $student->user->email }}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="p-3 rounded" style="background:rgba(255,255,255,0.7);border:1px solid rgba(79,70,229,0.15);">
                            <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#6366f1;margin-bottom:0.4rem;">
                                Default Password
                            </div>
                            <div style="font-family:monospace;font-size:0.85rem;color:#1e1b4b;">
                                {{ $student->phone ?? $student->admission_number }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-3 d-flex align-items-center gap-2"
                     style="font-size:0.8rem;color:#6366f1;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    Default password is the student's phone number. Ask them to change it after first login.
                </div>
            </div>
        </div>
    </div>
</div>
@else
    @if($student->email)
    <div class="alert mb-4"
         style="background:rgba(251,191,36,0.1);border:1px solid rgba(251,191,36,0.3);border-radius:0.75rem;">
        <i class="fa-solid fa-triangle-exclamation me-2" style="color:#d97706;"></i>
        <span style="color:#92400e;font-size:0.875rem;">
            Student login account could not be created automatically. Please create it manually from the
            <a href="{{ route('admin.students.show', $student) }}" style="color:#4f46e5;">student profile</a>.
        </span>
    </div>
    @else
    <div class="alert mb-4"
         style="background:rgba(107,114,128,0.08);border:1px solid rgba(107,114,128,0.2);border-radius:0.75rem;">
        <i class="fa-solid fa-info-circle me-2" style="color:#6b7280;"></i>
        <span style="color:#374151;font-size:0.875rem;">
            No email provided — student login account was not created.
            You can add an email and create login credentials from the
            <a href="{{ route('admin.students.edit', $student) }}" style="color:#4f46e5;">student edit page</a>.
        </span>
    </div>
    @endif
@endif

{{-- Receipt Card --}}
<div class="card" style="max-width:800px;margin:0 auto;">
    {{-- Receipt Header --}}
    <div class="card-body p-0">
        <div style="background:linear-gradient(135deg,#4f46e5,#7c3aed);padding:2rem;border-radius:0.75rem 0.75rem 0 0;">
            <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
                <div>
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="rounded-2 d-flex align-items-center justify-content-center"
                             style="width:48px;height:48px;background:rgba(255,255,255,0.2);">
                            <i class="fa-solid fa-graduation-cap text-white" style="font-size:1.4rem;"></i>
                        </div>
                        <div>
                            <div class="text-white fw-bold" style="font-size:1.1rem;">
                                {{ $tenant->name ?? 'EduTenant ERP' }}
                            </div>
                            <div style="color:rgba(255,255,255,0.7);font-size:0.8rem;">
                                {{ $tenant->address ?? '' }}
                                @if($tenant->city) &bull; {{ $tenant->city }} @endif
                            </div>
                        </div>
                    </div>
                    <div style="color:rgba(255,255,255,0.6);font-size:0.78rem;">
                        @if($tenant->affiliation_number)
                            Affiliation No: {{ $tenant->affiliation_number }}
                        @endif
                    </div>
                </div>
                <div class="text-end">
                    <div class="badge mb-1"
                         style="background:rgba(255,255,255,0.2);color:#fff;font-size:0.9rem;padding:0.5em 1em;font-family:monospace;">
                        {{ $receipt?->receipt_number ?? 'PENDING' }}
                    </div>
                    <div style="color:rgba(255,255,255,0.7);font-size:0.78rem;">Admission Receipt</div>
                    <div style="color:rgba(255,255,255,0.6);font-size:0.75rem;margin-top:0.25rem;">
                        {{ $receipt?->payment_date?->format('d M Y') ?? now()->format('d M Y') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="p-4">
            {{-- Student Info --}}
            <div class="row g-3 mb-4">
                <div class="col-md-8">
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ $student->photo_url }}"
                             alt="{{ $student->full_name }}"
                             class="rounded-circle"
                             style="width:64px;height:64px;object-fit:cover;border:2px solid var(--border);">
                        <div>
                            <h5 class="mb-1" style="font-weight:700;">{{ $student->full_name }}</h5>
                            <div style="font-size:0.85rem;color:var(--muted);">
                                <span class="badge me-1"
                                      style="background:rgba(79,70,229,0.1);color:#4f46e5;font-family:monospace;">
                                    {{ $student->admission_number }}
                                </span>
                                <span class="badge"
                                      style="background:rgba(5,150,105,0.1);color:#059669;">
                                    {{ $student->status === 'active' ? 'Active' : ucfirst($student->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <div style="font-size:0.82rem;color:var(--muted);">Admission Date</div>
                    <div style="font-weight:600;">{{ $student->admission_date?->format('d M Y') }}</div>
                    <div style="font-size:0.82rem;color:var(--muted);margin-top:0.5rem;">Academic Year</div>
                    <div style="font-weight:600;">{{ $student->academicYear?->name ?? '—' }}</div>
                </div>
            </div>

            {{-- Details Grid --}}
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="p-3 rounded" style="background:var(--bg);border:1px solid var(--border);">
                        <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:var(--muted);margin-bottom:0.75rem;">
                            Academic Details
                        </div>
                        <dl class="row mb-0" style="font-size:0.82rem;">
                            <dt class="col-5" style="color:var(--muted);font-weight:500;">Branch</dt>
                            <dd class="col-7 mb-1">{{ $student->branch?->name ?? '—' }}</dd>
                            <dt class="col-5" style="color:var(--muted);font-weight:500;">Course</dt>
                            <dd class="col-7 mb-1">{{ $student->branch?->course?->name ?? '—' }}</dd>
                            <dt class="col-5" style="color:var(--muted);font-weight:500;">Stream</dt>
                            <dd class="col-7 mb-1">{{ $student->branch?->course?->stream?->name ?? '—' }}</dd>
                            <dt class="col-5" style="color:var(--muted);font-weight:500;">Semester</dt>
                            <dd class="col-7 mb-1">Semester {{ $student->current_semester }}</dd>
                            <dt class="col-5" style="color:var(--muted);font-weight:500;">Category</dt>
                            <dd class="col-7 mb-0">
                                <span class="badge" style="background:rgba(79,70,229,0.1);color:#4f46e5;">
                                    {{ $student->category }}
                                </span>
                            </dd>
                        </dl>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 rounded" style="background:var(--bg);border:1px solid var(--border);">
                        <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:var(--muted);margin-bottom:0.75rem;">
                            Personal Details
                        </div>
                        <dl class="row mb-0" style="font-size:0.82rem;">
                            <dt class="col-5" style="color:var(--muted);font-weight:500;">Father</dt>
                            <dd class="col-7 mb-1">{{ $student->guardian?->father_name ?? $student->father_name ?? '—' }}</dd>
                            <dt class="col-5" style="color:var(--muted);font-weight:500;">Phone</dt>
                            <dd class="col-7 mb-1">{{ $student->phone ?? '—' }}</dd>
                            <dt class="col-5" style="color:var(--muted);font-weight:500;">Gender</dt>
                            <dd class="col-7 mb-1">{{ ucfirst($student->gender ?? '—') }}</dd>
                            <dt class="col-5" style="color:var(--muted);font-weight:500;">DOB</dt>
                            <dd class="col-7 mb-1">{{ $student->date_of_birth?->format('d M Y') ?? '—' }}</dd>
                            <dt class="col-5" style="color:var(--muted);font-weight:500;">Address</dt>
                            <dd class="col-7 mb-0" style="font-size:0.78rem;">
                                {{ $student->address ?? '—' }}
                                @if($student->city) , {{ $student->city }} @endif
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>

            {{-- Fee Table --}}
            @if($receipt)
            <div class="mb-4">
                <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:var(--muted);margin-bottom:0.75rem;">
                    Fee Details
                </div>
                <div class="table-responsive">
                    <table class="table mb-0" style="font-size:0.875rem;">
                        <thead>
                            <tr>
                                <th style="width:50%;">Description</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($receipt->admission_fee > 0)
                            <tr>
                                <td>Admission Fee</td>
                                <td class="text-end">₹{{ number_format($receipt->admission_fee, 2) }}</td>
                            </tr>
                            @endif
                            @if($receipt->tuition_fee > 0)
                            <tr>
                                <td>Tuition Fee</td>
                                <td class="text-end">₹{{ number_format($receipt->tuition_fee, 2) }}</td>
                            </tr>
                            @endif
                            @if($receipt->other_fees > 0)
                            <tr>
                                <td>Other Fees</td>
                                <td class="text-end">₹{{ number_format($receipt->other_fees, 2) }}</td>
                            </tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <tr style="background:var(--bg);">
                                <td style="font-weight:600;">Total Amount</td>
                                <td class="text-end" style="font-weight:700;color:#4f46e5;">
                                    ₹{{ number_format($receipt->total_amount, 2) }}
                                </td>
                            </tr>
                            <tr>
                                <td style="color:#059669;font-weight:600;">Amount Paid</td>
                                <td class="text-end" style="color:#059669;font-weight:700;">
                                    ₹{{ number_format($receipt->amount_paid, 2) }}
                                </td>
                            </tr>
                            @if($receipt->balance_due > 0)
                            <tr>
                                <td style="color:#dc2626;font-weight:600;">Balance Due</td>
                                <td class="text-end" style="color:#dc2626;font-weight:700;">
                                    ₹{{ number_format($receipt->balance_due, 2) }}
                                </td>
                            </tr>
                            @endif
                        </tfoot>
                    </table>
                </div>

                {{-- Payment Info --}}
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-3 p-3 rounded"
                     style="background:var(--bg);border:1px solid var(--border);">
                    <div style="font-size:0.82rem;">
                        <span style="color:var(--muted);">Payment Mode:</span>
                        <strong class="ms-1">{{ ucfirst($receipt->payment_mode) }}</strong>
                        @if($receipt->transaction_reference)
                            <span class="ms-2" style="color:var(--muted);">Ref:</span>
                            <strong class="ms-1">{{ $receipt->transaction_reference }}</strong>
                        @endif
                    </div>
                    <div>
                        @php
                            $statusColors = ['paid' => ['#dcfce7','#166534'], 'partial' => ['#fef3c7','#92400e'], 'pending' => ['#fee2e2','#991b1b']];
                            [$bg, $fg] = $statusColors[$receipt->status] ?? ['#f3f4f6','#374151'];
                        @endphp
                        <span class="badge" style="background:{{ $bg }};color:{{ $fg }};font-size:0.82rem;padding:0.4em 0.8em;">
                            {{ ucfirst($receipt->status) }}
                        </span>
                    </div>
                </div>
            </div>
            @else
            <div class="alert alert-info" style="border-radius:0.75rem;">
                <i class="fa-solid fa-info-circle me-2"></i>
                No fee was collected at the time of admission.
            </div>
            @endif

            {{-- Footer --}}
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-3"
                 style="border-top:1px solid var(--border);">
                <div style="font-size:0.75rem;color:var(--muted);">
                    Generated by {{ $receipt?->generatedBy?->name ?? auth()->user()->name }}
                    on {{ now()->format('d M Y, h:i A') }}
                </div>
                <div style="font-size:0.75rem;color:var(--muted);">
                    This is a computer-generated receipt.
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

