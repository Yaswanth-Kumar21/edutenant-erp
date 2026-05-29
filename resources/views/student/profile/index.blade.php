@extends('layouts.student-app')

@section('title', 'My Profile')

@section('breadcrumb')
    <li class="breadcrumb-item active">My Profile</li>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-user me-2" style="color:#4f46e5;"></i>My Profile</h1>
        <p style="color:var(--muted);font-size:0.875rem;margin:0;">Your personal and academic information</p>
    </div>
    <a href="{{ route('student.profile.password') }}" class="btn btn-outline-primary btn-sm">
        <i class="fa-solid fa-key me-1"></i> Change Password
    </a>
</div>

<div class="row g-3">
    {{-- Profile Card --}}
    <div class="col-lg-4">
        <div class="card text-center">
            <div class="card-body py-4">
                <img src="{{ $student->photo_url }}" alt="{{ $student->full_name }}"
                     class="rounded-circle mb-3"
                     style="width:100px;height:100px;object-fit:cover;border:4px solid var(--border);">
                <h5 style="font-weight:700;margin-bottom:4px;">{{ $student->full_name }}</h5>
                <div style="font-size:0.82rem;color:var(--muted);margin-bottom:0.75rem;">
                    {{ $student->branch?->course?->name }} &bull; {{ $student->branch?->name }}
                </div>
                <div class="d-flex justify-content-center gap-2 flex-wrap">
                    <span class="badge" style="background:rgba(79,70,229,0.1);color:#4f46e5;font-family:monospace;">
                        {{ $student->admission_number }}
                    </span>
                    <span class="badge" style="background:#dcfce7;color:#166534;">
                        {{ ucfirst($student->status) }}
                    </span>
                    <span class="badge" style="background:rgba(124,58,237,0.1);color:#7c3aed;">
                        {{ $student->category }}
                    </span>
                </div>
                <hr style="border-color:var(--border);">
                <div class="text-start" style="font-size:0.82rem;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="fa-solid fa-envelope" style="color:var(--muted);width:16px;"></i>
                        <span style="color:var(--muted);">{{ $user->email }}</span>
                    </div>
                    @if($student->phone)
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="fa-solid fa-phone" style="color:var(--muted);width:16px;"></i>
                        <span>{{ $student->phone }}</span>
                    </div>
                    @endif
                    @if($student->blood_group)
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-droplet" style="color:#dc2626;width:16px;"></i>
                        <span>{{ $student->blood_group }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Details --}}
    <div class="col-lg-8">
        {{-- Personal Info --}}
        <div class="card mb-3">
            <div class="card-header">
                <i class="fa-solid fa-id-card me-2" style="color:#4f46e5;"></i>Personal Information
            </div>
            <div class="card-body">
                <div class="row g-3" style="font-size:0.875rem;">
                    <div class="col-sm-6">
                        <div style="color:var(--muted);font-size:0.75rem;margin-bottom:2px;">First Name</div>
                        <div style="font-weight:500;">{{ $student->first_name }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div style="color:var(--muted);font-size:0.75rem;margin-bottom:2px;">Last Name</div>
                        <div style="font-weight:500;">{{ $student->last_name }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div style="color:var(--muted);font-size:0.75rem;margin-bottom:2px;">Date of Birth</div>
                        <div style="font-weight:500;">{{ $student->date_of_birth?->format('d M Y') ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div style="color:var(--muted);font-size:0.75rem;margin-bottom:2px;">Gender</div>
                        <div style="font-weight:500;">{{ ucfirst($student->gender ?? '—') }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div style="color:var(--muted);font-size:0.75rem;margin-bottom:2px;">Father Name</div>
                        <div style="font-weight:500;">{{ $student->father_name ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div style="color:var(--muted);font-size:0.75rem;margin-bottom:2px;">Mother Name</div>
                        <div style="font-weight:500;">{{ $student->mother_name ?? '—' }}</div>
                    </div>
                    <div class="col-12">
                        <div style="color:var(--muted);font-size:0.75rem;margin-bottom:2px;">Address</div>
                        <div style="font-weight:500;">
                            {{ $student->address ?? '—' }}
                            {{ $student->city ? ', ' . $student->city : '' }}
                            {{ $student->state ? ', ' . $student->state : '' }}
                            {{ $student->pincode ? ' - ' . $student->pincode : '' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Academic Info --}}
        <div class="card mb-3">
            <div class="card-header">
                <i class="fa-solid fa-graduation-cap me-2" style="color:#7c3aed;"></i>Academic Information
            </div>
            <div class="card-body">
                <div class="row g-3" style="font-size:0.875rem;">
                    <div class="col-sm-6">
                        <div style="color:var(--muted);font-size:0.75rem;margin-bottom:2px;">Course</div>
                        <div style="font-weight:500;">{{ $student->branch?->course?->name ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div style="color:var(--muted);font-size:0.75rem;margin-bottom:2px;">Branch</div>
                        <div style="font-weight:500;">{{ $student->branch?->name ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div style="color:var(--muted);font-size:0.75rem;margin-bottom:2px;">Stream</div>
                        <div style="font-weight:500;">{{ $student->branch?->course?->stream?->name ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div style="color:var(--muted);font-size:0.75rem;margin-bottom:2px;">Semester</div>
                        <div style="font-weight:500;">Semester {{ $student->current_semester }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div style="color:var(--muted);font-size:0.75rem;margin-bottom:2px;">Academic Year</div>
                        <div style="font-weight:500;">{{ $student->academicYear?->name ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div style="color:var(--muted);font-size:0.75rem;margin-bottom:2px;">Admission Date</div>
                        <div style="font-weight:500;">{{ $student->admission_date?->format('d M Y') ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div style="color:var(--muted);font-size:0.75rem;margin-bottom:2px;">University Reg. No</div>
                        <div style="font-weight:500;font-family:monospace;">{{ $student->university_reg_number ?? 'Not assigned yet' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div style="color:var(--muted);font-size:0.75rem;margin-bottom:2px;">Scholarship</div>
                        <div>
                            @if($student->scholarship_eligible)
                                <span class="badge" style="background:#dcfce7;color:#166534;">Eligible</span>
                            @else
                                <span style="font-weight:500;color:var(--muted);">Not Eligible</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Guardian Info --}}
        @if($student->guardian)
        <div class="card">
            <div class="card-header">
                <i class="fa-solid fa-users me-2" style="color:#d97706;"></i>Guardian Information
            </div>
            <div class="card-body">
                <div class="row g-3" style="font-size:0.875rem;">
                    <div class="col-sm-6">
                        <div style="color:var(--muted);font-size:0.75rem;margin-bottom:2px;">Father Name</div>
                        <div style="font-weight:500;">{{ $student->guardian->father_name ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div style="color:var(--muted);font-size:0.75rem;margin-bottom:2px;">Father Phone</div>
                        <div style="font-weight:500;">{{ $student->guardian->father_phone ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div style="color:var(--muted);font-size:0.75rem;margin-bottom:2px;">Father Occupation</div>
                        <div style="font-weight:500;">{{ $student->guardian->father_occupation ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div style="color:var(--muted);font-size:0.75rem;margin-bottom:2px;">Mother Name</div>
                        <div style="font-weight:500;">{{ $student->guardian->mother_name ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@endsection

