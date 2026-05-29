@extends('layouts.app')
@section('title', 'Edit Stream')
@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fa-solid fa-pen me-2" style="color:#4f46e5;"></i>Edit Stream</h1>
    <a href="{{ route('admin.setup.streams.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Back
    </a>
</div>
<div class="card" style="max-width:600px;">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.setup.streams.update', $stream) }}">
            @csrf @method('PATCH')
            <div class="mb-3">
                <label class="form-label">Stream Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $stream->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Code</label>
                <input type="text" name="code" class="form-control" value="{{ old('code', $stream->code) }}">
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-save me-1"></i> Update Stream
                </button>
                <a href="{{ route('admin.setup.streams.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
