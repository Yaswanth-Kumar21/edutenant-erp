@extends('layouts.app')
@section('title', 'New Student Admission')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}" style="color:#4f46e5;text-decoration:none;">Students</a></li>
    <li class="breadcrumb-item active">New Admission</li>
@endsection
@push('styles')
<style>
.wizard-steps{display:flex;align-items:center;gap:0;margin-bottom:2rem;overflow-x:auto;padding-bottom:.5rem;}
.wizard-step{display:flex;align-items:center;flex:1;min-width:0;}
.wizard-step-inner{display:flex;flex-direction:column;align-items:center;gap:.375rem;cursor:pointer;flex-shrink:0;}
.wizard-step-num{width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;border:2px solid var(--border);background:var(--surface);color:var(--muted);transition:all .25s ease;position:relative;z-index:1;}
.wizard-step-label{font-size:.68rem;font-weight:500;color:var(--muted);white-space:nowrap;transition:color .25s;}
.wizard-step-line{flex:1;height:2px;background:var(--border);margin:0 .2rem;margin-bottom:1.25rem;transition:background .25s;}
.wizard-step.active .wizard-step-num{background:linear-gradient(135deg,#4f46e5,#7c3aed);border-color:#4f46e5;color:#fff;box-shadow:0 4px 12px rgba(79,70,229,.35);}
.wizard-step.active .wizard-step-label{color:#4f46e5;font-weight:600;}
.wizard-step.completed .wizard-step-num{background:#059669;border-color:#059669;color:#fff;}
.wizard-step.completed .wizard-step-label{color:#059669;}
.wizard-step.completed+.wizard-step-line{background:#059669;}
.step-panel{display:none;}
.step-panel.active{display:block;animation:fadeUp .3s ease;}
@keyframes fadeUp{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:translateY(0);}}
.photo-upload-area{width:110px;height:110px;border-radius:50%;border:2px dashed var(--border);display:flex;align-items:center;justify-content:center;cursor:pointer;overflow:hidden;position:relative;transition:border-color .2s;background:var(--bg);}
.photo-upload-area:hover{border-color:#4f46e5;}
.photo-upload-area img{width:100%;height:100%;object-fit:cover;}
.photo-upload-overlay{position:absolute;inset:0;background:rgba(79,70,229,.7);display:flex;align-items:center;justify-content:center;opacity:0;transition:opacity .2s;border-radius:50%;}
.photo-upload-area:hover .photo-upload-overlay{opacity:1;}
.cert-upload-zone{border:2px dashed var(--border);border-radius:.75rem;padding:1.5rem;text-align:center;cursor:pointer;transition:all .2s;background:var(--bg);}
.cert-upload-zone:hover,.cert-upload-zone.dragover{border-color:#4f46e5;background:rgba(79,70,229,.04);}
.cert-item{display:flex;align-items:center;gap:.75rem;padding:.5rem .75rem;background:var(--surface);border:1px solid var(--border);border-radius:.5rem;margin-bottom:.4rem;}
.toggle-section{background:var(--bg);border:1px solid var(--border);border-radius:.75rem;padding:1rem 1.25rem;margin-bottom:1rem;}
.toggle-section-body{margin-top:1rem;padding-top:1rem;border-top:1px solid var(--border);}
.sem-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:.5rem;margin-top:.5rem;}
.sem-btn{padding:.4rem .5rem;border:1px solid var(--border);border-radius:.375rem;background:var(--surface);color:var(--muted);font-size:.78rem;font-weight:500;cursor:pointer;text-align:center;transition:all .15s;user-select:none;}
.sem-btn.selected{background:#4f46e5;border-color:#4f46e5;color:#fff;}
.sem-btn.all-sem{grid-column:1/-1;background:var(--surface2);}
.sem-btn.all-sem.selected{background:#059669;border-color:#059669;color:#fff;}
.fee-summary-row{display:flex;justify-content:space-between;align-items:center;padding:.45rem 0;border-bottom:1px solid var(--border);font-size:.875rem;}
.fee-summary-row:last-child{border-bottom:none;}
</style>
@endpush
@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-user-plus me-2" style="color:#4f46e5;"></i>New Student Admission</h1>
        <p class="mb-0" style="color:var(--muted);font-size:.875rem;">Complete all 7 steps to register a new student</p>
    </div>
    <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-2"></i>Back</a>
</div>
@if($errors->any())
<div class="alert alert-danger d-flex gap-2 mb-4" style="border-radius:.75rem;">
    <i class="fa-solid fa-circle-exclamation mt-1 flex-shrink-0"></i>
    <div><strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-1 ps-3">@foreach($errors->all() as $e)<li style="font-size:.875rem;">{{ $e }}</li>@endforeach</ul>
    </div>
</div>
<span id="admission-error-fields" data-fields="{{ json_encode($errors->keys()) }}" class="d-none"></span>
@endif

{{-- ── Wizard Steps ─────────────────────────────────────────────────────── --}}
<div class="wizard-steps" id="wizard-steps">
    @php $steps = [1=>'Personal',2=>'Academic',3=>'Category',4=>'Guardian',5=>'Scholarship',6=>'Documents',7=>'Receipt'];
    $icons = [1=>'fa-user',2=>'fa-graduation-cap',3=>'fa-id-card',4=>'fa-users',5=>'fa-award',6=>'fa-file-arrow-up',7=>'fa-receipt']; @endphp
    @foreach($steps as $n => $label)
    <div class="wizard-step {{ $n===1?'active':'' }}" data-step="{{ $n }}" onclick="goToStep({{ $n }})">
        <div class="wizard-step-inner">
            <div class="wizard-step-num"><i class="fa-solid {{ $icons[$n] }}" id="step{{ $n }}-icon"></i></div>
            <div class="wizard-step-label">{{ $label }}</div>
        </div>
    </div>
    @if($n < 7)<div class="wizard-step-line" id="line-{{ $n }}"></div>@endif
    @endforeach
</div>

{{-- ── Main Form ────────────────────────────────────────────────────────── --}}
<form method="POST" action="{{ route('admin.admissions.store') }}" enctype="multipart/form-data" id="admission-form" novalidate>
@csrf

{{-- ════ STEP 1: Personal ════ --}}
<div class="step-panel active" id="step-1">
<div class="card mb-4">
    <div class="card-header"><span class="rounded-circle d-inline-flex align-items-center justify-content-center me-2" style="width:24px;height:24px;background:rgba(79,70,229,.1);font-size:.72rem;font-weight:700;color:#4f46e5;">1</span>Personal Details</div>
    <div class="card-body">
        <div class="d-flex align-items-start gap-4 mb-4 flex-wrap">
            <div>
                <label class="form-label d-block">Profile Photo</label>
                <div class="photo-upload-area" onclick="document.getElementById('photo-input').click()">
                    <img id="photo-preview" src="https://ui-avatars.com/api/?name=Student&background=4f46e5&color=fff&size=128" alt="Photo">
                    <div class="photo-upload-overlay"><i class="fa-solid fa-camera text-white" style="font-size:1.1rem;"></i></div>
                </div>
                <input type="file" id="photo-input" name="photo" accept="image/*" class="d-none" onchange="previewPhoto(this)">
                <div class="mt-1" style="font-size:.7rem;color:var(--muted);text-align:center;">JPG/PNG max 2MB</div>
            </div>
            <div class="flex-1" style="min-width:200px;">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">First Name <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" placeholder="e.g. Rahul" required>
                        @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="col-md-6"><label class="form-label">Last Name <span class="text-danger">*</span></label>
                        <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" placeholder="e.g. Sharma" required>
                        @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                </div>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-4"><label class="form-label">Date of Birth</label>
                <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth') }}" max="{{ now()->subYears(14)->format('Y-m-d') }}">
                @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-4"><label class="form-label">Gender <span class="text-danger">*</span></label>
                <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                    <option value="">Select Gender</option>
                    @foreach(['male'=>'Male','female'=>'Female','other'=>'Other'] as $v=>$l)
                    <option value="{{ $v }}" {{ old('gender')===$v?'selected':'' }}>{{ $l }}</option>@endforeach
                </select>@error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-4"><label class="form-label">Blood Group</label>
                <select name="blood_group" class="form-select">
                    <option value="">Select</option>
                    @foreach(['A+','A-','B+','B-','O+','O-','AB+','AB-'] as $bg)
                    <option value="{{ $bg }}" {{ old('blood_group')===$bg?'selected':'' }}>{{ $bg }}</option>@endforeach
                </select></div>
            <div class="col-md-6"><label class="form-label">Phone <span class="text-danger">*</span></label>
                <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="10-digit mobile" required>
                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-6"><label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="student@email.com"></div>
            <div class="col-md-6"><label class="form-label">Aadhaar Number</label>
                <input type="text" name="aadhaar_number" class="form-control" value="{{ old('aadhaar_number') }}" placeholder="12-digit Aadhaar" maxlength="20"></div>
            <div class="col-md-6"><label class="form-label">Address</label>
                <input type="text" name="address" class="form-control" value="{{ old('address') }}" placeholder="Full residential address"></div>
            <div class="col-md-4"><label class="form-label">City</label><input type="text" name="city" class="form-control" value="{{ old('city') }}"></div>
            <div class="col-md-4"><label class="form-label">State</label><input type="text" name="state" class="form-control" value="{{ old('state') }}"></div>
            <div class="col-md-4"><label class="form-label">Pincode</label><input type="text" name="pincode" class="form-control" value="{{ old('pincode') }}" maxlength="10"></div>
        </div>
    </div>
</div>
<div class="d-flex justify-content-end">
    <button type="button" class="btn btn-primary px-5" onclick="nextStep(1)">Next: Academic <i class="fa-solid fa-arrow-right ms-2"></i></button>
</div>
</div>

{{-- ════ STEP 2: Academic ════ --}}
<div class="step-panel" id="step-2">
<div class="card mb-4">
    <div class="card-header"><span class="rounded-circle d-inline-flex align-items-center justify-content-center me-2" style="width:24px;height:24px;background:rgba(5,150,105,.1);font-size:.72rem;font-weight:700;color:#059669;">2</span>Academic Details</div>
    <div class="card-body">
        @php
            $branchError = $errors->has('branch_id') ? ' is-invalid' : '';
            $yearError   = $errors->has('academic_year_id') ? ' is-invalid' : '';
            // Pre-build branch options HTML to avoid Blade @foreach inside <select> parsing issue
            $branchOptionsHtml = '';
            foreach ($branches->groupBy('course.stream.name') as $sName => $sBranches) {
                $sLabel = e($sName ?? 'Other');
                $branchOptionsHtml .= "<optgroup label=\"{$sLabel}\">";
                foreach ($sBranches->groupBy('course.name') as $cName => $cBranches) {
                    foreach ($cBranches as $b) {
                        $sel = old('branch_id') == $b->id ? ' selected' : '';
                        $bName = e($b->name);
                        $cNameE = e($cName);
                        $branchOptionsHtml .= "<option value=\"{$b->id}\"{$sel} data-tuition=\"{$b->tuition_fee_student}\">{$bName} ({$cNameE})</option>";
                    }
                }
                $branchOptionsHtml .= '</optgroup>';
            }
            // Pre-build academic year options HTML
            $yearOptionsHtml = '';
            foreach ($academicYears as $yr) {
                $sel = (old('academic_year_id') == $yr->id || (!old('academic_year_id') && $yr->is_current)) ? ' selected' : '';
                $yrName = e($yr->name . ($yr->is_current ? ' (Current)' : ''));
                $yearOptionsHtml .= "<option value=\"{$yr->id}\"{$sel}>{$yrName}</option>";
            }
        @endphp
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Branch <span class="text-danger">*</span></label>
                <select name="branch_id" id="branch_id" class="form-select{{ $branchError }}" required>
                    <option value="">-- Select Branch --</option>
                    {!! $branchOptionsHtml !!}
                </select>
                @error('branch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-6"><label class="form-label">Academic Year <span class="text-danger">*</span></label>
                <select name="academic_year_id" class="form-select{{ $yearError }}" required>
                    <option value="">-- Select Year --</option>
                    {!! $yearOptionsHtml !!}
                </select>
                @error('academic_year_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-4"><label class="form-label">10th Marks (%)</label>
                <input type="number" name="marks_10th" class="form-control" value="{{ old('marks_10th') }}" placeholder="0.00" min="0" max="100" step="0.01"></div>
            <div class="col-md-4"><label class="form-label">12th Marks (%)</label>
                <input type="number" name="marks_12th" class="form-control" value="{{ old('marks_12th') }}" placeholder="0.00" min="0" max="100" step="0.01"></div>
            <div class="col-md-4"><label class="form-label">Current Semester</label>
                <select name="current_semester" class="form-select">
                    @for($i=1;$i<=8;$i++)<option value="{{ $i }}" {{ old('current_semester',1)==$i?'selected':'' }}>Semester {{ $i }}</option>@endfor
                </select></div>
            <div class="col-md-6"><label class="form-label">Previous Institution</label>
                <input type="text" name="previous_institution" class="form-control" value="{{ old('previous_institution') }}" placeholder="Name of previous college/school"></div>
            <div class="col-md-6"><label class="form-label">University Reg. Number</label>
                <input type="text" name="university_reg_number" class="form-control" value="{{ old('university_reg_number') }}" placeholder="Assigned by university (optional)"></div>
        </div>
    </div>
</div>
<div class="d-flex justify-content-between">
    <button type="button" class="btn btn-outline-secondary px-4" onclick="prevStep(2)"><i class="fa-solid fa-arrow-left me-2"></i>Previous</button>
    <button type="button" class="btn btn-primary px-5" onclick="nextStep(2)">Next: Category <i class="fa-solid fa-arrow-right ms-2"></i></button>
</div>
</div>

{{-- ════ STEP 3: Category ════ --}}
<div class="step-panel" id="step-3">
<div class="card mb-4">
    <div class="card-header"><span class="rounded-circle d-inline-flex align-items-center justify-content-center me-2" style="width:24px;height:24px;background:rgba(217,119,6,.1);font-size:.72rem;font-weight:700;color:#d97706;">3</span>Category &amp; Admission Details</div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Reservation Category <span class="text-danger">*</span></label>
                <div class="row g-2 mt-1">
                    @foreach(['GEN'=>['General','#4f46e5'],'OBC'=>['OBC','#059669'],'SC'=>['SC','#d97706'],'ST'=>['ST','#7c3aed'],'EWS'=>['EWS','#0891b2'],'OTHER'=>['Other','#6b7280']] as $val=>[$label,$color])
                    <div class="col-6 col-md-4">
                        <label class="d-flex align-items-center gap-2 p-2 rounded" style="border:2px solid var(--border);cursor:pointer;transition:all .2s;" id="cat-label-{{ $val }}">
                            <input type="radio" name="category" value="{{ $val }}" class="form-check-input m-0" {{ old('category','GEN')===$val?'checked':'' }} onchange="highlightCategory('{{ $val }}')">
                            <span style="font-size:.85rem;font-weight:600;color:{{ $color }};">{{ $label }}</span>
                        </label>
                    </div>@endforeach
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Admission Date <span class="text-danger">*</span></label>
                <input type="date" name="admission_date" class="form-control @error('admission_date') is-invalid @enderror" value="{{ old('admission_date',now()->format('Y-m-d')) }}" required>
                @error('admission_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <div class="d-flex align-items-center gap-3 p-3 rounded" style="background:var(--bg);border:1px solid var(--border);">
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" id="vehicle_opted" name="vehicle_opted" value="1" {{ old('vehicle_opted')?'checked':'' }} onchange="toggleVehicleDate(this.checked)">
                        <label class="form-check-label fw-500" for="vehicle_opted" style="font-weight:500;">
                            <i class="fa-solid fa-bus me-2" style="color:#0891b2;"></i>College Vehicle / Transport
                        </label>
                    </div>
                    <div id="vehicle-date-wrap" style="display:{{ old('vehicle_opted')?'flex':'none' }};align-items:center;gap:.75rem;">
                        <label class="form-label mb-0" style="white-space:nowrap;">Start Date</label>
                        <input type="date" name="vehicle_start_date" class="form-control" style="width:160px;" value="{{ old('vehicle_start_date') }}">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="d-flex justify-content-between">
    <button type="button" class="btn btn-outline-secondary px-4" onclick="prevStep(3)"><i class="fa-solid fa-arrow-left me-2"></i>Previous</button>
    <button type="button" class="btn btn-primary px-5" onclick="nextStep(3)">Next: Guardian <i class="fa-solid fa-arrow-right ms-2"></i></button>
</div>
</div>

{{-- ════ STEP 4: Guardian ════ --}}
<div class="step-panel" id="step-4">
<div class="card mb-4">
    <div class="card-header"><span class="rounded-circle d-inline-flex align-items-center justify-content-center me-2" style="width:24px;height:24px;background:rgba(124,58,237,.1);font-size:.72rem;font-weight:700;color:#7c3aed;">4</span>Guardian / Parent Details</div>
    <div class="card-body">
        <h6 class="fw-600 mb-3" style="font-size:.82rem;text-transform:uppercase;letter-spacing:.05em;color:var(--muted);">Father's Information</h6>
        <div class="row g-3 mb-4">
            <div class="col-md-6"><label class="form-label">Father's Name</label><input type="text" name="father_name" class="form-control" value="{{ old('father_name') }}" placeholder="Father's full name"></div>
            <div class="col-md-6"><label class="form-label">Occupation</label><input type="text" name="father_occupation" class="form-control" value="{{ old('father_occupation') }}" placeholder="e.g. Farmer, Business"></div>
            <div class="col-md-6"><label class="form-label">Father's Phone</label><input type="tel" name="father_phone" class="form-control" value="{{ old('father_phone') }}" placeholder="Mobile number"></div>
            <div class="col-md-6"><label class="form-label">Father's Email</label><input type="email" name="father_email" class="form-control" value="{{ old('father_email') }}" placeholder="Email (optional)"></div>
        </div>
        <h6 class="fw-600 mb-3" style="font-size:.82rem;text-transform:uppercase;letter-spacing:.05em;color:var(--muted);">Mother's Information</h6>
        <div class="row g-3 mb-4">
            <div class="col-md-6"><label class="form-label">Mother's Name</label><input type="text" name="mother_name" class="form-control" value="{{ old('mother_name') }}" placeholder="Mother's full name"></div>
            <div class="col-md-6"><label class="form-label">Occupation</label><input type="text" name="mother_occupation" class="form-control" value="{{ old('mother_occupation') }}" placeholder="e.g. Homemaker, Teacher"></div>
            <div class="col-md-6"><label class="form-label">Mother's Phone</label><input type="tel" name="mother_phone" class="form-control" value="{{ old('mother_phone') }}" placeholder="Mobile number"></div>
        </div>
        <h6 class="fw-600 mb-3" style="font-size:.82rem;text-transform:uppercase;letter-spacing:.05em;color:var(--muted);">Financial Information</h6>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Annual Family Income (₹)</label>
                <div class="input-group"><span class="input-group-text">₹</span>
                    <input type="number" name="annual_income" class="form-control" value="{{ old('annual_income') }}" placeholder="0" min="0">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="d-flex justify-content-between">
    <button type="button" class="btn btn-outline-secondary px-4" onclick="prevStep(4)"><i class="fa-solid fa-arrow-left me-2"></i>Previous</button>
    <button type="button" class="btn btn-primary px-5" onclick="nextStep(4)">Next: Scholarship &amp; Benefits <i class="fa-solid fa-arrow-right ms-2"></i></button>
</div>
</div>

{{-- ════ STEP 5: Scholarship & Benefits ════ --}}
<div class="step-panel" id="step-5">
<div class="card mb-4">
    <div class="card-header">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:28px;height:28px;background:rgba(5,150,105,.1);">
                <i class="fa-solid fa-award" style="color:#059669;font-size:.8rem;"></i>
            </div>
            <span>Scholarship &amp; Benefits</span>
            <span class="badge ms-1" style="background:rgba(5,150,105,.1);color:#059669;font-size:.7rem;">Step 5</span>
        </div>
    </div>
    <div class="card-body">

        {{-- ── Section 1: Government Scholarship ── --}}
        <div class="toggle-section" id="govt-section">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded d-flex align-items-center justify-content-center" style="width:36px;height:36px;background:rgba(79,70,229,.1);">
                        <i class="fa-solid fa-landmark" style="color:#4f46e5;font-size:.9rem;"></i>
                    </div>
                    <div>
                        <div style="font-weight:600;font-size:.9rem;">Government Scholarship</div>
                        <div style="font-size:.78rem;color:var(--muted);">SC/ST/BC/EWS/Minority/Merit scholarships</div>
                    </div>
                </div>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" id="govt_scholarship_eligible" name="govt_scholarship_eligible" value="1"
                           {{ old('govt_scholarship_eligible')?'checked':'' }} onchange="toggleSection('govt-body',this.checked)">
                    <label class="form-check-label" for="govt_scholarship_eligible">Eligible</label>
                </div>
            </div>
            <div class="toggle-section-body" id="govt-body" style="display:{{ old('govt_scholarship_eligible')?'block':'none' }};">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Scholarship Type <span class="text-danger">*</span></label>
                        <select name="govt_scholarship_type" class="form-select @error('govt_scholarship_type') is-invalid @enderror">
                            <option value="">-- Select Type --</option>
                            @foreach(\App\Models\StudentScholarship::GOVT_TYPES as $key=>$label)
                            <option value="{{ $key }}" {{ old('govt_scholarship_type')===$key?'selected':'' }}>{{ $label }}</option>@endforeach
                        </select>@error('govt_scholarship_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Scholarship Amount (₹)</label>
                        <div class="input-group"><span class="input-group-text">₹</span>
                            <input type="number" name="govt_scholarship_amount" class="form-control" value="{{ old('govt_scholarship_amount',0) }}" min="0" step="0.01">
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Applicable Semesters</label>
                        <div class="sem-grid" id="govt-sem-grid">
                            <div class="sem-btn all-sem" onclick="toggleAllSem('govt',this)">✓ All Semesters</div>
                            @for($s=1;$s<=8;$s++)
                            <div class="sem-btn" data-sem="{{ $s }}" data-group="govt" onclick="toggleSem(this,'govt')">Sem {{ $s }}</div>
                            @endfor
                        </div>
                        <div id="govt-sem-inputs"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Section 2: College Scholarship ── --}}
        <div class="toggle-section" id="college-section">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded d-flex align-items-center justify-content-center" style="width:36px;height:36px;background:rgba(217,119,6,.1);">
                        <i class="fa-solid fa-school" style="color:#d97706;font-size:.9rem;"></i>
                    </div>
                    <div>
                        <div style="font-weight:600;font-size:.9rem;">College Scholarship</div>
                        <div style="font-size:.78rem;color:var(--muted);">Merit, Sports, Alumni, Management scholarships</div>
                    </div>
                </div>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" id="college_scholarship_eligible" name="college_scholarship_eligible" value="1"
                           {{ old('college_scholarship_eligible')?'checked':'' }} onchange="toggleSection('college-body',this.checked)">
                    <label class="form-check-label" for="college_scholarship_eligible">Eligible</label>
                </div>
            </div>
            <div class="toggle-section-body" id="college-body" style="display:{{ old('college_scholarship_eligible')?'block':'none' }};">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Scholarship Name <span class="text-danger">*</span></label>
                        <input type="text" name="college_scholarship_name" class="form-control @error('college_scholarship_name') is-invalid @enderror"
                               value="{{ old('college_scholarship_name') }}" placeholder="e.g. Merit Scholarship, Sports Scholarship"
                               list="college-sch-list">
                        <datalist id="college-sch-list">
                            @foreach(\App\Models\StudentScholarship::COLLEGE_NAMES as $n)<option value="{{ $n }}">@endforeach
                        </datalist>
                        @error('college_scholarship_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Scholarship Amount (₹)</label>
                        <div class="input-group"><span class="input-group-text">₹</span>
                            <input type="number" name="college_scholarship_amount" class="form-control" value="{{ old('college_scholarship_amount',0) }}" min="0" step="0.01">
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Applicable Semesters</label>
                        <div class="sem-grid" id="college-sem-grid">
                            <div class="sem-btn all-sem" onclick="toggleAllSem('college',this)">✓ All Semesters</div>
                            @for($s=1;$s<=8;$s++)
                            <div class="sem-btn" data-sem="{{ $s }}" data-group="college" onclick="toggleSem(this,'college')">Sem {{ $s }}</div>
                            @endfor
                        </div>
                        <div id="college-sem-inputs"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Section 3: Transport Benefits (shown if vehicle_opted) ── --}}
        <div class="toggle-section" id="transport-section">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded d-flex align-items-center justify-content-center" style="width:36px;height:36px;background:rgba(8,145,178,.1);">
                        <i class="fa-solid fa-bus" style="color:#0891b2;font-size:.9rem;"></i>
                    </div>
                    <div>
                        <div style="font-weight:600;font-size:.9rem;">Transport Details</div>
                        <div style="font-size:.78rem;color:var(--muted);" id="transport-hint">
                            Enable "College Vehicle" in Step 3 to configure transport details
                        </div>
                    </div>
                </div>
                <span id="transport-status-badge" class="badge" style="background:rgba(107,114,128,.1);color:var(--muted);">Not Enabled</span>
            </div>
            <div class="toggle-section-body" id="transport-body" style="display:none;">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Route</label>
                        <input type="text" name="transport_route" class="form-control" value="{{ old('transport_route') }}" placeholder="e.g. City Center → College"></div>
                    <div class="col-md-6"><label class="form-label">Boarding Point</label>
                        <input type="text" name="transport_boarding" class="form-control" value="{{ old('transport_boarding') }}" placeholder="e.g. Bus Stop Name"></div>
                    <div class="col-md-6">
                        <label class="form-label">Transport Fee (₹/semester)</label>
                        <div class="input-group"><span class="input-group-text">₹</span>
                            <input type="number" name="transport_fee" class="form-control" value="{{ old('transport_fee',0) }}" min="0" step="0.01">
                        </div>
                    </div>
                    <div class="col-md-6"><label class="form-label">Notes</label>
                        <input type="text" name="transport_notes" class="form-control" value="{{ old('transport_notes') }}" placeholder="Any additional notes"></div>
                    <div class="col-12">
                        <label class="form-label">Applicable Semesters</label>
                        <div class="sem-grid" id="transport-sem-grid">
                            <div class="sem-btn all-sem" onclick="toggleAllSem('transport',this)">✓ All Semesters</div>
                            @for($s=1;$s<=8;$s++)
                            <div class="sem-btn" data-sem="{{ $s }}" data-group="transport" onclick="toggleSem(this,'transport')">Sem {{ $s }}</div>
                            @endfor
                        </div>
                        <div id="transport-sem-inputs"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<div class="d-flex justify-content-between">
    <button type="button" class="btn btn-outline-secondary px-4" onclick="prevStep(5)"><i class="fa-solid fa-arrow-left me-2"></i>Previous</button>
    <button type="button" class="btn btn-primary px-5" onclick="nextStep(5)">Next: Documents <i class="fa-solid fa-arrow-right ms-2"></i></button>
</div>
</div>

{{-- ════ STEP 6: Documents ════ --}}
<div class="step-panel" id="step-6">
<div class="card mb-4">
    <div class="card-header">
        <span class="rounded-circle d-inline-flex align-items-center justify-content-center me-2" style="width:24px;height:24px;background:rgba(8,145,178,.1);font-size:.72rem;font-weight:700;color:#0891b2;">6</span>
        Certificate Uploads
        <span class="badge ms-2" style="background:rgba(8,145,178,.1);color:#0891b2;font-size:.7rem;">Optional</span>
    </div>
    <div class="card-body">
        <p style="color:var(--muted);font-size:.875rem;" class="mb-3">Upload student certificates. Accepted: JPG, PNG, PDF. Max 5MB each. You can also upload later from the student profile.</p>
        <div class="row g-2 mb-4">
            @foreach($certificateTypes as $type => $label)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="d-flex align-items-center gap-2 p-2 rounded" style="border:1px solid var(--border);cursor:pointer;transition:all .2s;" id="cert-check-{{ $type }}" onclick="triggerCertUpload('{{ $type }}','{{ $label }}')">
                    <i class="fa-solid fa-circle-xmark" id="cert-icon-{{ $type }}" style="color:#9ca3af;font-size:.9rem;flex-shrink:0;"></i>
                    <span style="font-size:.78rem;">{{ $label }}</span>
                </div>
            </div>@endforeach
        </div>
        <div class="cert-upload-zone" id="cert-drop-zone" onclick="document.getElementById('cert-files-input').click()">
            <i class="fa-solid fa-cloud-arrow-up" style="font-size:2rem;color:#4f46e5;opacity:.6;"></i>
            <div class="mt-2" style="font-weight:500;color:var(--text);">Click to upload or drag &amp; drop</div>
            <div style="font-size:.8rem;color:var(--muted);margin-top:.25rem;">JPG, PNG, PDF -- up to 5MB each</div>
        </div>
        <input type="file" id="cert-files-input" name="certificates[]" accept=".jpg,.jpeg,.png,.pdf" multiple class="d-none" onchange="handleCertFiles(this.files)">
        <div id="cert-type-inputs"></div>
        <div id="cert-files-list" class="mt-3"></div>
    </div>
</div>
<div class="d-flex justify-content-between">
    <button type="button" class="btn btn-outline-secondary px-4" onclick="prevStep(6)"><i class="fa-solid fa-arrow-left me-2"></i>Previous</button>
    <button type="button" class="btn btn-primary px-5" onclick="nextStep(6)">Next: Fee &amp; Receipt <i class="fa-solid fa-arrow-right ms-2"></i></button>
</div>
</div>

{{-- ════ STEP 7: Receipt ════ --}}
<div class="step-panel" id="step-7">
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card mb-4">
            <div class="card-header"><span class="rounded-circle d-inline-flex align-items-center justify-content-center me-2" style="width:24px;height:24px;background:rgba(5,150,105,.1);font-size:.72rem;font-weight:700;color:#059669;">7</span>Admission Fee Collection</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Admission Fee (₹)</label>
                        <div class="input-group"><span class="input-group-text">₹</span>
                            <input type="number" name="admission_fee" id="admission_fee" class="form-control" value="{{ old('admission_fee',0) }}" min="0" step="0.01" oninput="updateFeeTotal()">
                        </div></div>
                    <div class="col-md-6"><label class="form-label">Tuition Fee (₹)</label>
                        <div class="input-group"><span class="input-group-text">₹</span>
                            <input type="number" name="tuition_fee" id="tuition_fee" class="form-control" value="{{ old('tuition_fee',0) }}" min="0" step="0.01" oninput="updateFeeTotal()">
                        </div></div>
                    <div class="col-md-6"><label class="form-label">Other Fees (₹)</label>
                        <div class="input-group"><span class="input-group-text">₹</span>
                            <input type="number" name="other_fees" id="other_fees" class="form-control" value="{{ old('other_fees',0) }}" min="0" step="0.01" oninput="updateFeeTotal()">
                        </div></div>
                    <div class="col-md-6"><label class="form-label">Amount Paid (₹)</label>
                        <div class="input-group"><span class="input-group-text">₹</span>
                            <input type="number" name="amount_paid" id="amount_paid" class="form-control" value="{{ old('amount_paid',0) }}" min="0" step="0.01" oninput="updateFeeTotal()">
                        </div></div>
                    <div class="col-md-6"><label class="form-label">Payment Mode</label>
                        <select name="payment_mode" class="form-select">
                            @foreach(['cash'=>'Cash','upi'=>'UPI','online'=>'Online Transfer','cheque'=>'Cheque','dd'=>'Demand Draft'] as $v=>$l)
                            <option value="{{ $v }}" {{ old('payment_mode','cash')===$v?'selected':'' }}>{{ $l }}</option>@endforeach
                        </select></div>
                    <div class="col-md-6"><label class="form-label">Transaction Reference</label>
                        <input type="text" name="transaction_reference" class="form-control" value="{{ old('transaction_reference') }}" placeholder="UTR / Cheque No. (optional)"></div>
                    <div class="col-md-6"><label class="form-label">Payment Date</label>
                        <input type="date" name="payment_date" class="form-control" value="{{ old('payment_date',now()->format('Y-m-d')) }}"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card" style="position:sticky;top:80px;">
            <div class="card-header"><i class="fa-solid fa-calculator me-2" style="color:#4f46e5;"></i>Fee Summary</div>
            <div class="card-body">
                <div class="fee-summary-row"><span style="color:var(--muted);">Admission Fee</span><span id="sum-admission">₹0</span></div>
                <div class="fee-summary-row"><span style="color:var(--muted);">Tuition Fee</span><span id="sum-tuition">₹0</span></div>
                <div class="fee-summary-row"><span style="color:var(--muted);">Other Fees</span><span id="sum-other">₹0</span></div>
                <div class="fee-summary-row" style="font-weight:600;"><span>Total Amount</span><span id="sum-total" style="color:#4f46e5;">₹0</span></div>
                <div class="fee-summary-row" style="color:#059669;font-weight:600;"><span>Amount Paid</span><span id="sum-paid">₹0</span></div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:.75rem 0 0;font-weight:700;font-size:1rem;color:#4f46e5;">
                    <span>Balance Due</span><span id="sum-balance" style="color:#dc2626;">₹0</span>
                </div>
                <div class="mt-3 p-3 rounded text-center" id="payment-status-badge" style="background:rgba(5,150,105,.08);border:1px solid rgba(5,150,105,.2);">
                    <span id="payment-status-text" style="font-size:.85rem;font-weight:600;color:#059669;"><i class="fa-solid fa-circle-check me-1"></i>Fully Paid</span>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="d-flex justify-content-between mt-2">
    <button type="button" class="btn btn-outline-secondary px-4" onclick="prevStep(7)"><i class="fa-solid fa-arrow-left me-2"></i>Previous</button>
    <button type="submit" class="btn btn-primary px-5" id="submit-btn"><i class="fa-solid fa-user-check me-2"></i>Complete Admission</button>
</div>
</div>

</form>
@endsection

@push('scripts')
<script>
'use strict';
const TOTAL = 7;
let currentStep = 1;

/* ── Wizard Navigation ─────────────────────────────────────────────── */
window.goToStep = function(step){
    if(step<1||step>TOTAL) return;
    document.querySelectorAll('.step-panel').forEach(p=>p.classList.remove('active'));
    document.getElementById('step-'+step).classList.add('active');
    document.querySelectorAll('.wizard-step').forEach(function(el){
        const s=parseInt(el.dataset.step);
        el.classList.remove('active','completed');
        if(s===step) el.classList.add('active');
        else if(s<step) el.classList.add('completed');
    });
    currentStep=step;
    window.scrollTo({top:0,behavior:'smooth'});
    if(step===5) syncTransportSection();
};
window.nextStep=function(from){ if(validateStep(from)) goToStep(from+1); };
window.prevStep=function(from){ goToStep(from-1); };

/* ── Validation ────────────────────────────────────────────────────── */
function validateStep(step){
    let valid=true;
    const panel=document.getElementById('step-'+step);
    const required=step===1?['first_name','last_name','gender','phone']:
                   step===2?['branch_id','academic_year_id']:
                   step===3?['admission_date']:[];
    required.forEach(function(name){
        const el=panel.querySelector('[name="'+name+'"]');
        if(el&&!el.value.trim()){el.classList.add('is-invalid');valid=false;}
        else if(el) el.classList.remove('is-invalid');
    });
    if(!valid) Swal.fire({icon:'warning',title:'Required Fields',text:'Please fill in all required fields.',confirmButtonColor:'#4f46e5',timer:3000,timerProgressBar:true});
    return valid;
}

/* ── Photo Preview ─────────────────────────────────────────────────── */
window.previewPhoto=function(input){
    if(input.files&&input.files[0]){
        const r=new FileReader();
        r.onload=e=>document.getElementById('photo-preview').src=e.target.result;
        r.readAsDataURL(input.files[0]);
    }
};

/* ── Category Highlight ────────────────────────────────────────────── */
window.highlightCategory=function(val){
    document.querySelectorAll('[id^="cat-label-"]').forEach(el=>{el.style.borderColor='var(--border)';el.style.background='transparent';});
    const a=document.getElementById('cat-label-'+val);
    if(a){a.style.borderColor='#4f46e5';a.style.background='rgba(79,70,229,.06)';}
};
const checkedCat=document.querySelector('[name="category"]:checked');
if(checkedCat) highlightCategory(checkedCat.value);

/* ── Vehicle Toggle ────────────────────────────────────────────────── */
window.toggleVehicleDate=function(show){
    document.getElementById('vehicle-date-wrap').style.display=show?'flex':'none';
    syncTransportSection();
};
document.getElementById('vehicle_opted')?.addEventListener('change',function(){syncTransportSection();});

function syncTransportSection(){
    const opted=document.getElementById('vehicle_opted')?.checked;
    const body=document.getElementById('transport-body');
    const hint=document.getElementById('transport-hint');
    const badge=document.getElementById('transport-status-badge');
    if(opted){
        body.style.display='block';
        hint.textContent='Configure route, boarding point and transport fee';
        badge.style.background='rgba(8,145,178,.1)';badge.style.color='#0891b2';badge.textContent='Enabled';
    } else {
        body.style.display='none';
        hint.textContent='Enable "College Vehicle" in Step 3 to configure transport details';
        badge.style.background='rgba(107,114,128,.1)';badge.style.color='var(--muted)';badge.textContent='Not Enabled';
    }
}

/* ── Toggle Section (scholarship panels) ──────────────────────────── */
window.toggleSection=function(id,show){
    document.getElementById(id).style.display=show?'block':'none';
};

/* ── Semester Selector ─────────────────────────────────────────────── */
window.toggleSem=function(btn,group){
    btn.classList.toggle('selected');
    updateSemInputs(group);
};
window.toggleAllSem=function(group,btn){
    const isAll=btn.classList.toggle('selected');
    const grid=document.getElementById(group+'-sem-grid');
    grid.querySelectorAll('.sem-btn:not(.all-sem)').forEach(b=>{
        if(isAll) b.classList.add('selected');
        else b.classList.remove('selected');
    });
    updateSemInputs(group);
};
function updateSemInputs(group){
    const container=document.getElementById(group+'-sem-inputs');
    container.innerHTML='';
    const grid=document.getElementById(group+'-sem-grid');
    grid.querySelectorAll('.sem-btn.selected:not(.all-sem)').forEach(function(b){
        const inp=document.createElement('input');
        inp.type='hidden';
        inp.name=group+'_scholarship_semesters[]';
        if(group==='transport') inp.name='transport_semesters[]';
        inp.value=b.dataset.sem;
        container.appendChild(inp);
    });
}

/* ── Certificate Upload ────────────────────────────────────────────── */
let _pendingType=null,_pendingLabel=null;
window.triggerCertUpload=function(type,label){_pendingType=type;_pendingLabel=label;document.getElementById('cert-files-input').click();};
window.handleCertFiles=function(files){
    const typeContainer=document.getElementById('cert-type-inputs');
    const listContainer=document.getElementById('cert-files-list');
    Array.from(files).forEach(function(file){
        const ct=_pendingType||'other',cl=_pendingLabel||'Other Document';
        const icon=document.getElementById('cert-icon-'+ct);
        if(icon){icon.className='fa-solid fa-circle-check';icon.style.color='#059669';}
        const hi=document.createElement('input');hi.type='hidden';hi.name='certificate_types[]';hi.value=ct;typeContainer.appendChild(hi);
        const item=document.createElement('div');item.className='cert-item';
        item.innerHTML=`<div style="width:32px;height:32px;border-radius:.375rem;background:rgba(79,70,229,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fa-solid fa-file-${file.type==='application/pdf'?'pdf':'image'}" style="color:#4f46e5;font-size:.85rem;"></i></div><div class="flex-1 overflow-hidden"><div style="font-size:.82rem;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${file.name}</div><div style="font-size:.72rem;color:var(--muted);">${cl} &bull; ${(file.size/1024).toFixed(1)} KB</div></div><span class="badge" style="background:#dcfce7;color:#166534;font-size:.7rem;">Ready</span>`;
        listContainer.appendChild(item);
    });
    _pendingType=null;_pendingLabel=null;
};
const dz=document.getElementById('cert-drop-zone');
if(dz){
    dz.addEventListener('dragover',e=>{e.preventDefault();dz.classList.add('dragover');});
    dz.addEventListener('dragleave',()=>dz.classList.remove('dragover'));
    dz.addEventListener('drop',function(e){e.preventDefault();dz.classList.remove('dragover');_pendingType='other';_pendingLabel='Other Document';handleCertFiles(e.dataTransfer.files);});
}

/* ── Fee Calculator ────────────────────────────────────────────────── */
window.updateFeeTotal=function(){
    const a=parseFloat(document.getElementById('admission_fee').value)||0;
    const t=parseFloat(document.getElementById('tuition_fee').value)||0;
    const o=parseFloat(document.getElementById('other_fees').value)||0;
    const p=parseFloat(document.getElementById('amount_paid').value)||0;
    const total=a+t+o,balance=Math.max(0,total-p);
    const fmt=v=>'₹'+v.toLocaleString('en-IN');
    document.getElementById('sum-admission').textContent=fmt(a);
    document.getElementById('sum-tuition').textContent=fmt(t);
    document.getElementById('sum-other').textContent=fmt(o);
    document.getElementById('sum-total').textContent=fmt(total);
    document.getElementById('sum-paid').textContent=fmt(p);
    document.getElementById('sum-balance').textContent=fmt(balance);
    const badge=document.getElementById('payment-status-badge'),text=document.getElementById('payment-status-text');
    if(total===0||p>=total){badge.style.background='rgba(5,150,105,.08)';badge.style.borderColor='rgba(5,150,105,.2)';text.style.color='#059669';text.innerHTML='<i class="fa-solid fa-circle-check me-1"></i>Fully Paid';}
    else if(p>0){badge.style.background='rgba(217,119,6,.08)';badge.style.borderColor='rgba(217,119,6,.2)';text.style.color='#d97706';text.innerHTML='<i class="fa-solid fa-circle-half-stroke me-1"></i>Partial Payment';}
    else{badge.style.background='rgba(220,38,38,.08)';badge.style.borderColor='rgba(220,38,38,.2)';text.style.color='#dc2626';text.innerHTML='<i class="fa-solid fa-circle-xmark me-1"></i>Pending';}
};
document.getElementById('branch_id')?.addEventListener('change',function(){
    const opt=this.options[this.selectedIndex];
    const tuition=opt.dataset.tuition||0;
    document.getElementById('tuition_fee').value=tuition;
    document.getElementById('amount_paid').value=tuition;
    updateFeeTotal();
});
updateFeeTotal();

/* ── Submit ────────────────────────────────────────────────────────── */
document.getElementById('admission-form').addEventListener('submit',function(){
    const btn=document.getElementById('submit-btn');
    btn.disabled=true;btn.innerHTML='<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
});

/* ── Jump to error step on validation failure ──────────────────────── */
const errorMeta = document.getElementById('admission-error-fields');
if (errorMeta) {
    const errorFields = JSON.parse(errorMeta.dataset.fields || '[]');
    const stepFields = {
        1: ['first_name','last_name','gender','phone','email','date_of_birth','blood_group','aadhaar_number','address','city','state','pincode','photo'],
        2: ['branch_id','academic_year_id','marks_10th','marks_12th','previous_institution','current_semester'],
        3: ['category','admission_date','vehicle_opted','vehicle_start_date'],
        4: ['father_name','father_occupation','father_phone','mother_name','annual_income'],
        5: ['govt_scholarship_type','college_scholarship_name','govt_scholarship_amount','college_scholarship_amount']
    };
    let targetStep = 7;
    for (let s = 1; s <= 5; s++) {
        if (errorFields.some(function(f){ return (stepFields[s] || []).indexOf(f) !== -1; })) {
            targetStep = s;
            break;
        }
    }
    goToStep(targetStep);
}
</script>
@endpush
