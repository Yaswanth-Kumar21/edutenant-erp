@extends('layouts.app')
@section('title', 'Add Stream')
@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fa-solid fa-layer-group me-2" style="color:#4f46e5;"></i>Add Stream</h1>
    <a href="{{ route('admin.setup.streams.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Back
    </a>
</div>
<div class="card" style="max-width:600px;">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.setup.streams.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Stream Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" placeholder="e.g. Science, Arts, Commerce" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Code</label>
                <input type="text" name="code" class="form-control" value="{{ old('code') }}" placeholder="e.g. SCI, ARTS">
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-save me-1"></i> Save Stream
                </button>
                <a href="{{ route('admin.setup.streams.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
