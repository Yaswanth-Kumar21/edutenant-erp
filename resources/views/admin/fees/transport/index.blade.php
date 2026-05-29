@extends('layouts.app')

@section('title', 'Transport Fees')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.fees.dashboard') }}" style="color:#4f46e5;text-decoration:none;">Fees</a></li>
    <li class="breadcrumb-item active">Transport Fees</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-bus me-2" style="color:#4f46e5;"></i>Transport / Vehicle Fees</h1>
        <p class="mb-0" style="color:var(--muted);font-size:0.875rem;">Monthly transport fee collection for opted students</p>
    </div>
    <a href="{{ route('admin.fees.transport.summary') }}" class="btn btn-outline-secondary">
        <i class="fa-solid fa-chart-bar me-2"></i> Monthly Summary
    </a>
</div>

{{-- Vehicle Fee Info --}}
@if($vehicleFeeType)
<div class="alert d-flex align-items-center gap-3 mb-4"
     style="background:rgba(79,70,229,0.06);border:1px solid rgba(79,70,229,0.2);border-radius:0.75rem;">
    <i class="fa-solid fa-bus" style="color:#4f46e5;font-size:1.25rem;"></i>
    <div>
        <strong>Vehicle Fee:</strong> ₹{{ number_format($vehicleFeeType->amount) }} per month
        &bull; Fee Code: {{ $vehicleFeeType->code }}
    </div>
</div>
@endif

{{-- Filters --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label mb-1" style="font-size:0.8rem;">Branch</label>
                <select name="branch_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Branches</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span style="font-weight:600;">
            Vehicle Students
            <span class="badge ms-2" style="background:rgba(79,70,229,0.1);color:#4f46e5;">{{ $students->total() }}</span>
        </span>
    </div>
    @if($students->isEmpty())
        <div class="empty-state">
            <i class="fa-solid fa-bus"></i>
            <h5 style="color:var(--muted);">No students opted for vehicle</h5>
        </div>
    @else
        <div class="table-responsive">
            <table class="table mb-0" style="font-size:0.875rem;">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Branch</th>
                        <th>Vehicle Since</th>
                        <th>Last Payment</th>
                        <th class="text-end">Collect</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    @php $lastPayment = $student->feePayments->first(); @endphp
                    <tr>
                        <td>
                            <div style="font-weight:500;">{{ $student->full_name }}</div>
                            <div style="font-size:0.72rem;color:var(--muted);">{{ $student->admission_number }}</div>
                        </td>
                        <td style="font-size:0.85rem;">{{ $student->branch?->name ?? '—' }}</td>
                        <td style="font-size:0.85rem;color:var(--muted);">
                            {{ $student->vehicle_start_date?->format('d M Y') ?? '—' }}
                        </td>
                        <td style="font-size:0.85rem;">
                            @if($lastPayment)
                                <span style="color:#059669;font-weight:600;">₹{{ number_format($lastPayment->amount_paid) }}</span>
                                <div style="font-size:0.72rem;color:var(--muted);">{{ $lastPayment->payment_date?->format('d M Y') }}</div>
                            @else
                                <span style="color:var(--muted);">No payments</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-primary"
                                    style="font-size:0.75rem;"
                                    data-bs-toggle="modal"
                                    data-bs-target="#collectModal"
                                    data-student-id="{{ $student->id }}"
                                    data-student-name="{{ $student->full_name }}"
                                    data-amount="{{ $vehicleFeeType?->amount ?? 0 }}">
                                <i class="fa-solid fa-plus me-1"></i> Collect
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($students->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2" style="background:transparent;border-top:1px solid var(--border);">
                <small style="color:var(--muted);">Page {{ $students->currentPage() }} of {{ $students->lastPage() }}</small>
                {{ $students->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        @endif
    @endif
</div>

{{-- Collect Modal --}}
<div class="modal fade" id="collectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:0.75rem;background:var(--surface);">
            <div class="modal-header" style="border-color:var(--border);">
                <h5 class="modal-title" style="font-size:1rem;font-weight:600;">
                    <i class="fa-solid fa-bus me-2" style="color:#4f46e5;"></i>
                    Collect Transport Fee
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="collectForm" action="">
                @csrf
                <div class="modal-body">
                    <p id="collect-student-name" style="font-weight:600;color:#4f46e5;margin-bottom:1rem;"></p>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label">Month <span class="text-danger">*</span></label>
                            <select name="month" class="form-select" required>
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Year <span class="text-danger">*</span></label>
                            <input type="number" name="year" class="form-control" value="{{ now()->year }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Amount Paid (₹) <span class="text-danger">*</span></label>
                            <input type="number" name="amount_paid" id="modal-amount" class="form-control" min="0" step="0.01" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Payment Mode</label>
                            <select name="payment_mode" class="form-select">
                                @foreach(\App\Models\FeePayment::PAYMENT_MODES as $mode => $label)
                                    <option value="{{ $mode }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                            <select name="academic_year_id" class="form-select" required>
                                @foreach(\App\Models\AcademicYear::where('tenant_id', auth()->user()->tenant_id)->orderByDesc('is_current')->get() as $yr)
                                    <option value="{{ $yr->id }}" {{ $yr->is_current ? 'selected' : '' }}>{{ $yr->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-color:var(--border);">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-check me-2"></i> Collect
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('collectModal').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    const studentId   = btn.dataset.studentId;
    const studentName = btn.dataset.studentName;
    const amount      = btn.dataset.amount;
    document.getElementById('collect-student-name').textContent = studentName;
    document.getElementById('modal-amount').value = amount;
    document.getElementById('collectForm').action = '/admin/fees/transport/collect/' + studentId;
});
</script>
@endpush

