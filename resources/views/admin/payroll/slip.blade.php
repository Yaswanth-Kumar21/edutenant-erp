<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Slip — {{ $payroll->pay_period }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f8f9fa; }
        .slip-wrapper { max-width: 800px; margin: 2rem auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,.1); }
        .slip-header { background: linear-gradient(135deg, #4f46e5, #7c3aed); color: #fff; padding: 2rem; }
        .slip-body { padding: 2rem; }
        .slip-table th { background: #f8f7ff; font-size: .8rem; text-transform: uppercase; letter-spacing: .05em; color: #6b7280; }
        .slip-table td { font-size: .9rem; padding: .6rem 1rem; }
        .net-box { background: linear-gradient(135deg, #059669, #10b981); color: #fff; border-radius: 8px; padding: 1.5rem; }
        @media print {
            body { background: #fff; }
            .slip-wrapper { box-shadow: none; margin: 0; border-radius: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

<div class="no-print text-center py-3">
    <button onclick="window.print()" class="btn btn-primary btn-sm me-2">
        <i class="fa-solid fa-print me-1"></i> Print Slip
    </button>
    <a href="{{ route('admin.payroll.show', $payroll) }}" class="btn btn-outline-secondary btn-sm">
        Back
    </a>
</div>

<div class="slip-wrapper">
    {{-- Header --}}
    <div class="slip-header">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h4 class="mb-1 fw-bold">{{ $tenant->name ?? 'EduTenant ERP' }}</h4>
                <p class="mb-0" style="opacity:.85;font-size:.875rem;">{{ $tenant->address ?? '' }}</p>
                @if($tenant->phone)
                    <p class="mb-0" style="opacity:.75;font-size:.8rem;">{{ $tenant->phone }}</p>
                @endif
            </div>
            <div class="text-end">
                <div style="font-size:1.2rem;font-weight:700;">SALARY SLIP</div>
                <div style="opacity:.85;font-size:.875rem;">{{ $payroll->pay_period }}</div>
                @if($payroll->payroll_number)
                    <div style="opacity:.7;font-size:.78rem;">{{ $payroll->payroll_number }}</div>
                @endif
            </div>
        </div>
    </div>

    <div class="slip-body">
        {{-- Employee Info --}}
        <div class="row mb-4">
            <div class="col-md-6">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <td style="color:#6b7280;font-size:.85rem;width:140px;">Employee Name</td>
                        <td style="font-weight:600;">{{ $payroll->staff->name }}</td>
                    </tr>
                    <tr>
                        <td style="color:#6b7280;font-size:.85rem;">Staff Code</td>
                        <td>{{ $payroll->staff->staff_code ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td style="color:#6b7280;font-size:.85rem;">Designation</td>
                        <td>{{ $payroll->staff->designation ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td style="color:#6b7280;font-size:.85rem;">Department</td>
                        <td>{{ $payroll->staff->department ?? '—' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <td style="color:#6b7280;font-size:.85rem;width:140px;">Pay Period</td>
                        <td style="font-weight:600;">{{ $payroll->pay_period }}</td>
                    </tr>
                    <tr>
                        <td style="color:#6b7280;font-size:.85rem;">Working Days</td>
                        <td>{{ $payroll->working_days }}</td>
                    </tr>
                    <tr>
                        <td style="color:#6b7280;font-size:.85rem;">Present Days</td>
                        <td>{{ $payroll->present_days }}</td>
                    </tr>
                    <tr>
                        <td style="color:#6b7280;font-size:.85rem;">Payment Mode</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $payroll->payment_mode)) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Attendance Summary --}}
        <div class="row g-2 mb-4">
            @foreach([
                ['label' => 'Present', 'value' => $payroll->present_days, 'bg' => '#dcfce7', 'color' => '#166534'],
                ['label' => 'Absent',  'value' => $payroll->absent_days,  'bg' => '#fee2e2', 'color' => '#991b1b'],
                ['label' => 'Leave',   'value' => $payroll->leave_days,   'bg' => '#fef3c7', 'color' => '#92400e'],
                ['label' => 'Half Day','value' => $payroll->half_days,    'bg' => '#e0f2fe', 'color' => '#0369a1'],
                ['label' => 'Holiday', 'value' => $payroll->holiday_days, 'bg' => '#f3f4f6', 'color' => '#374151'],
                ['label' => 'Allowed', 'value' => $payroll->allowed_holidays, 'bg' => '#f0fdf4', 'color' => '#166534'],
            ] as $item)
            <div class="col-2 text-center">
                <div style="background:{{ $item['bg'] }};color:{{ $item['color'] }};border-radius:8px;padding:.5rem;">
                    <div style="font-size:1.2rem;font-weight:700;">{{ $item['value'] }}</div>
                    <div style="font-size:.68rem;">{{ $item['label'] }}</div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Earnings & Deductions --}}
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <h6 style="font-size:.8rem;font-weight:700;text-transform:uppercase;color:#6b7280;margin-bottom:.5rem;">Earnings</h6>
                <table class="table slip-table table-bordered mb-0">
                    <thead><tr><th>Component</th><th class="text-end">Amount</th></tr></thead>
                    <tbody>
                        @if($payroll->basic_salary > 0)
                        <tr><td>Basic Salary</td><td class="text-end">₹{{ number_format($payroll->basic_salary) }}</td></tr>
                        @endif
                        @if($payroll->hra > 0)
                        <tr><td>HRA</td><td class="text-end">₹{{ number_format($payroll->hra) }}</td></tr>
                        @endif
                        @if($payroll->da > 0)
                        <tr><td>DA</td><td class="text-end">₹{{ number_format($payroll->da) }}</td></tr>
                        @endif
                        @if($payroll->other_allowances > 0)
                        <tr><td>Other Allowances</td><td class="text-end">₹{{ number_format($payroll->other_allowances) }}</td></tr>
                        @endif
                        <tr style="background:#f8f7ff;">
                            <td style="font-weight:600;">Gross Salary</td>
                            <td class="text-end" style="font-weight:700;">₹{{ number_format($payroll->gross_salary) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-6">
                <h6 style="font-size:.8rem;font-weight:700;text-transform:uppercase;color:#6b7280;margin-bottom:.5rem;">Deductions</h6>
                <table class="table slip-table table-bordered mb-0">
                    <thead><tr><th>Component</th><th class="text-end">Amount</th></tr></thead>
                    <tbody>
                        @if($payroll->absent_deduction > 0)
                        <tr><td>Absent Deduction ({{ $payroll->absent_days }} days)</td><td class="text-end" style="color:#dc2626;">₹{{ number_format($payroll->absent_deduction) }}</td></tr>
                        @endif
                        @if($payroll->pf_deduction > 0)
                        <tr><td>PF Deduction</td><td class="text-end" style="color:#dc2626;">₹{{ number_format($payroll->pf_deduction) }}</td></tr>
                        @endif
                        @if($payroll->tax_deduction > 0)
                        <tr><td>Tax Deduction</td><td class="text-end" style="color:#dc2626;">₹{{ number_format($payroll->tax_deduction) }}</td></tr>
                        @endif
                        @if($payroll->other_deductions > 0)
                        <tr><td>Other Deductions</td><td class="text-end" style="color:#dc2626;">₹{{ number_format($payroll->other_deductions) }}</td></tr>
                        @endif
                        <tr style="background:#fff5f5;">
                            <td style="font-weight:600;">Total Deductions</td>
                            <td class="text-end" style="font-weight:700;color:#dc2626;">₹{{ number_format($payroll->total_deductions) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Net Salary --}}
        <div class="net-box text-center">
            <div style="font-size:.9rem;opacity:.9;margin-bottom:.25rem;">Net Salary Payable</div>
            <div style="font-size:2.5rem;font-weight:800;">₹{{ number_format($payroll->net_salary) }}</div>
            <div style="font-size:.8rem;opacity:.8;margin-top:.25rem;">
                Per Day Rate: ₹{{ number_format($payroll->per_day_salary) }}
            </div>
        </div>

        {{-- Footer --}}
        <div class="row mt-4 pt-3" style="border-top:1px solid #e5e7eb;">
            <div class="col-6">
                <div style="font-size:.78rem;color:#6b7280;">Generated by: {{ $payroll->generatedBy->name ?? 'System' }}</div>
                <div style="font-size:.78rem;color:#6b7280;">Generated on: {{ $payroll->created_at->format('d M Y') }}</div>
            </div>
            <div class="col-6 text-end">
                <div style="font-size:.78rem;color:#6b7280;">Authorised Signatory</div>
                <div style="margin-top:2rem;border-top:1px solid #e5e7eb;padding-top:.25rem;font-size:.78rem;color:#6b7280;">
                    {{ $tenant->principal_name ?? 'Principal' }}
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</body>
</html>
