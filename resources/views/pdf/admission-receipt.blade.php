<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admission Receipt — {{ $student->admission_number }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; background: #fff; color: #1a1a2e; font-size: 11px; }
        .page { padding: 24px; }
        .header { background: #d97706; color: #fff; padding: 18px 24px; border-radius: 6px 6px 0 0; }
        .header-title { font-size: 17px; font-weight: bold; }
        .header-sub { font-size: 10px; opacity: .8; margin-top: 2px; }
        .body { padding: 18px 24px; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 6px 6px; }
        .section-title { font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: .06em; color: #d97706; margin: 14px 0 6px; border-bottom: 1px solid #fef3c7; padding-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #fffbeb; font-size: 10px; font-weight: bold; color: #6b7280; padding: 5px 8px; border-bottom: 2px solid #fde68a; text-align: left; }
        td { padding: 5px 8px; border-bottom: 1px solid #f3f4f6; font-size: 11px; }
        .flex-between { display: flex; justify-content: space-between; }
        .adm-no { font-family: monospace; font-size: 18px; font-weight: bold; color: #d97706; }
        .badge-active { background: #dcfce7; color: #166534; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; }
        .footer-row { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 20px; padding-top: 12px; border-top: 1px dashed #d1d5db; }
        .sign-line { width: 120px; border-bottom: 1px solid #374151; margin-bottom: 4px; }
        .sign-label { font-size: 9px; color: #6b7280; }
    </style>
</head>
<body>
<div class="page">
    <div class="header">
        <div class="flex-between">
            <div>
                <div class="header-title">{{ $tenant->name ?? 'EduTenant ERP' }}</div>
                <div class="header-sub">{{ $tenant->address ?? '' }}{{ $tenant->city ? ', ' . $tenant->city : '' }}</div>
                @if($tenant->affiliation_number)<div class="header-sub">Affiliation: {{ $tenant->affiliation_number }}</div>@endif
            </div>
            <div style="text-align:right;">
                <div style="font-size:9px;opacity:.7;text-transform:uppercase;">Admission Receipt</div>
                <div class="adm-no" style="color:#fff;">{{ $student->admission_number }}</div>
                <div style="font-size:10px;opacity:.7;">{{ $student->admission_date?->format('d M Y') }}</div>
            </div>
        </div>
    </div>

    <div class="body">
        <div class="section-title">Student Information</div>
        <table>
            <tr>
                <td style="width:25%;"><span style="color:#6b7280;font-size:10px;">Full Name</span><br><strong>{{ $student->full_name }}</strong></td>
                <td style="width:25%;"><span style="color:#6b7280;font-size:10px;">Date of Birth</span><br>{{ $student->date_of_birth?->format('d M Y') ?? '—' }}</td>
                <td style="width:25%;"><span style="color:#6b7280;font-size:10px;">Gender</span><br>{{ ucfirst($student->gender ?? '—') }}</td>
                <td style="width:25%;"><span style="color:#6b7280;font-size:10px;">Category</span><br>{{ $student->category }}</td>
            </tr>
            <tr>
                <td><span style="color:#6b7280;font-size:10px;">Father Name</span><br>{{ $student->father_name ?? '—' }}</td>
                <td><span style="color:#6b7280;font-size:10px;">Mother Name</span><br>{{ $student->mother_name ?? '—' }}</td>
                <td><span style="color:#6b7280;font-size:10px;">Phone</span><br>{{ $student->phone ?? '—' }}</td>
                <td><span style="color:#6b7280;font-size:10px;">Blood Group</span><br>{{ $student->blood_group ?? '—' }}</td>
            </tr>
            <tr>
                <td colspan="4"><span style="color:#6b7280;font-size:10px;">Address</span><br>{{ $student->address ?? '—' }}{{ $student->city ? ', ' . $student->city : '' }}{{ $student->state ? ', ' . $student->state : '' }}{{ $student->pincode ? ' - ' . $student->pincode : '' }}</td>
            </tr>
        </table>

        <div class="section-title">Academic Details</div>
        <table>
            <tr>
                <td style="width:25%;"><span style="color:#6b7280;font-size:10px;">Course</span><br><strong>{{ $student->branch?->course?->name ?? '—' }}</strong></td>
                <td style="width:25%;"><span style="color:#6b7280;font-size:10px;">Branch</span><br>{{ $student->branch?->name ?? '—' }}</td>
                <td style="width:25%;"><span style="color:#6b7280;font-size:10px;">Stream</span><br>{{ $student->branch?->course?->stream?->name ?? '—' }}</td>
                <td style="width:25%;"><span style="color:#6b7280;font-size:10px;">Academic Year</span><br>{{ $student->academicYear?->name ?? '—' }}</td>
            </tr>
            <tr>
                <td><span style="color:#6b7280;font-size:10px;">Semester</span><br>Semester {{ $student->current_semester }}</td>
                <td><span style="color:#6b7280;font-size:10px;">Scholarship</span><br>{{ $student->scholarship_eligible ? 'Eligible' : 'Not Eligible' }}</td>
                <td><span style="color:#6b7280;font-size:10px;">Status</span><br><span class="badge-active">Active</span></td>
                <td><span style="color:#6b7280;font-size:10px;">Univ. Reg. No</span><br>{{ $student->university_reg_number ?? 'Pending' }}</td>
            </tr>
        </table>

        @if($student->guardian)
        <div class="section-title">Guardian Details</div>
        <table>
            <tr>
                <td style="width:33%;"><span style="color:#6b7280;font-size:10px;">Father Name</span><br>{{ $student->guardian->father_name ?? '—' }}</td>
                <td style="width:33%;"><span style="color:#6b7280;font-size:10px;">Father Phone</span><br>{{ $student->guardian->father_phone ?? '—' }}</td>
                <td style="width:33%;"><span style="color:#6b7280;font-size:10px;">Father Occupation</span><br>{{ $student->guardian->father_occupation ?? '—' }}</td>
            </tr>
        </table>
        @endif

        @if($receipt)
        <div class="section-title">Receipt Details</div>
        <table>
            <tr>
                <td style="width:33%;"><span style="color:#6b7280;font-size:10px;">Receipt Number</span><br><strong style="font-family:monospace;color:#d97706;">{{ $receipt->receipt_number ?? '—' }}</strong></td>
                <td style="width:33%;"><span style="color:#6b7280;font-size:10px;">Amount Paid</span><br><strong style="color:#059669;">₹{{ number_format($receipt->amount_paid ?? 0, 2) }}</strong></td>
                <td style="width:33%;"><span style="color:#6b7280;font-size:10px;">Payment Mode</span><br>{{ ucfirst($receipt->payment_mode ?? 'Cash') }}</td>
            </tr>
        </table>
        @endif

        <div class="footer-row">
            <div style="font-size:9px;color:#9ca3af;">
                Generated on {{ now()->format('d M Y, h:i A') }}<br>
                This is a computer-generated receipt. No signature required.
            </div>
            <div style="text-align:right;">
                <div class="sign-line"></div>
                <div class="sign-label">Principal / Authorised Signatory</div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
