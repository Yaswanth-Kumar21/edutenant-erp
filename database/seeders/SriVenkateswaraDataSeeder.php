<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Branch;
use App\Models\FeePayment;
use App\Models\FeeType;
use App\Models\Role;
use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Models\StaffRole;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SriVenkateswaraDataSeeder extends Seeder
{
    public function run(): void
    {
        $tenant       = Tenant::where('slug', 'demo-college')->firstOrFail();
        $academicYear = AcademicYear::where('tenant_id', $tenant->id)->where('is_current', true)->first();
        $adminUser    = User::where('email', 'admin@svdcollege.edu')->first();

        $mpc   = Branch::where('tenant_id', $tenant->id)->where('name', 'MPC')->first();
        $bipc  = Branch::where('tenant_id', $tenant->id)->where('name', 'BiPC')->first();
        $hep   = Branch::where('tenant_id', $tenant->id)->where('name', 'HEP')->first();
        $bcomG = Branch::where('tenant_id', $tenant->id)->whereIn('name', ['BCom General', 'BCom', 'BCOM'])->first();
        $mpcs  = Branch::where('tenant_id', $tenant->id)->whereIn('name', ['MPCs', 'MPC'])->first() ?? $mpc;

        // Fallback — if any branch is null, use mpc
        if (!$mpc)   { $this->command->error('MPC branch not found for tenant ' . $tenant->id); return; }
        if (!$bipc)  $bipc  = $mpc;
        if (!$hep)   $hep   = $mpc;
        if (!$bcomG) $bcomG = $mpc;

        $uniformFee = FeeType::where('tenant_id', $tenant->id)->where('code', 'UNIFORM')->first();
        $examFee    = FeeType::where('tenant_id', $tenant->id)->where('code', 'EXAM')->first();
        $udfFee     = FeeType::where('tenant_id', $tenant->id)->where('code', 'UDF')->first();
        $recordFee  = FeeType::where('tenant_id', $tenant->id)->where('code', 'RECORD')->first();

        // ── Add more Teaching Staff ───────────────────────────────────────────
        $profRole = StaffRole::where('tenant_id', $tenant->id)->where('name', 'Professor')->first()
            ?? StaffRole::create(['tenant_id' => $tenant->id, 'name' => 'Professor', 'department' => 'Academic', 'staff_type' => 'teaching', 'is_active' => true]);

        $lecRole = StaffRole::where('tenant_id', $tenant->id)->where('name', 'Lecturer')->first()
            ?? StaffRole::create(['tenant_id' => $tenant->id, 'name' => 'Lecturer', 'department' => 'Academic', 'staff_type' => 'teaching', 'is_active' => true]);

        $staffData = [
            ['name' => 'Dr. Venkata Rao',    'subject' => 'Mathematics',  'designation' => 'Professor',          'salary' => 48000, 'type' => 'teaching',     'gender' => 'male'],
            ['name' => 'Mrs. Padmavathi',     'subject' => 'Physics',      'designation' => 'Associate Professor','salary' => 40000, 'type' => 'teaching',     'gender' => 'female'],
            ['name' => 'Mr. Srinivas Murthy', 'subject' => 'Chemistry',    'designation' => 'Lecturer',          'salary' => 33000, 'type' => 'teaching',     'gender' => 'male'],
            ['name' => 'Ms. Radha Krishna',   'subject' => 'Biology',      'designation' => 'Lecturer',          'salary' => 31000, 'type' => 'teaching',     'gender' => 'female'],
            ['name' => 'Mr. Narayana Swamy',  'subject' => 'English',      'designation' => 'Lecturer',          'salary' => 29000, 'type' => 'teaching',     'gender' => 'male'],
            ['name' => 'Mrs. Sarada Devi',    'subject' => 'Telugu',       'designation' => 'Lecturer',          'salary' => 27000, 'type' => 'teaching',     'gender' => 'female'],
            ['name' => 'Mr. Ranga Rao',       'subject' => null,           'designation' => 'Office Clerk',      'salary' => 18000, 'type' => 'non_teaching', 'gender' => 'male'],
            ['name' => 'Mrs. Kamala Devi',    'subject' => null,           'designation' => 'Librarian',         'salary' => 20000, 'type' => 'non_teaching', 'gender' => 'female'],
            ['name' => 'Mr. Govinda Rao',     'subject' => null,           'designation' => 'Peon',              'salary' => 12000, 'type' => 'non_teaching', 'gender' => 'male'],
        ];

        $staffModels = [];
        foreach ($staffData as $i => $s) {
            $staff = Staff::updateOrCreate(
                ['tenant_id' => $tenant->id, 'name' => $s['name']],
                [
                    'tenant_id'                  => $tenant->id,
                    'staff_role_id'              => $profRole->id,
                    'staff_code'                 => 'SVD-' . str_pad($i + 2, 4, '0', STR_PAD_LEFT),
                    'name'                       => $s['name'],
                    'staff_type'                 => $s['type'],
                    'designation'                => $s['designation'],
                    'subject'                    => $s['subject'],
                    'email'                      => strtolower(str_replace([' ', '.'], ['', ''], $s['name'])) . '@svdcollege.edu',
                    'phone'                      => '97000' . str_pad($i + 10001, 5, '0', STR_PAD_LEFT),
                    'gender'                     => $s['gender'],
                    'date_of_joining'            => Carbon::now()->subYears(rand(1, 10))->toDateString(),
                    'monthly_salary'             => $s['salary'],
                    'basic_salary'               => $s['salary'] * 0.6,
                    'hra'                        => $s['salary'] * 0.2,
                    'da'                         => $s['salary'] * 0.1,
                    'other_allowances'           => $s['salary'] * 0.1,
                    'pf_deduction'               => $s['salary'] * 0.12,
                    'allowed_holidays_per_month' => 2,
                    'salary_calculation_days'    => 30,
                    'status'                     => 'active',
                ]
            );
            $staffModels[] = $staff;
        }

        // ── Add Students ──────────────────────────────────────────────────────
        $students = [
            ['first_name' => 'Ravi',       'last_name' => 'Teja',      'branch' => $mpc,   'gender' => 'male',   'category' => 'GEN', 'marks_10th' => 88.0, 'marks_12th' => 82.0, 'phone' => '8100000001', 'vehicle' => false],
            ['first_name' => 'Sravani',    'last_name' => 'Reddy',     'branch' => $mpc,   'gender' => 'female', 'category' => 'OBC', 'marks_10th' => 91.5, 'marks_12th' => 87.0, 'phone' => '8100000002', 'vehicle' => true],
            ['first_name' => 'Mahesh',     'last_name' => 'Babu',      'branch' => $mpc,   'gender' => 'male',   'category' => 'SC',  'marks_10th' => 75.0, 'marks_12th' => 70.0, 'phone' => '8100000003', 'vehicle' => false],
            ['first_name' => 'Pooja',      'last_name' => 'Sharma',    'branch' => $bipc,  'gender' => 'female', 'category' => 'GEN', 'marks_10th' => 93.0, 'marks_12th' => 89.0, 'phone' => '8100000004', 'vehicle' => true],
            ['first_name' => 'Aakash',     'last_name' => 'Kumar',     'branch' => $bipc,  'gender' => 'male',   'category' => 'OBC', 'marks_10th' => 80.0, 'marks_12th' => 76.0, 'phone' => '8100000005', 'vehicle' => false],
            ['first_name' => 'Meghana',    'last_name' => 'Rao',       'branch' => $bipc,  'gender' => 'female', 'category' => 'ST',  'marks_10th' => 72.0, 'marks_12th' => 68.0, 'phone' => '8100000006', 'vehicle' => false],
            ['first_name' => 'Charan',     'last_name' => 'Tej',       'branch' => $hep,   'gender' => 'male',   'category' => 'GEN', 'marks_10th' => 78.0, 'marks_12th' => 74.0, 'phone' => '8100000007', 'vehicle' => true],
            ['first_name' => 'Bhavana',    'last_name' => 'Nair',      'branch' => $hep,   'gender' => 'female', 'category' => 'EWS', 'marks_10th' => 84.0, 'marks_12th' => 80.0, 'phone' => '8100000008', 'vehicle' => false],
            ['first_name' => 'Naveen',     'last_name' => 'Chandra',   'branch' => $hep,   'gender' => 'male',   'category' => 'OBC', 'marks_10th' => 77.0, 'marks_12th' => 73.0, 'phone' => '8100000009', 'vehicle' => false],
            ['first_name' => 'Keerthi',    'last_name' => 'Suresh',    'branch' => $bcomG, 'gender' => 'female', 'category' => 'GEN', 'marks_10th' => 86.0, 'marks_12th' => 83.0, 'phone' => '8100000010', 'vehicle' => true],
            ['first_name' => 'Tarun',      'last_name' => 'Varma',     'branch' => $bcomG, 'gender' => 'male',   'category' => 'SC',  'marks_10th' => 71.0, 'marks_12th' => 67.0, 'phone' => '8100000011', 'vehicle' => false],
            ['first_name' => 'Haritha',    'last_name' => 'Menon',     'branch' => $bcomG, 'gender' => 'female', 'category' => 'GEN', 'marks_10th' => 89.0, 'marks_12th' => 85.0, 'phone' => '8100000012', 'vehicle' => false],
            ['first_name' => 'Sai',        'last_name' => 'Kiran',     'branch' => $mpc,   'gender' => 'male',   'category' => 'GEN', 'marks_10th' => 94.0, 'marks_12th' => 91.0, 'phone' => '8100000013', 'vehicle' => true],
            ['first_name' => 'Nandini',    'last_name' => 'Prasad',    'branch' => $bipc,  'gender' => 'female', 'category' => 'OBC', 'marks_10th' => 83.0, 'marks_12th' => 79.0, 'phone' => '8100000014', 'vehicle' => false],
            ['first_name' => 'Rohith',     'last_name' => 'Goud',      'branch' => $hep,   'gender' => 'male',   'category' => 'ST',  'marks_10th' => 69.0, 'marks_12th' => 65.0, 'phone' => '8100000015', 'vehicle' => false],
        ];

        $year = now()->year;
        $studentModels = [];

        foreach ($students as $i => $s) {
            $admNum = 'EDU-' . $year . '-' . str_pad($i + 2, 4, '0', STR_PAD_LEFT);

            // Skip if admission number already exists
            if (Student::where('admission_number', $admNum)->exists()) {
                $admNum = 'EDU-' . $year . '-SVD-' . str_pad($i + 2, 4, '0', STR_PAD_LEFT);
            }

            $student = Student::updateOrCreate(
                ['tenant_id' => $tenant->id, 'phone' => $s['phone']],
                [
                    'tenant_id'             => $tenant->id,
                    'branch_id'             => $s['branch']->id,
                    'academic_year_id'      => $academicYear->id,
                    'admission_number'      => $admNum,
                    'admission_date'        => Carbon::now()->subMonths(rand(1, 8))->toDateString(),
                    'first_name'            => $s['first_name'],
                    'last_name'             => $s['last_name'],
                    'father_name'           => 'Mr. ' . $s['last_name'],
                    'mother_name'           => 'Mrs. ' . $s['last_name'],
                    'date_of_birth'         => Carbon::now()->subYears(rand(18, 22))->toDateString(),
                    'gender'                => $s['gender'],
                    'phone'                 => $s['phone'],
                    'email'                 => strtolower($s['first_name']) . '.' . strtolower($s['last_name']) . '@svdstudent.edu',
                    'address'               => rand(10, 99) . ' Gandhi Nagar, Tirupati',
                    'city'                  => 'Tirupati',
                    'state'                 => 'Andhra Pradesh',
                    'pincode'               => '517501',
                    'marks_10th'            => $s['marks_10th'],
                    'marks_12th'            => $s['marks_12th'],
                    'current_semester'      => rand(1, 3),
                    'current_year'          => 1,
                    'category'              => $s['category'],
                    'scholarship_eligible'  => in_array($s['category'], ['SC', 'ST', 'EWS']),
                    'vehicle_opted'         => $s['vehicle'],
                    'vehicle_start_date'    => $s['vehicle'] ? Carbon::now()->subMonths(rand(1, 6))->toDateString() : null,
                    'status'                => 'active',
                    'admission_step'        => 4,
                    'certificates_submitted'=> [],
                ]
            );

            $studentModels[] = $student;

            // ── Fee Payments ──────────────────────────────────────────────────
            $rcpBase = 'SVD-RCP-' . $year . '-' . str_pad($i + 50, 5, '0', STR_PAD_LEFT);

            // Uniform fee — all paid
            FeePayment::updateOrCreate(
                ['tenant_id' => $tenant->id, 'student_id' => $student->id, 'fee_type_id' => $uniformFee->id],
                [
                    'tenant_id'        => $tenant->id,
                    'student_id'       => $student->id,
                    'fee_type_id'      => $uniformFee->id,
                    'academic_year_id' => $academicYear->id,
                    'collected_by'     => $adminUser->id,
                    'receipt_number'   => $rcpBase . 'A',
                    'amount_due'       => 1500,
                    'amount_paid'      => 1500,
                    'discount'         => 0,
                    'fine'             => 0,
                    'payment_mode'     => ['cash', 'upi', 'card'][rand(0, 2)],
                    'payment_date'     => Carbon::now()->subDays(rand(5, 90))->toDateString(),
                    'status'           => 'paid',
                    'is_exempted'      => false,
                ]
            );

            // Exam fee — 80% paid, 20% pending
            $examPaid = (rand(1, 10) > 2);
            FeePayment::updateOrCreate(
                ['tenant_id' => $tenant->id, 'student_id' => $student->id, 'fee_type_id' => $examFee->id],
                [
                    'tenant_id'        => $tenant->id,
                    'student_id'       => $student->id,
                    'fee_type_id'      => $examFee->id,
                    'academic_year_id' => $academicYear->id,
                    'collected_by'     => $examPaid ? $adminUser->id : null,
                    'receipt_number'   => $rcpBase . 'B',
                    'amount_due'       => 800,
                    'amount_paid'      => $examPaid ? 800 : 0,
                    'discount'         => in_array($s['category'], ['SC', 'ST']) ? 200 : 0,
                    'fine'             => 0,
                    'semester'         => 1,
                    'payment_mode'     => 'cash',
                    'payment_date'     => Carbon::now()->subDays(rand(1, 45))->toDateString(),
                    'status'           => $examPaid ? 'paid' : 'pending',
                    'is_exempted'      => false,
                ]
            );

            // UDF fee — all paid
            FeePayment::updateOrCreate(
                ['tenant_id' => $tenant->id, 'student_id' => $student->id, 'fee_type_id' => $udfFee->id],
                [
                    'tenant_id'        => $tenant->id,
                    'student_id'       => $student->id,
                    'fee_type_id'      => $udfFee->id,
                    'academic_year_id' => $academicYear->id,
                    'collected_by'     => $adminUser->id,
                    'receipt_number'   => $rcpBase . 'C',
                    'amount_due'       => 500,
                    'amount_paid'      => 500,
                    'discount'         => 0,
                    'fine'             => 0,
                    'payment_mode'     => 'upi',
                    'payment_date'     => Carbon::now()->subDays(rand(10, 60))->toDateString(),
                    'status'           => 'paid',
                    'is_exempted'      => false,
                ]
            );

            // Record fee — only for B.Sc branches
            if ($s['branch']->has_record_fee && $recordFee) {
                FeePayment::updateOrCreate(
                    ['tenant_id' => $tenant->id, 'student_id' => $student->id, 'fee_type_id' => $recordFee->id],
                    [
                        'tenant_id'        => $tenant->id,
                        'student_id'       => $student->id,
                        'fee_type_id'      => $recordFee->id,
                        'academic_year_id' => $academicYear->id,
                        'collected_by'     => $adminUser->id,
                        'receipt_number'   => $rcpBase . 'D',
                        'amount_due'       => 300,
                        'amount_paid'      => 300,
                        'discount'         => 0,
                        'fine'             => 0,
                        'semester'         => 1,
                        'payment_mode'     => 'cash',
                        'payment_date'     => Carbon::now()->subDays(rand(5, 30))->toDateString(),
                        'status'           => 'paid',
                        'is_exempted'      => false,
                    ]
                );
            }
        }

        // ── Student Attendance (last 20 working days) ─────────────────────────
        foreach ($studentModels as $student) {
            for ($day = 20; $day >= 1; $day--) {
                $date = Carbon::now()->subDays($day)->toDateString();
                if (Carbon::parse($date)->isSunday()) continue;

                $status = (rand(1, 10) > 2) ? 'present' : 'absent';

                StudentAttendance::updateOrCreate(
                    ['student_id' => $student->id, 'attendance_date' => $date, 'subject' => 'General'],
                    [
                        'tenant_id'  => $tenant->id,
                        'branch_id'  => $student->branch_id,
                        'marked_by'  => $adminUser->id,
                        'semester'   => $student->current_semester,
                        'status'     => $status,
                    ]
                );
            }
        }

        // ── Staff Attendance (last 20 working days) ───────────────────────────
        $allStaff = Staff::where('tenant_id', $tenant->id)->get();
        foreach ($allStaff as $staff) {
            for ($day = 20; $day >= 1; $day--) {
                $date = Carbon::now()->subDays($day)->toDateString();
                if (Carbon::parse($date)->isSunday()) continue;

                $status = (rand(1, 10) > 1) ? 'present' : 'absent';

                StaffAttendance::updateOrCreate(
                    ['staff_id' => $staff->id, 'attendance_date' => $date],
                    [
                        'tenant_id' => $tenant->id,
                        'marked_by' => $adminUser->id,
                        'status'    => $status,
                    ]
                );
            }
        }

        $this->command->info('✅ Sri Venkateswara College data seeded:');
        $this->command->info('   Students: ' . count($studentModels));
        $this->command->info('   Staff: ' . count($staffData));
    }
}
