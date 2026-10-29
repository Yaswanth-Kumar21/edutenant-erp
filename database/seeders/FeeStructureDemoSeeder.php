<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Branch;
use App\Models\FeeStructure;
use App\Models\FeeType;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

/**
 * FeeStructureDemoSeeder
 * Creates semester-wise and branch-wise fee structures for Sri Venkateswara College
 */
class FeeStructureDemoSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::where('slug', 'svc')->firstOrFail();
        $ay     = AcademicYear::where('tenant_id', $tenant->id)->where('is_current', true)->firstOrFail();

        $branches  = Branch::where('tenant_id', $tenant->id)->get()->keyBy('name');
        $feeTypes  = FeeType::where('tenant_id', $tenant->id)->get()->keyBy('code');

        // Delete existing structures for this year
        FeeStructure::where('tenant_id', $tenant->id)
            ->where('academic_year_id', $ay->id)
            ->delete();

        // Fee amounts per branch
        $branchFees = [
            'MPC'         => ['TUITION' => 8000, 'RECORD' => 300],
            'MPCs'        => ['TUITION' => 8000, 'RECORD' => 300],
            'BiPC'        => ['TUITION' => 9000, 'RECORD' => 300],
            'CZBt'        => ['TUITION' => 9000, 'RECORD' => 300],
            'HEP'         => ['TUITION' => 5000],
            'HPS'         => ['TUITION' => 5000],
            'BCom General'=> ['TUITION' => 6000],
        ];

        // Common fees for ALL branches (same amount)
        $commonFees = [
            'UNIFORM'    => 1500,
            'EXAM'       => 800,
            'UDF'        => 500,
            'VEHICLE'    => 600,
            'INTERNSHIP' => 1000,
        ];

        $created = 0;

        foreach ($branches as $branchName => $branch) {
            // Common fees — semester 1
            foreach ($commonFees as $code => $amount) {
                if (!isset($feeTypes[$code])) continue;

                FeeStructure::create([
                    'tenant_id'       => $tenant->id,
                    'fee_type_id'     => $feeTypes[$code]->id,
                    'branch_id'       => $branch->id,
                    'stream_id'       => $branch->course?->stream_id,
                    'academic_year_id'=> $ay->id,
                    'semester'        => 1,
                    'year'            => 1,
                    'amount'          => $amount,
                    'is_active'       => true,
                ]);
                $created++;

                // Exam fee also for sem 2
                if ($code === 'EXAM') {
                    FeeStructure::create([
                        'tenant_id'       => $tenant->id,
                        'fee_type_id'     => $feeTypes[$code]->id,
                        'branch_id'       => $branch->id,
                        'stream_id'       => $branch->course?->stream_id,
                        'academic_year_id'=> $ay->id,
                        'semester'        => 2,
                        'year'            => 1,
                        'amount'          => $amount,
                        'is_active'       => true,
                    ]);
                    $created++;
                }
            }

            // Branch-specific fees
            if (isset($branchFees[$branchName])) {
                foreach ($branchFees[$branchName] as $code => $amount) {
                    if (!isset($feeTypes[$code])) continue;

                    FeeStructure::create([
                        'tenant_id'       => $tenant->id,
                        'fee_type_id'     => $feeTypes[$code]->id,
                        'branch_id'       => $branch->id,
                        'stream_id'       => $branch->course?->stream_id,
                        'academic_year_id'=> $ay->id,
                        'semester'        => 1,
                        'year'            => 1,
                        'amount'          => $amount,
                        'is_active'       => true,
                    ]);
                    $created++;
                }
            }
        }

        $this->command->info("✅ Fee Structures created: {$created} records for {$ay->name}");
    }
}
