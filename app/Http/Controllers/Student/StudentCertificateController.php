<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentCertificate;
use App\Services\TenantService;
use Illuminate\Support\Facades\Storage;

/**
 * StudentCertificateController
 *
 * Handles the student-facing certificate list and download.
 * Students can ONLY see and download their own certificates.
 */
class StudentCertificateController extends Controller
{
    public function index()
    {
        $user    = auth()->user();
        $student = $user->student;

        if (!$student) {
            abort(403, 'No student profile linked to this account.');
        }

        $tenant = TenantService::getTenant();

        $certificates = StudentCertificate::where('student_id', $student->id)
            ->where('tenant_id', $student->tenant_id)
            ->orderBy('created_at', 'desc')
            ->get();

        $allTypes     = StudentCertificate::TYPES;
        $submitted    = $certificates->pluck('certificate_type')->toArray();
        $missing      = array_diff(array_keys($allTypes), $submitted);

        return view('student.certificates.index', compact(
            'student', 'tenant', 'certificates', 'allTypes', 'missing'
        ));
    }

    /**
     * Download / view a certificate file.
     */
    public function download(StudentCertificate $certificate)
    {
        $user    = auth()->user();
        $student = $user->student;

        // Strict ownership check
        if (!$student || $certificate->student_id !== $student->id || $certificate->tenant_id !== $student->tenant_id) {
            abort(403, 'Access denied.');
        }

        if (!Storage::disk('public')->exists($certificate->file_path)) {
            abort(404, 'File not found.');
        }

        $filePath = Storage::disk('public')->path($certificate->file_path);
        $mimeType = $certificate->mime_type ?? 'application/octet-stream';

        return response()->file($filePath, [
            'Content-Type'        => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $certificate->original_filename . '"',
        ]);
    }
}
