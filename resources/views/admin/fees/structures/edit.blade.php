@extends('layouts.app')

@section('title', 'Edit Fee Structure')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.fees.structures.index') }}" style="color:#4f46e5;text-decoration:none;">Fee Structures</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-pen me-2" style="color:#4f46e5;"></i>Edit Fee Structure</h1>
    </div>
    <a href="{{ route('admin.fees.structures.index') }}" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-2"></i> Back
    </a>
</div>

<div class="card" style="max-width:700px;">
    <div class="card-header"><i class="fa-solid fa-layer-group me-2" style="color:#4f46e5;"></i>Structure Details</div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.fees.structures.update', $structure) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Fee Type <span class="text-danger">*</span></label>
                    <select name="fee_type_id" class="form-select @error('fee_type_id') is-invalid @enderror" required>
                        @foreach($feeTypes as $ft)
                            <option value="{{ $ft->id }}" {{ old('fee_type_id', $structure->fee_type_id) == $ft->id ? 'selected' : '' }}>{{ $ft->name }}</option>
                        @endforeach
                    </select>
                    @error('fee_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Amount (₹) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text" style="background:var(--bg);border-color:var(--border);">₹</span>
                        <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror"
                               value="{{ old('amount', $structure->amount) }}" min="0" step="0.01" required>
                    </div>
                    @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Branch</label>
                    <select name="branch_id" class="form-select">
                        <option value="">All Branches</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ old('branch_id', $structure->branch_id) == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Academic Year</label>
                    <select name="academic_year_id" class="form-select">
                        <option value="">All Years</option>
                        @foreach($academicYears as $yr)
                            <option value="{{ $yr->id }}" {{ old('academic_year_id', $structure->academic_year_id) == $yr->id ? 'selected' : '' }}>{{ $yr->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Semester</label>
                    <select name="semester" class="form-select">
                        <option value="">All</option>
                        @for($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}" {{ old('semester', $structure->semester) == $i ? 'selected' : '' }}>Semester {{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                               id="is_active" {{ old('is_active', $structure->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active" style="font-weight:500;">Active</label>
                    </div>
                </div>
                <div class="col-12 d-flex gap-2 pt-2">
                    <button type="submit" class="btn btn-primary px-5"><i class="fa-solid fa-floppy-disk me-2"></i> Update</button>
                    <a href="{{ route('admin.fees.structures.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
