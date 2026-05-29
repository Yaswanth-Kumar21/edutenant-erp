@extends('layouts.app')
@section('title', 'Expense Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.expenses.index') }}" style="color:var(--primary);text-decoration:none;">Expenses</a></li>
    <li class="breadcrumb-item active">{{ $expense->title }}</li>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-receipt me-2" style="color:#dc2626;"></i>Expense Details</h1>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.expenses.edit', $expense) }}" class="btn btn-outline-primary btn-sm">
            <i class="fa-solid fa-pen me-1"></i> Edit
        </a>
        <a href="{{ route('admin.expenses.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header"><i class="fa-solid fa-info-circle me-2" style="color:#dc2626;"></i>Expense Information</div>
            <div class="card-body">
                <dl class="row mb-0" style="font-size:.875rem;">
                    <dt class="col-4" style="color:var(--muted);">Title</dt>
                    <dd class="col-8 mb-3" style="font-weight:600;">{{ $expense->title }}</dd>
                    <dt class="col-4" style="color:var(--muted);">Category</dt>
                    <dd class="col-8 mb-3">
                        <span class="badge" style="background:rgba(220,38,38,.1);color:#dc2626;">{{ $expense->expenseCategory?->name ?? '—' }}</span>
                    </dd>
                    <dt class="col-4" style="color:var(--muted);">Amount</dt>
                    <dd class="col-8 mb-3" style="font-weight:700;color:#dc2626;font-size:1.1rem;">?{{ number_format($expense->amount, 2) }}</dd>
                    <dt class="col-4" style="color:var(--muted);">Date</dt>
                    <dd class="col-8 mb-3">{{ $expense->expense_date?->format('d M Y') ?? '—' }}</dd>
                    <dt class="col-4" style="color:var(--muted);">Payment Mode</dt>
                    <dd class="col-8 mb-3">{{ ucfirst($expense->payment_mode ?? '—') }}</dd>
                    <dt class="col-4" style="color:var(--muted);">Bill Number</dt>
                    <dd class="col-8 mb-3">{{ $expense->bill_number ?? '—' }}</dd>
                    <dt class="col-4" style="color:var(--muted);">Vendor</dt>
                    <dd class="col-8 mb-3">{{ $expense->vendor_name ?? '—' }}</dd>
                    @if($expense->description)
                    <dt class="col-4" style="color:var(--muted);">Description</dt>
                    <dd class="col-8 mb-0">{{ $expense->description }}</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>
</div>

@endsection

