<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to EduTenant ERP</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f3f4f6; margin: 0; padding: 20px; color: #1f2937; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,.07); }
        .header { background: linear-gradient(135deg, #2563EB, #4f46e5); padding: 32px 40px; color: #fff; }
        .header h1 { margin: 0 0 4px; font-size: 22px; font-weight: 700; }
        .header p { margin: 0; opacity: .8; font-size: 14px; }
        .body { padding: 32px 40px; }
        .greeting { font-size: 18px; font-weight: 600; margin-bottom: 16px; color: #1e1b4b; }
        .credentials-box {
            background: #f0f4ff;
            border: 2px solid #c7d2fe;
            border-radius: 10px;
            padding: 24px;
            margin: 24px 0;
        }
        .credentials-title {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #4338ca;
            margin-bottom: 16px;
        }
        .cred-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e0e7ff;
            font-size: 14px;
        }
        .cred-row:last-child { border-bottom: none; }
        .cred-label { color: #6b7280; font-weight: 500; }
        .cred-value { font-weight: 700; color: #1e1b4b; font-family: 'Courier New', monospace; background: #fff; padding: 4px 10px; border-radius: 6px; border: 1px solid #c7d2fe; }
        .warning-box {
            background: #fffbeb;
            border: 1px solid #fcd34d;
            border-radius: 8px;
            padding: 14px 18px;
            margin: 20px 0;
            font-size: 13px;
            color: #92400e;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        .warning-icon { font-size: 16px; flex-shrink: 0; margin-top: 1px; }
        .login-btn {
            display: inline-block;
            background: linear-gradient(135deg, #2563EB, #4f46e5);
            color: #fff;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 15px;
            margin: 8px 0 20px;
            text-align: center;
        }
        .steps-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .steps-title { font-size: 13px; font-weight: 700; color: #374151; margin-bottom: 12px; }
        .step-item { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 10px; font-size: 13px; color: #374151; }
        .step-num { width: 22px; height: 22px; border-radius: 50%; background: #2563EB; color: #fff; font-size: 11px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px; }
        .footer { background: #f9fafb; padding: 20px 40px; text-align: center; font-size: 12px; color: #9ca3af; border-top: 1px solid #e5e7eb; }
        p { line-height: 1.7; color: #374151; font-size: 14px; }
    </style>
</head>
<body>
<div class="container">

    {{-- Header --}}
    <div class="header">
        <h1>🎓 EduTenant ERP</h1>
        <p>Institution Management Platform</p>
    </div>

    <div class="body">
        <div class="greeting">Welcome, {{ $admin->name }}!</div>

        <p>
            Your College Admin account for <strong>{{ $tenant->name }}</strong> has been created on the
            <strong>EduTenant ERP</strong> platform. You can now log in and begin setting up your institution.
        </p>

        {{-- Credentials Box --}}
        <div class="credentials-box">
            <div class="credentials-title">🔐 Your Login Credentials</div>
            <div class="cred-row">
                <span class="cred-label">Institution</span>
                <span class="cred-value">{{ $tenant->name }}</span>
            </div>
            <div class="cred-row">
                <span class="cred-label">Login Email</span>
                <span class="cred-value">{{ $admin->email }}</span>
            </div>
            <div class="cred-row">
                <span class="cred-label">Temporary Password</span>
                <span class="cred-value">{{ $temporaryPassword }}</span>
            </div>
            <div class="cred-row">
                <span class="cred-label">Login URL</span>
                <span class="cred-value" style="font-size:12px;">{{ url('/login') }}</span>
            </div>
        </div>

        {{-- Warning --}}
        <div class="warning-box">
            <span class="warning-icon">⚠️</span>
            <div>
                <strong>Important:</strong> You will be required to change your password on first login.
                Please keep these credentials secure and do not share them with anyone.
            </div>
        </div>

        {{-- CTA Button --}}
        <div style="text-align:center;">
            <a href="{{ url('/login') }}" class="login-btn">Login to EduTenant ERP →</a>
        </div>

        {{-- Setup Steps --}}
        <div class="steps-box">
            <div class="steps-title">📋 Getting Started — Setup Checklist</div>
            <div class="step-item"><div class="step-num">1</div><div>Log in and change your temporary password</div></div>
            <div class="step-item"><div class="step-num">2</div><div>Configure Academic Years, Courses, and Branches</div></div>
            <div class="step-item"><div class="step-num">3</div><div>Set up Fee Types and Fee Structures</div></div>
            <div class="step-item"><div class="step-num">4</div><div>Add Staff members and assign roles</div></div>
            <div class="step-item"><div class="step-num">5</div><div>Begin admitting students</div></div>
        </div>

        <p>
            If you have any questions or need assistance, please contact your EduTenant platform administrator.
        </p>

        <p>Welcome aboard, and best wishes for a successful academic year!</p>
    </div>

    <div class="footer">
        <p>This is an automated email from EduTenant ERP. Please do not reply to this email.</p>
        <p>© {{ date('Y') }} EduTenant ERP. All rights reserved.</p>
    </div>
</div>
</body>
</html>
