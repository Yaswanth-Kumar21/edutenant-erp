@extends('layouts.super-admin-app')
@section('title', 'Add Institution')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('super.tenants.index') }}" style="color:var(--blue);">Institutions</a></li>
    <li class="breadcrumb-item active" style="color:var(--muted);">Add New</li>
@endsection
@push('styles')
<style>
.wizard-steps { display: flex; align-items: center; gap: 0; margin-bottom: 32px; }
.wizard-step { display: flex; align-items: center; gap: 10px; flex: 1; }
.wizard-step:last-child { flex: 0; }
.step-circle { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; flex-shrink: 0; transition: all .2s; }
.step-circle.done    { background: var(--green); color: #fff; }
.step-circle.active  { background: var(--blue); color: #fff; box-shadow: 0 0 0 4px rgba(37,99,235,.15); }
.step-circle.pending { background: var(--surface2); color: var(--muted); border: 2px solid var(--border); }
.step-label { font-size: 12px; font-weight: 500; }
.step-label.active  { color: var(--blue); }
.step-label.pending { color: var(--muted); }
.step-label.done    { color: var(--green); }
.step-line { flex: 1; height: 2px; background: var(--border); margin: 0 8px; }
.step-line.done { background: var(--green); }
.wizard-panel { display: none; }
.wizard-panel.active { display: block; }
</style>
@endpush
@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">Add Institution</h1>
        <p class="page-sub">Register a new college or institution on the EduTenant platform</p>
    </div>
    <a href="{{ route('super.tenants.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Back
    </a>
</div>

@if($errors->any())
<div class="alert alert-danger mb-4">
    <i class="fa-solid fa-circle-exclamation flex-shrink-0"></i>
    <div>
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-1 ps-3" style="font-size:12px;">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
</div>
@endif

<div class="row g-4">
    <div class="col-lg-8">
        {{-- Wizard Steps --}}
        <div class="wizard-steps">
            <div class="wizard-step">
                <div class="step-circle active" id="step1-circle">1</div>
                <div class="step-label active" id="step1-label">Basic Info</div>
            </div>
            <div class="step-line" id="line1"></div>
            <div class="wizard-step">
                <div class="step-circle pending" id="step2-circle">2</div>
                <div class="step-label pending" id="step2-label">Contact & Address</div>
            </div>
            <div class="step-line" id="line2"></div>
            <div class="wizard-step">
                <div class="step-circle pending" id="step3-circle">3</div>
                <div class="step-label pending" id="step3-label">Review</div>
            </div>
        </div>

        <form method="POST" action="{{ route('super.tenants.store') }}" id="wizard-form">
            @csrf

            {{-- Step 1: Basic Info --}}
            <div class="wizard-panel active" id="panel1">
                <div class="card">
                    <div class="card-header"><span style="font-weight:700;">Basic Information</span></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label">Institution Name <span style="color:var(--red);">*</span></label>
                                <input type="text" name="name" id="inst-name" class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name') }}" placeholder="e.g. Sri Venkateswara Degree College" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Slug <span style="color:var(--red);">*</span></label>
                                <input type="text" name="slug" id="inst-slug" class="form-control @error('slug') is-invalid @enderror"
                                       value="{{ old('slug') }}" placeholder="e.g. svc" required>
                                <div class="form-hint">Unique ID. Lowercase, no spaces.</div>
                                @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Principal Name</label>
                                <input type="text" name="principal_name" class="form-control" value="{{ old('principal_name') }}" placeholder="Dr. / Prof. Name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Affiliation Number</label>
                                <input type="text" name="affiliation_number" class="form-control" value="{{ old('affiliation_number') }}" placeholder="University affiliation code">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Website</label>
                                <input type="url" name="website" class="form-control" value="{{ old('website') }}" placeholder="https://college.edu">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Custom Domain</label>
                                <input type="text" name="domain" class="form-control" value="{{ old('domain') }}" placeholder="college.edu (optional)">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <button type="button" class="btn-primary" onclick="goStep(2)">Next: Contact & Address <i class="fa-solid fa-arrow-right ms-1"></i></button>
                </div>
            </div>

            {{-- Step 2: Contact & Address --}}
            <div class="wizard-panel" id="panel2">
                <div class="card">
                    <div class="card-header"><span style="font-weight:700;">Contact & Address</span></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Email Address <span style="color:var(--red);">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}" placeholder="admin@college.edu" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="+91 XXXXX XXXXX">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Street Address</label>
                                <textarea name="address" class="form-control" rows="2" placeholder="Street address...">{{ old('address') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">City</label>
                                <input type="text" name="city" class="form-control" value="{{ old('city') }}" placeholder="City">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">State</label>
                                <input type="text" name="state" class="form-control" value="{{ old('state') }}" placeholder="State">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Pincode</label>
                                <input type="text" name="pincode" class="form-control" value="{{ old('pincode') }}" placeholder="500001">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <button type="button" class="btn-secondary" onclick="goStep(1)"><i class="fa-solid fa-arrow-left me-1"></i> Back</button>
                    <button type="button" class="btn-primary" onclick="goStep(3)">Next: Review <i class="fa-solid fa-arrow-right ms-1"></i></button>
                </div>
            </div>

            {{-- Step 3: Review --}}
            <div class="wizard-panel" id="panel3">
                <div class="card">
                    <div class="card-header"><span style="font-weight:700;">Review & Confirm</span></div>
                    <div class="card-body">
                        <div class="row g-3" style="font-size:13px;">
                            <div class="col-12">
                                <div class="p-3 rounded" style="background:var(--surface2);border:1px solid var(--border);">
                                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:12px;">Basic Information</div>
                                    <div class="row g-2">
                                        <div class="col-6"><span style="color:var(--muted);">Name:</span> <strong id="rev-name">—</strong></div>
                                        <div class="col-6"><span style="color:var(--muted);">Slug:</span> <code id="rev-slug">—</code></div>
                                        <div class="col-6"><span style="color:var(--muted);">Email:</span> <strong id="rev-email">—</strong></div>
                                        <div class="col-6"><span style="color:var(--muted);">Phone:</span> <strong id="rev-phone">—</strong></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 p-3 rounded" style="background:rgba(37,99,235,.06);border:1px solid rgba(37,99,235,.15);">
                            <div class="d-flex gap-2 align-items-start" style="font-size:12px;color:var(--blue);">
                                <i class="fa-solid fa-circle-info mt-1 flex-shrink-0"></i>
                                <div>After creating the institution, you can add college admin users, configure streams, courses, branches, and academic years from the institution's admin panel.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <button type="button" class="btn-secondary" onclick="goStep(2)"><i class="fa-solid fa-arrow-left me-1"></i> Back</button>
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-building me-1"></i> Create Institution
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Help Panel --}}
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header">
                <span style="font-weight:700;font-size:13px;">Onboarding Checklist</span>
                <span style="font-size:11px;color:var(--muted);">1/5 complete</span>
            </div>
            <div class="card-body p-0">
                @php
                $onboardSteps = [
                    ['done'=>true,  'label'=>'Create institution',  'sub'=>'Fill in basic details — you are here'],
                    ['done'=>false, 'label'=>'Add college admin',   'sub'=>'Create admin user account'],
                    ['done'=>false, 'label'=>'Configure academics', 'sub'=>'Streams, courses, branches, years'],
                    ['done'=>false, 'label'=>'Set up fee types',    'sub'=>'Define fee structures'],
                    ['done'=>false, 'label'=>'Add students',        'sub'=>'Begin admissions'],
                ];
                @endphp
                {{-- Progress bar --}}
                <div class="px-4 pt-3 pb-2">
                    <div style="height:5px;background:var(--border);border-radius:3px;overflow:hidden;">
                        <div style="height:100%;width:20%;background:var(--green);border-radius:3px;"></div>
                    </div>
                    <div style="font-size:11px;color:var(--muted);margin-top:4px;">20% complete — 4 steps remaining after creation</div>
                </div>
                @foreach($onboardSteps as $i => $s)
                <div class="d-flex align-items-start gap-3 px-4 py-3" style="border-top:1px solid var(--border);">
                    <div style="width:22px;height:22px;border-radius:50%;flex-shrink:0;margin-top:1px;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;
                        background:{{ $s['done'] ? 'var(--green)' : 'var(--surface2, #F1F5F9)' }};
                        color:{{ $s['done'] ? '#fff' : 'var(--muted)' }};
                        border:{{ $s['done'] ? 'none' : '2px solid var(--border)' }};">
                        @if($s['done'])<i class="fa-solid fa-check" style="font-size:9px;"></i>@else{{ $i+1 }}@endif
                    </div>
                    <div>
                        <div style="font-size:12px;font-weight:600;color:{{ $s['done'] ? 'var(--green)' : 'var(--text)' }};">
                            {{ $s['label'] }}
                            @if($s['done'])<i class="fa-solid fa-circle-check ms-1" style="font-size:10px;color:var(--green);"></i>@endif
                        </div>
                        <div style="font-size:11px;color:var(--muted);">{{ $s['sub'] }}</div>
                    </div>
                </div>
                @endforeach
                <div class="px-4 py-3" style="border-top:1px solid var(--border);background:rgba(37,99,235,.04);">
                    <div style="font-size:11px;color:var(--blue);display:flex;align-items:flex-start;gap:6px;">
                        <i class="fa-solid fa-circle-info mt-1 flex-shrink-0"></i>
                        <div>After creating the institution, you'll be redirected to the institution page where you can track real-time onboarding progress.</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><span style="font-weight:700;font-size:13px;">Slug Guidelines</span></div>
            <div class="card-body" style="font-size:12px;color:var(--muted);">
                <ul class="ps-3 mb-0" style="line-height:2.2;">
                    <li>Lowercase letters and numbers only</li>
                    <li>Use hyphens instead of spaces</li>
                    <li>Must be unique across the platform</li>
                    <li>Cannot be changed after creation</li>
                    <li>Example: <code>svc-tirupati</code></li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
let currentStep = 1;
function goStep(n) {
    document.getElementById('panel'+currentStep).classList.remove('active');
    document.getElementById('panel'+n).classList.add('active');
    // Update circles
    for(let i=1;i<=3;i++){
        const c=document.getElementById('step'+i+'-circle'),l=document.getElementById('step'+i+'-label');
        if(i<n){c.className='step-circle done';c.innerHTML='<i class="fa-solid fa-check" style="font-size:10px;"></i>';l.className='step-label done';}
        else if(i===n){c.className='step-circle active';c.textContent=i;l.className='step-label active';}
        else{c.className='step-circle pending';c.textContent=i;l.className='step-label pending';}
        if(i<3){const line=document.getElementById('line'+i);line.className='step-line'+(i<n?' done':'');}
    }
    if(n===3){
        document.getElementById('rev-name').textContent=document.querySelector('[name="name"]').value||'—';
        document.getElementById('rev-slug').textContent=document.querySelector('[name="slug"]').value||'—';
        document.getElementById('rev-email').textContent=document.querySelector('[name="email"]').value||'—';
        document.getElementById('rev-phone').textContent=document.querySelector('[name="phone"]').value||'—';
    }
    currentStep=n;
    window.scrollTo({top:0,behavior:'smooth'});
}
// Auto-generate slug
document.getElementById('inst-name')?.addEventListener('input',function(){
    const s=document.getElementById('inst-slug');
    if(!s.dataset.manual)s.value=this.value.toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/^-|-$/g,'');
});
document.getElementById('inst-slug')?.addEventListener('input',function(){
    this.dataset.manual='1';
    this.value=this.value.toLowerCase().replace(/[^a-z0-9-]/g,'');
});
// If errors, go to correct step
@if($errors->has('name') || $errors->has('slug'))goStep(1);@elseif($errors->has('email'))goStep(2);@endif
</script>
@endpush
