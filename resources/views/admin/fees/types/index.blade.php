@extends('layouts.app')
@section('title', 'Fee Types')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.fees.dashboard') }}" style="color:var(--primary);text-decoration:none;">Fees</a></li>
    <li class="breadcrumb-item active">Fee Types</li>
@endsection
@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-tags me-2" style="color:#4f46e5;"></i>Fee Types</h1>
        <p class="mb-0" style="color:var(--muted);font-size:.875rem;">Configure fee categories and amounts</p>
    </div>
    <a href="{{ route('admin.fees.types.create') }}" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus me-1"></i> Add Fee Type</a>
</div>
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span style="font-weight:600;"><i class="fa-solid fa-list me-2" style="color:#4f46e5;"></i>All Fee Types</span>
        <span class="badge" style="background:rgba(79,70,229,.1);color:#4f46e5;">{{ $feeTypes->count() }}</span>
    </div>
    <div class="card-body p-0">
        @if($feeTypes->isEmpty())
        <div class="empty-state py-5">
            <i class="fa-solid fa-tags d-block mb-2" style="font-size:2.5rem;opacity:.3;"></i>
            <div>No fee types configured yet.</div>
            <a href="{{ route('admin.fees.types.create') }}" class="btn btn-primary btn-sm mt-3">Add Fee Type</a>
        </div>
        @else
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr><th>Name</th><th>Code</th><th>Frequency</th><th>Default Amount</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @foreach($feeTypes as $type)
                    <tr>
                        <td style="font-weight:600;">{{ $type->name }}</td>
                        <td><span style="font-family:monospace;font-size:.82rem;color:#4f46e5;">{{ $type->code }}</span></td>
                        <td>
                            @php $fc = ['one_time'=>['#dbeafe','#1e40af'],'per_semester'=>['#dcfce7','#166534'],'per_year'=>['#fef3c7','#92400e'],'monthly'=>['#f3e8ff','#7c3aed']][$type->frequency] ?? ['#f3f4f6','#374151']; @endphp
                            <span class="badge" style="background:{{ $fc[0] }};color:{{ $fc[1] }};font-size:.72rem;">{{ ucfirst(str_replace('_',' ',$type->frequency)) }}</span>
                        </td>
                        <td style="font-weight:700;color:#059669;">?{{ number_format($type->amount ?? 0, 2) }}</td>
                        <td>
                            @if($type->is_active ?? true)
                                <span class="badge" style="background:#dcfce7;color:#166534;">Active</span>
                            @else
                                <span class="badge" style="background:#fee2e2;color:#991b1b;">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.fees.types.edit', $type) }}" class="btn btn-sm btn-outline-secondary" style="padding:.2rem .5rem;"><i class="fa-solid fa-pen" style="font-size:.72rem;"></i></a>
                                <form method="POST" action="{{ route('admin.fees.types.destroy', $type) }}">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-outline-danger" style="padding:.2rem .5rem;" onclick="return confirm('Delete this fee type?')"><i class="fa-solid fa-trash" style="font-size:.72rem;"></i></button></form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection

