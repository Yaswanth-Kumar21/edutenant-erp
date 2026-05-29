@extends('layouts.app')
@section('title', 'Edit Academic Year')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.setup.academic-years.index') }}" style="color:var(--primary);text-decoration:none;">Academic Years</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')

<div class="page-header">
    <h1 class="page-title"><i class="fa-solid fa-pen me-2" style="color:#4f46e5;"></i>Edit Academic Year</h1>
    <a href="{{ route('admin.setup.academic-years.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Back</a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.setup.academic-years.update', $academicYear) }}">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Year Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $academicYear->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $academicYear->start_date?->toDateString()) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $academicYear->end_date?->toDateString()) }}">
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="form-check">
                            <input type="checkbox" name="is_current" value="1" class="form-check-input" id="is_current" {{ $academicYear->is_current ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_current">Set as Current Academic Year</label>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-2"></i> Update</button>
                        <a href="{{ route('admin.setup.academic-years.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
