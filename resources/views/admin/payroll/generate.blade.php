@extends('layouts.app')
@section('title', 'Generate Payroll')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-calculator me-2" style="color:#d97706;"></i>Generate Payroll</h1>
        <p class="mb-0" style="color:var(--muted);font-size:.875rem;">
            Preview and generate payroll for
            <strong>{{ \Carbon\Carbon::create()->month($month)->format('F') }} {{ $year }}</strong>
        </p>
    </div>
    <a href="{{ route('admin.payroll.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Back
    </a>
</div>

{{-- Month Selector --}}
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
                    @foreach(range(now()->year, now()->year - 2) as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-2">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-refresh me-1"></i> Recalculate
                </button>
            </div>
        </form>
    </div>
</div>

@if($calculations->isNotEmpty())
<form method="POST" action="{{ route('admin.payroll.store') }}">
    @csrf
    <input type="hidden" name="month" value="{{ $month }}">
    <input type="hidden" name="year" value="{{ $year }}">

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <span style="font-weight:600;">
                Payroll Preview — {{ \Carbon\Carbon::create()->month($month)->format('F') }} {{ $year }}
            </span>
            <div class="d-flex gap-2 align-items-center">
                <label class="form-check-label" style="font-size:.85rem;cursor:pointer;">
                    <input type="checkbox" id="selectAll" class="form-check-input me-1"> Select All
                </label>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th style="width:40px;"><input type="checkbox" id="selectAllTop" class="form-check-input"></th>
                        <th>Staff Member</th>
                        <th class="text-center">Present</th>
                        <th class="text-center">Absent</th>
                        <th class="text-center">Leave</th>
                        <th class="text-center">Deduction</th>
                        <th class="text-center">Gross</th>
                        <th class="text-center">Net Salary</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($calculations as $calc)
                    @php $staff = $calc['staff']; @endphp
                    <tr>
                        <td>
                            <input type="checkbox" name="staff_ids[]" value="{{ $staff->id }}"
                                   class="form-check-input staff-check"
                                   {{ $calc['existing'] ? 'checked' : 'checked' }}>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $staff->photo_url }}" class="rounded-circle"
                                     style="width:30px;height:30px;object-fit:cover;" alt="">
                                <div>
                                    <div style="font-weight:500;font-size:.875rem;">{{ $staff->name }}</div>
                                    <div style="font-size:.72rem;color:var(--muted);">{{ $staff->designation ?? $staff->staff_type }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center"><span class="badge" style="background:#dcfce7;color:#166534;">{{ $calc['present_days'] }}</span></td>
                        <td class="text-center"><span class="badge" style="background:#fee2e2;color:#991b1b;">{{ $calc['absent_days'] }}</span></td>
                        <td class="text-center"><span class="badge" style="background:#fef3c7;color:#92400e;">{{ $calc['leave_days'] }}</span></td>
                        <td class="text-center" style="color:#dc2626;font-size:.85rem;">
                            -₹{{ number_format($calc['deduction_amt']) }}
                            @if($calc['deduction_days'] > 0)
                                <div style="font-size:.7rem;color:var(--muted);">{{ $calc['deduction_days'] }} days</div>
                            @endif
                        </td>
                        <td class="text-center" style="font-size:.85rem;">₹{{ number_format($staff->monthly_salary) }}</td>
                        <td class="text-center" style="font-weight:700;color:#059669;">
                            ₹{{ number_format($calc['net_salary']) }}
                        </td>
                        <td class="text-center">
                            @if($calc['existing'])
                                @if($calc['existing']->isPaid())
                                    <span class="badge" style="background:#dcfce7;color:#166534;">Paid</span>
                                @elseif($calc['existing']->isApproved())
                                    <span class="badge" style="background:#dbeafe;color:#1e40af;">Approved</span>
                                @else
                                    <span class="badge bg-secondary">Draft</span>
                                @endif
                            @else
                                <span class="badge" style="background:#fef3c7;color:#92400e;">New</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background:var(--bg);">
                        <td colspan="6" style="font-weight:600;text-align:right;padding:.75rem 1rem;">Total Payable:</td>
                        <td class="text-center" style="font-weight:700;color:#059669;">
                            ₹{{ number_format($calculations->sum('net_salary')) }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="card-footer d-flex justify-content-between align-items-center"
             style="background:transparent;border-top:1px solid var(--border);">
            <small style="color:var(--muted);">
                <i class="fa-solid fa-info-circle me-1"></i>
                Salary = (Present + min(Leave, {{ 2 }} allowed holidays) × per-day rate. Absent days beyond allowed are deducted.
            </small>
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-save me-2"></i> Save Payroll
            </button>
        </div>
    </div>
</form>
@else
<div class="card">
    <div class="card-body">
        <div class="empty-state py-5">
            <i class="fa-solid fa-users" style="font-size:3rem;opacity:.2;"></i>
            <h5 class="mt-3" style="color:var(--muted);">No active staff members found</h5>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
const selectAll = document.getElementById('selectAll');
const selectAllTop = document.getElementById('selectAllTop');
const checks = document.querySelectorAll('.staff-check');

function toggleAll(checked) {
    checks.forEach(c => c.checked = checked);
    if (selectAll) selectAll.checked = checked;
    if (selectAllTop) selectAllTop.checked = checked;
}

if (selectAll) selectAll.addEventListener('change', e => toggleAll(e.target.checked));
if (selectAllTop) selectAllTop.addEventListener('change', e => toggleAll(e.target.checked));
</script>
@endpush

