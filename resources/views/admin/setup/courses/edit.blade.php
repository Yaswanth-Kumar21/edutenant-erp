@extends('layouts.app')
@section('title', 'Edit Course')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.setup.courses.index') }}" style="color:var(--primary);text-decoration:none;">Courses</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection
@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fa-solid fa-pen me-2" style="color:#4f46e5;"></i>Edit Course</h1>
    <a href="{{ route('admin.setup.courses.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Back</a>
</div>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.setup.courses.update', $course) }}">
                    @csrf @method('PUT')
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label">Course Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $course->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Code</label>
                            <input type="text" name="code" class="form-control" value="{{ old('code', $course->code) }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stream</label>
                        <select name="stream_id" class="form-select">
                            @foreach($streams as $stream)
                                <option value="{{ $stream->id }}" {{ $course->stream_id == $stream->id ? 'selected' : '' }}>{{ $stream->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Duration (Years)</label>
                            <input type="number" name="duration_years" class="form-control" value="{{ old('duration_years', $course->duration_years) }}" min="1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Total Semesters</label>
                            <input type="number" name="total_semesters" class="form-control" value="{{ old('total_semesters', $course->total_semesters) }}" min="1">
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-2"></i> Update Course</button>
                        <a href="{{ route('admin.setup.courses.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
