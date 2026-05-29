<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeePayment;
use App\Models\Student;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function daily(Request $request)
    {
        $tenantId = TenantService::getTenantId();
        $date     = $request->get('date', today()->toDateString());

        $dailyFees = FeePayment::where('tenant_id', $tenantId)
            ->whereDate('payment_date', $date)->where('status', 'paid')
            ->with(['student', 'feeType'])->get();

        $totalToday = $dailyFees->sum('amount_paid');

        return view('admin.reports.daily', compact('dailyFees', 'totalToday', 'date'));
    }

    public function annual(Request $request)
    {
        $tenantId = TenantService::getTenantId();
        $year     = $request->get('year', now()->year);

        $annualFees = FeePayment::where('tenant_id', $tenantId)
            ->whereYear('payment_date', $year)->where('status', 'paid')
            ->select(DB::raw('MONTH(payment_date) as month'), DB::raw('SUM(amount_paid) as total'))
            ->groupBy('month')->orderBy('month')->get();

        return view('admin.reports.annual', compact('annualFees', 'year'));
    }

    public function students(Request $request)
    {
        $tenantId = TenantService::getTenantId();
        $students = Student::where('tenant_id', $tenantId)->with('branch.course.stream')->paginate(50);
        return view('admin.reports.students', compact('students'));
    }

    public function fees(Request $request)
    {
        $tenantId = TenantService::getTenantId();
        $payments = FeePayment::where('tenant_id', $tenantId)
            ->with(['student', 'feeType'])->latest()->paginate(50);
        return view('admin.reports.fees', compact('payments'));
    }
}
