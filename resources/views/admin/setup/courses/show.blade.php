@extends('layouts.app')
@section('title', 'Course Details')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.setup.courses.index') }}" style="color:var(--primary);text-decoration:none;">Courses</a></li>
    <li class="breadcrumb-item active">{{ $course->name }}</li>
@endsection
@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fa-solid fa-book me-2" style="color:#4f46e5;"></i>{{ $course->name }}</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.setup.courses.edit', $course) }}" class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-pen me-1"></i> Edit</a>
        <a href="{{ route('admin.setup.courses.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Back</a>
    </div>
</div>
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <dl class="row mb-0" style="font-size:.875rem;">
                    <dt class="col-4" style="color:var(--muted);">Name</dt><dd class="col-8 mb-3" style="font-weight:600;">{{ $course->name }}</dd>
                    <dt class="col-4" style="color:var(--muted);">Code</dt><dd class="col-8 mb-3" style="font-family:monospace;color:#4f46e5;">{{ $course->code ?? '—' }}</dd>
                    <dt class="col-4" style="color:var(--muted);">Stream</dt><dd class="col-8 mb-3">{{ $course->stream?->name ?? '—' }}</dd>
                    <dt class="col-4" style="color:var(--muted);">Duration</dt><dd class="col-8 mb-3">{{ $course->duration_years ?? '—' }} Year(s)</dd>
                    <dt class="col-4" style="color:var(--muted);">Semesters</dt><dd class="col-8 mb-0">{{ $course->total_semesters ?? '—' }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection

