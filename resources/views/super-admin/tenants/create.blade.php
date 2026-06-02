@extends('layouts.super-admin-app')
@section('title', 'Add Institution')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('super.tenants.index') }}" style="color:var(--blue);">Institutions</a></li>
    <li class="breadcrumb-item active" style="color:var(--muted);">Add New</li>
@endsection
@push('styles')
<style>
.wizard-steps{display:flex;align-items:center;gap:0;margin-bottom:32px;}
.wizard-step{display:flex;align-items:center;gap:10px;flex:1;}
.wizard-step:last-child{flex:0;}
.step-circle{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;transition:all .2s;}
.step-circle.done{background:var(--green);color:#fff;}
.step-circle.active{background:var(--blue);color:#fff;box-shadow:0 0 0 4px rgba(37,99,235,.15);}
.step-circle.pending{background:var(--surface2);color:var(--muted);border:2px solid var(--border);}
.step-label{font-size:12px;font-weight:500;}
.step-label.active{color:var(--blue);}
.step-label.pending{color:var(--muted);}
.step-label.done{color:var(--green);}
.step-line{flex:1;height:2px;background:var(--border);margin:0 8px;}
.step-line.done{background:var(--green);}
.wizard-panel{display:none;}
.wizard-panel.active{display:block;}
.principal-email-highlight{
    background:linear-gradient(135deg,rgba(37,99,235,.06),rgba(79,70,229,.04));
    border:2px solid rgba(37,99,235,.25);
    border-radius:10px;
    padding:16px;
    margin-bottom:8px;
}
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
                <div class="step-label pending" id="step2-label">Principal &amp; Contact</div>
            </div>
            <div class="step-line" id="line2"></div>
            <div class="wizard-step">
                <div class="step-circle pending" id="step3-circle">3</div>
                <div class="step-label pending" id="step3-label">Review</div>
            </div>
        </div>

        <form method="POST" action="{{ route('super.tenants.store') }}" id="wizard-form">
            @csrf

            {{-- ── Step 1: Basic Info ── --}}
            <div class="wizard-panel active" id="panel1">
                <div class="card">
                    <div class="card-header"><span style="font-weight:700;">Basic Information</span></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label">Institution Name <span style="color:var(--red);">*</span></label>
                                <input type="text" name="name" id="inst-name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name') }}"
                                       placeholder="e.g. Sunrise Degree College" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Slug <span style="color:var(--red);">*</span></label>
                                <input type="text" name="slug" id="inst-slug"
                                       class="form-control @error('slug') is-invalid @enderror"
                                       value="{{ old('slug') }}"
                                       placeholder="e.g. sunrise-college" required>
                                <div class="form-hint">Auto-generated. Lowercase, no spaces.</div>
                                @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Affiliation Number</label>
                                <input type="text" name="affiliation_number" class="form-control"
                                       value="{{ old('affiliation_number') }}"
                                       placeholder="University affiliation code">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Website</label>
                                <input type="url" name="website" class="form-control"
                                       value="{{ old('website') }}"
                                       placeholder="https://college.edu">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Custom Domain</label>
                                <input type="text" name="domain" class="form-control"
                                       value="{{ old('domain') }}"
                                       placeholder="college.edu (optional)">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <button type="button" class="btn-primary" onclick="goStep(2)">
                        Next: Principal &amp; Contact <i class="fa-solid fa-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>

            {{-- ── Step 2: Principal & Contact ── --}}
            <div class="wizard-panel" id="panel2">
                <div class="card">
                    <div class="card-header"><span style="font-weight:700;">Principal &amp; Contact Details</span></div>
                    <div class="card-body">
                        <div class="row g-3">

                            {{-- Principal Email — MOST IMPORTANT FIELD --}}
                            <div class="col-12">
                                <div class="principal-email-highlight">
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <div style="width:32px;height:32px;border-radius:8px;background:var(--blue);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                            <i class="fa-solid fa-key" style="color:#fff;font-size:13px;"></i>
                                        </div>
                                        <div>
                                            <div style="font-weight:700;font-size:14px;color:var(--blue);">Principal Login Email</div>
                                            <div style="font-size:11px;color:var(--muted);">This email will be used to login to the College Admin dashboard</div>
                                        </div>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Principal Name <span style="color:var(--red);">*</span></label>
                                            <input type="text" name="principal_name"
                                                   class="form-control @error('principal_name') is-invalid @enderror"
                                                   value="{{ old('principal_name') }}"
                                                   placeholder="Dr. / Prof. Full Name" required>
                                            @error('principal_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">
                                                Principal Email <span style="color:var(--red);">*</span>
                                                <span class="badge ms-1" style="background:rgba(37,99,235,.1);color:var(--blue);font-size:10px;">Used for Login</span>
                                            </label>
                                            <input type="email" name="principal_email" id="principal-email"
                                                   class="form-control @error('principal_email') is-invalid @enderror"
                                                   value="{{ old('principal_email') }}"
                                                   placeholder="principal@gmail.com" required>
                                            <div class="form-hint" style="color:var(--blue);">
                                                <i class="fa-solid fa-circle-info me-1"></i>
                                                Login credentials will be sent to this email
                                            </div>
                                            @error('principal_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Institution Contact Email --}}
                            <div class="col-md-6">
                                <label class="form-label">
                                    Institution Contact Email <span style="color:var(--red);">*</span>
                                </label>
                                <input type="email" name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}"
                                       placeholder="info@college.edu" required>
                                <div class="form-hint">General institution contact email (different from principal email)</div>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone" class="form-control"
                                       value="{{ old('phone') }}"
                                       placeholder="+91 XXXXX XXXXX">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Street Address</label>
                                <textarea name="address" class="form-control" rows="2"
                                          placeholder="Street address...">{{ old('address') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">City</label>
                                <input type="text" name="city" class="form-control"
                                       value="{{ old('city') }}" placeholder="City">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">State</label>
                                <input type="text" name="state" class="form-control"
                                       value="{{ old('state') }}" placeholder="State">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Pincode</label>
                                <input type="text" name="pincode" class="form-control"
                                       value="{{ old('pincode') }}" placeholder="500001">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <button type="button" class="btn-secondary" onclick="goStep(1)">
                        <i class="fa-solid fa-arrow-left me-1"></i> Back
                    </button>
                    <button type="button" class="btn-primary" onclick="goStep(3)">
                        Next: Review <i class="fa-solid fa-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>

            {{-- ── Step 3: Review ── --}}
            <div class="wizard-panel" id="panel3">
                <div class="card">
                    <div class="card-header"><span style="font-weight:700;">Review &amp; Confirm</span></div>
                    <div class="card-body">
                        <div class="row g-3" style="font-size:13px;">
                            <div class="col-12">
                                <div class="p-3 rounded mb-3" style="background:var(--surface2);border:1px solid var(--border);">
                                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:12px;">Institution</div>
                                    <div class="row g-2">
                                        <div class="col-6"><span style="color:var(--muted);">Name:</span> <strong id="rev-name">—</strong></div>
                                        <div class="col-6"><span style="color:var(--muted);">Slug:</span> <code id="rev-slug">—</code></div>
                                    </div>
                                </div>
                                <div class="p-3 rounded" style="background:linear-gradient(135deg,rgba(37,99,235,.06),rgba(79,70,229,.04));border:2px solid rgba(37,99,235,.2);">
                                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--blue);margin-bottom:12px;">
                                        <i class="fa-solid fa-key me-1"></i> College Admin Credentials (will be sent via email)
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-6"><span style="color:var(--muted);">Principal:</span> <strong id="rev-principal">—</strong></div>
                                        <div class="col-6"><span style="color:var(--muted);">Login Email:</span> <strong id="rev-principal-email" style="color:var(--blue);">—</strong></div>
                                        <div class="col-12">
                                            <div class="mt-2 p-2 rounded" style="background:rgba(37,99,235,.08);font-size:12px;color:var(--blue);">
                                                <i class="fa-solid fa-envelope me-1"></i>
                                                A welcome email with login credentials will be sent to <strong id="rev-email-confirm">—</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <button type="button" class="btn-secondary" onclick="goStep(2)">
                        <i class="fa-solid fa-arrow-left me-1"></i> Back
                    </button>
                    <button type="submit" class="btn-primary" id="submit-btn">
                        <i class="fa-solid fa-building me-1"></i> Create Institution &amp; Send Credentials
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Help Panel --}}
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header">
                <span style="font-weight:700;font-size:13px;">What happens after creation?</span>
            </div>
            <div class="card-body p-0">
                @php $steps = [
                    ['icon'=>'fa-building','color'=>'var(--blue)','text'=>'Institution record created in the system'],
                    ['icon'=>'fa-user-shield','color'=>'var(--green)','text'=>'College Admin account auto-created'],
                    ['icon'=>'fa-envelope','color'=>'var(--purple)','text'=>'Welcome email sent to Principal Email with login credentials'],
                    ['icon'=>'fa-key','color'=>'var(--orange)','text'=>'Principal logs in and changes password on first login'],
                    ['icon'=>'fa-graduation-cap','color'=>'var(--blue)','text'=>'College Admin sets up courses, fees, students'],
                ]; @endphp
                @foreach($steps as $i => $s)
                <div class="d-flex align-items-start gap-3 px-4 py-3"
                     style="border-bottom:{{ $i < count($steps)-1 ? '1px solid var(--border)' : 'none' }};">
                    <div style="width:28px;height:28px;border-radius:50%;background:rgba(37,99,235,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
                        <i class="fa-solid {{ $s['icon'] }}" style="color:{{ $s['color'] }};font-size:11px;"></i>
                    </div>
                    <span style="font-size:12px;color:var(--text2);">{{ $s['text'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <div class="card">
            <div class="card-header"><span style="font-weight:700;font-size:13px;">Email Fields Explained</span></div>
            <div class="card-body" style="font-size:12px;color:var(--muted);line-height:2;">
                <div class="mb-2 p-2 rounded" style="background:rgba(37,99,235,.06);border:1px solid rgba(37,99,235,.15);">
                    <strong style="color:var(--blue);">Principal Email</strong><br>
                    The principal's personal email. Used as the College Admin login. Credentials sent here.
                </div>
                <div class="p-2 rounded" style="background:var(--surface2);border:1px solid var(--border);">
                    <strong style="color:var(--text);">Institution Contact Email</strong><br>
                    General college email (e.g. info@college.edu). Used for institution records only.
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
let currentStep = 1;

function goStep(n) {
    // Validate step 2 before proceeding to step 3
    if (n === 3) {
        const principalEmail = document.querySelector('[name="principal_email"]').value;
        const instEmail = document.querySelector('[name="email"]').value;
        if (!principalEmail) {
            Swal.fire({icon:'warning',title:'Required',text:'Please enter the Principal Email.',confirmButtonColor:'#2563EB'});
            return;
        }
        if (!instEmail) {
            Swal.fire({icon:'warning',title:'Required',text:'Please enter the Institution Contact Email.',confirmButtonColor:'#2563EB'});
            return;
        }
    }

    document.getElementById('panel'+currentStep).classList.remove('active');
    document.getElementById('panel'+n).classList.add('active');

    for (let i = 1; i <= 3; i++) {
        const c = document.getElementById('step'+i+'-circle');
        const l = document.getElementById('step'+i+'-label');
        if (i < n) {
            c.className = 'step-circle done';
            c.innerHTML = '<i class="fa-solid fa-check" style="font-size:10px;"></i>';
            l.className = 'step-label done';
        } else if (i === n) {
            c.className = 'step-circle active';
            c.textContent = i;
            l.className = 'step-label active';
        } else {
            c.className = 'step-circle pending';
            c.textContent = i;
            l.className = 'step-label pending';
        }
        if (i < 3) {
            document.getElementById('line'+i).className = 'step-line' + (i < n ? ' done' : '');
        }
    }

    if (n === 3) {
        const name = document.querySelector('[name="name"]').value || '—';
        const slug = document.querySelector('[name="slug"]').value || '—';
        const principal = document.querySelector('[name="principal_name"]').value || '—';
        const principalEmail = document.querySelector('[name="principal_email"]').value || '—';

        document.getElementById('rev-name').textContent = name;
        document.getElementById('rev-slug').textContent = slug;
        document.getElementById('rev-principal').textContent = principal;
        document.getElementById('rev-principal-email').textContent = principalEmail;
        document.getElementById('rev-email-confirm').textContent = principalEmail;
    }

    currentStep = n;
    window.scrollTo({top: 0, behavior: 'smooth'});
}

// Auto-generate slug from institution name
document.getElementById('inst-name')?.addEventListener('input', function () {
    const s = document.getElementById('inst-slug');
    if (!s.dataset.manual) {
        s.value = this.value.toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-|-$/g, '');
    }
});

document.getElementById('inst-slug')?.addEventListener('input', function () {
    this.dataset.manual = '1';
    this.value = this.value.toLowerCase().replace(/[^a-z0-9-]/g, '');
});

// Submit button loading state
document.getElementById('wizard-form')?.addEventListener('submit', function () {
    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" style="width:14px;height:14px;"></span>Creating Institution...';
});

// Jump to correct step if validation failed
@if($errors->has('name') || $errors->has('slug'))
    goStep(1);
@elseif($errors->has('email') || $errors->has('principal_email') || $errors->has('principal_name'))
    goStep(2);
@endif
</script>
@endpush
