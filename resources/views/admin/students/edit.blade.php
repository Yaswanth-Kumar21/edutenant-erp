@extends('layouts.app')

@section('title', 'Edit — ' . $student->full_name)

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.students.index') }}" style="color:#4f46e5;text-decoration:none;">Students</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.students.show', $student) }}" style="color:#4f46e5;text-decoration:none;">
            {{ $student->full_name }}
        </a>
    </li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fa-solid fa-pen me-2" style="color:#4f46e5;"></i>
            Edit Student
        </h1>
        <p class="mb-0" style="color:var(--muted);font-size:0.875rem;">
            {{ $student->admission_number }} &bull; {{ $student->full_name }}
        </p>
    </div>
    <a href="{{ route('admin.students.show', $student) }}" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-2"></i> Back
    </a>
</div>

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

<form method="POST" action="{{ route('admin.students.update', $student) }}"
      enctype="multipart/form-data" id="edit-form">
    @csrf @method('PUT')

    <div class="row g-4">
        {{-- Left Column --}}
        <div class="col-lg-8">

            {{-- Personal Details --}}
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fa-solid fa-user me-2" style="color:#4f46e5;"></i>
                    Personal Details
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name"
                                   class="form-control @error('first_name') is-invalid @enderror"
                                   value="{{ old('first_name', $student->first_name) }}" required>
                            @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name"
                                   class="form-control @error('last_name') is-invalid @enderror"
                                   value="{{ old('last_name', $student->last_name) }}" required>
                            @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="date_of_birth"
                                   class="form-control @error('date_of_birth') is-invalid @enderror"
                                   value="{{ old('date_of_birth', $student->date_of_birth?->format('Y-m-d')) }}">
                            @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                                <option value="">Select</option>
                                <option value="male"   {{ old('gender', $student->gender) === 'male'   ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $student->gender) === 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other"  {{ old('gender', $student->gender) === 'other'  ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Blood Group</label>
                            <select name="blood_group" class="form-select">
                                <option value="">Select</option>
                                @foreach(['A+','A-','B+','B-','O+','O-','AB+','AB-'] as $bg)
                                    <option value="{{ $bg }}" {{ old('blood_group', $student->blood_group) === $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="tel" name="phone"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone', $student->phone) }}">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $student->email) }}">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Aadhaar Number</label>
                            <input type="text" name="aadhaar_number"
                                   class="form-control"
                                   value="{{ old('aadhaar_number', $student->aadhaar_number) }}"
                                   maxlength="20">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Address</label>
                            <input type="text" name="address"
                                   class="form-control"
                                   value="{{ old('address', $student->address) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control"
                                   value="{{ old('city', $student->city) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">State</label>
                            <input type="text" name="state" class="form-control"
                                   value="{{ old('state', $student->state) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Pincode</label>
                            <input type="text" name="pincode" class="form-control"
                                   value="{{ old('pincode', $student->pincode) }}" maxlength="10">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Academic Details --}}
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fa-solid fa-graduation-cap me-2" style="color:#059669;"></i>
                    Academic Details
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">10th Marks (%)</label>
                            <input type="number" name="marks_10th" class="form-control"
                                   value="{{ old('marks_10th', $student->marks_10th) }}"
                                   min="0" max="100" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">12th Marks (%)</label>
                            <input type="number" name="marks_12th" class="form-control"
                                   value="{{ old('marks_12th', $student->marks_12th) }}"
                                   min="0" max="100" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Current Semester</label>
                            <select name="current_semester" class="form-select">
                                @for($i = 1; $i <= 8; $i++)
                                    <option value="{{ $i }}" {{ old('current_semester', $student->current_semester) == $i ? 'selected' : '' }}>
                                        Semester {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">University Reg. Number</label>
                            <input type="text" name="university_reg_number" class="form-control"
                                   value="{{ old('university_reg_number', $student->university_reg_number) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                                @foreach(['GEN' => 'General (GEN)', 'OBC' => 'OBC', 'SC' => 'SC', 'ST' => 'ST', 'EWS' => 'EWS', 'OTHER' => 'Other'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('category', $student->category) === $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Guardian Details --}}
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fa-solid fa-users me-2" style="color:#7c3aed;"></i>
                    Guardian Details
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Father's Name</label>
                            <input type="text" name="father_name" class="form-control"
                                   value="{{ old('father_name', $student->guardian?->father_name ?? $student->father_name) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Father's Phone</label>
                            <input type="tel" name="father_phone" class="form-control"
                                   value="{{ old('father_phone', $student->guardian?->father_phone) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Father's Occupation</label>
                            <input type="text" name="father_occupation" class="form-control"
                                   value="{{ old('father_occupation', $student->guardian?->father_occupation) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mother's Name</label>
                            <input type="text" name="mother_name" class="form-control"
                                   value="{{ old('mother_name', $student->guardian?->mother_name ?? $student->mother_name) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mother's Phone</label>
                            <input type="tel" name="mother_phone" class="form-control"
                                   value="{{ old('mother_phone', $student->guardian?->mother_phone) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Annual Family Income (₹)</label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:var(--bg);border-color:var(--border);">₹</span>
                                <input type="number" name="annual_income" class="form-control"
                                       value="{{ old('annual_income', $student->guardian?->annual_income) }}" min="0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="col-lg-4">
            {{-- Photo --}}
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fa-solid fa-camera me-2" style="color:#4f46e5;"></i>
                    Profile Photo
                </div>
                <div class="card-body text-center">
                    <img id="photo-preview" src="{{ $student->photo_url }}"
                         alt="{{ $student->full_name }}"
                         class="rounded-circle mb-3"
                         style="width:100px;height:100px;object-fit:cover;border:2px solid var(--border);">
                    <div>
                        <label for="photo-input" class="btn btn-outline-primary btn-sm">
                            <i class="fa-solid fa-upload me-1"></i> Change Photo
                        </label>
                        <input type="file" id="photo-input" name="photo"
                               accept="image/*" class="d-none"
                               onchange="previewPhoto(this)">
                    </div>
                    <div class="mt-1" style="font-size:0.72rem;color:var(--muted);">
                        JPG/PNG, max 2MB
                    </div>
                </div>
            </div>

            {{-- Status --}}
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fa-solid fa-toggle-on me-2" style="color:#059669;"></i>
                    Status &amp; Options
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Student Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="active"     {{ old('status', $student->status) === 'active'     ? 'selected' : '' }}>Active</option>
                            <option value="inactive"   {{ old('status', $student->status) === 'inactive'   ? 'selected' : '' }}>Inactive</option>
                            <option value="passed_out" {{ old('status', $student->status) === 'passed_out' ? 'selected' : '' }}>Passed Out</option>
                            <option value="dropped"    {{ old('status', $student->status) === 'dropped'    ? 'selected' : '' }}>Dropped</option>
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="scholarship_eligible"
                               name="scholarship_eligible" value="1"
                               {{ old('scholarship_eligible', $student->scholarship_eligible) ? 'checked' : '' }}>
                        <label class="form-check-label" for="scholarship_eligible" style="font-size:0.875rem;">
                            Scholarship Eligible
                        </label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="vehicle_opted"
                               name="vehicle_opted" value="1"
                               {{ old('vehicle_opted', $student->vehicle_opted) ? 'checked' : '' }}
                               onchange="document.getElementById('vehicle-date-row').style.display = this.checked ? 'block' : 'none'">
                        <label class="form-check-label" for="vehicle_opted" style="font-size:0.875rem;">
                            College Vehicle
                        </label>
                    </div>
                    <div id="vehicle-date-row" class="mt-2"
                         style="display:{{ old('vehicle_opted', $student->vehicle_opted) ? 'block' : 'none' }};">
                        <label class="form-label" style="font-size:0.82rem;">Vehicle Start Date</label>
                        <input type="date" name="vehicle_start_date" class="form-control form-control-sm"
                               value="{{ old('vehicle_start_date', $student->vehicle_start_date?->format('Y-m-d')) }}">
                    </div>
                </div>
            </div>

            {{-- Admission Info (read-only) --}}
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fa-solid fa-info-circle me-2" style="color:var(--muted);"></i>
                    Admission Info
                </div>
                <div class="card-body" style="font-size:0.875rem;">
                    <div class="d-flex justify-content-between mb-2">
                        <span style="color:var(--muted);">Adm. Number</span>
                        <strong style="font-family:monospace;color:#4f46e5;">{{ $student->admission_number }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span style="color:var(--muted);">Adm. Date</span>
                        <strong>{{ $student->admission_date?->format('d M Y') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span style="color:var(--muted);">Branch</span>
                        <strong>{{ $student->branch?->name ?? '—' }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="d-flex gap-3 justify-content-end mt-2">
        <a href="{{ route('admin.students.show', $student) }}" class="btn btn-outline-secondary px-4">
            <i class="fa-solid fa-xmark me-2"></i> Cancel
        </a>
        <button type="submit" class="btn btn-primary px-5">
            <i class="fa-solid fa-floppy-disk me-2"></i> Save Changes
        </button>
    </div>
</form>

@endsection

@push('scripts')
<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('photo-preview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush

