<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Low Attendance Alert</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f3f4f6; margin: 0; padding: 20px; color: #1f2937; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,.07); }
        .header { background: linear-gradient(135deg, #d97706, #f59e0b); padding: 32px 40px; color: #fff; }
        .header h1 { margin: 0 0 4px; font-size: 22px; font-weight: 700; }
        .body { padding: 32px 40px; }
        .alert-box { background: #fef3c7; border: 1px solid #fcd34d; border-radius: 8px; padding: 20px; margin: 20px 0; text-align: center; }
        .pct { font-size: 48px; font-weight: 800; color: #d97706; }
        .info-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .info-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #e5e7eb; font-size: 14px; }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #6b7280; }
        .info-value { font-weight: 600; }
        .footer { background: #f9fafb; padding: 20px 40px; text-align: center; font-size: 12px; color: #9ca3af; border-top: 1px solid #e5e7eb; }
        p { line-height: 1.6; color: #374151; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>⚠️ Low Attendance Alert</h1>
        <p>{{ $tenant->name }}</p>
    </div>
    <div class="body">
        <p>Dear <strong>{{ $student->full_name }}</strong>,</p>
        <p>This is an important notice regarding your attendance at <strong>{{ $tenant->name }}</strong>.</p>

        <div class="alert-box">
            <div class="pct">{{ $percentage }}%</div>
            <div style="font-size:14px;color:#92400e;font-weight:600;">Your Current Attendance</div>
            <div style="font-size:13px;color:#78350f;margin-top:8px;">Minimum required: 75%</div>
        </div>

        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Student Name</span>
                <span class="info-value">{{ $student->full_name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Admission No</span>
                <span class="info-value" style="font-family:monospace;color:#4f46e5;">{{ $student->admission_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Branch</span>
                <span class="info-value">{{ $student->branch?->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Current Attendance</span>
                <span class="info-value" style="color:#d97706;">{{ $percentage }}%</span>
            </div>
        </div>

        <p><strong>Action Required:</strong> Please attend classes regularly to maintain the minimum 75% attendance requirement. Students with attendance below 75% may not be eligible to appear in examinations.</p>
        <p>If you have any concerns, please contact your class teacher or the college office immediately.</p>
    </div>
    <div class="footer">
        <p>This is an automated alert from {{ $tenant->name }} ERP System.</p>
        <p>© {{ date('Y') }} {{ $tenant->name }}. All rights reserved.</p>
    </div>
</div>
</body>
</html>
