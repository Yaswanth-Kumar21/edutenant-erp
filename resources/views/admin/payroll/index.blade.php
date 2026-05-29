@extends('layouts.app')
@section('title', 'Payroll')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-file-invoice-dollar me-2" style="color:#d97706;"></i>Payroll Management</h1>
        <p class="mb-0" style="color:var(--muted);font-size:.875rem;">Monthly salary processing and payroll records</p>
    </div>
    <div class="d-flex gap-2">
        <div class="dropdown">
            <button class="btn btn-outline-success btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fa-solid fa-file-excel me-1"></i> Export
            </button>
            <ul class="dropdown-menu shadow border-0" style="border-radius:.75rem;background:var(--surface);">
                <li>
                    <a class="dropdown-item" style="font-size:.85rem;"
                       href="{{ route('admin.exports.payroll', ['month'=>$month,'year'=>$year,'format'=>'xlsx']) }}">
                        <i class="fa-solid fa-file-excel me-2" style="color:#059669;"></i> Excel (.xlsx)
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" style="font-size:.85rem;"
                       href="{{ route('admin.exports.payroll', ['month'=>$month,'year'=>$year,'format'=>'csv']) }}">
                        <i class="fa-solid fa-file-csv me-2" style="color:#0891b2;"></i> CSV
                    </a>
                </li>
            </ul>
        </div>
        <a href="{{ route('admin.payroll.summary', ['month' => $month, 'year' => $year]) }}"
           class="btn btn-outline-primary btn-sm">
            <i class="fa-solid fa-chart-bar me-1"></i> Summary
        </a>
        <a href="{{ route('admin.payroll.generate', ['month' => $month, 'year' => $year]) }}"
           class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus me-1"></i> Generate Payroll
        </a>
    </div>
</div>

{{-- Month Summary Cards --}}
@if($summary && $summary->count > 0)
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div style="font-size:1.6rem;font-weight:800;color:#4f46e5;">{{ $summary->count }}</div>
            <div style="font-size:.78rem;color:var(--muted);">Staff Processed</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div style="font-size:1.3rem;font-weight:800;color:#059669;">₹{{ number_format($summary->total_gross) }}</div>
            <div style="font-size:.78rem;color:var(--muted);">Total Gross</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div style="font-size:1.3rem;font-weight:800;color:#dc2626;">₹{{ number_format($summary->total_deductions) }}</div>
            <div style="font-size:.78rem;color:var(--muted);">Total Deductions</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div style="font-size:1.3rem;font-weight:800;color:#d97706;">₹{{ number_format($summary->total_net) }}</div>
            <div style="font-size:.78rem;color:var(--muted);">Net Payable</div>
        </div>
    </div>
</div>
@endif

{{-- Filters --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-6 col-md-2">
                <label class="form-label mb-1" style="font-size:.8rem;">Month</label>
                <select name="month" class="form-select form-select-sm">
                    @foreach(range(1,12) as $m)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1" style="font-size:.8rem;">Year</label>
                <select name="year" class="form-select form-select-sm">
                    @foreach(range(now()->year, now()->year - 3) as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1" style="font-size:.8rem;">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="draft"    {{ request('status') === 'draft'    ? 'selected' : '' }}>Draft</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="paid"     {{ request('status') === 'paid'     ? 'selected' : '' }}>Paid</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1" style="font-size:.8rem;">Staff</label>
                <select name="staff_id" class="form-select form-select-sm">
                    <option value="">All Staff</option>
                    @foreach($staffList as $s)
                        <option value="{{ $s->id }}" {{ request('staff_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="fa-solid fa-filter me-1"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Payroll Table --}}
<div class="card">
    <div class="card-header">
        <span style="font-weight:600;">Payroll Records
            <span class="badge ms-2" style="background:rgba(217,119,6,.1);color:#d97706;">{{ $payrolls->total() }}</span>
        </span>
    </div>

    @if($payrolls->isEmpty())
        <div class="card-body">
            <div class="empty-state py-4">
                <i class="fa-solid fa-file-invoice-dollar" style="font-size:3rem;opacity:.2;"></i>
                <h5 class="mt-3" style="color:var(--muted);">No payroll records found</h5>
                <a href="{{ route('admin.payroll.generate') }}" class="btn btn-primary btn-sm mt-2">
                    Generate Payroll
                </a>
            </div>
        </div>
    @else
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Staff Member</th>
                        <th class="text-center">Period</th>
                        <th class="text-center">Present</th>
                        <th class="text-center">Absent</th>
                        <th class="text-center">Gross</th>
                        <th class="text-center">Deductions</th>
                        <th class="text-center">Net Salary</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payrolls as $payroll)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $payroll->staff->photo_url }}" class="rounded-circle"
                                     style="width:30px;height:30px;object-fit:cover;" alt="">
                                <div>
                                    <div style="font-weight:500;font-size:.875rem;">{{ $payroll->staff->name }}</div>
                                    <div style="font-size:.72rem;color:var(--muted);">{{ $payroll->staff->designation ?? $payroll->staff->staff_type }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center" style="font-size:.85rem;font-weight:500;">{{ $payroll->pay_period }}</td>
                        <td class="text-center"><span class="badge" style="background:#dcfce7;color:#166534;">{{ $payroll->present_days }}</span></td>
                        <td class="text-center"><span class="badge" style="background:#fee2e2;color:#991b1b;">{{ $payroll->absent_days }}</span></td>
                        <td class="text-center" style="font-size:.85rem;">₹{{ number_format($payroll->gross_salary) }}</td>
                        <td class="text-center" style="font-size:.85rem;color:#dc2626;">-₹{{ number_format($payroll->total_deductions) }}</td>
                        <td class="text-center" style="font-weight:700;color:#059669;">₹{{ number_format($payroll->net_salary) }}</td>
                        <td class="text-center">
                            @if($payroll->isPaid())
                                <span class="badge" style="background:#dcfce7;color:#166534;">Paid</span>
                            @elseif($payroll->isApproved())
                                <span class="badge" style="background:#dbeafe;color:#1e40af;">Approved</span>
                            @else
                                <span class="badge bg-secondary">Draft</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="{{ route('admin.payroll.show', $payroll) }}"
                                   class="btn btn-sm btn-outline-primary" style="padding:.2rem .5rem;" title="View">
                                    <i class="fa-solid fa-eye" style="font-size:.75rem;"></i>
                                </a>
                                <a href="{{ route('admin.pdf.payroll-slip', $payroll) }}"
                                   class="btn btn-sm btn-outline-danger" style="padding:.2rem .5rem;" title="Download PDF Slip" target="_blank">
                                    <i class="fa-solid fa-file-pdf" style="font-size:.75rem;"></i>
                                </a>
                                @if($payroll->staff?->user?->email || $payroll->staff?->email)
                                <form method="POST" action="{{ route('admin.notifications.send.payroll', $payroll) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success"
                                            style="padding:.2rem .5rem;" title="Email Payslip">
                                        <i class="fa-solid fa-envelope" style="font-size:.75rem;"></i>
                                    </button>
                                </form>
                                @endif
                                @if($payroll->isDraft())
                                <form method="POST" action="{{ route('admin.payroll.approve', $payroll) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-success"
                                            style="padding:.2rem .5rem;" title="Approve">
                                        <i class="fa-solid fa-check" style="font-size:.75rem;"></i>
                                    </button>
                                </form>
                                @endif
                                @if($payroll->isApproved())
                                <button type="button" class="btn btn-sm btn-outline-warning"
                                        style="padding:.2rem .5rem;" title="Mark Paid"
                                        data-bs-toggle="modal" data-bs-target="#markPaidModal"
                                        data-payroll="{{ $payroll->id }}">
                                    <i class="fa-solid fa-money-bill" style="font-size:.75rem;"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($payrolls->hasPages())
        <div class="card-footer" style="background:transparent;border-top:1px solid var(--border);">
            {{ $payrolls->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
        @endif
    @endif
</div>

{{-- Mark Paid Modal --}}
<div class="modal fade" id="markPaidModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background:var(--surface);border-color:var(--border);">
            <div class="modal-header" style="border-color:var(--border);">
                <h5 class="modal-title fw-600" style="font-weight:600;">Mark Salary as Paid</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="markPaidForm">
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
                        <input type="text" name="transaction_reference" class="form-control"
                               placeholder="UTR / Cheque No / Reference">
                    </div>
                </div>
                <div class="modal-footer" style="border-color:var(--border);">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fa-solid fa-check me-1"></i> Confirm Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.getElementById('markPaidModal').addEventListener('show.bs.modal', function(e) {
    const id = e.relatedTarget.dataset.payroll;
    document.getElementById('markPaidForm').action = '/admin/payroll/' + id + '/mark-paid';
});
</script>
@endpush

