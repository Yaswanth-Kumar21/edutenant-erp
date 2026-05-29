<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdmissionConfirmationMail;
use App\Mail\AttendanceAlertMail;
use App\Mail\FeePaymentReceiptMail;
use App\Mail\PayrollNotificationMail;
use App\Models\FeePayment;
use App\Models\Payroll;
use App\Models\Student;
use App\Services\TenantService;
use App\Traits\TenantScoped;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

/**
 * NotificationDispatchController
 *
 * Handles sending real email notifications from the admin panel.
 * All emails are queued (QUEUE_CONNECTION=database in .env).
 */
class NotificationDispatchController extends Controller
{
    use TenantScoped;

    /**
     * Send admission confirmation email to a student.
     * POST /admin/notifications/admission/{student}
     */
    public function sendAdmissionConfirmation(Student $student)
    {
        $this->assertTenant($student);

        $email = $student->email ?? $student->user?->email;

        if (!$email) {
            return back()->with('error', 'Student has no email address on record.');
        }

        $student->load(['branch.course', 'academicYear']);
        $tenant = $this->tenant();

        Mail::to($email)->queue(new AdmissionConfirmationMail($student, $tenant));

        return back()->with('success', "Admission confirmation email queued for {$student->full_name}.");
    }

    /**
     * Send fee payment receipt email.
     * POST /admin/notifications/fee-receipt/{payment}
     */
    public function sendFeeReceipt(FeePayment $payment)
    {
        $this->assertTenant($payment);

        $payment->load(['student', 'feeType', 'academicYear']);
        $email = $payment->student?->email ?? $payment->student?->user?->email;

        if (!$email) {
            return back()->with('error', 'Student has no email address on record.');
        }

        $tenant = $this->tenant();

        Mail::to($email)->queue(new FeePaymentReceiptMail($payment, $tenant));

        return back()->with('success', "Fee receipt email queued for {$payment->student?->full_name}.");
    }

    /**
     * Send payroll notification email to staff.
     * POST /admin/notifications/payroll/{payroll}
     */
    public function sendPayrollNotification(Payroll $payroll)
    {
        $this->assertTenant($payroll);

        $payroll->load(['staff.user']);
        $email = $payroll->staff?->user?->email ?? $payroll->staff?->email;

        if (!$email) {
            return back()->with('error', 'Staff member has no email address on record.');
        }

        $tenant = $this->tenant();

        Mail::to($email)->queue(new PayrollNotificationMail($payroll, $tenant));

        return back()->with('success', "Payroll notification email queued for {$payroll->staff?->name}.");
    }

    /**
     * Send low attendance alerts to all students below threshold.
     * POST /admin/notifications/attendance-alerts
     */
    public function sendAttendanceAlerts(Request $request)
    {
        $request->validate([
            'threshold' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $tenantId  = $this->tenantId();
        $tenant    = $this->tenant();
        $threshold = $request->get('threshold', 75);

        $students = Student::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->with(['attendance', 'user'])
            ->get();

        $sent = 0;
        foreach ($students as $student) {
            $total   = $student->attendance->count();
            $present = $student->attendance->where('status', 'present')->count();
            $pct     = $total > 0 ? round(($present / $total) * 100, 1) : 0;

            if ($pct > 0 && $pct < $threshold) {
                $email = $student->email ?? $student->user?->email;
                if ($email) {
                    Mail::to($email)->queue(new AttendanceAlertMail($student, $tenant, $pct));
                    $sent++;
                }
            }
        }

        return back()->with('success', "Attendance alert emails queued for {$sent} student(s) below {$threshold}%.");
    }
}
