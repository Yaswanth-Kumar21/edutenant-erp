<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    public function __construct(
        private readonly int $tenantId,
        private readonly ?int $branchId = null,
        private readonly ?string $status = null
    ) {}

    public function query()
    {
        $query = Student::where('tenant_id', $this->tenantId)
            ->with(['branch.course', 'academicYear'])
            ->orderBy('first_name');

        if ($this->branchId) {
            $query->where('branch_id', $this->branchId);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Admission No', 'First Name', 'Last Name', 'Father Name',
            'Gender', 'Date of Birth', 'Phone', 'Email',
            'Branch', 'Course', 'Semester', 'Academic Year',
            'Category', 'Status', 'Scholarship', 'Admission Date',
            'City', 'State',
        ];
    }

    public function map($student): array
    {
        return [
            $student->admission_number,
            $student->first_name,
            $student->last_name,
            $student->father_name,
            ucfirst($student->gender ?? ''),
            $student->date_of_birth?->format('d/m/Y'),
            $student->phone,
            $student->email,
            $student->branch?->name,
            $student->branch?->course?->name,
            $student->current_semester,
            $student->academicYear?->name,
            $student->category,
            ucfirst($student->status),
            $student->scholarship_eligible ? 'Yes' : 'No',
            $student->admission_date?->format('d/m/Y'),
            $student->city,
            $student->state,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '4F46E5']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }

    public function title(): string
    {
        return 'Students';
    }
}
