<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentAttendance;
use App\Services\TenantService;

/**
 * StudentAttendanceController
 *
 * Handles the student-facing attendance calendar and detail view.
 * Students can ONLY see their own attendance records.
 */
class StudentAttendanceController extends Controller
{
    public function index()
    {
        $user    = auth()->user();
        $student = $user->student;

        if (!$student) {
            abort(403, 'No student profile linked to this account.');
        }

        $tenant = TenantService::getTenant();

        // Get all attendance records for calendar
        $records = StudentAttendance::where('student_id', $student->id)
            ->where('tenant_id', $student->tenant_id)
            ->orderBy('attendance_date')
            ->get();

        // Monthly breakdown for the last 6 months
        $monthlyStats = [];
        for ($i = 5; $i >= 0; $i--) {
            $month     = now()->subMonths($i);
            $monthKey  = $month->format('Y-m');
            $monthRecs = $records->filter(fn($r) => $r->attendance_date->format('Y-m') === $monthKey);

            $monthlyStats[] = [
                'month'   => $month->format('M Y'),
                'total'   => $monthRecs->count(),
                'present' => $monthRecs->where('status', 'present')->count(),
                'absent'  => $monthRecs->where('status', 'absent')->count(),
                'late'    => $monthRecs->where('status', 'late')->count(),
            ];
        }

        // Calendar data — group by date for the current month
        $currentMonth = request('month', now()->format('Y-m'));
        [$year, $month] = explode('-', $currentMonth);

        $calendarRecords = $records->filter(
            fn($r) => $r->attendance_date->format('Y-m') === $currentMonth
        )->keyBy(fn($r) => $r->attendance_date->format('Y-m-d'));

        // Summary stats
        $totalDays    = $records->count();
        $presentDays  = $records->where('status', 'present')->count();
        $absentDays   = $records->where('status', 'absent')->count();
        $lateDays     = $records->where('status', 'late')->count();
        $percentage   = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 1) : 0;

        // Recent records (last 10)
        $recentRecords = $records->sortByDesc('attendance_date')->take(10);

        return view('student.attendance.index', compact(
            'student', 'tenant',
            'records', 'monthlyStats', 'calendarRecords',
            'currentMonth', 'year', 'month',
            'totalDays', 'presentDays', 'absentDays', 'lateDays', 'percentage',
            'recentRecords'
        ));
    }
}
