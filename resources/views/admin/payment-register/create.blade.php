@extends('layouts.app')
@section('title', 'New Payment Entry')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.payment-register.index') }}">Payments Register</a>
    </li>
    <li class="breadcrumb-item active">New Entry</li>
@endsection

@push('styles')
<style>
.payment-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 1rem;
}
.amount-input-group .input-group-text {
    font-size: .8rem;
    min-width: 28px;
    justify-content: center;
}
#live-total {
    font-size: 1.6rem;
    font-weight: 800;
    color: var(--primary);
}
</style>
@endpush

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fa-solid fa-plus-circle me-2" style="color:var(--primary);"></i>New Payment Entry
        </h1>
        <p class="mb-0" style="color:var(--muted);font-size:.875rem;">
            Record a daily payments register entry
        </p>
    </div>
    <a href="{{ route('admin.payment-register.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Back
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger mb-4">
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $err)
                <li style="font-size:.875rem;">{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.payment-register.store') }}" id="payment-form">
    @csrf

    {{-- Header fields --}}
    <div class="card mb-4">
        <div class="card-header">
            <i class="fa-solid fa-calendar-alt me-2" style="color:var(--primary);"></i>Entry Details
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Financial Year <span class="text-danger">*</span></label>
                    <select name="academic_year_id" class="form-select @error('academic_year_id') is-invalid @enderror" required>
                        <option value="">Select Year</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}"
                                {{ (old('academic_year_id', $currentYear?->id) == $year->id) ? 'selected' : '' }}>
                                {{ $year->name }} {{ $year->is_current ? '(Current)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('academic_year_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Date <span class="text-danger">*</span></label>
                    <input type="date" name="entry_date"
                           class="form-control @error('entry_date') is-invalid @enderror"
                           value="{{ old('entry_date', today()->toDateString()) }}" required>
                    @error('entry_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Remarks</label>
                    <input type="text" name="remarks"
                           class="form-control @error('remarks') is-invalid @enderror"
                           value="{{ old('remarks') }}"
                           placeholder="Optional notes…">
                    @error('remarks')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Payment amounts grid --}}
    <div class="card mb-4">
        <div class="card-header">
            <i class="fa-solid fa-indian-rupee-sign me-2" style="color:var(--primary);"></i>
            Payment Amounts
            <span class="ms-auto text-muted" style="font-size:.78rem;font-weight:400;">
                Leave blank or enter 0 for categories not applicable today
            </span>
        </div>
        <div class="card-body">
            <div class="payment-grid">
                @foreach($columns as $field => $label)
                <div>
                    <label class="form-label" style="font-size:.8rem;">{{ $label }}</label>
                    <div class="input-group input-group-sm amount-input-group">
                        <span class="input-group-text">&#8377;</span>
                        <input type="number" name="{{ $field }}"
                               class="form-control payment-amount @error($field) is-invalid @enderror"
                               value="{{ old($field, '0') }}"
                               min="0" step="0.01"
                               placeholder="0.00">
                        @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Live total & submit --}}
    <div class="card mb-4">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <div style="font-size:.78rem;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;">
                    Row Total
                </div>
                <div id="live-total">&#8377;0.00</div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.payment-register.index') }}"
                   class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-save me-1"></i> Save Entry
                </button>
            </div>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
(function () {
    const inputs  = document.querySelectorAll('.payment-amount');
    const display = document.getElementById('live-total');

    function recalc() {
        let sum = 0;
        inputs.forEach(i => { sum += parseFloat(i.value) || 0; });
        display.textContent = '₹' + sum.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    inputs.forEach(i => i.addEventListener('input', recalc));
    recalc(); // initial
})();
</script>
@endpush
