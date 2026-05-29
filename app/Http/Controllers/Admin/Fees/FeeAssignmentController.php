<?php

namespace App\Http\Controllers\Admin\Fees;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Branch;
use App\Models\FeeType;
use App\Models\Student;
use App\Services\FeeAssignmentService;
use App\Traits\TenantScoped;
use Illuminate\Http\Request;

class FeeAssignmentController extends Controller
{
    use TenantScoped;

    public function __construct(
        private readonly FeeAssignmentService $service
    ) {}

    public function index(Request $request)
    {
        $tenantId = $this->tenantId();

        $query = Student::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->with(['branch.course', 'academicYear', 'feePayments']);

        if ($request->filled('branch_id'))        $query->where('branch_id', $request->branch_id);
        if ($request->filled('academic_year_id')) $query->where('academic_year_id', $request->academic_year_id);

        $students      = $query->orderBy('first_name')->paginate(25)->withQueryString();
        $branches      = Branch::where('tenant_id', $tenantId)->with('course')->orderBy('name')->get();
        $academicYears = AcademicYear::where('tenant_id', $tenantId)->orderByDesc('is_current')->get();
        $feeTypes      = FeeType::where('tenant_id', $tenantId)->where('is_active', true)->get();

        return view('admin.fees.assignments.index', compact(
            'students', 'branches', 'academicYears', 'feeTypes'
        ));
    }

    public function assignToStudent(Request $request, Student $student)
    {
        $this->assertTenant($student);

        $request->validate([
            'fee_type_id'      => ['required', 'exists:fee_types,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'amount_due'       => ['required', 'numeric', 'min:0'],
            'semester'         => ['nullable', 'integer', 'min:1', 'max:12'],
        ]);

        $this->service->assignToStudent($student, $request->validated(), $this->tenantId());

        return back()->with('success', 'Fee assigned to student successfully.');
    }

    public function bulkAssign(Request $request)
    {
        $request->validate([
            'branch_id'        => ['required', 'exists:branches,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'fee_type_id'      => ['required', 'exists:fee_types,id'],
            'semester'         => ['nullable', 'integer', 'min:1', 'max:12'],
            'amount_due'       => ['required', 'numeric', 'min:0'],
        ]);

        $count = $this->service->bulkAssign($request->validated(), $this->tenantId());

        return back()->with('success', "Fees assigned to {$count} students successfully.");
    }
}
