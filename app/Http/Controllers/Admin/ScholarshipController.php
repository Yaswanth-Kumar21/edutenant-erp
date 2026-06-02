<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentScholarship;
use App\Services\TenantService;
use App\Traits\TenantScoped;
use Illuminate\Http\Request;

/**
 * ScholarshipController
 *
 * Manages student scholarships — list, add, edit, delete.
 * Accessible by College Admin and Staff.
 * Teachers have read-only access.
 */
class ScholarshipController extends Controller
{
    use TenantScoped;

    /**
     * List all scholarships across all students.
     */
    public function index(Request $request)
    {
        $tenantId = $this->tenantId();

        $query = StudentScholarship::where('tenant_id', $tenantId)
            ->with(['student.branch.course']);

        if ($request->filled('category')) {
            $query->where('scholarship_category', $request->category);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('student', fn($q) =>
                $q->where('first_name', 'like', "%{$s}%")
                  ->orWhere('last_name', 'like', "%{$s}%")
                  ->orWhere('admission_number', 'like', "%{$s}%")
            );
        }

        $scholarships = $query->latest()->paginate(20)->withQueryString();

        // Summary stats
        $stats = [
            'total_govt'    => StudentScholarship::where('tenant_id', $tenantId)->where('scholarship_category', 'government')->where('status', 'active')->count(),
            'total_college' => StudentScholarship::where('tenant_id', $tenantId)->where('scholarship_category', 'college')->where('status', 'active')->count(),
            'total_amount'  => StudentScholarship::where('tenant_id', $tenantId)->where('status', 'active')->sum('amount'),
            'students_count'=> StudentScholarship::where('tenant_id', $tenantId)->where('status', 'active')->distinct('student_id')->count('student_id'),
        ];

        return view('admin.scholarships.index', compact('scholarships', 'stats'));
    }

    /**
     * Show form to add a scholarship to a student.
     */
    public function create(Request $request)
    {
        $tenantId = $this->tenantId();
        $student  = null;

        if ($request->filled('student_id')) {
            $student = Student::where('tenant_id', $tenantId)
                ->with('branch.course')
                ->findOrFail($request->student_id);
        }

        $students = Student::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'admission_number']);

        return view('admin.scholarships.create', compact('student', 'students'));
    }

    /**
     * Store a new scholarship record.
     */
    public function store(Request $request)
    {
        $tenantId = $this->tenantId();

        $validated = $request->validate([
            'student_id'           => ['required', 'exists:students,id'],
            'scholarship_category' => ['required', 'in:government,college'],
            'scholarship_type'     => ['nullable', 'string', 'max:100'],
            'scholarship_name'     => ['nullable', 'string', 'max:150'],
            'amount'               => ['required', 'numeric', 'min:0'],
            'applicable_semesters' => ['nullable', 'array'],
            'applicable_semesters.*' => ['integer', 'min:1', 'max:8'],
            'start_date'           => ['nullable', 'date'],
            'end_date'             => ['nullable', 'date', 'after_or_equal:start_date'],
            'status'               => ['required', 'in:active,inactive,expired'],
            'remarks'              => ['nullable', 'string', 'max:500'],
        ]);

        // Verify student belongs to this tenant
        $student = Student::where('tenant_id', $tenantId)->findOrFail($validated['student_id']);

        $scholarship = StudentScholarship::create(array_merge($validated, [
            'tenant_id' => $tenantId,
        ]));

        // Update cached flag on student
        $student->update(['scholarship_eligible' => true]);

        return redirect()
            ->route('admin.scholarships.index')
            ->with('success', "Scholarship added for {$student->full_name}.");
    }

    /**
     * Show edit form.
     */
    public function edit(StudentScholarship $scholarship)
    {
        $this->assertTenantModel($scholarship);

        $scholarship->load('student.branch.course');

        return view('admin.scholarships.edit', compact('scholarship'));
    }

    /**
     * Update scholarship record.
     */
    public function update(Request $request, StudentScholarship $scholarship)
    {
        $this->assertTenantModel($scholarship);

        $validated = $request->validate([
            'scholarship_type'     => ['nullable', 'string', 'max:100'],
            'scholarship_name'     => ['nullable', 'string', 'max:150'],
            'amount'               => ['required', 'numeric', 'min:0'],
            'applicable_semesters' => ['nullable', 'array'],
            'applicable_semesters.*' => ['integer', 'min:1', 'max:8'],
            'start_date'           => ['nullable', 'date'],
            'end_date'             => ['nullable', 'date'],
            'status'               => ['required', 'in:active,inactive,expired'],
            'remarks'              => ['nullable', 'string', 'max:500'],
        ]);

        $scholarship->update($validated);

        // Recalculate cached flag
        $hasActive = StudentScholarship::where('student_id', $scholarship->student_id)
            ->where('status', 'active')->exists();
        $scholarship->student->update(['scholarship_eligible' => $hasActive]);

        return redirect()
            ->route('admin.scholarships.index')
            ->with('success', 'Scholarship updated successfully.');
    }

    /**
     * Delete a scholarship record.
     */
    public function destroy(StudentScholarship $scholarship)
    {
        $this->assertTenantModel($scholarship);

        $studentId = $scholarship->student_id;
        $scholarship->delete();

        // Recalculate cached flag
        $hasActive = StudentScholarship::where('student_id', $studentId)
            ->where('status', 'active')->exists();
        Student::find($studentId)?->update(['scholarship_eligible' => $hasActive]);

        return back()->with('success', 'Scholarship record removed.');
    }

    /**
     * Verify the scholarship belongs to the current tenant.
     */
    private function assertTenantModel(StudentScholarship $scholarship): void
    {
        if ($scholarship->tenant_id !== $this->tenantId()) {
            abort(403, 'Unauthorized access.');
        }
    }
}
