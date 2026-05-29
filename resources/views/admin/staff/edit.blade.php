@extends('layouts.app')
@section('title', 'Edit Staff — ' . $staff->name)

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-pen me-2" style="color:#4f46e5;"></i>Edit Staff — {{ $staff->name }}</h1>
    </div>
    <a href="{{ route('admin.staff.show', $staff) }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Back
    </a>
</div>

<form method="POST" action="{{ route('admin.staff.update', $staff) }}" enctype="multipart/form-data">
    @csrf @method('PATCH')

    <div class="row g-4">
        <div class="col-lg-8">

            <div class="card mb-4">
                <div class="card-header"><h6 class="mb-0 fw-600" style="font-weight:600;">Basic Information</h6></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $staff->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Staff Type <span class="text-danger">*</span></label>
                            <select name="staff_type" class="form-select" required>
                                <option value="teaching"     {{ old('staff_type', $staff->staff_type) === 'teaching'     ? 'selected' : '' }}>Teaching</option>
                                <option value="non_teaching" {{ old('staff_type', $staff->staff_type) === 'non_teaching' ? 'selected' : '' }}>Non-Teaching</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Designation</label>
                            <input type="text" name="designation" class="form-control" value="{{ old('designation', $staff->designation) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Department</label>
                            <input type="text" name="department" class="form-control" value="{{ old('department', $staff->department) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Staff Role</label>
                            <select name="staff_role_id" class="form-select">
                                <option value="">Select Role</option>
                                @foreach($staffRoles as $role)
                                    <option value="{{ $role->id }}" {{ old('staff_role_id', $staff->staff_role_id) == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Subject</label>
                            <input type="text" name="subject" class="form-control" value="{{ old('subject', $staff->subject) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Qualification</label>
                            <input type="text" name="qualification" class="form-control" value="{{ old('qualification', $staff->qualification) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active"   {{ old('status', $staff->status) === 'active'   ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $staff->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="resigned" {{ old('status', $staff->status) === 'resigned' ? 'selected' : '' }}>Resigned</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="">Select</option>
                                <option value="male"   {{ old('gender', $staff->gender) === 'male'   ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $staff->gender) === 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other"  {{ old('gender', $staff->gender) === 'other'  ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $staff->date_of_birth?->toDateString()) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date of Joining</label>
                            <input type="date" name="date_of_joining" class="form-control" value="{{ old('date_of_joining', $staff->date_of_joining?->toDateString()) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $staff->email) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $staff->phone) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="2">{{ old('address', $staff->address) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h6 class="mb-0 fw-600" style="font-weight:600;">Salary Details</h6></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Monthly Salary <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="monthly_salary" class="form-control"
                                       value="{{ old('monthly_salary', $staff->monthly_salary) }}" min="0" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Basic Salary</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="basic_salary" class="form-control" value="{{ old('basic_salary', $staff->basic_salary) }}" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">HRA</label>
                            <div class="input-group"><span class="input-group-text">₹</span>
                            <input type="number" name="hra" class="form-control" value="{{ old('hra', $staff->hra) }}" min="0" step="0.01"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">DA</label>
                            <div class="input-group"><span class="input-group-text">₹</span>
                            <input type="number" name="da" class="form-control" value="{{ old('da', $staff->da) }}" min="0" step="0.01"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Other Allowances</label>
                            <div class="input-group"><span class="input-group-text">₹</span>
                            <input type="number" name="other_allowances" class="form-control" value="{{ old('other_allowances', $staff->other_allowances) }}" min="0" step="0.01"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">PF Deduction</label>
                            <div class="input-group"><span class="input-group-text">₹</span>
                            <input type="number" name="pf_deduction" class="form-control" value="{{ old('pf_deduction', $staff->pf_deduction) }}" min="0" step="0.01"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tax Deduction</label>
                            <div class="input-group"><span class="input-group-text">₹</span>
                            <input type="number" name="tax_deduction" class="form-control" value="{{ old('tax_deduction', $staff->tax_deduction) }}" min="0" step="0.01"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Allowed Holidays/Month</label>
                            <input type="number" name="allowed_holidays_per_month" class="form-control"
                                   value="{{ old('allowed_holidays_per_month', $staff->allowed_holidays_per_month ?? 2) }}" min="0" max="30">
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header"><h6 class="mb-0 fw-600" style="font-weight:600;">Photo</h6></div>
                <div class="card-body text-center">
                    <img id="preview-img" src="{{ $staff->photo_url }}"
                         class="rounded-circle mb-3" style="width:100px;height:100px;object-fit:cover;">
                    <input type="file" name="photo" id="photo-input" class="form-control form-control-sm" accept="image/*">
                    <div class="form-text">Leave blank to keep current photo</div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="fa-solid fa-save me-2"></i> Update Staff
                    </button>
                    <a href="{{ route('admin.staff.show', $staff) }}" class="btn btn-outline-secondary w-100">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
document.getElementById('photo-input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById('preview-img').src = e.target.result;
        reader.readAsDataURL(file);
    }
});
</script>
@endpush
