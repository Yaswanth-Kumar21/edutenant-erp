<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payroll Slip</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; background: #fff; color: #1a1a2e; font-size: 12px; }
        .page { padding: 24px; }
        .header { background: #7c3aed; color: #fff; padding: 18px 24px; border-radius: 6px 6px 0 0; }
        .header-title { font-size: 17px; font-weight: bold; }
        .header-sub { font-size: 10px; opacity: .8; margin-top: 2px; }
        .body { padding: 18px 24px; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 6px 6px; }
        .section-title { font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: .06em; color: #7c3aed; margin: 14px 0 6px; border-bottom: 1px solid #ede9fe; padding-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f5f3ff; font-size: 10px; font-weight: bold; color: #6b7280; padding: 6px 10px; border-bottom: 2px solid #ede9fe; text-align: left; }
        td { padding: 6px 10px; border-bottom: 1px solid #f3f4f6; font-size: 11px; }
        .net-row td { font-weight: bold; background: #f5f3ff; font-size: 13px; color: #7c3aed; }
        .flex-between { display: flex; justify-content: space-between; }
        .info-grid { display: table; width: 100%; }
        .info-cell { display: table-cell; width: 50%; padding: 4px 0; font-size: 11px; }
        .info-label { color: #6b7280; font-size: 10px; }
        .footer-row { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 20px; padding-top: 12px; border-top: 1px dashed #d1d5db; }
        .sign-line { width: 120px; border-bottom: 1px solid #374151; margin-bottom: 4px; }
        .sign-label { font-size: 9px; color: #6b7280; }
        .badge-paid { background: #dcfce7; color: #166534; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; }
        .badge-pending { background: #fef3c7; color: #92400e; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; }
    </style>
</head>
<body>
<div class="page">
    <div class="header">
        <div class="flex-between">
            <div>
                <div class="header-title">{{ $tenant->name ?? 'EduTenant ERP' }}</div>
                <div class="header-sub">{{ $tenant->address ?? '' }}{{ $tenant->city ? ', ' . $tenant->city : '' }}</div>
            </div>
            <div style="text-align:right;">
                <div style="font-size:9px;opacity:.7;text-transform:uppercase;">Payroll Slip</div>
                <div style="font-size:14px;font-weight:bold;margin-top:2px;">
                    {{ date('F Y', mktime(0,0,0,$payroll->month,1,$payroll->year)) }}
                </div>
            </div>
        </div>
    </div>

    <div class="body">
        {{-- Employee Info --}}
        <div class="section-title">Employee Details</div>
        <table>
            <tr>
                <td style="width:25%;"><span class="info-label">Name</span><br><strong>{{ $payroll->staff?->user?->name }}</strong></td>
                <td style="width:25%;"><span class="info-label">Designation</span><br>{{ $payroll->staff?->designation ?? '—' }}</td>
                <td style="width:25%;"><span class="info-label">Department</span><br>{{ $payroll->staff?->department ?? '—' }}</td>
                <td style="width:25%;"><span class="info-label">Status</span><br>
                    <span class="{{ $payroll->status === 'paid' ? 'badge-paid' : 'badge-pending' }}">{{ ucfirst($payroll->status) }}</span>
                </td>
            </tr>
        </table>

        {{-- Earnings --}}
        <div class="section-title">Earnings</div>
        <table>
            <thead><tr><th>Component</th><th style="text-align:right;">Amount</th></tr></thead>
            <tbody>
                <tr><td>Basic Salary</td><td style="text-align:right;">₹{{ number_format($payroll->basic_salary, 2) }}</td></tr>
                @if(($payroll->hra ?? 0) > 0)
                <tr><td>House Rent Allowance (HRA)</td><td style="text-align:right;color:#059669;">₹{{ number_format($payroll->hra, 2) }}</td></tr>
                @endif
                @if(($payroll->da ?? 0) > 0)
                <tr><td>Dearness Allowance (DA)</td><td style="text-align:right;color:#059669;">₹{{ number_format($payroll->da, 2) }}</td></tr>
                @endif
                @if(($payroll->other_allowances ?? 0) > 0)
                <tr><td>Other Allowances</td><td style="text-align:right;color:#059669;">₹{{ number_format($payroll->other_allowances, 2) }}</td></tr>
                @endif
                <tr style="background:#f5f3ff;font-weight:bold;">
                    <td>Gross Salary</td>
                    <td style="text-align:right;">₹{{ number_format($payroll->gross_salary, 2) }}</td>
                </tr>
            </tbody>
        </table>

        {{-- Deductions --}}
        @if(($payroll->pf_deduction ?? 0) + ($payroll->tax_deduction ?? 0) + ($payroll->other_deductions ?? 0) > 0)
        <div class="section-title">Deductions</div>
        <table>
            <thead><tr><th>Component</th><th style="text-align:right;">Amount</th></tr></thead>
            <tbody>
                @if(($payroll->pf_deduction ?? 0) > 0)
                <tr><td>Provident Fund (PF)</td><td style="text-align:right;color:#dc2626;">₹{{ number_format($payroll->pf_deduction, 2) }}</td></tr>
                @endif
                @if(($payroll->tax_deduction ?? 0) > 0)
                <tr><td>Income Tax (TDS)</td><td style="text-align:right;color:#dc2626;">₹{{ number_format($payroll->tax_deduction, 2) }}</td></tr>
                @endif
                @if(($payroll->other_deductions ?? 0) > 0)
                <tr><td>Other Deductions</td><td style="text-align:right;color:#dc2626;">₹{{ number_format($payroll->other_deductions, 2) }}</td></tr>
                @endif
            </tbody>
        </table>
        @endif

        {{-- Net Salary --}}
        <table style="margin-top:8px;">
            <tbody>
                <tr class="net-row">
                    <td>NET SALARY PAYABLE</td>
                    <td style="text-align:right;font-size:15px;">₹{{ number_format($payroll->net_salary, 2) }}</td>
                </tr>
            </tbody>
        </table>

        @if($payroll->payment_date)
        <div style="font-size:10px;color:#6b7280;margin-top:8px;">
            Payment Date: <strong>{{ $payroll->payment_date->format('d M Y') }}</strong>
        </div>
        @endif

        <div class="footer-row">
            <div style="font-size:9px;color:#9ca3af;">
                Generated on {{ now()->format('d M Y, h:i A') }}<br>
                This is a computer-generated payslip. No signature required.
            </div>
            <div style="text-align:right;">
                <div class="sign-line"></div>
                <div class="sign-label">Authorised Signatory</div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
