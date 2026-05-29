@extends('layouts.app')
@section('title', 'Academic Years')

@section('breadcrumb')
    <li class="breadcrumb-item active">Academic Years</li>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-calendar me-2" style="color:#4f46e5;"></i>Academic Years</h1>
        <p class="mb-0" style="color:var(--muted);font-size:.875rem;">Manage academic year configurations</p>
    </div>
    <a href="{{ route('admin.setup.academic-years.create') }}" class="btn btn-primary btn-sm">
        <i class="fa-solid fa-plus me-1"></i> Add Academic Year
    </a>
</div>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span style="font-weight:600;"><i class="fa-solid fa-list me-2" style="color:#4f46e5;"></i>All Academic Years</span>
        <span class="badge" style="background:rgba(79,70,229,.1);color:#4f46e5;">{{ $years->count() }}</span>
    </div>
    <div class="card-body p-0">
        @if($years->isEmpty())
        <div class="empty-state py-5">
            <i class="fa-solid fa-calendar d-block mb-2" style="font-size:2.5rem;opacity:.3;"></i>
            <div>No academic years configured yet.</div>
            <a href="{{ route('admin.setup.academic-years.create') }}" class="btn btn-primary btn-sm mt-3">Add First Year</a>
        </div>
        @else
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($years as $year)
                    <tr>
                        <td style="font-weight:600;">{{ $year->name }}</td>
                        <td style="font-size:.85rem;">{{ $year->start_date?->format('d M Y') ?? '—' }}</td>
                        <td style="font-size:.85rem;">{{ $year->end_date?->format('d M Y') ?? '—' }}</td>
                        <td>
                            @if($year->is_current)
                                <span class="badge" style="background:#dcfce7;color:#166534;">Current</span>
                            @else
                                <span class="badge" style="background:#f3f4f6;color:#6b7280;">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.setup.academic-years.edit', $year) }}" class="btn btn-sm btn-outline-secondary" style="padding:.2rem .5rem;"><i class="fa-solid fa-pen" style="font-size:.72rem;"></i></a>
                                <form method="POST" action="{{ route('admin.setup.academic-years.destroy', $year) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" style="padding:.2rem .5rem;" onclick="return confirm('Delete this academic year?')"><i class="fa-solid fa-trash" style="font-size:.72rem;"></i></button>
                                </form>
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

