@extends('layouts.app')
@section('title', 'Add Staff Member')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-user-plus me-2" style="color:#059669;"></i>Add Staff Member</h1>
    </div>
    <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Back
    </a>
</div>

<form method="POST" action="{{ route('admin.staff.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="row g-4">
        {{-- Left Column --}}
        <div class="col-lg-8">

            {{-- Basic Info --}}
            <div class="card mb-4">
                <div class="card-header"><h6 class="mb-0 fw-600" style="font-weight:600;"><i class="fa-solid fa-user me-2" style="color:#4f46e5;"></i>Basic Information</h6></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" placeholder="Enter full name" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Staff Type <span class="text-danger">*</span></label>
                            <select name="staff_type" class="form-select @error('staff_type') is-invalid @enderror" required>
                                <option value="">Select Type</option>
                                <option value="teaching"     {{ old('staff_type') === 'teaching'     ? 'selected' : '' }}>Teaching</option>
                                <option value="non_teaching" {{ old('staff_type') === 'non_teaching' ? 'selected' : '' }}>Non-Teaching</option>
                            </select>
                            @error('staff_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Designation</label>
                            <input type="text" name="designation" class="form-control"
                                   value="{{ old('designation') }}" placeholder="e.g. Professor, Lecturer, Clerk">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Department</label>
                            <input type="text" name="department" class="form-control"
                                   value="{{ old('department') }}" placeholder="e.g. Science, Commerce">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Staff Role</label>
                            <select name="staff_role_id" class="form-select">
                                <option value="">Select Role</option>
                                @foreach($staffRoles as $role)
                                    <option value="{{ $role->id }}" {{ old('staff_role_id') == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Subject (Teaching)</label>
                            <input type="text" name="subject" class="form-control"
                                   value="{{ old('subject') }}" placeholder="e.g. Mathematics, Physics">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Qualification</label>
                            <input type="text" name="qualification" class="form-control"
                                   value="{{ old('qualification') }}" placeholder="e.g. M.Sc, B.Ed, MBA">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="">Select</option>
                                <option value="male"   {{ old('gender') === 'male'   ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other"  {{ old('gender') === 'other'  ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date of Joining</label>
                            <input type="date" name="date_of_joining" class="form-control" value="{{ old('date_of_joining') }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Contact Info --}}
            <div class="card mb-4">
                <div class="card-header"><h6 class="mb-0 fw-600" style="font-weight:600;"><i class="fa-solid fa-phone me-2" style="color:#059669;"></i>Contact Information</h6></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="staff@example.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="10-digit mobile">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="2" placeholder="Full address">{{ old('address') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Salary Details --}}
            <div class="card mb-4">
                <div class="card-header"><h6 class="mb-0 fw-600" style="font-weight:600;"><i class="fa-solid fa-indian-rupee-sign me-2" style="color:#d97706;"></i>Salary Details</h6></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Monthly Salary (Gross) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="monthly_salary" class="form-control @error('monthly_salary') is-invalid @enderror"
                                       value="{{ old('monthly_salary', 0) }}" min="0" step="0.01" required>
                            </div>
                            @error('monthly_salary')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Basic Salary</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="basic_salary" class="form-control" value="{{ old('basic_salary', 0) }}" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">HRA</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="hra" class="form-control" value="{{ old('hra', 0) }}" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">DA</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="da" class="form-control" value="{{ old('da', 0) }}" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Other Allowances</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="other_allowances" class="form-control" value="{{ old('other_allowances', 0) }}" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">PF Deduction</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="pf_deduction" class="form-control" value="{{ old('pf_deduction', 0) }}" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tax Deduction</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="tax_deduction" class="form-control" value="{{ old('tax_deduction', 0) }}" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Allowed Holidays/Month</label>
                            <input type="number" name="allowed_holidays_per_month" class="form-control"
                                   value="{{ old('allowed_holidays_per_month', 2) }}" min="0" max="30">
                            <div class="form-text">Default: 2 holidays per month</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bank Details --}}
            <div class="card mb-4">
                <div class="card-header"><h6 class="mb-0 fw-600" style="font-weight:600;"><i class="fa-solid fa-building-columns me-2" style="color:#0891b2;"></i>Bank Details</h6></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Bank Account Number</label>
                            <input type="text" name="bank_account" class="form-control" value="{{ old('bank_account') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Bank Name</label>
                            <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">IFSC Code</label>
                            <input type="text" name="ifsc_code" class="form-control" value="{{ old('ifsc_code') }}" placeholder="e.g. SBIN0001234">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Aadhaar Number</label>
                            <input type="text" name="aadhaar_number" class="form-control" value="{{ old('aadhaar_number') }}" maxlength="20">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">PAN Number</label>
                            <input type="text" name="pan_number" class="form-control" value="{{ old('pan_number') }}" maxlength="20">
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Right Column --}}
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header"><h6 class="mb-0 fw-600" style="font-weight:600;">Photo</h6></div>
                <div class="card-body text-center">
                    <div id="photo-preview" class="mb-3">
                        <img id="preview-img" src="https://ui-avatars.com/api/?name=Staff&background=059669&color=fff&size=128"
                             class="rounded-circle" style="width:100px;height:100px;object-fit:cover;">
                    </div>
                    <input type="file" name="photo" id="photo-input" class="form-control form-control-sm" accept="image/*">
                    <div class="form-text">Max 2MB. JPG, PNG.</div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="fa-solid fa-save me-2"></i> Add Staff Member
                    </button>
                    <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary w-100">
                        Cancel
                    </a>
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
