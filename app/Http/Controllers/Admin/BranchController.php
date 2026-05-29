<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Course;
use App\Services\TenantService;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::where('tenant_id', TenantService::getTenantId())->with('course.stream')->get();
        return view('admin.setup.branches.index', compact('branches'));
    }

    public function create()
    {
        $courses = Course::where('tenant_id', TenantService::getTenantId())->with('stream')->get();
        return view('admin.setup.branches.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'course_id'            => 'required|exists:courses,id',
            'name'                 => 'required|string|max:150',
            'code'                 => 'nullable|string|max:30',
            'intake_capacity'      => 'required|integer|min:1',
            'tuition_fee_student'  => 'required|numeric|min:0',
            'tuition_fee_govt'     => 'required|numeric|min:0',
            'has_record_fee'       => 'boolean',
        ]);
        Branch::create(array_merge($data, ['tenant_id' => TenantService::getTenantId()]));
        return redirect()->route('admin.setup.branches.index')->with('success', 'Branch created.');
    }

    public function show(Branch $branch) { return view('admin.setup.branches.show', compact('branch')); }
    public function edit(Branch $branch)
    {
        $courses = Course::where('tenant_id', TenantService::getTenantId())->with('stream')->get();
        return view('admin.setup.branches.edit', compact('branch', 'courses'));
    }

    public function update(Request $request, Branch $branch)
    {
        $branch->update($request->validate(['name' => 'required|string|max:150']));
        return redirect()->route('admin.setup.branches.index')->with('success', 'Branch updated.');
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();
        return redirect()->route('admin.setup.branches.index')->with('success', 'Branch deleted.');
    }
}
