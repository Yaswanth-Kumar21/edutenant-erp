@extends('layouts.app')
@section('title', 'Add Branch')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.setup.branches.index') }}" style="color:var(--primary);text-decoration:none;">Branches</a></li>
    <li class="breadcrumb-item active">Add</li>
@endsection
@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fa-solid fa-plus me-2" style="color:#4f46e5;"></i>Add Branch</h1>
    <a href="{{ route('admin.setup.branches.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Back</a>
</div>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.setup.branches.store') }}">
                    @csrf
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label">Branch Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. Computer Science A" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Code</label>
                            <input type="text" name="code" class="form-control" value="{{ old('code') }}" placeholder="e.g. CSA">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Course <span class="text-danger">*</span></label>
                        <select name="course_id" class="form-select @error('course_id') is-invalid @enderror" required>
                            <option value="">Select Course</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->stream?->name }} — {{ $course->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('course_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Intake Capacity <span class="text-danger">*</span></label>
                            <input type="number" name="intake_capacity" class="form-control" value="{{ old('intake_capacity', 60) }}" min="1" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tuition Fee (Student) <span class="text-danger">*</span></label>
                            <input type="number" name="tuition_fee_student" class="form-control" value="{{ old('tuition_fee_student', 0) }}" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tuition Fee (Govt) <span class="text-danger">*</span></label>
                            <input type="number" name="tuition_fee_govt" class="form-control" value="{{ old('tuition_fee_govt', 0) }}" min="0" step="0.01" required>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-2"></i> Save Branch</button>
                        <a href="{{ route('admin.setup.branches.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
