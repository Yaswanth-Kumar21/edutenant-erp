<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Services\TenantService;
use Illuminate\Http\Request;

/**
 * Student360Controller
 *
 * Powers the Student 360° Complete Profile module.
 * Accessible by Admin (full), Staff (view + limited edit), Teacher (read-only).
 *
 * Search by: Roll Number, Admission Number, or Student Name.
 * Displays the complete student lifecycle in a tabbed ERP dashboard.
 */
class Student360Controller extends Controller
{
    /**
     * Search page — entry point for the 360° view.
     */
    public function search(Request $request)
    {
        $student  = null;
        $results  = collect();
        $query    = $request->get('q', '');
        $tenantId = TenantService::getTenantId();

        if ($request->filled('q')) {
            $results = Student::where('tenant_id', $tenantId)
                ->where(function ($q) use ($query) {
                    $q->where('admission_number', 'like', "%{$query}%")
                      ->orWhere('first_name', 'like', "%{$query}%")
                      ->orWhere('last_name', 'like', "%{$query}%")
                      ->orWhereRaw("CONCAT(first_name,' ',last_name) LIKE ?", ["%{$query}%"])
                      ->orWhere('phone', 'like', "%{$query}%")
                      ->orWhere('university_reg_number', 'like', "%{$query}%");
                })
                ->with(['branch.course.stream', 'academicYear'])
                ->limit(10)
                ->get();

            // If exactly one result, redirect directly to the 360 view
            if ($results->count() === 1) {
                return redirect()->route('admin.students.360', $results->first());
            }
        }

        return view('admin.students.360-search', compact('results', 'query'));
    }

    /**
     * Full 360° profile view for a specific student.
     */
    public function show(Student $student)
    {
        $this->authorizeTenant($student);

        $student->load([
            'branch.course.stream',
            'academicYear',
            'guardian',
            'profile',
            'certificates',
            'user',
            'feePayments.feeType',
            'feePayments.collectedBy',
            'admissionReceipts',
            'attendance',
            'scholarships',
            'transport',
            'transportHistory',
        ]);

        $tenantId = TenantService::getTenantId();

        // ── Attendance Analytics ──────────────────────────────────────────
        $allAttendance = $student->attendance;
        $totalDays     = $allAttendance->count();
        $presentDays   = $allAttendance->where('status', 'present')->count();
        $absentDays    = $allAttendance->where('status', 'absent')->count();
        $attendancePct = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 1) : 0;

        // Semester-wise attendance
        $semesterAttendance = $allAttendance
            ->groupBy('semester')
            ->map(function ($records, $sem) {
                $total   = $records->count();
                $present = $records->where('status', 'present')->count();
                return [
                    'semester' => $sem,
                    'total'    => $total,
                    'present'  => $present,
                    'absent'   => $total - $present,
                    'pct'      => $total > 0 ? round(($present / $total) * 100, 1) : 0,
                ];
            })
            ->sortKeys()
            ->values();

        // ── Fee Analytics ─────────────────────────────────────────────────
        $feePayments    = $student->feePayments;
        $totalPaid      = $feePayments->where('status', 'paid')->sum('amount_paid');
        $totalDue       = $feePayments->whereIn('status', ['pending', 'partial'])->sum(fn($p) => $p->amount_due - $p->amount_paid);
        $totalDiscount  = $feePayments->sum('discount');

        // Scholarship total
        $govtScholarshipTotal    = $student->scholarships->where('scholarship_category', 'government')->sum('amount');
        $collegeScholarshipTotal = $student->scholarships->where('scholarship_category', 'college')->sum('amount');
        $totalScholarship        = $govtScholarshipTotal + $collegeScholarshipTotal;

        // Transport fee total
        $transportFeeTotal = $student->transportHistory->where('status', 'active')->sum('transport_fee');

        // ── Timeline Events ───────────────────────────────────────────────
        $timeline = $this->buildTimeline($student);

        // ── Status Config ─────────────────────────────────────────────────
        $statusConfig = [
            'active'      => ['color' => '#059669', 'bg' => '#dcfce7', 'icon' => 'fa-circle-check',  'label' => 'Active'],
            'inactive'    => ['color' => '#6b7280', 'bg' => '#f3f4f6', 'icon' => 'fa-circle-minus',  'label' => 'Inactive'],
            'passed_out'  => ['color' => '#4f46e5', 'bg' => '#ede9fe', 'icon' => 'fa-graduation-cap','label' => 'Graduated'],
            'dropped'     => ['color' => '#dc2626', 'bg' => '#fee2e2', 'icon' => 'fa-circle-xmark',  'label' => 'Dropped'],
        ];
        $currentStatus = $statusConfig[$student->status] ?? $statusConfig['active'];

        return view('admin.students.360', compact(
            'student',
            'totalDays', 'presentDays', 'absentDays', 'attendancePct',
            'semesterAttendance',
            'totalPaid', 'totalDue', 'totalDiscount',
            'govtScholarshipTotal', 'collegeScholarshipTotal', 'totalScholarship',
            'transportFeeTotal',
            'timeline',
            'currentStatus'
        ));
    }

    /**
     * Build a chronological timeline of all student events.
     */
    private function buildTimeline(Student $student): array
    {
        $events = [];

        // Admission
        $events[] = [
            'date'  => $student->admission_date,
            'icon'  => 'fa-user-plus',
            'color' => '#4f46e5',
            'bg'    => 'rgba(79,70,229,.1)',
            'title' => 'Admission Completed',
            'desc'  => "Admitted to {$student->branch?->name} — {$student->branch?->course?->name}",
            'badge' => 'Admission',
        ];

        // Scholarships
        foreach ($student->scholarships as $sch) {
            $events[] = [
                'date'  => $sch->start_date ?? $student->admission_date,
                'icon'  => 'fa-award',
                'color' => '#059669',
                'bg'    => 'rgba(5,150,105,.1)',
                'title' => 'Scholarship Assigned',
                'desc'  => $sch->display_name . ' — ₹' . number_format($sch->amount),
                'badge' => ucfirst($sch->scholarship_category),
            ];
        }

        // Transport
        foreach ($student->transportHistory as $tr) {
            $events[] = [
                'date'  => $tr->created_at,
                'icon'  => 'fa-bus',
                'color' => '#0891b2',
                'bg'    => 'rgba(8,145,178,.1)',
                'title' => 'Transport Assigned',
                'desc'  => ($tr->route ?? 'Route not specified') . ($tr->boarding_point ? " — {$tr->boarding_point}" : ''),
                'badge' => 'Transport',
            ];
        }

        // Fee payments
        foreach ($student->feePayments->where('status', 'paid') as $payment) {
            $events[] = [
                'date'  => $payment->payment_date,
                'icon'  => 'fa-receipt',
                'color' => '#d97706',
                'bg'    => 'rgba(217,119,6,.1)',
                'title' => 'Fee Payment',
                'desc'  => ($payment->feeType?->name ?? 'Fee') . ' — ₹' . number_format($payment->amount_paid) . ' (' . ucfirst($payment->payment_mode) . ')',
                'badge' => $payment->receipt_number,
            ];
        }

        // Sort chronologically
        usort($events, fn($a, $b) => ($a['date'] ?? now()) <=> ($b['date'] ?? now()));

        return $events;
    }

    private function authorizeTenant(Student $student): void
    {
        if ($student->tenant_id !== TenantService::getTenantId()) {
            abort(403, 'Unauthorized access.');
        }
    }
}
