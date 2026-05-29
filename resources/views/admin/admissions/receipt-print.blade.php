<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Receipt — {{ $student->admission_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #fff;
            color: #1a1a2e;
            margin: 0;
            padding: 0;
        }
        .receipt-wrapper {
            max-width: 750px;
            margin: 0 auto;
            padding: 2rem;
        }
        .receipt-header {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: #fff;
            padding: 1.5rem 2rem;
            border-radius: 0.75rem 0.75rem 0 0;
        }
        .receipt-body { padding: 1.5rem 2rem; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 0.75rem 0.75rem; }
        .info-box { background: #f8f7ff; border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 1rem; }
        .info-label { font-size: 0.72rem; color: #6b7280; font-weight: 500; }
        .info-value { font-size: 0.875rem; font-weight: 600; color: #1e1b4b; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8f7ff; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #6b7280; padding: 0.625rem 0.875rem; border-bottom: 2px solid #e5e7eb; }
        td { padding: 0.625rem 0.875rem; border-bottom: 1px solid #e5e7eb; font-size: 0.875rem; }
        tfoot td { font-weight: 700; background: #f8f7ff; }
        .badge-cat { background: rgba(79,70,229,0.1); color: #4f46e5; padding: 0.2em 0.6em; border-radius: 0.25rem; font-size: 0.75rem; font-weight: 600; }
        .status-paid { background: #dcfce7; color: #166534; padding: 0.3em 0.8em; border-radius: 0.375rem; font-size: 0.8rem; font-weight: 600; }
        .status-partial { background: #fef3c7; color: #92400e; padding: 0.3em 0.8em; border-radius: 0.375rem; font-size: 0.8rem; font-weight: 600; }
        .status-pending { background: #fee2e2; color: #991b1b; padding: 0.3em 0.8em; border-radius: 0.375rem; font-size: 0.8rem; font-weight: 600; }
        .divider { border: none; border-top: 1px dashed #d1d5db; margin: 1rem 0; }
        .print-btn {
            position: fixed; bottom: 2rem; right: 2rem;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: #fff; border: none; border-radius: 50px;
            padding: 0.75rem 1.5rem; font-size: 0.9rem; font-weight: 600;
            cursor: pointer; box-shadow: 0 4px 15px rgba(79,70,229,0.4);
            display: flex; align-items: center; gap: 0.5rem;
        }
        @media print {
            .print-btn { display: none !important; }
            body { padding: 0; }
            .receipt-wrapper { padding: 0; max-width: 100%; }
        }
    </style>
</head>
<body>

<div class="receipt-wrapper">
    {{-- Header --}}
    <div class="receipt-header">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <div style="font-size:1.2rem;font-weight:800;margin-bottom:0.25rem;">
                    {{ $tenant->name ?? 'EduTenant ERP' }}
                </div>
                <div style="font-size:0.8rem;opacity:0.8;">
                    {{ $tenant->address ?? '' }}
                    @if($tenant->city) &bull; {{ $tenant->city }}, {{ $tenant->state }} @endif
                </div>
                @if($tenant->phone)
                <div style="font-size:0.78rem;opacity:0.7;margin-top:0.25rem;">
                    <i class="fa-solid fa-phone me-1"></i>{{ $tenant->phone }}
                    @if($tenant->email)
                        &nbsp;&bull;&nbsp;<i class="fa-solid fa-envelope me-1"></i>{{ $tenant->email }}
                    @endif
                </div>
                @endif
            </div>
            <div class="text-end">
                <div style="font-size:0.75rem;opacity:0.7;text-transform:uppercase;letter-spacing:0.05em;">
                    Admission Receipt
                </div>
                <div style="font-size:1.1rem;font-weight:800;font-family:monospace;margin-top:0.25rem;">
                    {{ $receipt?->receipt_number ?? 'PENDING' }}
                </div>
                <div style="font-size:0.78rem;opacity:0.7;margin-top:0.25rem;">
                    {{ $receipt?->payment_date?->format('d M Y') ?? now()->format('d M Y') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Body --}}
    <div class="receipt-body">

        {{-- Student Info --}}
        <div class="d-flex align-items-center gap-3 mb-3">
            <img src="{{ $student->photo_url }}"
                 alt="{{ $student->full_name }}"
                 style="width:60px;height:60px;border-radius:50%;object-fit:cover;border:2px solid #e5e7eb;">
            <div>
                <div style="font-size:1.1rem;font-weight:700;">{{ $student->full_name }}</div>
                <div style="font-size:0.82rem;color:#6b7280;">
                    <span class="badge-cat">{{ $student->admission_number }}</span>
                    &nbsp;&bull;&nbsp;{{ $student->category }}
                    &nbsp;&bull;&nbsp;{{ ucfirst($student->gender ?? '') }}
                </div>
            </div>
        </div>

        <hr class="divider">

        {{-- Details --}}
        <div class="row g-3 mb-3">
            <div class="col-6">
                <div class="info-box">
                    <div class="info-label mb-2">Academic Details</div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="info-label">Branch</span>
                        <span class="info-value">{{ $student->branch?->name ?? '—' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="info-label">Course</span>
                        <span class="info-value">{{ $student->branch?->course?->name ?? '—' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="info-label">Stream</span>
                        <span class="info-value">{{ $student->branch?->course?->stream?->name ?? '—' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="info-label">Academic Year</span>
                        <span class="info-value">{{ $student->academicYear?->name ?? '—' }}</span>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="info-box">
                    <div class="info-label mb-2">Personal Details</div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="info-label">Father's Name</span>
                        <span class="info-value">{{ $student->guardian?->father_name ?? $student->father_name ?? '—' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="info-label">Phone</span>
                        <span class="info-value">{{ $student->phone ?? '—' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="info-label">Date of Birth</span>
                        <span class="info-value">{{ $student->date_of_birth?->format('d M Y') ?? '—' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="info-label">Admission Date</span>
                        <span class="info-value">{{ $student->admission_date?->format('d M Y') ?? '—' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <hr class="divider">

        {{-- Fee Table --}}
        @if($receipt)
        <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#6b7280;margin-bottom:0.75rem;">
            Fee Details
        </div>
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-end">Amount (₹)</th>
                </tr>
            </thead>
            <tbody>
                @if($receipt->admission_fee > 0)
                <tr>
                    <td>Admission Fee</td>
                    <td class="text-end">{{ number_format($receipt->admission_fee, 2) }}</td>
                </tr>
                @endif
                @if($receipt->tuition_fee > 0)
                <tr>
                    <td>Tuition Fee</td>
                    <td class="text-end">{{ number_format($receipt->tuition_fee, 2) }}</td>
                </tr>
                @endif
                @if($receipt->other_fees > 0)
                <tr>
                    <td>Other Fees</td>
                    <td class="text-end">{{ number_format($receipt->other_fees, 2) }}</td>
                </tr>
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <td>Total Amount</td>
                    <td class="text-end" style="color:#4f46e5;">{{ number_format($receipt->total_amount, 2) }}</td>
                </tr>
                <tr>
                    <td style="color:#059669;">Amount Paid</td>
                    <td class="text-end" style="color:#059669;">{{ number_format($receipt->amount_paid, 2) }}</td>
                </tr>
                @if($receipt->balance_due > 0)
                <tr>
                    <td style="color:#dc2626;">Balance Due</td>
                    <td class="text-end" style="color:#dc2626;">{{ number_format($receipt->balance_due, 2) }}</td>
                </tr>
                @endif
            </tfoot>
        </table>

        <div class="d-flex align-items-center justify-content-between mt-3 flex-wrap gap-2">
            <div style="font-size:0.82rem;color:#6b7280;">
                Payment Mode: <strong>{{ ucfirst($receipt->payment_mode) }}</strong>
                @if($receipt->transaction_reference)
                    &nbsp;&bull;&nbsp; Ref: <strong>{{ $receipt->transaction_reference }}</strong>
                @endif
            </div>
            <span class="status-{{ $receipt->status }}">{{ ucfirst($receipt->status) }}</span>
        </div>
        @else
        <div style="text-align:center;padding:1.5rem;color:#6b7280;font-size:0.875rem;">
            No fee collected at admission.
        </div>
        @endif

        <hr class="divider">

        {{-- Footer --}}
        <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mt-3">
            <div style="font-size:0.72rem;color:#9ca3af;">
                Generated by {{ $receipt?->generatedBy?->name ?? auth()->user()->name }}
                &bull; {{ now()->format('d M Y, h:i A') }}
                <br>This is a computer-generated receipt. No signature required.
            </div>
            <div style="text-align:center;">
                <div style="width:120px;height:40px;border-bottom:1px solid #374151;margin-bottom:0.25rem;"></div>
                <div style="font-size:0.72rem;color:#6b7280;">Authorised Signatory</div>
            </div>
        </div>
    </div>
</div>

<button class="print-btn" onclick="window.print()">
    <i class="fa-solid fa-print"></i> Print Receipt
</button>

<script>
    // Auto-trigger print dialog after a short delay
    window.addEventListener('load', function () {
        setTimeout(function () {
            // Don't auto-print — let user click the button
        }, 500);
    });
</script>
</body>
</html>
