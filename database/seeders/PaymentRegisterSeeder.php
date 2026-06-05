<?php

namespace Database\Seeders;

use App\Models\PaymentRegister;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * PaymentRegisterSeeder
 *
 * Seeds realistic dummy data for:
 *  - Sri Venkateswara Degree College  (tenant_id=1, academic_year_id=5, year 2025-2026)
 *  - Sai Chaitanya Degree College     (tenant_id=2, academic_year_id=2, year 2024-2025)
 *
 * Covers ~4 months of entries per college so the dashboard
 * (running balance, yesterday spend, profit/loss) is clearly visible.
 */
class PaymentRegisterSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing dummy data for these two colleges only
        PaymentRegister::whereIn('tenant_id', [1, 2])->forceDelete();

        // ── Sri Venkateswara Degree College ───────────────────────────────────
        // tenant_id = 1 | academic_year_id = 5 | 2025-2026 | recorded_by = 1
        $this->seedCollege(
            tenantId:      1,
            yearId:        5,
            recordedBy:    1,
            startDate:     '2025-07-01',
            label:         'Sri Venkateswara'
        );

        // ── Sai Chaitanya Degree College ──────────────────────────────────────
        // tenant_id = 2 | academic_year_id = 2 | 2024-2025 | recorded_by = 2
        $this->seedCollege(
            tenantId:      2,
            yearId:        2,
            recordedBy:    2,
            startDate:     '2024-07-01',
            label:         'Sai Chaitanya'
        );

        $this->command->info('✅ Payment Register seeded for SVC and SCC colleges.');
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function seedCollege(int $tenantId, int $yearId, int $recordedBy, string $startDate, string $label): void
    {
        // 4 months × ~8 entries per month = ~32 entries per college
        $entries = $this->buildEntries($startDate);

        foreach ($entries as $entry) {
            $total = array_sum([
                $entry['staff_salary'],
                $entry['rented_vehicle_charges'],
                $entry['diesel_charges'],
                $entry['current_bill_charges'],
                $entry['repay_borrowings'],
                $entry['repay_interest'],
                $entry['stationary'],
                $entry['vehicle_finance'],
                $entry['contingent_charges'],
                $entry['vehicle_repairs_expenditures'],
                $entry['university_dd'],
                $entry['travelling_charges'],
                $entry['building_repairs_construction'],
                $entry['miscellaneous_expenditures'],
                $entry['drinking_water_charges'],
                $entry['new_student_registration'],
                $entry['nagarjuna_university_fee'],
                $entry['internet_bill'],
                $entry['uniform_cloth'],
                $entry['college_campaign_charges'],
            ]);

            PaymentRegister::create(array_merge($entry, [
                'tenant_id'       => $tenantId,
                'academic_year_id'=> $yearId,
                'recorded_by'     => $recordedBy,
                'total'           => $total,
            ]));
        }

        $this->command->info("  → {$label}: " . count($entries) . " entries inserted.");
    }

    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Build a realistic 4-month payment schedule.
     * Salary is paid on the 1st/2nd of each month.
     * Utility bills mid-month. Repairs/misc spread across days.
     */
    private function buildEntries(string $startDate): array
    {
        $base = \Carbon\Carbon::parse($startDate);
        $rows = [];

        // ── Month 1 ───────────────────────────────────────────────────────────
        $m = $base->copy();

        // 1st — Salary day
        $rows[] = $this->row($m->copy()->day(1), staffSalary: 185000, dieselCharges: 4200, drinkingWater: 800, internetBill: 1200);
        // 5th — Vehicle finance EMI
        $rows[] = $this->row($m->copy()->day(5), vehicleFinance: 28500, repayBorrowings: 15000, repayInterest: 2800);
        // 8th — Stationery purchase
        $rows[] = $this->row($m->copy()->day(8), stationary: 6500, contingentCharges: 3200);
        // 12th — University DD
        $rows[] = $this->row($m->copy()->day(12), universityDd: 45000, nagarjunaFee: 12000);
        // 15th — Utility bills
        $rows[] = $this->row($m->copy()->day(15), currentBill: 8400, drinkingWater: 600, internetBill: 1200);
        // 18th — Travel & misc
        $rows[] = $this->row($m->copy()->day(18), travellingCharges: 5200, miscExpenses: 3800, contingentCharges: 1500);
        // 22nd — Vehicle repairs
        $rows[] = $this->row($m->copy()->day(22), vehicleRepairs: 12500, dieselCharges: 3800);
        // 28th — Building maintenance
        $rows[] = $this->row($m->copy()->day(28), buildingRepairs: 35000, rentedVehicle: 8000);

        // ── Month 2 ───────────────────────────────────────────────────────────
        $m->addMonth();

        $rows[] = $this->row($m->copy()->day(1), staffSalary: 185000, dieselCharges: 4500, drinkingWater: 800, internetBill: 1200);
        $rows[] = $this->row($m->copy()->day(4), vehicleFinance: 28500, repayBorrowings: 15000, repayInterest: 2800);
        $rows[] = $this->row($m->copy()->day(7), stationary: 4200, collegeCampaign: 18000);
        $rows[] = $this->row($m->copy()->day(10), universityDd: 30000, nagarjunaFee: 8000);
        $rows[] = $this->row($m->copy()->day(14), currentBill: 9200, drinkingWater: 700);
        $rows[] = $this->row($m->copy()->day(16), newStudentReg: 22000, contingentCharges: 4500);
        $rows[] = $this->row($m->copy()->day(20), uniformCloth: 38500, miscExpenses: 5200);
        $rows[] = $this->row($m->copy()->day(25), vehicleRepairs: 8200, travellingCharges: 6800, rentedVehicle: 5000);
        $rows[] = $this->row($m->copy()->day(29), buildingRepairs: 42000, dieselCharges: 4100);

        // ── Month 3 ───────────────────────────────────────────────────────────
        $m->addMonth();

        $rows[] = $this->row($m->copy()->day(1), staffSalary: 185000, dieselCharges: 4600, drinkingWater: 900, internetBill: 1200);
        $rows[] = $this->row($m->copy()->day(3), vehicleFinance: 28500, repayBorrowings: 15000, repayInterest: 2800);
        $rows[] = $this->row($m->copy()->day(6), stationary: 3800, contingentCharges: 2100);
        $rows[] = $this->row($m->copy()->day(11), universityDd: 55000, nagarjunaFee: 15000);
        $rows[] = $this->row($m->copy()->day(13), currentBill: 7800, drinkingWater: 650);
        $rows[] = $this->row($m->copy()->day(17), travellingCharges: 9500, miscExpenses: 4200, collegeCampaign: 12000);
        $rows[] = $this->row($m->copy()->day(21), vehicleRepairs: 6500, rentedVehicle: 9000, dieselCharges: 3900);
        $rows[] = $this->row($m->copy()->day(24), newStudentReg: 18000, uniformCloth: 25000);
        $rows[] = $this->row($m->copy()->day(27), buildingRepairs: 28000, contingentCharges: 3500);

        // ── Month 4 ───────────────────────────────────────────────────────────
        $m->addMonth();

        $rows[] = $this->row($m->copy()->day(1), staffSalary: 188000, dieselCharges: 5200, drinkingWater: 900, internetBill: 1200);
        $rows[] = $this->row($m->copy()->day(3), vehicleFinance: 28500, repayBorrowings: 15000, repayInterest: 2800);
        $rows[] = $this->row($m->copy()->day(5), stationary: 5500, contingentCharges: 2800);
        $rows[] = $this->row($m->copy()->day(9), universityDd: 40000, nagarjunaFee: 10000);
        $rows[] = $this->row($m->copy()->day(12), currentBill: 10200, drinkingWater: 750);
        $rows[] = $this->row($m->copy()->day(15), travellingCharges: 7200, miscExpenses: 6500);
        $rows[] = $this->row($m->copy()->day(19), vehicleRepairs: 15000, rentedVehicle: 7500, dieselCharges: 4800);
        $rows[] = $this->row($m->copy()->day(22), newStudentReg: 32000, collegeCampaign: 25000);
        $rows[] = $this->row($m->copy()->day(26), buildingRepairs: 60000, uniformCloth: 18000);
        $rows[] = $this->row($m->copy()->day(30), miscExpenses: 8500, contingentCharges: 4200, stationary: 2200);

        return $rows;
    }

    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Build a single row array with named parameters.
     * Any column not passed defaults to 0.
     */
    private function row(
        \Carbon\Carbon $date,
        float $staffSalary        = 0,
        float $rentedVehicle      = 0,
        float $dieselCharges      = 0,
        float $currentBill        = 0,
        float $repayBorrowings    = 0,
        float $repayInterest      = 0,
        float $stationary         = 0,
        float $vehicleFinance     = 0,
        float $contingentCharges  = 0,
        float $vehicleRepairs     = 0,
        float $universityDd       = 0,
        float $travellingCharges  = 0,
        float $buildingRepairs    = 0,
        float $miscExpenses       = 0,
        float $drinkingWater      = 0,
        float $newStudentReg      = 0,
        float $nagarjunaFee       = 0,
        float $internetBill       = 0,
        float $uniformCloth       = 0,
        float $collegeCampaign    = 0,
        string $remarks           = ''
    ): array {
        return [
            'entry_date'                    => $date->toDateString(),
            'staff_salary'                  => $staffSalary,
            'rented_vehicle_charges'        => $rentedVehicle,
            'diesel_charges'                => $dieselCharges,
            'current_bill_charges'          => $currentBill,
            'repay_borrowings'              => $repayBorrowings,
            'repay_interest'                => $repayInterest,
            'stationary'                    => $stationary,
            'vehicle_finance'               => $vehicleFinance,
            'contingent_charges'            => $contingentCharges,
            'vehicle_repairs_expenditures'  => $vehicleRepairs,
            'university_dd'                 => $universityDd,
            'travelling_charges'            => $travellingCharges,
            'building_repairs_construction' => $buildingRepairs,
            'miscellaneous_expenditures'    => $miscExpenses,
            'drinking_water_charges'        => $drinkingWater,
            'new_student_registration'      => $newStudentReg,
            'nagarjuna_university_fee'      => $nagarjunaFee,
            'internet_bill'                 => $internetBill,
            'uniform_cloth'                 => $uniformCloth,
            'college_campaign_charges'      => $collegeCampaign,
            'remarks'                       => $remarks,
        ];
    }
}
