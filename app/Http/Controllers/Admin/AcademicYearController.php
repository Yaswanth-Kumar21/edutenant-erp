<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Services\TenantService;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index()
    {
        $years = AcademicYear::where('tenant_id', TenantService::getTenantId())->latest()->get();
        return view('admin.setup.academic-years.index', compact('years'));
    }

    public function create() { return view('admin.setup.academic-years.create'); }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:20',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
            'is_current' => 'boolean',
        ]);

        if (!empty($data['is_current'])) {
            AcademicYear::where('tenant_id', TenantService::getTenantId())->update(['is_current' => false]);
        }

        AcademicYear::create(array_merge($data, ['tenant_id' => TenantService::getTenantId()]));
        return redirect()->route('admin.setup.academic-years.index')->with('success', 'Academic year created.');
    }

    public function show(AcademicYear $academicYear) { return view('admin.setup.academic-years.show', compact('academicYear')); }
    public function edit(AcademicYear $academicYear) { return view('admin.setup.academic-years.edit', compact('academicYear')); }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:20',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after:start_date',
            'is_current' => 'boolean',
        ]);

        // If marking as current, unset all others for this tenant first
        if (!empty($data['is_current'])) {
            AcademicYear::where('tenant_id', $academicYear->tenant_id)
                ->where('id', '!=', $academicYear->id)
                ->update(['is_current' => false]);
        }

        $data['is_current'] = !empty($data['is_current']);
        $academicYear->update($data);

        return redirect()->route('admin.setup.academic-years.index')
            ->with('success', 'Academic year updated.');
    }

    public function destroy(AcademicYear $academicYear)
    {
        $academicYear->delete();
        return redirect()->route('admin.setup.academic-years.index')->with('success', 'Deleted.');
    }
}
