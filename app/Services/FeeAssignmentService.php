<?php

namespace App\Services;

use App\Models\FeePayment;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

/**
 * FeeAssignmentService
 *
 * Handles assigning fee structures to students.
 * Creates pending FeePayment records without collecting money.
 */
class FeeAssignmentService
{
    public function assignToStudent(Student $student, array $data, int $tenantId): FeePayment
    {
        return DB::transaction(function () use ($student, $data, $tenantId) {
            $existing = FeePayment::where('tenant_id', $tenantId)
                ->where('student_id', $student->id)
                ->where('fee_type_id', $data['fee_type_id'])
                ->where('academic_year_id', $data['academic_year_id'])
                ->when(isset($data['semester']), fn($q) => $q->where('semester', $data['semester']))
                ->whereIn('status', ['pending', 'partial'])
                ->first();

            if ($existing) return $existing;

            return FeePayment::create([
                'tenant_id'        => $tenantId,
                'student_id'       => $student->id,
                'fee_type_id'      => $data['fee_type_id'],
                'academic_year_id' => $data['academic_year_id'],
                'collected_by'     => null,
                'receipt_number'   => FeeCollectionService::generateReceiptNumber($tenantId),
                'amount_due'       => $data['amount_due'],
                'amount_paid'      => 0,
                'discount'         => 0,
                'fine'             => 0,
                'semester'         => $data['semester'] ?? null,
                'payment_mode'     => 'cash',
                'payment_date'     => now()->toDateString(),
                'status'           => 'pending',
                'is_exempted'      => false,
            ]);
        });
    }

    public function bulkAssign(array $data, int $tenantId): int
    {
        $students = Student::where('tenant_id', $tenantId)
            ->where('branch_id', $data['branch_id'])
            ->where('academic_year_id', $data['academic_year_id'])
            ->where('status', 'active')
            ->get();

        $count = 0;
        foreach ($students as $student) {
            $this->assignToStudent($student, $data, $tenantId);
            $count++;
        }

        return $count;
    }
}
