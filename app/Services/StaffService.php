<?php

namespace App\Services;

use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Models\Payroll;
use Illuminate\Support\Facades\DB;

/**
 * StaffService
 *
 * Business logic for staff management and salary calculations.
 */
class StaffService
{
    /**
     * Get staff statistics for a tenant.
     */
    public static function getStats(int $tenantId): array
    {
        return [
            'total'        => Staff::where('tenant_id', $tenantId)->count(),
            'active'       => Staff::where('tenant_id', $tenantId)->where('status', 'active')->count(),
            'teaching'     => Staff::where('tenant_id', $tenantId)->where('staff_type', 'teaching')->where('status', 'active')->count(),
            'non_teaching' => Staff::where('tenant_id', $tenantId)->where('staff_type', 'non_teaching')->where('status', 'active')->count(),
        ];
    }

    /**
     * Calculate net salary for a staff member based on attendance.
     * Logic: 30-day month, 2 holidays allowed per month.
     */
    public static function calculateNetSalary(Staff $staff, int $month, int $year): array
    {
        $totalDays     = $staff->salary_calculation_days; // 30
        $allowedLeaves = $staff->allowed_holidays_per_month; // 2
        $perDaySalary  = $staff->monthly_salary / $totalDays;

        // Count attendance for the month
        $records = StaffAttendance::where('staff_id', $staff->id)
            ->whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year)
            ->get();

        $presentDays = $records->whereIn('status', ['present'])->count();
        $halfDays    = $records->where('status', 'half_day')->count();
        $leaveDays   = $records->where('status', 'leave')->count();
        $absentDays  = $records->where('status', 'absent')->count();
        $holidayDays = $records->where('status', 'holiday')->count();

        // Effective present = present + half_day*0.5 + min(leave, allowed_leaves)
        $effectivePresent = $presentDays + ($halfDays * 0.5) + min($leaveDays, $allowedLeaves);
        $effectivePresent = min($effectivePresent, $totalDays);

        $grossSalary    = round($perDaySalary * $effectivePresent, 2);
        $deductionDays  = max(0, $absentDays - max(0, $allowedLeaves - $leaveDays));
        $deductionAmt   = round($perDaySalary * $deductionDays, 2);
        $netSalary      = max(0, $grossSalary - $deductionAmt);

        return [
            'gross_salary'    => $staff->monthly_salary,
            'present_days'    => $presentDays,
            'half_days'       => $halfDays,
            'leave_days'      => $leaveDays,
            'absent_days'     => $absentDays,
            'holiday_days'    => $holidayDays,
            'allowed_leaves'  => $allowedLeaves,
            'deduction_days'  => $deductionDays,
            'deduction_amt'   => $deductionAmt,
            'net_salary'      => $netSalary,
            'per_day_salary'  => round($perDaySalary, 2),
            'working_days'    => $totalDays,
        ];
    }

    /**
     * Generate a unique staff code.
     */
    public static function generateStaffCode(int $tenantId): string
    {
        $last = Staff::where('tenant_id', $tenantId)
            ->whereNotNull('staff_code')
            ->latest('id')
            ->first();

        $next = $last
            ? ((int) substr($last->staff_code, -4)) + 1
            : 1;

        return 'STF-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
