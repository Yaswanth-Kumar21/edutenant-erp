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
.principal-email-box{
    background:linear-gradient(135deg,rgba(37,99,235,.07),rgba(79,70,229,.05));
    border:2px solid rgba(37,99,235,.35);
    border-radius:12px;
    padding:20px;
    margin-bottom:4px;
    position:relative;
}
.principal-email-box::before{
    content:'CREDENTIALS DELIVERED HERE';
    position:absolute;top:-10px;left:16px;
    background:var(--blue);color:#fff;
    font-size:9px;font-weight:800;letter-spacing:.1em;
    padding:2px 10px;border-radius:20px;
}
.field-email-live{
    border:2px solid rgba(37,99,235,.4) !important;
    background:rgba(37,99,235,.03) !important;
    font-weight:600 !important;
    font-size:15px !important;
}
.field-email-live:focus{
    border-color:var(--blue) !important;
    box-shadow:0 0 0 3px rgba(37,99,235,.15) !important;
}
.email-diff-warning{
    display:none;
    background:rgba(217,119,6,.08);
    border:1px solid rgba(217,119,6,.3);
    border-radius:8px;
    padding:10px 14px;
    font-size:12px;
    color:#92400e;
    margin-top:8px;
}
.review-email-box{
    background:linear-gradient(135deg,rgba(37,99,235,.08),rgba(79,70,229,.05));
    border:2px solid rgba(37,99,235,.3);
    border-radius:10px;
    padding:18px;
    margin-top:12px;
}
.review-email-value{
    font-size:16px;font-weight:800;color:var(--blue);
    font-family:monospace;
    background:rgba(37,99,235,.08);
    border:1px solid rgba(37,99,235,.2);
    border-radius:6px;
    padding:8px 14px;
    display:inline-block;
    margin-top:6px;
    word-break:break-all;
}
.confirm-checkbox-wrap{
    background:rgba(5,150,105,.06);
    border:1px solid rgba(5,150,105,.25);
    border-radius:8px;
    padding:14px 16px;
    margin-top:14px;
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
<span id="create-form-error-fields" data-fields="{{ json_encode($errors->keys()) }}" class="d-none"></span>
@endif

<div class="row g-4">
<div class="col-lg-8">

{{-- Wizard Steps --}}
<div class="wizard-steps">
    <div class="wizard-step"><div class="step-circle active" id="step1-circle">1</div><div class="step-label active" id="step1-label">Basic Info</div></div>
    <div class="step-line" id="line1"></div>
    <div class="wizard-step"><div class="step-circle pending" id="step2-circle">2</div><div class="step-label pending" id="step2-label">Principal &amp; Contact</div></div>
    <div class="step-line" id="line2"></div>
    <div class="wizard-step"><div class="step-circle pending" id="step3-circle">3</div><div class="step-label pending" id="step3-label">Review &amp; Confirm</div></div>
</div>

<form method="POST" action="{{ route('super.tenants.store') }}" id="wizard-form">
@csrf

{{-- ════ STEP 1: Basic Info ════ --}}
<div class="wizard-panel active" id="panel1">
<div class="card">
    <div class="card-header"><span style="font-weight:700;">Basic Information</span></div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label">Institution Name <span style="color:var(--red);">*</span></label>
                <input type="text" name="name" id="inst-name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" placeholder="e.g. Sunrise Degree College" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Slug <span style="color:var(--red);">*</span></label>
                <input type="text" name="slug" id="inst-slug"
                       class="form-control @error('slug') is-invalid @enderror"
                       value="{{ old('slug') }}" placeholder="sunrise-college" required>
                <div class="form-hint">Auto-generated. Lowercase, hyphens only.</div>
                @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
    <button type="button" class="btn-primary" onclick="goStep(2)">Next: Principal &amp; Contact <i class="fa-solid fa-arrow-right ms-1"></i></button>
</div>
</div>

{{-- ════ STEP 2: Principal & Contact ════ --}}
<div class="wizard-panel" id="panel2">
<div class="card">
    <div class="card-header"><span style="font-weight:700;">Principal &amp; Contact Details</span></div>
    <div class="card-body">
        <div class="row g-3">

            {{-- ── PRINCIPAL EMAIL — primary delivery destination ── --}}
            <div class="col-12">
                <div class="principal-email-box">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div style="width:40px;height:40px;border-radius:10px;background:var(--blue);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fa-solid fa-envelope-circle-check" style="color:#fff;font-size:16px;"></i>
                        </div>
                        <div>
                            <div style="font-weight:800;font-size:15px;color:var(--blue);">
                                Principal Email
                                <span style="font-size:11px;font-weight:600;background:var(--blue);color:#fff;padding:2px 8px;border-radius:20px;margin-left:6px;">Login Credentials Sent Here</span>
                            </div>
                            <div style="font-size:12px;color:var(--muted);margin-top:2px;">
                                The College Admin welcome email, login email, temporary password, and onboarding instructions will be delivered to this address.
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Principal Name <span style="color:var(--red);">*</span></label>
                            <input type="text" name="principal_name" id="principal-name"
                                   class="form-control @error('principal_name') is-invalid @enderror"
                                   value="{{ old('principal_name') }}" placeholder="Dr. / Prof. Full Name" required>
                            @error('principal_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight:700;color:var(--blue);">
                                Principal Email — College Admin Login Credentials Will Be Sent Here
                                <span style="color:var(--red);">*</span>
                            </label>
                            <div class="d-flex gap-2 align-items-start">
                                <div class="flex-1">
                                    <input type="email" name="principal_email" id="principal-email"
                                           class="form-control field-email-live @error('principal_email') is-invalid @enderror"
                                           value="{{ old('principal_email') }}"
                                           placeholder="principal@gmail.com"
                                           required oninput="onPrincipalEmailChange(this.value)">
                                    @error('principal_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <button type="button" class="btn-secondary flex-shrink-0" id="test-email-btn"
                                        onclick="sendTestEmail()"
                                        style="padding:8px 12px;font-size:12px;white-space:nowrap;"
                                        title="Send a test email to verify this inbox is reachable">
                                    <i class="fa-solid fa-paper-plane me-1"></i>Test
                                </button>
                            </div>
                            <div class="mt-2 p-2 rounded d-flex align-items-start gap-2"
                                 style="background:rgba(37,99,235,.08);border:1px solid rgba(37,99,235,.2);">
                                <i class="fa-solid fa-circle-info flex-shrink-0 mt-1" style="color:var(--blue);font-size:11px;"></i>
                                <span style="font-size:11px;color:var(--blue);">
                                    <strong>This is the ONLY address that will receive the welcome email.</strong>
                                    The temporary password and login URL are sent exclusively to this inbox.
                                </span>
                            </div>
                            {{-- Live preview of what will be sent --}}
                            <div id="email-live-preview" style="display:none;margin-top:8px;">
                                <div class="d-flex align-items-center gap-2 p-2 rounded" style="background:rgba(5,150,105,.07);border:1px solid rgba(5,150,105,.25);">
                                    <i class="fa-solid fa-check-circle" style="color:var(--green);flex-shrink:0;"></i>
                                    <span style="font-size:12px;color:#065f46;">Welcome email will be delivered to: <strong id="email-preview-value"></strong></span>
                                </div>
                            </div>
                            {{-- Test email result --}}
                            <div id="test-email-result" style="display:none;margin-top:6px;font-size:12px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Institution Contact Email ── --}}
            <div class="col-md-6">
                <label class="form-label">Institution Contact Email <span style="color:var(--red);">*</span></label>
                <input type="email" name="email" id="inst-email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" placeholder="info@college.edu" required
                       oninput="checkEmailDiff()">
                <div class="form-hint">General college contact email — used for records only, NOT for credentials.</div>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                {{-- Diff warning --}}
                <div class="email-diff-warning" id="email-diff-warning">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i>
                    <strong>Note:</strong> The Institution Contact Email is different from the Principal Email.
                    Credentials will be sent to <strong id="diff-principal-email-val">—</strong> (Principal Email), <u>not</u> to the Institution Contact Email.
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="+91 XXXXX XXXXX">
            </div>
            <div class="col-12">
                <label class="form-label">Street Address</label>
                <textarea name="address" class="form-control" rows="2" placeholder="Street address...">{{ old('address') }}</textarea>
            </div>
            <div class="col-md-4"><label class="form-label">City</label><input type="text" name="city" class="form-control" value="{{ old('city') }}" placeholder="City"></div>
            <div class="col-md-4"><label class="form-label">State</label><input type="text" name="state" class="form-control" value="{{ old('state') }}" placeholder="State"></div>
            <div class="col-md-4"><label class="form-label">Pincode</label><input type="text" name="pincode" class="form-control" value="{{ old('pincode') }}" placeholder="500001"></div>
        </div>
    </div>
</div>
<div class="d-flex justify-content-between mt-3">
    <button type="button" class="btn-secondary" onclick="goStep(1)"><i class="fa-solid fa-arrow-left me-1"></i> Back</button>
    <button type="button" class="btn-primary" onclick="goStep(3)">Next: Review &amp; Confirm <i class="fa-solid fa-arrow-right ms-1"></i></button>
</div>
</div>

{{-- ════ STEP 3: Review & Confirm ════ --}}
<div class="wizard-panel" id="panel3">
<div class="card">
    <div class="card-header"><span style="font-weight:700;">Review &amp; Confirm</span></div>
    <div class="card-body">

        {{-- Institution summary --}}
        <div class="p-3 rounded mb-3" style="background:var(--surface2);border:1px solid var(--border);font-size:13px;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:10px;">Institution</div>
            <div class="row g-2">
                <div class="col-6"><span style="color:var(--muted);">Name:</span> <strong id="rev-name">--</strong></div>
                <div class="col-6"><span style="color:var(--muted);">Slug:</span> <code id="rev-slug">--</code></div>
                <div class="col-6"><span style="color:var(--muted);">Principal:</span> <strong id="rev-principal">--</strong></div>
            </div>
        </div>

        {{-- ── EMAIL DELIVERY CONFIRMATION — most prominent section ── --}}
        <div class="review-email-box">
            <div style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--blue);margin-bottom:12px;">
                <i class="fa-solid fa-envelope-circle-check me-2"></i>
                Credentials Will Be Delivered To
            </div>

            <div style="font-size:12px;color:var(--muted);margin-bottom:4px;">Principal Email (welcome email recipient):</div>
            <div class="review-email-value" id="rev-principal-email-big">--</div>

            <div class="mt-3 p-3 rounded" style="background:rgba(37,99,235,.06);border:1px dashed rgba(37,99,235,.3);font-size:12px;color:var(--blue);">
                <i class="fa-solid fa-circle-info me-1"></i>
                Please verify this email belongs to the Principal / College Admin and can receive emails.
                The welcome email will contain the <strong>Login Email</strong>, <strong>Temporary Password</strong>, and <strong>Login URL</strong>.
            </div>

            {{-- Contact email note if different --}}
            <div id="rev-diff-note" style="display:none;margin-top:10px;" class="p-2 rounded"
                 style="background:rgba(217,119,6,.08);border:1px solid rgba(217,119,6,.25);font-size:12px;color:#92400e;">
                <i class="fa-solid fa-triangle-exclamation me-1" style="color:#d97706;"></i>
                Institution Contact Email (<span id="rev-inst-email">--</span>) is <strong>different</strong> from Principal Email.
                Credentials go to the <strong>Principal Email only</strong>.
            </div>

            {{-- ── Confirmation Checkbox ── --}}
            <div class="confirm-checkbox-wrap">
                <label class="d-flex align-items-start gap-3" style="cursor:pointer;">
                    <input type="checkbox" id="confirm-email-checkbox" style="width:18px;height:18px;margin-top:2px;flex-shrink:0;cursor:pointer;"
                           onchange="validateConfirmCheckbox()">
                    <div style="font-size:13px;color:var(--text);font-weight:500;">
                        <i class="fa-solid fa-shield-check me-1" style="color:var(--green);"></i>
                        I confirm that <strong id="rev-confirm-email-text" style="color:var(--blue);">--</strong>
                        belongs to the Principal / College Admin and is ready to receive login credentials.
                    </div>
                </label>
            </div>
        </div>

    </div>
</div>
<div class="d-flex justify-content-between mt-3">
    <button type="button" class="btn-secondary" onclick="goStep(2)"><i class="fa-solid fa-arrow-left me-1"></i> Back</button>
    <button type="submit" class="btn-primary" id="submit-btn" disabled
            style="opacity:.5;cursor:not-allowed;"
            title="Please check the confirmation checkbox above">
        <i class="fa-solid fa-building me-1"></i> Create Institution &amp; Send Credentials
    </button>
</div>
</div>

</form>
</div>

{{-- ── Help Sidebar ── --}}
<div class="col-lg-4">
    <div class="card mb-3">
        <div class="card-header"><span style="font-weight:700;font-size:13px;">What happens after creation?</span></div>
        <div class="card-body p-0">
            @php $steps = [
                ['icon'=>'fa-building','color'=>'var(--blue)','text'=>'Institution record created in the system'],
                ['icon'=>'fa-user-shield','color'=>'var(--green)','text'=>'College Admin account auto-created with a temporary password'],
                ['icon'=>'fa-envelope-circle-check','color'=>'var(--purple)','text'=>'Welcome email sent to Principal Email with login credentials'],
                ['icon'=>'fa-key','color'=>'var(--orange)','text'=>'Principal logs in and changes the temporary password'],
                ['icon'=>'fa-graduation-cap','color'=>'var(--blue)','text'=>'College Admin configures courses, fees, and students'],
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

    <div class="card mb-3">
        <div class="card-header"><span style="font-weight:700;font-size:13px;">Email Fields — What's the Difference?</span></div>
        <div class="card-body" style="font-size:12px;line-height:1.8;">
            <div class="p-3 rounded mb-2" style="background:rgba(37,99,235,.07);border:2px solid rgba(37,99,235,.25);">
                <div style="font-weight:800;color:var(--blue);margin-bottom:4px;">
                    <i class="fa-solid fa-envelope-circle-check me-1"></i> Principal Email
                </div>
                <div style="color:var(--text2);">The principal's personal email. The welcome email with login credentials is sent <strong>exclusively here</strong>. This becomes the College Admin login.</div>
            </div>
            <div class="p-3 rounded" style="background:var(--surface2);border:1px solid var(--border);">
                <div style="font-weight:700;color:var(--text);margin-bottom:4px;">
                    <i class="fa-solid fa-building me-1"></i> Institution Contact Email
                </div>
                <div style="color:var(--muted);">General college contact email (e.g. info@college.edu). Used for institution records only. <strong>No credentials are sent here.</strong></div>
            </div>
        </div>
    </div>

    <div class="card" style="border:1px solid rgba(220,38,38,.2);background:rgba(220,38,38,.02);">
        <div class="card-header" style="background:rgba(220,38,38,.05);">
            <i class="fa-solid fa-circle-exclamation me-1" style="color:var(--red);"></i>
            <span style="font-weight:700;font-size:13px;color:var(--red);">Important Checklist</span>
        </div>
        <div class="card-body p-0">
            @foreach(['The Principal Email must belong to a real, active inbox','It should be the principal\'s personal email, not a shared inbox','Ensure it is not already registered in the system','The temporary password is sent only once — save it from the next screen'] as $i => $tip)
            <div class="d-flex align-items-start gap-2 px-3 py-2"
                 style="border-bottom:{{ $i < 3 ? '1px solid var(--border)' : 'none' }};font-size:12px;color:var(--text2);">
                <i class="fa-solid fa-circle-dot mt-1 flex-shrink-0" style="color:var(--red);font-size:9px;"></i>
                {{ $tip }}
            </div>
            @endforeach
        </div>
    </div>
</div>

</div>{{-- end .row --}}
@endsection

@push('scripts')
<script>
let currentStep = 1;

/* ── Wizard Navigation ──────────────────────────────────────────────────── */
function goStep(n) {
    if (n === 3) {
        const principalEmail = document.getElementById('principal-email').value.trim();
        const instEmail      = document.getElementById('inst-email').value.trim();
        if (!principalEmail) {
            Swal.fire({icon:'warning',title:'Principal Email Required',
                text:'Please enter the Principal Email. Login credentials will be sent to this address.',
                confirmButtonColor:'#2563EB'});
            return;
        }
        if (!instEmail) {
            Swal.fire({icon:'warning',title:'Institution Email Required',
                text:'Please enter the Institution Contact Email.',
                confirmButtonColor:'#2563EB'});
            return;
        }
        populateReview();
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
            c.className = 'step-circle active'; c.textContent = i;
            l.className = 'step-label active';
        } else {
            c.className = 'step-circle pending'; c.textContent = i;
            l.className = 'step-label pending';
        }
        if (i < 3) document.getElementById('line'+i).className = 'step-line'+(i < n?' done':'');
    }
    currentStep = n;
    window.scrollTo({top:0,behavior:'smooth'});
}

/* ── Populate Review Step ───────────────────────────────────────────────── */
function populateReview() {
    const name           = document.querySelector('[name="name"]').value || '--';
    const slug           = document.getElementById('inst-slug').value || '--';
    const principal      = document.getElementById('principal-name')?.value || '--';
    const principalEmail = document.getElementById('principal-email').value.trim();
    const instEmail      = document.getElementById('inst-email').value.trim();

    document.getElementById('rev-name').textContent      = name;
    document.getElementById('rev-slug').textContent      = slug;
    document.getElementById('rev-principal').textContent = principal;
    document.getElementById('rev-principal-email-big').textContent   = principalEmail || '--';
    document.getElementById('rev-confirm-email-text').textContent    = principalEmail || '--';

    // Show diff note if emails differ
    const diffNote = document.getElementById('rev-diff-note');
    if (instEmail && principalEmail && instEmail !== principalEmail) {
        document.getElementById('rev-inst-email').textContent = instEmail;
        diffNote.style.display = 'block';
    } else {
        diffNote.style.display = 'none';
    }

    // Reset checkbox on each review visit
    document.getElementById('confirm-email-checkbox').checked = false;
    updateSubmitBtn(false);
}

/* ── Confirmation Checkbox ──────────────────────────────────────────────── */
function validateConfirmCheckbox() {
    const checked = document.getElementById('confirm-email-checkbox').checked;
    updateSubmitBtn(checked);
}
function updateSubmitBtn(enabled) {
    const btn = document.getElementById('submit-btn');
    btn.disabled    = !enabled;
    btn.style.opacity = enabled ? '1' : '.5';
    btn.style.cursor  = enabled ? 'pointer' : 'not-allowed';
    btn.title = enabled ? '' : 'Please confirm the email address above';
}

/* ── Live email preview + diff warning ──────────────────────────────────── */
function onPrincipalEmailChange(val) {
    const preview = document.getElementById('email-live-preview');
    const previewVal = document.getElementById('email-preview-value');
    if (val && val.includes('@')) {
        previewVal.textContent = val;
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
    checkEmailDiff();
}
function checkEmailDiff() {
    const pe  = document.getElementById('principal-email').value.trim();
    const ie  = document.getElementById('inst-email').value.trim();
    const warn = document.getElementById('email-diff-warning');
    const diffVal = document.getElementById('diff-principal-email-val');
    if (pe && ie && pe !== ie) {
        diffVal.textContent = pe;
        warn.style.display = 'block';
    } else {
        warn.style.display = 'none';
    }
}

/* ── Send Test Email ────────────────────────────────────────────────────── */
function sendTestEmail() {
    const email = document.getElementById('principal-email').value.trim();
    if (!email || !email.includes('@')) {
        Swal.fire({icon:'warning',title:'Enter Email First',text:'Please enter the Principal Email address before sending a test.',confirmButtonColor:'#2563EB'});
        return;
    }
    const btn    = document.getElementById('test-email-btn');
    const result = document.getElementById('test-email-result');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" style="width:12px;height:12px;"></span>';
    result.style.display = 'none';

    fetch('/test-email-ajax', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({to: email})
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-paper-plane me-1"></i>Test';
        result.style.display = 'block';
        if (data.status === 'success') {
            result.innerHTML = '<div style="color:var(--green);background:rgba(5,150,105,.08);border:1px solid rgba(5,150,105,.25);border-radius:6px;padding:8px 10px;"><i class="fa-solid fa-circle-check me-1"></i><strong>Test email sent!</strong> Check inbox: ' + email + '</div>';
        } else {
            result.innerHTML = '<div style="color:var(--red);background:rgba(220,38,38,.06);border:1px solid rgba(220,38,38,.2);border-radius:6px;padding:8px 10px;"><i class="fa-solid fa-circle-xmark me-1"></i><strong>Failed:</strong> ' + (data.message || 'SMTP error') + '</div>';
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-paper-plane me-1"></i>Test';
        result.style.display = 'block';
        result.innerHTML = '<div style="color:var(--red);font-size:12px;"><i class="fa-solid fa-circle-xmark me-1"></i>Network error. Check SMTP config.</div>';
    });
}

/* ── Slug auto-generate ──────────────────────────────────────────────────── */
document.getElementById('inst-name')?.addEventListener('input', function () {
    const s = document.getElementById('inst-slug');
    if (!s.dataset.manual) {
        s.value = this.value.toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/^-|-$/g,'');
    }
});
document.getElementById('inst-slug')?.addEventListener('input', function () {
    this.dataset.manual = '1';
    this.value = this.value.toLowerCase().replace(/[^a-z0-9-]/g,'');
});

/* ── Submit loading state ────────────────────────────────────────────────── */
document.getElementById('wizard-form')?.addEventListener('submit', function () {
    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" style="width:14px;height:14px;"></span>Creating Institution...';
});

/* ── Jump to error step on validation failure ────────────────────────────── */
const errorMeta = document.getElementById('create-form-error-fields');
if (errorMeta) {
    const errorFields = JSON.parse(errorMeta.dataset.fields || '[]');
    const stepMap = {
        1: ['name','slug','affiliation_number','website','domain'],
        2: ['principal_name','principal_email','email','phone','address','city','state','pincode']
    };
    for (let s = 1; s <= 2; s++) {
        if (errorFields.some(function(f){ return (stepMap[s]||[]).indexOf(f) !== -1; })) {
            goStep(s); break;
        }
    }
}

/* ── Init: trigger preview if old() values exist ────────────────────────── */
const pe = document.getElementById('principal-email');
if (pe && pe.value) { onPrincipalEmailChange(pe.value); }
</script>
@endpush
