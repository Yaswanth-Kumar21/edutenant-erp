@extends('layouts.super-admin-app')
@section('title', 'Institutions')
@section('breadcrumb')
    <li class="breadcrumb-item active" style="color:var(--muted);">Institutions</li>
@endsection
@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">Institutions</h1>
        <p class="page-sub">Manage all colleges and institutions on the platform</p>
    </div>
    <a href="{{ route('super.tenants.create') }}" class="btn-primary">
        <i class="fa-solid fa-plus"></i> Add Institution
    </a>
</div>

{{-- Stats Row --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="kpi-card blue">
            <div class="kpi-icon blue mb-2"><i class="fa-solid fa-building"></i></div>
            <div class="kpi-value">{{ $tenants->total() }}</div>
            <div class="kpi-label">Total</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card green">
            <div class="kpi-icon green mb-2"><i class="fa-solid fa-circle-check"></i></div>
            <div class="kpi-value">{{ $tenants->where('status','active')->count() }}</div>
            <div class="kpi-label">Active</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card purple">
            <div class="kpi-icon purple mb-2"><i class="fa-solid fa-user-graduate"></i></div>
            <div class="kpi-value">{{ number_format($tenants->sum('students_count')) }}</div>
            <div class="kpi-label">Students</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card cyan">
            <div class="kpi-icon cyan mb-2"><i class="fa-solid fa-users"></i></div>
            <div class="kpi-value">{{ number_format($tenants->sum('staff_count')) }}</div>
            <div class="kpi-label">Staff</div>
        </div>
    </div>
</div>

{{-- Search + Filter Bar --}}
<div class="card mb-4">
    <div class="card-body" style="padding:16px 20px;">
        <form method="GET" class="d-flex align-items-center gap-3 flex-wrap">
            <div class="sa-search flex-1" style="min-width:200px;">
                <i class="fa-solid fa-search sa-search-icon"></i>
                <input type="text" name="search" class="form-control" placeholder="Search institutions..." value="{{ request('search') }}" style="width:100%;">
            </div>
            <select name="status" class="form-select" style="width:140px;" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="active"    {{ request('status')==='active'    ? 'selected' : '' }}>Active</option>
                <option value="inactive"  {{ request('status')==='inactive'  ? 'selected' : '' }}>Inactive</option>
                <option value="suspended" {{ request('status')==='suspended' ? 'selected' : '' }}>Suspended</option>
            </select>
            <button type="submit" class="btn-primary" style="padding:8px 16px;font-size:13px;">
                <i class="fa-solid fa-filter"></i> Filter
            </button>
            @if(request()->hasAny(['search','status']))
            <a href="{{ route('super.tenants.index') }}" class="btn-secondary" style="font-size:13px;">
                <i class="fa-solid fa-xmark"></i> Clear
            </a>
            @endif
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-header">
        <div style="font-weight:700;font-size:14px;color:var(--text);">
            Institutions
            <span class="badge badge-blue ms-2">{{ $tenants->total() }}</span>
        </div>
        <div style="font-size:12px;color:var(--muted);">
            Showing {{ $tenants->firstItem() ?? 0 }}–{{ $tenants->lastItem() ?? 0 }} of {{ $tenants->total() }}
        </div>
    </div>
    @if($tenants->isEmpty())
    <div class="card-body">
        <div class="empty-state">
            <div class="empty-state-icon"><i class="fa-solid fa-building"></i></div>
            <h5>No institutions found</h5>
            <p>{{ request()->hasAny(['search','status']) ? 'Try adjusting your filters.' : 'Get started by adding your first institution.' }}</p>
            @if(!request()->hasAny(['search','status']))
            <a href="{{ route('super.tenants.create') }}" class="btn-primary">Add Institution</a>
            @endif
        </div>
    </div>
    @else
    <div class="table-responsive">
        <table class="sa-table">
            <thead>
                <tr>
                    <th>Institution</th>
                    <th>Contact</th>
                    <th>Location</th>
                    <th style="text-align:center;">Students</th>
                    <th style="text-align:center;">Staff</th>
                    <th style="text-align:center;">Setup</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tenants as $tenant)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div style="width:38px;height:38px;border-radius:10px;background:var(--blue-xl);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="fa-solid fa-graduation-cap" style="color:var(--blue);font-size:15px;"></i>
                            </div>
                            <div>
                                <div style="font-weight:600;font-size:13px;color:var(--text);">{{ $tenant->name }}</div>
                                <div style="font-size:11px;color:var(--muted);font-family:monospace;margin-top:1px;">{{ $tenant->slug }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="font-size:12px;color:var(--text);">{{ $tenant->email ?? '—' }}</div>
                        <div style="font-size:11px;color:var(--muted);">{{ $tenant->phone ?? '' }}</div>
                    </td>
                    <td style="font-size:12px;color:var(--muted);">{{ $tenant->city ?? '—' }}{{ $tenant->state ? ', '.$tenant->state : '' }}</td>
                    <td style="text-align:center;"><span class="badge badge-blue">{{ $tenant->students_count ?? 0 }}</span></td>
                    <td style="text-align:center;"><span class="badge" style="background:var(--green-l);color:#065F46;">{{ $tenant->staff_count ?? 0 }}</span></td>
                    <td style="text-align:center;">
                        @php $badge = $onboardingBadges[$tenant->id] ?? ['percentage'=>0,'color'=>'red','label'=>'0/5']; @endphp
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <div style="width:40px;height:5px;background:var(--border);border-radius:3px;overflow:hidden;">
                                <div style="height:100%;width:{{ $badge['percentage'] }}%;background:{{ $badge['color'] === 'green' ? 'var(--green)' : ($badge['color'] === 'orange' ? 'var(--orange)' : 'var(--red)') }};border-radius:3px;"></div>
                            </div>
                            <span style="font-size:11px;font-weight:700;color:{{ $badge['color'] === 'green' ? 'var(--green)' : ($badge['color'] === 'orange' ? 'var(--orange)' : 'var(--red)') }};">{{ $badge['label'] }}</span>
                        </div>
                    </td>
                    <td style="text-align:center;">
                        @if($tenant->status === 'active')
                            <span class="badge badge-active"><i class="fa-solid fa-circle" style="font-size:6px;"></i> Active</span>
                        @elseif($tenant->status === 'suspended')
                            <span class="badge badge-pending">Suspended</span>
                        @else
                            <span class="badge badge-inactive">Inactive</span>
                        @endif
                    </td>
                    <td style="text-align:right;">
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="{{ route('super.tenants.show', $tenant) }}" class="btn-icon" title="View Details"><i class="fa-solid fa-eye" style="font-size:12px;"></i></a>
                            <a href="{{ route('super.tenants.edit', $tenant) }}" class="btn-icon" title="Edit"><i class="fa-solid fa-pen" style="font-size:12px;"></i></a>
                            <form id="del-{{ $tenant->id }}" method="POST" action="{{ route('super.tenants.destroy', $tenant) }}">@csrf @method('DELETE')</form>
                            <button type="button" class="btn-icon danger" title="Delete" data-confirm-delete="del-{{ $tenant->id }}" data-name="{{ $tenant->name }}">
                                <i class="fa-solid fa-trash" style="font-size:12px;"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($tenants->hasPages())
    <div class="card-body pt-3 pb-2">{{ $tenants->withQueryString()->links('pagination::bootstrap-5') }}</div>
    @endif
    @endif
</div>

@endsection
