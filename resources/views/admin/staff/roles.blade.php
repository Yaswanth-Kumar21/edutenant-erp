@extends('layouts.app')
@section('title', 'Staff Roles')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-id-badge me-2" style="color:#7c3aed;"></i>Staff Roles & Designations</h1>
        <p class="mb-0" style="color:var(--muted);font-size:.875rem;">Manage roles and designations for staff members</p>
    </div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addRoleModal">
        <i class="fa-solid fa-plus me-1"></i> Add Role
    </button>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fa-solid fa-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-3">
    @forelse($roles as $role)
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <div>
                        <h6 class="mb-1 fw-600" style="font-weight:600;">{{ $role->name }}</h6>
                        @if($role->department)
                            <span class="badge" style="background:rgba(79,70,229,.1);color:#4f46e5;font-size:.7rem;">
                                {{ $role->department }}
                            </span>
                        @endif
                    </div>
                    <span class="badge {{ $role->is_active ? 'bg-success' : 'bg-secondary' }}">
                        {{ $role->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                @if($role->description)
                    <p style="font-size:.82rem;color:var(--muted);margin-bottom:.5rem;">{{ $role->description }}</p>
                @endif
                <div class="d-flex align-items-center gap-2 mt-2">
                    <span class="badge" style="background:rgba(5,150,105,.1);color:#059669;">
                        <i class="fa-solid fa-users me-1"></i>{{ $role->staff_count }} Staff
                    </span>
                    <span class="badge" style="background:rgba(124,58,237,.1);color:#7c3aed;font-size:.7rem;">
                        {{ ucfirst(str_replace('_', ' ', $role->staff_type)) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="empty-state py-4">
                    <i class="fa-solid fa-id-badge" style="font-size:3rem;opacity:.2;"></i>
                    <h5 class="mt-3" style="color:var(--muted);">No roles defined yet</h5>
                    <button class="btn btn-primary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                        Add First Role
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endforelse
</div>

{{-- Add Role Modal --}}
<div class="modal fade" id="addRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background:var(--surface);border-color:var(--border);">
            <div class="modal-header" style="border-color:var(--border);">
                <h5 class="modal-title fw-600" style="font-weight:600;">Add Staff Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.staff.roles.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Role Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. HOD, Class Teacher, Lab Assistant">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Department</label>
                        <input type="text" name="department" class="form-control" placeholder="e.g. Science, Commerce">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Applicable To</label>
                        <select name="staff_type" class="form-select">
                            <option value="both">Both Teaching & Non-Teaching</option>
                            <option value="teaching">Teaching Only</option>
                            <option value="non_teaching">Non-Teaching Only</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Brief description..."></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-color:var(--border);">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Create Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

