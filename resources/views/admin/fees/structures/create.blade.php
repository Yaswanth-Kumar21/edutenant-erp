@extends('layouts.app')

@section('title', 'Add Fee Structure')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.fees.structures.index') }}" style="color:#4f46e5;text-decoration:none;">Fee Structures</a></li>
    <li class="breadcrumb-item active">Add</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-plus me-2" style="color:#4f46e5;"></i>Add Fee Structure</h1>
        <p class="mb-0" style="color:var(--muted);font-size:0.875rem;">Define fee amount for a branch/semester combination</p>
    </div>
    <a href="{{ route('admin.fees.structures.index') }}" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-2"></i> Back
    </a>
</div>

@if($errors->any())
<div class="alert alert-danger d-flex gap-2 mb-4" style="border-radius:0.75rem;">
    <i class="fa-solid fa-circle-exclamation mt-1 flex-shrink-0"></i>
    <div><strong>Please fix errors:</strong><ul class="mb-0 mt-1 ps-3">@foreach($errors->all() as $e)<li style="font-size:0.875rem;">{{ $e }}</li>@endforeach</ul></div>
</div>
@endif

<div class="card" style="max-width:700px;">
    <div class="card-header"><i class="fa-solid fa-layer-group me-2" style="color:#4f46e5;"></i>Structure Details</div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.fees.structures.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Fee Type <span class="text-danger">*</span></label>
                    <select name="fee_type_id" class="form-select @error('fee_type_id') is-invalid @enderror" required>
                        <option value="">— Select Fee Type —</option>
                        @foreach($feeTypes as $ft)
                            <option value="{{ $ft->id }}" {{ old('fee_type_id') == $ft->id ? 'selected' : '' }}>{{ $ft->name }}</option>
                        @endforeach
                    </select>
                    @error('fee_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Amount (₹) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text" style="background:var(--bg);border-color:var(--border);">₹</span>
                        <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror"
                               value="{{ old('amount', 0) }}" min="0" step="0.01" required>
                    </div>
                    @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Branch <small style="color:var(--muted);">(leave blank = all branches)</small></label>
                    <select name="branch_id" class="form-select @error('branch_id') is-invalid @enderror">
                        <option value="">All Branches</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ old('branch_id') == $b->id ? 'selected' : '' }}>
                                {{ $b->name }} ({{ $b->course?->name }})
                            </option>
                        @endforeach
                    </select>
                    @error('branch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Stream <small style="color:var(--muted);">(optional)</small></label>
                    <select name="stream_id" class="form-select">
                        <option value="">All Streams</option>
                        @foreach($streams as $st)
                            <option value="{{ $st->id }}" {{ old('stream_id') == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Academic Year</label>
                    <select name="academic_year_id" class="form-select">
                        <option value="">All Years</option>
                        @foreach($academicYears as $yr)
                            <option value="{{ $yr->id }}" {{ old('academic_year_id') == $yr->id ? 'selected' : '' }}>
                                {{ $yr->name }} @if($yr->is_current) (Current) @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Semester</label>
                    <select name="semester" class="form-select">
                        <option value="">All Semesters</option>
                        @for($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}" {{ old('semester') == $i ? 'selected' : '' }}>Semester {{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Year</label>
                    <select name="year" class="form-select">
                        <option value="">All Years</option>
                        @for($i = 1; $i <= 6; $i++)
                            <option value="{{ $i }}" {{ old('year') == $i ? 'selected' : '' }}>Year {{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                               id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active" style="font-weight:500;">Active</label>
                    </div>
                </div>
                <div class="col-12 d-flex gap-2 pt-2">
                    <button type="submit" class="btn btn-primary px-5">
                        <i class="fa-solid fa-floppy-disk me-2"></i> Save Structure
                    </button>
                    <a href="{{ route('admin.fees.structures.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

