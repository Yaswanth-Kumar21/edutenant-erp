@extends('layouts.app')
@section('title', 'Add Fee Type')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.fees.types.index') }}" style="color:var(--primary);text-decoration:none;">Fee Types</a></li>
    <li class="breadcrumb-item active">Add</li>
@endsection
@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fa-solid fa-plus me-2" style="color:#4f46e5;"></i>Add Fee Type</h1>
    <a href="{{ route('admin.fees.types.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Back</a>
</div>
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.fees.types.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Fee Type Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" placeholder="e.g. Tuition Fee" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                               value="{{ old('code') }}" placeholder="e.g. TUITION" style="text-transform:uppercase;" required>
                        <div class="form-text">Unique identifier. Use uppercase letters only.</div>
                        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Frequency <span class="text-danger">*</span></label>
                        <select name="frequency" class="form-select @error('frequency') is-invalid @enderror" required>
                            <option value="">Select Frequency</option>
                            <option value="one_time"     {{ old('frequency') === 'one_time'     ? 'selected' : '' }}>One Time</option>
                            <option value="per_semester" {{ old('frequency') === 'per_semester' ? 'selected' : '' }}>Per Semester</option>
                            <option value="per_year"     {{ old('frequency') === 'per_year'     ? 'selected' : '' }}>Per Year</option>
                            <option value="monthly"      {{ old('frequency') === 'monthly'      ? 'selected' : '' }}>Monthly</option>
                        </select>
                        @error('frequency')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Default Amount (₹) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror"
                               value="{{ old('amount', 0) }}" min="0" step="0.01" required>
                        @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-2"></i> Save Fee Type</button>
                        <a href="{{ route('admin.fees.types.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
