@extends('layouts.app')
@section('title', 'Students Report')

@section('breadcrumb')
    <li class="breadcrumb-item active">Students Report</li>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-user-graduate me-2" style="color:#059669;"></i>Students Report</h1>
        <p class="mb-0" style="color:var(--muted);font-size:.875rem;">Complete student enrollment report</p>
    </div>
    <div class="d-flex gap-2">
        <div class="dropdown">
            <button class="btn btn-outline-success btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fa-solid fa-file-excel me-1"></i> Export
            </button>
            <ul class="dropdown-menu shadow border-0" style="border-radius:.75rem;background:var(--surface);">
                <li><a class="dropdown-item" style="font-size:.85rem;" href="{{ route('admin.exports.students', ['format'=>'xlsx']) }}"><i class="fa-solid fa-file-excel me-2" style="color:#059669;"></i> Excel (.xlsx)</a></li>
                <li><a class="dropdown-item" style="font-size:.85rem;" href="{{ route('admin.exports.students', ['format'=>'csv']) }}"><i class="fa-solid fa-file-csv me-2" style="color:#0891b2;"></i> CSV</a></li>
            </ul>
        </div>
    </div>
</div>

{{-- Summary --}}
<div class="row g-3 mb-4">
    <div class="col-sm-3">
        <div class="card text-center p-3">
            <div style="font-size:1.75rem;font-weight:800;color:#4f46e5;">{{ $students->total() }}</div>
            <div style="font-size:.78rem;color:var(--muted);">Total Students</div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card text-center p-3">
            <div style="font-size:1.75rem;font-weight:800;color:#059669;">{{ $students->where('status','active')->count() }}</div>
            <div style="font-size:.78rem;color:var(--muted);">Active</div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card text-center p-3">
            <div style="font-size:1.75rem;font-weight:800;color:#d97706;">{{ $students->where('scholarship_eligible',true)->count() }}</div>
            <div style="font-size:.78rem;color:var(--muted);">Scholarship Eligible</div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card text-center p-3">
            <div style="font-size:1.75rem;font-weight:800;color:#7c3aed;">{{ $students->currentPage() }} / {{ $students->lastPage() }}</div>
            <div style="font-size:.78rem;color:var(--muted);">Page</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span style="font-weight:600;"><i class="fa-solid fa-list me-2" style="color:#059669;"></i>All Students</span>
        <span class="badge" style="background:rgba(5,150,105,.1);color:#059669;">{{ $students->total() }} total</span>
    </div>
    <div class="card-body p-0">
        @if($students->isEmpty())
        <div class="empty-state py-5">
            <i class="fa-solid fa-user-graduate d-block mb-2" style="font-size:2.5rem;opacity:.3;"></i>
            <div>No students found.</div>
        </div>
        @else
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student</th>
                        <th>Adm. No</th>
                        <th>Branch / Course</th>
                        <th>Category</th>
                        <th>Semester</th>
                        <th>Adm. Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $i => $student)
                    <tr>
                        <td style="font-size:.78rem;color:var(--muted);">{{ ($students->currentPage()-1)*$students->perPage() + $i + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $student->photo_url }}" class="rounded-circle" style="width:30px;height:30px;object-fit:cover;">
                                <div>
                                    <div style="font-weight:500;font-size:.875rem;">{{ $student->full_name }}</div>
                                    <div style="font-size:.72rem;color:var(--muted);">{{ $student->phone ?? $student->email ?? '—' }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span style="font-family:monospace;font-size:.8rem;color:#4f46e5;font-weight:600;">{{ $student->admission_number }}</span></td>
                        <td style="font-size:.82rem;">
                            <div style="font-weight:500;">{{ $student->branch?->name ?? '—' }}</div>
                            <div style="font-size:.72rem;color:var(--muted);">{{ $student->branch?->course?->name ?? '' }}</div>
                        </td>
                        <td><span class="badge" style="background:rgba(79,70,229,.1);color:#4f46e5;font-size:.72rem;">{{ $student->category }}</span></td>
                        <td style="font-size:.82rem;">Sem {{ $student->current_semester }}</td>
                        <td style="font-size:.78rem;color:var(--muted);">{{ $student->admission_date?->format('d M Y') ?? '—' }}</td>
                        <td>
                            @php $sc = ['active'=>['#dcfce7','#166534'],'inactive'=>['#fee2e2','#991b1b'],'passed_out'=>['#dbeafe','#1e40af'],'dropped'=>['#fef3c7','#92400e']][$student->status] ?? ['#f3f4f6','#374151']; @endphp
                            <span class="badge" style="background:{{ $sc[0] }};color:{{ $sc[1] }};font-size:.72rem;">{{ ucfirst(str_replace('_',' ',$student->status)) }}</span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.students.show', $student) }}" class="btn btn-sm btn-outline-primary" style="padding:.2rem .5rem;" title="View"><i class="fa-solid fa-eye" style="font-size:.72rem;"></i></a>
                                <a href="{{ route('admin.pdf.student-report', $student) }}" class="btn btn-sm btn-outline-danger" style="padding:.2rem .5rem;" title="PDF" target="_blank"><i class="fa-solid fa-file-pdf" style="font-size:.72rem;"></i></a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($students->hasPages())
        <div class="d-flex justify-content-center py-3">{{ $students->links('pagination::bootstrap-5') }}</div>
        @endif
        @endif
    </div>
</div>

@endsection

