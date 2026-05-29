@extends('layouts.app')

@section('title', 'Fee Structure')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.fees.structures.index') }}" style="color:#4f46e5;text-decoration:none;">Fee Structures</a></li>
    <li class="breadcrumb-item active">Details</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-layer-group me-2" style="color:#4f46e5;"></i>Fee Structure Details</h1>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.fees.structures.edit', $structure) }}" class="btn btn-outline-primary">
            <i class="fa-solid fa-pen me-2"></i> Edit
        </a>
        <a href="{{ route('admin.fees.structures.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-2"></i> Back
        </a>
    </div>
</div>

<div class="card" style="max-width:600px;">
    <div class="card-header"><i class="fa-solid fa-layer-group me-2" style="color:#4f46e5;"></i>Structure Details</div>
    <div class="card-body">
        <dl class="row mb-0" style="font-size:0.875rem;">
            <dt class="col-4" style="color:var(--muted);">Fee Type</dt>
            <dd class="col-8 mb-3" style="font-weight:600;">{{ $structure->feeType?->name ?? '—' }}</dd>

            <dt class="col-4" style="color:var(--muted);">Amount</dt>
            <dd class="col-8 mb-3" style="font-size:1.25rem;font-weight:800;color:#4f46e5;">₹{{ number_format($structure->amount, 2) }}</dd>

            <dt class="col-4" style="color:var(--muted);">Branch</dt>
            <dd class="col-8 mb-3">{{ $structure->branch?->name ?? 'All Branches' }}</dd>

            <dt class="col-4" style="color:var(--muted);">Stream</dt>
            <dd class="col-8 mb-3">{{ $structure->stream?->name ?? 'All Streams' }}</dd>

            <dt class="col-4" style="color:var(--muted);">Academic Year</dt>
            <dd class="col-8 mb-3">{{ $structure->academicYear?->name ?? 'All Years' }}</dd>

            <dt class="col-4" style="color:var(--muted);">Semester</dt>
            <dd class="col-8 mb-3">{{ $structure->semester ? 'Semester '.$structure->semester : 'All Semesters' }}</dd>

            <dt class="col-4" style="color:var(--muted);">Status</dt>
            <dd class="col-8 mb-0">
                @if($structure->is_active)
                    <span class="badge" style="background:#dcfce7;color:#166534;">Active</span>
                @else
                    <span class="badge" style="background:#fee2e2;color:#991b1b;">Inactive</span>
                @endif
            </dd>
        </dl>
    </div>
</div>
@endsection

