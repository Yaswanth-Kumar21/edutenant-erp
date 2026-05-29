<?php

namespace App\Exports;

use App\Models\Expense;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExpensesExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    public function __construct(
        private readonly int $tenantId,
        private readonly ?string $dateFrom = null,
        private readonly ?string $dateTo = null
    ) {}

    public function query()
    {
        $query = Expense::where('tenant_id', $this->tenantId)
            ->with(['expenseCategory', 'recordedBy'])
            ->orderByDesc('expense_date');

        if ($this->dateFrom) $query->whereDate('expense_date', '>=', $this->dateFrom);
        if ($this->dateTo)   $query->whereDate('expense_date', '<=', $this->dateTo);

        return $query;
    }

    public function headings(): array
    {
        return [
            'Date', 'Title', 'Category', 'Amount',
            'Payment Mode', 'Bill Number', 'Vendor', 'Description', 'Recorded By',
        ];
    }

    public function map($expense): array
    {
        return [
            $expense->expense_date?->format('d/m/Y'),
            $expense->title,
            $expense->expenseCategory?->name,
            $expense->amount,
            ucfirst($expense->payment_mode ?? ''),
            $expense->bill_number,
            $expense->vendor_name,
            $expense->description,
            $expense->recordedBy?->name,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => 'DC2626']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }

    public function title(): string
    {
        return 'Expenses';
    }
}
