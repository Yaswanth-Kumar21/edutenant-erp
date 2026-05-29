<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdmissionRequest;
use App\Models\AcademicYear;
use App\Models\AdmissionReceipt;
use App\Models\Branch;
use App\Models\Student;
use App\Models\StudentCertificate;
use App\Services\AdmissionService;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * AdmissionController
 *
 * Handles the full multi-step student admission workflow.
 * Separated from StudentController to keep each controller focused.
 *
 * Routes:
 *   GET  /admin/admissions/create       → wizard form
 *   POST /admin/admissions              → process admission
 *   GET  /admin/admissions/{student}/receipt → view receipt
 *   GET  /admin/admissions/{student}/receipt/print → print receipt
 */
class AdmissionController extends Controller
{
    /**
     * Show the multi-step admission wizard.
     */
    public function create()
    {
        $tenantId     = TenantService::getTenantId();
        $tenant       = TenantService::getTenant();

        // Load branches grouped by course → stream for the wizard dropdowns
        $branches     = Branch::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->with(['course.stream'])
            ->orderBy('name')
            ->get();

        $academicYears = AcademicYear::where('tenant_id', $tenantId)
            ->orderByDesc('is_current')
            ->orderByDesc('start_date')
            ->get();

        // Current academic year pre-selected
        $currentYear  = $academicYears->firstWhere('is_current', true);

        $certificateTypes = StudentCertificate::TYPES;

        return view('admin.admissions.create', compact(
            'branches', 'academicYears', 'currentYear', 'certificateTypes', 'tenant'
        ));
    }

    /**
     * Process the admission form and create all related records.
     */
    public function store(StoreAdmissionRequest $request)
    {
        $tenantId = TenantService::getTenantId();
        $userId   = auth()->id();
        $data     = $request->validated();

        // ── Handle profile photo upload ───────────────────────────────────
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store(
                "photos/{$tenantId}",
                'public'
            );
        }

        // ── Handle certificate uploads ────────────────────────────────────
        $certFiles = [];
        if ($request->hasFile('certificates')) {
            $certFiles = $request->file('certificates');
        }

        // ── Delegate to service layer ─────────────────────────────────────
        $student = AdmissionService::processAdmission(
            $data,
            $tenantId,
            $userId,
            $certFiles,
            $photoPath
        );

        return redirect()
            ->route('admin.admissions.receipt', $student)
            ->with('success', "Student admitted successfully! Admission No: {$student->admission_number}");
    }

    /**
     * Show the admission receipt for a student.
     */
    public function receipt(Student $student)
    {
        // Tenant isolation check
        $this->authorizeTenant($student);

        $student->load([
            'branch.course.stream',
            'academicYear',
            'guardian',
            'user',
            'admissionReceipts' => fn($q) => $q->latest()->limit(1),
        ]);

        $receipt = $student->admissionReceipts->first();
        $tenant  = TenantService::getTenant();

        return view('admin.admissions.receipt', compact('student', 'receipt', 'tenant'));
    }

    /**
     * Print-friendly receipt view (no sidebar/topnav).
     */
    public function printReceipt(Student $student)
    {
        $this->authorizeTenant($student);

        $student->load([
            'branch.course.stream',
            'academicYear',
            'guardian',
            'user',
            'admissionReceipts' => fn($q) => $q->latest()->limit(1),
        ]);

        $receipt = $student->admissionReceipts->first();
        $tenant  = TenantService::getTenant();

        return view('admin.admissions.receipt-print', compact('student', 'receipt', 'tenant'));
    }

    /**
     * Upload a certificate for an existing student.
     */
    public function uploadCertificate(Request $request, Student $student)
    {
        $this->authorizeTenant($student);

        $request->validate([
            'certificate_type' => ['required', 'string'],
            'certificate_file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $tenantId  = TenantService::getTenantId();
        $certType  = $request->certificate_type;
        $certLabel = StudentCertificate::TYPES[$certType] ?? 'Other Document';

        $path = $request->file('certificate_file')->store(
            "certificates/{$tenantId}/{$student->id}",
            'public'
        );

        StudentCertificate::create([
            'tenant_id'         => $tenantId,
            'student_id'        => $student->id,
            'certificate_type'  => $certType,
            'certificate_label' => $certLabel,
            'file_path'         => $path,
            'original_filename' => $request->file('certificate_file')->getClientOriginalName(),
            'mime_type'         => $request->file('certificate_file')->getMimeType(),
            'file_size'         => $request->file('certificate_file')->getSize(),
        ]);

        // Update the JSON tracking field
        $submitted   = $student->certificates_submitted ?? [];
        $submitted[] = $certType;
        $student->update(['certificates_submitted' => array_unique($submitted)]);

        return back()->with('success', "{$certLabel} uploaded successfully.");
    }

    /**
     * Delete a certificate.
     */
    public function deleteCertificate(StudentCertificate $certificate)
    {
        $this->authorizeTenant($certificate->student);

        // Remove file from storage
        Storage::disk('public')->delete($certificate->file_path);
        $certificate->delete();

        return back()->with('success', 'Certificate removed.');
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
