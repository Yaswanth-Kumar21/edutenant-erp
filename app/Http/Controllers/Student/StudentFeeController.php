<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\FeePayment;
use App\Services\TenantService;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * StudentFeeController
 *
 * Handles the student-facing fee history and receipt download.
 * Students can ONLY see their own fee records.
 */
class StudentFeeController extends Controller
{
    public function index()
    {
        $user    = auth()->user();
        $student = $user->student;

        if (!$student) {
            abort(403, 'No student profile linked to this account.');
        }

        $tenant = TenantService::getTenant();

        $payments = FeePayment::where('student_id', $student->id)
            ->where('tenant_id', $student->tenant_id)
            ->with(['feeType', 'academicYear', 'collectedBy'])
            ->latest('payment_date')
            ->paginate(15);

        $totalPaid    = FeePayment::where('student_id', $student->id)->where('status', 'paid')->sum('amount_paid');
        $totalDue     = FeePayment::where('student_id', $student->id)->whereIn('status', ['pending', 'partial'])->sum('amount_due');
        $totalPending = FeePayment::where('student_id', $student->id)->where('status', 'pending')->count();

        return view('student.fees.index', compact(
            'student', 'tenant', 'payments', 'totalPaid', 'totalDue', 'totalPending'
        ));
    }

    public function show(FeePayment $payment)
    {
        $user    = auth()->user();
        $student = $user->student;

        // Strict ownership check — student can only see their own payments
        if (!$student || $payment->student_id !== $student->id || $payment->tenant_id !== $student->tenant_id) {
            abort(403, 'Access denied.');
        }

        $payment->load(['feeType', 'academicYear', 'collectedBy', 'student.branch.course.stream']);
        $tenant = TenantService::getTenant();

        return view('student.fees.show', compact('payment', 'tenant', 'student'));
    }

    /**
     * Download fee receipt as PDF.
     */
    public function downloadReceipt(FeePayment $payment)
    {
        $user    = auth()->user();
        $student = $user->student;

        if (!$student || $payment->student_id !== $student->id || $payment->tenant_id !== $student->tenant_id) {
            abort(403, 'Access denied.');
        }

        $payment->load(['feeType', 'academicYear', 'collectedBy', 'student.branch.course.stream']);
        $tenant = TenantService::getTenant();

        $pdf = Pdf::loadView('pdf.fee-receipt', compact('payment', 'tenant'))
            ->setPaper('a5', 'portrait');

        return $pdf->download("fee-receipt-{$payment->receipt_number}.pdf");
    }
}
