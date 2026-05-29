<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Receipt</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f3f4f6; margin: 0; padding: 20px; color: #1f2937; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,.07); }
        .header { background: linear-gradient(135deg, #059669, #10b981); padding: 32px 40px; color: #fff; }
        .header h1 { margin: 0 0 4px; font-size: 22px; font-weight: 700; }
        .header p { margin: 0; opacity: .8; font-size: 14px; }
        .body { padding: 32px 40px; }
        .receipt-no { font-family: monospace; font-size: 20px; font-weight: 800; color: #059669; margin-bottom: 20px; }
        .info-box { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .info-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #d1fae5; font-size: 14px; }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #6b7280; }
        .info-value { font-weight: 600; color: #1e1b4b; }
        .amount-box { background: #f8f7ff; border: 2px solid #4f46e5; border-radius: 8px; padding: 16px 20px; text-align: center; margin: 20px 0; }
        .amount { font-size: 32px; font-weight: 800; color: #059669; }
        .amount-label { font-size: 13px; color: #6b7280; margin-top: 4px; }
        .badge-paid { display: inline-block; background: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 20px; font-size: 13px; font-weight: 600; }
        .footer { background: #f9fafb; padding: 20px 40px; text-align: center; font-size: 12px; color: #9ca3af; border-top: 1px solid #e5e7eb; }
        p { line-height: 1.6; color: #374151; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>{{ $tenant->name }}</h1>
        <p>Fee Payment Receipt</p>
    </div>
    <div class="body">
        <div class="receipt-no">Receipt #{{ $payment->receipt_number }}</div>
        <p>Dear <strong>{{ $payment->student?->full_name }}</strong>,</p>
        <p>Your fee payment has been successfully recorded. Here are the details:</p>

        <div class="amount-box">
            <div class="amount">₹{{ number_format($payment->amount_paid, 2) }}</div>
            <div class="amount-label">Amount Paid on {{ $payment->payment_date?->format('d M Y') }}</div>
            <div style="margin-top:8px;"><span class="badge-paid">{{ ucfirst($payment->status) }}</span></div>
        </div>

        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Student</span>
                <span class="info-value">{{ $payment->student?->full_name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Admission No</span>
                <span class="info-value" style="font-family:monospace;color:#4f46e5;">{{ $payment->student?->admission_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Fee Type</span>
                <span class="info-value">{{ $payment->feeType?->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Academic Year</span>
                <span class="info-value">{{ $payment->academicYear?->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Payment Mode</span>
                <span class="info-value">{{ \App\Models\FeePayment::PAYMENT_MODES[$payment->payment_mode] ?? ucfirst($payment->payment_mode) }}</span>
            </div>
            @if($payment->transaction_reference)
            <div class="info-row">
                <span class="info-label">Transaction Ref</span>
                <span class="info-value">{{ $payment->transaction_reference }}</span>
            </div>
            @endif
            @if($payment->balance > 0)
            <div class="info-row">
                <span class="info-label">Balance Due</span>
                <span class="info-value" style="color:#dc2626;">₹{{ number_format($payment->balance, 2) }}</span>
            </div>
            @endif
        </div>

        <p>Please keep this email as your payment confirmation. For any queries, contact the college accounts office.</p>
    </div>
    <div class="footer">
        <p>This is an automated email from {{ $tenant->name }} ERP System.</p>
        <p>© {{ date('Y') }} {{ $tenant->name }}. All rights reserved.</p>
    </div>
</div>
</body>
</html>
