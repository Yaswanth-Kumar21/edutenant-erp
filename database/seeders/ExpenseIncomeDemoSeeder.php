<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Income;
use App\Models\IncomeCategory;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ExpenseIncomeDemoSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::where('slug', 'svc')->firstOrFail();
        $admin  = User::where('email', 'admin@svc.edu')->firstOrFail();

        // ── Expense Categories ────────────────────────────────────────────
        $cats = [
            'SAL'  => ['name' => 'Salaries',      'code' => 'SAL'],
            'UTIL' => ['name' => 'Utilities',      'code' => 'UTIL'],
            'MAIN' => ['name' => 'Maintenance',    'code' => 'MAIN'],
            'STAT' => ['name' => 'Stationery',     'code' => 'STAT'],
            'TRAN' => ['name' => 'Transport',      'code' => 'TRAN'],
            'MISC' => ['name' => 'Miscellaneous',  'code' => 'MISC'],
        ];

        $expCats = [];
        foreach ($cats as $key => $data) {
            $expCats[$key] = ExpenseCategory::firstOrCreate(
                ['tenant_id' => $tenant->id, 'code' => $data['code']],
                ['tenant_id' => $tenant->id, 'name' => $data['name'], 'code' => $data['code'], 'is_active' => true]
            );
        }

        // ── Income Categories ─────────────────────────────────────────────
        $incCatData = [
            'DON'  => ['name' => 'Donations', 'code' => 'DON'],
            'GRT'  => ['name' => 'Grants',    'code' => 'GRT'],
            'EVT'  => ['name' => 'Events',    'code' => 'EVT'],
            'CANT' => ['name' => 'Canteen',   'code' => 'CANT'],
        ];

        $incCats = [];
        foreach ($incCatData as $key => $data) {
            $incCats[$key] = IncomeCategory::firstOrCreate(
                ['tenant_id' => $tenant->id, 'code' => $data['code']],
                ['tenant_id' => $tenant->id, 'name' => $data['name'], 'code' => $data['code'], 'is_active' => true]
            );
        }

        // ── Expenses — 6 months ───────────────────────────────────────────
        $expenses = [
            ['cat'=>'SAL',  'amount'=>185000, 'title'=>'January Staff Salaries',       'months'=>5, 'day'=>1,  'mode'=>'online'],
            ['cat'=>'UTIL', 'amount'=>8400,   'title'=>'Electricity Bill January',     'months'=>5, 'day'=>15, 'mode'=>'online'],
            ['cat'=>'MAIN', 'amount'=>12500,  'title'=>'Lab Equipment Repair',         'months'=>5, 'day'=>20, 'mode'=>'cash'],
            ['cat'=>'STAT', 'amount'=>6500,   'title'=>'Office Supplies',              'months'=>5, 'day'=>8,  'mode'=>'cash'],
            ['cat'=>'SAL',  'amount'=>185000, 'title'=>'February Staff Salaries',      'months'=>4, 'day'=>1,  'mode'=>'online'],
            ['cat'=>'UTIL', 'amount'=>9200,   'title'=>'Electricity Bill February',    'months'=>4, 'day'=>14, 'mode'=>'online'],
            ['cat'=>'TRAN', 'amount'=>18000,  'title'=>'Bus Fuel & Maintenance Feb',   'months'=>4, 'day'=>10, 'mode'=>'cash'],
            ['cat'=>'MAIN', 'amount'=>35000,  'title'=>'Classroom Renovation',         'months'=>4, 'day'=>22, 'mode'=>'cheque'],
            ['cat'=>'SAL',  'amount'=>185000, 'title'=>'March Staff Salaries',         'months'=>3, 'day'=>1,  'mode'=>'online'],
            ['cat'=>'UTIL', 'amount'=>7800,   'title'=>'Electricity Bill March',       'months'=>3, 'day'=>15, 'mode'=>'online'],
            ['cat'=>'STAT', 'amount'=>4200,   'title'=>'Exam Answer Sheets',           'months'=>3, 'day'=>5,  'mode'=>'cash'],
            ['cat'=>'MISC', 'amount'=>8500,   'title'=>'Annual Day Function',          'months'=>3, 'day'=>25, 'mode'=>'cash'],
            ['cat'=>'SAL',  'amount'=>185000, 'title'=>'April Staff Salaries',         'months'=>2, 'day'=>1,  'mode'=>'online'],
            ['cat'=>'UTIL', 'amount'=>10200,  'title'=>'Electricity Bill April',       'months'=>2, 'day'=>12, 'mode'=>'online'],
            ['cat'=>'TRAN', 'amount'=>22000,  'title'=>'Bus Fuel April',               'months'=>2, 'day'=>8,  'mode'=>'cash'],
            ['cat'=>'MAIN', 'amount'=>28000,  'title'=>'Library Furniture',            'months'=>2, 'day'=>18, 'mode'=>'cheque'],
            ['cat'=>'SAL',  'amount'=>185000, 'title'=>'May Staff Salaries',           'months'=>1, 'day'=>1,  'mode'=>'online'],
            ['cat'=>'UTIL', 'amount'=>11500,  'title'=>'Electricity Bill May',         'months'=>1, 'day'=>14, 'mode'=>'online'],
            ['cat'=>'MISC', 'amount'=>15000,  'title'=>'Sports Day Expenses',          'months'=>1, 'day'=>20, 'mode'=>'cash'],
            ['cat'=>'TRAN', 'amount'=>19500,  'title'=>'Bus Fuel May',                 'months'=>1, 'day'=>10, 'mode'=>'cash'],
            ['cat'=>'SAL',  'amount'=>188000, 'title'=>'June Staff Salaries',          'months'=>0, 'day'=>1,  'mode'=>'online'],
            ['cat'=>'UTIL', 'amount'=>9800,   'title'=>'Electricity Bill June',        'months'=>0, 'day'=>10, 'mode'=>'online'],
            ['cat'=>'STAT', 'amount'=>5500,   'title'=>'New Year Stationery',          'months'=>0, 'day'=>5,  'mode'=>'cash'],
        ];

        foreach ($expenses as $e) {
            $date = Carbon::now()->subMonths($e['months'])->startOfMonth()->addDays($e['day'] - 1);
            Expense::create([
                'tenant_id'           => $tenant->id,
                'expense_category_id' => $expCats[$e['cat']]->id,
                'recorded_by'         => $admin->id,
                'title'               => $e['title'],
                'description'         => $e['title'],
                'amount'              => $e['amount'],
                'expense_date'        => $date->toDateString(),
                'payment_mode'        => $e['mode'],
            ]);
        }

        // ── Income — 6 months ─────────────────────────────────────────────
        $incomes = [
            ['cat'=>'DON',  'amount'=>50000,  'title'=>'Alumni Association Donation',    'months'=>5, 'day'=>10],
            ['cat'=>'CANT', 'amount'=>8000,   'title'=>'Canteen Lease January',          'months'=>5, 'day'=>5],
            ['cat'=>'GRT',  'amount'=>125000, 'title'=>'State Govt Infrastructure Grant','months'=>4, 'day'=>15],
            ['cat'=>'CANT', 'amount'=>8000,   'title'=>'Canteen Lease February',         'months'=>4, 'day'=>5],
            ['cat'=>'EVT',  'amount'=>35000,  'title'=>'Annual Day Ticket Sales',        'months'=>3, 'day'=>28],
            ['cat'=>'CANT', 'amount'=>8000,   'title'=>'Canteen Lease March',            'months'=>3, 'day'=>5],
            ['cat'=>'DON',  'amount'=>25000,  'title'=>'Management Donation April',      'months'=>2, 'day'=>12],
            ['cat'=>'CANT', 'amount'=>8000,   'title'=>'Canteen Lease April',            'months'=>2, 'day'=>5],
            ['cat'=>'EVT',  'amount'=>18000,  'title'=>'Sports Day Registration',        'months'=>1, 'day'=>22],
            ['cat'=>'CANT', 'amount'=>8000,   'title'=>'Canteen Lease May',              'months'=>1, 'day'=>5],
            ['cat'=>'GRT',  'amount'=>75000,  'title'=>'AICTE Development Grant',        'months'=>0, 'day'=>8],
            ['cat'=>'CANT', 'amount'=>8000,   'title'=>'Canteen Lease June',             'months'=>0, 'day'=>5],
        ];

        foreach ($incomes as $i) {
            $date = Carbon::now()->subMonths($i['months'])->startOfMonth()->addDays($i['day'] - 1);
            Income::create([
                'tenant_id'          => $tenant->id,
                'income_category_id' => $incCats[$i['cat']]->id,
                'recorded_by'        => $admin->id,
                'title'              => $i['title'],
                'description'        => $i['title'],
                'amount'             => $i['amount'],
                'income_date'        => $date->toDateString(),
                'payment_mode'       => 'online',
            ]);
        }

        $totalExp = array_sum(array_column($expenses, 'amount'));
        $totalInc = array_sum(array_column($incomes, 'amount'));

        $this->command->info('✅ Expenses & Income Demo Data Seeded!');
        $this->command->info('   Expenses : ' . count($expenses) . ' records — ₹' . number_format($totalExp));
        $this->command->info('   Income   : ' . count($incomes)  . ' records — ₹' . number_format($totalInc));
    }
}
