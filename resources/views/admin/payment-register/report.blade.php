<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments Register — {{ $selectedYear?->name }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            background: #fff;
            color: #000;
        }

        /* ── Controls bar (hidden on print) ── */
        .controls {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 20px;
            background: #1e40af;
            color: #fff;
            gap: 12px;
            flex-wrap: wrap;
        }
        .controls select, .controls button {
            padding: 5px 12px;
            border-radius: 6px;
            border: 1px solid rgba(255,255,255,.4);
            font-size: 13px;
            cursor: pointer;
        }
        .controls select { background: rgba(255,255,255,.15); color: #fff; }
        .controls select option { background: #1e40af; }
        .controls button.print-btn { background: #fff; color: #1e40af; font-weight: 700; border: none; }
        .controls button.back-btn { background: transparent; color: #fff; font-weight: 500; }
        @media print { .controls { display: none !important; } }

        /* ── Report wrapper ── */
        .report-page {
            padding: 16px 12px;
        }

        /* ── Header ── */
        .report-header {
            text-align: center;
            margin-bottom: 6px;
        }
        .report-header .fy {
            font-size: 13px;
            font-weight: bold;
            border: 1px solid #000;
            display: inline-block;
            padding: 2px 20px;
            margin-bottom: 3px;
        }
        .report-header .college-name {
            font-size: 15px;
            font-weight: bold;
            border: 1px solid #000;
            display: inline-block;
            padding: 2px 20px;
        }

        /* ── Section title ── */
        .section-title {
            text-align: center;
            font-size: 10px;
            font-weight: bold;
            border: 1px solid #000;
            padding: 3px 0;
            margin: 4px 0;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        /* ── Table ── */
        .register-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .register-table th,
        .register-table td {
            border: 1px solid #000;
            padding: 3px 2px;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .register-table thead th {
            font-size: 7.5px;
            font-weight: bold;
            background: #f0f0f0;
            line-height: 1.2;
        }
        .register-table tbody td {
            font-size: 8px;
        }
        .register-table tfoot td {
            font-size: 8px;
            font-weight: bold;
            background: #e8e8e8;
        }

        /* Column widths */
        .col-sno   { width: 2.5%; }
        .col-date  { width: 5%; }
        .col-pay   { width: 3.9%; } /* 20 columns × 3.9% ≈ 78% */
        .col-total { width: 4.5%; }

        /* Data rows — alternate shading for readability */
        .register-table tbody tr:nth-child(even) td {
            background: #fafafa;
        }

        /* Empty rows for writing */
        .register-table tbody tr.blank-row td {
            height: 18px;
        }

        /* Footer total row */
        .register-table tfoot tr td {
            text-align: center;
        }

        /* ── No data message ── */
        .no-data {
            text-align: center;
            padding: 40px;
            font-size: 12px;
            color: #555;
        }

        /* ── Print page setup ── */
        @page {
            size: A3 landscape;
            margin: 8mm;
        }
        @media print {
            body { font-size: 8px; }
            .report-page { padding: 0; }
        }
    </style>
</head>
<body>

{{-- Controls (screen only) --}}
<div class="controls">
    <div style="font-weight:700;font-size:15px;">
        <i style="margin-right:6px;">📋</i> Payments Register Report
    </div>
    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
        <form method="GET" style="display:flex;align-items:center;gap:8px;">
            <select name="academic_year_id" onchange="this.form.submit()">
                @foreach($academicYears as $year)
                    <option value="{{ $year->id }}" {{ $selectedYearId == $year->id ? 'selected' : '' }}>
                        {{ $year->name }} {{ $year->is_current ? '(Current)' : '' }}
                    </option>
                @endforeach
            </select>
        </form>
        <a href="{{ route('admin.exports.payment-register', ['academic_year_id'=>$selectedYearId, 'format'=>'xlsx']) }}"
           style="background:rgba(255,255,255,.15);color:#fff;padding:5px 14px;border-radius:6px;text-decoration:none;font-size:13px;border:1px solid rgba(255,255,255,.4);">
            ⬇ Excel
        </a>
        <button class="print-btn" onclick="window.print()">🖨 Print</button>
        <a href="{{ route('admin.payment-register.index') }}"
           style="background:transparent;color:#fff;padding:5px 14px;border-radius:6px;text-decoration:none;font-size:13px;border:1px solid rgba(255,255,255,.4);">
            ← Back
        </a>
    </div>
</div>

<div class="report-page">

    {{-- Report Header --}}
    <div class="report-header">
        <div class="fy">&lt;{{ $selectedYear?->name ?? 'Financial Year' }}&gt;</div><br>
        <div class="college-name">&lt;{{ $tenant?->name ?? 'College Name' }}&gt;</div>
    </div>

    <div class="section-title">PAYMENTS</div>

    @if($entries->count())

    <table class="register-table">
        <thead>
            <tr>
                <th class="col-sno">S.NO</th>
                <th class="col-date">DATE</th>
                @foreach($columns as $field => $label)
                    <th class="col-pay">{{ strtoupper($label) }}</th>
                @endforeach
                <th class="col-total">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entries as $i => $entry)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $entry->entry_date?->format('d/m/Y') }}</td>
                @foreach(array_keys($columns) as $field)
                    <td>
                        @if((float) $entry->{$field} > 0)
                            {{ number_format($entry->{$field}, 2) }}
                        @endif
                    </td>
                @endforeach
                <td><strong>{{ number_format($entry->total, 2) }}</strong></td>
            </tr>
            @endforeach

            {{-- Blank rows to pad the register for handwriting if needed --}}
            @for($b = $entries->count(); $b < 30; $b++)
            <tr class="blank-row">
                <td>{{ $b + 1 }}</td>
                <td></td>
                @foreach($columns as $col => $lbl)<td></td>@endforeach
                <td></td>
            </tr>
            @endfor
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" style="text-align:right;font-weight:bold;font-size:8.5px;">
                    TOTAL
                </td>
                @foreach(array_keys($columns) as $field)
                    <td>
                        @if($columnTotals[$field] > 0)
                            {{ number_format($columnTotals[$field], 2) }}
                        @endif
                    </td>
                @endforeach
                <td><strong>{{ number_format($grandTotal, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    @else
        <div class="no-data">
            No payment entries found for {{ $selectedYear?->name ?? 'the selected year' }}.
            <br><br>
            <a href="{{ route('admin.payment-register.create') }}"
               style="color:#1e40af;font-weight:bold;">Add First Entry →</a>
        </div>
    @endif

</div>

</body>
</html>
