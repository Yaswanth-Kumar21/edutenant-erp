@extends('layouts.app')
@section('title', 'Payroll Details')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-file-invoice-dollar me-2" style="color:#d97706;"></i>Payroll Details</h1>
        <p class="mb-0" style="color:var(--muted);font-size:.875rem;">{{ $payroll->pay_period }} — {{ $payroll->staff->name }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.payroll.slip', $payroll) }}" class="btn btn-outline-primary btn-sm" target="_blank">
            <i class="fa-solid fa-file-pdf me-1"></i> Salary Slip
        </a>
        <a href="{{ route('admin.payroll.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-body text-center p-4">
                <img src="{{ $payroll->staff->photo_url }}" class="rounded-circle mb-3"
                     style="width:80px;height:80px;object-fit:cover;border:3px solid var(--border);" alt="">
                <h5 class="mb-1 fw-700" style="font-weight:700;">{{ $payroll->staff->name }}</h5>
                <p class="mb-2" style="color:var(--muted);font-size:.875rem;">{{ $payroll->staff->designation ?? '—' }}</p>
                <span class="badge" style="background:rgba(79,70,229,.1);color:#4f46e5;">{{ $payroll->staff->staff_code ?? 'N/A' }}</span>
                <hr style="border-color:var(--border);">
                <div class="text-start">
                    <div class="d-flex justify-content-between mb-2">
                        <span style="font-size:.85rem;color:var(--muted);">Pay Period</span>
                        <span style="font-weight:600;font-size:.875rem;">{{ $payroll->pay_period }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span style="font-size:.85rem;color:var(--muted);">Payroll No.</span>
                        <span style="font-size:.85rem;">{{ $payroll->payroll_number ?? '—' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span style="font-size:.85rem;color:var(--muted);">Status</span>
                        @if($payroll->isPaid())
                            <span class="badge" style="background:#dcfce7;color:#166534;">Paid</span>
                        @elseif($payroll->isApproved())
                            <span class="badge" style="background:#dbeafe;color:#1e40af;">Approved</span>
                        @else
                            <span class="badge bg-secondary">Draft</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="card">
            <div class="card-body">
                @if($payroll->isDraft())
                <form method="POST" action="{{ route('admin.payroll.approve', $payroll) }}" class="mb-2">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-success w-100 btn-sm">
                        <i class="fa-solid fa-check me-1"></i> Approve Payroll
                    </button>
                </form>
                @endif
                @if($payroll->isApproved())
                <button type="button" class="btn btn-warning w-100 btn-sm mb-2"
                        data-bs-toggle="modal" data-bs-target="#markPaidModal">
                    <i class="fa-solid fa-money-bill me-1"></i> Mark as Paid
                </button>
                @endif
                @if($payroll->isPaid())
                <div class="alert alert-success py-2 mb-2" style="font-size:.85rem;">
                    <i class="fa-solid fa-check-circle me-1"></i>
                    Paid on {{ $payroll->payment_date?->format('d M Y') }}
                    via {{ ucfirst(str_replace('_', ' ', $payroll->payment_mode)) }}
                    @if($payroll->transaction_reference)
                        <br><small>Ref: {{ $payroll->transaction_reference }}</small>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        {{-- Attendance Summary --}}
        <div class="card mb-4">
            <div class="card-header"><h6 class="mb-0 fw-600" style="font-weight:600;"><i class="fa-solid fa-calendar-check me-2" style="color:#4f46e5;"></i>Attendance Summary</h6></div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach([
                        ['label' => 'Working Days',    'value' => $payroll->working_days,    'color' => '#4f46e5'],
                        ['label' => 'Present Days',    'value' => $payroll->present_days,    'color' => '#059669'],
                        ['label' => 'Absent Days',     'value' => $payroll->absent_days,     'color' => '#dc2626'],
                        ['label' => 'Leave Days',      'value' => $payroll->leave_days,      'color' => '#d97706'],
                        ['label' => 'Half Days',       'value' => $payroll->half_days,       'color' => '#0891b2'],
                        ['label' => 'Allowed Holidays','value' => $payroll->allowed_holidays,'color' => '#6b7280'],
                    ] as $item)
                    <div class="col-4 col-md-2">
                        <div class="card text-center p-2" style="border-color:var(--border);">
                            <div style="font-size:1.4rem;font-weight:700;color:{{ $item['color'] }};">{{ $item['value'] }}</div>
                            <div style="font-size:.68rem;color:var(--muted);">{{ $item['label'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Salary Breakdown --}}
        <div class="card">
            <div class="card-header"><h6 class="mb-0 fw-600" style="font-weight:600;"><i class="fa-solid fa-indian-rupee-sign me-2" style="color:#d97706;"></i>Salary Breakdown</h6></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <h6 style="font-size:.8rem;font-weight:600;text-transform:uppercase;color:var(--muted);margin-bottom:.75rem;">Earnings</h6>
                        @foreach([
                            ['label' => 'Basic Salary',      'value' => $payroll->basic_salary],
                            ['label' => 'HRA',               'value' => $payroll->hra],
                            ['label' => 'DA',                'value' => $payroll->da],
                            ['label' => 'Other Allowances',  'value' => $payroll->other_allowances],
                        ] as $item)
                        @if($item['value'] > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span style="font-size:.875rem;color:var(--muted);">{{ $item['label'] }}</span>
                            <span style="font-size:.875rem;">₹{{ number_format($item['value']) }}</span>
                        </div>
                        @endif
                        @endforeach
                        <hr style="border-color:var(--border);">
                        <div class="d-flex justify-content-between">
                            <span style="font-weight:600;">Gross Salary</span>
                            <span style="font-weight:700;">₹{{ number_format($payroll->gross_salary) }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 style="font-size:.8rem;font-weight:600;text-transform:uppercase;color:var(--muted);margin-bottom:.75rem;">Deductions</h6>
                        @foreach([
                            ['label' => 'Absent Deduction', 'value' => $payroll->absent_deduction],
                            ['label' => 'PF Deduction',     'value' => $payroll->pf_deduction],
                            ['label' => 'Tax Deduction',    'value' => $payroll->tax_deduction],
                            ['label' => 'Other Deductions', 'value' => $payroll->other_deductions],
                        ] as $item)
                        @if($item['value'] > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span style="font-size:.875rem;color:var(--muted);">{{ $item['label'] }}</span>
                            <span style="font-size:.875rem;color:#dc2626;">-₹{{ number_format($item['value']) }}</span>
                        </div>
                        @endif
                        @endforeach
                        <hr style="border-color:var(--border);">
                        <div class="d-flex justify-content-between">
                            <span style="font-weight:600;">Total Deductions</span>
                            <span style="font-weight:700;color:#dc2626;">-₹{{ number_format($payroll->total_deductions) }}</span>
                        </div>
                    </div>
                </div>
                <div class="mt-3 p-3 rounded" style="background:rgba(5,150,105,.08);border:1px solid rgba(5,150,105,.2);">
                    <div class="d-flex justify-content-between align-items-center">
                        <span style="font-weight:700;font-size:1rem;">Net Salary Payable</span>
                        <span style="font-size:1.6rem;font-weight:800;color:#059669;">
                            ₹{{ number_format($payroll->net_salary) }}
                        </span>
                    </div>
                    <div style="font-size:.78rem;color:var(--muted);margin-top:.25rem;">
                        Per day rate: ₹{{ number_format($payroll->per_day_salary) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Mark Paid Modal --}}
<div class="modal fade" id="markPaidModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background:var(--surface);border-color:var(--border);">
            <div class="modal-header" style="border-color:var(--border);">
                <h5 class="modal-title fw-600" style="font-weight:600;">Mark Salary as Paid</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.payroll.mark-paid', $payroll) }}">
                @csrf @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Payment Mode <span class="text-danger">*</span></label>
                        <select name="payment_mode" class="form-select" required>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cash">Cash</option>
                            <option value="cheque">Cheque</option>
                            <option value="upi">UPI</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control"
                               value="{{ today()->toDateString() }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Transaction Reference</label>
                        <input type="text" name="transaction_reference" class="form-control">
                    </div>
                </div>
                <div class="modal-footer" style="border-color:var(--border);">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm">Confirm Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

