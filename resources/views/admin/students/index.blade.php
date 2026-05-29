@extends('layouts.app')

@section('title', 'Students')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Students</li>
@endsection

@section('content')

{{-- ── Page Header ──────────────────────────────────────────────────────── --}}
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fa-solid fa-user-graduate me-2" style="color:#4f46e5;"></i>
            Students
        </h1>
        <p class="mb-0" style="color:var(--muted);font-size:0.875rem;">
            Manage student records and admissions
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <div class="dropdown">
            <button class="btn btn-outline-success btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fa-solid fa-file-excel me-1"></i> Export
            </button>
            <ul class="dropdown-menu shadow border-0" style="border-radius:.75rem;background:var(--surface);">
                <li>
                    <a class="dropdown-item" style="font-size:.85rem;"
                       href="{{ route('admin.exports.students', array_merge(request()->query(), ['format'=>'xlsx'])) }}">
                        <i class="fa-solid fa-file-excel me-2" style="color:#059669;"></i> Excel (.xlsx)
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" style="font-size:.85rem;"
                       href="{{ route('admin.exports.students', array_merge(request()->query(), ['format'=>'csv'])) }}">
                        <i class="fa-solid fa-file-csv me-2" style="color:#0891b2;"></i> CSV
                    </a>
                </li>
            </ul>
        </div>
        <a href="{{ route('admin.admissions.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-user-plus me-2"></i> New Admission
        </a>
    </div>
</div>

{{-- ── Quick Stats ──────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="card p-3 text-center">
            <div style="font-size:1.75rem;font-weight:800;color:#4f46e5;" data-counter data-target="{{ $stats['total'] }}">
                {{ $stats['total'] }}
            </div>
            <div style="font-size:0.78rem;color:var(--muted);margin-top:4px;">Total Students</div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card p-3 text-center">
            <div style="font-size:1.75rem;font-weight:800;color:#059669;" data-counter data-target="{{ $stats['active'] }}">
                {{ $stats['active'] }}
            </div>
            <div style="font-size:0.78rem;color:var(--muted);margin-top:4px;">Active Students</div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card p-3 text-center">
            <div style="font-size:1.75rem;font-weight:800;color:#d97706;" data-counter data-target="{{ $stats['this_month'] }}">
                {{ $stats['this_month'] }}
            </div>
            <div style="font-size:0.78rem;color:var(--muted);margin-top:4px;">Admitted This Month</div>
        </div>
    </div>
</div>

{{-- ── Filter Bar ───────────────────────────────────────────────────────── --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.students.index') }}" id="filter-form">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label mb-1" style="font-size:0.8rem;">Search</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text" style="background:var(--bg);border-color:var(--border);">
                            <i class="fa-solid fa-search" style="color:var(--muted);font-size:0.8rem;"></i>
                        </span>
                        <input type="text" name="search" class="form-control"
                               placeholder="Name, adm. no, phone..."
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label mb-1" style="font-size:0.8rem;">Stream</label>
                    <select name="stream_id" class="form-select form-select-sm"
                            onchange="this.form.submit()">
                        <option value="">All Streams</option>
                        @foreach($streams as $stream)
                            <option value="{{ $stream->id }}"
                                {{ request('stream_id') == $stream->id ? 'selected' : '' }}>
                                {{ $stream->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label mb-1" style="font-size:0.8rem;">Branch</label>
                    <select name="branch_id" class="form-select form-select-sm">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}"
                                {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label mb-1" style="font-size:0.8rem;">Category</label>
                    <select name="category" class="form-select form-select-sm">
                        <option value="">All Categories</option>
                        @foreach(['GEN','OBC','SC','ST','EWS','OTHER'] as $cat)
                            <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>
                                {{ $cat }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-1">
                    <label class="form-label mb-1" style="font-size:0.8rem;">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="active"     {{ request('status') === 'active'     ? 'selected' : '' }}>Active</option>
                        <option value="inactive"   {{ request('status') === 'inactive'   ? 'selected' : '' }}>Inactive</option>
                        <option value="passed_out" {{ request('status') === 'passed_out' ? 'selected' : '' }}>Passed Out</option>
                        <option value="dropped"    {{ request('status') === 'dropped'    ? 'selected' : '' }}>Dropped</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-1">
                        <i class="fa-solid fa-filter me-1"></i> Filter
                    </button>
                    @if(request()->hasAny(['search','branch_id','stream_id','category','status','academic_year_id']))
                        <a href="{{ route('admin.students.index') }}"
                           class="btn btn-outline-secondary btn-sm"
                           title="Clear filters">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ── Students Table ───────────────────────────────────────────────────── --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <span style="font-weight:600;">
            Students
            <span class="badge ms-2" style="background:rgba(79,70,229,0.1);color:#4f46e5;">
                {{ $students->total() }}
            </span>
        </span>
        <small style="color:var(--muted);">
            Showing {{ $students->firstItem() ?? 0 }}–{{ $students->lastItem() ?? 0 }}
            of {{ $students->total() }}
        </small>
    </div>

    @if($students->isEmpty())
        <div class="empty-state">
            <i class="fa-solid fa-user-graduate"></i>
            <h5 style="color:var(--muted);">No students found</h5>
            <p style="color:var(--muted);font-size:0.875rem;">
                @if(request()->hasAny(['search','branch_id','stream_id','category','status']))
                    Try adjusting your filters or
                    <a href="{{ route('admin.students.index') }}" style="color:#4f46e5;">clear all filters</a>.
                @else
                    Get started by
                    <a href="{{ route('admin.admissions.create') }}" style="color:#4f46e5;">admitting your first student</a>.
                @endif
            </p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Adm. No</th>
                        <th>Branch / Course</th>
                        <th>Category</th>
                        <th>Semester</th>
                        <th>Admission Date</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $student->photo_url }}"
                                     alt="{{ $student->full_name }}"
                                     class="rounded-circle"
                                     style="width:36px;height:36px;object-fit:cover;flex-shrink:0;">
                                <div>
                                    <div style="font-weight:500;font-size:0.875rem;">
                                        {{ $student->full_name }}
                                    </div>
                                    <div style="font-size:0.75rem;color:var(--muted);">
                                        {{ $student->phone ?? $student->email ?? '—' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span style="font-family:monospace;font-size:0.82rem;font-weight:500;color:#4f46e5;">
                                {{ $student->admission_number }}
                            </span>
                        </td>
                        <td style="font-size:0.85rem;">
                            <div style="font-weight:500;">{{ $student->branch?->name ?? '—' }}</div>
                            @if($student->branch?->course)
                                <div style="font-size:0.75rem;color:var(--muted);">
                                    {{ $student->branch->course->name }}
                                    @if($student->branch->course->stream)
                                        &bull; {{ $student->branch->course->stream->name }}
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td>
                            <span class="badge"
                                  style="background:rgba(79,70,229,0.1);color:#4f46e5;font-size:0.75rem;">
                                {{ $student->category }}
                            </span>
                        </td>
                        <td style="font-size:0.85rem;">
                            Sem {{ $student->current_semester ?? '—' }}
                        </td>
                        <td style="font-size:0.82rem;color:var(--muted);">
                            {{ $student->admission_date?->format('d M Y') ?? '—' }}
                        </td>
                        <td>
                            @switch($student->status)
                                @case('active')
                                    <span class="badge" style="background:#dcfce7;color:#166534;">
                                        <i class="fa-solid fa-circle me-1" style="font-size:0.5rem;"></i>Active
                                    </span>
                                    @break
                                @case('inactive')
                                    <span class="badge" style="background:#fee2e2;color:#991b1b;">Inactive</span>
                                    @break
                                @case('passed_out')
                                    <span class="badge" style="background:#dbeafe;color:#1e40af;">Passed Out</span>
                                    @break
                                @case('dropped')
                                    <span class="badge" style="background:#fef3c7;color:#92400e;">Dropped</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">{{ ucfirst($student->status) }}</span>
                            @endswitch
                        </td>
                        <td class="text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="{{ route('admin.students.show', $student) }}"
                                   class="btn btn-sm btn-outline-primary"
                                   style="border-radius:0.375rem;padding:0.25rem 0.5rem;"
                                   title="View Profile">
                                    <i class="fa-solid fa-eye" style="font-size:0.75rem;"></i>
                                </a>
                                <a href="{{ route('admin.admissions.receipt', $student) }}"
                                   class="btn btn-sm btn-outline-success"
                                   style="border-radius:0.375rem;padding:0.25rem 0.5rem;"
                                   title="Admission Receipt">
                                    <i class="fa-solid fa-receipt" style="font-size:0.75rem;"></i>
                                </a>
                                <a href="{{ route('admin.students.edit', $student) }}"
                                   class="btn btn-sm btn-outline-secondary"
                                   style="border-radius:0.375rem;padding:0.25rem 0.5rem;"
                                   title="Edit">
                                    <i class="fa-solid fa-pen" style="font-size:0.75rem;"></i>
                                </a>
                                <form id="del-student-{{ $student->id }}"
                                      method="POST"
                                      action="{{ route('admin.students.destroy', $student) }}">
                                    @csrf @method('DELETE')
                                </form>
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        style="border-radius:0.375rem;padding:0.25rem 0.5rem;"
                                        data-confirm-delete="del-student-{{ $student->id }}"
                                        data-name="{{ $student->full_name }}"
                                        title="Archive">
                                    <i class="fa-solid fa-trash" style="font-size:0.75rem;"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($students->hasPages())
            <div class="card-footer d-flex align-items-center justify-content-between flex-wrap gap-2"
                 style="background:transparent;border-top:1px solid var(--border);">
                <small style="color:var(--muted);">
                    Page {{ $students->currentPage() }} of {{ $students->lastPage() }}
                </small>
                {{ $students->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        @endif
    @endif
</div>

@endsection

