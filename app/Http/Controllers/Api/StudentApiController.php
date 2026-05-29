<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FeePayment;
use App\Models\Message;
use App\Models\StudentAttendance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * StudentApiController
 *
 * Mobile API endpoints for the student portal.
 * All endpoints require Sanctum token auth + student role.
 * Strict tenant isolation enforced on every query.
 */
class StudentApiController extends Controller
{
    /**
     * GET /api/v1/student/dashboard
     */
    public function dashboard(Request $request): JsonResponse
    {
        $user    = $request->user();
        $student = $user->student;

        if (!$student) {
            return response()->json(['message' => 'No student profile found.'], 404);
        }

        $student->load(['branch.course.stream', 'academicYear']);

        $totalPaid = FeePayment::where('student_id', $student->id)
            ->where('status', 'paid')->sum('amount_paid');

        $totalDue = FeePayment::where('student_id', $student->id)
            ->whereIn('status', ['pending', 'partial'])->sum('amount_due');

        $totalDays    = StudentAttendance::where('student_id', $student->id)->count();
        $presentDays  = StudentAttendance::where('student_id', $student->id)->where('status', 'present')->count();
        $attendancePct = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 1) : 0;

        $certCount = $student->certificates()->count();

        return response()->json([
            'student' => [
                'id'               => $student->id,
                'admission_number' => $student->admission_number,
                'full_name'        => $student->full_name,
                'photo_url'        => $student->photo_url,
                'branch'           => $student->branch?->name,
                'course'           => $student->branch?->course?->name,
                'semester'         => $student->current_semester,
                'academic_year'    => $student->academicYear?->name,
                'category'         => $student->category,
                'status'           => $student->status,
            ],
            'stats' => [
                'total_fees_paid'    => (float) $totalPaid,
                'total_fees_due'     => (float) $totalDue,
                'attendance_percent' => $attendancePct,
                'present_days'       => $presentDays,
                'total_days'         => $totalDays,
                'certificates_count' => $certCount,
            ],
        ]);
    }

    /**
     * GET /api/v1/student/fees
     */
    public function fees(Request $request): JsonResponse
    {
        $student = $request->user()->student;

        if (!$student) {
            return response()->json(['message' => 'No student profile found.'], 404);
        }

        $payments = FeePayment::where('student_id', $student->id)
            ->where('tenant_id', $student->tenant_id)
            ->with(['feeType', 'academicYear'])
            ->latest('payment_date')
            ->paginate(20);

        return response()->json([
            'data' => $payments->map(fn($p) => [
                'id'             => $p->id,
                'receipt_number' => $p->receipt_number,
                'fee_type'       => $p->feeType?->name,
                'academic_year'  => $p->academicYear?->name,
                'semester'       => $p->semester,
                'amount_due'     => (float) $p->amount_due,
                'amount_paid'    => (float) $p->amount_paid,
                'balance'        => (float) $p->balance,
                'payment_mode'   => $p->payment_mode,
                'payment_date'   => $p->payment_date?->format('Y-m-d'),
                'status'         => $p->status,
            ]),
            'meta' => [
                'total'        => $payments->total(),
                'current_page' => $payments->currentPage(),
                'last_page'    => $payments->lastPage(),
                'total_paid'   => FeePayment::where('student_id', $student->id)->where('status', 'paid')->sum('amount_paid'),
                'total_due'    => FeePayment::where('student_id', $student->id)->whereIn('status', ['pending', 'partial'])->sum('amount_due'),
            ],
        ]);
    }

    /**
     * GET /api/v1/student/attendance
     */
    public function attendance(Request $request): JsonResponse
    {
        $student = $request->user()->student;

        if (!$student) {
            return response()->json(['message' => 'No student profile found.'], 404);
        }

        $month = $request->get('month'); // format: Y-m

        $query = StudentAttendance::where('student_id', $student->id)
            ->where('tenant_id', $student->tenant_id)
            ->orderBy('attendance_date');

        if ($month) {
            [$y, $m] = explode('-', $month);
            $query->whereYear('attendance_date', $y)->whereMonth('attendance_date', $m);
        } else {
            $query->whereYear('attendance_date', now()->year);
        }

        $records = $query->get();

        $totalDays   = $records->count();
        $presentDays = $records->where('status', 'present')->count();
        $absentDays  = $records->where('status', 'absent')->count();
        $percentage  = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 1) : 0;

        return response()->json([
            'summary' => [
                'total_days'   => $totalDays,
                'present_days' => $presentDays,
                'absent_days'  => $absentDays,
                'percentage'   => $percentage,
            ],
            'records' => $records->map(fn($r) => [
                'date'    => $r->attendance_date?->format('Y-m-d'),
                'status'  => $r->status,
                'subject' => $r->subject,
                'remarks' => $r->remarks,
            ]),
        ]);
    }

    /**
     * GET /api/v1/student/notifications
     */
    public function notifications(Request $request): JsonResponse
    {
        $user    = $request->user();
        $student = $user->student;

        if (!$student) {
            return response()->json(['message' => 'No student profile found.'], 404);
        }

        // Get messages targeted to this student or their branch
        $messages = Message::where('tenant_id', $student->tenant_id)
            ->where(function ($q) use ($student) {
                $q->where('recipient_type', 'all')
                  ->orWhere('student_id', $student->id)
                  ->orWhere('branch_id', $student->branch_id);
            })
            ->latest()
            ->limit(20)
            ->get();

        // Build smart notifications
        $notifications = [];

        // Fee due alert
        $totalDue = FeePayment::where('student_id', $student->id)
            ->whereIn('status', ['pending', 'partial'])->sum('amount_due');
        if ($totalDue > 0) {
            $notifications[] = [
                'type'    => 'fee_due',
                'title'   => 'Fee Due',
                'message' => '₹' . number_format($totalDue) . ' pending. Please pay at the office.',
                'icon'    => 'warning',
                'date'    => now()->format('Y-m-d'),
            ];
        }

        // Low attendance alert
        $totalDays   = StudentAttendance::where('student_id', $student->id)->count();
        $presentDays = StudentAttendance::where('student_id', $student->id)->where('status', 'present')->count();
        $pct = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 1) : 0;
        if ($pct > 0 && $pct < 75) {
            $notifications[] = [
                'type'    => 'low_attendance',
                'title'   => 'Low Attendance',
                'message' => "Your attendance is {$pct}%. Minimum 75% required.",
                'icon'    => 'danger',
                'date'    => now()->format('Y-m-d'),
            ];
        }

        // Messages from admin
        foreach ($messages as $msg) {
            $notifications[] = [
                'type'    => 'message',
                'title'   => $msg->subject,
                'message' => $msg->body,
                'icon'    => 'info',
                'date'    => $msg->sent_at?->format('Y-m-d') ?? $msg->created_at?->format('Y-m-d'),
            ];
        }

        return response()->json(['notifications' => $notifications]);
    }
}
