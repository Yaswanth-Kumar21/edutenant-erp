@extends('layouts.app')
@section('title', 'Add Scholarship')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.scholarships.index') }}" style="color:#4f46e5;text-decoration:none;">Scholarships</a></li>
    <li class="breadcrumb-item active">Add Scholarship</li>
@endsection
@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-award me-2" style="color:#059669;"></i>Add Scholarship</h1>
        <p class="page-sub">Assign a government or college scholarship to a student</p>
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
        <form method="POST" action="{{ route('admin.scholarships.store') }}">
            @csrf

            <div class="card mb-4">
                <div class="card-header"><i class="fa-solid fa-user-graduate me-2" style="color:#4f46e5;"></i>Student</div>
                <div class="card-body">
                    @if($student)
                        <input type="hidden" name="student_id" value="{{ $student->id }}">
                        <div class="d-flex align-items-center gap-3 p-3 rounded"
                             style="background:rgba(79,70,229,.06);border:1px solid rgba(79,70,229,.2);">
                            <img src="{{ $student->photo_url }}" class="rounded-circle"
                                 style="width:48px;height:48px;object-fit:cover;">
                            <div>
                                <div style="font-weight:600;">{{ $student->full_name }}</div>
                                <div style="font-size:.78rem;color:var(--muted);">
                                    {{ $student->admission_number }} &bull;
                                    {{ $student->branch?->name }} &bull; Sem {{ $student->current_semester }}
                                </div>
                            </div>
                            <a href="{{ route('admin.scholarships.create') }}" class="btn btn-sm btn-outline-secondary ms-auto">Change</a>
                        </div>
                    @else
                        <label class="form-label">Select Student <span class="text-danger">*</span></label>
                        <select name="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                            <option value="">— Select Student —</option>
                            @foreach($students as $s)
                            <option value="{{ $s->id }}" {{ old('student_id')==$s->id?'selected':'' }}>
                                {{ $s->full_name }} ({{ $s->admission_number }})
                            </option>
                            @endforeach
                        </select>
                        @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><i class="fa-solid fa-award me-2" style="color:#059669;"></i>Scholarship Details</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select name="scholarship_category" id="sch-category"
                                    class="form-select @error('scholarship_category') is-invalid @enderror"
                                    onchange="toggleScholarshipFields(this.value)" required>
                                <option value="">— Select Category —</option>
                                <option value="government" {{ old('scholarship_category')==='government'?'selected':'' }}>Government</option>
                                <option value="college"    {{ old('scholarship_category')==='college'?'selected':'' }}>College</option>
                            </select>
                            @error('scholarship_category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active"   {{ old('status','active')==='active'?'selected':'' }}>Active</option>
                                <option value="inactive" {{ old('status')==='inactive'?'selected':'' }}>Inactive</option>
                                <option value="expired"  {{ old('status')==='expired'?'selected':'' }}>Expired</option>
                            </select>
                        </div>

                        {{-- Government fields --}}
                        <div class="col-md-6" id="govt-type-field" style="display:{{ old('scholarship_category')==='government'?'block':'none' }};">
                            <label class="form-label">Scholarship Type <span class="text-danger">*</span></label>
                            <select name="scholarship_type" class="form-select @error('scholarship_type') is-invalid @enderror">
                                <option value="">— Select Type —</option>
                                @foreach(\App\Models\StudentScholarship::GOVT_TYPES as $key=>$label)
                                <option value="{{ $key }}" {{ old('scholarship_type')===$key?'selected':'' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('scholarship_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- College fields --}}
                        <div class="col-md-6" id="college-name-field" style="display:{{ old('scholarship_category')==='college'?'block':'none' }};">
                            <label class="form-label">Scholarship Name <span class="text-danger">*</span></label>
                            <input type="text" name="scholarship_name"
                                   class="form-control @error('scholarship_name') is-invalid @enderror"
                                   value="{{ old('scholarship_name') }}"
                                   placeholder="e.g. Merit Scholarship"
                                   list="college-sch-list">
                            <datalist id="college-sch-list">
                                @foreach(\App\Models\StudentScholarship::COLLEGE_NAMES as $n)
                                <option value="{{ $n }}">@endforeach
                            </datalist>
                            @error('scholarship_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Amount (₹) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="amount"
                                       class="form-control @error('amount') is-invalid @enderror"
                                       value="{{ old('amount', 0) }}" min="0" step="0.01" required>
                            </div>
                            @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control"
                                   value="{{ old('start_date', now()->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control"
                                   value="{{ old('end_date') }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Applicable Semesters</label>
                            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:.5rem;margin-top:.25rem;" id="sem-grid">
                                <div class="sem-btn all-sem" onclick="toggleAllSem(this)"
                                     style="padding:.4rem .5rem;border:1px solid var(--border);border-radius:.375rem;background:var(--surface2);color:var(--muted);font-size:.78rem;font-weight:500;cursor:pointer;text-align:center;grid-column:1/-1;">
                                    ✓ All Semesters
                                </div>
                                @for($s=1;$s<=8;$s++)
                                <div class="sem-btn" data-sem="{{ $s }}" onclick="toggleSem(this)"
                                     style="padding:.4rem .5rem;border:1px solid var(--border);border-radius:.375rem;background:var(--surface);color:var(--muted);font-size:.78rem;font-weight:500;cursor:pointer;text-align:center;">
                                    Sem {{ $s }}
                                </div>
                                @endfor
                            </div>
                            <div id="sem-inputs"></div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Remarks</label>
                            <textarea name="remarks" class="form-control" rows="2"
                                      placeholder="Any additional notes...">{{ old('remarks') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3 justify-content-end">
                <a href="{{ route('admin.scholarships.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                <button type="submit" class="btn btn-primary px-5">
                    <i class="fa-solid fa-award me-2"></i>Add Scholarship
                </button>
            </div>
        </form>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><i class="fa-solid fa-circle-info me-2" style="color:#4f46e5;"></i>Guide</div>
            <div class="card-body" style="font-size:.82rem;color:var(--muted);line-height:1.8;">
                <p><strong style="color:var(--text);">Government Scholarships</strong><br>
                SC/ST/BC/EWS/Minority/Merit — funded by state or central government.</p>
                <p><strong style="color:var(--text);">College Scholarships</strong><br>
                Merit, Sports, Alumni, Management — funded by the institution.</p>
                <p><strong style="color:var(--text);">Applicable Semesters</strong><br>
                Select which semesters this scholarship applies to. Use "All Semesters" for full-course scholarships.</p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function toggleScholarshipFields(val) {
    document.getElementById('govt-type-field').style.display  = val==='government'?'block':'none';
    document.getElementById('college-name-field').style.display = val==='college'?'block':'none';
}

function toggleSem(btn) {
    const isSelected = btn.style.background === 'rgb(79, 70, 229)';
    btn.style.background = isSelected ? 'var(--surface)' : '#4f46e5';
    btn.style.color      = isSelected ? 'var(--muted)'   : '#fff';
    btn.style.borderColor= isSelected ? 'var(--border)'  : '#4f46e5';
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
