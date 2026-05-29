@extends('layouts.app')

@section('title', 'Collect Fee')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.fees.dashboard') }}" style="color:#4f46e5;text-decoration:none;">Fees</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.fees.payments.index') }}" style="color:#4f46e5;text-decoration:none;">Payments</a>
    </li>
    <li class="breadcrumb-item active">Collect Fee</li>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fa-solid fa-hand-holding-dollar me-2" style="color:#4f46e5;"></i>
            Collect Fee Payment
        </h1>
        <p class="mb-0" style="color:var(--muted);font-size:0.875rem;">
            Record a fee payment for a student
        </p>
    </div>
    <a href="{{ route('admin.fees.payments.index') }}" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-2"></i> Back
    </a>
</div>

@if($errors->any())
<div class="alert alert-danger d-flex gap-2 mb-4" style="border-radius:0.75rem;">
    <i class="fa-solid fa-circle-exclamation mt-1 flex-shrink-0"></i>
    <div>
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-1 ps-3">
            @foreach($errors->all() as $error)
                <li style="font-size:0.875rem;">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

<form method="POST" action="{{ route('admin.fees.payments.store') }}" id="fee-form">
    @csrf

    <div class="row g-4">
        {{-- Left: Form --}}
        <div class="col-lg-7">

            {{-- Student Selection --}}
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fa-solid fa-user-graduate me-2" style="color:#4f46e5;"></i>
                    Student Details
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Student <span class="text-danger">*</span></label>
                            <select name="student_id" id="student_id"
                                    class="form-select @error('student_id') is-invalid @enderror"
                                    required onchange="loadStudentInfo(this.value)">
                                <option value="">— Search and select student —</option>
                                @foreach($students as $s)
                                    <option value="{{ $s->id }}"
                                        {{ (old('student_id') == $s->id || ($student && $student->id == $s->id)) ? 'selected' : '' }}>
                                        {{ $s->admission_number }} — {{ $s->first_name }} {{ $s->last_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Student info preview --}}
                        <div class="col-12" id="student-info-box" style="display:{{ $student ? 'block' : 'none' }};">
                            <div class="p-3 rounded" style="background:rgba(79,70,229,0.05);border:1px solid rgba(79,70,229,0.15);">
                                <div class="d-flex align-items-center gap-3">
                                    <div id="student-avatar"
                                         style="width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,#4f46e5,#7c3aed);
                                                display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:1rem;flex-shrink:0;">
                                        {{ $student ? strtoupper(substr($student->first_name, 0, 1)) : 'S' }}
                                    </div>
                                    <div>
                                        <div id="student-name" style="font-weight:600;">
                                            {{ $student?->full_name }}
                                        </div>
                                        <div id="student-meta" style="font-size:0.78rem;color:var(--muted);">
                                            {{ $student?->branch?->name }} &bull; {{ $student?->category }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Fee Details --}}
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fa-solid fa-tags me-2" style="color:#059669;"></i>
                    Fee Details
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Fee Type <span class="text-danger">*</span></label>
                            <select name="fee_type_id" id="fee_type_id"
                                    class="form-select @error('fee_type_id') is-invalid @enderror"
                                    required onchange="updateAmountDue(this)">
                                <option value="">— Select Fee Type —</option>
                                @foreach($feeTypes as $ft)
                                    <option value="{{ $ft->id }}"
                                            data-amount="{{ $ft->amount }}"
                                        {{ old('fee_type_id') == $ft->id ? 'selected' : '' }}>
                                        {{ $ft->name }}
                                        @if($ft->amount > 0) (?{{ number_format($ft->amount) }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('fee_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                            <select name="academic_year_id"
                                    class="form-select @error('academic_year_id') is-invalid @enderror" required>
                                <option value="">— Select Year —</option>
                                @foreach($academicYears as $yr)
                                    <option value="{{ $yr->id }}"
                                        {{ (old('academic_year_id') == $yr->id || (!old('academic_year_id') && $yr->is_current)) ? 'selected' : '' }}>
                                        {{ $yr->name }} @if($yr->is_current) (Current) @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('academic_year_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Semester</label>
                            <select name="semester" class="form-select">
                                <option value="">N/A</option>
                                @for($i = 1; $i <= 8; $i++)
                                    <option value="{{ $i }}" {{ old('semester') == $i ? 'selected' : '' }}>
                                        Semester {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Amount Due (?) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:var(--bg);border-color:var(--border);">?</span>
                                <input type="number" name="amount_due" id="amount_due"
                                       class="form-control @error('amount_due') is-invalid @enderror"
                                       value="{{ old('amount_due', 0) }}" min="0" step="0.01"
                                       required oninput="recalculate()">
                            </div>
                            @error('amount_due')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Discount (?)</label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:var(--bg);border-color:var(--border);">?</span>
                                <input type="number" name="discount" id="discount"
                                       class="form-control" value="{{ old('discount', 0) }}"
                                       min="0" step="0.01" oninput="recalculate()">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fine / Late Fee (?)</label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:var(--bg);border-color:var(--border);">?</span>
                                <input type="number" name="fine" id="fine"
                                       class="form-control" value="{{ old('fine', 0) }}"
                                       min="0" step="0.01" oninput="recalculate()">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Amount Paid (?) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:var(--bg);border-color:var(--border);">?</span>
                                <input type="number" name="amount_paid" id="amount_paid"
                                       class="form-control @error('amount_paid') is-invalid @enderror"
                                       value="{{ old('amount_paid', 0) }}" min="0" step="0.01"
                                       required oninput="recalculate()">
                            </div>
                            @error('amount_paid')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Payment Details --}}
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fa-solid fa-credit-card me-2" style="color:#7c3aed;"></i>
                    Payment Details
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Payment Mode <span class="text-danger">*</span></label>
                            <div class="row g-2">
                                @foreach(['cash' => ['Cash','fa-money-bill-wave','#059669'], 'upi' => ['UPI','fa-mobile-screen','#4f46e5'], 'card' => ['Card','fa-credit-card','#7c3aed'], 'bank_transfer' => ['Bank Transfer','fa-building-columns','#0891b2'], 'cheque' => ['Cheque','fa-file-invoice','#d97706'], 'dd' => ['DD','fa-file-contract','#6b7280']] as $mode => [$label, $icon, $color])
                                <div class="col-6">
                                    <label class="d-flex align-items-center gap-2 p-2 rounded"
                                           style="border:2px solid var(--border);cursor:pointer;transition:all 0.2s;"
                                           id="mode-label-{{ $mode }}">
                                        <input type="radio" name="payment_mode" value="{{ $mode }}"
                                               class="form-check-input m-0"
                                               {{ old('payment_mode', 'cash') === $mode ? 'checked' : '' }}
                                               onchange="highlightMode('{{ $mode }}')">
                                        <i class="fa-solid {{ $icon }}" style="color:{{ $color }};font-size:0.9rem;"></i>
                                        <span style="font-size:0.82rem;font-weight:500;">{{ $label }}</span>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            @error('payment_mode')<div class="text-danger mt-1" style="font-size:0.8rem;">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                                <input type="date" name="payment_date"
                                       class="form-control @error('payment_date') is-invalid @enderror"
                                       value="{{ old('payment_date', now()->format('Y-m-d')) }}" required>
                                @error('payment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div>
                                <label class="form-label">Transaction Reference</label>
                                <input type="text" name="transaction_reference"
                                       class="form-control"
                                       value="{{ old('transaction_reference') }}"
                                       placeholder="UTR / Cheque No. / Ref ID">
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Remarks</label>
                            <textarea name="remarks" class="form-control" rows="2"
                                      placeholder="Optional payment remarks...">{{ old('remarks') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Summary --}}
        <div class="col-lg-5">
            <div class="card" style="position:sticky;top:80px;">
                <div class="card-header">
                    <i class="fa-solid fa-calculator me-2" style="color:#4f46e5;"></i>
                    Payment Summary
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid var(--border);font-size:0.875rem;">
                        <span style="color:var(--muted);">Amount Due</span>
                        <span id="sum-due" style="font-weight:600;">?0</span>
                    </div>
                    <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid var(--border);font-size:0.875rem;">
                        <span style="color:var(--muted);">Discount</span>
                        <span id="sum-discount" style="color:#059669;">- ?0</span>
                    </div>
                    <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid var(--border);font-size:0.875rem;">
                        <span style="color:var(--muted);">Fine / Late Fee</span>
                        <span id="sum-fine" style="color:#dc2626;">+ ?0</span>
                    </div>
                    <div class="d-flex justify-content-between py-2" style="border-bottom:2px solid var(--border);font-size:0.875rem;font-weight:600;">
                        <span>Net Payable</span>
                        <span id="sum-net" style="color:#4f46e5;">?0</span>
                    </div>
                    <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid var(--border);font-size:0.875rem;">
                        <span style="color:var(--muted);">Amount Paid</span>
                        <span id="sum-paid" style="color:#059669;font-weight:600;">?0</span>
                    </div>
                    <div class="d-flex justify-content-between py-3" style="font-size:1rem;font-weight:700;">
                        <span>Balance Due</span>
                        <span id="sum-balance" style="color:#dc2626;">?0</span>
                    </div>

                    {{-- Status indicator --}}
                    <div id="status-badge" class="text-center p-3 rounded mb-4"
                         style="background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.2);">
                        <i class="fa-solid fa-circle-check" style="color:#059669;"></i>
                        <span id="status-text" style="font-size:0.875rem;font-weight:600;color:#059669;margin-left:0.5rem;">
                            Fully Paid
                        </span>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3" id="submit-btn"
                            style="font-size:1rem;font-weight:600;">
                        <i class="fa-solid fa-check me-2"></i> Record Payment
                    </button>
                    <a href="{{ route('admin.fees.payments.index') }}"
                       class="btn btn-outline-secondary w-100 mt-2">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
(function () {
    const fmt = v => '?' + parseFloat(v || 0).toLocaleString('en-IN', {minimumFractionDigits: 0});

    window.recalculate = function () {
        const due      = parseFloat(document.getElementById('amount_due').value)  || 0;
        const discount = parseFloat(document.getElementById('discount').value)    || 0;
        const fine     = parseFloat(document.getElementById('fine').value)        || 0;
        const paid     = parseFloat(document.getElementById('amount_paid').value) || 0;
        const net      = Math.max(0, due - discount + fine);
        const balance  = Math.max(0, net - paid);

        document.getElementById('sum-due').textContent      = fmt(due);
        document.getElementById('sum-discount').textContent = '- ' + fmt(discount);
        document.getElementById('sum-fine').textContent     = '+ ' + fmt(fine);
        document.getElementById('sum-net').textContent      = fmt(net);
        document.getElementById('sum-paid').textContent     = fmt(paid);
        document.getElementById('sum-balance').textContent  = fmt(balance);

        const badge = document.getElementById('status-badge');
        const text  = document.getElementById('status-text');

        if (net === 0 || paid >= net) {
            badge.style.background   = 'rgba(5,150,105,0.08)';
            badge.style.borderColor  = 'rgba(5,150,105,0.2)';
            text.style.color         = '#059669';
            text.innerHTML = '<i class="fa-solid fa-circle-check me-1"></i>Fully Paid';
        } else if (paid > 0) {
            badge.style.background   = 'rgba(217,119,6,0.08)';
            badge.style.borderColor  = 'rgba(217,119,6,0.2)';
            text.style.color         = '#d97706';
            text.innerHTML = '<i class="fa-solid fa-circle-half-stroke me-1"></i>Partial Payment';
        } else {
            badge.style.background   = 'rgba(220,38,38,0.08)';
            badge.style.borderColor  = 'rgba(220,38,38,0.2)';
            text.style.color         = '#dc2626';
            text.innerHTML = '<i class="fa-solid fa-circle-xmark me-1"></i>Pending';
        }
    };

    window.updateAmountDue = function (select) {
        const opt    = select.options[select.selectedIndex];
        const amount = parseFloat(opt.dataset.amount) || 0;
        if (amount > 0) {
            document.getElementById('amount_due').value  = amount;
            document.getElementById('amount_paid').value = amount;
        }
        recalculate();
    };

    window.highlightMode = function (mode) {
        document.querySelectorAll('[id^="mode-label-"]').forEach(el => {
            el.style.borderColor = 'var(--border)';
            el.style.background  = 'transparent';
        });
        const active = document.getElementById('mode-label-' + mode);
        if (active) {
            active.style.borderColor = '#4f46e5';
            active.style.background  = 'rgba(79,70,229,0.06)';
        }
    };

    // Init
    const checkedMode = document.querySelector('[name="payment_mode"]:checked');
    if (checkedMode) highlightMode(checkedMode.value);
    recalculate();

    window.loadStudentInfo = function (id) {
        const box = document.getElementById('student-info-box');
        if (!id) { box.style.display = 'none'; return; }
        // Simple display from select option text
        const sel  = document.getElementById('student_id');
        const text = sel.options[sel.selectedIndex].text;
        const parts = text.split(' — ');
        document.getElementById('student-name').textContent = parts[1] || text;
        document.getElementById('student-meta').textContent = parts[0] || '';
        document.getElementById('student-avatar').textContent = (parts[1] || 'S').charAt(0).toUpperCase();
        box.style.display = 'block';
    };

    // Init student info if pre-selected
    const sel = document.getElementById('student_id');
    if (sel && sel.value) loadStudentInfo(sel.value);

    // Submit loading state
    document.getElementById('fee-form').addEventListener('submit', function () {
        const btn = document.getElementById('submit-btn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
    });
})();
</script>
@endpush

