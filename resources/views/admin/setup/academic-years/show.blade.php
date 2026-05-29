@extends('layouts.app')
@section('title', 'Academic Year')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.setup.academic-years.index') }}" style="color:var(--primary);text-decoration:none;">Academic Years</a></li>
    <li class="breadcrumb-item active">{{ $academicYear->name }}</li>
@endsection
@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fa-solid fa-calendar me-2" style="color:#4f46e5;"></i>{{ $academicYear->name }}</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.setup.academic-years.edit', $academicYear) }}" class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-pen me-1"></i> Edit</a>
        <a href="{{ route('admin.setup.academic-years.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Back</a>
    </div>
</div>
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <dl class="row mb-0" style="font-size:.875rem;">
                    <dt class="col-4" style="color:var(--muted);">Name</dt><dd class="col-8 mb-3" style="font-weight:600;">{{ $academicYear->name }}</dd>
                    <dt class="col-4" style="color:var(--muted);">Start Date</dt><dd class="col-8 mb-3">{{ $academicYear->start_date?->format('d M Y') ?? '—' }}</dd>
                    <dt class="col-4" style="color:var(--muted);">End Date</dt><dd class="col-8 mb-3">{{ $academicYear->end_date?->format('d M Y') ?? '—' }}</dd>
                    <dt class="col-4" style="color:var(--muted);">Status</dt>
                    <dd class="col-8 mb-0">
                        @if($academicYear->is_current)
                            <span class="badge" style="background:#dcfce7;color:#166534;">Current</span>
                        @else
                            <span class="badge" style="background:#f3f4f6;color:#6b7280;">Inactive</span>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection

