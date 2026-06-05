@extends('layouts.app')
@section('title', 'Payment Entry Details')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.payment-register.index') }}">Payments Register</a>
    </li>
    <li class="breadcrumb-item active">Entry #{{ $paymentRegister->id }}</li>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fa-solid fa-eye me-2" style="color:var(--primary);"></i>
            Entry — {{ $paymentRegister->entry_date?->format('d M Y') }}
        </h1>
        <p class="mb-0" style="color:var(--muted);font-size:.875rem;">
            Financial Year: {{ $paymentRegister->academicYear?->name ?? '—' }}
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.payment-register.edit', $paymentRegister) }}"
           class="btn btn-primary btn-sm">
            <i class="fa-solid fa-pen me-1"></i> Edit
        </a>
        <a href="{{ route('admin.payment-register.index') }}"
           class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="fa-solid fa-indian-rupee-sign me-2" style="color:var(--primary);"></i>
        Payment Breakdown
        <span class="ms-auto" style="font-weight:700;font-size:.9rem;color:var(--primary);">
            Total: &#8377;{{ number_format($paymentRegister->total, 2) }}
        </span>
    </div>
    <div class="card-body">
        <div class="row g-3">
            @foreach($columns as $field => $label)
                @if((float) $paymentRegister->{$field} > 0)
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="p-3 rounded" style="background:var(--surface2);border:1px solid var(--border);">
                        <div style="font-size:.72rem;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.25rem;">
                            {{ $label }}
                        </div>
                        <div style="font-size:1.1rem;font-weight:700;color:var(--text);">
                            &#8377;{{ number_format($paymentRegister->{$field}, 2) }}
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>

        @if($paymentRegister->remarks)
        <div class="mt-3 p-3 rounded" style="background:var(--surface2);border:1px solid var(--border);">
            <div style="font-size:.72rem;color:var(--muted);font-weight:600;text-transform:uppercase;margin-bottom:.25rem;">
                Remarks
            </div>
            <div style="font-size:.875rem;">{{ $paymentRegister->remarks }}</div>
        </div>
        @endif
    </div>
</div>

@endsection
