@extends('layouts.app')

@section('title', 'Transport Fee Summary')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.fees.transport.index') }}" style="color:#4f46e5;text-decoration:none;">Transport Fees</a></li>
    <li class="breadcrumb-item active">Monthly Summary</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-chart-bar me-2" style="color:#4f46e5;"></i>Transport Fee Summary</h1>
        <p class="mb-0" style="color:var(--muted);font-size:0.875rem;">
            {{ \Carbon\Carbon::create()->month($month)->format('F') }} {{ $year }}
        </p>
    </div>
    <a href="{{ route('admin.fees.transport.index') }}" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-2"></i> Back
    </a>
</div>

{{-- Month/Year Filter --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="d-flex align-items-center gap-3 flex-wrap">
            <div>
                <label class="form-label mb-1" style="font-size:0.8rem;">Month</label>
                <select name="month" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="form-label mb-1" style="font-size:0.8rem;">Year</label>
                <input type="number" name="year" class="form-control form-control-sm" style="width:100px;"
                       value="{{ $year }}" onchange="this.form.submit()">
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card text-center p-3">
            <div style="font-size:1.5rem;font-weight:800;color:#059669;">₹{{ number_format($totalCollected) }}</div>
            <div style="font-size:0.78rem;color:var(--muted);">Collected This Month</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center p-3">
            <div style="font-size:1.5rem;font-weight:800;color:#4f46e5;">{{ $summary->count() }}</div>
            <div style="font-size:0.78rem;color:var(--muted);">Payments Recorded</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center p-3">
            <div style="font-size:1.5rem;font-weight:800;color:#d97706;">{{ $totalStudents }}</div>
            <div style="font-size:0.78rem;color:var(--muted);">Total Vehicle Students</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><i class="fa-solid fa-list me-2" style="color:#4f46e5;"></i>Payment Records</div>
    @if($summary->isEmpty())
        <div class="empty-state py-4">
            <i class="fa-solid fa-bus d-block mb-2"></i>
            <p class="mb-0 small">No transport fee payments for this month</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table mb-0" style="font-size:0.875rem;">
                <thead><tr><th>Student</th><th>Branch</th><th>Amount</th><th>Mode</th><th>Date</th><th>Receipt</th></tr></thead>
                <tbody>
                    @foreach($summary as $p)
                    <tr>
                        <td>
                            <div style="font-weight:500;">{{ $p->student?->full_name }}</div>
                            <div style="font-size:0.72rem;color:var(--muted);">{{ $p->student?->admission_number }}</div>
                        </td>
                        <td>{{ $p->student?->branch?->name ?? '—' }}</td>
                        <td style="font-weight:600;color:#059669;">₹{{ number_format($p->amount_paid) }}</td>
                        <td><span class="badge" style="background:rgba(79,70,229,0.1);color:#4f46e5;font-size:0.7rem;">{{ strtoupper($p->payment_mode) }}</span></td>
                        <td style="color:var(--muted);">{{ $p->payment_date?->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('admin.fees.payments.receipt', $p) }}" class="btn btn-sm btn-outline-primary" style="padding:0.2rem 0.5rem;font-size:0.72rem;">
                                <i class="fa-solid fa-receipt"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection

