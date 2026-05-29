<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AttendanceExport;
use App\Exports\ExpensesExport;
use App\Exports\FeePaymentsExport;
use App\Exports\IncomeExport;
use App\Exports\PayrollExport;
use App\Exports\StudentsExport;
use App\Http\Controllers\Controller;
use App\Traits\TenantScoped;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

/**
 * ExportController
 *
 * Handles Excel/CSV exports for all major modules.
 * All exports are tenant-scoped.
 */
class ExportController extends Controller
{
    use TenantScoped;

    public function students(Request $request)
    {
        $tenantId = $this->tenantId();
        $format   = $request->get('format', 'xlsx');
        $filename = 'students-' . now()->format('Y-m-d');

        $export = new StudentsExport(
            tenantId: $tenantId,
            branchId: $request->branch_id,
            status:   $request->status
        );

        return $format === 'csv'
            ? Excel::download($export, "{$filename}.csv", \Maatwebsite\Excel\Excel::CSV)
            : Excel::download($export, "{$filename}.xlsx");
    }

    public function feePayments(Request $request)
    {
        $tenantId = $this->tenantId();
        $format   = $request->get('format', 'xlsx');
        $filename = 'fee-payments-' . now()->format('Y-m-d');

        $export = new FeePaymentsExport(
            tenantId:       $tenantId,
            status:         $request->status,
            academicYearId: $request->academic_year_id,
            dateFrom:       $request->date_from,
            dateTo:         $request->date_to
        );

        return $format === 'csv'
            ? Excel::download($export, "{$filename}.csv", \Maatwebsite\Excel\Excel::CSV)
            : Excel::download($export, "{$filename}.xlsx");
    }

    public function attendance(Request $request)
    {
        $tenantId = $this->tenantId();
        $format   = $request->get('format', 'xlsx');
        $month    = $request->get('month', now()->format('Y-m'));
        $filename = "attendance-{$month}";

        $export = new AttendanceExport(
            tenantId: $tenantId,
            branchId: $request->branch_id,
            month:    $month
        );

        return $format === 'csv'
            ? Excel::download($export, "{$filename}.csv", \Maatwebsite\Excel\Excel::CSV)
            : Excel::download($export, "{$filename}.xlsx");
    }

    public function payroll(Request $request)
    {
        $tenantId = $this->tenantId();
        $format   = $request->get('format', 'xlsx');
        $filename = 'payroll-' . now()->format('Y-m-d');

        $export = new PayrollExport(
            tenantId: $tenantId,
            month:    $request->month,
            year:     $request->year
        );

        return $format === 'csv'
            ? Excel::download($export, "{$filename}.csv", \Maatwebsite\Excel\Excel::CSV)
            : Excel::download($export, "{$filename}.xlsx");
    }

    public function expenses(Request $request)
    {
        $tenantId = $this->tenantId();
        $format   = $request->get('format', 'xlsx');
        $filename = 'expenses-' . now()->format('Y-m-d');

        $export = new ExpensesExport(
            tenantId: $tenantId,
            dateFrom: $request->date_from,
            dateTo:   $request->date_to
        );

        return $format === 'csv'
            ? Excel::download($export, "{$filename}.csv", \Maatwebsite\Excel\Excel::CSV)
            : Excel::download($export, "{$filename}.xlsx");
    }

    public function income(Request $request)
    {
        $tenantId = $this->tenantId();
        $format   = $request->get('format', 'xlsx');
        $filename = 'income-' . now()->format('Y-m-d');

        $export = new IncomeExport(
            tenantId: $tenantId,
            dateFrom: $request->date_from,
            dateTo:   $request->date_to
        );

        return $format === 'csv'
            ? Excel::download($export, "{$filename}.csv", \Maatwebsite\Excel\Excel::CSV)
            : Excel::download($export, "{$filename}.xlsx");
    }
}
