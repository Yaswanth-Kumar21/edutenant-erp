<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Branch;
use App\Models\FeePayment;
use App\Models\FeeType;
use App\Models\Student;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * FeeDashboardDemoSeeder
 *
 * Adds rich fee payment data for Sri Venkateswara Degree College
 * so the Fee Dashboard looks impressive for demos.
 *
 * Generates:
 *  - 6 months of fee collection history
 *  - Multiple fee types per student
 *  - Mix of Paid / Partial / Pending statuses
 *  - Various payment modes (Cash, UPI, Card, NEFT)
 */
class FeeDashboardDemoSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::where('slug', 'svc')->firstOrFail();
        $academicYear = AcademicYear::where('tenant_id', $tenant->id)
            ->where('is_current', true)
            ->firstOrFail();

        $adminUser = User::where('email', 'admin@svc.edu')->firstOrFail();

        $students = Student::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->get();

        if ($students->isEmpty()) {
            $this->command->error('No students found. Run SriVenkateswaraDataSeeder first.');
            return;
        }

        // Get fee types
        $feeTypes = FeeType::where('tenant_id', $tenant->id)->get()->keyBy('code');

        $uniformFee  = $feeTypes['UNIFORM']    ?? null;
        $examFee     = $feeTypes['EXAM']       ?? null;
        $udfFee      = $feeTypes['UDF']        ?? null;
        $recordFee   = $feeTypes['RECORD']     ?? null;
        $vehicleFee  = $feeTypes['VEHICLE']    ?? null;
        $tuitionFee  = $feeTypes['TUITION']    ?? null;
        $otherFee    = $feeTypes['OTHER']      ?? null;

        $modes = ['cash', 'upi', 'online', 'cheque', 'cash', 'upi', 'cash']; // weighted towards cash/upi
        $counter = 100;

        // Delete old auto-seeded payments to avoid duplicates
        FeePayment::where('tenant_id', $tenant->id)
            ->where('receipt_number', 'like', 'DEMO-%')
            ->forceDelete();

        foreach ($students as $index => $student) {
            $hasBSc = $student->branch && $student->branch->has_record_fee;
            $hasVehicle = $student->vehicle_opted;

            // ── 1. Tuition Fee (all students, paid, Jan–Feb) ──────────────
            if ($tuitionFee) {
                $amount = $hasBSc ? 8000 : ($student->branch?->name === 'BCom General' ? 6000 : 5000);
                $this->createPayment($tenant->id, $student->id, $tuitionFee->id,
                    $academicYear->id, $adminUser->id,
                    'DEMO-TUI-' . str_pad($counter++, 5, '0', STR_PAD_LEFT),
                    $amount, $amount, 0, 'paid',
                    $modes[array_rand($modes)],
                    Carbon::now()->subMonths(5)->subDays(rand(1,5))->toDateString()
                );
            }

            // ── 2. Uniform Fee (all students, paid) ───────────────────────
            if ($uniformFee) {
                $this->createPayment($tenant->id, $student->id, $uniformFee->id,
                    $academicYear->id, $adminUser->id,
                    'DEMO-UNI-' . str_pad($counter++, 5, '0', STR_PAD_LEFT),
                    1500, 1500, 0, 'paid',
                    $modes[array_rand($modes)],
                    Carbon::now()->subMonths(4)->subDays(rand(1,10))->toDateString()
                );
            }

            // ── 3. Exam Fee Sem 1 (80% paid, 20% partial) ────────────────
            if ($examFee) {
                $paid = ($index % 5 !== 0); // 80% paid
                $this->createPayment($tenant->id, $student->id, $examFee->id,
                    $academicYear->id, $adminUser->id,
                    'DEMO-EXM-' . str_pad($counter++, 5, '0', STR_PAD_LEFT),
                    800, $paid ? 800 : 400, 0,
                    $paid ? 'paid' : 'partial',
                    $modes[array_rand($modes)],
                    Carbon::now()->subMonths(3)->subDays(rand(1,15))->toDateString(),
                    semester: 1
                );
            }

            // ── 4. Exam Fee Sem 2 (60% paid, 40% pending) ────────────────
            if ($examFee) {
                $status = ($index % 3 === 0) ? 'pending' : 'paid';
                $this->createPayment($tenant->id, $student->id, $examFee->id,
                    $academicYear->id, $adminUser->id,
                    'DEMO-EXM2-' . str_pad($counter++, 5, '0', STR_PAD_LEFT),
                    800, $status === 'paid' ? 800 : 0, 0,
                    $status,
                    $modes[array_rand($modes)],
                    Carbon::now()->subMonths(2)->subDays(rand(1,20))->toDateString(),
                    semester: 2
                );
            }

            // ── 5. UDF Fee (all paid) ─────────────────────────────────────
            if ($udfFee) {
                $this->createPayment($tenant->id, $student->id, $udfFee->id,
                    $academicYear->id, $adminUser->id,
                    'DEMO-UDF-' . str_pad($counter++, 5, '0', STR_PAD_LEFT),
                    500, 500, 0, 'paid',
                    $modes[array_rand($modes)],
                    Carbon::now()->subMonths(4)->subDays(rand(1,8))->toDateString()
                );
            }

            // ── 6. Record Fee (only B.Sc branches) ───────────────────────
            if ($recordFee && $hasBSc) {
                $this->createPayment($tenant->id, $student->id, $recordFee->id,
                    $academicYear->id, $adminUser->id,
                    'DEMO-REC-' . str_pad($counter++, 5, '0', STR_PAD_LEFT),
                    300, 300, 0, 'paid',
                    'cash',
                    Carbon::now()->subMonths(3)->subDays(rand(1,10))->toDateString(),
                    semester: 1
                );
            }

            // ── 7. Vehicle Fee (monthly, for students with vehicle) ───────
            if ($vehicleFee && $hasVehicle) {
                for ($month = 5; $month >= 1; $month--) {
                    $paid = ($month > 1); // last month may be pending
                    $this->createPayment($tenant->id, $student->id, $vehicleFee->id,
                        $academicYear->id, $adminUser->id,
                        'DEMO-VEH-' . str_pad($counter++, 5, '0', STR_PAD_LEFT),
                        600, $paid ? 600 : 0, 0,
                        $paid ? 'paid' : 'pending',
                        'cash',
                        Carbon::now()->subMonths($month)->startOfMonth()->addDays(rand(1,5))->toDateString()
                    );
                }
            }

            // ── 8. Internship Fee (final year students only — odd index) ──
            if ($otherFee && $index % 4 === 0) {
                $this->createPayment($tenant->id, $student->id, $otherFee->id,
                    $academicYear->id, $adminUser->id,
                    'DEMO-OTH-' . str_pad($counter++, 5, '0', STR_PAD_LEFT),
                    500, 500, 0, 'paid',
                    'upi',
                    Carbon::now()->subMonths(1)->subDays(rand(1,20))->toDateString()
                );
            }
        }

        // ── Summary ───────────────────────────────────────────────────────────
        $total   = FeePayment::where('tenant_id', $tenant->id)->count();
        $paid    = FeePayment::where('tenant_id', $tenant->id)->where('status','paid')->count();
        $pending = FeePayment::where('tenant_id', $tenant->id)->where('status','pending')->count();
        $partial = FeePayment::where('tenant_id', $tenant->id)->where('status','partial')->count();
        $revenue = FeePayment::where('tenant_id', $tenant->id)->where('status','paid')->sum('amount_paid');

        $this->command->info('✅ Fee Dashboard Demo Data Seeded!');
        $this->command->info("   Total Records : {$total}");
        $this->command->info("   Paid          : {$paid}");
        $this->command->info("   Partial       : {$partial}");
        $this->command->info("   Pending       : {$pending}");
        $this->command->info("   Total Revenue : ₹" . number_format($revenue));
    }

    private function createPayment(
        int $tenantId,
        int $studentId,
        int $feeTypeId,
        int $academicYearId,
        int $collectedBy,
        string $receiptNumber,
        float $amountDue,
        float $amountPaid,
        float $discount,
        string $status,
        string $paymentMode,
        string $paymentDate,
        int $semester = 1
    ): void {
        // Skip if same receipt already exists
        if (FeePayment::where('receipt_number', $receiptNumber)->exists()) {
            return;
        }

        FeePayment::create([
            'tenant_id'        => $tenantId,
            'student_id'       => $studentId,
            'fee_type_id'      => $feeTypeId,
            'academic_year_id' => $academicYearId,
            'collected_by'     => $collectedBy,
            'receipt_number'   => $receiptNumber,
            'amount_due'       => $amountDue,
            'amount_paid'      => $amountPaid,
            'discount'         => $discount,
            'fine'             => 0,
            'semester'         => $semester,
            'payment_mode'     => $paymentMode,
            'payment_date'     => $paymentDate,
            'status'           => $status,
            'is_exempted'      => false,
        ]);
    }
}
