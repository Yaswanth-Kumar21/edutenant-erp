@extends('layouts.student-app')

@section('title', 'My Certificates')

@section('breadcrumb')
    <li class="breadcrumb-item active">My Certificates</li>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-file-certificate me-2" style="color:#0891b2;"></i>My Certificates</h1>
        <p style="color:var(--muted);font-size:0.875rem;margin:0;">View and download your uploaded documents</p>
    </div>
</div>

{{-- Progress --}}
@php $total = count($allTypes); $done = count($allTypes) - count($missing); $pct = $total > 0 ? round(($done/$total)*100) : 0; @endphp
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span style="font-weight:600;font-size:0.875rem;">Document Submission Progress</span>
            <span style="font-size:0.875rem;color:var(--muted);">{{ $done }} / {{ $total }} submitted</span>
        </div>
        <div style="height:8px;background:var(--border);border-radius:4px;overflow:hidden;">
            <div style="height:100%;width:{{ $pct }}%;background:linear-gradient(90deg,#059669,#10b981);border-radius:4px;transition:width 0.5s;"></div>
        </div>
        @if(count($missing) > 0)
        <div class="mt-2" style="font-size:0.78rem;color:#d97706;">
            <i class="fa-solid fa-triangle-exclamation me-1"></i>
            Missing: {{ implode(', ', array_map(fn($k) => $allTypes[$k], $missing)) }}
        </div>
        @else
        <div class="mt-2" style="font-size:0.78rem;color:#059669;">
            <i class="fa-solid fa-circle-check me-1"></i> All documents submitted!
        </div>
        @endif
    </div>
</div>

{{-- Certificates Grid --}}
@if($certificates->isEmpty())
<div class="card">
    <div class="card-body text-center py-5" style="color:var(--muted);">
        <i class="fa-solid fa-folder-open d-block mb-3" style="font-size:3rem;opacity:0.3;"></i>
        <div style="font-size:1rem;font-weight:600;margin-bottom:0.5rem;">No documents uploaded yet</div>
        <div style="font-size:0.875rem;">Please visit the college office to submit your documents.</div>
    </div>
</div>
@else
<div class="row g-3">
    @foreach($certificates as $cert)
    <div class="col-sm-6 col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-start gap-3">
                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:48px;height:48px;background:{{ $cert->is_pdf ? 'rgba(220,38,38,0.1)' : 'rgba(79,70,229,0.1)' }};">
                        <i class="fa-solid {{ $cert->is_pdf ? 'fa-file-pdf' : 'fa-file-image' }}"
                           style="font-size:1.4rem;color:{{ $cert->is_pdf ? '#dc2626' : '#4f46e5' }};"></i>
                    </div>
                    <div class="flex-1 overflow-hidden">
                        <div style="font-weight:600;font-size:0.875rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ $cert->certificate_label }}
                        </div>
                        <div style="font-size:0.72rem;color:var(--muted);margin-top:2px;">
                            {{ $cert->file_size_human }}
                            &bull; {{ $cert->created_at?->format('d M Y') }}
                        </div>
                        @if($cert->is_verified)
                        <span class="badge mt-1" style="background:#dcfce7;color:#166534;font-size:0.65rem;">
                            <i class="fa-solid fa-circle-check me-1"></i>Verified
                        </span>
                        @else
                        <span class="badge mt-1" style="background:#fef3c7;color:#92400e;font-size:0.65rem;">
                            <i class="fa-solid fa-clock me-1"></i>Pending Verification
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-footer" style="background:transparent;border-top:1px solid var(--border);padding:0.625rem 1rem;">
                <a href="{{ route('student.certificates.download', $cert) }}"
                   target="_blank"
                   class="btn btn-sm btn-outline-primary w-100" style="font-size:0.78rem;">
                    <i class="fa-solid fa-download me-1"></i> View / Download
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- Missing Documents --}}
@if(count($missing) > 0)
<div class="card mt-4">
    <div class="card-header">
        <i class="fa-solid fa-triangle-exclamation me-2" style="color:#d97706;"></i>
        Pending Documents ({{ count($missing) }})
    </div>
    <div class="card-body">
        <p style="font-size:0.875rem;color:var(--muted);margin-bottom:1rem;">
            The following documents have not been submitted yet. Please visit the college office to submit them.
        </p>
        <div class="row g-2">
            @foreach($missing as $key)
            <div class="col-sm-6 col-md-4">
                <div class="d-flex align-items-center gap-2 p-2 rounded"
                     style="background:rgba(220,38,38,0.05);border:1px solid rgba(220,38,38,0.15);">
                    <i class="fa-solid fa-file-circle-xmark" style="color:#dc2626;font-size:1rem;"></i>
                    <span style="font-size:0.82rem;color:var(--text);">{{ $allTypes[$key] }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

@endsection

