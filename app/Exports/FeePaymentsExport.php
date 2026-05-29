<?php

namespace App\Exports;

use App\Models\FeePayment;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FeePaymentsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    public function __construct(
        private readonly int $tenantId,
        private readonly ?string $status = null,
        private readonly ?int $academicYearId = null,
        private readonly ?string $dateFrom = null,
        private readonly ?string $dateTo = null
    ) {}

    public function query()
    {
        $query = FeePayment::where('tenant_id', $this->tenantId)
            ->with(['student', 'feeType', 'academicYear', 'collectedBy'])
            ->orderByDesc('payment_date');

        if ($this->status)         $query->where('status', $this->status);
        if ($this->academicYearId) $query->where('academic_year_id', $this->academicYearId);
        if ($this->dateFrom)       $query->whereDate('payment_date', '>=', $this->dateFrom);
        if ($this->dateTo)         $query->whereDate('payment_date', '<=', $this->dateTo);

        return $query;
    }

    public function headings(): array
    {
        return [
            'Receipt No', 'Student Name', 'Admission No',
            'Fee Type', 'Academic Year', 'Semester',
            'Amount Due', 'Amount Paid', 'Discount', 'Fine', 'Balance',
            'Payment Mode', 'Transaction Ref', 'Payment Date',
            'Status', 'Collected By',
        ];
    }

    public function map($payment): array
    {
        return [
            $payment->receipt_number,
            $payment->student?->full_name,
            $payment->student?->admission_number,
            $payment->feeType?->name,
            $payment->academicYear?->name,
            $payment->semester,
            $payment->amount_due,
            $payment->amount_paid,
            $payment->discount,
            $payment->fine,
            $payment->balance,
            ucfirst($payment->payment_mode),
            $payment->transaction_reference,
            $payment->payment_date?->format('d/m/Y'),
            ucfirst($payment->status),
            $payment->collectedBy?->name,
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
        return 'Fee Payments';
    }
}
