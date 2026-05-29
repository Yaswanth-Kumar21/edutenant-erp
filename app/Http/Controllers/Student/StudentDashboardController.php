<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\TenantService;

/**
 * StudentDashboardController
 *
 * Handles the student-facing portal dashboard.
 * Only accessible to users with the 'student' role (enforced by route middleware).
 * Students can ONLY see their own personal data — never admin analytics.
 */
class StudentDashboardController extends Controller
{
    public function index()
    {
        $user    = auth()->user();
        $student = $user->student;

        // Safety check — if somehow a non-student reaches here
        if (!$student) {
            abort(403, 'No student profile linked to this account. Please contact the college admin.');
        }

        $student->load([
            'branch.course.stream',
            'academicYear',
            'feePayments.feeType',
            'attendance',
            'certificates',
            'guardian',
            'admissionReceipts' => fn($q) => $q->latest()->limit(1),
        ]);

        $tenant = TenantService::getTenant();

        // ── Fee summary (own fees only) ───────────────────────────────────
        $totalFeesPaid  = $student->total_fees_paid;
        $totalFeesDue   = $student->feePayments->whereIn('status', ['pending', 'partial'])->sum('amount_due');
        $recentPayments = $student->feePayments()->with('feeType')->latest('payment_date')->limit(5)->get();

        // ── Attendance summary (own attendance only) ──────────────────────
        $attendancePercent = $student->attendance_percentage;
        $totalDays         = $student->attendance->count();
        $presentDays       = $student->attendance->where('status', 'present')->count();
        $absentDays        = $student->attendance->where('status', 'absent')->count();

        // ── Certificates ──────────────────────────────────────────────────
        $certCount = $student->certificates->count();

        return view('student.dashboard', compact(
            'student', 'tenant',
            'totalFeesPaid', 'totalFeesDue', 'recentPayments',
            'attendancePercent', 'totalDays', 'presentDays', 'absentDays',
            'certCount'
        ));
    }
}
