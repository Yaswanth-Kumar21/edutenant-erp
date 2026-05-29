<?php

namespace App\Services;

use App\Models\FeePayment;
use App\Models\Staff;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

/**
 * DashboardService
 *
 * Aggregates statistics for the admin dashboard.
 */
class DashboardService
{
    public static function getStats(int $tenantId): array
    {
        return [
            'total_students'     => Student::where('tenant_id', $tenantId)->where('status', 'active')->count(),
            'total_staff'        => Staff::where('tenant_id', $tenantId)->where('status', 'active')->count(),
            'total_teaching'     => Staff::where('tenant_id', $tenantId)->where('staff_type', 'teaching')->where('status', 'active')->count(),
            'total_non_teaching' => Staff::where('tenant_id', $tenantId)->where('staff_type', 'non_teaching')->where('status', 'active')->count(),
            'fees_this_month'    => FeePayment::where('tenant_id', $tenantId)
                ->whereMonth('payment_date', now()->month)
                ->whereYear('payment_date', now()->year)
                ->where('status', 'paid')
                ->sum('amount_paid'),
            'fees_today'         => FeePayment::where('tenant_id', $tenantId)
                ->whereDate('payment_date', today())
                ->where('status', 'paid')
                ->sum('amount_paid'),
        ];
    }

    public static function getMonthlyFees(int $tenantId): \Illuminate\Support\Collection
    {
        return FeePayment::where('tenant_id', $tenantId)
            ->where('status', 'paid')
            ->where('payment_date', '>=', now()->subMonths(6))
            ->select(
                DB::raw('MONTH(payment_date) as month'),
                DB::raw('YEAR(payment_date) as year'),
                DB::raw('SUM(amount_paid) as total')
            )
            ->groupBy(DB::raw('YEAR(payment_date)'), DB::raw('MONTH(payment_date)'))
            ->orderBy('year')->orderBy('month')
            ->get();
    }
}
