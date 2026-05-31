<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeePayment;
use App\Models\LeaveRequest;
use App\Models\Staff;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Tenant;
use App\Services\DashboardService;
use App\Services\OnboardingService;
use App\Services\TenantService;
use Illuminate\Support\Facades\DB;

/**
 * DashboardController
 *
 * Routes to the correct role-specific dashboard.
 * Each role gets its own view with tailored data.
 */
class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            return $this->superAdminDashboard();
        }

        if ($user->isStudent()) {
            return redirect()->route('student.dashboard');
        }

        if ($user->isTeacher()) {
            return $this->teacherDashboard();
        }

        if ($user->isStaff()) {
            return $this->staffDashboard();
        }

        // College Admin
        return $this->adminDashboard();
    }

    // ── College Admin Dashboard ───────────────────────────────────────────────
    private function adminDashboard()
    {
        $tenant   = TenantService::getTenant();
        $tenantId = $tenant->id;

        $stats = DashboardService::getStats($tenantId);

        // Additional admin-specific stats
        $stats['pending_fees']    = FeePayment::where('tenant_id', $tenantId)->whereIn('status', ['pending', 'partial'])->sum('amount_due');
        $stats['new_admissions']  = Student::where('tenant_id', $tenantId)->whereMonth('admission_date', now()->month)->count();
        $stats['pending_leaves']  = LeaveRequest::whereHas('staff', fn($q) => $q->where('tenant_id', $tenantId))->where('status', 'pending')->count();

        $monthlyFees = DashboardService::getMonthlyFees($tenantId);

        $studentsByBranch = Student::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->with('branch')
            ->select('branch_id', DB::raw('COUNT(*) as count'))
            ->groupBy('branch_id')
            ->get();

        $recentAdmissions = Student::where('tenant_id', $tenantId)
            ->with(['branch.course'])
            ->latest('admission_date')
            ->limit(5)
            ->get();

        $recentPayments = FeePayment::where('tenant_id', $tenantId)
            ->with(['student', 'feeType'])
            ->latest('payment_date')
            ->limit(5)
            ->get();

        // Fee collection by category
        $feesByType = FeePayment::where('tenant_id', $tenantId)
            ->where('status', 'paid')
            ->whereYear('payment_date', now()->year)
            ->with('feeType')
            ->select('fee_type_id', DB::raw('SUM(amount_paid) as total'))
            ->groupBy('fee_type_id')
            ->get();

        return view('admin.dashboard', compact(
            'stats', 'monthlyFees', 'studentsByBranch',
            'recentAdmissions', 'recentPayments', 'feesByType', 'tenant'
        ));    }

    // ── Staff Dashboard ───────────────────────────────────────────────────────
    private function staffDashboard()
    {
        $tenant   = TenantService::getTenant();
        $tenantId = $tenant->id;

        $stats = [
            'total_students'  => Student::where('tenant_id', $tenantId)->where('status', 'active')->count(),
            'fees_today'      => FeePayment::where('tenant_id', $tenantId)->whereDate('payment_date', today())->where('status', 'paid')->sum('amount_paid'),
            'fees_this_month' => FeePayment::where('tenant_id', $tenantId)->whereMonth('payment_date', now()->month)->whereYear('payment_date', now()->year)->where('status', 'paid')->sum('amount_paid'),
            'pending_fees'    => FeePayment::where('tenant_id', $tenantId)->whereIn('status', ['pending', 'partial'])->count(),
            'new_admissions'  => Student::where('tenant_id', $tenantId)->whereMonth('admission_date', now()->month)->count(),
            'attendance_today' => StudentAttendance::where('tenant_id', $tenantId)->whereDate('attendance_date', today())->count(),
        ];

        $recentAdmissions = Student::where('tenant_id', $tenantId)
            ->with(['branch.course'])
            ->latest('admission_date')
            ->limit(6)
            ->get();

        $recentPayments = FeePayment::where('tenant_id', $tenantId)
            ->with(['student', 'feeType'])
            ->latest('payment_date')
            ->limit(6)
            ->get();

        $monthlyFees = DashboardService::getMonthlyFees($tenantId);

        return view('admin.staff-dashboard', compact(
            'stats', 'recentAdmissions', 'recentPayments', 'monthlyFees', 'tenant'
        ));
    }

    // ── Teacher Dashboard ─────────────────────────────────────────────────────
    private function teacherDashboard()
    {
        $tenant   = TenantService::getTenant();
        $tenantId = $tenant->id;

        $stats = [
            'total_students'    => Student::where('tenant_id', $tenantId)->where('status', 'active')->count(),
            'present_today'     => StudentAttendance::where('tenant_id', $tenantId)->whereDate('attendance_date', today())->where('status', 'present')->count(),
            'absent_today'      => StudentAttendance::where('tenant_id', $tenantId)->whereDate('attendance_date', today())->where('status', 'absent')->count(),
            'attendance_today'  => StudentAttendance::where('tenant_id', $tenantId)->whereDate('attendance_date', today())->count(),
            'low_attendance'    => 0, // calculated below
        ];

        // Students with < 75% attendance
        $allStudents = Student::where('tenant_id', $tenantId)->where('status', 'active')
            ->with('attendance')->get();
        $lowAttendance = $allStudents->filter(function ($s) {
            $total   = $s->attendance->count();
            $present = $s->attendance->where('status', 'present')->count();
            return $total > 0 && ($present / $total) * 100 < 75;
        });
        $stats['low_attendance'] = $lowAttendance->count();

        // Recent attendance records
        $recentAttendance = StudentAttendance::where('tenant_id', $tenantId)
            ->with(['student', 'branch'])
            ->latest('attendance_date')
            ->limit(10)
            ->get();

        // Students by branch for class overview
        $studentsByBranch = Student::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->with('branch')
            ->select('branch_id', DB::raw('COUNT(*) as count'))
            ->groupBy('branch_id')
            ->get();

        // Monthly attendance trend
        $monthlyAttendance = StudentAttendance::where('tenant_id', $tenantId)
            ->where('attendance_date', '>=', now()->subMonths(6))
            ->select(
                DB::raw("EXTRACT(MONTH FROM attendance_date) as month"),
                DB::raw("EXTRACT(YEAR FROM attendance_date) as year"),
                DB::raw("SUM(CASE WHEN status='present' THEN 1 ELSE 0 END) as present_count"),
                DB::raw('COUNT(*) as total_count')
            )
            ->groupBy(DB::raw("EXTRACT(YEAR FROM attendance_date)"), DB::raw("EXTRACT(MONTH FROM attendance_date)"))
            ->orderBy('year')->orderBy('month')
            ->get();

        return view('admin.teacher-dashboard', compact(
            'stats', 'recentAttendance', 'studentsByBranch', 'monthlyAttendance', 'tenant'
        ));
    }

    // ── Super Admin Dashboard ─────────────────────────────────────────────────
    private function superAdminDashboard()
    {
        $stats = [
            'total_tenants'   => Tenant::count(),
            'active_tenants'  => Tenant::where('status', 'active')->count(),
            'inactive_tenants'=> Tenant::where('status', '!=', 'active')->count(),
            'total_students'  => Student::count(),
            'total_staff'     => Staff::count(),
            'total_fees'      => FeePayment::where('status', 'paid')->sum('amount_paid'),
        ];

        $tenants = Tenant::withCount(['students', 'staff'])
            ->with('academicYears')
            ->latest()
            ->paginate(10);

        // Per-tenant fee collection
        $tenantFees = FeePayment::where('status', 'paid')
            ->select('tenant_id', DB::raw('SUM(amount_paid) as total'))
            ->groupBy('tenant_id')
            ->pluck('total', 'tenant_id');

        // Monthly platform-wide fee trend
        $platformFees = FeePayment::where('status', 'paid')
            ->where('payment_date', '>=', now()->subMonths(6))
            ->select(
                DB::raw("EXTRACT(MONTH FROM payment_date) as month"),
                DB::raw("EXTRACT(YEAR FROM payment_date) as year"),
                DB::raw('SUM(amount_paid) as total')
            )
            ->groupBy(DB::raw("EXTRACT(YEAR FROM payment_date)"), DB::raw("EXTRACT(MONTH FROM payment_date)"))
            ->orderBy('year')->orderBy('month')
            ->get();

        // Onboarding badges for each tenant
        $onboardingBadges = [];
        foreach ($tenants as $tenant) {
            $onboardingBadges[$tenant->id] = OnboardingService::getBadge($tenant);
        }

        return view('admin.super-dashboard', compact('stats', 'tenants', 'tenantFees', 'platformFees', 'onboardingBadges'));
    }
}
