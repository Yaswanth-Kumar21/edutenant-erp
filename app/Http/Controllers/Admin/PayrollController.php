<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\Staff;
use App\Services\StaffService;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    /**
     * Payroll listing with filters.
     */
    public function index(Request $request)
    {
        $tenantId = TenantService::getTenantId();
        $month    = (int) $request->get('month', now()->month);
        $year     = (int) $request->get('year', now()->year);

        $query = Payroll::where('tenant_id', $tenantId)->with('staff');

        if ($request->filled('month'))    $query->where('month', $request->month);
        if ($request->filled('year'))     $query->where('year', $request->year);
        if ($request->filled('status'))   $query->where('status', $request->status);
        if ($request->filled('staff_id')) $query->where('staff_id', $request->staff_id);

        $payrolls  = $query->orderByDesc('year')->orderByDesc('month')->paginate(20)->withQueryString();
        $staffList = Staff::where('tenant_id', $tenantId)->where('status', 'active')->orderBy('name')->get();

        // Summary for selected month/year
        $summary = Payroll::where('tenant_id', $tenantId)
            ->where('month', $month)->where('year', $year)
            ->selectRaw('COUNT(*) as count, SUM(net_salary) as total_net, SUM(total_deductions) as total_deductions, SUM(gross_salary) as total_gross')
            ->first();

        return view('admin.payroll.index', compact('payrolls', 'staffList', 'month', 'year', 'summary'));
    }

    /**
     * Generate payroll for a specific month — shows preview before saving.
     */
    public function generate(Request $request)
    {
        $tenantId  = TenantService::getTenantId();
        $month     = (int) $request->get('month', now()->month);
        $year      = (int) $request->get('year', now()->year);
        $staffList = Staff::where('tenant_id', $tenantId)->where('status', 'active')->orderBy('name')->get();

        // Calculate salary for each staff member
        $calculations = $staffList->map(function ($staff) use ($month, $year) {
            $calc = StaffService::calculateNetSalary($staff, $month, $year);

            // Check if payroll already exists
            $existing = Payroll::where('staff_id', $staff->id)
                ->where('month', $month)->where('year', $year)->first();

            return array_merge($calc, [
                'staff'    => $staff,
                'existing' => $existing,
            ]);
        });

        return view('admin.payroll.generate', compact('calculations', 'month', 'year', 'staffList'));
    }

    /**
     * Save generated payroll records.
     */
    public function store(Request $request)
    {
        $request->validate([
            'month'      => 'required|integer|min:1|max:12',
            'year'       => 'required|integer|min:2020',
            'staff_ids'  => 'required|array',
            'staff_ids.*'=> 'exists:staff,id',
        ]);

        $tenantId = TenantService::getTenantId();
        $month    = (int) $request->month;
        $year     = (int) $request->year;
        $count    = 0;

        foreach ($request->staff_ids as $staffId) {
            $staff = Staff::where('tenant_id', $tenantId)->find($staffId);
            if (!$staff) continue;

            $calc = StaffService::calculateNetSalary($staff, $month, $year);

            Payroll::updateOrCreate(
                ['staff_id' => $staffId, 'month' => $month, 'year' => $year],
                [
                    'tenant_id'        => $tenantId,
                    'generated_by'     => auth()->id(),
                    'payroll_number'   => 'PAY-' . $year . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($staffId, 4, '0', STR_PAD_LEFT),
                    'gross_salary'     => $staff->monthly_salary,
                    'basic_salary'     => $staff->basic_salary ?? $staff->monthly_salary,
                    'hra'              => $staff->hra ?? 0,
                    'da'               => $staff->da ?? 0,
                    'other_allowances' => $staff->other_allowances ?? 0,
                    'working_days'     => $calc['working_days'],
                    'present_days'     => $calc['present_days'],
                    'absent_days'      => $calc['absent_days'],
                    'leave_days'       => $calc['leave_days'],
                    'holiday_days'     => $calc['holiday_days'],
                    'half_days'        => $calc['half_days'],
                    'allowed_holidays' => $calc['allowed_leaves'],
                    'absent_deduction' => $calc['deduction_amt'],
                    'pf_deduction'     => $staff->pf_deduction ?? 0,
                    'tax_deduction'    => $staff->tax_deduction ?? 0,
                    'other_deductions' => 0,
                    'total_deductions' => $calc['deduction_amt'] + ($staff->pf_deduction ?? 0) + ($staff->tax_deduction ?? 0),
                    'net_salary'       => $calc['net_salary'] - ($staff->pf_deduction ?? 0) - ($staff->tax_deduction ?? 0),
                    'per_day_salary'   => $calc['per_day_salary'],
                    'status'           => 'draft',
                ]
            );
            $count++;
        }

        return redirect()->route('admin.payroll.index', ['month' => $month, 'year' => $year])
            ->with('success', "Payroll generated for {$count} staff members.");
    }

    /**
     * Show individual payroll / salary slip.
     */
    public function show(Payroll $payroll)
    {
        $this->authorizeTenant($payroll);
        $payroll->load(['staff', 'generatedBy']);
        $tenant = TenantService::getTenant();

        return view('admin.payroll.show', compact('payroll', 'tenant'));
    }

    /**
     * Print salary slip.
     */
    public function slip(Payroll $payroll)
    {
        $this->authorizeTenant($payroll);
        $payroll->load(['staff', 'generatedBy']);
        $tenant = TenantService::getTenant();

        return view('admin.payroll.slip', compact('payroll', 'tenant'));
    }

    /**
     * Approve a payroll record.
     */
    public function approve(Payroll $payroll)
    {
        $this->authorizeTenant($payroll);
        $payroll->update(['status' => 'approved']);

        return back()->with('success', 'Payroll approved.');
    }

    /**
     * Mark payroll as paid.
     */
    public function markPaid(Request $request, Payroll $payroll)
    {
        $this->authorizeTenant($payroll);

        $request->validate([
            'payment_mode'          => 'required|in:bank_transfer,cash,cheque,upi',
            'payment_date'          => 'required|date',
            'transaction_reference' => 'nullable|string|max:100',
        ]);

        $payroll->update([
            'status'                => 'paid',
            'payment_mode'          => $request->payment_mode,
            'payment_date'          => $request->payment_date,
            'transaction_reference' => $request->transaction_reference,
        ]);

        return back()->with('success', 'Salary marked as paid.');
    }

    /**
     * Payroll summary for a month.
     */
    public function summary(Request $request)
    {
        $tenantId = TenantService::getTenantId();
        $month    = (int) $request->get('month', now()->month);
        $year     = (int) $request->get('year', now()->year);

        $payrolls = Payroll::where('tenant_id', $tenantId)
            ->where('month', $month)->where('year', $year)
            ->with('staff')
            ->orderBy('status')
            ->get();

        $totals = [
            'gross'      => $payrolls->sum('gross_salary'),
            'deductions' => $payrolls->sum('total_deductions'),
            'net'        => $payrolls->sum('net_salary'),
            'paid'       => $payrolls->where('status', 'paid')->sum('net_salary'),
            'pending'    => $payrolls->whereIn('status', ['draft', 'approved'])->sum('net_salary'),
            'count'      => $payrolls->count(),
        ];

        return view('admin.payroll.summary', compact('payrolls', 'totals', 'month', 'year'));
    }

    private function authorizeTenant(Payroll $payroll): void
    {
        if ($payroll->tenant_id !== TenantService::getTenantId()) abort(403);
    }
}
