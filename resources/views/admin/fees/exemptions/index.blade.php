@extends('layouts.app')

@section('title', 'Fee Exemptions')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.fees.dashboard') }}" style="color:#4f46e5;text-decoration:none;">Fees</a></li>
    <li class="breadcrumb-item active">Exemptions</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-hand-holding-heart me-2" style="color:#0891b2;"></i>Fee Exemptions</h1>
        <p class="mb-0" style="color:var(--muted);font-size:0.875rem;">Students with fee waivers and exemptions</p>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span style="font-weight:600;">
            Exempted Payments
            <span class="badge ms-2" style="background:rgba(8,145,178,0.1);color:#0891b2;">{{ $exemptions->total() }}</span>
        </span>
    </div>
    @if($exemptions->isEmpty())
        <div class="empty-state">
            <i class="fa-solid fa-hand-holding-heart"></i>
            <h5 style="color:var(--muted);">No fee exemptions recorded</h5>
        </div>
    @else
        <div class="table-responsive">
            <table class="table mb-0" style="font-size:0.875rem;">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Fee Type</th>
                        <th>Amount</th>
                        <th>Reason</th>
                        <th>Exempted By</th>
                        <th>Date</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($exemptions as $p)
                    <tr>
                        <td>
                            <div style="font-weight:500;">{{ $p->student?->full_name }}</div>
                            <div style="font-size:0.72rem;color:var(--muted);">{{ $p->student?->admission_number }}</div>
                        </td>
                        <td>{{ $p->feeType?->name ?? '—' }}</td>
                        <td style="color:#0891b2;font-weight:600;">₹{{ number_format($p->amount_due) }}</td>
                        <td style="font-size:0.82rem;max-width:200px;">{{ $p->exemption_reason ?? '—' }}</td>
                        <td style="font-size:0.82rem;">{{ $p->exemptedBy?->name ?? '—' }}</td>
                        <td style="color:var(--muted);font-size:0.82rem;">{{ $p->updated_at?->format('d M Y') }}</td>
                        <td class="text-end">
                            <form method="POST" action="{{ route('admin.fees.exemptions.destroy', $p) }}" id="revoke-{{ $p->id }}">
                                @csrf @method('DELETE')
                            </form>
                            <button type="button" class="btn btn-sm btn-outline-danger"
                                    style="font-size:0.75rem;padding:0.25rem 0.6rem;"
                                    data-confirm-delete="revoke-{{ $p->id }}"
                                    data-name="exemption for {{ $p->student?->full_name }}">
                                Revoke
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($exemptions->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2" style="background:transparent;border-top:1px solid var(--border);">
                <small style="color:var(--muted);">Page {{ $exemptions->currentPage() }} of {{ $exemptions->lastPage() }}</small>
                {{ $exemptions->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        @endif
    @endif
</div>
@endsection

