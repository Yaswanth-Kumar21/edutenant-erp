@extends('layouts.app')
@section('title', 'Income Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.incomes.index') }}" style="color:var(--primary);text-decoration:none;">Income</a></li>
    <li class="breadcrumb-item active">{{ $income->title }}</li>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-arrow-trend-up me-2" style="color:#059669;"></i>Income Details</h1>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.incomes.edit', $income) }}" class="btn btn-outline-primary btn-sm">
            <i class="fa-solid fa-pen me-1"></i> Edit
        </a>
        <a href="{{ route('admin.incomes.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header"><i class="fa-solid fa-info-circle me-2" style="color:#059669;"></i>Income Information</div>
            <div class="card-body">
                <dl class="row mb-0" style="font-size:.875rem;">
                    <dt class="col-4" style="color:var(--muted);">Title</dt>
                    <dd class="col-8 mb-3" style="font-weight:600;">{{ $income->title }}</dd>
                    <dt class="col-4" style="color:var(--muted);">Category</dt>
                    <dd class="col-8 mb-3">
                        <span class="badge" style="background:rgba(5,150,105,.1);color:#059669;">{{ $income->incomeCategory?->name ?? '—' }}</span>
                    </dd>
                    <dt class="col-4" style="color:var(--muted);">Amount</dt>
                    <dd class="col-8 mb-3" style="font-weight:700;color:#059669;font-size:1.1rem;">?{{ number_format($income->amount, 2) }}</dd>
                    <dt class="col-4" style="color:var(--muted);">Date</dt>
                    <dd class="col-8 mb-3">{{ $income->income_date?->format('d M Y') ?? '—' }}</dd>
                    <dt class="col-4" style="color:var(--muted);">Payment Mode</dt>
                    <dd class="col-8 mb-3">{{ ucfirst($income->payment_mode ?? '—') }}</dd>
                    <dt class="col-4" style="color:var(--muted);">Reference No</dt>
                    <dd class="col-8 mb-3">{{ $income->reference_number ?? '—' }}</dd>
                    @if($income->description)
                    <dt class="col-4" style="color:var(--muted);">Description</dt>
                    <dd class="col-8 mb-0">{{ $income->description }}</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>
</div>

@endsection

