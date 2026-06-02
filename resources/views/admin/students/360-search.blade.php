@extends('layouts.app')
@section('title', 'Student 360° Search')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}" style="color:#4f46e5;text-decoration:none;">Students</a></li>
    <li class="breadcrumb-item active">360° View</li>
@endsection
@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-circle-nodes me-2" style="color:#4f46e5;"></i>Student 360° View</h1>
        <p class="page-sub">Complete student lifecycle — admission to current date</p>
    </div>
</div>

{{-- Search Card --}}
<div class="card mb-4" style="max-width:680px;margin:0 auto;">
    <div class="card-body p-4">
        <div class="text-center mb-4">
            <div style="width:64px;height:64px;border-radius:16px;background:linear-gradient(135deg,#4f46e5,#7c3aed);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                <i class="fa-solid fa-magnifying-glass" style="color:#fff;font-size:1.5rem;"></i>
            </div>
            <h5 style="font-weight:700;margin-bottom:4px;">Find a Student</h5>
            <p style="color:var(--muted);font-size:.875rem;margin:0;">Search by Admission Number, Roll Number, or Student Name</p>
        </div>
        <form method="GET" action="{{ route('admin.students.360.search') }}">
            <div class="input-group" style="border-radius:.75rem;overflow:hidden;box-shadow:0 2px 12px rgba(79,70,229,.15);">
                <span class="input-group-text" style="background:#fff;border-right:none;padding:0 1rem;">
                    <i class="fa-solid fa-search" style="color:#4f46e5;"></i>
                </span>
                <input type="text" name="q" class="form-control" value="{{ $query }}"
                       placeholder="Enter admission number, name, or phone..."
                       style="border-left:none;font-size:1rem;padding:.75rem .5rem;" autofocus>
                <button type="submit" class="btn btn-primary px-4" style="border-radius:0 .75rem .75rem 0;">
                    <i class="fa-solid fa-search me-2"></i>Search
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Results --}}
@if($query && $results->isEmpty())
<div class="card" style="max-width:680px;margin:0 auto;">
    <div class="card-body">
        <div class="empty-state py-4">
            <i class="fa-solid fa-user-slash d-block mb-2" style="font-size:2.5rem;opacity:.2;"></i>
            <h5>No students found</h5>
            <p>No results for "<strong>{{ $query }}</strong>". Try a different search term.</p>
        </div>
    </div>
</div>
@elseif($results->isNotEmpty())
<div class="card" style="max-width:680px;margin:0 auto;">
    <div class="card-header">
        <span style="font-weight:600;">{{ $results->count() }} result(s) for "{{ $query }}"</span>
    </div>
    <div class="card-body p-0">
        @foreach($results as $s)
        <a href="{{ route('admin.students.360', $s) }}" class="d-flex align-items-center gap-3 p-3 text-decoration-none"
           style="border-bottom:1px solid var(--border);transition:background .15s;color:inherit;"
           onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background='transparent'">
            <img src="{{ $s->photo_url }}" class="rounded-circle" style="width:44px;height:44px;object-fit:cover;border:2px solid var(--border);">
            <div class="flex-1">
                <div style="font-weight:600;font-size:.9rem;">{{ $s->full_name }}</div>
                <div style="font-size:.78rem;color:var(--muted);">
                    <span style="font-family:monospace;color:#4f46e5;">{{ $s->admission_number }}</span>
                    &bull; {{ $s->branch?->name ?? '—' }}
                    &bull; Sem {{ $s->current_semester }}
                </div>
            </div>
            <div class="text-end">
                @php $sc=['active'=>['#dcfce7','#166534'],'inactive'=>['#fee2e2','#991b1b'],'passed_out'=>['#ede9fe','#5b21b6'],'dropped'=>['#fee2e2','#991b1b']][$s->status]??['#f3f4f6','#374151']; @endphp
                <span class="badge" style="background:{{ $sc[0] }};color:{{ $sc[1] }};">{{ ucfirst(str_replace('_',' ',$s->status)) }}</span>
                <div style="font-size:.72rem;color:var(--muted);margin-top:4px;">{{ $s->academicYear?->name }}</div>
            </div>
            <i class="fa-solid fa-chevron-right" style="color:var(--muted);font-size:.8rem;"></i>
        </a>
        @endforeach
    </div>
</div>
@endif

@endsection
