<?php

namespace App\Services;

use App\Models\FeePayment;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

/**
 * FeeAnalyticsService
 *
 * Provides all analytics data for the fee dashboard.
 */
class FeeAnalyticsService
{
    public function getDashboardStats(int $tenantId, ?int $yearId): array
    {
        $baseQuery = FeePayment::where('tenant_id', $tenantId);
        if ($yearId) $baseQuery->where('academic_year_id', $yearId);

        $totalCollected    = (clone $baseQuery)->where('status', 'paid')->sum('amount_paid');
        $totalPending      = (clone $baseQuery)->whereIn('status', ['pending', 'partial'])->sum('amount_due');
        $todayCollected    = (clone $baseQuery)->where('status', 'paid')->whereDate('payment_date', today())->sum('amount_paid');
        $monthCollected    = (clone $baseQuery)->where('status', 'paid')->whereMonth('payment_date', now()->month)->whereYear('payment_date', now()->year)->sum('amount_paid');
        $totalTransactions = (clone $baseQuery)->count();
        $exemptedCount     = (clone $baseQuery)->where('is_exempted', true)->count();

        return compact('totalCollected', 'totalPending', 'todayCollected', 'monthCollected', 'totalTransactions', 'exemptedCount');
    }

    public function getMonthlyCollection(int $tenantId, int $months = 12): array
    {
        $rows = FeePayment::where('tenant_id', $tenantId)
            ->where('status', 'paid')
            ->where('payment_date', '>=', now()->subMonths($months)->startOfMonth())
            ->select(
                DB::raw('YEAR(payment_date) as yr'),
                DB::raw('MONTH(payment_date) as mo'),
                DB::raw('SUM(amount_paid) as total')
            )
            ->groupBy(DB::raw('YEAR(payment_date)'), DB::raw('MONTH(payment_date)'))
            ->orderBy('yr')
            ->orderBy('mo')
            ->get();

        return $rows->map(fn($r) => [
            'month' => \Carbon\Carbon::createFromDate($r->yr, $r->mo, 1)->format('M Y'),
            'total' => (float) $r->total,
        ])->toArray();
    }

    public function getBranchCollection(int $tenantId, ?int $yearId): array
    {
        $query = FeePayment::where('fee_payments.tenant_id', $tenantId)
            ->where('fee_payments.status', 'paid')
            ->join('students', 'fee_payments.student_id', '=', 'students.id')
            ->join('branches', 'students.branch_id', '=', 'branches.id')
            ->select('branches.name as branch', DB::raw('SUM(fee_payments.amount_paid) as total'))
            ->groupBy('branches.name')
            ->orderByDesc('total');

        if ($yearId) $query->where('fee_payments.academic_year_id', $yearId);

        return $query->get()->map(fn($r) => ['branch' => $r->branch, 'total' => (float) $r->total])->toArray();
    }

    public function getCategoryCollection(int $tenantId, ?int $yearId): array
    {
        $query = FeePayment::where('fee_payments.tenant_id', $tenantId)
            ->where('fee_payments.status', 'paid')
            ->join('students', 'fee_payments.student_id', '=', 'students.id')
            ->select('students.category', DB::raw('SUM(fee_payments.amount_paid) as total'))
            ->groupBy('students.category')
            ->orderByDesc('total');

        if ($yearId) $query->where('fee_payments.academic_year_id', $yearId);

        return $query->get()->map(fn($r) => ['category' => $r->category, 'total' => (float) $r->total])->toArray();
    }

    public function getPendingDues(int $tenantId, ?int $yearId, int $limit = 10): \Illuminate\Support\Collection
    {
        $query = FeePayment::where('tenant_id', $tenantId)
            ->whereIn('status', ['pending', 'partial'])
            ->with(['student.branch', 'feeType'])
            ->orderByDesc('amount_due');

        if ($yearId) $query->where('academic_year_id', $yearId);

        return $query->limit($limit)->get();
    }

    public function getStudentFeeSummary(Student $student): array
    {
        $payments = $student->feePayments;

        return [
            'total_due'      => $payments->sum('amount_due'),
            'total_paid'     => $payments->where('status', 'paid')->sum('amount_paid'),
            'total_pending'  => $payments->whereIn('status', ['pending', 'partial'])->sum('amount_due'),
            'total_exempted' => $payments->where('is_exempted', true)->count(),
            'payment_count'  => $payments->count(),
            'last_payment'   => $payments->sortByDesc('payment_date')->first(),
        ];
    }
}
