<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Confirmed</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f3f4f6; margin: 0; padding: 20px; color: #1f2937; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,.07); }
        .header { background: linear-gradient(135deg, #4f46e5, #7c3aed); padding: 32px 40px; color: #fff; }
        .header h1 { margin: 0 0 4px; font-size: 22px; font-weight: 700; }
        .header p { margin: 0; opacity: .8; font-size: 14px; }
        .body { padding: 32px 40px; }
        .greeting { font-size: 18px; font-weight: 600; margin-bottom: 16px; }
        .info-box { background: #f8f7ff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .info-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #e5e7eb; font-size: 14px; }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #6b7280; }
        .info-value { font-weight: 600; color: #1e1b4b; }
        .badge { display: inline-block; background: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 20px; font-size: 13px; font-weight: 600; }
        .footer { background: #f9fafb; padding: 20px 40px; text-align: center; font-size: 12px; color: #9ca3af; border-top: 1px solid #e5e7eb; }
        p { line-height: 1.6; color: #374151; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>{{ $tenant->name }}</h1>
        <p>{{ $tenant->address }}{{ $tenant->city ? ', ' . $tenant->city : '' }}</p>
    </div>
    <div class="body">
        <div class="greeting">🎓 Admission Confirmed!</div>
        <p>Dear <strong>{{ $student->full_name }}</strong>,</p>
        <p>We are pleased to confirm your admission to <strong>{{ $tenant->name }}</strong>. Your admission has been successfully processed.</p>

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

        <p>Please visit the college office to complete any remaining documentation. You can log in to the student portal to view your admission receipt, fee details, and attendance.</p>
        <p>If you have any questions, please contact us at <a href="mailto:{{ $tenant->email }}" style="color:#4f46e5;">{{ $tenant->email }}</a>{{ $tenant->phone ? ' or call ' . $tenant->phone : '' }}.</p>
        <p>Welcome to {{ $tenant->name }}!</p>
    </div>
    <div class="footer">
        <p>This is an automated email from {{ $tenant->name }} ERP System. Please do not reply to this email.</p>
        <p>© {{ date('Y') }} {{ $tenant->name }}. All rights reserved.</p>
    </div>
</div>
</body>
</html>
