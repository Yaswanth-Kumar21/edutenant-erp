<?php

namespace App\Services;

use App\Helpers\NumberHelper;
use App\Models\FeePayment;
use Illuminate\Support\Facades\DB;

/**
 * FeeCollectionService
 *
 * Handles all fee payment collection business logic.
 * Generates receipt numbers, calculates balances, records transactions.
 */
class FeeCollectionService
{
    /**
     * Generate a unique tenant-scoped receipt number.
     * Format: RCP-YYYY-NNNNN  e.g. RCP-2026-00042
     */
    public static function generateReceiptNumber(int $tenantId): string
    {
        return DB::transaction(function () use ($tenantId) {
            $year = now()->year;

            $last = FeePayment::where('tenant_id', $tenantId)
                ->where('receipt_number', 'like', "RCP-{$year}-%")
                ->lockForUpdate()
                ->latest('id')
                ->first();

            $next = $last
                ? ((int) substr($last->receipt_number, strrpos($last->receipt_number, '-') + 1)) + 1
                : 1;

            return 'RCP-' . $year . '-' . NumberHelper::zeroPad($next, 5);
        });
    }

    /**
     * Record a fee payment.
     */
    public function collectPayment(array $data, int $tenantId, int $userId): FeePayment
    {
        return DB::transaction(function () use ($data, $tenantId, $userId) {

            $amountDue  = (float) ($data['amount_due']  ?? 0);
            $amountPaid = (float) ($data['amount_paid'] ?? 0);
            $discount   = (float) ($data['discount']    ?? 0);
            $fine       = (float) ($data['fine']        ?? 0);

            $effectiveDue = max(0, $amountDue - $discount + $fine);
            $balance      = max(0, $effectiveDue - $amountPaid);

            $status = 'pending';
            if ($amountPaid >= $effectiveDue && $effectiveDue > 0) {
                $status = 'paid';
            } elseif ($amountPaid > 0) {
                $status = 'partial';
            }

            return FeePayment::create([
                'tenant_id'             => $tenantId,
                'student_id'            => $data['student_id'],
                'fee_type_id'           => $data['fee_type_id'],
                'academic_year_id'      => $data['academic_year_id'],
                'collected_by'          => $userId,
                'receipt_number'        => self::generateReceiptNumber($tenantId),
                'amount_due'            => $amountDue,
                'amount_paid'           => $amountPaid,
                'discount'              => $discount,
                'fine'                  => $fine,
                'semester'              => $data['semester'] ?? null,
                'year'                  => $data['year'] ?? null,
                'payment_mode'          => $data['payment_mode'] ?? 'cash',
                'transaction_reference' => $data['transaction_reference'] ?? null,
                'payment_date'          => $data['payment_date'] ?? now()->toDateString(),
                'remarks'               => $data['remarks'] ?? null,
                'status'                => $status,
                'is_exempted'           => false,
            ]);
        });
    }
}
