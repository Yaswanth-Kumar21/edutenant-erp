<?php

namespace App\Exports;

use App\Models\StudentAttendance;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    public function __construct(
        private readonly int $tenantId,
        private readonly ?int $branchId = null,
        private readonly ?string $month = null
    ) {}

    public function query()
    {
        $query = StudentAttendance::where('tenant_id', $this->tenantId)
            ->with(['student', 'branch', 'markedBy'])
            ->orderBy('attendance_date');

        if ($this->branchId) {
            $query->where('branch_id', $this->branchId);
        }

        if ($this->month) {
            [$year, $mon] = explode('-', $this->month);
            $query->whereYear('attendance_date', $year)->whereMonth('attendance_date', $mon);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Date', 'Student Name', 'Admission No',
            'Branch', 'Semester', 'Subject',
            'Status', 'Remarks', 'Marked By',
        ];
    }

    public function map($record): array
    {
        return [
            $record->attendance_date?->format('d/m/Y'),
            $record->student?->full_name,
            $record->student?->admission_number,
            $record->branch?->name,
            $record->semester,
            $record->subject,
            ucfirst($record->status),
            $record->remarks,
            $record->markedBy?->name,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '0891B2']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }

    public function title(): string
    {
        return 'Attendance';
    }
}
