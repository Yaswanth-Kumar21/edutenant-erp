@extends('layouts.app')

@section('title', 'Receipt — '.$payment->receipt_number)

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.fees.payments.index') }}" style="color:#4f46e5;text-decoration:none;">Payments</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Receipt</li>
@endsection

@section('content')

{{-- ── Action Bar (hidden on print) ───────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between mb-4 no-print">
    <div>
        <h1 class="page-title">
            <i class="fa-solid fa-receipt me-2" style="color:#4f46e5;"></i>
            Fee Receipt
        </h1>
        <p class="mb-0" style="color:var(--muted);font-size:0.875rem;">
            {{ $payment->receipt_number }}
        </p>
    </div>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fa-solid fa-print me-2"></i> Print Receipt
        </button>
        <a href="{{ route('admin.fees.payments.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-2"></i> Back
        </a>
    </div>
</div>

{{-- ── Receipt Card ─────────────────────────────────────────────────────── --}}
<div class="card mx-auto" id="receipt-card" style="max-width:720px;">
    <div class="card-body p-0">

        {{-- ── College Header ──────────────────────────────────────────── --}}
        <div class="receipt-header text-center p-4"
             style="background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border-radius:0.75rem 0.75rem 0 0;">
            <div class="d-flex align-items-center justify-content-center gap-3 mb-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width:56px;height:56px;background:rgba(255,255,255,0.2);flex-shrink:0;">
                    <i class="fa-solid fa-graduation-cap" style="font-size:1.5rem;"></i>
                </div>
                <div class="text-start">
                    <h2 style="font-size:1.4rem;font-weight:800;margin:0;line-height:1.2;">
                        {{ auth()->user()->tenant->name ?? 'EduTenant ERP' }}
                    </h2>
                    <p style="margin:0;font-size:0.8rem;opacity:0.85;">
                        Fee Receipt — Official Document
                    </p>
                </div>
            </div>
        </div>

        {{-- ── Receipt Meta ─────────────────────────────────────────────── --}}
        <div class="p-4">
            <div class="row g-0 mb-4">
                <div class="col-6">
                    <table style="font-size:0.85rem;width:100%;">
                        <tr>
                            <td style="color:var(--muted);padding:3px 0;width:120px;">Receipt No</td>
                            <td style="font-weight:700;color:#4f46e5;font-family:monospace;">
                                {{ $payment->receipt_number }}
                            </td>
                        </tr>
                        <tr>
                            <td style="color:var(--muted);padding:3px 0;">Payment Date</td>
                            <td style="font-weight:500;">
                                {{ $payment->payment_date?->format('d F Y') ?? now()->format('d F Y') }}
                            </td>
                        </tr>
                        <tr>
                            <td style="color:var(--muted);padding:3px 0;">Payment Mode</td>
                            <td>
                                <span style="text-transform:uppercase;font-weight:600;color:#059669;">
                                    {{ $payment->payment_mode }}
                                </span>
                            </td>
                        </tr>
                        @if($payment->transaction_reference)
                        <tr>
                            <td style="color:var(--muted);padding:3px 0;">Txn Ref</td>
                            <td style="font-family:monospace;font-size:0.8rem;">
                                {{ $payment->transaction_reference }}
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
                <div class="col-6 text-end">
                    <div style="font-size:0.75rem;color:var(--muted);">Collected By</div>
                    <div style="font-weight:600;font-size:0.9rem;">
                        {{ $payment->collectedBy?->name ?? auth()->user()->name }}
                    </div>
                    <div style="font-size:0.75rem;color:var(--muted);margin-top:8px;">Printed On</div>
                    <div style="font-size:0.85rem;">{{ now()->format('d M Y, h:i A') }}</div>
                </div>
            </div>

            {{-- Divider --}}
            <hr style="border-color:var(--border);margin:0 0 1.25rem;">

            {{-- ── Student Details ──────────────────────────────────────── --}}
            <h6 class="section-title">Student Details</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <table style="font-size:0.85rem;width:100%;">
                        <tr>
                            <td style="color:var(--muted);padding:3px 0;width:130px;">Student Name</td>
                            <td style="font-weight:600;">
                                {{ $payment->student?->full_name ?? '—' }}
                            </td>
                        </tr>
                        <tr>
                            <td style="color:var(--muted);padding:3px 0;">Admission No</td>
                            <td style="font-family:monospace;font-weight:500;color:#4f46e5;">
                                {{ $payment->student?->admission_number ?? '—' }}
                            </td>
                        </tr>
                        <tr>
                            <td style="color:var(--muted);padding:3px 0;">Father's Name</td>
                            <td>{{ $payment->student?->father_name ?? '—' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table style="font-size:0.85rem;width:100%;">
                        <tr>
                            <td style="color:var(--muted);padding:3px 0;width:130px;">Branch</td>
                            <td>{{ $payment->student?->branch?->name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td style="color:var(--muted);padding:3px 0;">Course</td>
                            <td>{{ $payment->student?->branch?->course?->name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td style="color:var(--muted);padding:3px 0;">Semester</td>
                            <td>{{ $payment->student?->current_semester ? 'Semester '.$payment->student->current_semester : '—' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- ── Fee Details ──────────────────────────────────────────── --}}
            <h6 class="section-title">Fee Details</h6>
            <div class="table-responsive mb-4">
                <table class="table" style="font-size:0.875rem;border:1px solid var(--border);border-radius:0.5rem;overflow:hidden;">
                    <thead>
                        <tr style="background:var(--bg);">
                            <th style="padding:0.625rem 0.875rem;">#</th>
                            <th style="padding:0.625rem 0.875rem;">Fee Type</th>
                            <th style="padding:0.625rem 0.875rem;">Semester</th>
                            <th style="padding:0.625rem 0.875rem;text-align:right;">Amount Due</th>
                            <th style="padding:0.625rem 0.875rem;text-align:right;">Discount</th>
                            <th style="padding:0.625rem 0.875rem;text-align:right;">Fine</th>
                            <th style="padding:0.625rem 0.875rem;text-align:right;">Amount Paid</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding:0.75rem 0.875rem;">1</td>
                            <td style="padding:0.75rem 0.875rem;font-weight:500;">
                                {{ $payment->feeType?->name ?? '—' }}
                            </td>
                            <td style="padding:0.75rem 0.875rem;">
                                {{ $payment->semester ? 'Sem '.$payment->semester : '—' }}
                            </td>
                            <td style="padding:0.75rem 0.875rem;text-align:right;">
                                ₹{{ number_format($payment->amount_due) }}
                            </td>
                            <td style="padding:0.75rem 0.875rem;text-align:right;color:#059669;">
                                {{ $payment->discount > 0 ? '₹'.number_format($payment->discount) : '—' }}
                            </td>
                            <td style="padding:0.75rem 0.875rem;text-align:right;color:#dc2626;">
                                {{ $payment->fine > 0 ? '₹'.number_format($payment->fine) : '—' }}
                            </td>
                            <td style="padding:0.75rem 0.875rem;text-align:right;font-weight:700;color:#059669;">
                                ₹{{ number_format($payment->amount_paid) }}
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr style="background:var(--bg);">
                            <td colspan="6" style="padding:0.75rem 0.875rem;font-weight:700;text-align:right;">
                                Total Amount Paid
                            </td>
                            <td style="padding:0.75rem 0.875rem;font-weight:800;color:#059669;text-align:right;font-size:1rem;">
                                ₹{{ number_format($payment->amount_paid) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- ── Amount in Words ──────────────────────────────────────── --}}
            <div class="p-3 mb-4 rounded"
                 style="background:rgba(79,70,229,0.05);border:1px solid rgba(79,70,229,0.15);">
                <span style="font-size:0.8rem;color:var(--muted);">Amount in Words: </span>
                <span style="font-weight:600;font-size:0.875rem;color:var(--text);" id="amount-words">
                    {{-- JS will fill this --}}
                    ₹{{ number_format($payment->amount_paid) }} Only
                </span>
            </div>

            @if($payment->remarks)
            <div class="mb-4">
                <span style="font-size:0.8rem;color:var(--muted);">Remarks: </span>
                <span style="font-size:0.875rem;">{{ $payment->remarks }}</span>
            </div>
            @endif

            {{-- ── Signature Line ───────────────────────────────────────── --}}
            <div class="row mt-5 pt-3">
                <div class="col-4 text-center">
                    <div style="border-top:1px solid var(--border);padding-top:0.5rem;font-size:0.8rem;color:var(--muted);">
                        Student Signature
                    </div>
                </div>
                <div class="col-4 text-center">
                    <div style="border-top:1px solid var(--border);padding-top:0.5rem;font-size:0.8rem;color:var(--muted);">
                        Cashier / Accountant
                    </div>
                </div>
                <div class="col-4 text-center">
                    <div style="border-top:1px solid var(--border);padding-top:0.5rem;font-size:0.8rem;color:var(--muted);">
                        Principal / Authorized Signatory
                    </div>
                </div>
            </div>

            {{-- ── Footer Note ──────────────────────────────────────────── --}}
            <div class="text-center mt-4 pt-3" style="border-top:1px dashed var(--border);">
                <p style="font-size:0.75rem;color:var(--muted);margin:0;">
                    This is a computer-generated receipt and is valid without a physical signature.
                    Please retain this receipt for your records.
                </p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    @media print {
        .no-print,
        #sidebar,
        #main-content > nav,
        .topnav {
            display: none !important;
        }

        #main-content {
            margin-left: 0 !important;
        }

        .page-content {
            padding: 0 !important;
        }

        #receipt-card {
            max-width: 100% !important;
            box-shadow: none !important;
            border: none !important;
        }

        body {
            background: #fff !important;
        }

        .receipt-header {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        @page {
            margin: 1cm;
            size: A4;
        }
    }
</style>
@endpush

