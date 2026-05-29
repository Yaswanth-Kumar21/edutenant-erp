<?php

namespace App\Http\Controllers\Admin\Fees;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\FeePayment;
use App\Models\Student;
use App\Services\FeeAnalyticsService;
use App\Traits\TenantScoped;
use Illuminate\Http\Request;

/**
 * FeeDashboardController
 *
 * Handles the fee analytics dashboard and student fee profile.
 */
class FeeDashboardController extends Controller
{
    use TenantScoped;

    public function __construct(
        private readonly FeeAnalyticsService $analytics
    ) {}

    /**
     * Fee management dashboard with analytics.
     */
    public function index(Request $request)
    {
        $tenantId    = $this->tenantId();
        $tenant      = $this->tenant();
        $currentYear = AcademicYear::where('tenant_id', $tenantId)
            ->where('is_current', true)->first();

        $yearId = $request->get('academic_year_id', $currentYear?->id);

        $stats              = $this->analytics->getDashboardStats($tenantId, $yearId);
        $monthlyCollection  = $this->analytics->getMonthlyCollection($tenantId, 12);
        $branchCollection   = $this->analytics->getBranchCollection($tenantId, $yearId);
        $categoryCollection = $this->analytics->getCategoryCollection($tenantId, $yearId);

        $recentPayments = FeePayment::where('tenant_id', $tenantId)
            ->with(['student', 'feeType'])
            ->latest('payment_date')
            ->limit(10)
            ->get();

        $pendingDues   = $this->analytics->getPendingDues($tenantId, $yearId);
        $academicYears = AcademicYear::where('tenant_id', $tenantId)
            ->orderByDesc('is_current')
            ->orderByDesc('start_date')
            ->get();

        return view('admin.fees.dashboard', compact(
            'stats', 'monthlyCollection', 'branchCollection',
            'categoryCollection', 'recentPayments', 'pendingDues',
            'academicYears', 'currentYear', 'yearId', 'tenant'
        ));
    }

    /**
     * Individual student fee profile.
     */
    public function studentProfile(Student $student, Request $request)
    {
        $this->assertTenant($student);

        $student->load([
            'branch.course.stream',
            'academicYear',
            'feePayments' => fn($q) => $q->with('feeType')->latest('payment_date'),
            'guardian',
        ]);

        $feeSummary      = $this->analytics->getStudentFeeSummary($student);
        $pendingPayments = $student->feePayments->whereIn('status', ['pending', 'partial']);

        return view('admin.fees.student-profile', compact(
            'student', 'feeSummary', 'pendingPayments'
        ));
    }
}
