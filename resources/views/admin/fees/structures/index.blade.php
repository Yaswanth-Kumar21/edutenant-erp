@extends('layouts.app')

@section('title', 'Fee Structures')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.fees.dashboard') }}" style="color:#4f46e5;text-decoration:none;">Fees</a></li>
    <li class="breadcrumb-item active">Fee Structures</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-layer-group me-2" style="color:#4f46e5;"></i>Fee Structures</h1>
        <p class="mb-0" style="color:var(--muted);font-size:0.875rem;">Semester-wise and branch-wise fee definitions</p>
    </div>
    <a href="{{ route('admin.fees.structures.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus me-2"></i> Add Structure
    </a>
</div>

{{-- Filters --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label mb-1" style="font-size:0.8rem;">Branch</label>
                <select name="branch_id" class="form-select form-select-sm">
                    <option value="">All Branches</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1" style="font-size:0.8rem;">Fee Type</label>
                <select name="fee_type_id" class="form-select form-select-sm">
                    <option value="">All Types</option>
                    @foreach($feeTypes as $ft)
                        <option value="{{ $ft->id }}" {{ request('fee_type_id') == $ft->id ? 'selected' : '' }}>{{ $ft->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1" style="font-size:0.8rem;">Academic Year</label>
                <select name="academic_year_id" class="form-select form-select-sm">
                    <option value="">All Years</option>
                    @foreach($academicYears as $yr)
                        <option value="{{ $yr->id }}" {{ request('academic_year_id') == $yr->id ? 'selected' : '' }}>{{ $yr->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-1"><i class="fa-solid fa-filter me-1"></i> Filter</button>
                @if(request()->hasAny(['branch_id','fee_type_id','academic_year_id']))
                    <a href="{{ route('admin.fees.structures.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-xmark"></i></a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span style="font-weight:600;">Fee Structures <span class="badge ms-2" style="background:rgba(79,70,229,0.1);color:#4f46e5;">{{ $structures->total() }}</span></span>
    </div>
    @if($structures->isEmpty())
        <div class="empty-state">
            <i class="fa-solid fa-layer-group"></i>
            <h5 style="color:var(--muted);">No fee structures defined</h5>
            <a href="{{ route('admin.fees.structures.create') }}" class="btn btn-primary btn-sm mt-2">
                <i class="fa-solid fa-plus me-1"></i> Add First Structure
            </a>
        </div>
    @else
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Fee Type</th>
                        <th>Branch</th>
                        <th>Stream</th>
                        <th>Academic Year</th>
                        <th>Semester</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($structures as $s)
                    <tr>
                        <td style="font-weight:500;">{{ $s->feeType?->name ?? '—' }}</td>
                        <td style="font-size:0.85rem;">{{ $s->branch?->name ?? 'All Branches' }}</td>
                        <td style="font-size:0.85rem;">{{ $s->stream?->name ?? 'All Streams' }}</td>
                        <td style="font-size:0.85rem;">{{ $s->academicYear?->name ?? 'All Years' }}</td>
                        <td style="font-size:0.85rem;">{{ $s->semester ? 'Sem '.$s->semester : 'All' }}</td>
                        <td style="font-weight:700;color:#4f46e5;">₹{{ number_format($s->amount, 2) }}</td>
                        <td>
                            @if($s->is_active)
                                <span class="badge" style="background:#dcfce7;color:#166534;">Active</span>
                            @else
                                <span class="badge" style="background:#fee2e2;color:#991b1b;">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="{{ route('admin.fees.structures.edit', $s) }}" class="btn btn-sm btn-outline-secondary" style="padding:0.25rem 0.5rem;" title="Edit">
                                    <i class="fa-solid fa-pen" style="font-size:0.75rem;"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.fees.structures.destroy', $s) }}" id="del-str-{{ $s->id }}">@csrf @method('DELETE')</form>
                                <button type="button" class="btn btn-sm btn-outline-danger" style="padding:0.25rem 0.5rem;"
                                        data-confirm-delete="del-str-{{ $s->id }}" data-name="{{ $s->feeType?->name }}" title="Delete">
                                    <i class="fa-solid fa-trash" style="font-size:0.75rem;"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($structures->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2" style="background:transparent;border-top:1px solid var(--border);">
                <small style="color:var(--muted);">Page {{ $structures->currentPage() }} of {{ $structures->lastPage() }}</small>
                {{ $structures->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        @endif
    @endif
</div>
@endsection

