<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password — EduTenant ERP</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --blue: #2563EB; --blue-d: #1D4ED8;
            --green: #059669; --orange: #D97706;
            --red: #DC2626; --indigo: #4F46E5;
            --text: #0F172A; --muted: #64748B;
            --border: #E2E8F0; --bg: #F8FAFC;
            --surface: #FFFFFF; --radius: 8px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 25px 50px rgba(0,0,0,.25);
            width: 100%;
            max-width: 480px;
            overflow: hidden;
        }
        .card-header-custom {
            background: linear-gradient(135deg, #2563EB, #4f46e5);
            padding: 32px 36px 24px;
            color: #fff;
        }
        .card-header-custom .icon-wrap {
            width: 52px; height: 52px;
            background: rgba(255,255,255,.15);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
            margin-bottom: 16px;
        }
        .card-header-custom h1 { font-size: 20px; font-weight: 700; margin-bottom: 6px; }
        .card-header-custom p { font-size: 13px; opacity: .8; margin: 0; line-height: 1.5; }
        .card-body-custom { padding: 32px 36px; }
        .warning-banner {
            background: #fffbeb;
            border: 1px solid #fcd34d;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 13px;
            color: #92400e;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 24px;
        }
        .form-label { font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 6px; display: block; }
        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            color: var(--text);
            transition: all .15s;
        }
        .form-control:focus { outline: none; border-color: var(--blue); box-shadow: 0 0 0 3px rgba(37,99,235,.1); }
        .form-control.is-invalid { border-color: var(--red); }
        .invalid-feedback { font-size: 12px; color: var(--red); margin-top: 4px; }
        .input-group-custom { position: relative; }
        .input-group-custom .toggle-pw {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            background: none; border: none; color: var(--muted); cursor: pointer; font-size: 14px;
        }
        .strength-bar { height: 4px; border-radius: 2px; background: var(--border); margin-top: 8px; overflow: hidden; }
        .strength-fill { height: 100%; border-radius: 2px; transition: width .3s, background .3s; width: 0; }
        .strength-label { font-size: 11px; color: var(--muted); margin-top: 4px; }
        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, var(--blue), var(--indigo));
            border: none;
            color: #fff;
            font-weight: 700;
            font-size: 15px;
            padding: 13px;
            border-radius: var(--radius);
            cursor: pointer;
            transition: all .2s;
            margin-top: 8px;
        }
        .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(37,99,235,.35); }
        .requirements { background: #f8fafc; border: 1px solid var(--border); border-radius: 8px; padding: 14px 16px; margin-top: 16px; }
        .requirements-title { font-size: 12px; font-weight: 600; color: var(--muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: .04em; }
        .req-item { font-size: 12px; color: var(--muted); display: flex; align-items: center; gap: 6px; margin-bottom: 4px; }
        .req-item.met { color: var(--green); }
        .req-item i { font-size: 11px; width: 14px; }
        .form-group { margin-bottom: 20px; }
        .alert-danger-custom {
            background: rgba(220,38,38,.08);
            border: 1px solid rgba(220,38,38,.2);
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 13px;
            color: #991B1B;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="card">
    <div class="card-header-custom">
        <div class="icon-wrap"><i class="fa-solid fa-key"></i></div>
        <h1>Change Your Password</h1>
        <p>For security, you must set a new password before accessing your dashboard.</p>
    </div>
    <div class="card-body-custom">

        {{-- Warning Banner --}}
        <div class="warning-banner">
            <i class="fa-solid fa-triangle-exclamation flex-shrink-0 mt-1"></i>
            <div>
                You are using a <strong>temporary password</strong>. Please create a strong, unique password to secure your account.
            </div>
        </div>

        {{-- Validation Errors --}}
        @if($errors->any())
        <div class="alert-danger-custom">
            <i class="fa-solid fa-circle-exclamation me-2"></i>
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('password.change.update') }}" id="changePasswordForm">
            @csrf

            {{-- Current Password --}}
            <div class="form-group">
                <label class="form-label">Current (Temporary) Password <span style="color:var(--red);">*</span></label>
                <div class="input-group-custom">
                    <input type="password" name="current_password" id="currentPw"
                           class="form-control @error('current_password') is-invalid @enderror"
                           placeholder="Enter your temporary password" required>
                    <button type="button" class="toggle-pw" onclick="togglePw('currentPw', this)">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
                @error('current_password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- New Password --}}
            <div class="form-group">
                <label class="form-label">New Password <span style="color:var(--red);">*</span></label>
                <div class="input-group-custom">
                    <input type="password" name="password" id="newPw"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Create a strong password"
                           oninput="checkStrength(this.value)"
                           required>
                    <button type="button" class="toggle-pw" onclick="togglePw('newPw', this)">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
                <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
                <div class="strength-label" id="strengthLabel">Enter a password</div>
                @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div class="form-group">
                <label class="form-label">Confirm New Password <span style="color:var(--red);">*</span></label>
                <div class="input-group-custom">
                    <input type="password" name="password_confirmation" id="confirmPw"
                           class="form-control"
                           placeholder="Repeat your new password" required>
                    <button type="button" class="toggle-pw" onclick="togglePw('confirmPw', this)">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
            </div>

            {{-- Password Requirements --}}
            <div class="requirements">
                <div class="requirements-title">Password Requirements</div>
                <div class="req-item" id="req-length"><i class="fa-solid fa-circle"></i> At least 8 characters</div>
                <div class="req-item" id="req-upper"><i class="fa-solid fa-circle"></i> One uppercase letter</div>
                <div class="req-item" id="req-lower"><i class="fa-solid fa-circle"></i> One lowercase letter</div>
                <div class="req-item" id="req-number"><i class="fa-solid fa-circle"></i> One number</div>
                <div class="req-item" id="req-symbol"><i class="fa-solid fa-circle"></i> One special character (!@#$%)</div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fa-solid fa-shield-check me-2"></i> Set New Password & Continue
            </button>
        </form>

        <div style="text-align:center;margin-top:20px;">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="background:none;border:none;color:var(--muted);font-size:13px;cursor:pointer;">
                    <i class="fa-solid fa-right-from-bracket me-1"></i> Sign out instead
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function togglePw(id, btn) {
    const input = document.getElementById(id);
    const icon  = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

function checkStrength(pw) {
    const fill  = document.getElementById('strengthFill');
    const label = document.getElementById('strengthLabel');

    const checks = {
        length: pw.length >= 8,
        upper:  /[A-Z]/.test(pw),
        lower:  /[a-z]/.test(pw),
        number: /[0-9]/.test(pw),
        symbol: /[^A-Za-z0-9]/.test(pw),
    };

    // Update requirement indicators
    Object.entries(checks).forEach(([key, met]) => {
        const el = document.getElementById('req-' + key);
        if (el) {
            el.classList.toggle('met', met);
            el.querySelector('i').className = met ? 'fa-solid fa-circle-check' : 'fa-solid fa-circle';
        }
    });

    const score = Object.values(checks).filter(Boolean).length;
    const widths  = ['0%', '20%', '40%', '60%', '80%', '100%'];
    const colors  = ['#dc2626', '#dc2626', '#f59e0b', '#f59e0b', '#10b981', '#059669'];
    const labels  = ['', 'Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];

    fill.style.width      = widths[score];
    fill.style.background = colors[score];
    label.textContent     = labels[score];
    label.style.color     = colors[score];
}
</script>
</body>
</html>
