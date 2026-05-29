@extends('layouts.app')
@section('title', 'Edit Expense')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.expenses.index') }}" style="color:var(--primary);text-decoration:none;">Expenses</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-pen me-2" style="color:#dc2626;"></i>Edit Expense</h1>
        <p class="mb-0" style="color:var(--muted);font-size:.875rem;">Update expense record</p>
    </div>
    <a href="{{ route('admin.expenses.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Back
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header"><i class="fa-solid fa-pen me-2" style="color:#dc2626;"></i>Edit Expense Details</div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.expenses.update', $expense) }}">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title', $expense->title) }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Amount (?) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror"
                                   value="{{ old('amount', $expense->amount) }}" step="0.01" min="0" required>
                            @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Expense Date</label>
                            <input type="date" name="expense_date" class="form-control"
                                   value="{{ old('expense_date', $expense->expense_date?->toDateString()) }}">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Payment Mode</label>
                            <select name="payment_mode" class="form-select">
                                <option value="">Select Mode</option>
                                @foreach(['cash'=>'Cash','upi'=>'UPI','online'=>'Online','cheque'=>'Cheque'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('payment_mode', $expense->payment_mode) === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Bill Number</label>
                            <input type="text" name="bill_number" class="form-control"
                                   value="{{ old('bill_number', $expense->bill_number) }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Vendor Name</label>
                        <input type="text" name="vendor_name" class="form-control"
                               value="{{ old('vendor_name', $expense->vendor_name) }}">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $expense->description) }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-save me-2"></i> Update Expense
                        </button>
                        <a href="{{ route('admin.expenses.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

