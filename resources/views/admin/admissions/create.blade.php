@extends('layouts.app')

@section('title', 'New Student Admission')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.students.index') }}" style="color:#4f46e5;text-decoration:none;">Students</a>
    </li>
    <li class="breadcrumb-item active">New Admission</li>
@endsection

@push('styles')
<style>
/* ── Wizard Styles ─────────────────────────────────────────────────── */
.wizard-steps {
    display: flex;
    align-items: center;
    gap: 0;
    margin-bottom: 2rem;
    overflow-x: auto;
    padding-bottom: 0.5rem;
}
.wizard-step {
    display: flex;
    align-items: center;
    flex: 1;
    min-width: 0;
}
.wizard-step-inner {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.375rem;
    cursor: pointer;
    flex-shrink: 0;
}
.wizard-step-num {
    width: 40px; height: 40px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 0.9rem;
    border: 2px solid var(--border);
    background: var(--surface);
    color: var(--muted);
    transition: all 0.25s ease;
    position: relative;
    z-index: 1;
}
.wizard-step-label {
    font-size: 0.72rem;
    font-weight: 500;
    color: var(--muted);
    white-space: nowrap;
    transition: color 0.25s;
}
.wizard-step-line {
    flex: 1;
    height: 2px;
    background: var(--border);
    margin: 0 0.25rem;
    margin-bottom: 1.25rem;
    transition: background 0.25s;
}
/* Active step */
.wizard-step.active .wizard-step-num {
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    border-color: #4f46e5;
    color: #fff;
    box-shadow: 0 4px 12px rgba(79,70,229,0.35);
}
.wizard-step.active .wizard-step-label { color: #4f46e5; font-weight: 600; }
/* Completed step */
.wizard-step.completed .wizard-step-num {
    background: #059669;
    border-color: #059669;
    color: #fff;
}
.wizard-step.completed .wizard-step-label { color: #059669; }
.wizard-step.completed + .wizard-step-line { background: #059669; }

/* ── Step Panels ───────────────────────────────────────────────────── */
.step-panel { display: none; }
.step-panel.active { display: block; animation: fadeInUp 0.3s ease; }

/* ── Photo Upload ──────────────────────────────────────────────────── */
.photo-upload-area {
    width: 120px; height: 120px;
    border-radius: 50%;
    border: 2px dashed var(--border);
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    overflow: hidden;
    position: relative;
    transition: border-color 0.2s;
    background: var(--bg);
}
.photo-upload-area:hover { border-color: #4f46e5; }
.photo-upload-area img { width: 100%; height: 100%; object-fit: cover; }
.photo-upload-overlay {
    position: absolute; inset: 0;
    background: rgba(79,70,229,0.7);
    display: flex; align-items: center; justify-content: center;
    opacity: 0; transition: opacity 0.2s;
    border-radius: 50%;
}
.photo-upload-area:hover .photo-upload-overlay { opacity: 1; }

/* ── Certificate Upload ────────────────────────────────────────────── */
.cert-upload-zone {
    border: 2px dashed var(--border);
    border-radius: 0.75rem;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
    background: var(--bg);
}
.cert-upload-zone:hover, .cert-upload-zone.dragover {
    border-color: #4f46e5;
    background: rgba(79,70,229,0.04);
}
.cert-item {
    display: flex; align-items: center; gap: 0.75rem;
    padding: 0.625rem 0.875rem;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 0.5rem;
    margin-bottom: 0.5rem;
}
.cert-item .cert-icon {
    width: 36px; height: 36px;
    border-radius: 0.375rem;
    display: flex; align-items: center; justify-content: center;
    background: rgba(79,70,229,0.1);
    flex-shrink: 0;
}

/* ── Fee Summary Card ──────────────────────────────────────────────── */
.fee-summary-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid var(--border);
    font-size: 0.875rem;
}
.fee-summary-row:last-child { border-bottom: none; }
.fee-total-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 0.75rem 0 0;
    font-weight: 700; font-size: 1rem;
    color: #4f46e5;
}
</style>
@endpush

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fa-solid fa-user-plus me-2" style="color:#4f46e5;"></i>
            New Student Admission
        </h1>
        <p class="mb-0" style="color:var(--muted);font-size:0.875rem;">
            Complete all steps to register a new student
        </p>
    </div>
    <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-2"></i> Back to Students
    </a>
</div>

{{-- Validation Errors --}}
@if($errors->any())
<div class="alert alert-danger d-flex gap-2 mb-4" style="border-radius:0.75rem;">
    <i class="fa-solid fa-circle-exclamation mt-1 flex-shrink-0"></i>
    <div>
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-1 ps-3">
            @foreach($errors->all() as $error)
                <li style="font-size:0.875rem;">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

{{-- ── Wizard Progress ──────────────────────────────────────────────────── --}}
<div class="wizard-steps" id="wizard-steps">
    <div class="wizard-step active" data-step="1" onclick="goToStep(1)">
        <div class="wizard-step-inner">
            <div class="wizard-step-num"><i class="fa-solid fa-user" id="step1-icon"></i></div>
            <div class="wizard-step-label">Personal</div>
        </div>
    </div>
    <div class="wizard-step-line" id="line-1"></div>
    <div class="wizard-step" data-step="2" onclick="goToStep(2)">
        <div class="wizard-step-inner">
            <div class="wizard-step-num"><i class="fa-solid fa-graduation-cap" id="step2-icon"></i></div>
            <div class="wizard-step-label">Academic</div>
        </div>
    </div>
    <div class="wizard-step-line" id="line-2"></div>
    <div class="wizard-step" data-step="3" onclick="goToStep(3)">
        <div class="wizard-step-inner">
            <div class="wizard-step-num"><i class="fa-solid fa-id-card" id="step3-icon"></i></div>
            <div class="wizard-step-label">Category</div>
        </div>
    </div>
    <div class="wizard-step-line" id="line-3"></div>
    <div class="wizard-step" data-step="4" onclick="goToStep(4)">
        <div class="wizard-step-inner">
            <div class="wizard-step-num"><i class="fa-solid fa-users" id="step4-icon"></i></div>
            <div class="wizard-step-label">Guardian</div>
        </div>
    </div>
    <div class="wizard-step-line" id="line-4"></div>
    <div class="wizard-step" data-step="5" onclick="goToStep(5)">
        <div class="wizard-step-inner">
            <div class="wizard-step-num"><i class="fa-solid fa-file-arrow-up" id="step5-icon"></i></div>
            <div class="wizard-step-label">Documents</div>
        </div>
    </div>
    <div class="wizard-step-line" id="line-5"></div>
    <div class="wizard-step" data-step="6" onclick="goToStep(6)">
        <div class="wizard-step-inner">
            <div class="wizard-step-num"><i class="fa-solid fa-receipt" id="step6-icon"></i></div>
            <div class="wizard-step-label">Receipt</div>
        </div>
    </div>
</div>

{{-- ── Main Form ────────────────────────────────────────────────────────── --}}
<form method="POST" action="{{ route('admin.admissions.store') }}"
      enctype="multipart/form-data" id="admission-form" novalidate>
    @csrf

    {{-- ════════════════════════════════════════════════════════════════════ --}}
    {{-- STEP 1: Personal Details                                            --}}
    {{-- ════════════════════════════════════════════════════════════════════ --}}
    <div class="step-panel active" id="step-1">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width:28px;height:28px;background:rgba(79,70,229,0.1);">
                    <span style="font-size:0.75rem;font-weight:700;color:#4f46e5;">1</span>
                </div>
                <span>Personal Details</span>
            </div>
            <div class="card-body">
                {{-- Photo Upload --}}
                <div class="d-flex align-items-start gap-4 mb-4 flex-wrap">
                    <div>
                        <label class="form-label d-block">Profile Photo</label>
                        <div class="photo-upload-area" id="photo-preview-wrap" onclick="document.getElementById('photo-input').click()">
                            <img id="photo-preview"
                                 src="https://ui-avatars.com/api/?name=Student&background=4f46e5&color=fff&size=128"
                                 alt="Photo">
                            <div class="photo-upload-overlay">
                                <i class="fa-solid fa-camera text-white" style="font-size:1.25rem;"></i>
                            </div>
                        </div>
                        <input type="file" id="photo-input" name="photo"
                               accept="image/*" class="d-none"
                               onchange="previewPhoto(this)">
                        <div class="mt-1" style="font-size:0.72rem;color:var(--muted);text-align:center;">
                            JPG/PNG, max 2MB
                        </div>
                        @error('photo')
                            <div class="text-danger mt-1" style="font-size:0.8rem;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="flex-1" style="min-width:200px;">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name"
                                       class="form-control @error('first_name') is-invalid @enderror"
                                       value="{{ old('first_name') }}" placeholder="e.g. Rahul" required>
                                @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name"
                                       class="form-control @error('last_name') is-invalid @enderror"
                                       value="{{ old('last_name') }}" placeholder="e.g. Sharma" required>
                                @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="date_of_birth"
                               class="form-control @error('date_of_birth') is-invalid @enderror"
                               value="{{ old('date_of_birth') }}"
                               max="{{ now()->subYears(14)->format('Y-m-d') }}">
                        @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Gender <span class="text-danger">*</span></label>
                        <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                            <option value="">Select Gender</option>
                            <option value="male"   {{ old('gender') === 'male'   ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other"  {{ old('gender') === 'other'  ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Blood Group</label>
                        <select name="blood_group" class="form-select @error('blood_group') is-invalid @enderror">
                            <option value="">Select Blood Group</option>
                            @foreach(['A+','A-','B+','B-','O+','O-','AB+','AB-'] as $bg)
                                <option value="{{ $bg }}" {{ old('blood_group') === $bg ? 'selected' : '' }}>{{ $bg }}</option>
                            @endforeach
                        </select>
                        @error('blood_group')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text" style="background:var(--bg);border-color:var(--border);">
                                <i class="fa-solid fa-phone" style="font-size:0.8rem;color:var(--muted);"></i>
                            </span>
                            <input type="tel" name="phone"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone') }}" placeholder="10-digit mobile" required>
                        </div>
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background:var(--bg);border-color:var(--border);">
                                <i class="fa-solid fa-envelope" style="font-size:0.8rem;color:var(--muted);"></i>
                            </span>
                            <input type="email" name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}" placeholder="student@email.com">
                        </div>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Aadhaar Number</label>
                        <input type="text" name="aadhaar_number"
                               class="form-control @error('aadhaar_number') is-invalid @enderror"
                               value="{{ old('aadhaar_number') }}" placeholder="12-digit Aadhaar"
                               maxlength="20">
                        @error('aadhaar_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Address</label>
                        <input type="text" name="address"
                               class="form-control @error('address') is-invalid @enderror"
                               value="{{ old('address') }}" placeholder="Full residential address">
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">City</label>
                        <input type="text" name="city"
                               class="form-control @error('city') is-invalid @enderror"
                               value="{{ old('city') }}" placeholder="City">
                        @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">State</label>
                        <input type="text" name="state"
                               class="form-control @error('state') is-invalid @enderror"
                               value="{{ old('state') }}" placeholder="State">
                        @error('state')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Pincode</label>
                        <input type="text" name="pincode"
                               class="form-control @error('pincode') is-invalid @enderror"
                               value="{{ old('pincode') }}" placeholder="6-digit pincode" maxlength="10">
                        @error('pincode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-primary px-5" onclick="nextStep(1)">
                Next: Academic Details <i class="fa-solid fa-arrow-right ms-2"></i>
            </button>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════ --}}
    {{-- STEP 2: Academic Details                                            --}}
    {{-- ════════════════════════════════════════════════════════════════════ --}}
    <div class="step-panel" id="step-2">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width:28px;height:28px;background:rgba(5,150,105,0.1);">
                    <span style="font-size:0.75rem;font-weight:700;color:#059669;">2</span>
                </div>
                <span>Academic Details</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Branch <span class="text-danger">*</span></label>
                        <select name="branch_id" id="branch_id"
                                class="form-select @error('branch_id') is-invalid @enderror" required>
                            <option value="">— Select Branch —</option>
                            @foreach($branches->groupBy('course.stream.name') as $streamName => $streamBranches)
                                <optgroup label="{{ $streamName ?? 'Other' }}">
                                    @foreach($streamBranches->groupBy('course.name') as $courseName => $courseBranches)
                                        @foreach($courseBranches as $branch)
                                            <option value="{{ $branch->id }}"
                                                {{ old('branch_id') == $branch->id ? 'selected' : '' }}
                                                data-tuition="{{ $branch->tuition_fee_student }}">
                                                {{ $branch->name }} ({{ $courseName }})
                                            </option>
                                        @endforeach
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @error('branch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                        <select name="academic_year_id"
                                class="form-select @error('academic_year_id') is-invalid @enderror" required>
                            <option value="">— Select Year —</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}"
                                    {{ (old('academic_year_id') == $year->id || (!old('academic_year_id') && $year->is_current)) ? 'selected' : '' }}>
                                    {{ $year->name }}
                                    @if($year->is_current) (Current) @endif
                                </option>
                            @endforeach
                        </select>
                        @error('academic_year_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">10th Marks (%)</label>
                        <div class="input-group">
                            <input type="number" name="marks_10th"
                                   class="form-control @error('marks_10th') is-invalid @enderror"
                                   value="{{ old('marks_10th') }}" placeholder="0.00"
                                   min="0" max="100" step="0.01">
                            <span class="input-group-text" style="background:var(--bg);border-color:var(--border);">%</span>
                        </div>
                        @error('marks_10th')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">12th Marks (%)</label>
                        <div class="input-group">
                            <input type="number" name="marks_12th"
                                   class="form-control @error('marks_12th') is-invalid @enderror"
                                   value="{{ old('marks_12th') }}" placeholder="0.00"
                                   min="0" max="100" step="0.01">
                            <span class="input-group-text" style="background:var(--bg);border-color:var(--border);">%</span>
                        </div>
                        @error('marks_12th')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Current Semester</label>
                        <select name="current_semester" class="form-select">
                            @for($i = 1; $i <= 8; $i++)
                                <option value="{{ $i }}" {{ old('current_semester', 1) == $i ? 'selected' : '' }}>
                                    Semester {{ $i }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Previous Institution</label>
                        <input type="text" name="previous_institution"
                               class="form-control @error('previous_institution') is-invalid @enderror"
                               value="{{ old('previous_institution') }}"
                               placeholder="Name of previous college/school">
                        @error('previous_institution')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">University Reg. Number</label>
                        <input type="text" name="university_reg_number"
                               class="form-control @error('university_reg_number') is-invalid @enderror"
                               value="{{ old('university_reg_number') }}"
                               placeholder="Assigned by university (optional)">
                        @error('university_reg_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-between">
            <button type="button" class="btn btn-outline-secondary px-4" onclick="prevStep(2)">
                <i class="fa-solid fa-arrow-left me-2"></i> Previous
            </button>
            <button type="button" class="btn btn-primary px-5" onclick="nextStep(2)">
                Next: Category &amp; Admission <i class="fa-solid fa-arrow-right ms-2"></i>
            </button>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════ --}}
    {{-- STEP 3: Category & Admission                                        --}}
    {{-- ════════════════════════════════════════════════════════════════════ --}}
    <div class="step-panel" id="step-3">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width:28px;height:28px;background:rgba(217,119,6,0.1);">
                    <span style="font-size:0.75rem;font-weight:700;color:#d97706;">3</span>
                </div>
                <span>Category &amp; Admission Details</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Reservation Category <span class="text-danger">*</span></label>
                        <div class="row g-2 mt-1">
                            @foreach(['GEN' => ['General','#4f46e5'], 'OBC' => ['OBC','#059669'], 'SC' => ['SC','#d97706'], 'ST' => ['ST','#7c3aed'], 'EWS' => ['EWS','#0891b2'], 'OTHER' => ['Other','#6b7280']] as $val => [$label, $color])
                            <div class="col-6 col-md-4">
                                <label class="d-flex align-items-center gap-2 p-2 rounded cursor-pointer"
                                       style="border:2px solid var(--border);cursor:pointer;transition:all 0.2s;"
                                       id="cat-label-{{ $val }}">
                                    <input type="radio" name="category" value="{{ $val }}"
                                           class="form-check-input m-0"
                                           {{ old('category', 'GEN') === $val ? 'checked' : '' }}
                                           onchange="highlightCategory('{{ $val }}')">
                                    <span style="font-size:0.85rem;font-weight:600;color:{{ $color }};">{{ $label }}</span>
                                </label>
                            </div>
                            @endforeach
                        </div>
                        @error('category')<div class="text-danger mt-1" style="font-size:0.8rem;">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Admission Date <span class="text-danger">*</span></label>
                        <input type="date" name="admission_date"
                               class="form-control @error('admission_date') is-invalid @enderror"
                               value="{{ old('admission_date', now()->format('Y-m-d')) }}" required>
                        @error('admission_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" id="scholarship_eligible"
                                   name="scholarship_eligible" value="1"
                                   {{ old('scholarship_eligible') ? 'checked' : '' }}>
                            <label class="form-check-label" for="scholarship_eligible" style="font-weight:500;">
                                Scholarship Eligible
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" id="vehicle_opted"
                                   name="vehicle_opted" value="1"
                                   {{ old('vehicle_opted') ? 'checked' : '' }}
                                   onchange="toggleVehicleDate(this.checked)">
                            <label class="form-check-label" for="vehicle_opted" style="font-weight:500;">
                                College Vehicle / Transport
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4" id="vehicle-date-row"
                         style="display:{{ old('vehicle_opted') ? 'block' : 'none' }};">
                        <label class="form-label">Vehicle Start Date</label>
                        <input type="date" name="vehicle_start_date"
                               class="form-control @error('vehicle_start_date') is-invalid @enderror"
                               value="{{ old('vehicle_start_date') }}">
                        @error('vehicle_start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-between">
            <button type="button" class="btn btn-outline-secondary px-4" onclick="prevStep(3)">
                <i class="fa-solid fa-arrow-left me-2"></i> Previous
            </button>
            <button type="button" class="btn btn-primary px-5" onclick="nextStep(3)">
                Next: Guardian Details <i class="fa-solid fa-arrow-right ms-2"></i>
            </button>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════ --}}
    {{-- STEP 4: Guardian Details                                            --}}
    {{-- ════════════════════════════════════════════════════════════════════ --}}
    <div class="step-panel" id="step-4">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width:28px;height:28px;background:rgba(124,58,237,0.1);">
                    <span style="font-size:0.75rem;font-weight:700;color:#7c3aed;">4</span>
                </div>
                <span>Guardian / Parent Details</span>
            </div>
            <div class="card-body">
                {{-- Father --}}
                <h6 class="section-title mb-3">Father's Information</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Father's Name</label>
                        <input type="text" name="father_name"
                               class="form-control @error('father_name') is-invalid @enderror"
                               value="{{ old('father_name') }}" placeholder="Father's full name">
                        @error('father_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Occupation</label>
                        <input type="text" name="father_occupation"
                               class="form-control @error('father_occupation') is-invalid @enderror"
                               value="{{ old('father_occupation') }}" placeholder="e.g. Farmer, Business">
                        @error('father_occupation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Father's Phone</label>
                        <input type="tel" name="father_phone"
                               class="form-control @error('father_phone') is-invalid @enderror"
                               value="{{ old('father_phone') }}" placeholder="Mobile number">
                        @error('father_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Father's Email</label>
                        <input type="email" name="father_email"
                               class="form-control @error('father_email') is-invalid @enderror"
                               value="{{ old('father_email') }}" placeholder="Email (optional)">
                        @error('father_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Mother --}}
                <h6 class="section-title mb-3">Mother's Information</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Mother's Name</label>
                        <input type="text" name="mother_name"
                               class="form-control @error('mother_name') is-invalid @enderror"
                               value="{{ old('mother_name') }}" placeholder="Mother's full name">
                        @error('mother_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Occupation</label>
                        <input type="text" name="mother_occupation"
                               class="form-control @error('mother_occupation') is-invalid @enderror"
                               value="{{ old('mother_occupation') }}" placeholder="e.g. Homemaker, Teacher">
                        @error('mother_occupation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Mother's Phone</label>
                        <input type="tel" name="mother_phone"
                               class="form-control @error('mother_phone') is-invalid @enderror"
                               value="{{ old('mother_phone') }}" placeholder="Mobile number">
                        @error('mother_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Financial --}}
                <h6 class="section-title mb-3">Financial Information</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Annual Family Income (₹)</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background:var(--bg);border-color:var(--border);">₹</span>
                            <input type="number" name="annual_income"
                                   class="form-control @error('annual_income') is-invalid @enderror"
                                   value="{{ old('annual_income') }}" placeholder="0" min="0">
                        </div>
                        @error('annual_income')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox"
                                   id="guardian_scholarship_eligible"
                                   name="guardian_scholarship_eligible" value="1"
                                   {{ old('guardian_scholarship_eligible') ? 'checked' : '' }}>
                            <label class="form-check-label" for="guardian_scholarship_eligible" style="font-weight:500;">
                                Eligible for Government Scholarship
                            </label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Scholarship Details</label>
                        <textarea name="scholarship_details" class="form-control" rows="2"
                                  placeholder="e.g. SC/ST scholarship, merit scholarship details...">{{ old('scholarship_details') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-between">
            <button type="button" class="btn btn-outline-secondary px-4" onclick="prevStep(4)">
                <i class="fa-solid fa-arrow-left me-2"></i> Previous
            </button>
            <button type="button" class="btn btn-primary px-5" onclick="nextStep(4)">
                Next: Documents <i class="fa-solid fa-arrow-right ms-2"></i>
            </button>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════ --}}
    {{-- STEP 5: Certificate Uploads                                         --}}
    {{-- ════════════════════════════════════════════════════════════════════ --}}
    <div class="step-panel" id="step-5">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width:28px;height:28px;background:rgba(8,145,178,0.1);">
                    <span style="font-size:0.75rem;font-weight:700;color:#0891b2;">5</span>
                </div>
                <span>Certificate Uploads</span>
                <span class="badge ms-2" style="background:rgba(8,145,178,0.1);color:#0891b2;font-size:0.72rem;">Optional</span>
            </div>
            <div class="card-body">
                <p style="color:var(--muted);font-size:0.875rem;" class="mb-3">
                    Upload student certificates. Accepted formats: JPG, PNG, PDF. Max 5MB per file.
                    You can also upload these later from the student profile.
                </p>

                {{-- Quick checklist --}}
                <div class="row g-2 mb-4">
                    @foreach($certificateTypes as $type => $label)
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="d-flex align-items-center gap-2 p-2 rounded"
                             style="border:1px solid var(--border);cursor:pointer;transition:all 0.2s;"
                             id="cert-check-{{ $type }}"
                             onclick="triggerCertUpload('{{ $type }}', '{{ $label }}')">
                            <i class="fa-solid fa-circle-xmark" id="cert-icon-{{ $type }}"
                               style="color:#9ca3af;font-size:0.9rem;flex-shrink:0;"></i>
                            <span style="font-size:0.78rem;">{{ $label }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Upload zone --}}
                <div class="cert-upload-zone" id="cert-drop-zone"
                     onclick="document.getElementById('cert-files-input').click()">
                    <i class="fa-solid fa-cloud-arrow-up" style="font-size:2.5rem;color:#4f46e5;opacity:0.6;"></i>
                    <div class="mt-2" style="font-weight:500;color:var(--text);">
                        Click to upload or drag &amp; drop certificates
                    </div>
                    <div style="font-size:0.8rem;color:var(--muted);margin-top:0.25rem;">
                        JPG, PNG, PDF — up to 5MB each
                    </div>
                </div>

                {{-- Hidden file input --}}
                <input type="file" id="cert-files-input" name="certificates[]"
                       accept=".jpg,.jpeg,.png,.pdf" multiple class="d-none"
                       onchange="handleCertFiles(this.files)">

                {{-- Hidden type inputs (populated by JS) --}}
                <div id="cert-type-inputs"></div>

                {{-- Uploaded files list --}}
                <div id="cert-files-list" class="mt-3"></div>
            </div>
        </div>
        <div class="d-flex justify-content-between">
            <button type="button" class="btn btn-outline-secondary px-4" onclick="prevStep(5)">
                <i class="fa-solid fa-arrow-left me-2"></i> Previous
            </button>
            <button type="button" class="btn btn-primary px-5" onclick="nextStep(5)">
                Next: Fee &amp; Receipt <i class="fa-solid fa-arrow-right ms-2"></i>
            </button>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════ --}}
    {{-- STEP 6: Fee & Receipt                                               --}}
    {{-- ════════════════════════════════════════════════════════════════════ --}}
    <div class="step-panel" id="step-6">
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center gap-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width:28px;height:28px;background:rgba(5,150,105,0.1);">
                            <span style="font-size:0.75rem;font-weight:700;color:#059669;">6</span>
                        </div>
                        <span>Admission Fee Collection</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Admission Fee (₹)</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background:var(--bg);border-color:var(--border);">₹</span>
                                    <input type="number" name="admission_fee" id="admission_fee"
                                           class="form-control" value="{{ old('admission_fee', 0) }}"
                                           min="0" step="0.01" oninput="updateFeeTotal()">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tuition Fee (₹)</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background:var(--bg);border-color:var(--border);">₹</span>
                                    <input type="number" name="tuition_fee" id="tuition_fee"
                                           class="form-control" value="{{ old('tuition_fee', 0) }}"
                                           min="0" step="0.01" oninput="updateFeeTotal()">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Other Fees (₹)</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background:var(--bg);border-color:var(--border);">₹</span>
                                    <input type="number" name="other_fees" id="other_fees"
                                           class="form-control" value="{{ old('other_fees', 0) }}"
                                           min="0" step="0.01" oninput="updateFeeTotal()">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Amount Paid (₹)</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background:var(--bg);border-color:var(--border);">₹</span>
                                    <input type="number" name="amount_paid" id="amount_paid"
                                           class="form-control" value="{{ old('amount_paid', 0) }}"
                                           min="0" step="0.01" oninput="updateFeeTotal()">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Payment Mode</label>
                                <select name="payment_mode" class="form-select">
                                    <option value="cash"   {{ old('payment_mode', 'cash') === 'cash'   ? 'selected' : '' }}>Cash</option>
                                    <option value="upi"    {{ old('payment_mode') === 'upi'    ? 'selected' : '' }}>UPI</option>
                                    <option value="online" {{ old('payment_mode') === 'online' ? 'selected' : '' }}>Online Transfer</option>
                                    <option value="cheque" {{ old('payment_mode') === 'cheque' ? 'selected' : '' }}>Cheque</option>
                                    <option value="dd"     {{ old('payment_mode') === 'dd'     ? 'selected' : '' }}>Demand Draft</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Transaction Reference</label>
                                <input type="text" name="transaction_reference"
                                       class="form-control"
                                       value="{{ old('transaction_reference') }}"
                                       placeholder="UTR / Cheque No. (optional)">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Payment Date</label>
                                <input type="date" name="payment_date"
                                       class="form-control"
                                       value="{{ old('payment_date', now()->format('Y-m-d')) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Fee Summary Card --}}
            <div class="col-lg-5">
                <div class="card" style="position:sticky;top:80px;">
                    <div class="card-header">
                        <i class="fa-solid fa-calculator me-2" style="color:#4f46e5;"></i>
                        Fee Summary
                    </div>
                    <div class="card-body">
                        <div class="fee-summary-row">
                            <span style="color:var(--muted);">Admission Fee</span>
                            <span id="sum-admission">₹0</span>
                        </div>
                        <div class="fee-summary-row">
                            <span style="color:var(--muted);">Tuition Fee</span>
                            <span id="sum-tuition">₹0</span>
                        </div>
                        <div class="fee-summary-row">
                            <span style="color:var(--muted);">Other Fees</span>
                            <span id="sum-other">₹0</span>
                        </div>
                        <div class="fee-summary-row" style="font-weight:600;">
                            <span>Total Amount</span>
                            <span id="sum-total" style="color:#4f46e5;">₹0</span>
                        </div>
                        <div class="fee-summary-row" style="color:#059669;font-weight:600;">
                            <span>Amount Paid</span>
                            <span id="sum-paid">₹0</span>
                        </div>
                        <div class="fee-total-row">
                            <span>Balance Due</span>
                            <span id="sum-balance" style="color:#dc2626;">₹0</span>
                        </div>

                        <div class="mt-3 p-3 rounded" id="payment-status-badge"
                             style="background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.2);text-align:center;">
                            <i class="fa-solid fa-circle-check" style="color:#059669;"></i>
                            <span id="payment-status-text" style="font-size:0.85rem;font-weight:600;color:#059669;margin-left:0.5rem;">
                                Fully Paid
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between mt-2">
            <button type="button" class="btn btn-outline-secondary px-4" onclick="prevStep(6)">
                <i class="fa-solid fa-arrow-left me-2"></i> Previous
            </button>
            <button type="submit" class="btn btn-primary px-5" id="submit-btn">
                <i class="fa-solid fa-user-check me-2"></i> Complete Admission
            </button>
        </div>
    </div>

</form>

@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    /* ── Wizard State ──────────────────────────────────────────────────── */
    let currentStep = {{ $errors->any() ? 1 : 1 }};
    const totalSteps = 6;

    window.goToStep = function (step) {
        if (step < 1 || step > totalSteps) return;

        // Hide all panels
        document.querySelectorAll('.step-panel').forEach(p => p.classList.remove('active'));
        // Show target
        document.getElementById('step-' + step).classList.add('active');

        // Update wizard indicators
        document.querySelectorAll('.wizard-step').forEach(function (el) {
            const s = parseInt(el.dataset.step);
            el.classList.remove('active', 'completed');
            if (s === step) el.classList.add('active');
            else if (s < step) el.classList.add('completed');
        });

        currentStep = step;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    window.nextStep = function (from) {
        if (validateStep(from)) {
            goToStep(from + 1);
        }
    };

    window.prevStep = function (from) {
        goToStep(from - 1);
    };

    /* ── Step Validation ───────────────────────────────────────────────── */
    function validateStep(step) {
        let valid = true;
        const panel = document.getElementById('step-' + step);

        if (step === 1) {
            const required = ['first_name', 'last_name', 'gender', 'phone'];
            required.forEach(function (name) {
                const el = panel.querySelector('[name="' + name + '"]');
                if (el && !el.value.trim()) {
                    el.classList.add('is-invalid');
                    valid = false;
                } else if (el) {
                    el.classList.remove('is-invalid');
                }
            });
        }

        if (step === 2) {
            const branchEl = panel.querySelector('[name="branch_id"]');
            const yearEl   = panel.querySelector('[name="academic_year_id"]');
            if (branchEl && !branchEl.value) { branchEl.classList.add('is-invalid'); valid = false; }
            else if (branchEl) branchEl.classList.remove('is-invalid');
            if (yearEl && !yearEl.value) { yearEl.classList.add('is-invalid'); valid = false; }
            else if (yearEl) yearEl.classList.remove('is-invalid');
        }

        if (!valid) {
            Swal.fire({
                icon: 'warning',
                title: 'Required Fields',
                text: 'Please fill in all required fields before proceeding.',
                confirmButtonColor: '#4f46e5',
                timer: 3000,
                timerProgressBar: true,
            });
        }
        return valid;
    }

    /* ── Photo Preview ─────────────────────────────────────────────────── */
    window.previewPhoto = function (input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('photo-preview').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    };

    /* ── Category Highlight ────────────────────────────────────────────── */
    window.highlightCategory = function (val) {
        document.querySelectorAll('[id^="cat-label-"]').forEach(function (el) {
            el.style.borderColor = 'var(--border)';
            el.style.background  = 'transparent';
        });
        const active = document.getElementById('cat-label-' + val);
        if (active) {
            active.style.borderColor = '#4f46e5';
            active.style.background  = 'rgba(79,70,229,0.06)';
        }
    };

    // Init category highlight
    const checkedCat = document.querySelector('[name="category"]:checked');
    if (checkedCat) highlightCategory(checkedCat.value);

    /* ── Vehicle Date Toggle ───────────────────────────────────────────── */
    window.toggleVehicleDate = function (show) {
        document.getElementById('vehicle-date-row').style.display = show ? 'block' : 'none';
    };

    /* ── Certificate Upload ────────────────────────────────────────────── */
    let certFiles = [];
    let certTypes = [];

    window.triggerCertUpload = function (type, label) {
        // Store the type for the next file upload
        window._pendingCertType  = type;
        window._pendingCertLabel = label;
        document.getElementById('cert-files-input').click();
    };

    window.handleCertFiles = function (files) {
        const typeInputsContainer = document.getElementById('cert-type-inputs');
        const listContainer       = document.getElementById('cert-files-list');

        Array.from(files).forEach(function (file) {
            const certType  = window._pendingCertType  || 'other';
            const certLabel = window._pendingCertLabel || 'Other Document';

            certFiles.push(file);
            certTypes.push(certType);

            // Update checklist icon
            const icon = document.getElementById('cert-icon-' + certType);
            if (icon) {
                icon.className = 'fa-solid fa-circle-check';
                icon.style.color = '#059669';
            }

            // Add hidden type input
            const hiddenInput = document.createElement('input');
            hiddenInput.type  = 'hidden';
            hiddenInput.name  = 'certificate_types[]';
            hiddenInput.value = certType;
            typeInputsContainer.appendChild(hiddenInput);

            // Add file to visual list
            const item = document.createElement('div');
            item.className = 'cert-item';
            item.innerHTML = `
                <div class="cert-icon">
                    <i class="fa-solid fa-file-${file.type === 'application/pdf' ? 'pdf' : 'image'}"
                       style="color:#4f46e5;font-size:0.9rem;"></i>
                </div>
                <div class="flex-1 overflow-hidden">
                    <div style="font-size:0.82rem;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        ${file.name}
                    </div>
                    <div style="font-size:0.72rem;color:var(--muted);">
                        ${certLabel} &bull; ${(file.size / 1024).toFixed(1)} KB
                    </div>
                </div>
                <span class="badge" style="background:#dcfce7;color:#166534;font-size:0.7rem;">Ready</span>
            `;
            listContainer.appendChild(item);
        });

        // Reset pending type
        window._pendingCertType  = null;
        window._pendingCertLabel = null;
    };

    // Drag & drop
    const dropZone = document.getElementById('cert-drop-zone');
    if (dropZone) {
        dropZone.addEventListener('dragover', function (e) {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });
        dropZone.addEventListener('dragleave', function () {
            dropZone.classList.remove('dragover');
        });
        dropZone.addEventListener('drop', function (e) {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            window._pendingCertType  = 'other';
            window._pendingCertLabel = 'Other Document';
            handleCertFiles(e.dataTransfer.files);
        });
    }

    /* ── Fee Calculator ────────────────────────────────────────────────── */
    window.updateFeeTotal = function () {
        const admission = parseFloat(document.getElementById('admission_fee').value) || 0;
        const tuition   = parseFloat(document.getElementById('tuition_fee').value)   || 0;
        const other     = parseFloat(document.getElementById('other_fees').value)    || 0;
        const paid      = parseFloat(document.getElementById('amount_paid').value)   || 0;
        const total     = admission + tuition + other;
        const balance   = Math.max(0, total - paid);

        const fmt = v => '₹' + v.toLocaleString('en-IN', { minimumFractionDigits: 0 });

        document.getElementById('sum-admission').textContent = fmt(admission);
        document.getElementById('sum-tuition').textContent   = fmt(tuition);
        document.getElementById('sum-other').textContent     = fmt(other);
        document.getElementById('sum-total').textContent     = fmt(total);
        document.getElementById('sum-paid').textContent      = fmt(paid);
        document.getElementById('sum-balance').textContent   = fmt(balance);

        const badge = document.getElementById('payment-status-badge');
        const text  = document.getElementById('payment-status-text');

        if (total === 0 || paid >= total) {
            badge.style.background   = 'rgba(5,150,105,0.08)';
            badge.style.borderColor  = 'rgba(5,150,105,0.2)';
            text.style.color         = '#059669';
            text.innerHTML = '<i class="fa-solid fa-circle-check me-1"></i>Fully Paid';
        } else if (paid > 0) {
            badge.style.background   = 'rgba(217,119,6,0.08)';
            badge.style.borderColor  = 'rgba(217,119,6,0.2)';
            text.style.color         = '#d97706';
            text.innerHTML = '<i class="fa-solid fa-circle-half-stroke me-1"></i>Partial Payment';
        } else {
            badge.style.background   = 'rgba(220,38,38,0.08)';
            badge.style.borderColor  = 'rgba(220,38,38,0.2)';
            text.style.color         = '#dc2626';
            text.innerHTML = '<i class="fa-solid fa-circle-xmark me-1"></i>Pending';
        }
    };

    // Auto-fill tuition from branch selection
    document.getElementById('branch_id')?.addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        const tuition = opt.dataset.tuition || 0;
        document.getElementById('tuition_fee').value = tuition;
        document.getElementById('amount_paid').value = tuition;
        updateFeeTotal();
    });

    // Init fee display
    updateFeeTotal();

    /* ── Form Submit ───────────────────────────────────────────────────── */
    document.getElementById('admission-form').addEventListener('submit', function (e) {
        const btn = document.getElementById('submit-btn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
    });

    /* ── If validation failed, jump to first error step ───────────────── */
    @if($errors->any())
    // Determine which step has errors
    const errorFields = @json($errors->keys());
    const step1Fields = ['first_name','last_name','gender','phone','email','date_of_birth','blood_group','aadhaar_number','address','city','state','pincode','photo'];
    const step2Fields = ['branch_id','academic_year_id','marks_10th','marks_12th','previous_institution','current_semester'];
    const step3Fields = ['category','admission_date','scholarship_eligible','vehicle_opted','vehicle_start_date'];
    const step4Fields = ['father_name','father_occupation','father_phone','mother_name','annual_income'];

    let targetStep = 6;
    if (errorFields.some(f => step1Fields.includes(f))) targetStep = 1;
    else if (errorFields.some(f => step2Fields.includes(f))) targetStep = 2;
    else if (errorFields.some(f => step3Fields.includes(f))) targetStep = 3;
    else if (errorFields.some(f => step4Fields.includes(f))) targetStep = 4;

    goToStep(targetStep);
    @endif

})();
</script>
@endpush

