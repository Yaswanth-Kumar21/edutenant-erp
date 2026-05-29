<?php

namespace App\Exports;

use App\Models\Payroll;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PayrollExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    public function __construct(
        private readonly int $tenantId,
        private readonly ?int $month = null,
        private readonly ?int $year = null
    ) {}

    public function query()
    {
        $query = Payroll::where('tenant_id', $this->tenantId)
            ->with(['staff.user'])
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc');

        if ($this->month) $query->where('month', $this->month);
        if ($this->year)  $query->where('year', $this->year);

        return $query;
    }

    public function headings(): array
    {
        return [
            'Staff Name', 'Designation', 'Month', 'Year',
            'Basic Salary', 'HRA', 'DA', 'Other Allowances',
            'PF Deduction', 'Tax Deduction', 'Other Deductions',
            'Gross Salary', 'Net Salary', 'Status', 'Payment Date',
        ];
    }

    public function map($payroll): array
    {
        return [
            $payroll->staff?->user?->name,
            $payroll->staff?->designation,
            date('F', mktime(0, 0, 0, $payroll->month, 1)),
            $payroll->year,
            $payroll->basic_salary,
            $payroll->hra ?? 0,
            $payroll->da ?? 0,
            $payroll->other_allowances ?? 0,
            $payroll->pf_deduction ?? 0,
            $payroll->tax_deduction ?? 0,
            $payroll->other_deductions ?? 0,
            $payroll->gross_salary,
            $payroll->net_salary,
            ucfirst($payroll->status),
            $payroll->payment_date?->format('d/m/Y'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '7C3AED']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }

    public function title(): string
    {
        return 'Payroll';
    }
}
