<?php

namespace App\Services;

use App\Models\StaffAttendance;
use App\Models\StudentAttendance;
use Illuminate\Support\Facades\DB;

/**
 * AttendanceService
 *
 * Business logic for attendance marking, reporting, and analytics.
 */
class AttendanceService
{
    /**
     * Get attendance summary for a single student.
     */
    public static function getStudentSummary(int $studentId, ?int $month = null, ?int $year = null): array
    {
        $query = StudentAttendance::where('student_id', $studentId);

        if ($month) $query->whereMonth('attendance_date', $month);
        if ($year)  $query->whereYear('attendance_date', $year);

        $records = $query->get();
        $total   = $records->count();
        $present = $records->where('status', 'present')->count();
        $absent  = $records->where('status', 'absent')->count();
        $late    = $records->where('status', 'late')->count();

        return [
            'total'      => $total,
            'present'    => $present,
            'absent'     => $absent,
            'late'       => $late,
            'percentage' => $total > 0 ? round(($present / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Get attendance summary for a branch on a given date.
     */
    public static function getBranchDailySummary(int $branchId, string $date): array
    {
        $records = StudentAttendance::where('branch_id', $branchId)
            ->whereDate('attendance_date', $date)
            ->get();

        $total   = $records->count();
        $present = $records->where('status', 'present')->count();
        $absent  = $records->where('status', 'absent')->count();
        $late    = $records->where('status', 'late')->count();

        return [
            'total'      => $total,
            'present'    => $present,
            'absent'     => $absent,
            'late'       => $late,
            'percentage' => $total > 0 ? round(($present / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Get monthly attendance report for a branch.
     */
    public static function getMonthlyReport(int $tenantId, int $branchId, int $month, int $year): array
    {
        $records = StudentAttendance::where('tenant_id', $tenantId)
            ->where('branch_id', $branchId)
            ->whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year)
            ->with('student')
            ->get();

        $byStudent = $records->groupBy('student_id');

        return $byStudent->map(function ($studentRecords) {
            $total   = $studentRecords->count();
            $present = $studentRecords->where('status', 'present')->count();
            $absent  = $studentRecords->where('status', 'absent')->count();
            $late    = $studentRecords->where('status', 'late')->count();

            return [
                'student'    => $studentRecords->first()->student,
                'total'      => $total,
                'present'    => $present,
                'absent'     => $absent,
                'late'       => $late,
                'percentage' => $total > 0 ? round(($present / $total) * 100, 2) : 0,
            ];
        })->values()->toArray();
    }

    /**
     * Get staff attendance summary for a month.
     */
    public static function getStaffMonthlySummary(int $staffId, int $month, int $year): array
    {
        $records = StaffAttendance::where('staff_id', $staffId)
            ->whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year)
            ->get();

        $total    = $records->count();
        $present  = $records->whereIn('status', ['present', 'half_day'])->count();
        $absent   = $records->where('status', 'absent')->count();
        $leave    = $records->where('status', 'leave')->count();
        $holiday  = $records->where('status', 'holiday')->count();
        $halfDay  = $records->where('status', 'half_day')->count();

        return [
            'total'      => $total,
            'present'    => $present,
            'absent'     => $absent,
            'leave'      => $leave,
            'holiday'    => $holiday,
            'half_day'   => $halfDay,
            'percentage' => $total > 0 ? round(($present / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Get tenant-wide attendance analytics for dashboard.
     */
    public static function getTenantStats(int $tenantId, string $date): array
    {
        $studentRecords = StudentAttendance::where('tenant_id', $tenantId)
            ->whereDate('attendance_date', $date)
            ->get();

        $staffRecords = StaffAttendance::where('tenant_id', $tenantId)
            ->whereDate('attendance_date', $date)
            ->get();

        return [
            'students_present' => $studentRecords->where('status', 'present')->count(),
            'students_absent'  => $studentRecords->where('status', 'absent')->count(),
            'students_total'   => $studentRecords->count(),
            'staff_present'    => $staffRecords->whereIn('status', ['present', 'half_day'])->count(),
            'staff_absent'     => $staffRecords->where('status', 'absent')->count(),
            'staff_total'      => $staffRecords->count(),
        ];
    }
}
