@extends('layouts.app')
@section('title', 'Courses')
@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-book me-2" style="color:#059669;"></i>Courses</h1>
        <p class="mb-0" style="color:var(--muted);font-size:.875rem;">Manage degree courses (B.Sc, BA, BCom)</p>
    </div>
    <a href="{{ route('admin.setup.courses.create') }}" class="btn btn-primary btn-sm">
        <i class="fa-solid fa-plus me-1"></i> Add Course
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    <i class="fa-solid fa-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card">
    <div class="card-header">
        <span style="font-weight:600;">All Courses
            <span class="badge ms-2" style="background:rgba(5,150,105,.1);color:#059669;">{{ $courses->count() }}</span>
        </span>
    </div>
    @if($courses->isEmpty())
    <div class="card-body">
        <div class="empty-state py-4">
            <i class="fa-solid fa-book" style="font-size:3rem;opacity:.2;"></i>
            <h5 class="mt-3" style="color:var(--muted);">No courses added yet</h5>
            <a href="{{ route('admin.setup.courses.create') }}" class="btn btn-primary btn-sm mt-2">Add First Course</a>
        </div>
    </div>
    @else
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Course Name</th>
                    <th>Code</th>
                    <th>Stream</th>
                    <th class="text-center">Duration</th>
                    <th class="text-center">Semesters</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($courses as $i => $course)
                <tr>
                    <td style="color:var(--muted);font-size:.82rem;">{{ $i + 1 }}</td>
                    <td style="font-weight:500;">{{ $course->name }}</td>
                    <td><span class="badge" style="background:rgba(5,150,105,.1);color:#059669;">{{ $course->code ?? '—' }}</span></td>
                    <td style="font-size:.85rem;">{{ $course->stream->name ?? '—' }}</td>
                    <td class="text-center" style="font-size:.85rem;">{{ $course->duration_years ?? '—' }} yrs</td>
                    <td class="text-center" style="font-size:.85rem;">{{ $course->total_semesters ?? '—' }}</td>
                    <td class="text-end">
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="{{ route('admin.setup.courses.edit', $course) }}"
                               class="btn btn-sm btn-outline-secondary" style="padding:.2rem .5rem;">
                                <i class="fa-solid fa-pen" style="font-size:.75rem;"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.setup.courses.destroy', $course) }}" id="del-course-{{ $course->id }}">
                                @csrf @method('DELETE')
                            </form>
                            <button type="button" class="btn btn-sm btn-outline-danger" style="padding:.2rem .5rem;"
                                    data-confirm-delete="del-course-{{ $course->id }}"
                                    data-name="{{ $course->name }}">
                                <i class="fa-solid fa-trash" style="font-size:.75rem;"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection

