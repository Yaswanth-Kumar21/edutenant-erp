@extends('layouts.student-app')

@section('title', 'My Fees')

@section('breadcrumb')
    <li class="breadcrumb-item active">My Fees</li>
@endsection

@section('content')

{{-- Page Header --}}
<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-indian-rupee-sign me-2" style="color:#7c3aed;"></i>My Fee History</h1>
        <p style="color:var(--muted);font-size:0.875rem;margin:0;">All your fee payments and dues</p>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="stat-card green">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-value" style="font-size:1.5rem;">₹{{ number_format($totalPaid) }}</div>
                    <div class="stat-label mt-1">Total Paid</div>
                </div>
                <i class="fa-solid fa-circle-check stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card orange">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-value" style="font-size:1.5rem;">₹{{ number_format($totalDue) }}</div>
                    <div class="stat-label mt-1">Total Due</div>
                </div>
                <i class="fa-solid fa-clock stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card purple">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-value">{{ $totalPending }}</div>
                    <div class="stat-label mt-1">Pending Payments</div>
                </div>
                <i class="fa-solid fa-receipt stat-icon"></i>
            </div>
        </div>
    </div>
</div>

{{-- Payments Table --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="fa-solid fa-list me-2" style="color:#7c3aed;"></i>Payment Records</span>
        <span class="badge" style="background:rgba(124,58,237,0.1);color:#7c3aed;">{{ $payments->total() }} records</span>
    </div>
    <div class="card-body p-0">
        @if($payments->isEmpty())
            <div class="text-center py-5" style="color:var(--muted);">
                <i class="fa-solid fa-receipt d-block mb-2" style="font-size:2.5rem;opacity:0.3;"></i>
                <div style="font-size:0.9rem;">No fee payments recorded yet.</div>
            </div>
        @else
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Receipt No</th>
                        <th>Fee Type</th>
                        <th>Academic Year</th>
                        <th>Payment Date</th>
                        <th>Amount Due</th>
                        <th>Amount Paid</th>
                        <th>Mode</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                    <tr>
                        <td>
                            <span style="font-family:monospace;font-size:0.8rem;color:#4f46e5;font-weight:600;">
                                {{ $payment->receipt_number }}
                            </span>
                        </td>
                        <td>
                            <div style="font-weight:500;">{{ $payment->feeType?->name ?? '—' }}</div>
                            @if($payment->semester)
                            <div style="font-size:0.72rem;color:var(--muted);">Sem {{ $payment->semester }}</div>
                            @endif
                        </td>
                        <td style="font-size:0.82rem;">{{ $payment->academicYear?->name ?? '—' }}</td>
                        <td style="font-size:0.82rem;">{{ $payment->payment_date?->format('d M Y') ?? '—' }}</td>
                        <td style="font-weight:600;">₹{{ number_format($payment->amount_due, 2) }}</td>
                        <td style="font-weight:700;color:#059669;">₹{{ number_format($payment->amount_paid, 2) }}</td>
                        <td>
                            <span class="badge" style="background:rgba(79,70,229,0.1);color:#4f46e5;font-size:0.72rem;">
                                {{ \App\Models\FeePayment::PAYMENT_MODES[$payment->payment_mode] ?? ucfirst($payment->payment_mode) }}
                            </span>
                        </td>
                        <td>
                            @php
                                $statusColors = [
                                    'paid'    => ['bg'=>'#dcfce7','color'=>'#166534'],
                                    'partial' => ['bg'=>'#fef3c7','color'=>'#92400e'],
                                    'pending' => ['bg'=>'#fee2e2','color'=>'#991b1b'],
                                ];
                                $sc = $statusColors[$payment->status] ?? ['bg'=>'#f3f4f6','color'=>'#374151'];
                            @endphp
                            <span class="badge" style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }};font-size:0.72rem;">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('student.fees.show', $payment) }}"
                                   class="btn btn-sm btn-outline-primary" style="font-size:0.72rem;padding:0.25rem 0.5rem;"
                                   title="View Receipt">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('student.fees.receipt', $payment) }}"
                                   class="btn btn-sm btn-outline-danger" style="font-size:0.72rem;padding:0.25rem 0.5rem;"
                                   title="Download PDF">
                                    <i class="fa-solid fa-file-pdf"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
        <div class="d-flex justify-content-center py-3">
            {{ $payments->links() }}
        </div>
        @endif
        @endif
    </div>
</div>

@endsection

