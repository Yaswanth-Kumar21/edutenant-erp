<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Receipt — {{ $payment->receipt_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #fff; color: #1a1a2e; margin: 0; padding: 0; }
        .receipt-wrapper { max-width: 680px; margin: 0 auto; padding: 1.5rem; }
        .receipt-header { background: linear-gradient(135deg, #4f46e5, #7c3aed); color: #fff; padding: 1.5rem 2rem; border-radius: 0.75rem 0.75rem 0 0; }
        .receipt-body { padding: 1.5rem 2rem; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 0.75rem 0.75rem; }
        .info-box { background: #f8f7ff; border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 1rem; }
        .info-label { font-size: 0.72rem; color: #6b7280; font-weight: 500; }
        .info-value { font-size: 0.875rem; font-weight: 600; color: #1e1b4b; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8f7ff; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #6b7280; padding: 0.5rem 0.75rem; border-bottom: 2px solid #e5e7eb; }
        td { padding: 0.5rem 0.75rem; border-bottom: 1px solid #e5e7eb; font-size: 0.875rem; }
        tfoot td { font-weight: 700; background: #f8f7ff; }
        .divider { border: none; border-top: 1px dashed #d1d5db; margin: 1rem 0; }
        .status-paid    { background: #dcfce7; color: #166534; padding: 0.3em 0.8em; border-radius: 0.375rem; font-size: 0.8rem; font-weight: 600; }
        .status-partial { background: #fef3c7; color: #92400e; padding: 0.3em 0.8em; border-radius: 0.375rem; font-size: 0.8rem; font-weight: 600; }
        .status-pending { background: #fee2e2; color: #991b1b; padding: 0.3em 0.8em; border-radius: 0.375rem; font-size: 0.8rem; font-weight: 600; }
        .print-btn { position: fixed; bottom: 2rem; right: 2rem; background: linear-gradient(135deg, #4f46e5, #7c3aed); color: #fff; border: none; border-radius: 50px; padding: 0.75rem 1.5rem; font-size: 0.9rem; font-weight: 600; cursor: pointer; box-shadow: 0 4px 15px rgba(79,70,229,0.4); }
        @media print { .print-btn { display: none !important; } .receipt-wrapper { padding: 0; max-width: 100%; } }
    </style>
</head>
<body>
<div class="receipt-wrapper">
    <div class="receipt-header">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <div style="font-size:1.15rem;font-weight:800;margin-bottom:0.2rem;">{{ $tenant->name ?? 'EduTenant ERP' }}</div>
                <div style="font-size:0.78rem;opacity:0.8;">{{ $tenant->address ?? '' }}@if($tenant->city) &bull; {{ $tenant->city }}, {{ $tenant->state }}@endif</div>
                @if($tenant->phone)<div style="font-size:0.75rem;opacity:0.7;margin-top:0.2rem;">Ph: {{ $tenant->phone }}</div>@endif
            </div>
            <div class="text-end">
                <div style="font-size:0.7rem;opacity:0.7;text-transform:uppercase;letter-spacing:0.05em;">Fee Receipt</div>
                <div style="font-size:1rem;font-weight:800;font-family:monospace;margin-top:0.2rem;">{{ $payment->receipt_number }}</div>
                <div style="font-size:0.75rem;opacity:0.7;margin-top:0.2rem;">{{ $payment->payment_date?->format('d M Y') }}</div>
            </div>
        </div>
    </div>

    <div class="receipt-body">
        {{-- Student --}}
        <div class="d-flex align-items-center gap-3 mb-3">
            <img src="{{ $payment->student?->photo_url }}" alt="" style="width:52px;height:52px;border-radius:50%;object-fit:cover;border:2px solid #e5e7eb;">
            <div>
                <div style="font-size:1rem;font-weight:700;">{{ $payment->student?->full_name }}</div>
                <div style="font-size:0.78rem;color:#6b7280;">
                    <span style="font-family:monospace;color:#4f46e5;">{{ $payment->student?->admission_number }}</span>
                    &bull; {{ $payment->student?->branch?->name }}
                    &bull; {{ $payment->student?->category }}
                </div>
            </div>
        </div>
        <hr class="divider">

        <div class="row g-3 mb-3">
            <div class="col-6">
                <div class="info-box">
                    <div class="info-label mb-2">Fee Details</div>
                    <div class="d-flex justify-content-between mb-1"><span class="info-label">Fee Type</span><span class="info-value">{{ $payment->feeType?->name }}</span></div>
                    <div class="d-flex justify-content-between mb-1"><span class="info-label">Academic Year</span><span class="info-value">{{ $payment->academicYear?->name }}</span></div>
                    @if($payment->semester)<div class="d-flex justify-content-between mb-1"><span class="info-label">Semester</span><span class="info-value">Sem {{ $payment->semester }}</span></div>@endif
                    <div class="d-flex justify-content-between"><span class="info-label">Payment Mode</span><span class="info-value">{{ \App\Models\FeePayment::PAYMENT_MODES[$payment->payment_mode] ?? ucfirst($payment->payment_mode) }}</span></div>
                </div>
            </div>
            <div class="col-6">
                <div class="info-box">
                    <div class="info-label mb-2">Amount Breakdown</div>
                    <div class="d-flex justify-content-between mb-1"><span class="info-label">Amount Due</span><span class="info-value">₹{{ number_format($payment->amount_due, 2) }}</span></div>
                    @if($payment->discount > 0)<div class="d-flex justify-content-between mb-1"><span class="info-label">Discount</span><span style="color:#059669;font-size:0.82rem;font-weight:600;">- ₹{{ number_format($payment->discount, 2) }}</span></div>@endif
                    @if($payment->fine > 0)<div class="d-flex justify-content-between mb-1"><span class="info-label">Fine</span><span style="color:#dc2626;font-size:0.82rem;font-weight:600;">+ ₹{{ number_format($payment->fine, 2) }}</span></div>@endif
                    <div class="d-flex justify-content-between pt-1" style="border-top:1px solid #e5e7eb;"><span class="info-label">Amount Paid</span><span style="color:#059669;font-size:0.875rem;font-weight:700;">₹{{ number_format($payment->amount_paid, 2) }}</span></div>
                    @if($payment->balance > 0)<div class="d-flex justify-content-between mt-1"><span class="info-label">Balance Due</span><span style="color:#dc2626;font-size:0.82rem;font-weight:600;">₹{{ number_format($payment->balance, 2) }}</span></div>@endif
                </div>
            </div>
        </div>

        @if($payment->transaction_reference)
        <div style="font-size:0.78rem;color:#6b7280;margin-bottom:0.75rem;">
            Transaction Ref: <strong>{{ $payment->transaction_reference }}</strong>
        </div>
        @endif

        <hr class="divider">
        <div class="d-flex justify-content-between align-items-end flex-wrap gap-3">
            <div style="font-size:0.7rem;color:#9ca3af;">
                Collected by {{ $payment->collectedBy?->name ?? 'System' }} &bull; {{ $payment->created_at?->format('d M Y, h:i A') }}<br>
                This is a computer-generated receipt. No signature required.
            </div>
            <div class="text-end">
                <span class="status-{{ $payment->status }}">{{ ucfirst($payment->status) }}</span>
                <div style="width:120px;height:36px;border-bottom:1px solid #374151;margin-top:1.5rem;margin-bottom:0.25rem;"></div>
                <div style="font-size:0.7rem;color:#6b7280;">Authorised Signatory</div>
            </div>
        </div>
    </div>
</div>
<button class="print-btn" onclick="window.print()">🖨 Print Receipt</button>
</body>
</html>
