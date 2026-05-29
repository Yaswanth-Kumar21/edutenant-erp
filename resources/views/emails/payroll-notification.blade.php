<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Salary Slip</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f3f4f6; margin: 0; padding: 20px; color: #1f2937; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,.07); }
        .header { background: linear-gradient(135deg, #7c3aed, #a855f7); padding: 32px 40px; color: #fff; }
        .header h1 { margin: 0 0 4px; font-size: 22px; font-weight: 700; }
        .body { padding: 32px 40px; }
        .amount-box { background: #f5f3ff; border: 2px solid #7c3aed; border-radius: 8px; padding: 16px 20px; text-align: center; margin: 20px 0; }
        .amount { font-size: 36px; font-weight: 800; color: #7c3aed; }
        .info-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .info-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #e5e7eb; font-size: 14px; }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #6b7280; }
        .info-value { font-weight: 600; }
        .footer { background: #f9fafb; padding: 20px 40px; text-align: center; font-size: 12px; color: #9ca3af; border-top: 1px solid #e5e7eb; }
        p { line-height: 1.6; color: #374151; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>{{ $tenant->name }}</h1>
        <p>Salary Slip — {{ date('F Y', mktime(0, 0, 0, $payroll->month, 1, $payroll->year)) }}</p>
    </div>
    <div class="body">
        <p>Dear <strong>{{ $payroll->staff?->user?->name }}</strong>,</p>
        <p>Your salary for <strong>{{ date('F Y', mktime(0, 0, 0, $payroll->month, 1, $payroll->year)) }}</strong> has been processed.</p>

        <div class="amount-box">
            <div class="amount">₹{{ number_format($payroll->net_salary, 2) }}</div>
            <div style="font-size:13px;color:#6b7280;margin-top:4px;">Net Salary Credited</div>
        </div>

        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Basic Salary</span>
                <span class="info-value">₹{{ number_format($payroll->basic_salary, 2) }}</span>
            </div>
            @if(($payroll->hra ?? 0) > 0)
            <div class="info-row">
                <span class="info-label">HRA</span>
                <span class="info-value" style="color:#059669;">+ ₹{{ number_format($payroll->hra, 2) }}</span>
            </div>
            @endif
            @if(($payroll->da ?? 0) > 0)
            <div class="info-row">
                <span class="info-label">DA</span>
                <span class="info-value" style="color:#059669;">+ ₹{{ number_format($payroll->da, 2) }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Gross Salary</span>
                <span class="info-value">₹{{ number_format($payroll->gross_salary, 2) }}</span>
            </div>
            @if(($payroll->pf_deduction ?? 0) > 0)
            <div class="info-row">
                <span class="info-label">PF Deduction</span>
                <span class="info-value" style="color:#dc2626;">- ₹{{ number_format($payroll->pf_deduction, 2) }}</span>
            </div>
            @endif
            @if(($payroll->tax_deduction ?? 0) > 0)
            <div class="info-row">
                <span class="info-label">Tax Deduction</span>
                <span class="info-value" style="color:#dc2626;">- ₹{{ number_format($payroll->tax_deduction, 2) }}</span>
            </div>
            @endif
            <div class="info-row" style="border-top:2px solid #7c3aed;padding-top:10px;">
                <span class="info-label" style="font-weight:700;">Net Salary</span>
                <span class="info-value" style="color:#7c3aed;font-size:16px;">₹{{ number_format($payroll->net_salary, 2) }}</span>
            </div>
        </div>

        <p>For any discrepancies, please contact the HR/Accounts department.</p>
    </div>
    <div class="footer">
        <p>This is an automated email from {{ $tenant->name }} ERP System.</p>
        <p>© {{ date('Y') }} {{ $tenant->name }}. All rights reserved.</p>
    </div>
</div>
</body>
</html>
