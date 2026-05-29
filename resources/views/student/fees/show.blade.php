@extends('layouts.student-app')

@section('title', 'Fee Receipt — ' . $payment->receipt_number)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('student.fees.index') }}" style="color:var(--primary);text-decoration:none;">My Fees</a></li>
    <li class="breadcrumb-item active">Receipt {{ $payment->receipt_number }}</li>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-receipt me-2" style="color:#059669;"></i>Fee Receipt</h1>
        <p style="color:var(--muted);font-size:0.875rem;margin:0;">Receipt #{{ $payment->receipt_number }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('student.fees.receipt', $payment) }}"
           class="btn btn-danger btn-sm">
            <i class="fa-solid fa-file-pdf me-1"></i> Download PDF
        </a>
        <a href="{{ route('student.fees.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="row g-3 justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            {{-- Receipt Header --}}
            <div class="card-body p-0">
                <div class="p-4" style="background:linear-gradient(135deg,#059669,#10b981);border-radius:0.75rem 0.75rem 0 0;">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                        <div>
                            <div class="text-white fw-bold" style="font-size:1.1rem;">{{ $tenant->name ?? 'EduTenant ERP' }}</div>
                            <div style="color:rgba(255,255,255,0.75);font-size:0.8rem;">{{ $tenant->address ?? '' }}{{ $tenant->city ? ', ' . $tenant->city : '' }}</div>
                            @if($tenant->phone)<div style="color:rgba(255,255,255,0.7);font-size:0.75rem;">Ph: {{ $tenant->phone }}</div>@endif
                        </div>
                        <div class="text-end">
                            <div style="color:rgba(255,255,255,0.7);font-size:0.7rem;text-transform:uppercase;letter-spacing:.05em;">Fee Receipt</div>
                            <div class="text-white fw-bold" style="font-family:monospace;font-size:1.1rem;">{{ $payment->receipt_number }}</div>
                            <div style="color:rgba(255,255,255,0.7);font-size:0.75rem;">{{ $payment->payment_date?->format('d M Y') }}</div>
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    {{-- Student Info --}}
                    <div class="d-flex align-items-center gap-3 mb-4 pb-3" style="border-bottom:1px dashed var(--border);">
                        <img src="{{ $student->photo_url }}" alt="{{ $student->full_name }}"
                             class="rounded-circle" style="width:52px;height:52px;object-fit:cover;border:2px solid var(--border);">
                        <div>
                            <div style="font-size:1rem;font-weight:700;">{{ $student->full_name }}</div>
                            <div style="font-size:0.78rem;color:var(--muted);">
                                <span style="font-family:monospace;color:#4f46e5;">{{ $student->admission_number }}</span>
                                &bull; {{ $student->branch?->name }}
                                &bull; {{ $student->category }}
                            </div>
                        </div>
                    </div>

                    {{-- Fee Details --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 rounded" style="background:var(--bg);border:1px solid var(--border);">
                                <div style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--muted);margin-bottom:0.75rem;">Fee Details</div>
                                <div class="d-flex justify-content-between mb-2" style="font-size:0.82rem;">
                                    <span style="color:var(--muted);">Fee Type</span>
                                    <span style="font-weight:600;">{{ $payment->feeType?->name }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2" style="font-size:0.82rem;">
                                    <span style="color:var(--muted);">Academic Year</span>
                                    <span style="font-weight:600;">{{ $payment->academicYear?->name }}</span>
                                </div>
                                @if($payment->semester)
                                <div class="d-flex justify-content-between mb-2" style="font-size:0.82rem;">
                                    <span style="color:var(--muted);">Semester</span>
                                    <span style="font-weight:600;">Sem {{ $payment->semester }}</span>
                                </div>
                                @endif
                                <div class="d-flex justify-content-between" style="font-size:0.82rem;">
                                    <span style="color:var(--muted);">Payment Mode</span>
                                    <span style="font-weight:600;">{{ \App\Models\FeePayment::PAYMENT_MODES[$payment->payment_mode] ?? ucfirst($payment->payment_mode) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded" style="background:var(--bg);border:1px solid var(--border);">
                                <div style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--muted);margin-bottom:0.75rem;">Amount Breakdown</div>
                                <div class="d-flex justify-content-between mb-2" style="font-size:0.82rem;">
                                    <span style="color:var(--muted);">Amount Due</span>
                                    <span style="font-weight:600;">₹{{ number_format($payment->amount_due, 2) }}</span>
                                </div>
                                @if($payment->discount > 0)
                                <div class="d-flex justify-content-between mb-2" style="font-size:0.82rem;">
                                    <span style="color:var(--muted);">Discount</span>
                                    <span style="font-weight:600;color:#059669;">- ₹{{ number_format($payment->discount, 2) }}</span>
                                </div>
                                @endif
                                @if($payment->fine > 0)
                                <div class="d-flex justify-content-between mb-2" style="font-size:0.82rem;">
                                    <span style="color:var(--muted);">Fine</span>
                                    <span style="font-weight:600;color:#dc2626;">+ ₹{{ number_format($payment->fine, 2) }}</span>
                                </div>
                                @endif
                                <div class="d-flex justify-content-between pt-2 mb-2" style="border-top:1px solid var(--border);font-size:0.875rem;">
                                    <span style="color:var(--muted);">Amount Paid</span>
                                    <span style="font-weight:700;color:#059669;font-size:1rem;">₹{{ number_format($payment->amount_paid, 2) }}</span>
                                </div>
                                @if($payment->balance > 0)
                                <div class="d-flex justify-content-between" style="font-size:0.82rem;">
                                    <span style="color:var(--muted);">Balance Due</span>
                                    <span style="font-weight:600;color:#dc2626;">₹{{ number_format($payment->balance, 2) }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($payment->transaction_reference)
                    <div style="font-size:0.78rem;color:var(--muted);margin-bottom:1rem;">
                        Transaction Ref: <strong>{{ $payment->transaction_reference }}</strong>
                    </div>
                    @endif

                    {{-- Footer --}}
                    <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 pt-3" style="border-top:1px dashed var(--border);">
                        <div style="font-size:0.72rem;color:var(--muted);">
                            Collected by {{ $payment->collectedBy?->name ?? 'System' }}<br>
                            {{ $payment->created_at?->format('d M Y, h:i A') }}<br>
                            This is a computer-generated receipt.
                        </div>
                        <div class="text-end">
                            @php
                                $sc = ['paid'=>['#dcfce7','#166534'],'partial'=>['#fef3c7','#92400e'],'pending'=>['#fee2e2','#991b1b']][$payment->status] ?? ['#f3f4f6','#374151'];
                            @endphp
                            <span class="badge" style="background:{{ $sc[0] }};color:{{ $sc[1] }};font-size:0.8rem;padding:0.4em 0.8em;">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

