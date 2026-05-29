@extends('layouts.app')
@section('title', 'Branches')
@section('breadcrumb')
    <li class="breadcrumb-item active">Branches</li>
@endsection
@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-code-branch me-2" style="color:#4f46e5;"></i>Branches</h1>
        <p class="mb-0" style="color:var(--muted);font-size:.875rem;">Manage course branches and sections</p>
    </div>
    <a href="{{ route('admin.setup.branches.create') }}" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus me-1"></i> Add Branch</a>
</div>
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span style="font-weight:600;"><i class="fa-solid fa-list me-2" style="color:#4f46e5;"></i>All Branches</span>
        <span class="badge" style="background:rgba(79,70,229,.1);color:#4f46e5;">{{ $branches->count() }}</span>
    </div>
    <div class="card-body p-0">
        @if($branches->isEmpty())
        <div class="empty-state py-5"><i class="fa-solid fa-code-branch d-block mb-2" style="font-size:2.5rem;opacity:.3;"></i><div>No branches yet.</div><a href="{{ route('admin.setup.branches.create') }}" class="btn btn-primary btn-sm mt-3">Add Branch</a></div>
        @else
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th>Name</th><th>Code</th><th>Course</th><th>Stream</th><th>Intake</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    @foreach($branches as $branch)
                    <tr>
                        <td style="font-weight:600;">{{ $branch->name }}</td>
                        <td style="font-family:monospace;font-size:.82rem;color:#4f46e5;">{{ $branch->code ?? '—' }}</td>
                        <td style="font-size:.85rem;">{{ $branch->course?->name ?? '—' }}</td>
                        <td style="font-size:.82rem;color:var(--muted);">{{ $branch->course?->stream?->name ?? '—' }}</td>
                        <td style="font-size:.85rem;">{{ $branch->intake_capacity ?? '—' }}</td>
                        <td>
                            @if($branch->is_active ?? true)
                                <span class="badge" style="background:#dcfce7;color:#166534;">Active</span>
                            @else
                                <span class="badge" style="background:#fee2e2;color:#991b1b;">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.setup.branches.edit', $branch) }}" class="btn btn-sm btn-outline-secondary" style="padding:.2rem .5rem;"><i class="fa-solid fa-pen" style="font-size:.72rem;"></i></a>
                                <form method="POST" action="{{ route('admin.setup.branches.destroy', $branch) }}">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-outline-danger" style="padding:.2rem .5rem;" onclick="return confirm('Delete this branch?')"><i class="fa-solid fa-trash" style="font-size:.72rem;"></i></button></form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection

