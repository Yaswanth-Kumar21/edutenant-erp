@extends('layouts.app')
@section('title', 'Scholarship Management')
@section('breadcrumb')
    <li class="breadcrumb-item active">Scholarships</li>
@endsection
@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-award me-2" style="color:#059669;"></i>Scholarship Management</h1>
        <p class="page-sub">Manage government and college scholarships for all students</p>
    </div>
    @if(auth()->user()->isCollegeAdmin() || auth()->user()->isSuperAdmin())
    <a href="{{ route('admin.scholarships.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus me-2"></i>Add Scholarship
    </a>
    @endif
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div style="font-size:1.75rem;font-weight:800;color:#4f46e5;">{{ $stats['students_count'] }}</div>
            <div style="font-size:.78rem;color:var(--muted);">Students with Scholarship</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div style="font-size:1.75rem;font-weight:800;color:#059669;">{{ $stats['total_govt'] }}</div>
            <div style="font-size:.78rem;color:var(--muted);">Govt Scholarships</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div style="font-size:1.75rem;font-weight:800;color:#d97706;">{{ $stats['total_college'] }}</div>
            <div style="font-size:.78rem;color:var(--muted);">College Scholarships</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div style="font-size:1.75rem;font-weight:800;color:#7c3aed;">₹{{ number_format($stats['total_amount']) }}</div>
            <div style="font-size:.78rem;color:var(--muted);">Total Amount</div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label mb-1" style="font-size:.8rem;">Search Student</label>
                <input type="text" name="search" class="form-control form-control-sm"
                       value="{{ request('search') }}" placeholder="Name or admission number...">
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1" style="font-size:.8rem;">Category</label>
                <select name="category" class="form-select form-select-sm">
                    <option value="">All Categories</option>
                    <option value="government" {{ request('category')==='government'?'selected':'' }}>Government</option>
                    <option value="college"    {{ request('category')==='college'?'selected':'' }}>College</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1" style="font-size:.8rem;">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="active"  {{ request('status')==='active'?'selected':'' }}>Active</option>
                    <option value="inactive"{{ request('status')==='inactive'?'selected':'' }}>Inactive</option>
                    <option value="expired" {{ request('status')==='expired'?'selected':'' }}>Expired</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-1">Filter</button>
                <a href="{{ route('admin.scholarships.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span style="font-weight:600;"><i class="fa-solid fa-list me-2" style="color:#059669;"></i>All Scholarships</span>
        <span class="badge" style="background:rgba(5,150,105,.1);color:#059669;">{{ $scholarships->total() }} records</span>
    </div>
    @if($scholarships->isEmpty())
    <div class="card-body">
        <div class="empty-state py-4">
            <i class="fa-solid fa-award d-block mb-2" style="font-size:2.5rem;opacity:.2;"></i>
            <h5>No Scholarships Found</h5>
            <p>No scholarship records match your filters.</p>
            @if(auth()->user()->isCollegeAdmin() || auth()->user()->isSuperAdmin())
            <a href="{{ route('admin.scholarships.create') }}" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-plus me-1"></i>Add First Scholarship
            </a>
            @endif
        </div>
    </div>
    @else
    <div class="table-responsive">
        <table class="table mb-0" style="font-size:.875rem;">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Category</th>
                    <th>Scholarship</th>
                    <th>Amount</th>
                    <th>Semesters</th>
                    <th>Status</th>
                    <th>From</th>
                    @if(auth()->user()->isCollegeAdmin() || auth()->user()->isSuperAdmin())
                    <th class="text-end">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($scholarships as $sch)
                <tr>
                    <td>
                        <a href="{{ route('admin.students.360', $sch->student) }}#scholarships"
                           style="color:#4f46e5;text-decoration:none;font-weight:500;">
                            {{ $sch->student?->full_name ?? '—' }}
                        </a>
                        <div style="font-size:.72rem;color:var(--muted);font-family:monospace;">
                            {{ $sch->student?->admission_number }}
                        </div>
                    </td>
                    <td>
                        <span class="badge" style="background:{{ $sch->isGovernment()?'rgba(79,70,229,.1)':'rgba(217,119,6,.1)' }};color:{{ $sch->isGovernment()?'#4f46e5':'#d97706' }};">
                            <i class="fa-solid {{ $sch->isGovernment()?'fa-landmark':'fa-school' }} me-1"></i>
                            {{ ucfirst($sch->scholarship_category) }}
                        </span>
                    </td>
                    <td style="font-weight:500;">{{ $sch->display_name }}</td>
                    <td style="font-weight:700;color:#059669;">₹{{ number_format($sch->amount) }}</td>
                    <td style="font-size:.82rem;color:var(--muted);">{{ $sch->semesters_label }}</td>
                    <td>
                        @if($sch->status==='active')
                            <span class="badge" style="background:#dcfce7;color:#166534;">Active</span>
                        @elseif($sch->status==='expired')
                            <span class="badge" style="background:#fee2e2;color:#991b1b;">Expired</span>
                        @else
                            <span class="badge" style="background:#f3f4f6;color:#374151;">Inactive</span>
                        @endif
                    </td>
                    <td style="font-size:.82rem;color:var(--muted);">
                        {{ $sch->start_date?->format('d M Y') ?? '—' }}
                    </td>
                    @if(auth()->user()->isCollegeAdmin() || auth()->user()->isSuperAdmin())
                    <td class="text-end">
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="{{ route('admin.scholarships.edit', $sch) }}"
                               class="btn btn-sm btn-outline-primary"
                               style="padding:.2rem .5rem;font-size:.72rem;" title="Edit">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.scholarships.destroy', $sch) }}"
                                  id="del-sch-{{ $sch->id }}">@csrf @method('DELETE')</form>
                            <button type="button" class="btn btn-sm btn-outline-danger"
                                    style="padding:.2rem .5rem;font-size:.72rem;"
                                    data-confirm-delete="del-sch-{{ $sch->id }}"
                                    data-name="this scholarship" title="Delete">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($scholarships->hasPages())
    <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2"
         style="background:transparent;border-top:1px solid var(--border);">
        <small style="color:var(--muted);">
            Showing {{ $scholarships->firstItem() }}–{{ $scholarships->lastItem() }} of {{ $scholarships->total() }}
        </small>
        {{ $scholarships->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
    @endif
</div>

@endsection
