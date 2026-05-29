@extends('layouts.app')

@section('title', 'Fee Assignments')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.fees.dashboard') }}" style="color:#4f46e5;text-decoration:none;">Fees</a></li>
    <li class="breadcrumb-item active">Assignments</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-clipboard-list me-2" style="color:#4f46e5;"></i>Fee Assignments</h1>
        <p class="mb-0" style="color:var(--muted);font-size:0.875rem;">Assign fee structures to students in bulk or individually</p>
    </div>
</div>

{{-- Bulk Assignment Card --}}
<div class="card mb-4">
    <div class="card-header">
        <i class="fa-solid fa-users me-2" style="color:#059669;"></i>
        Bulk Fee Assignment
        <span class="badge ms-2" style="background:rgba(5,150,105,0.1);color:#059669;">Assign to entire branch</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.fees.assignments.bulk') }}">
            @csrf
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Branch <span class="text-danger">*</span></label>
                    <select name="branch_id" class="form-select" required>
                        <option value="">— Select Branch —</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->course?->name }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fee Type <span class="text-danger">*</span></label>
                    <select name="fee_type_id" class="form-select" required>
                        <option value="">— Select Fee Type —</option>
                        @foreach($feeTypes as $ft)
                            <option value="{{ $ft->id }}" data-amount="{{ $ft->amount }}">
                                {{ $ft->name }} @if($ft->amount > 0) (₹{{ number_format($ft->amount) }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                    <select name="academic_year_id" class="form-select" required>
                        @foreach($academicYears as $yr)
                            <option value="{{ $yr->id }}" {{ $yr->is_current ? 'selected' : '' }}>{{ $yr->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Amount Due (₹) <span class="text-danger">*</span></label>
                    <input type="number" name="amount_due" id="bulk-amount" class="form-control" value="0" min="0" step="0.01" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fa-solid fa-bolt me-1"></i> Bulk Assign
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

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
            <div class="col-md-4">
                <label class="form-label mb-1" style="font-size:0.8rem;">Academic Year</label>
                <select name="academic_year_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Years</option>
                    @foreach($academicYears as $yr)
                        <option value="{{ $yr->id }}" {{ request('academic_year_id') == $yr->id ? 'selected' : '' }}>{{ $yr->name }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

{{-- Students Table --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span style="font-weight:600;">Students <span class="badge ms-2" style="background:rgba(79,70,229,0.1);color:#4f46e5;">{{ $students->total() }}</span></span>
    </div>
    @if($students->isEmpty())
        <div class="empty-state">
            <i class="fa-solid fa-users"></i>
            <h5 style="color:var(--muted);">No students found</h5>
        </div>
    @else
        <div class="table-responsive">
            <table class="table mb-0" style="font-size:0.875rem;">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Branch</th>
                        <th>Semester</th>
                        <th>Fees Assigned</th>
                        <th>Total Paid</th>
                        <th>Pending</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    @php
                        $totalDue    = $student->feePayments->sum('amount_due');
                        $totalPaid   = $student->feePayments->where('status','paid')->sum('amount_paid');
                        $totalPending = $student->feePayments->whereIn('status',['pending','partial'])->sum('amount_due');
                    @endphp
                    <tr>
                        <td>
                            <div style="font-weight:500;">{{ $student->full_name }}</div>
                            <div style="font-size:0.72rem;color:var(--muted);">{{ $student->admission_number }}</div>
                        </td>
                        <td style="font-size:0.85rem;">{{ $student->branch?->name ?? '—' }}</td>
                        <td style="font-size:0.85rem;">Sem {{ $student->current_semester }}</td>
                        <td style="font-size:0.85rem;">{{ $student->feePayments->count() }} fees</td>
                        <td style="color:#059669;font-weight:600;">₹{{ number_format($totalPaid) }}</td>
                        <td style="color:{{ $totalPending > 0 ? '#dc2626' : '#059669' }};font-weight:600;">
                            ₹{{ number_format($totalPending) }}
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.fees.student.profile', $student) }}"
                               class="btn btn-sm btn-outline-primary" style="font-size:0.75rem;padding:0.25rem 0.6rem;">
                                Fee Profile
                            </a>
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
@endsection

@push('scripts')
<script>
// Auto-fill amount from fee type selection
document.querySelector('[name="fee_type_id"]')?.addEventListener('change', function () {
    const opt = this.options[this.selectedIndex];
    const amount = parseFloat(opt.dataset.amount) || 0;
    if (amount > 0) document.getElementById('bulk-amount').value = amount;
});
</script>
@endpush

