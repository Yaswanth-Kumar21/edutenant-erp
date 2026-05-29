@extends('layouts.super-admin-app')
@section('title', $tenant->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('super.tenants.index') }}" style="color:var(--blue);">Institutions</a></li>
    <li class="breadcrumb-item active" style="color:var(--muted);">{{ Str::limit($tenant->name, 30) }}</li>
@endsection

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">{{ $tenant->name }}</h1>
        <p class="page-sub">Institution details and onboarding progress</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('super.tenants.edit', $tenant) }}" class="btn-primary"><i class="fa-solid fa-pen"></i> Edit</a>
        <a href="{{ route('super.tenants.index') }}" class="btn-secondary"><i class="fa-solid fa-arrow-left"></i> Back</a>
    </div>
</div>

{{-- KPI Row --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="kpi-card blue"><div class="kpi-icon blue mb-2"><i class="fa-solid fa-user-graduate"></i></div><div class="kpi-value">{{ $tenant->students_count ?? 0 }}</div><div class="kpi-label">Students</div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card green"><div class="kpi-icon green mb-2"><i class="fa-solid fa-users"></i></div><div class="kpi-value">{{ $tenant->staff_count ?? 0 }}</div><div class="kpi-label">Staff</div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card {{ $onboarding['percentage'] === 100 ? 'green' : ($onboarding['percentage'] >= 60 ? 'orange' : 'red') }}">
            <div class="kpi-icon {{ $onboarding['percentage'] === 100 ? 'green' : ($onboarding['percentage'] >= 60 ? 'orange' : 'red') }} mb-2">
                <i class="fa-solid fa-{{ $onboarding['is_complete'] ? 'circle-check' : 'circle-half-stroke' }}"></i>
            </div>
            <div class="kpi-value">{{ $onboarding['percentage'] }}%</div>
            <div class="kpi-label">Setup Complete</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card {{ $tenant->status === 'active' ? 'green' : 'red' }}">
            <div class="kpi-icon {{ $tenant->status === 'active' ? 'green' : 'red' }} mb-2">
                <i class="fa-solid fa-circle-{{ $tenant->status === 'active' ? 'check' : 'xmark' }}"></i>
            </div>
            <div class="kpi-value" style="font-size:18px;">{{ ucfirst($tenant->status) }}</div>
            <div class="kpi-label">Status</div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Left: Onboarding Checklist --}}
    <div class="col-lg-5">

        {{-- Onboarding Progress Card --}}
        <div class="card mb-4">
            <div class="card-header">
                <span style="font-weight:700;">Setup Progress</span>
                <span style="font-size:13px;font-weight:700;color:{{ $onboarding['percentage'] === 100 ? 'var(--green)' : ($onboarding['percentage'] >= 60 ? 'var(--orange)' : 'var(--red)') }};">
                    {{ $onboarding['completed_count'] }}/{{ $onboarding['total'] }} Complete
                </span>
            </div>
            <div class="card-body">
                {{-- Progress Bar --}}
                <div class="mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span style="font-size:13px;font-weight:600;color:var(--text);">
                            {{ $onboarding['percentage'] }}% Complete
                        </span>
                        @if($onboarding['is_complete'])
                        <span class="badge badge-active"><i class="fa-solid fa-circle-check me-1"></i>Fully Configured</span>
                        @else
                        <span style="font-size:12px;color:var(--muted);">{{ $onboarding['total'] - $onboarding['completed_count'] }} step(s) remaining</span>
                        @endif
                    </div>
                    <div style="height:8px;background:var(--border);border-radius:4px;overflow:hidden;">
                        <div style="height:100%;width:{{ $onboarding['percentage'] }}%;background:{{ $onboarding['percentage'] === 100 ? 'var(--green)' : ($onboarding['percentage'] >= 60 ? 'var(--orange)' : 'var(--red)') }};border-radius:4px;transition:width .5s ease;"></div>
                    </div>
                </div>

                {{-- Steps --}}
                <div class="d-flex flex-column gap-1">
                    @foreach($onboarding['steps'] as $step)
                    <div class="d-flex align-items-start gap-3 p-3 rounded"
                         style="background:{{ $step['done'] ? 'rgba(16,185,129,.06)' : 'rgba(239,68,68,.04)' }};border:1px solid {{ $step['done'] ? 'rgba(16,185,129,.15)' : 'rgba(239,68,68,.12)' }};">
                        {{-- Step indicator --}}
                        <div style="width:28px;height:28px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;
                            background:{{ $step['done'] ? 'var(--green)' : 'rgba(239,68,68,.15)' }};
                            color:{{ $step['done'] ? '#fff' : 'var(--red)' }};">
                            @if($step['done'])
                                <i class="fa-solid fa-check" style="font-size:11px;"></i>
                            @else
                                {{ $step['number'] }}
                            @endif
                        </div>
                        {{-- Step content --}}
                        <div class="flex-1">
                            <div style="font-size:13px;font-weight:600;color:var(--text);line-height:1.3;">
                                {{ $step['label'] }}
                            </div>
                            <div style="font-size:11px;color:var(--muted);margin-top:2px;">
                                {{ $step['detail'] }}
                            </div>
                        </div>
                        {{-- Status icon --}}
                        <div style="flex-shrink:0;">
                            @if($step['done'])
                                <i class="fa-solid fa-circle-check" style="color:var(--green);font-size:14px;"></i>
                            @else
                                <i class="fa-solid fa-triangle-exclamation" style="color:var(--orange);font-size:14px;"></i>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                @if(!$onboarding['is_complete'])
                <div class="mt-3 p-3 rounded" style="background:rgba(37,99,235,.06);border:1px solid rgba(37,99,235,.15);">
                    <div style="font-size:12px;color:var(--blue);display:flex;align-items:flex-start;gap:8px;">
                        <i class="fa-solid fa-circle-info mt-1 flex-shrink-0"></i>
                        <div>Steps 3–5 are completed by the College Admin after logging in. Share the admin credentials to continue setup.</div>
                    </div>
                </div>
                @else
                <div class="mt-3 p-3 rounded" style="background:rgba(16,185,129,.06);border:1px solid rgba(16,185,129,.15);">
                    <div style="font-size:12px;color:var(--green);display:flex;align-items:center;gap:8px;">
                        <i class="fa-solid fa-circle-check flex-shrink-0"></i>
                        <div>This institution is fully configured and operational.</div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="card">
            <div class="card-header"><span style="font-weight:700;font-size:13px;">Quick Actions</span></div>
            <div class="card-body d-flex flex-column gap-2">
                <a href="{{ route('super.tenants.edit', $tenant) }}" class="btn-primary w-100" style="justify-content:center;">
                    <i class="fa-solid fa-pen"></i> Edit Institution
                </a>
                <form method="POST" action="{{ route('super.tenants.switch', $tenant) }}">
                    @csrf
                    <button type="submit" class="btn-secondary w-100" style="justify-content:center;">
                        <i class="fa-solid fa-right-left"></i> Switch to This Tenant
                    </button>
                </form>
                <form id="del-tenant" method="POST" action="{{ route('super.tenants.destroy', $tenant) }}">@csrf @method('DELETE')</form>
                <button type="button" class="btn-danger w-100" style="justify-content:center;" data-confirm-delete="del-tenant" data-name="{{ $tenant->name }}">
                    <i class="fa-solid fa-trash"></i> Delete Institution
                </button>
            </div>
        </div>
    </div>

    {{-- Right: Institution Details --}}
    <div class="col-lg-7">
        <div class="card mb-4">
            <div class="card-header"><span style="font-weight:700;">Institution Details</span></div>
            <div class="card-body p-0">
                @php $details = [
                    ['label'=>'Institution Name', 'value'=>$tenant->name],
                    ['label'=>'Slug',             'value'=>$tenant->slug, 'mono'=>true],
                    ['label'=>'Email',            'value'=>$tenant->email ?? '—'],
                    ['label'=>'Phone',            'value'=>$tenant->phone ?? '—'],
                    ['label'=>'Principal',        'value'=>$tenant->principal_name ?? '—'],
                    ['label'=>'Affiliation No',   'value'=>$tenant->affiliation_number ?? '—', 'mono'=>true],
                    ['label'=>'Website',          'value'=>$tenant->website ?? '—'],
                    ['label'=>'Custom Domain',    'value'=>$tenant->domain ?? '—'],
                ]; @endphp
                @foreach($details as $i => $d)
                <div class="d-flex align-items-center justify-content-between px-4 py-3" style="border-bottom:{{ $i < count($details)-1 ? '1px solid var(--border)' : 'none' }};">
                    <span style="font-size:12px;color:var(--muted);min-width:140px;">{{ $d['label'] }}</span>
                    <span style="font-size:13px;font-weight:600;color:var(--text);{{ isset($d['mono']) ? 'font-family:monospace;' : '' }}">{{ $d['value'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><span style="font-weight:700;">Address</span></div>
            <div class="card-body" style="font-size:13px;color:var(--text2);line-height:1.8;">
                {{ $tenant->address ?? '—' }}<br>
                @if($tenant->city){{ $tenant->city }}{{ $tenant->state ? ', '.$tenant->state : '' }}{{ $tenant->pincode ? ' — '.$tenant->pincode : '' }}@endif
            </div>
        </div>

        <div class="card">
            <div class="card-header"><span style="font-weight:700;">Subscription & Metadata</span></div>
            <div class="card-body p-0">
                @php $meta = [
                    ['label'=>'Subscription Start', 'value'=>$tenant->subscription_start?->format('d M Y') ?? 'Not set'],
                    ['label'=>'Subscription End',   'value'=>$tenant->subscription_end?->format('d M Y') ?? 'Not set'],
                    ['label'=>'Created',            'value'=>$tenant->created_at?->format('d M Y, h:i A')],
                    ['label'=>'Last Updated',       'value'=>$tenant->updated_at?->diffForHumans()],
                ]; @endphp
                @foreach($meta as $i => $m)
                <div class="d-flex align-items-center justify-content-between px-4 py-3" style="border-bottom:{{ $i < count($meta)-1 ? '1px solid var(--border)' : 'none' }};">
                    <span style="font-size:12px;color:var(--muted);">{{ $m['label'] }}</span>
                    <span style="font-size:12px;font-weight:600;color:var(--text);">{{ $m['value'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection
