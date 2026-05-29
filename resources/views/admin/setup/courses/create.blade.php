@extends('layouts.app')
@section('title', 'Add Course')
@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fa-solid fa-book me-2" style="color:#059669;"></i>Add Course</h1>
    <a href="{{ route('admin.setup.courses.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Back
    </a>
</div>
<div class="card" style="max-width:600px;">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.setup.courses.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Stream <span class="text-danger">*</span></label>
                <select name="stream_id" class="form-select @error('stream_id') is-invalid @enderror" required>
                    <option value="">Select Stream</option>
                    @foreach($streams as $stream)
                        <option value="{{ $stream->id }}" {{ old('stream_id') == $stream->id ? 'selected' : '' }}>
                            {{ $stream->name }}
                        </option>
                    @endforeach
                </select>
                @error('stream_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Course Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" placeholder="e.g. B.Sc, BA, BCom" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Code</label>
                <input type="text" name="code" class="form-control" value="{{ old('code') }}" placeholder="e.g. BSC">
            </div>
            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label class="form-label">Duration (Years) <span class="text-danger">*</span></label>
                    <input type="number" name="duration_years" class="form-control" value="{{ old('duration_years', 3) }}" min="1" required>
                </div>
                <div class="col-6">
                    <label class="form-label">Total Semesters <span class="text-danger">*</span></label>
                    <input type="number" name="total_semesters" class="form-control" value="{{ old('total_semesters', 6) }}" min="1" required>
                </div>
            </div>
            <div class="mb-3">
                <div class="form-check">
                    <input type="checkbox" name="has_record_fee" value="1" class="form-check-input"
                           id="has_record_fee" {{ old('has_record_fee') ? 'checked' : '' }}>
                    <label class="form-check-label" for="has_record_fee">Has Record Fee</label>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-save me-1"></i> Save Course
                </button>
                <a href="{{ route('admin.setup.courses.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
