<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Report — {{ $month }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; background: #fff; color: #1a1a2e; font-size: 10px; }
        .page { padding: 20px; }
        .header { background: #0891b2; color: #fff; padding: 14px 20px; border-radius: 6px 6px 0 0; }
        .header-title { font-size: 15px; font-weight: bold; }
        .header-sub { font-size: 9px; opacity: .8; margin-top: 2px; }
        .body { padding: 14px 20px; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 6px 6px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #ecfeff; font-size: 9px; font-weight: bold; color: #0e7490; padding: 5px 6px; border-bottom: 2px solid #a5f3fc; text-align: left; }
        td { padding: 4px 6px; border-bottom: 1px solid #f3f4f6; font-size: 10px; }
        tr:nth-child(even) td { background: #f9fafb; }
        .badge-present { background: #dcfce7; color: #166534; padding: 1px 6px; border-radius: 3px; font-size: 9px; font-weight: bold; }
        .badge-absent  { background: #fee2e2; color: #991b1b; padding: 1px 6px; border-radius: 3px; font-size: 9px; font-weight: bold; }
        .badge-late    { background: #fef3c7; color: #92400e; padding: 1px 6px; border-radius: 3px; font-size: 9px; font-weight: bold; }
        .flex-between { display: flex; justify-content: space-between; }
        .summary-box { display: inline-block; padding: 6px 12px; border-radius: 5px; text-align: center; margin-right: 8px; }
        .footer-note { font-size: 8px; color: #9ca3af; margin-top: 12px; padding-top: 8px; border-top: 1px dashed #d1d5db; }
    </style>
</head>
<body>
<div class="page">
    <div class="header">
        <div class="flex-between">
            <div>
                <div class="header-title">{{ $tenant->name ?? 'EduTenant ERP' }}</div>
                <div class="header-sub">Attendance Report — {{ date('F Y', mktime(0,0,0,$mon,1,$year)) }}</div>
            </div>
            <div style="text-align:right;font-size:9px;opacity:.8;">
                Generated: {{ now()->format('d M Y') }}<br>
                Total Records: {{ $records->count() }}
            </div>
        </div>
    </div>

    <div class="body">
        {{-- Summary --}}
        <div style="margin-bottom:12px;padding:10px;background:#f0fdfe;border:1px solid #a5f3fc;border-radius:6px;">
            <div style="font-size:9px;font-weight:bold;color:#0e7490;margin-bottom:6px;text-transform:uppercase;letter-spacing:.05em;">Summary</div>
            <div style="display:flex;gap:16px;">
                <div><span style="font-size:16px;font-weight:bold;color:#059669;">{{ $records->where('status','present')->count() }}</span><br><span style="font-size:9px;color:#6b7280;">Present</span></div>
                <div><span style="font-size:16px;font-weight:bold;color:#dc2626;">{{ $records->where('status','absent')->count() }}</span><br><span style="font-size:9px;color:#6b7280;">Absent</span></div>
                <div><span style="font-size:16px;font-weight:bold;color:#d97706;">{{ $records->where('status','late')->count() }}</span><br><span style="font-size:9px;color:#6b7280;">Late</span></div>
                <div><span style="font-size:16px;font-weight:bold;color:#6b7280;">{{ $records->count() }}</span><br><span style="font-size:9px;color:#6b7280;">Total</span></div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Student Name</th>
                    <th>Adm. No</th>
                    <th>Branch</th>
                    <th>Semester</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Marked By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $i => $record)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $record->attendance_date?->format('d M Y') }}</td>
                    <td><strong>{{ $record->student?->full_name }}</strong></td>
                    <td style="font-family:monospace;color:#4f46e5;font-size:9px;">{{ $record->student?->admission_number }}</td>
                    <td>{{ $record->branch?->name }}</td>
                    <td>{{ $record->semester ? 'Sem ' . $record->semester : '—' }}</td>
                    <td>{{ $record->subject ?? '—' }}</td>
                    <td><span class="badge-{{ $record->status }}">{{ ucfirst($record->status) }}</span></td>
                    <td>{{ $record->markedBy?->name ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="9" style="text-align:center;color:#9ca3af;padding:16px;">No attendance records found for this period.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer-note">
            This is a computer-generated attendance report from {{ $tenant->name }} ERP System. Generated on {{ now()->format('d M Y, h:i A') }}.
        </div>
    </div>
</div>
</body>
</html>
