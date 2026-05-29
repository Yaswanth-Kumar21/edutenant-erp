@extends('layouts.app')
@section('title', 'Stream Details')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.setup.streams.index') }}" style="color:var(--primary);text-decoration:none;">Streams</a></li>
    <li class="breadcrumb-item active">{{ $stream->name }}</li>
@endsection
@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fa-solid fa-layer-group me-2" style="color:#4f46e5;"></i>{{ $stream->name }}</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.setup.streams.edit', $stream) }}" class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-pen me-1"></i> Edit</a>
        <a href="{{ route('admin.setup.streams.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Back</a>
    </div>
</div>
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <dl class="row mb-0" style="font-size:.875rem;">
                    <dt class="col-4" style="color:var(--muted);">Name</dt>
                    <dd class="col-8 mb-3" style="font-weight:600;">{{ $stream->name }}</dd>
                    <dt class="col-4" style="color:var(--muted);">Code</dt>
                    <dd class="col-8 mb-3" style="font-family:monospace;color:#4f46e5;">{{ $stream->code ?? '—' }}</dd>
                    <dt class="col-4" style="color:var(--muted);">Courses</dt>
                    <dd class="col-8 mb-0">{{ $stream->courses?->count() ?? 0 }} course(s)</dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection

