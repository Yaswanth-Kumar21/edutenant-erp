@extends('layouts.app')
@section('title', 'Edit Message')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.messages.index') }}" style="color:var(--primary);text-decoration:none;">Messages</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection
@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fa-solid fa-pen me-2" style="color:#7c3aed;"></i>Edit Message</h1>
    <a href="{{ route('admin.messages.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Back</a>
</div>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.messages.update', $message) }}">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" name="subject" class="form-control" value="{{ old('subject', $message->subject) }}">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Message Body <span class="text-danger">*</span></label>
                        <textarea name="body" class="form-control" rows="5" required>{{ old('body', $message->body) }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-2"></i> Update</button>
                        <a href="{{ route('admin.messages.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
