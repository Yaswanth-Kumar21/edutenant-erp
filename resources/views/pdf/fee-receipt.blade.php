<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fee Receipt — {{ $payment->receipt_number }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; background: #fff; color: #1a1a2e; font-size: 12px; }
        .page { padding: 20px; max-width: 100%; }
        .header { background: #4f46e5; color: #fff; padding: 16px 20px; border-radius: 6px 6px 0 0; }
        .header-title { font-size: 16px; font-weight: bold; }
        .header-sub { font-size: 10px; opacity: .8; margin-top: 2px; }
        .receipt-no { font-size: 13px; font-weight: bold; font-family: monospace; }
        .body { padding: 16px 20px; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 6px 6px; }
        .student-row { display: flex; align-items: center; margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px dashed #d1d5db; }
        .student-name { font-size: 14px; font-weight: bold; }
        .student-sub { font-size: 10px; color: #6b7280; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; margin: 12px 0; }
        th { background: #f8f7ff; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: .04em; color: #6b7280; padding: 6px 8px; border-bottom: 2px solid #e5e7eb; text-align: left; }
        td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; font-size: 11px; }
        .amount-row td { font-weight: bold; background: #f8f7ff; font-size: 12px; }
        .paid-amount { color: #059669; font-size: 14px; font-weight: bold; }
        .status-paid { background: #dcfce7; color: #166534; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; }
        .status-partial { background: #fef3c7; color: #92400e; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; }
        .footer-row { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 16px; padding-top: 12px; border-top: 1px dashed #d1d5db; }
        .footer-note { font-size: 9px; color: #9ca3af; }
        .sign-box { text-align: right; }
        .sign-line { width: 100px; border-bottom: 1px solid #374151; margin-bottom: 4px; }
        .sign-label { font-size: 9px; color: #6b7280; }
        .flex-between { display: flex; justify-content: space-between; }
    </style>
</head>
<body>
<div class="page">
    <div class="header">
        <div class="flex-between">
            <div>
                <div class="header-title">{{ $tenant->name ?? 'EduTenant ERP' }}</div>
                <div class="header-sub">{{ $tenant->address ?? '' }}{{ $tenant->city ? ', ' . $tenant->city : '' }}</div>
                @if($tenant->phone)<div class="header-sub">Ph: {{ $tenant->phone }}</div>@endif
            </div>
            <div style="text-align:right;">
                <div style="font-size:9px;opacity:.7;text-transform:uppercase;letter-spacing:.05em;">Fee Receipt</div>
                <div class="receipt-no">{{ $payment->receipt_number }}</div>
                <div style="font-size:10px;opacity:.7;margin-top:2px;">{{ $payment->payment_date?->format('d M Y') }}</div>
            </div>
        </div>
    </div>

    <div class="body">
        <div class="student-row">
            <div>
                <div class="student-name">{{ $payment->student?->full_name }}</div>
                <div class="student-sub">
                    {{ $payment->student?->admission_number }}
                    &bull; {{ $payment->student?->branch?->name }}
                    &bull; {{ $payment->student?->category }}
                </div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Fee Type</th>
                    <th>Academic Year</th>
                    <th>Semester</th>
                    <th>Payment Mode</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $payment->feeType?->name }}</td>
                    <td>{{ $payment->academicYear?->name }}</td>
                    <td>{{ $payment->semester ? 'Sem ' . $payment->semester : '—' }}</td>
                    <td>{{ \App\Models\FeePayment::PAYMENT_MODES[$payment->payment_mode] ?? ucfirst($payment->payment_mode) }}</td>
                    <td><span class="status-{{ $payment->status }}">{{ ucfirst($payment->status) }}</span></td>
                </tr>
            </tbody>
        </table>

        <table>
            <thead>
                <tr>
                    <th>Amount Due</th>
                    <th>Discount</th>
                    <th>Fine</th>
                    <th>Amount Paid</th>
                    <th>Balance</th>
                </tr>
            </thead>
            <tbody>
                <tr class="amount-row">
                    <td>₹{{ number_format($payment->amount_due, 2) }}</td>
                    <td style="color:#059669;">{{ $payment->discount > 0 ? '- ₹' . number_format($payment->discount, 2) : '—' }}</td>
                    <td style="color:#dc2626;">{{ $payment->fine > 0 ? '+ ₹' . number_format($payment->fine, 2) : '—' }}</td>
                    <td class="paid-amount">₹{{ number_format($payment->amount_paid, 2) }}</td>
                    <td style="color:{{ $payment->balance > 0 ? '#dc2626' : '#059669' }};">
                        {{ $payment->balance > 0 ? '₹' . number_format($payment->balance, 2) : 'Nil' }}
                    </td>
                </tr>
            </tbody>
        </table>

        @if($payment->transaction_reference)
        <div style="font-size:10px;color:#6b7280;margin-bottom:8px;">
            Transaction Ref: <strong>{{ $payment->transaction_reference }}</strong>
        </div>
        @endif

        <div class="footer-row">
            <div class="footer-note">
                Collected by {{ $payment->collectedBy?->name ?? 'System' }}<br>
                {{ $payment->created_at?->format('d M Y, h:i A') }}<br>
                This is a computer-generated receipt. No signature required.
            </div>
            <div class="sign-box">
                <div class="sign-line"></div>
                <div class="sign-label">Authorised Signatory</div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
