<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Confirmed</title>
    <style>
        body{font-family:'Segoe UI',Arial,sans-serif;background:#f3f4f6;margin:0;padding:20px;color:#1f2937;}
        .container{max-width:600px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 6px rgba(0,0,0,.07);}
        .header{background:linear-gradient(135deg,#4f46e5,#7c3aed);padding:32px 40px;color:#fff;}
        .header h1{margin:0 0 4px;font-size:22px;font-weight:700;}
        .header p{margin:0;opacity:.8;font-size:14px;}
        .body{padding:32px 40px;}
        .greeting{font-size:18px;font-weight:600;margin-bottom:16px;color:#1e1b4b;}
        .info-box{background:#f8f7ff;border:1px solid #e5e7eb;border-radius:8px;padding:20px;margin:20px 0;}
        .info-row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #e5e7eb;font-size:14px;}
        .info-row:last-child{border-bottom:none;}
        .info-label{color:#6b7280;}
        .info-value{font-weight:600;color:#1e1b4b;}
        .credentials-box{background:#f0f4ff;border:2px solid #c7d2fe;border-radius:10px;padding:24px;margin:24px 0;}
        .credentials-title{font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#4338ca;margin-bottom:16px;}
        .cred-row{display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid #e0e7ff;font-size:14px;}
        .cred-row:last-child{border-bottom:none;}
        .cred-label{color:#6b7280;font-weight:500;}
        .cred-value{font-weight:700;color:#1e1b4b;font-family:'Courier New',monospace;background:#fff;padding:4px 10px;border-radius:6px;border:1px solid #c7d2fe;}
        .warning-box{background:#fffbeb;border:1px solid #fcd34d;border-radius:8px;padding:14px 18px;margin:20px 0;font-size:13px;color:#92400e;}
        .login-btn{display:inline-block;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;text-decoration:none;padding:14px 32px;border-radius:8px;font-weight:700;font-size:15px;margin:8px 0 20px;text-align:center;}
        .badge{display:inline-block;background:#dcfce7;color:#166534;padding:4px 12px;border-radius:20px;font-size:13px;font-weight:600;}
        .footer{background:#f9fafb;padding:20px 40px;text-align:center;font-size:12px;color:#9ca3af;border-top:1px solid #e5e7eb;}
        p{line-height:1.7;color:#374151;font-size:14px;}
    </style>
</head>
<body>
<div class="container">

    {{-- Header --}}
    <div class="header">
        <h1>🎓 {{ $tenant->name }}</h1>
        <p>{{ $tenant->address }}{{ $tenant->city ? ', ' . $tenant->city : '' }}</p>
    </div>

    <div class="body">
        <div class="greeting">Congratulations, {{ $student->full_name }}! 🎉</div>

        <p>
            Your admission to <strong>{{ $tenant->name }}</strong> has been successfully confirmed.
            You can now log in to the Student Portal to view your fees, attendance, and certificates.
        </p>

        {{-- Admission Details --}}
        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Admission Number</span>
                <span class="info-value" style="font-family:monospace;color:#4f46e5;">{{ $student->admission_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Course / Branch</span>
                <span class="info-value">{{ $student->branch?->course?->name }} — {{ $student->branch?->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Academic Year</span>
                <span class="info-value">{{ $student->academicYear?->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Admission Date</span>
                <span class="info-value">{{ $student->admission_date?->format('d M Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Status</span>
                <span class="badge">Active</span>
            </div>
        </div>

        {{-- Login Credentials --}}
        <div class="credentials-box">
            <div class="credentials-title">🔐 Your Student Portal Login Credentials</div>
            <div class="cred-row">
                <span class="cred-label">Login Email</span>
                <span class="cred-value">{{ $loginEmail }}</span>
            </div>
            <div class="cred-row">
                <span class="cred-label">Password</span>
                <span class="cred-value">{{ $loginPassword }}</span>
            </div>
            <div class="cred-row">
                <span class="cred-label">Login URL</span>
                <span class="cred-value" style="font-size:12px;">{{ url('/login') }}</span>
            </div>
        </div>

        {{-- Warning --}}
        <div class="warning-box">
            <strong>⚠ Important:</strong> Your default password is your <strong>phone number</strong>.
            Please change your password after your first login to keep your account secure.
        </div>

        {{-- CTA --}}
        <div style="text-align:center;">
            <a href="{{ url('/login') }}" class="login-btn">Login to Student Portal →</a>
        </div>

        <p>
            You can view your admission receipt, fee dues, attendance records, and download certificates from the Student Portal.
            For any queries, contact us at
            <a href="mailto:{{ $tenant->email }}" style="color:#4f46e5;">{{ $tenant->email }}</a>
            {{ $tenant->phone ? ' or call ' . $tenant->phone : '' }}.
        </p>
        <p>Welcome to {{ $tenant->name }}! Wishing you a successful academic year.</p>
    </div>

    <div class="footer">
        <p>This is an automated email from {{ $tenant->name }} ERP System. Please do not reply to this email.</p>
        <p>© {{ date('Y') }} {{ $tenant->name }}. All rights reserved.</p>
    </div>
</div>
</body>
</html>
