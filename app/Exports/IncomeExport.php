<?php

namespace App\Exports;

use App\Models\Income;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class IncomeExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    public function __construct(
        private readonly int $tenantId,
        private readonly ?string $dateFrom = null,
        private readonly ?string $dateTo   = null
    ) {}

    public function query()
    {
        $query = Income::where('tenant_id', $this->tenantId)
            ->with(['incomeCategory', 'recordedBy'])
            ->orderByDesc('income_date');

        if ($this->dateFrom) $query->whereDate('income_date', '>=', $this->dateFrom);
        if ($this->dateTo)   $query->whereDate('income_date', '<=', $this->dateTo);

        return $query;
    }

    public function headings(): array
    {
        return [
            'Date', 'Title', 'Category', 'Amount',
            'Payment Mode', 'Reference Number', 'Description', 'Recorded By',
        ];
    }

    public function map($income): array
    {
        return [
            $income->income_date?->format('d/m/Y'),
            $income->title,
            $income->incomeCategory?->name,
            $income->amount,
            ucfirst($income->payment_mode ?? ''),
            $income->reference_number,
            $income->description,
            $income->recordedBy?->name,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '059669']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }

    public function title(): string
    {
        return 'Income';
    }
}
