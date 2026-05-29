<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Report — {{ $student->admission_number }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; background: #fff; color: #1a1a2e; font-size: 11px; }
        .page { padding: 24px; }
        .header { background: #4f46e5; color: #fff; padding: 18px 24px; border-radius: 6px 6px 0 0; }
        .header-title { font-size: 17px; font-weight: bold; }
        .header-sub { font-size: 10px; opacity: .8; margin-top: 2px; }
        .body { padding: 18px 24px; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 6px 6px; }
        .section-title { font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: .06em; color: #4f46e5; margin: 14px 0 6px; border-bottom: 1px solid #e0e7ff; padding-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8f7ff; font-size: 10px; font-weight: bold; color: #6b7280; padding: 5px 8px; border-bottom: 2px solid #e0e7ff; text-align: left; }
        td { padding: 5px 8px; border-bottom: 1px solid #f3f4f6; font-size: 11px; }
        .flex-between { display: flex; justify-content: space-between; }
        .stat-box { display: inline-block; padding: 8px 14px; border-radius: 6px; text-align: center; margin-right: 8px; }
        .stat-val { font-size: 18px; font-weight: bold; }
        .stat-lbl { font-size: 9px; margin-top: 2px; }
        .badge { padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; }
        .badge-active { background: #dcfce7; color: #166534; }
        .badge-paid { background: #dcfce7; color: #166534; }
        .badge-partial { background: #fef3c7; color: #92400e; }
        .badge-pending { background: #fee2e2; color: #991b1b; }
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
            </div>
            <div style="text-align:right;">
                <div style="font-size:9px;opacity:.7;text-transform:uppercase;">Student Report</div>
                <div style="font-size:12px;font-weight:bold;font-family:monospace;margin-top:2px;">{{ $student->admission_number }}</div>
                <div style="font-size:10px;opacity:.7;">{{ now()->format('d M Y') }}</div>
            </div>
        </div>
    </div>

    <div class="body">
        {{-- Personal Info --}}
        <div class="section-title">Personal Information</div>
        <table>
            <tr>
                <td style="width:25%;"><span style="color:#6b7280;font-size:10px;">Full Name</span><br><strong>{{ $student->full_name }}</strong></td>
                <td style="width:25%;"><span style="color:#6b7280;font-size:10px;">Date of Birth</span><br>{{ $student->date_of_birth?->format('d M Y') ?? '—' }}</td>
                <td style="width:25%;"><span style="color:#6b7280;font-size:10px;">Gender</span><br>{{ ucfirst($student->gender ?? '—') }}</td>
                <td style="width:25%;"><span style="color:#6b7280;font-size:10px;">Blood Group</span><br>{{ $student->blood_group ?? '—' }}</td>
            </tr>
            <tr>
                <td><span style="color:#6b7280;font-size:10px;">Phone</span><br>{{ $student->phone ?? '—' }}</td>
                <td><span style="color:#6b7280;font-size:10px;">Email</span><br>{{ $student->email ?? '—' }}</td>
                <td><span style="color:#6b7280;font-size:10px;">Category</span><br>{{ $student->category }}</td>
                <td><span style="color:#6b7280;font-size:10px;">Status</span><br><span class="badge badge-active">{{ ucfirst($student->status) }}</span></td>
            </tr>
        </table>

        {{-- Academic Info --}}
        <div class="section-title">Academic Information</div>
        <table>
            <tr>
                <td style="width:25%;"><span style="color:#6b7280;font-size:10px;">Branch</span><br><strong>{{ $student->branch?->name ?? '—' }}</strong></td>
                <td style="width:25%;"><span style="color:#6b7280;font-size:10px;">Course</span><br>{{ $student->branch?->course?->name ?? '—' }}</td>
                <td style="width:25%;"><span style="color:#6b7280;font-size:10px;">Semester</span><br>Semester {{ $student->current_semester }}</td>
                <td style="width:25%;"><span style="color:#6b7280;font-size:10px;">Academic Year</span><br>{{ $student->academicYear?->name ?? '—' }}</td>
            </tr>
            <tr>
                <td><span style="color:#6b7280;font-size:10px;">Admission Date</span><br>{{ $student->admission_date?->format('d M Y') ?? '—' }}</td>
                <td><span style="color:#6b7280;font-size:10px;">Univ. Reg. No</span><br>{{ $student->university_reg_number ?? 'Not assigned' }}</td>
                <td><span style="color:#6b7280;font-size:10px;">Scholarship</span><br>{{ $student->scholarship_eligible ? 'Eligible' : 'Not Eligible' }}</td>
                <td><span style="color:#6b7280;font-size:10px;">Father Name</span><br>{{ $student->father_name ?? '—' }}</td>
            </tr>
        </table>

        {{-- Attendance Summary --}}
        <div class="section-title">Attendance Summary</div>
        <table>
            <tr>
                <td style="width:25%;text-align:center;">
                    <div style="font-size:20px;font-weight:bold;color:#4f46e5;">{{ $attendancePct }}%</div>
                    <div style="font-size:9px;color:#6b7280;">Attendance %</div>
                </td>
                <td style="width:25%;text-align:center;">
                    <div style="font-size:20px;font-weight:bold;color:#059669;">{{ $presentDays }}</div>
                    <div style="font-size:9px;color:#6b7280;">Present Days</div>
                </td>
                <td style="width:25%;text-align:center;">
                    <div style="font-size:20px;font-weight:bold;color:#dc2626;">{{ $absentDays }}</div>
                    <div style="font-size:9px;color:#6b7280;">Absent Days</div>
                </td>
                <td style="width:25%;text-align:center;">
                    <div style="font-size:20px;font-weight:bold;color:#6b7280;">{{ $totalDays }}</div>
                    <div style="font-size:9px;color:#6b7280;">Total Days</div>
                </td>
            </tr>
        </table>

        {{-- Fee Summary --}}
        <div class="section-title">Fee Summary</div>
        <table>
            <tr>
                <td style="width:50%;text-align:center;">
                    <div style="font-size:16px;font-weight:bold;color:#059669;">₹{{ number_format($totalPaid, 2) }}</div>
                    <div style="font-size:9px;color:#6b7280;">Total Fees Paid</div>
                </td>
                <td style="width:50%;text-align:center;">
                    <div style="font-size:16px;font-weight:bold;color:#dc2626;">₹{{ number_format($totalDue, 2) }}</div>
                    <div style="font-size:9px;color:#6b7280;">Total Fees Pending</div>
                </td>
            </tr>
        </table>

        @if($student->feePayments->count() > 0)
        <div class="section-title">Fee Payment History</div>
        <table>
            <thead>
                <tr>
                    <th>Receipt No</th>
                    <th>Fee Type</th>
                    <th>Date</th>
                    <th>Amount Paid</th>
                    <th>Mode</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($student->feePayments->take(10) as $payment)
                <tr>
                    <td style="font-family:monospace;font-size:10px;">{{ $payment->receipt_number }}</td>
                    <td>{{ $payment->feeType?->name }}</td>
                    <td>{{ $payment->payment_date?->format('d M Y') }}</td>
                    <td style="color:#059669;font-weight:bold;">₹{{ number_format($payment->amount_paid, 2) }}</td>
                    <td>{{ ucfirst($payment->payment_mode) }}</td>
                    <td><span class="badge badge-{{ $payment->status }}">{{ ucfirst($payment->status) }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        {{-- Certificates --}}
        @if($student->certificates->count() > 0)
        <div class="section-title">Submitted Documents ({{ $student->certificates->count() }})</div>
        <table>
            <thead><tr><th>Document</th><th>Type</th><th>Verified</th><th>Uploaded</th></tr></thead>
            <tbody>
                @foreach($student->certificates as $cert)
                <tr>
                    <td>{{ $cert->certificate_label }}</td>
                    <td>{{ strtoupper(pathinfo($cert->original_filename, PATHINFO_EXTENSION)) }}</td>
                    <td>{{ $cert->is_verified ? 'Yes' : 'Pending' }}</td>
                    <td>{{ $cert->created_at?->format('d M Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <div class="footer-row">
            <div style="font-size:9px;color:#9ca3af;">
                Generated on {{ now()->format('d M Y, h:i A') }}<br>
                This is a computer-generated report.
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
