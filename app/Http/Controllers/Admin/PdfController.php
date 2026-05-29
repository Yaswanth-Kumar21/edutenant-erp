<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeePayment;
use App\Models\Payroll;
use App\Models\Student;
use App\Services\TenantService;
use App\Traits\TenantScoped;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * PdfController
 *
 * Generates downloadable PDFs for:
 *  - Fee receipts
 *  - Payroll slips
 *  - Student reports
 *  - Admission receipts
 *  - Attendance reports
 */
class PdfController extends Controller
{
    use TenantScoped;

    /**
     * Download fee receipt as PDF.
     */
    public function feeReceipt(FeePayment $payment)
    {
        $this->assertTenant($payment);
        $payment->load(['student.branch.course.stream', 'feeType', 'academicYear', 'collectedBy']);
        $tenant = $this->tenant();

        $pdf = Pdf::loadView('pdf.fee-receipt', compact('payment', 'tenant'))
            ->setPaper('a5', 'portrait');

        return $pdf->download("fee-receipt-{$payment->receipt_number}.pdf");
    }

    /**
     * Download payroll slip as PDF.
     */
    public function payrollSlip(Payroll $payroll)
    {
        $this->assertTenant($payroll);
        $payroll->load(['staff.user', 'approvedBy']);
        $tenant = $this->tenant();

        $pdf = Pdf::loadView('pdf.payroll-slip', compact('payroll', 'tenant'))
            ->setPaper('a4', 'portrait');

        $name = $payroll->staff?->user?->name ?? 'staff';
        return $pdf->download("payroll-slip-{$name}-{$payroll->month}-{$payroll->year}.pdf");
    }

    /**
     * Download student report as PDF.
     */
    public function studentReport(Student $student)
    {
        $this->assertTenant($student);
        $student->load([
            'branch.course.stream',
            'academicYear',
            'guardian',
            'feePayments.feeType',
            'attendance',
            'certificates',
        ]);
        $tenant = $this->tenant();

        $totalPaid       = $student->total_fees_paid;
        $totalDue        = $student->feePayments->whereIn('status', ['pending', 'partial'])->sum('amount_due');
        $attendancePct   = $student->attendance_percentage;
        $presentDays     = $student->attendance->where('status', 'present')->count();
        $absentDays      = $student->attendance->where('status', 'absent')->count();
        $totalDays       = $student->attendance->count();

        $pdf = Pdf::loadView('pdf.student-report', compact(
            'student', 'tenant',
            'totalPaid', 'totalDue',
            'attendancePct', 'presentDays', 'absentDays', 'totalDays'
        ))->setPaper('a4', 'portrait');

        return $pdf->download("student-report-{$student->admission_number}.pdf");
    }

    /**
     * Download admission receipt as PDF.
     */
    public function admissionReceipt(Student $student)
    {
        $this->assertTenant($student);
        $student->load([
            'branch.course.stream',
            'academicYear',
            'guardian',
            'admissionReceipts' => fn($q) => $q->latest()->limit(1),
        ]);
        $receipt = $student->admissionReceipts->first();
        $tenant  = $this->tenant();

        $pdf = Pdf::loadView('pdf.admission-receipt', compact('student', 'receipt', 'tenant'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("admission-receipt-{$student->admission_number}.pdf");
    }

    /**
     * Download attendance report as PDF.
     */
    public function attendanceReport()
    {
        $tenantId = $this->tenantId();
        $tenant   = $this->tenant();

        $branchId = request('branch_id');
        $month    = request('month', now()->format('Y-m'));
        [$year, $mon] = explode('-', $month);

        $query = \App\Models\StudentAttendance::where('tenant_id', $tenantId)
            ->whereYear('attendance_date', $year)
            ->whereMonth('attendance_date', $mon)
            ->with(['student.branch', 'markedBy']);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $records = $query->orderBy('attendance_date')->get();

        $pdf = Pdf::loadView('pdf.attendance-report', compact('records', 'tenant', 'month', 'year', 'mon'))
            ->setPaper('a4', 'landscape');

        return $pdf->download("attendance-report-{$month}.pdf");
    }
}
