@extends('layouts.app')
@section('title', $staff->name)

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-user-tie me-2" style="color:#059669;"></i>{{ $staff->name }}</h1>
        <p class="mb-0" style="color:var(--muted);font-size:.875rem;">{{ $staff->designation ?? $staff->staff_type }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.staff.edit', $staff) }}" class="btn btn-outline-primary btn-sm">
            <i class="fa-solid fa-pen me-1"></i> Edit
        </a>
        <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="row g-4">
    {{-- Profile Card --}}
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-body text-center p-4">
                <img src="{{ $staff->photo_url }}" class="rounded-circle mb-3"
                     style="width:100px;height:100px;object-fit:cover;border:3px solid var(--border);" alt="">
                <h5 class="mb-1 fw-700" style="font-weight:700;">{{ $staff->name }}</h5>
                <p class="mb-2" style="color:var(--muted);font-size:.875rem;">{{ $staff->designation ?? '—' }}</p>
                @if($staff->isTeaching())
                    <span class="badge" style="background:rgba(79,70,229,.1);color:#4f46e5;">
                        <i class="fa-solid fa-chalkboard-teacher me-1"></i>Teaching
                    </span>
                @else
                    <span class="badge" style="background:rgba(217,119,6,.1);color:#d97706;">
                        <i class="fa-solid fa-briefcase me-1"></i>Non-Teaching
                    </span>
                @endif
                @if($staff->status === 'active')
                    <span class="badge ms-1" style="background:#dcfce7;color:#166534;">Active</span>
                @else
                    <span class="badge ms-1" style="background:#fee2e2;color:#991b1b;">{{ ucfirst($staff->status) }}</span>
                @endif

                <hr style="border-color:var(--border);">

                <div class="text-start">
                    @if($staff->email)
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="fa-solid fa-envelope" style="color:var(--muted);width:16px;"></i>
                        <span style="font-size:.85rem;">{{ $staff->email }}</span>
                    </div>
                    @endif
                    @if($staff->phone)
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="fa-solid fa-phone" style="color:var(--muted);width:16px;"></i>
                        <span style="font-size:.85rem;">{{ $staff->phone }}</span>
                    </div>
                    @endif
                    @if($staff->department)
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="fa-solid fa-building" style="color:var(--muted);width:16px;"></i>
                        <span style="font-size:.85rem;">{{ $staff->department }}</span>
                    </div>
                    @endif
                    @if($staff->subject)
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="fa-solid fa-book" style="color:var(--muted);width:16px;"></i>
                        <span style="font-size:.85rem;">{{ $staff->subject }}</span>
                    </div>
                    @endif
                    @if($staff->date_of_joining)
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="fa-solid fa-calendar" style="color:var(--muted);width:16px;"></i>
                        <span style="font-size:.85rem;">Joined {{ $staff->date_of_joining->format('d M Y') }}</span>
                    </div>
                    @endif
                    @if($staff->staff_code)
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-id-badge" style="color:var(--muted);width:16px;"></i>
                        <span style="font-size:.85rem;">{{ $staff->staff_code }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Salary Card --}}
        <div class="card mb-4">
            <div class="card-header"><h6 class="mb-0 fw-600" style="font-weight:600;"><i class="fa-solid fa-indian-rupee-sign me-2" style="color:#d97706;"></i>Salary Info</h6></div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span style="font-size:.85rem;color:var(--muted);">Gross Salary</span>
                    <span style="font-weight:600;">?{{ number_format($staff->monthly_salary) }}</span>
                </div>
                @if($staff->pf_deduction > 0)
                <div class="d-flex justify-content-between mb-2">
                    <span style="font-size:.85rem;color:var(--muted);">PF Deduction</span>
                    <span style="color:#dc2626;">-?{{ number_format($staff->pf_deduction) }}</span>
                </div>
                @endif
                @if($staff->tax_deduction > 0)
                <div class="d-flex justify-content-between mb-2">
                    <span style="font-size:.85rem;color:var(--muted);">Tax Deduction</span>
                    <span style="color:#dc2626;">-?{{ number_format($staff->tax_deduction) }}</span>
                </div>
                @endif
                <hr style="border-color:var(--border);">
                <div class="d-flex justify-content-between">
                    <span style="font-weight:600;">Net Salary</span>
                    <span style="font-weight:700;color:#059669;font-size:1.1rem;">
                        ?{{ number_format($staff->monthly_salary - $staff->pf_deduction - $staff->tax_deduction) }}
                    </span>
                </div>
                <div class="mt-2" style="font-size:.75rem;color:var(--muted);">
                    {{ $staff->allowed_holidays_per_month ?? 2 }} holidays/month allowed
                </div>
            </div>
        </div>
    </div>

    {{-- Right Column --}}
    <div class="col-lg-8">

        {{-- This Month Salary Calculation --}}
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-600" style="font-weight:600;">
                    <i class="fa-solid fa-calculator me-2" style="color:#4f46e5;"></i>
                    {{ now()->format('F Y') }} Salary Calculation
                </h6>
                <a href="{{ route('admin.payroll.generate', ['month' => now()->month, 'year' => now()->year]) }}"
                   class="btn btn-sm btn-outline-primary">
                    <i class="fa-solid fa-file-invoice me-1"></i> Generate Payroll
                </a>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach([
                        ['label' => 'Present Days',   'value' => $salaryCalc['present_days'],   'color' => '#059669'],
                        ['label' => 'Absent Days',    'value' => $salaryCalc['absent_days'],    'color' => '#dc2626'],
                        ['label' => 'Leave Days',     'value' => $salaryCalc['leave_days'],     'color' => '#d97706'],
                        ['label' => 'Half Days',      'value' => $salaryCalc['half_days'],      'color' => '#0891b2'],
                        ['label' => 'Deduction Days', 'value' => $salaryCalc['deduction_days'], 'color' => '#dc2626'],
                        ['label' => 'Deduction Amt',  'value' => '?'.number_format($salaryCalc['deduction_amt']), 'color' => '#dc2626'],
                    ] as $item)
                    <div class="col-6 col-md-4">
                        <div class="card p-3 text-center" style="border-color:var(--border);">
                            <div style="font-size:1.4rem;font-weight:700;color:{{ $item['color'] }};">{{ $item['value'] }}</div>
                            <div style="font-size:.75rem;color:var(--muted);">{{ $item['label'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-3 p-3 rounded" style="background:rgba(5,150,105,.08);border:1px solid rgba(5,150,105,.2);">
                    <div class="d-flex justify-content-between align-items-center">
                        <span style="font-weight:600;">Estimated Net Salary</span>
                        <span style="font-size:1.4rem;font-weight:800;color:#059669;">
                            ?{{ number_format($salaryCalc['net_salary']) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Attendance --}}
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0 fw-600" style="font-weight:600;">
                    <i class="fa-solid fa-calendar-check me-2" style="color:#059669;"></i>
                    Recent Attendance (Last 30 Days)
                </h6>
            </div>
            <div class="card-body p-3">
                @if($recentAttendance->isEmpty())
                    <p style="color:var(--muted);font-size:.875rem;">No attendance records found.</p>
                @else
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($recentAttendance as $att)
                        @php
                            $colors = [
                                'present'  => '#059669',
                                'absent'   => '#dc2626',
                                'holiday'  => '#6b7280',
                                'half_day' => '#0891b2',
                                'leave'    => '#d97706',
                            ];
                            $c = $colors[$att->status] ?? '#6b7280';
                        @endphp
                        <div title="{{ $att->attendance_date->format('d M') }} — {{ ucfirst($att->status) }}"
                             style="width:28px;height:28px;border-radius:4px;background:{{ $c }};
                                    display:flex;align-items:center;justify-content:center;
                                    color:#fff;font-size:.65rem;font-weight:600;cursor:default;">
                            {{ $att->attendance_date->format('d') }}
                        </div>
                        @endforeach
                    </div>
                    <div class="d-flex gap-3 mt-2" style="font-size:.75rem;">
                        <span><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#059669;"></span> Present</span>
                        <span><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#dc2626;"></span> Absent</span>
                        <span><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#0891b2;"></span> Half Day</span>
                        <span><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#d97706;"></span> Leave</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Recent Payrolls --}}
        @if($recentPayrolls->isNotEmpty())
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-600" style="font-weight:600;">
                    <i class="fa-solid fa-file-invoice-dollar me-2" style="color:#d97706;"></i>
                    Recent Payrolls
                </h6>
                <a href="{{ route('admin.payroll.index', ['staff_id' => $staff->id]) }}"
                   style="font-size:.8rem;color:#4f46e5;text-decoration:none;">View all</a>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Period</th>
                            <th class="text-center">Gross</th>
                            <th class="text-center">Deductions</th>
                            <th class="text-center">Net</th>
                            <th class="text-center">Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentPayrolls as $payroll)
                        <tr>
                            <td style="font-weight:500;font-size:.875rem;">{{ $payroll->pay_period }}</td>
                            <td class="text-center" style="font-size:.85rem;">?{{ number_format($payroll->gross_salary) }}</td>
                            <td class="text-center" style="font-size:.85rem;color:#dc2626;">-?{{ number_format($payroll->total_deductions) }}</td>
                            <td class="text-center" style="font-weight:600;color:#059669;">?{{ number_format($payroll->net_salary) }}</td>
                            <td class="text-center">
                                @if($payroll->isPaid())
                                    <span class="badge" style="background:#dcfce7;color:#166534;">Paid</span>
                                @elseif($payroll->isApproved())
                                    <span class="badge" style="background:#dbeafe;color:#1e40af;">Approved</span>
                                @else
                                    <span class="badge bg-secondary">Draft</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.payroll.slip', $payroll) }}"
                                   class="btn btn-sm btn-outline-secondary" style="padding:.2rem .5rem;">
                                    <i class="fa-solid fa-file-pdf" style="font-size:.75rem;"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>
</div>

@endsection

