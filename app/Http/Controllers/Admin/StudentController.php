<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateStudentRequest;
use App\Models\AcademicYear;
use App\Models\Branch;
use App\Models\Course;
use App\Models\Stream;
use App\Models\Student;
use App\Services\AdmissionService;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * StudentController
 *
 * Handles student listing, viewing, editing, and deletion.
 * Admission (create/store) is handled by AdmissionController.
 */
class StudentController extends Controller
{
    /**
     * Student listing with advanced filters and pagination.
     */
    public function index(Request $request)
    {
        $tenantId = TenantService::getTenantId();

        $query = Student::where('tenant_id', $tenantId)
            ->with(['branch.course.stream', 'academicYear']);

        // ── Search ────────────────────────────────────────────────────────
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('admission_number', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('aadhaar_number', 'like', "%{$search}%");
            });
        }

        // ── Filters ───────────────────────────────────────────────────────
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('stream_id')) {
            // Filter by stream via branch → course → stream
            $branchIds = Branch::where('tenant_id', $tenantId)
                ->whereHas('course', fn($q) => $q->where('stream_id', $request->stream_id))
                ->pluck('id');
            $query->whereIn('branch_id', $branchIds);
        }

        if ($request->filled('course_id')) {
            $branchIds = Branch::where('tenant_id', $tenantId)
                ->where('course_id', $request->course_id)
                ->pluck('id');
            $query->whereIn('branch_id', $branchIds);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // ── Sorting ───────────────────────────────────────────────────────
        $sortBy  = $request->get('sort', 'admission_date');
        $sortDir = $request->get('dir', 'desc');
        $allowedSorts = ['admission_date', 'first_name', 'admission_number', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');
        }

        $students     = $query->paginate(20)->withQueryString();
        $branches     = Branch::where('tenant_id', $tenantId)->with('course')->orderBy('name')->get();
        $streams      = Stream::where('tenant_id', $tenantId)->where('is_active', true)->get();
        $courses      = Course::where('tenant_id', $tenantId)->where('is_active', true)->get();
        $academicYears = AcademicYear::where('tenant_id', $tenantId)->orderByDesc('is_current')->get();

        // ── Summary stats for the listing page ───────────────────────────
        $stats = [
            'total'      => Student::where('tenant_id', $tenantId)->count(),
            'active'     => Student::where('tenant_id', $tenantId)->where('status', 'active')->count(),
            'this_month' => Student::where('tenant_id', $tenantId)
                ->whereMonth('admission_date', now()->month)
                ->whereYear('admission_date', now()->year)
                ->count(),
        ];

        return view('admin.students.index', compact(
            'students', 'branches', 'streams', 'courses', 'academicYears', 'stats'
        ));
    }

    /**
     * Show student detail page.
     */
    public function show(Student $student)
    {
        $this->authorizeTenant($student);

        $student->load([
            'branch.course.stream',
            'academicYear',
            'feePayments.feeType',
            'attendance',
            'profile',
            'guardian',
            'certificates',
            'admissionReceipts',
            'user',
        ]);

        return view('admin.students.show', compact('student'));
    }

    /**
     * Full student profile page (detailed view).
     */
    public function profile(Student $student)
    {
        $this->authorizeTenant($student);

        $student->load([
            'branch.course.stream',
            'academicYear',
            'feePayments.feeType',
            'attendance',
            'profile',
            'guardian',
            'certificates',
            'admissionReceipts',
            'user',
        ]);

        return view('admin.students.profile', compact('student'));
    }

    /**
     * Show edit form.
     */
    public function edit(Student $student)
    {
        $this->authorizeTenant($student);

        $tenantId      = TenantService::getTenantId();
        $branches      = Branch::where('tenant_id', $tenantId)->with('course.stream')->orderBy('name')->get();
        $academicYears = AcademicYear::where('tenant_id', $tenantId)->get();

        $student->load(['profile', 'guardian']);

        return view('admin.students.edit', compact('student', 'branches', 'academicYears'));
    }

    /**
     * Update student record.
     */
    public function update(UpdateStudentRequest $request, Student $student)
    {
        $this->authorizeTenant($student);

        $data = $request->validated();

        // ── Handle photo upload ───────────────────────────────────────────
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($student->photo) {
                Storage::disk('public')->delete($student->photo);
            }
            $data['photo'] = $request->file('photo')->store(
                'photos/' . TenantService::getTenantId(),
                'public'
            );
        } else {
            unset($data['photo']);
        }

        // ── Update core student fields ────────────────────────────────────
        $studentFields = [
            'first_name', 'last_name', 'date_of_birth', 'gender', 'blood_group',
            'aadhaar_number', 'phone', 'email', 'address', 'city', 'state', 'pincode',
            'photo', 'marks_10th', 'marks_12th', 'current_semester', 'current_year',
            'category', 'status', 'university_reg_number', 'scholarship_eligible',
            'vehicle_opted', 'vehicle_start_date',
        ];

        $student->update(array_intersect_key($data, array_flip($studentFields)));

        // ── Update guardian details ───────────────────────────────────────
        $guardianFields = [
            'father_name', 'father_occupation', 'father_phone',
            'mother_name', 'mother_phone', 'annual_income',
        ];

        $guardianData = array_intersect_key($data, array_flip($guardianFields));
        if (!empty($guardianData)) {
            $student->guardian()->updateOrCreate(
                ['student_id' => $student->id],
                array_merge($guardianData, ['tenant_id' => $student->tenant_id])
            );
        }

        return redirect()
            ->route('admin.students.show', $student)
            ->with('success', 'Student profile updated successfully.');
    }

    /**
     * Soft-delete a student.
     */
    public function destroy(Student $student)
    {
        $this->authorizeTenant($student);

        $student->delete();

        return redirect()
            ->route('admin.students.index')
            ->with('success', "Student {$student->full_name} has been archived.");
    }

    /**
     * Create a login account for an existing student who doesn't have one.
     * Used for students admitted before this feature was added.
     */
    public function createLogin(Request $request, Student $student)
    {
        $this->authorizeTenant($student);

        // Already has a login
        if ($student->user_id) {
            return back()->with('info', 'This student already has a login account.');
        }

        if (empty($student->email)) {
            return back()->with('error', 'Cannot create login — student has no email address. Please add an email first.');
        }

        $tenantId = TenantService::getTenantId();

        $user = AdmissionService::createStudentLoginAccount($student, $tenantId, [
            'email'      => $student->email,
            'phone'      => $student->phone,
            'first_name' => $student->first_name,
            'last_name'  => $student->last_name,
        ]);

        if ($user) {
            $student->update(['user_id' => $user->id]);
            return back()->with('success',
                "Login created. Email: {$student->email} | Password: " .
                ($student->phone ?? $student->admission_number)
            );
        }

        return back()->with('error', 'Login account could not be created. The email may already be in use.');
    }

    /**
     * Ensure the student belongs to the current tenant.
     */
    private function authorizeTenant(Student $student): void
    {
        if ($student->tenant_id !== TenantService::getTenantId()) {
            abort(403, 'Unauthorized access.');
        }
    }
}
