<?php

namespace App\Exports;

use App\Models\AcademicYear;
use App\Models\PaymentRegister;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentRegisterExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle, WithEvents
{
    private array $columnKeys;

    public function __construct(
        private readonly int $tenantId,
        private readonly ?int $academicYearId = null,
        private readonly ?string $dateFrom = null,
        private readonly ?string $dateTo = null
    ) {
        $this->columnKeys = array_keys(PaymentRegister::PAYMENT_COLUMNS);
    }

    public function query()
    {
        $query = PaymentRegister::where('tenant_id', $this->tenantId)
            ->with('academicYear')
            ->orderBy('entry_date');

        if ($this->academicYearId) {
            $query->where('academic_year_id', $this->academicYearId);
        }
        if ($this->dateFrom) {
            $query->whereDate('entry_date', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('entry_date', '<=', $this->dateTo);
        }

        return $query;
    }

    public function headings(): array
    {
        $heads = ['S.No', 'Date'];
        foreach (PaymentRegister::PAYMENT_COLUMNS as $label) {
            $heads[] = $label;
        }
        $heads[] = 'Total';
        $heads[] = 'Remarks';
        return $heads;
    }

    public function map($entry): array
    {
        static $sno = 0;
        $sno++;

        $row = [
            $sno,
            $entry->entry_date?->format('d/m/Y'),
        ];

        foreach ($this->columnKeys as $col) {
            $row[] = (float) $entry->{$col};
        }

        $row[] = (float) $entry->total;
        $row[] = $entry->remarks;

        return $row;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '1E40AF']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }

    public function title(): string
    {
        return 'Payments Register';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet      = $event->sheet->getDelegate();
                $lastRow    = $sheet->getHighestRow();
                $lastCol    = $sheet->getHighestColumn();

                // Add totals row at bottom
                $totalsRow  = $lastRow + 1;
                $sheet->setCellValue("A{$totalsRow}", 'TOTAL');
                $sheet->getStyle("A{$totalsRow}")->getFont()->setBold(true);

                // Sum each numeric column (C onwards = payment columns + total)
                $colIndex = 3; // Column C is the first payment column
                foreach ($this->columnKeys as $col) {
                    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                    $sheet->setCellValue("{$colLetter}{$totalsRow}", "=SUM({$colLetter}2:{$colLetter}{$lastRow})");
                    $sheet->getStyle("{$colLetter}{$totalsRow}")->getFont()->setBold(true);
                    $colIndex++;
                }

                // Total column sum
                $totalColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                $sheet->setCellValue("{$totalColLetter}{$totalsRow}", "=SUM({$totalColLetter}2:{$totalColLetter}{$lastRow})");
                $sheet->getStyle("{$totalColLetter}{$totalsRow}")->getFont()->setBold(true);

                // Style totals row
                $sheet->getStyle("A{$totalsRow}:{$lastCol}{$totalsRow}")->applyFromArray([
                    'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'DBEAFE']],
                ]);
            },
        ];
    }
}
