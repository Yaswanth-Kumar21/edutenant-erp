<?php

namespace Database\Seeders;

use App\Models\Payroll;
use App\Models\Staff;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * PayrollDemoSeeder — generates 3 months of paid payrolls for all staff
 */
class PayrollDemoSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::where('slug', 'svc')->firstOrFail();
        $admin  = User::where('email', 'admin@svc.edu')->firstOrFail();
        $allStaff = Staff::where('tenant_id', $tenant->id)->where('status', 'active')->get();

        if ($allStaff->isEmpty()) {
            $this->command->error('No staff found.');
            return;
        }

        // Delete old demo payrolls
        Payroll::where('tenant_id', $tenant->id)
            ->where('payroll_number', 'like', 'PR-%')
            ->forceDelete();

        $counter = 1;
        $months = [
            ['month' => Carbon::now()->subMonths(2)->month, 'year' => Carbon::now()->subMonths(2)->year],
            ['month' => Carbon::now()->subMonths(1)->month, 'year' => Carbon::now()->subMonths(1)->year],
            ['month' => Carbon::now()->month,               'year' => Carbon::now()->year],
        ];

        foreach ($months as $period) {
            foreach ($allStaff as $staff) {
                $gross       = $staff->monthly_salary ?? 25000;
                $basic       = $staff->basic_salary   ?? ($gross * 0.6);
                $hra         = $staff->hra             ?? ($gross * 0.2);
                $da          = $staff->da              ?? ($gross * 0.1);
                $other       = $staff->other_allowances ?? ($gross * 0.1);
                $pf          = $staff->pf_deduction    ?? ($gross * 0.12);
                $presentDays = rand(24, 26);
                $absentDays  = 26 - $presentDays;
                $perDay      = round($gross / 26, 2);
                $absentDed   = round($perDay * $absentDays, 2);
                $totalDed    = round($pf + $absentDed, 2);
                $net         = round($gross - $totalDed, 2);

                // Only mark current month as approved (not yet paid), past months paid
                $isPast   = ($period['month'] !== Carbon::now()->month || $period['year'] !== Carbon::now()->year);
                $status   = $isPast ? 'paid' : 'approved';
                $payDate  = $isPast
                    ? Carbon::create($period['year'], $period['month'])->endOfMonth()->toDateString()
                    : null;

                // Skip if already exists for this staff/month/year
                if (Payroll::where('tenant_id', $tenant->id)
                    ->where('staff_id', $staff->id)
                    ->where('month', $period['month'])
                    ->where('year', $period['year'])
                    ->exists()) {
                    continue;
                }

                Payroll::create([
                    'tenant_id'           => $tenant->id,
                    'staff_id'            => $staff->id,
                    'generated_by'        => $admin->id,
                    'payroll_number'      => 'PR-' . $period['year'] . '-' . str_pad($period['month'], 2, '0', STR_PAD_LEFT) . '-' . str_pad($counter++, 4, '0', STR_PAD_LEFT),
                    'month'               => $period['month'],
                    'year'                => $period['year'],
                    'gross_salary'        => $gross,
                    'basic_salary'        => $basic,
                    'hra'                 => $hra,
                    'da'                  => $da,
                    'other_allowances'    => $other,
                    'working_days'        => 26,
                    'present_days'        => $presentDays,
                    'absent_days'         => $absentDays,
                    'leave_days'          => 0,
                    'holiday_days'        => 4,
                    'half_days'           => 0,
                    'allowed_holidays'    => $staff->allowed_holidays_per_month ?? 2,
                    'per_day_salary'      => $perDay,
                    'absent_deduction'    => $absentDed,
                    'pf_deduction'        => $pf,
                    'tax_deduction'       => 0,
                    'other_deductions'    => 0,
                    'total_deductions'    => $totalDed,
                    'net_salary'          => $net,
                    'status'              => $status,
                    'payment_mode'        => 'bank_transfer',
                    'payment_date'        => $payDate,
                    'remarks'             => 'Auto-generated demo payroll',
                ]);
            }
        }

        $paid  = Payroll::where('tenant_id', $tenant->id)->where('status', 'paid')->count();
        $appr  = Payroll::where('tenant_id', $tenant->id)->where('status', 'approved')->count();
        $total = Payroll::where('tenant_id', $tenant->id)->sum('net_salary');

        $this->command->info('✅ Payroll Demo Data Seeded!');
        $this->command->info('   Paid     : ' . $paid . ' payrolls');
        $this->command->info('   Approved : ' . $appr . ' payrolls (current month)');
        $this->command->info('   Total Net: ₹' . number_format($total));
    }
}
