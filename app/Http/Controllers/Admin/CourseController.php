<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Stream;
use App\Services\TenantService;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::where('tenant_id', TenantService::getTenantId())->with('stream')->get();
        return view('admin.setup.courses.index', compact('courses'));
    }

    public function create()
    {
        $streams = Stream::where('tenant_id', TenantService::getTenantId())->get();
        return view('admin.setup.courses.create', compact('streams'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'stream_id'       => 'required|exists:streams,id',
            'name'            => 'required|string|max:100',
            'code'            => 'nullable|string|max:20',
            'duration_years'  => 'required|integer|min:1',
            'total_semesters' => 'required|integer|min:1',
            'has_record_fee'  => 'boolean',
        ]);
        Course::create(array_merge($data, ['tenant_id' => TenantService::getTenantId()]));
        return redirect()->route('admin.setup.courses.index')->with('success', 'Course created.');
    }

    public function show(Course $course) { return view('admin.setup.courses.show', compact('course')); }
    public function edit(Course $course)
    {
        $streams = Stream::where('tenant_id', TenantService::getTenantId())->get();
        return view('admin.setup.courses.edit', compact('course', 'streams'));
    }

    public function update(Request $request, Course $course)
    {
        $course->update($request->validate(['name' => 'required|string|max:100']));
        return redirect()->route('admin.setup.courses.index')->with('success', 'Course updated.');
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->route('admin.setup.courses.index')->with('success', 'Course deleted.');
    }
}
