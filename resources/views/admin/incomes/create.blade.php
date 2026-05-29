@extends('layouts.app')
@section('title', 'Add Income')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.incomes.index') }}" style="color:var(--primary);text-decoration:none;">Income</a></li>
    <li class="breadcrumb-item active">Add Income</li>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-arrow-trend-up me-2" style="color:#059669;"></i>Add Income</h1>
        <p class="mb-0" style="color:var(--muted);font-size:.875rem;">Record a new income entry</p>
    </div>
    <a href="{{ route('admin.incomes.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Back
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header"><i class="fa-solid fa-plus me-2" style="color:#059669;"></i>Income Details</div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.incomes.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title') }}" placeholder="e.g. Tuition Fee Collection" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select name="income_category_id" class="form-select @error('income_category_id') is-invalid @enderror" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('income_category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('income_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Amount (?) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror"
                                   value="{{ old('amount') }}" placeholder="0.00" step="0.01" min="0" required>
                            @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Income Date <span class="text-danger">*</span></label>
                            <input type="date" name="income_date" class="form-control @error('income_date') is-invalid @enderror"
                                   value="{{ old('income_date', today()->toDateString()) }}" required>
                            @error('income_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Payment Mode</label>
                            <select name="payment_mode" class="form-select">
                                <option value="">Select Mode</option>
                                @foreach(['cash'=>'Cash','upi'=>'UPI','bank_transfer'=>'Bank Transfer','cheque'=>'Cheque','online'=>'Online'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('payment_mode') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reference Number</label>
                        <input type="text" name="reference_number" class="form-control"
                               value="{{ old('reference_number') }}" placeholder="Transaction ID / Cheque No">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"
                                  placeholder="Additional notes...">{{ old('description') }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-save me-2"></i> Save Income
                        </button>
                        <a href="{{ route('admin.incomes.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

