<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Branch;
use App\Models\Course;
use App\Models\FeePayment;
use App\Models\FeeType;
use App\Models\Role;
use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Models\StaffRole;
use App\Models\Stream;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $collegeAdminRole = Role::where('name', 'college_admin')->first();
        $staffRole        = Role::where('name', 'staff')->first();
        $teacherRole      = Role::where('name', 'teacher')->first();
        $studentRole      = Role::where('name', 'student')->first();

        // ── College 2: Nagarjuna Degree College ──────────────────────────────
        $this->seedCollege(
            [
                'name'             => 'Nagarjuna Degree College',
                'slug'             => 'nagarjuna-college',
                'email'            => 'admin@nagarjuna.edu',
                'phone'            => '9876500001',
                'address'          => '45 University Road, Guntur',
                'city'             => 'Guntur',
                'state'            => 'Andhra Pradesh',
                'pincode'          => '522001',
                'principal_name'   => 'Dr. Suresh Babu',
                'affiliation_number' => 'AU/2024/002',
            ],
            $collegeAdminRole, $staffRole, $teacherRole, $studentRole,
            'admin@nagarjuna.edu'
        );

        // ── College 3: Krishna Degree College ────────────────────────────────
        $this->seedCollege(
            [
                'name'             => 'Krishna Degree College',
                'slug'             => 'krishna-college',
                'email'            => 'admin@krishna.edu',
                'phone'            => '9876500002',
                'address'          => '78 College Street, Vijayawada',
                'city'             => 'Vijayawada',
                'state'            => 'Andhra Pradesh',
                'pincode'          => '520001',
                'principal_name'   => 'Dr. Lakshmi Devi',
                'affiliation_number' => 'AU/2024/003',
            ],
            $collegeAdminRole, $staffRole, $teacherRole, $studentRole,
            'admin@krishna.edu'
        );

        $this->command->info('✅ Dummy data seeded for 2 additional colleges.');
        $this->command->info('   Nagarjuna College → admin@nagarjuna.edu / password');
        $this->command->info('   Krishna College   → admin@krishna.edu / password');
    }

    private function seedCollege(
        array $collegeData,
        $collegeAdminRole, $staffRole, $teacherRole, $studentRole,
        string $adminEmail
    ): void {
        // ── Create Tenant ─────────────────────────────────────────────────────
        $tenant = Tenant::updateOrCreate(
            ['slug' => $collegeData['slug']],
            array_merge($collegeData, [
                'status'             => 'active',
                'subscription_start' => now(),
                'subscription_end'   => now()->addYear(),
            ])
        );

        // ── Academic Year ─────────────────────────────────────────────────────
        $academicYear = AcademicYear::updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => '2024-2025'],
            [
                'tenant_id'  => $tenant->id,
                'name'       => '2024-2025',
                'start_date' => '2024-06-01',
                'end_date'   => '2025-05-31',
                'is_current' => true,
            ]
        );

        // ── Streams ───────────────────────────────────────────────────────────
        $science = Stream::updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'Science'],
            ['tenant_id' => $tenant->id, 'name' => 'Science', 'code' => 'SCI', 'is_active' => true]
        );
        $arts = Stream::updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'Arts'],
            ['tenant_id' => $tenant->id, 'name' => 'Arts', 'code' => 'ARTS', 'is_active' => true]
        );

        // ── Courses ───────────────────────────────────────────────────────────
        $bsc = Course::updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'B.Sc'],
            ['tenant_id' => $tenant->id, 'stream_id' => $science->id, 'name' => 'B.Sc', 'code' => 'BSC', 'duration_years' => 3, 'total_semesters' => 6, 'has_record_fee' => true, 'is_active' => true]
        );
        $ba = Course::updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'BA'],
            ['tenant_id' => $tenant->id, 'stream_id' => $arts->id, 'name' => 'BA', 'code' => 'BA', 'duration_years' => 3, 'total_semesters' => 6, 'has_record_fee' => false, 'is_active' => true]
        );
        $bcom = Course::updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'BCom'],
            ['tenant_id' => $tenant->id, 'stream_id' => $arts->id, 'name' => 'BCom', 'code' => 'BCOM', 'duration_years' => 3, 'total_semesters' => 6, 'has_record_fee' => false, 'is_active' => true]
        );

        // ── Branches ──────────────────────────────────────────────────────────
        $mpc = Branch::updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'MPC'],
            ['tenant_id' => $tenant->id, 'course_id' => $bsc->id, 'name' => 'MPC', 'code' => 'MPC', 'intake_capacity' => 60, 'tuition_fee_student' => 8000, 'tuition_fee_govt' => 15000, 'has_record_fee' => true, 'is_active' => true]
        );
        $bipc = Branch::updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'BiPC'],
            ['tenant_id' => $tenant->id, 'course_id' => $bsc->id, 'name' => 'BiPC', 'code' => 'BIPC', 'intake_capacity' => 60, 'tuition_fee_student' => 9000, 'tuition_fee_govt' => 15000, 'has_record_fee' => true, 'is_active' => true]
        );
        $hep = Branch::updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'HEP'],
            ['tenant_id' => $tenant->id, 'course_id' => $ba->id, 'name' => 'HEP', 'code' => 'HEP', 'intake_capacity' => 60, 'tuition_fee_student' => 5000, 'tuition_fee_govt' => 15000, 'has_record_fee' => false, 'is_active' => true]
        );
        $bcomG = Branch::updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'BCom General'],
            ['tenant_id' => $tenant->id, 'course_id' => $bcom->id, 'name' => 'BCom General', 'code' => 'BCOMG', 'intake_capacity' => 60, 'tuition_fee_student' => 6000, 'tuition_fee_govt' => 15000, 'has_record_fee' => false, 'is_active' => true]
        );

        // ── Fee Types ─────────────────────────────────────────────────────────
        $feeTypes = [
            ['code' => 'UNIFORM',    'name' => 'Uniform Fee',    'frequency' => 'one_time',    'amount' => 1500],
            ['code' => 'EXAM',       'name' => 'Exam Fee',       'frequency' => 'per_semester','amount' => 800],
            ['code' => 'UDF',        'name' => 'UDF Fee',        'frequency' => 'per_year',    'amount' => 500],
            ['code' => 'RECORD',     'name' => 'Record Fee',     'frequency' => 'per_semester','amount' => 300],
            ['code' => 'VEHICLE',    'name' => 'Vehicle Fee',    'frequency' => 'monthly',     'amount' => 600],
            ['code' => 'TUITION',    'name' => 'Tuition Fee',    'frequency' => 'per_year',    'amount' => 0],
            ['code' => 'OTHER',      'name' => 'Other Fee',      'frequency' => 'one_time',    'amount' => 0],
            ['code' => 'INTERNSHIP', 'name' => 'Internship Fee', 'frequency' => 'one_time',    'amount' => 1000],
        ];
        $feeTypeModels = [];
        foreach ($feeTypes as $ft) {
            $feeTypeModels[$ft['code']] = FeeType::updateOrCreate(
                ['tenant_id' => $tenant->id, 'code' => $ft['code']],
                array_merge($ft, [
                    'tenant_id'               => $tenant->id,
                    'is_active'               => true,
                    'applicable_all_streams'  => true,
                    'applicable_all_branches' => true,
                    'can_be_exempted'         => true,
                    'sort_order'              => 1,
                ])
            );
        }

        // ── Staff Roles ───────────────────────────────────────────────────────
        $profRole = StaffRole::updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'Professor'],
            ['tenant_id' => $tenant->id, 'name' => 'Professor', 'department' => 'Academic', 'staff_type' => 'teaching', 'is_active' => true]
        );
        $officeRole = StaffRole::updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'Office Staff'],
            ['tenant_id' => $tenant->id, 'name' => 'Office Staff', 'department' => 'Administration', 'staff_type' => 'non_teaching', 'is_active' => true]
        );

        // ── Users ─────────────────────────────────────────────────────────────
        $adminUser = User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'name'      => 'College Admin',
                'email'     => $adminEmail,
                'password'  => Hash::make('password'),
                'tenant_id' => $tenant->id,
                'role_id'   => $collegeAdminRole?->id,
                'status'    => 'active',
            ]
        );

        // ── Teaching Staff ────────────────────────────────────────────────────
        $teachingStaff = [
            ['name' => 'Dr. Ravi Kumar',    'subject' => 'Mathematics',  'designation' => 'Professor',         'salary' => 45000, 'gender' => 'male'],
            ['name' => 'Mrs. Priya Sharma', 'subject' => 'Physics',      'designation' => 'Associate Professor','salary' => 38000, 'gender' => 'female'],
            ['name' => 'Mr. Anil Reddy',    'subject' => 'Chemistry',    'designation' => 'Lecturer',          'salary' => 32000, 'gender' => 'male'],
            ['name' => 'Ms. Kavitha Rao',   'subject' => 'Biology',      'designation' => 'Lecturer',          'salary' => 30000, 'gender' => 'female'],
            ['name' => 'Mr. Suresh Naidu',  'subject' => 'English',      'designation' => 'Lecturer',          'salary' => 28000, 'gender' => 'male'],
        ];

        $staffModels = [];
        foreach ($teachingStaff as $i => $s) {
            $staffModels[] = Staff::updateOrCreate(
                ['tenant_id' => $tenant->id, 'name' => $s['name']],
                [
                    'tenant_id'                  => $tenant->id,
                    'staff_role_id'              => $profRole->id,
                    'staff_code'                 => 'STF-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                    'name'                       => $s['name'],
                    'staff_type'                 => 'teaching',
                    'designation'                => $s['designation'],
                    'subject'                    => $s['subject'],
                    'email'                      => strtolower(str_replace([' ', '.'], ['', ''], $s['name'])) . '@' . $tenant->slug . '.edu',
                    'phone'                      => '98765' . str_pad($i + 10000, 5, '0', STR_PAD_LEFT),
                    'gender'                     => $s['gender'],
                    'date_of_joining'            => Carbon::now()->subYears(rand(1, 8))->toDateString(),
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
        }

        // ── Non-Teaching Staff ────────────────────────────────────────────────
        $nonTeachingStaff = [
            ['name' => 'Mr. Ramesh Clerk',  'designation' => 'Office Clerk',  'salary' => 18000, 'gender' => 'male'],
            ['name' => 'Mrs. Sunita Peon',  'designation' => 'Peon',          'salary' => 12000, 'gender' => 'female'],
        ];

        foreach ($nonTeachingStaff as $i => $s) {
            $staffModels[] = Staff::updateOrCreate(
                ['tenant_id' => $tenant->id, 'name' => $s['name']],
                [
                    'tenant_id'                  => $tenant->id,
                    'staff_role_id'              => $officeRole->id,
                    'staff_code'                 => 'STF-' . str_pad($i + 10, 4, '0', STR_PAD_LEFT),
                    'name'                       => $s['name'],
                    'staff_type'                 => 'non_teaching',
                    'designation'                => $s['designation'],
                    'gender'                     => $s['gender'],
                    'date_of_joining'            => Carbon::now()->subYears(rand(1, 5))->toDateString(),
                    'monthly_salary'             => $s['salary'],
                    'basic_salary'               => $s['salary'] * 0.6,
                    'allowed_holidays_per_month' => 2,
                    'salary_calculation_days'    => 30,
                    'status'                     => 'active',
                ]
            );
        }

        // ── Students ──────────────────────────────────────────────────────────
        $studentData = [
            ['first_name' => 'Arjun',    'last_name' => 'Sharma',   'branch' => $mpc,   'gender' => 'male',   'category' => 'GEN', 'marks_10th' => 85.5, 'marks_12th' => 78.0, 'phone' => '9000000001'],
            ['first_name' => 'Priya',    'last_name' => 'Reddy',    'branch' => $mpc,   'gender' => 'female', 'category' => 'OBC', 'marks_10th' => 92.0, 'marks_12th' => 88.5, 'phone' => '9000000002'],
            ['first_name' => 'Kiran',    'last_name' => 'Kumar',    'branch' => $bipc,  'gender' => 'male',   'category' => 'SC',  'marks_10th' => 76.0, 'marks_12th' => 72.0, 'phone' => '9000000003'],
            ['first_name' => 'Sneha',    'last_name' => 'Patel',    'branch' => $bipc,  'gender' => 'female', 'category' => 'GEN', 'marks_10th' => 88.0, 'marks_12th' => 84.0, 'phone' => '9000000004'],
            ['first_name' => 'Rahul',    'last_name' => 'Verma',    'branch' => $hep,   'gender' => 'male',   'category' => 'ST',  'marks_10th' => 70.0, 'marks_12th' => 68.0, 'phone' => '9000000005'],
            ['first_name' => 'Ananya',   'last_name' => 'Singh',    'branch' => $hep,   'gender' => 'female', 'category' => 'OBC', 'marks_10th' => 82.0, 'marks_12th' => 79.0, 'phone' => '9000000006'],
            ['first_name' => 'Vikram',   'last_name' => 'Naidu',    'branch' => $bcomG, 'gender' => 'male',   'category' => 'GEN', 'marks_10th' => 79.0, 'marks_12th' => 75.0, 'phone' => '9000000007'],
            ['first_name' => 'Divya',    'last_name' => 'Rao',      'branch' => $bcomG, 'gender' => 'female', 'category' => 'EWS', 'marks_10th' => 86.0, 'marks_12th' => 82.0, 'phone' => '9000000008'],
            ['first_name' => 'Suresh',   'last_name' => 'Babu',     'branch' => $mpc,   'gender' => 'male',   'category' => 'GEN', 'marks_10th' => 91.0, 'marks_12th' => 87.0, 'phone' => '9000000009'],
            ['first_name' => 'Lakshmi',  'last_name' => 'Devi',     'branch' => $bipc,  'gender' => 'female', 'category' => 'SC',  'marks_10th' => 74.0, 'marks_12th' => 71.0, 'phone' => '9000000010'],
        ];

        $year = now()->year;
        $tenantPrefix = 'T' . $tenant->id;
        $studentModels = [];

        foreach ($studentData as $i => $s) {
            $admNum = 'EDU-' . $year . '-' . $tenantPrefix . str_pad($i + 100, 4, '0', STR_PAD_LEFT);

            $student = Student::updateOrCreate(
                ['tenant_id' => $tenant->id, 'admission_number' => $admNum],
                [
                    'tenant_id'             => $tenant->id,
                    'branch_id'             => $s['branch']->id,
                    'academic_year_id'      => $academicYear->id,
                    'admission_number'      => $admNum,
                    'admission_date'        => Carbon::now()->subMonths(rand(1, 10))->toDateString(),
                    'first_name'            => $s['first_name'],
                    'last_name'             => $s['last_name'],
                    'father_name'           => 'Mr. ' . $s['last_name'] . ' Sr.',
                    'mother_name'           => 'Mrs. ' . $s['last_name'],
                    'date_of_birth'         => Carbon::now()->subYears(rand(18, 22))->toDateString(),
                    'gender'                => $s['gender'],
                    'phone'                 => $s['phone'],
                    'email'                 => strtolower($s['first_name']) . '.' . strtolower($s['last_name']) . '@student.edu',
                    'address'               => rand(1, 99) . ' Main Street, ' . $tenant->city,
                    'city'                  => $tenant->city,
                    'state'                 => 'Andhra Pradesh',
                    'pincode'               => $tenant->pincode,
                    'marks_10th'            => $s['marks_10th'],
                    'marks_12th'            => $s['marks_12th'],
                    'current_semester'      => 1,
                    'current_year'          => 1,
                    'category'              => $s['category'],
                    'scholarship_eligible'  => in_array($s['category'], ['SC', 'ST', 'EWS']),
                    'vehicle_opted'         => ($i % 3 === 0),
                    'status'                => 'active',
                    'admission_step'        => 4,
                    'certificates_submitted'=> [],
                ]
            );

            $studentModels[] = $student;

            // ── Fee Payments for each student ─────────────────────────────────
            $uniformFee = $feeTypeModels['UNIFORM'];
            $examFee    = $feeTypeModels['EXAM'];
            $udfFee     = $feeTypeModels['UDF'];

            // Uniform fee
            FeePayment::updateOrCreate(
                ['tenant_id' => $tenant->id, 'student_id' => $student->id, 'fee_type_id' => $uniformFee->id],
                [
                    'tenant_id'        => $tenant->id,
                    'student_id'       => $student->id,
                    'fee_type_id'      => $uniformFee->id,
                    'academic_year_id' => $academicYear->id,
                    'collected_by'     => $adminUser->id,
                    'receipt_number'   => $tenantPrefix . '-RCP-' . $year . '-' . str_pad(($i * 3) + 1, 5, '0', STR_PAD_LEFT),
                    'amount_due'       => 1500,
                    'amount_paid'      => 1500,
                    'discount'         => 0,
                    'fine'             => 0,
                    'payment_mode'     => 'cash',
                    'payment_date'     => Carbon::now()->subDays(rand(1, 60))->toDateString(),
                    'status'           => 'paid',
                    'is_exempted'      => false,
                ]
            );

            // Exam fee (paid by most, pending for some)
            $examPaid = ($i % 4 !== 0);
            FeePayment::updateOrCreate(
                ['tenant_id' => $tenant->id, 'student_id' => $student->id, 'fee_type_id' => $examFee->id],
                [
                    'tenant_id'        => $tenant->id,
                    'student_id'       => $student->id,
                    'fee_type_id'      => $examFee->id,
                    'academic_year_id' => $academicYear->id,
                    'collected_by'     => $examPaid ? $adminUser->id : null,
                    'receipt_number'   => $tenantPrefix . '-RCP-' . $year . '-' . str_pad(($i * 3) + 2, 5, '0', STR_PAD_LEFT),
                    'amount_due'       => 800,
                    'amount_paid'      => $examPaid ? 800 : 0,
                    'discount'         => 0,
                    'fine'             => 0,
                    'semester'         => 1,
                    'payment_mode'     => 'cash',
                    'payment_date'     => $examPaid ? Carbon::now()->subDays(rand(1, 30))->toDateString() : now()->toDateString(),
                    'status'           => $examPaid ? 'paid' : 'pending',
                    'is_exempted'      => false,
                ]
            );

            // UDF fee
            FeePayment::updateOrCreate(
                ['tenant_id' => $tenant->id, 'student_id' => $student->id, 'fee_type_id' => $udfFee->id],
                [
                    'tenant_id'        => $tenant->id,
                    'student_id'       => $student->id,
                    'fee_type_id'      => $udfFee->id,
                    'academic_year_id' => $academicYear->id,
                    'collected_by'     => $adminUser->id,
                    'receipt_number'   => $tenantPrefix . '-RCP-' . $year . '-' . str_pad(($i * 3) + 3, 5, '0', STR_PAD_LEFT),
                    'amount_due'       => 500,
                    'amount_paid'      => 500,
                    'discount'         => 0,
                    'fine'             => 0,
                    'payment_mode'     => 'upi',
                    'payment_date'     => Carbon::now()->subDays(rand(1, 45))->toDateString(),
                    'status'           => 'paid',
                    'is_exempted'      => false,
                ]
            );
        }

        // ── Student Attendance (last 10 days) ─────────────────────────────────
        foreach ($studentModels as $student) {
            for ($day = 10; $day >= 1; $day--) {
                $date = Carbon::now()->subDays($day)->toDateString();
                // Skip Sundays
                if (Carbon::parse($date)->isSunday()) continue;

                $status = (rand(1, 10) > 2) ? 'present' : 'absent'; // 80% present

                StudentAttendance::updateOrCreate(
                    ['student_id' => $student->id, 'attendance_date' => $date, 'subject' => 'General'],
                    [
                        'tenant_id'       => $tenant->id,
                        'branch_id'       => $student->branch_id,
                        'marked_by'       => $adminUser->id,
                        'semester'        => 1,
                        'status'          => $status,
                    ]
                );
            }
        }

        // ── Staff Attendance (last 10 days) ───────────────────────────────────
        foreach ($staffModels as $staff) {
            for ($day = 10; $day >= 1; $day--) {
                $date = Carbon::now()->subDays($day)->toDateString();
                if (Carbon::parse($date)->isSunday()) continue;

                $status = (rand(1, 10) > 1) ? 'present' : 'absent'; // 90% present

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
    }
}
