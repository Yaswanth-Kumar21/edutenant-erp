<?php

namespace App\Services;

use App\Models\AdmissionReceipt;
use App\Models\GuardianDetail;
use App\Models\Role;
use App\Models\Student;
use App\Models\StudentCertificate;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * AdmissionService
 *
 * Handles all business logic for the student admission workflow.
 * Keeps the controller thin and logic testable.
 */
class AdmissionService
{
    /**
     * Generate a unique, tenant-scoped admission number.
     * Format: EDU-YYYY-NNNN (e.g. EDU-2026-0001)
     *
     * Uses a DB lock to prevent race conditions in concurrent admissions.
     */
    public static function generateAdmissionNumber(int $tenantId): string
    {
        return DB::transaction(function () use ($tenantId) {
            $year = now()->year;

            // Lock the latest record for this tenant+year to prevent duplicates
            $last = Student::where('tenant_id', $tenantId)
                ->where('admission_number', 'like', "EDU-{$year}-%")
                ->lockForUpdate()
                ->latest('id')
                ->first();

            if ($last) {
                // Extract the numeric suffix and increment
                $parts = explode('-', $last->admission_number);
                $nextNum = ((int) end($parts)) + 1;
            } else {
                $nextNum = 1;
            }

            return 'EDU-' . $year . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Generate a unique admission receipt number.
     * Format: ADM-RCPT-YYYY-NNNNN
     */
    public static function generateReceiptNumber(int $tenantId): string
    {
        return DB::transaction(function () use ($tenantId) {
            $year = now()->year;

            $last = AdmissionReceipt::where('tenant_id', $tenantId)
                ->where('receipt_number', 'like', "ADM-RCPT-{$year}-%")
                ->lockForUpdate()
                ->latest('id')
                ->first();

            if ($last) {
                $parts = explode('-', $last->receipt_number);
                $nextNum = ((int) end($parts)) + 1;
            } else {
                $nextNum = 1;
            }

            return 'ADM-RCPT-' . $year . '-' . str_pad($nextNum, 5, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Process the full admission workflow in a single DB transaction.
     * Creates: Student, StudentProfile, GuardianDetail, Certificates, AdmissionReceipt
     *
     * @param  array  $data     Validated request data
     * @param  int    $tenantId Current tenant ID
     * @param  int    $userId   Authenticated user ID
     * @param  array  $files    Uploaded certificate files (keyed by certificate_type)
     * @param  string|null $photoPath  Already-stored photo path
     * @return Student
     */
    public static function processAdmission(
        array $data,
        int $tenantId,
        int $userId,
        array $files = [],
        ?string $photoPath = null
    ): Student {
        return DB::transaction(function () use ($data, $tenantId, $userId, $files, $photoPath) {

            // ── 1. Create Student ─────────────────────────────────────────
            $admissionNumber = self::generateAdmissionNumber($tenantId);

            $student = Student::create([
                'tenant_id'              => $tenantId,
                'branch_id'              => $data['branch_id'],
                'academic_year_id'       => $data['academic_year_id'],
                'admission_number'       => $admissionNumber,
                'admission_date'         => $data['admission_date'],
                'university_reg_number'  => $data['university_reg_number'] ?? null,
                'first_name'             => $data['first_name'],
                'last_name'              => $data['last_name'],
                'father_name'            => $data['father_name'] ?? null,
                'mother_name'            => $data['mother_name'] ?? null,
                'date_of_birth'          => $data['date_of_birth'] ?? null,
                'gender'                 => $data['gender'],
                'blood_group'            => $data['blood_group'] ?? null,
                'aadhaar_number'         => $data['aadhaar_number'] ?? null,
                'phone'                  => $data['phone'],
                'email'                  => $data['email'] ?? null,
                'address'                => $data['address'] ?? null,
                'city'                   => $data['city'] ?? null,
                'state'                  => $data['state'] ?? null,
                'pincode'                => $data['pincode'] ?? null,
                'photo'                  => $photoPath,
                'marks_10th'             => $data['marks_10th'] ?? null,
                'marks_12th'             => $data['marks_12th'] ?? null,
                'previous_institution'   => $data['previous_institution'] ?? null,
                'current_semester'       => $data['current_semester'] ?? 1,
                'current_year'           => $data['current_year'] ?? 1,
                'category'               => $data['category'],
                'scholarship_eligible'   => (bool) ($data['scholarship_eligible'] ?? false),
                'vehicle_opted'          => (bool) ($data['vehicle_opted'] ?? false),
                'vehicle_start_date'     => ($data['vehicle_opted'] ?? false) ? ($data['vehicle_start_date'] ?? null) : null,
                'status'                 => 'active',
                'admission_step'         => 4,
                'certificates_submitted' => [],
            ]);

            // ── 2. Create Student Profile ─────────────────────────────────
            StudentProfile::create([
                'tenant_id'              => $tenantId,
                'student_id'             => $student->id,
                'aadhaar_number'         => $data['aadhaar_number'] ?? null,
                'blood_group'            => $data['blood_group'] ?? null,
                'previous_institution'   => $data['previous_institution'] ?? null,
                'university_reg_number'  => $data['university_reg_number'] ?? null,
                'scholarship_applied'    => (bool) ($data['scholarship_eligible'] ?? false),
                'hostel_required'        => false,
            ]);

            // ── 3. Create Guardian Details ────────────────────────────────
            GuardianDetail::create([
                'tenant_id'              => $tenantId,
                'student_id'             => $student->id,
                'father_name'            => $data['father_name'] ?? null,
                'father_occupation'      => $data['father_occupation'] ?? null,
                'father_phone'           => $data['father_phone'] ?? null,
                'father_email'           => $data['father_email'] ?? null,
                'mother_name'            => $data['mother_name'] ?? null,
                'mother_occupation'      => $data['mother_occupation'] ?? null,
                'mother_phone'           => $data['mother_phone'] ?? null,
                'annual_income'          => $data['annual_income'] ?? null,
                'scholarship_eligible'   => (bool) ($data['guardian_scholarship_eligible'] ?? false),
                'scholarship_details'    => $data['scholarship_details'] ?? null,
            ]);

            // ── 4. Store Certificates ─────────────────────────────────────
            $certTypes = $data['certificate_types'] ?? [];
            $submittedCerts = [];

            foreach ($files as $index => $file) {
                $certType  = $certTypes[$index] ?? 'other';
                $certLabel = StudentCertificate::TYPES[$certType] ?? 'Other Document';

                // Tenant-isolated storage: certificates/{tenant_id}/{student_id}/
                $path = $file->store(
                    "certificates/{$tenantId}/{$student->id}",
                    'public'
                );

                StudentCertificate::create([
                    'tenant_id'         => $tenantId,
                    'student_id'        => $student->id,
                    'certificate_type'  => $certType,
                    'certificate_label' => $certLabel,
                    'file_path'         => $path,
                    'original_filename' => $file->getClientOriginalName(),
                    'mime_type'         => $file->getMimeType(),
                    'file_size'         => $file->getSize(),
                ]);

                $submittedCerts[] = $certType;
            }

            // Update the JSON tracking field on student
            if (!empty($submittedCerts)) {
                $student->update(['certificates_submitted' => array_unique($submittedCerts)]);
            }

            // ── 5. Generate Admission Receipt ─────────────────────────────
            $admissionFee = (float) ($data['admission_fee'] ?? 0);
            $tuitionFee   = (float) ($data['tuition_fee'] ?? 0);
            $otherFees    = (float) ($data['other_fees'] ?? 0);
            $totalAmount  = $admissionFee + $tuitionFee + $otherFees;
            $amountPaid   = (float) ($data['amount_paid'] ?? $totalAmount);
            $balanceDue   = max(0, $totalAmount - $amountPaid);

            $receiptStatus = 'pending';
            if ($totalAmount == 0 || $amountPaid >= $totalAmount) {
                $receiptStatus = 'paid';
            } elseif ($amountPaid > 0) {
                $receiptStatus = 'partial';
            }

            AdmissionReceipt::create([
                'tenant_id'             => $tenantId,
                'student_id'            => $student->id,
                'academic_year_id'      => $data['academic_year_id'],
                'generated_by'          => $userId,
                'receipt_number'        => self::generateReceiptNumber($tenantId),
                'admission_fee'         => $admissionFee,
                'tuition_fee'           => $tuitionFee,
                'other_fees'            => $otherFees,
                'total_amount'          => $totalAmount,
                'amount_paid'           => $amountPaid,
                'balance_due'           => $balanceDue,
                'payment_mode'          => $data['payment_mode'] ?? 'cash',
                'transaction_reference' => $data['transaction_reference'] ?? null,
                'payment_date'          => $data['payment_date'] ?? now()->toDateString(),
                'status'                => $receiptStatus,
                'fee_details'           => [
                    'admission_fee' => $admissionFee,
                    'tuition_fee'   => $tuitionFee,
                    'other_fees'    => $otherFees,
                ],
            ]);

            // ── 6. Create Student Login Account ───────────────────────────
            // Only create if the student has an email address.
            // Default password = phone number (student must change on first login).
            $studentLoginUser = self::createStudentLoginAccount($student, $tenantId, $data);
            if ($studentLoginUser) {
                $student->update(['user_id' => $studentLoginUser->id]);
            }

            return $student;
        });
    }

    /**
     * Create a login User account for a newly admitted student.
     *
     * Rules:
     * - Email is required for login. If no email provided, skip silently.
     * - If a user with that email already exists, link the existing user.
     * - Default password = phone number.
     * - If no phone, default password = admission number.
     * - Role = 'student'.
     *
     * @return User|null
     */
    public static function createStudentLoginAccount(
        Student $student,
        int $tenantId,
        array $data
    ): ?User {
        $email = $data['email'] ?? null;

        // Cannot create login without an email
        if (empty($email)) {
            return null;
        }

        // If a user with this email already exists, just link them
        $existing = User::where('email', $email)->first();
        if ($existing) {
            return $existing;
        }

        // Default password: phone number, fallback to admission number
        $defaultPassword = !empty($data['phone'])
            ? $data['phone']
            : $student->admission_number;

        // Fetch the student role
        $studentRole = Role::where('name', Role::STUDENT)->first();

        try {
            $user = User::create([
                'tenant_id' => $tenantId,
                'role_id'   => $studentRole?->id,
                'name'      => trim($data['first_name'] . ' ' . $data['last_name']),
                'email'     => $email,
                'phone'     => $data['phone'] ?? null,
                'password'  => Hash::make($defaultPassword),
                'status'    => 'active',
            ]);

            return $user;
        } catch (\Throwable $e) {
            // Log but don't fail the whole admission if user creation fails
            Log::warning('Student login account creation failed during admission.', [
                'student_id' => $student->id,
                'email'      => $email,
                'error'      => $e->getMessage(),
            ]);

            return null;
        }
    }
}
