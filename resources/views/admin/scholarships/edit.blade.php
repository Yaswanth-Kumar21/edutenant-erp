@extends('layouts.app')
@section('title', 'Edit Scholarship')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.scholarships.index') }}" style="color:#4f46e5;text-decoration:none;">Scholarships</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection
@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-pen me-2" style="color:#4f46e5;"></i>Edit Scholarship</h1>
        <p class="page-sub">{{ $scholarship->student?->full_name }} — {{ $scholarship->display_name }}</p>
    </div>
    <a href="{{ route('admin.scholarships.index') }}" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-2"></i>Back
    </a>
</div>

@if($errors->any())
<div class="alert alert-danger d-flex gap-2 mb-4" style="border-radius:.75rem;">
    <i class="fa-solid fa-circle-exclamation mt-1 flex-shrink-0"></i>
    <div><strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-1 ps-3">@foreach($errors->all() as $e)<li style="font-size:.875rem;">{{ $e }}</li>@endforeach</ul>
    </div>
</div>
@endif

<div class="row g-4">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('admin.scholarships.update', $scholarship) }}">
            @csrf @method('PUT')

            {{-- Student (read-only) --}}
            <div class="card mb-4">
                <div class="card-header"><i class="fa-solid fa-user-graduate me-2" style="color:#4f46e5;"></i>Student</div>
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 p-3 rounded"
                         style="background:rgba(79,70,229,.06);border:1px solid rgba(79,70,229,.2);">
                        <img src="{{ $scholarship->student?->photo_url }}" class="rounded-circle"
                             style="width:48px;height:48px;object-fit:cover;">
                        <div>
                            <div style="font-weight:600;">{{ $scholarship->student?->full_name }}</div>
                            <div style="font-size:.78rem;color:var(--muted);">
                                {{ $scholarship->student?->admission_number }} &bull;
                                {{ $scholarship->student?->branch?->name }}
                            </div>
                        </div>
                        <span class="badge ms-auto" style="background:{{ $scholarship->isGovernment()?'rgba(79,70,229,.1)':'rgba(217,119,6,.1)' }};color:{{ $scholarship->isGovernment()?'#4f46e5':'#d97706' }};">
                            {{ ucfirst($scholarship->scholarship_category) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><i class="fa-solid fa-award me-2" style="color:#059669;"></i>Scholarship Details</div>
                <div class="card-body">
                    <div class="row g-3">
                        @if($scholarship->isGovernment())
                        <div class="col-md-6">
                            <label class="form-label">Scholarship Type</label>
                            <select name="scholarship_type" class="form-select">
                                <option value="">— Select Type —</option>
                                @foreach(\App\Models\StudentScholarship::GOVT_TYPES as $key=>$label)
                                <option value="{{ $key }}" {{ old('scholarship_type',$scholarship->scholarship_type)===$key?'selected':'' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        @else
                        <div class="col-md-6">
                            <label class="form-label">Scholarship Name</label>
                            <input type="text" name="scholarship_name" class="form-control"
                                   value="{{ old('scholarship_name',$scholarship->scholarship_name) }}"
                                   list="college-sch-list">
                            <datalist id="college-sch-list">
                                @foreach(\App\Models\StudentScholarship::COLLEGE_NAMES as $n)<option value="{{ $n }}">@endforeach
                            </datalist>
                        </div>
                        @endif

                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                @foreach(['active'=>'Active','inactive'=>'Inactive','expired'=>'Expired'] as $v=>$l)
                                <option value="{{ $v }}" {{ old('status',$scholarship->status)===$v?'selected':'' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Amount (₹) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="amount" class="form-control"
                                       value="{{ old('amount',$scholarship->amount) }}" min="0" step="0.01" required>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control"
                                   value="{{ old('start_date',$scholarship->start_date?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control"
                                   value="{{ old('end_date',$scholarship->end_date?->format('Y-m-d')) }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Applicable Semesters</label>
                            @php $activeSems = $scholarship->applicable_semesters ?? []; @endphp
                            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:.5rem;margin-top:.25rem;" id="sem-grid">
                                <div class="sem-btn all-sem" onclick="toggleAllSem(this)"
                                     style="padding:.4rem .5rem;border:1px solid var(--border);border-radius:.375rem;background:{{ count($activeSems)===8?'#059669':'var(--surface2)' }};color:{{ count($activeSems)===8?'#fff':'var(--muted)' }};font-size:.78rem;font-weight:500;cursor:pointer;text-align:center;grid-column:1/-1;">
                                    ✓ All Semesters
                                </div>
                                @for($s=1;$s<=8;$s++)
                                @php $isActive = in_array($s,$activeSems); @endphp
                                <div class="sem-btn" data-sem="{{ $s }}" onclick="toggleSem(this)"
                                     style="padding:.4rem .5rem;border:1px solid {{ $isActive?'#4f46e5':'var(--border)' }};border-radius:.375rem;background:{{ $isActive?'#4f46e5':'var(--surface)' }};color:{{ $isActive?'#fff':'var(--muted)' }};font-size:.78rem;font-weight:500;cursor:pointer;text-align:center;">
                                    Sem {{ $s }}
                                </div>
                                @endfor
                            </div>
                            <div id="sem-inputs">
                                @foreach($activeSems as $sem)
                                <input type="hidden" name="applicable_semesters[]" value="{{ $sem }}">
                                @endforeach
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Remarks</label>
                            <textarea name="remarks" class="form-control" rows="2">{{ old('remarks',$scholarship->remarks) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3 justify-content-end">
                <a href="{{ route('admin.scholarships.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                <button type="submit" class="btn btn-primary px-5">
                    <i class="fa-solid fa-floppy-disk me-2"></i>Save Changes
                </button>
            </div>
        </form>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><i class="fa-solid fa-circle-info me-2" style="color:var(--muted);"></i>Record Info</div>
            <div class="card-body p-0">
                @foreach([
                    ['Created',  $scholarship->created_at?->format('d M Y, h:i A')],
                    ['Updated',  $scholarship->updated_at?->diffForHumans()],
                    ['Category', ucfirst($scholarship->scholarship_category)],
                ] as [$l,$v])
                <div class="d-flex justify-content-between px-4 py-3" style="border-bottom:1px solid var(--border);font-size:.82rem;">
                    <span style="color:var(--muted);">{{ $l }}</span>
                    <span style="font-weight:600;">{{ $v }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function toggleSem(btn) {
    const isSelected = btn.style.background === 'rgb(79, 70, 229)';
    btn.style.background  = isSelected ? 'var(--surface)' : '#4f46e5';
    btn.style.color       = isSelected ? 'var(--muted)'   : '#fff';
    btn.style.borderColor = isSelected ? 'var(--border)'  : '#4f46e5';
    updateSemInputs();
}
function toggleAllSem(btn) {
    const isSelected = btn.style.background === 'rgb(5, 150, 105)';
    document.querySelectorAll('#sem-grid .sem-btn:not(.all-sem)').forEach(b => {
        b.style.background  = isSelected ? 'var(--surface)' : '#4f46e5';
        b.style.color       = isSelected ? 'var(--muted)'   : '#fff';
        b.style.borderColor = isSelected ? 'var(--border)'  : '#4f46e5';
    });
    btn.style.background  = isSelected ? 'var(--surface2)' : '#059669';
    btn.style.color       = isSelected ? 'var(--muted)'    : '#fff';
    btn.style.borderColor = isSelected ? 'var(--border)'   : '#059669';
    updateSemInputs();
}
function updateSemInputs() {
    const container = document.getElementById('sem-inputs');
    container.innerHTML = '';
    document.querySelectorAll('#sem-grid .sem-btn:not(.all-sem)').forEach(b => {
        if (b.style.background === 'rgb(79, 70, 229)') {
            const inp = document.createElement('input');
            inp.type  = 'hidden';
            inp.name  = 'applicable_semesters[]';
            inp.value = b.dataset.sem;
            container.appendChild(inp);
        }
    });
}
</script>
@endpush
