@extends('layouts.app')
@section('title', 'Fee Type Details')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.fees.types.index') }}" style="color:var(--primary);text-decoration:none;">Fee Types</a></li>
    <li class="breadcrumb-item active">{{ $type->name }}</li>
@endsection
@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fa-solid fa-tag me-2" style="color:#4f46e5;"></i>{{ $type->name }}</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.fees.types.edit', $type) }}" class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-pen me-1"></i> Edit</a>
        <a href="{{ route('admin.fees.types.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Back</a>
    </div>
</div>
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <dl class="row mb-0" style="font-size:.875rem;">
                    <dt class="col-4" style="color:var(--muted);">Name</dt>
                    <dd class="col-8 mb-3" style="font-weight:600;">{{ $type->name }}</dd>
                    <dt class="col-4" style="color:var(--muted);">Code</dt>
                    <dd class="col-8 mb-3"><span style="font-family:monospace;color:#4f46e5;font-weight:600;">{{ $type->code }}</span></dd>
                    <dt class="col-4" style="color:var(--muted);">Frequency</dt>
                    <dd class="col-8 mb-3">
                        @php $fc = ['one_time'=>['#dbeafe','#1e40af'],'per_semester'=>['#dcfce7','#166534'],'per_year'=>['#fef3c7','#92400e'],'monthly'=>['#f3e8ff','#7c3aed']][$type->frequency] ?? ['#f3f4f6','#374151']; @endphp
                        <span class="badge" style="background:{{ $fc[0] }};color:{{ $fc[1] }};">{{ ucfirst(str_replace('_',' ',$type->frequency)) }}</span>
                    </dd>
                    <dt class="col-4" style="color:var(--muted);">Default Amount</dt>
                    <dd class="col-8 mb-3" style="font-weight:700;color:#059669;font-size:1rem;">?{{ number_format($type->amount ?? 0, 2) }}</dd>
                    <dt class="col-4" style="color:var(--muted);">Status</dt>
                    <dd class="col-8 mb-0">
                        @if($type->is_active ?? true)
                            <span class="badge" style="background:#dcfce7;color:#166534;">Active</span>
                        @else
                            <span class="badge" style="background:#fee2e2;color:#991b1b;">Inactive</span>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection

