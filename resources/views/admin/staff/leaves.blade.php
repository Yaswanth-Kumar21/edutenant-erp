@extends('layouts.app')
@section('title', 'Leave Requests')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-calendar-xmark me-2" style="color:#d97706;"></i>Leave Requests</h1>
        <p class="mb-0" style="color:var(--muted);font-size:.875rem;">Manage staff leave applications</p>
    </div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addLeaveModal">
        <i class="fa-solid fa-plus me-1"></i> New Leave Request
    </button>
</div>

{{-- Filters --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label mb-1" style="font-size:.8rem;">Staff Member</label>
                <select name="staff_id" class="form-select form-select-sm">
                    <option value="">All Staff</option>
                    @foreach($staffList as $member)
                        <option value="{{ $member->id }}" {{ request('staff_id') == $member->id ? 'selected' : '' }}>
                            {{ $member->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label mb-1" style="font-size:.8rem;">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="fa-solid fa-filter me-1"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Leave Table --}}
<div class="card">
    <div class="card-header">
        <span style="font-weight:600;">Leave Requests
            <span class="badge ms-2" style="background:rgba(217,119,6,.1);color:#d97706;">{{ $leaves->total() }}</span>
        </span>
    </div>

    @if($leaves->isEmpty())
        <div class="card-body">
            <div class="empty-state py-4">
                <i class="fa-solid fa-calendar-xmark" style="font-size:3rem;opacity:.2;"></i>
                <h5 class="mt-3" style="color:var(--muted);">No leave requests found</h5>
            </div>
        </div>
    @else
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Staff Member</th>
                        <th>Leave Type</th>
                        <th>From</th>
                        <th>To</th>
                        <th class="text-center">Days</th>
                        <th>Reason</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leaves as $leave)
                    <tr>
                        <td>
                            <div style="font-weight:500;font-size:.875rem;">{{ $leave->staff->name ?? '—' }}</div>
                            <div style="font-size:.72rem;color:var(--muted);">{{ $leave->staff->designation ?? '' }}</div>
                        </td>
                        <td>
                            <span class="badge" style="background:rgba(79,70,229,.1);color:#4f46e5;">
                                {{ \App\Models\LeaveRequest::LEAVE_TYPES[$leave->leave_type] ?? $leave->leave_type }}
                            </span>
                        </td>
                        <td style="font-size:.85rem;">{{ $leave->from_date->format('d M Y') }}</td>
                        <td style="font-size:.85rem;">{{ $leave->to_date->format('d M Y') }}</td>
                        <td class="text-center">
                            <span class="badge bg-secondary">{{ $leave->total_days }}</span>
                        </td>
                        <td style="font-size:.82rem;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                            {{ $leave->reason }}
                        </td>
                        <td class="text-center">
                            @if($leave->status === 'pending')
                                <span class="badge" style="background:#fef3c7;color:#92400e;">Pending</span>
                            @elseif($leave->status === 'approved')
                                <span class="badge" style="background:#dcfce7;color:#166534;">Approved</span>
                            @elseif($leave->status === 'rejected')
                                <span class="badge" style="background:#fee2e2;color:#991b1b;">Rejected</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($leave->status) }}</span>
                            @endif
                        </td>
                        <td class="text-end">
                            @if($leave->isPending())
                            <div class="d-flex gap-1 justify-content-end">
                                <form method="POST" action="{{ route('admin.staff.leaves.approve', $leave) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-success"
                                            style="padding:.2rem .5rem;" title="Approve">
                                        <i class="fa-solid fa-check" style="font-size:.75rem;"></i>
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm btn-outline-danger"
                                        style="padding:.2rem .5rem;" title="Reject"
                                        data-bs-toggle="modal"
                                        data-bs-target="#rejectModal"
                                        data-leave="{{ $leave->id }}">
                                    <i class="fa-solid fa-xmark" style="font-size:.75rem;"></i>
                                </button>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($leaves->hasPages())
        <div class="card-footer" style="background:transparent;border-top:1px solid var(--border);">
            {{ $leaves->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
        @endif
    @endif
</div>

{{-- Add Leave Modal --}}
<div class="modal fade" id="addLeaveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background:var(--surface);border-color:var(--border);">
            <div class="modal-header" style="border-color:var(--border);">
                <h5 class="modal-title fw-600" style="font-weight:600;">New Leave Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.staff.leaves.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Staff Member <span class="text-danger">*</span></label>
                        <select name="staff_id" class="form-select" required>
                            <option value="">Select Staff</option>
                            @foreach($staffList as $member)
                                <option value="{{ $member->id }}">{{ $member->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Leave Type <span class="text-danger">*</span></label>
                        <select name="leave_type" class="form-select" required>
                            @foreach(\App\Models\LeaveRequest::LEAVE_TYPES as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">From Date <span class="text-danger">*</span></label>
                            <input type="date" name="from_date" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">To Date <span class="text-danger">*</span></label>
                            <input type="date" name="to_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="3" required placeholder="Reason for leave..."></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-color:var(--border);">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content" style="background:var(--surface);border-color:var(--border);">
            <div class="modal-header" style="border-color:var(--border);">
                <h5 class="modal-title fw-600" style="font-weight:600;">Reject Leave</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="rejectForm">
                @csrf @method('PATCH')
                <div class="modal-body">
                    <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                    <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
                </div>
                <div class="modal-footer" style="border-color:var(--border);">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.getElementById('rejectModal').addEventListener('show.bs.modal', function(e) {
    const leaveId = e.relatedTarget.dataset.leave;
    document.getElementById('rejectForm').action = '/admin/staff/leaves/' + leaveId + '/reject';
});
</script>
@endpush

