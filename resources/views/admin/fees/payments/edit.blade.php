@extends('layouts.app')

@section('title', 'Edit Payment')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.fees.payments.index') }}" style="color:#4f46e5;text-decoration:none;">Payments</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-pen me-2" style="color:#4f46e5;"></i>Edit Payment</h1>
        <p class="mb-0" style="color:var(--muted);font-size:0.875rem;">{{ $payment->receipt_number }} — {{ $payment->student?->full_name }}</p>
    </div>
    <a href="{{ route('admin.fees.payments.show', $payment) }}" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-2"></i> Back
    </a>
</div>

<div class="card" style="max-width:600px;">
    <div class="card-header"><i class="fa-solid fa-pen me-2" style="color:#4f46e5;"></i>Update Payment Details</div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.fees.payments.update', $payment) }}">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Payment Mode <span class="text-danger">*</span></label>
                <select name="payment_mode" class="form-select @error('payment_mode') is-invalid @enderror" required>
                    @foreach(\App\Models\FeePayment::PAYMENT_MODES as $mode => $label)
                        <option value="{{ $mode }}" {{ old('payment_mode', $payment->payment_mode) === $mode ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('payment_mode')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Transaction Reference</label>
                <input type="text" name="transaction_reference" class="form-control"
                       value="{{ old('transaction_reference', $payment->transaction_reference) }}"
                       placeholder="UTR / Cheque No.">
            </div>
            <div class="mb-4">
                <label class="form-label">Remarks</label>
                <textarea name="remarks" class="form-control" rows="3">{{ old('remarks', $payment->remarks) }}</textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fa-solid fa-floppy-disk me-2"></i> Save Changes
                </button>
                <a href="{{ route('admin.fees.payments.show', $payment) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

