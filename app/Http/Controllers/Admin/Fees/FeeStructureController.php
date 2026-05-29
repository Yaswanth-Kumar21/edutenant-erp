<?php

namespace App\Http\Controllers\Admin\Fees;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Fees\StoreFeeStructureRequest;
use App\Models\AcademicYear;
use App\Models\Branch;
use App\Models\FeeStructure;
use App\Models\FeeType;
use App\Models\Stream;
use App\Traits\TenantScoped;
use Illuminate\Http\Request;

class FeeStructureController extends Controller
{
    use TenantScoped;

    public function index(Request $request)
    {
        $tenantId = $this->tenantId();

        $query = FeeStructure::where('tenant_id', $tenantId)
            ->with(['feeType', 'branch.course', 'stream', 'academicYear']);

        if ($request->filled('branch_id'))        $query->where('branch_id', $request->branch_id);
        if ($request->filled('fee_type_id'))      $query->where('fee_type_id', $request->fee_type_id);
        if ($request->filled('academic_year_id')) $query->where('academic_year_id', $request->academic_year_id);

        $structures    = $query->orderBy('fee_type_id')->paginate(20)->withQueryString();
        $branches      = Branch::where('tenant_id', $tenantId)->with('course')->orderBy('name')->get();
        $feeTypes      = FeeType::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('sort_order')->get();
        $academicYears = AcademicYear::where('tenant_id', $tenantId)->orderByDesc('is_current')->get();

        return view('admin.fees.structures.index', compact(
            'structures', 'branches', 'feeTypes', 'academicYears'
        ));
    }

    public function create()
    {
        $tenantId      = $this->tenantId();
        $branches      = Branch::where('tenant_id', $tenantId)->with('course.stream')->orderBy('name')->get();
        $streams       = Stream::where('tenant_id', $tenantId)->where('is_active', true)->get();
        $feeTypes      = FeeType::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('sort_order')->get();
        $academicYears = AcademicYear::where('tenant_id', $tenantId)->orderByDesc('is_current')->get();

        return view('admin.fees.structures.create', compact(
            'branches', 'streams', 'feeTypes', 'academicYears'
        ));
    }

    public function store(StoreFeeStructureRequest $request)
    {
        $data              = $request->validated();
        $data['tenant_id'] = $this->tenantId();

        FeeStructure::create($data);

        return redirect()
            ->route('admin.fees.structures.index')
            ->with('success', 'Fee structure created successfully.');
    }

    public function show(FeeStructure $structure)
    {
        $this->assertTenant($structure);
        $structure->load(['feeType', 'branch.course', 'stream', 'academicYear']);

        return view('admin.fees.structures.show', compact('structure'));
    }

    public function edit(FeeStructure $structure)
    {
        $this->assertTenant($structure);

        $tenantId      = $this->tenantId();
        $branches      = Branch::where('tenant_id', $tenantId)->with('course.stream')->orderBy('name')->get();
        $streams       = Stream::where('tenant_id', $tenantId)->where('is_active', true)->get();
        $feeTypes      = FeeType::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('sort_order')->get();
        $academicYears = AcademicYear::where('tenant_id', $tenantId)->orderByDesc('is_current')->get();

        return view('admin.fees.structures.edit', compact(
            'structure', 'branches', 'streams', 'feeTypes', 'academicYears'
        ));
    }

    public function update(StoreFeeStructureRequest $request, FeeStructure $structure)
    {
        $this->assertTenant($structure);
        $structure->update($request->validated());

        return redirect()
            ->route('admin.fees.structures.index')
            ->with('success', 'Fee structure updated.');
    }

    public function destroy(FeeStructure $structure)
    {
        $this->assertTenant($structure);
        $structure->delete();

        return redirect()
            ->route('admin.fees.structures.index')
            ->with('success', 'Fee structure deleted.');
    }
}
