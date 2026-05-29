@extends('layouts.app')

@section('title', 'Staff')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Staff</li>
@endsection

@section('content')

{{-- ── Page Header ──────────────────────────────────────────────────────── --}}
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fa-solid fa-users me-2" style="color:#059669;"></i>
            Staff
        </h1>
        <p class="mb-0" style="color:var(--muted);font-size:0.875rem;">
            Manage teaching and non-teaching staff members
        </p>
    </div>
    <a href="{{ route('admin.staff.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-user-plus me-2"></i> Add Staff
    </a>
</div>

{{-- Stats Row --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card blue">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label mb-1">Total Staff</div>
                    <div class="stat-value">{{ $stats['total'] }}</div>
                </div>
                <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card green">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label mb-1">Active</div>
                    <div class="stat-value">{{ $stats['active'] }}</div>
                </div>
                <div class="stat-icon"><i class="fa-solid fa-circle-check"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card purple">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label mb-1">Teaching</div>
                    <div class="stat-value">{{ $stats['teaching'] }}</div>
                </div>
                <div class="stat-icon"><i class="fa-solid fa-chalkboard-teacher"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card orange">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label mb-1">Non-Teaching</div>
                    <div class="stat-value">{{ $stats['non_teaching'] }}</div>
                </div>
                <div class="stat-icon"><i class="fa-solid fa-briefcase"></i></div>
            </div>
        </div>
    </div>
</div>

{{-- Quick Links --}}
<div class="d-flex gap-2 mb-4">
    <a href="{{ route('admin.staff.leaves') }}" class="btn btn-outline-warning btn-sm">
        <i class="fa-solid fa-calendar-xmark me-1"></i> Leave Requests
    </a>
    <a href="{{ route('admin.staff.roles') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-id-badge me-1"></i> Staff Roles
    </a>
    <a href="{{ route('admin.payroll.index') }}" class="btn btn-outline-primary btn-sm">
        <i class="fa-solid fa-file-invoice-dollar me-1"></i> Payroll
    </a>
</div>

{{-- ── Filter Bar ───────────────────────────────────────────────────────── --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.staff.index') }}" class="row g-2 align-items-end">
            <div class="col-12 col-md-5">
                <label class="form-label mb-1" style="font-size:0.8rem;">Search</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text" style="background:var(--bg);border-color:var(--border);">
                        <i class="fa-solid fa-search" style="color:var(--muted);font-size:0.8rem;"></i>
                    </span>
                    <input type="text" name="search" class="form-control"
                           placeholder="Name, designation, subject..."
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label mb-1" style="font-size:0.8rem;">Type</label>
                <select name="staff_type" class="form-select form-select-sm">
                    <option value="">All Types</option>
                    <option value="teaching"     {{ request('staff_type') === 'teaching'     ? 'selected' : '' }}>Teaching</option>
                    <option value="non_teaching" {{ request('staff_type') === 'non_teaching' ? 'selected' : '' }}>Non-Teaching</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1" style="font-size:0.8rem;">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-1">
                    <i class="fa-solid fa-filter me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search','staff_type','status']))
                    <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- ── Staff Table ──────────────────────────────────────────────────────── --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span style="font-weight:600;">
            Staff Members
            <span class="badge ms-2" style="background:rgba(5,150,105,0.1);color:#059669;">
                {{ $staff->total() }}
            </span>
        </span>
        <small style="color:var(--muted);">
            Showing {{ $staff->firstItem() ?? 0 }}–{{ $staff->lastItem() ?? 0 }}
            of {{ $staff->total() }}
        </small>
    </div>

    @if($staff->isEmpty())
        <div class="empty-state">
            <i class="fa-solid fa-users"></i>
            <h5 style="color:var(--muted);">No staff members found</h5>
            <p style="color:var(--muted);font-size:0.875rem;">
                <a href="{{ route('admin.staff.create') }}" style="color:#4f46e5;">Add your first staff member</a>
                to get started.
            </p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Staff Member</th>
                        <th>Type</th>
                        <th>Designation</th>
                        <th>Subject</th>
                        <th>Salary</th>
                        <th>Joined</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($staff as $member)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $member->photo_url }}"
                                     alt="{{ $member->name }}"
                                     class="rounded-circle"
                                     style="width:36px;height:36px;object-fit:cover;flex-shrink:0;">
                                <div>
                                    <div style="font-weight:500;font-size:0.875rem;">{{ $member->name }}</div>
                                    <div style="font-size:0.75rem;color:var(--muted);">
                                        {{ $member->email ?? $member->phone ?? '—' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($member->isTeaching())
                                <span class="badge" style="background:rgba(79,70,229,0.1);color:#4f46e5;">
                                    <i class="fa-solid fa-chalkboard-teacher me-1"></i>Teaching
                                </span>
                            @else
                                <span class="badge" style="background:rgba(217,119,6,0.1);color:#d97706;">
                                    <i class="fa-solid fa-briefcase me-1"></i>Non-Teaching
                                </span>
                            @endif
                        </td>
                        <td style="font-size:0.85rem;">{{ $member->designation ?? '—' }}</td>
                        <td style="font-size:0.85rem;color:var(--muted);">
                            {{ $member->subject ?? '—' }}
                        </td>
                        <td style="font-size:0.85rem;font-weight:600;color:#059669;">
                            ₹{{ number_format($member->monthly_salary) }}
                            <div style="font-size:0.7rem;font-weight:400;color:var(--muted);">/ month</div>
                        </td>
                        <td style="font-size:0.82rem;color:var(--muted);">
                            {{ $member->date_of_joining?->format('d M Y') ?? '—' }}
                        </td>
                        <td>
                            @if($member->status === 'active')
                                <span class="badge" style="background:#dcfce7;color:#166534;">
                                    <i class="fa-solid fa-circle me-1" style="font-size:0.5rem;"></i>Active
                                </span>
                            @else
                                <span class="badge" style="background:#fee2e2;color:#991b1b;">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="{{ route('admin.staff.show', $member) }}"
                                   class="btn btn-sm btn-outline-primary"
                                   style="border-radius:0.375rem;padding:0.25rem 0.5rem;"
                                   title="View">
                                    <i class="fa-solid fa-eye" style="font-size:0.75rem;"></i>
                                </a>
                                <a href="{{ route('admin.staff.edit', $member) }}"
                                   class="btn btn-sm btn-outline-secondary"
                                   style="border-radius:0.375rem;padding:0.25rem 0.5rem;"
                                   title="Edit">
                                    <i class="fa-solid fa-pen" style="font-size:0.75rem;"></i>
                                </a>
                                <form id="del-staff-{{ $member->id }}"
                                      method="POST"
                                      action="{{ route('admin.staff.destroy', $member) }}">
                                    @csrf @method('DELETE')
                                </form>
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        style="border-radius:0.375rem;padding:0.25rem 0.5rem;"
                                        data-confirm-delete="del-staff-{{ $member->id }}"
                                        data-name="{{ $member->name }}"
                                        title="Delete">
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
        @if($staff->hasPages())
            <div class="card-footer d-flex align-items-center justify-content-between flex-wrap gap-2"
                 style="background:transparent;border-top:1px solid var(--border);">
                <small style="color:var(--muted);">
                    Page {{ $staff->currentPage() }} of {{ $staff->lastPage() }}
                </small>
                {{ $staff->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        @endif
    @endif
</div>

@endsection

