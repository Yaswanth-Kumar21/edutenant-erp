<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Branch;
use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Services\AttendanceService;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    // ═══════════════════════════════════════════════════════════════════════════
    // STUDENT ATTENDANCE
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Student attendance marking page.
     */
    public function students(Request $request)
    {
        $tenantId = TenantService::getTenantId();
        $branches = Branch::where('tenant_id', $tenantId)->with('course')->orderBy('name')->get();
        $students = collect();
        $existingAttendance = collect();
        $date     = $request->get('date', today()->toDateString());
        $branchId = $request->get('branch_id');

        if ($branchId && $date) {
            $students = Student::where('tenant_id', $tenantId)
                ->where('branch_id', $branchId)
                ->where('status', 'active')
                ->orderBy('first_name')
                ->get();

            $existingAttendance = StudentAttendance::where('tenant_id', $tenantId)
                ->where('branch_id', $branchId)
                ->whereDate('attendance_date', $date)
                ->pluck('status', 'student_id');
        }

        // Today's summary stats
        $todayStats = AttendanceService::getTenantStats($tenantId, $date);

        return view('admin.attendance.students', compact(
            'branches', 'students', 'existingAttendance', 'date', 'branchId', 'todayStats'
        ));
    }

    /**
     * Mark / update student attendance.
     */
    public function markStudents(Request $request)
    {
        $request->validate([
            'branch_id'       => 'required|exists:branches,id',
            'attendance_date' => 'required|date',
            'attendance'      => 'required|array',
            'attendance.*'    => 'required|in:present,absent,late,holiday',
        ]);

        $tenantId = TenantService::getTenantId();
        $branch   = Branch::where('tenant_id', $tenantId)->findOrFail($request->branch_id);

        // Get semester from first student in branch
        $semester = Student::where('branch_id', $branch->id)->value('current_semester') ?? 1;

        $count = 0;
        foreach ($request->attendance as $studentId => $status) {
            StudentAttendance::updateOrCreate(
                [
                    'student_id'      => $studentId,
                    'attendance_date' => $request->attendance_date,
                    'subject'         => 'General',
                ],
                [
                    'tenant_id'       => $tenantId,
                    'branch_id'       => $request->branch_id,
                    'marked_by'       => auth()->id(),
                    'semester'        => $semester,
                    'status'          => $status,
                ]
            );
            $count++;
        }

        return redirect()->back()->with('success', "Attendance marked for {$count} students.");
    }

    /**
     * Student attendance report — monthly summary per branch.
     */
    public function studentReport(Request $request)
    {
        $tenantId = TenantService::getTenantId();
        $branches = Branch::where('tenant_id', $tenantId)->with('course')->orderBy('name')->get();
        $month    = (int) $request->get('month', now()->month);
        $year     = (int) $request->get('year', now()->year);
        $branchId = $request->get('branch_id');
        $report   = [];

        if ($branchId) {
            $report = AttendanceService::getMonthlyReport($tenantId, $branchId, $month, $year);
        }

        // Monthly working days (exclude Sundays)
        $workingDays = $this->countWorkingDays($month, $year);

        return view('admin.attendance.student-report', compact(
            'branches', 'month', 'year', 'branchId', 'report', 'workingDays'
        ));
    }

    /**
     * Student attendance analytics dashboard.
     */
    public function studentAnalytics(Request $request)
    {
        $tenantId = TenantService::getTenantId();
        $month    = (int) $request->get('month', now()->month);
        $year     = (int) $request->get('year', now()->year);

        // Branch-wise attendance percentage
        $branchStats = DB::table('student_attendance as sa')
            ->join('branches as b', 'sa.branch_id', '=', 'b.id')
            ->where('sa.tenant_id', $tenantId)
            ->whereMonth('sa.attendance_date', $month)
            ->whereYear('sa.attendance_date', $year)
            ->select(
                'b.name as branch',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN sa.status = "present" THEN 1 ELSE 0 END) as present'),
                DB::raw('SUM(CASE WHEN sa.status = "absent" THEN 1 ELSE 0 END) as absent')
            )
            ->groupBy('b.id', 'b.name')
            ->get();

        // Daily trend for the month
        $dailyTrend = DB::table('student_attendance')
            ->where('tenant_id', $tenantId)
            ->whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year)
            ->select(
                DB::raw('DATE(attendance_date) as date'),
                DB::raw('SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present'),
                DB::raw('SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy(DB::raw('DATE(attendance_date)'))
            ->orderBy('date')
            ->get();

        // Overall stats
        $overallStats = [
            'total_records' => StudentAttendance::where('tenant_id', $tenantId)
                ->whereMonth('attendance_date', $month)->whereYear('attendance_date', $year)->count(),
            'present'       => StudentAttendance::where('tenant_id', $tenantId)
                ->whereMonth('attendance_date', $month)->whereYear('attendance_date', $year)
                ->where('status', 'present')->count(),
            'absent'        => StudentAttendance::where('tenant_id', $tenantId)
                ->whereMonth('attendance_date', $month)->whereYear('attendance_date', $year)
                ->where('status', 'absent')->count(),
        ];
        $overallStats['percentage'] = $overallStats['total_records'] > 0
            ? round(($overallStats['present'] / $overallStats['total_records']) * 100, 1)
            : 0;

        return view('admin.attendance.student-analytics', compact(
            'branchStats', 'dailyTrend', 'overallStats', 'month', 'year'
        ));
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // STAFF ATTENDANCE
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Staff attendance marking page.
     */
    public function staff(Request $request)
    {
        $tenantId  = TenantService::getTenantId();
        $date      = $request->get('date', today()->toDateString());
        $staffList = Staff::where('tenant_id', $tenantId)->where('status', 'active')->orderBy('name')->get();

        $existingAttendance = StaffAttendance::where('tenant_id', $tenantId)
            ->whereDate('attendance_date', $date)
            ->pluck('status', 'staff_id');

        $todayStats = AttendanceService::getTenantStats($tenantId, $date);

        return view('admin.attendance.staff', compact(
            'staffList', 'existingAttendance', 'date', 'todayStats'
        ));
    }

    /**
     * Mark / update staff attendance.
     */
    public function markStaff(Request $request)
    {
        $request->validate([
            'attendance_date' => 'required|date',
            'attendance'      => 'required|array',
            'attendance.*'    => 'required|in:present,absent,holiday,half_day,leave',
        ]);

        $tenantId = TenantService::getTenantId();
        $count    = 0;

        foreach ($request->attendance as $staffId => $status) {
            StaffAttendance::updateOrCreate(
                ['staff_id' => $staffId, 'attendance_date' => $request->attendance_date],
                ['tenant_id' => $tenantId, 'marked_by' => auth()->id(), 'status' => $status]
            );
            $count++;
        }

        return redirect()->back()->with('success', "Staff attendance marked for {$count} members.");
    }

    /**
     * Staff attendance monthly report.
     */
    public function staffReport(Request $request)
    {
        $tenantId  = TenantService::getTenantId();
        $month     = (int) $request->get('month', now()->month);
        $year      = (int) $request->get('year', now()->year);
        $staffList = Staff::where('tenant_id', $tenantId)->where('status', 'active')->orderBy('name')->get();

        $report = $staffList->map(function ($staff) use ($month, $year) {
            return array_merge(
                ['staff' => $staff],
                AttendanceService::getStaffMonthlySummary($staff->id, $month, $year)
            );
        });

        $workingDays = $this->countWorkingDays($month, $year);

        return view('admin.attendance.staff-report', compact(
            'report', 'month', 'year', 'workingDays'
        ));
    }

    // ─── Private Helpers ──────────────────────────────────────────────────────

    private function countWorkingDays(int $month, int $year): int
    {
        $start = \Carbon\Carbon::createFromDate($year, $month, 1);
        $end   = $start->copy()->endOfMonth();
        $days  = 0;
        while ($start->lte($end)) {
            if (!$start->isSunday()) $days++;
            $start->addDay();
        }
        return $days;
    }
}
