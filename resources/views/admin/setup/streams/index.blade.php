@extends('layouts.app')
@section('title', 'Streams')
@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-layer-group me-2" style="color:#4f46e5;"></i>Streams</h1>
        <p class="mb-0" style="color:var(--muted);font-size:.875rem;">Manage academic streams (Science, Arts, Commerce)</p>
    </div>
    <a href="{{ route('admin.setup.streams.create') }}" class="btn btn-primary btn-sm">
        <i class="fa-solid fa-plus me-1"></i> Add Stream
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    <i class="fa-solid fa-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card">
    <div class="card-header">
        <span style="font-weight:600;">All Streams
            <span class="badge ms-2" style="background:rgba(79,70,229,.1);color:#4f46e5;">{{ $streams->count() }}</span>
        </span>
    </div>

    @if($streams->isEmpty())
    <div class="card-body">
        <div class="empty-state py-4">
            <i class="fa-solid fa-layer-group" style="font-size:3rem;opacity:.2;"></i>
            <h5 class="mt-3" style="color:var(--muted);">No streams added yet</h5>
            <a href="{{ route('admin.setup.streams.create') }}" class="btn btn-primary btn-sm mt-2">Add First Stream</a>
        </div>
    </div>
    @else
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Stream Name</th>
                    <th>Code</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($streams as $i => $stream)
                <tr>
                    <td style="color:var(--muted);font-size:.82rem;">{{ $i + 1 }}</td>
                    <td style="font-weight:500;">{{ $stream->name }}</td>
                    <td><span class="badge" style="background:rgba(79,70,229,.1);color:#4f46e5;">{{ $stream->code ?? '—' }}</span></td>
                    <td>
                        @if($stream->is_active ?? true)
                            <span class="badge" style="background:#dcfce7;color:#166534;">Active</span>
                        @else
                            <span class="badge" style="background:#fee2e2;color:#991b1b;">Inactive</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="{{ route('admin.setup.streams.edit', $stream) }}"
                               class="btn btn-sm btn-outline-secondary" style="padding:.2rem .5rem;">
                                <i class="fa-solid fa-pen" style="font-size:.75rem;"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.setup.streams.destroy', $stream) }}" id="del-stream-{{ $stream->id }}">
                                @csrf @method('DELETE')
                            </form>
                            <button type="button" class="btn btn-sm btn-outline-danger" style="padding:.2rem .5rem;"
                                    data-confirm-delete="del-stream-{{ $stream->id }}"
                                    data-name="{{ $stream->name }}">
                                <i class="fa-solid fa-trash" style="font-size:.75rem;"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

@endsection

