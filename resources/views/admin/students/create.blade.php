@extends('layouts.app')

@section('title', 'Add Student')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.students.index') }}" style="color:#4f46e5;text-decoration:none;">Students</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Add Student</li>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fa-solid fa-user-plus me-2" style="color:#4f46e5;"></i>
            New Student Admission
        </h1>
        <p class="mb-0" style="color:var(--muted);font-size:0.875rem;">
            Fill in all required fields to register a new student
        </p>
    </div>
    <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-2"></i> Back
    </a>
</div>

{{-- Validation Errors Summary --}}
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

<form method="POST" action="{{ route('admin.admissions.store') }}" novalidate id="student-form" enctype="multipart/form-data">
    @csrf

    {{-- ── Section 1: Personal Details ──────────────────────────────────── --}}
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width:28px;height:28px;background:rgba(79,70,229,0.1);">
                    <span style="font-size:0.75rem;font-weight:700;color:#4f46e5;">1</span>
                </div>
                <span>Personal Details</span>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
                           value="{{ old('first_name') }}" placeholder="e.g. Rahul" required>
                    @error('first_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                           value="{{ old('last_name') }}" placeholder="e.g. Sharma" required>
                    @error('last_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Father's Name</label>
                    <input type="text" name="father_name" class="form-control @error('father_name') is-invalid @enderror"
                           value="{{ old('father_name') }}" placeholder="Father's full name">
                    @error('father_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Mother's Name</label>
                    <input type="text" name="mother_name" class="form-control @error('mother_name') is-invalid @enderror"
                           value="{{ old('mother_name') }}" placeholder="Mother's full name">
                    @error('mother_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror"
                           value="{{ old('date_of_birth') }}" max="{{ now()->subYears(15)->format('Y-m-d') }}">
                    @error('date_of_birth')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                        <option value="">Select Gender</option>
                        <option value="male"   {{ old('gender') === 'male'   ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other"  {{ old('gender') === 'other'  ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('gender')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Phone Number</label>
                    <div class="input-group">
                        <span class="input-group-text" style="background:var(--bg);border-color:var(--border);">
                            <i class="fa-solid fa-phone" style="font-size:0.8rem;color:var(--muted);"></i>
                        </span>
                        <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror"
                               value="{{ old('phone') }}" placeholder="10-digit mobile">
                    </div>
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text" style="background:var(--bg);border-color:var(--border);">
                            <i class="fa-solid fa-envelope" style="font-size:0.8rem;color:var(--muted);"></i>
                        </span>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" placeholder="student@email.com">
                    </div>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control @error('address') is-invalid @enderror"
                           value="{{ old('address') }}" placeholder="Full residential address">
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- ── Section 2: Academic Details ───────────────────────────────────── --}}
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width:28px;height:28px;background:rgba(5,150,105,0.1);">
                    <span style="font-size:0.75rem;font-weight:700;color:#059669;">2</span>
                </div>
                <span>Academic Details</span>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Branch <span class="text-danger">*</span></label>
                    <select name="branch_id" class="form-select @error('branch_id') is-invalid @enderror" required>
                        <option value="">Select Branch</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}"
                                {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                                @if($branch->course)
                                    ({{ $branch->course->name }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('branch_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                    <select name="academic_year_id" class="form-select @error('academic_year_id') is-invalid @enderror" required>
                        <option value="">Select Academic Year</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}"
                                {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                                {{ $year->name ?? $year->year }}
                            </option>
                        @endforeach
                    </select>
                    @error('academic_year_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">10th Marks (%)</label>
                    <div class="input-group">
                        <input type="number" name="marks_10th" class="form-control @error('marks_10th') is-invalid @enderror"
                               value="{{ old('marks_10th') }}" placeholder="0.00" min="0" max="100" step="0.01">
                        <span class="input-group-text" style="background:var(--bg);border-color:var(--border);">%</span>
                    </div>
                    @error('marks_10th')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">12th Marks (%)</label>
                    <div class="input-group">
                        <input type="number" name="marks_12th" class="form-control @error('marks_12th') is-invalid @enderror"
                               value="{{ old('marks_12th') }}" placeholder="0.00" min="0" max="100" step="0.01">
                        <span class="input-group-text" style="background:var(--bg);border-color:var(--border);">%</span>
                    </div>
                    @error('marks_12th')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Current Semester</label>
                    <select name="current_semester" class="form-select @error('current_semester') is-invalid @enderror">
                        <option value="">Select Semester</option>
                        @for($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}" {{ old('current_semester') == $i ? 'selected' : '' }}>
                                Semester {{ $i }}
                            </option>
                        @endfor
                    </select>
                    @error('current_semester')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- ── Section 3: Category & Admission ──────────────────────────────── --}}
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width:28px;height:28px;background:rgba(217,119,6,0.1);">
                    <span style="font-size:0.75rem;font-weight:700;color:#d97706;">3</span>
                </div>
                <span>Category &amp; Admission</span>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Category <span class="text-danger">*</span></label>
                    <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                        <option value="">Select Category</option>
                        @foreach(['GEN' => 'General (GEN)', 'OBC' => 'OBC', 'SC' => 'SC', 'ST' => 'ST', 'EWS' => 'EWS', 'OTHER' => 'Other'] as $val => $label)
                            <option value="{{ $val }}" {{ old('category') === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('category')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Admission Date <span class="text-danger">*</span></label>
                    <input type="date" name="admission_date"
                           class="form-control @error('admission_date') is-invalid @enderror"
                           value="{{ old('admission_date', now()->format('Y-m-d')) }}" required>
                    @error('admission_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- ── Section 4: Vehicle ────────────────────────────────────────────── --}}
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width:28px;height:28px;background:rgba(124,58,237,0.1);">
                    <span style="font-size:0.75rem;font-weight:700;color:#7c3aed;">4</span>
                </div>
                <span>Vehicle / Transport</span>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox"
                               id="vehicle_opted" name="vehicle_opted"
                               value="1"
                               {{ old('vehicle_opted') ? 'checked' : '' }}
                               onchange="document.getElementById('vehicle-date-row').style.display = this.checked ? 'block' : 'none'">
                        <label class="form-check-label" for="vehicle_opted" style="font-weight:500;">
                            Student opted for college vehicle / transport
                        </label>
                    </div>
                </div>
                <div class="col-md-4" id="vehicle-date-row"
                     style="display:{{ old('vehicle_opted') ? 'block' : 'none' }};">
                    <label class="form-label">Vehicle Start Date</label>
                    <input type="date" name="vehicle_start_date"
                           class="form-control @error('vehicle_start_date') is-invalid @enderror"
                           value="{{ old('vehicle_start_date') }}">
                    @error('vehicle_start_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- ── Form Actions ──────────────────────────────────────────────────── --}}
    <div class="d-flex gap-3 justify-content-end">
        <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary px-4">
            <i class="fa-solid fa-xmark me-2"></i> Cancel
        </a>
        <button type="submit" class="btn btn-primary px-5">
            <i class="fa-solid fa-user-plus me-2"></i> Admit Student
        </button>
    </div>

</form>

@endsection

@push('scripts')
<script>
    // Client-side validation
    document.getElementById('student-form').addEventListener('submit', function (e) {
        const required = this.querySelectorAll('[required]');
        let valid = true;
        required.forEach(function (field) {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                valid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });
        if (!valid) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Required Fields Missing',
                text: 'Please fill in all required fields before submitting.',
                confirmButtonColor: '#4f46e5',
            });
        }
    });
</script>
@endpush

