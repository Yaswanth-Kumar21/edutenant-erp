<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Branch;
use App\Models\Course;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FeePayment;
use App\Models\FeeType;
use App\Models\GuardianDetail;
use App\Models\Income;
use App\Models\IncomeCategory;
use App\Models\Role;
use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Models\StaffRole;
use App\Models\Stream;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentProfile;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    // ── Shared role references ────────────────────────────────────────────────
    private Role $superAdminRole;
    private Role $collegeAdminRole;
    private Role $staffRole;
    private Role $teacherRole;
    private Role $studentRole;

    public function run(): void
    {
        $this->command->info('🚀 Starting EduTenant ERP Demo Data Seeder...');
        $this->command->newLine();

        // ── Step 1: Roles ─────────────────────────────────────────────────────
        $this->seedRoles();

        // ── Step 2: Super Admin ───────────────────────────────────────────────
        $this->seedSuperAdmin();

        // ── Step 3: Colleges ──────────────────────────────────────────────────
        $college1 = $this->seedCollege(
            name:      'Sri Venkateswara Degree College',
            slug:      'svc',
            email:     'admin@svc.edu',
            city:      'Tirupati',
            pincode:   '517501',
            principal: 'Dr. Ramesh Kumar',
            affNo:     'AU/2024/001',
            adminEmail:'admin@svc.edu',
            staffEmail:'staff@svc.edu',
        );

        $college2 = $this->seedCollege(
            name:      'Sai Chaitanya Degree College',
            slug:      'scc',
            email:     'admin@scc.edu',
            city:      'Vijayawada',
            pincode:   '520001',
            principal: 'Dr. Lakshmi Prasad',
            affNo:     'AU/2024/002',
            adminEmail:'admin@scc.edu',
            staffEmail:'staff@scc.edu',
        );

        $college3 = $this->seedCollege(
            name:      'Narayana Degree College',
            slug:      'ndc',
            email:     'admin@ndc.edu',
            city:      'Guntur',
            pincode:   '522001',
            principal: 'Dr. Suresh Babu',
            affNo:     'AU/2024/003',
            adminEmail:'admin@ndc.edu',
            staffEmail:'staff@ndc.edu',
        );

        // ── Step 4: Print credentials ─────────────────────────────────────────
        $this->printCredentials();

        $this->command->newLine();
        $this->command->info('✅ Demo data seeding complete! ERP is ready for manual UI testing.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // ROLES
    // ─────────────────────────────────────────────────────────────────────────
    private function seedRoles(): void
    {
        $roles = [
            ['name' => 'super_admin',   'display_name' => 'Super Admin',   'permissions' => ['*'],                                                                                    'is_system' => true],
            ['name' => 'college_admin', 'display_name' => 'College Admin', 'permissions' => ['manage_students','manage_staff','manage_fees','manage_attendance','manage_reports','manage_settings','send_messages','manage_expenses'], 'is_system' => true],
            ['name' => 'staff',         'display_name' => 'Staff',         'permissions' => ['manage_students','collect_fees','view_reports','mark_attendance','send_messages'],       'is_system' => true],
            ['name' => 'teacher',       'display_name' => 'Teacher',       'permissions' => ['mark_attendance','view_students','send_messages'],                                       'is_system' => true],
            ['name' => 'student',       'display_name' => 'Student',       'permissions' => ['view_own_profile','view_own_fees','view_own_attendance'],                                'is_system' => true],
        ];

        foreach ($roles as $r) {
            Role::updateOrCreate(['name' => $r['name']], $r);
        }

        $this->superAdminRole   = Role::where('name', 'super_admin')->first();
        $this->collegeAdminRole = Role::where('name', 'college_admin')->first();
        $this->staffRole        = Role::where('name', 'staff')->first();
        $this->teacherRole      = Role::where('name', 'teacher')->first();
        $this->studentRole      = Role::where('name', 'student')->first();

        $this->command->info('  ✓ Roles seeded (5 roles)');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SUPER ADMIN
    // ─────────────────────────────────────────────────────────────────────────
    private function seedSuperAdmin(): void
    {
        User::updateOrCreate(['email' => 'superadmin@erp.com'], [
            'name'           => 'Super Admin',
            'email'          => 'superadmin@erp.com',
            'password'       => Hash::make('password'),
            'role_id'        => $this->superAdminRole->id,
            'is_super_admin' => true,
            'status'         => 'active',
        ]);
        $this->command->info('  ✓ Super Admin created');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // COLLEGE (TENANT) SETUP
    // ─────────────────────────────────────────────────────────────────────────
    private function seedCollege(
        string $name, string $slug, string $email,
        string $city, string $pincode, string $principal,
        string $affNo, string $adminEmail, string $staffEmail
    ): Tenant {
        $this->command->newLine();
        $this->command->info("  📚 Seeding: {$name}");

        // ── Tenant ────────────────────────────────────────────────────────────
        $tenant = Tenant::updateOrCreate(['slug' => $slug], [
            'name'               => $name,
            'slug'               => $slug,
            'email'              => $email,
            'phone'              => '9876' . rand(100000, 999999),
            'address'            => rand(10,99) . ' College Road',
            'city'               => $city,
            'state'              => 'Andhra Pradesh',
            'pincode'            => $pincode,
            'principal_name'     => $principal,
            'affiliation_number' => $affNo,
            'status'             => 'active',
            'subscription_start' => now(),
            'subscription_end'   => now()->addYear(),
        ]);

        // ── Academic Year ─────────────────────────────────────────────────────
        $ay = AcademicYear::updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => '2024-2025'],
            ['tenant_id' => $tenant->id, 'name' => '2024-2025',
             'start_date' => '2024-06-01', 'end_date' => '2025-05-31', 'is_current' => true]
        );

        // ── Streams ───────────────────────────────────────────────────────────
        $sci  = Stream::updateOrCreate(['tenant_id' => $tenant->id, 'name' => 'Science'],
            ['tenant_id' => $tenant->id, 'name' => 'Science', 'code' => 'SCI', 'is_active' => true]);
        $arts = Stream::updateOrCreate(['tenant_id' => $tenant->id, 'name' => 'Arts'],
            ['tenant_id' => $tenant->id, 'name' => 'Arts', 'code' => 'ARTS', 'is_active' => true]);

        // ── Courses ───────────────────────────────────────────────────────────
        $bsc  = Course::updateOrCreate(['tenant_id' => $tenant->id, 'name' => 'B.Sc'],
            ['tenant_id' => $tenant->id, 'stream_id' => $sci->id,  'name' => 'B.Sc',  'code' => 'BSC',  'duration_years' => 3, 'total_semesters' => 6, 'has_record_fee' => true,  'is_active' => true]);
        $ba   = Course::updateOrCreate(['tenant_id' => $tenant->id, 'name' => 'BA'],
            ['tenant_id' => $tenant->id, 'stream_id' => $arts->id, 'name' => 'BA',    'code' => 'BA',   'duration_years' => 3, 'total_semesters' => 6, 'has_record_fee' => false, 'is_active' => true]);
        $bcom = Course::updateOrCreate(['tenant_id' => $tenant->id, 'name' => 'BCom'],
            ['tenant_id' => $tenant->id, 'stream_id' => $arts->id, 'name' => 'BCom',  'code' => 'BCOM', 'duration_years' => 3, 'total_semesters' => 6, 'has_record_fee' => false, 'is_active' => true]);

        // ── Branches ─────────────────────────────────────────────────────────
        $mpc  = Branch::updateOrCreate(['tenant_id' => $tenant->id, 'name' => 'MPC'],
            ['tenant_id' => $tenant->id, 'course_id' => $bsc->id,  'name' => 'MPC',     'code' => 'MPC',   'intake_capacity' => 60, 'tuition_fee_student' => 8000,  'tuition_fee_govt' => 15000, 'has_record_fee' => true,  'is_active' => true]);
        $bipc = Branch::updateOrCreate(['tenant_id' => $tenant->id, 'name' => 'BiPC'],
            ['tenant_id' => $tenant->id, 'course_id' => $bsc->id,  'name' => 'BiPC',    'code' => 'BIPC',  'intake_capacity' => 60, 'tuition_fee_student' => 9000,  'tuition_fee_govt' => 15000, 'has_record_fee' => true,  'is_active' => true]);
        $hep  = Branch::updateOrCreate(['tenant_id' => $tenant->id, 'name' => 'HEP'],
            ['tenant_id' => $tenant->id, 'course_id' => $ba->id,   'name' => 'HEP',     'code' => 'HEP',   'intake_capacity' => 60, 'tuition_fee_student' => 5000,  'tuition_fee_govt' => 15000, 'has_record_fee' => false, 'is_active' => true]);
        $bcomG= Branch::updateOrCreate(['tenant_id' => $tenant->id, 'name' => 'BCom General'],
            ['tenant_id' => $tenant->id, 'course_id' => $bcom->id, 'name' => 'BCom General', 'code' => 'BCOMG', 'intake_capacity' => 60, 'tuition_fee_student' => 6000, 'tuition_fee_govt' => 15000, 'has_record_fee' => false, 'is_active' => true]);

        // ── Fee Types ─────────────────────────────────────────────────────────
        $feeTypes = $this->seedFeeTypes($tenant);

        // ── Staff Roles ───────────────────────────────────────────────────────
        $this->seedStaffRoles($tenant);

        // ── Categories ───────────────────────────────────────────────────────
        $this->seedCategories($tenant);

        // ── Users: Admin + Staff + Teachers ──────────────────────────────────
        $adminUser = $this->seedAdminUser($tenant, $adminEmail, $name);
        $staffUser = $this->seedStaffUser($tenant, $staffEmail);
        $teachers  = $this->seedTeachers($tenant, $slug);

        // ── Staff records ─────────────────────────────────────────────────────
        $staffModels = $this->seedStaffRecords($tenant, $teachers);

        // ── Students ─────────────────────────────────────────────────────────
        $students = $this->seedStudents($tenant, $ay, $mpc, $bipc, $hep, $bcomG, $adminUser, $feeTypes, $slug);

        // ── Attendance ────────────────────────────────────────────────────────
        $this->seedAttendance($tenant, $students, $staffModels, $adminUser);

        // ── Finance ───────────────────────────────────────────────────────────
        $this->seedFinance($tenant, $adminUser);

        $this->command->info("    ✓ {$name} fully seeded");
        return $tenant;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // FEE TYPES
    // ─────────────────────────────────────────────────────────────────────────
    private function seedFeeTypes(Tenant $tenant): array
    {
        $types = [
            ['code' => 'UNIFORM',    'name' => 'Uniform Fee',    'frequency' => 'one_time',    'amount' => 1500, 'sort_order' => 1],
            ['code' => 'EXAM',       'name' => 'Exam Fee',       'frequency' => 'per_semester','amount' => 800,  'sort_order' => 2],
            ['code' => 'UDF',        'name' => 'UDF Fee',        'frequency' => 'per_year',    'amount' => 500,  'sort_order' => 3],
            ['code' => 'RECORD',     'name' => 'Record Fee',     'frequency' => 'per_semester','amount' => 300,  'sort_order' => 4],
            ['code' => 'VEHICLE',    'name' => 'Vehicle Fee',    'frequency' => 'monthly',     'amount' => 600,  'sort_order' => 5],
            ['code' => 'TUITION',    'name' => 'Tuition Fee',    'frequency' => 'per_year',    'amount' => 0,    'sort_order' => 6],
            ['code' => 'OTHER',      'name' => 'Other Fee',      'frequency' => 'one_time',    'amount' => 0,    'sort_order' => 7],
            ['code' => 'INTERNSHIP', 'name' => 'Internship Fee', 'frequency' => 'one_time',    'amount' => 1000, 'sort_order' => 8],
        ];

        $result = [];
        foreach ($types as $t) {
            $result[$t['code']] = FeeType::updateOrCreate(
                ['tenant_id' => $tenant->id, 'code' => $t['code']],
                array_merge($t, ['tenant_id' => $tenant->id, 'is_active' => true,
                    'applicable_all_streams' => true, 'applicable_all_branches' => true, 'can_be_exempted' => true])
            );
        }
        return $result;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STAFF ROLES + CATEGORIES
    // ─────────────────────────────────────────────────────────────────────────
    private function seedStaffRoles(Tenant $tenant): void
    {
        $roles = [
            ['name' => 'Principal',          'department' => 'Administration', 'staff_type' => 'both'],
            ['name' => 'Professor',          'department' => 'Academic',       'staff_type' => 'teaching'],
            ['name' => 'Associate Professor','department' => 'Academic',       'staff_type' => 'teaching'],
            ['name' => 'Lecturer',           'department' => 'Academic',       'staff_type' => 'teaching'],
            ['name' => 'Lab Assistant',      'department' => 'Science',        'staff_type' => 'non_teaching'],
            ['name' => 'Office Staff',       'department' => 'Administration', 'staff_type' => 'non_teaching'],
            ['name' => 'Accountant',         'department' => 'Finance',        'staff_type' => 'non_teaching'],
            ['name' => 'Librarian',          'department' => 'Library',        'staff_type' => 'non_teaching'],
        ];
        foreach ($roles as $r) {
            StaffRole::updateOrCreate(
                ['tenant_id' => $tenant->id, 'name' => $r['name']],
                array_merge($r, ['tenant_id' => $tenant->id, 'is_active' => true])
            );
        }
    }

    private function seedCategories(Tenant $tenant): void
    {
        $cats = [
            ['code' => 'GEN', 'name' => 'General',                   'sort_order' => 1],
            ['code' => 'OBC', 'name' => 'Other Backward Class',       'sort_order' => 2],
            ['code' => 'SC',  'name' => 'Scheduled Caste',            'sort_order' => 3],
            ['code' => 'ST',  'name' => 'Scheduled Tribe',            'sort_order' => 4],
            ['code' => 'EWS', 'name' => 'Economically Weaker Section','sort_order' => 5],
        ];
        foreach ($cats as $c) {
            \App\Models\Category::updateOrCreate(
                ['tenant_id' => $tenant->id, 'code' => $c['code']],
                array_merge($c, ['tenant_id' => $tenant->id, 'is_active' => true])
            );
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // USERS: ADMIN, STAFF, TEACHERS
    // ─────────────────────────────────────────────────────────────────────────
    private function seedAdminUser(Tenant $tenant, string $email, string $collegeName): User
    {
        return User::updateOrCreate(['email' => $email], [
            'name'      => 'College Admin',
            'email'     => $email,
            'password'  => Hash::make('password'),
            'tenant_id' => $tenant->id,
            'role_id'   => $this->collegeAdminRole->id,
            'status'    => 'active',
        ]);
    }

    private function seedStaffUser(Tenant $tenant, string $email): User
    {
        return User::updateOrCreate(['email' => $email], [
            'name'      => 'Admission Staff',
            'email'     => $email,
            'password'  => Hash::make('password'),
            'tenant_id' => $tenant->id,
            'role_id'   => $this->staffRole->id,
            'status'    => 'active',
        ]);
    }

    private function seedTeachers(Tenant $tenant, string $slug): array
    {
        $teachers = [
            ['name' => 'Dr. Ravi Kumar',    'subject' => 'Mathematics', 'email' => "ravi.kumar@{$slug}.edu"],
            ['name' => 'Mrs. Priya Sharma', 'subject' => 'Physics',     'email' => "priya.sharma@{$slug}.edu"],
            ['name' => 'Mr. Anil Reddy',    'subject' => 'Chemistry',   'email' => "anil.reddy@{$slug}.edu"],
            ['name' => 'Ms. Kavitha Rao',   'subject' => 'Biology',     'email' => "kavitha.rao@{$slug}.edu"],
            ['name' => 'Mr. Suresh Naidu',  'subject' => 'English',     'email' => "suresh.naidu@{$slug}.edu"],
        ];

        $users = [];
        foreach ($teachers as $t) {
            $users[] = User::updateOrCreate(['email' => $t['email']], [
                'name'      => $t['name'],
                'email'     => $t['email'],
                'password'  => Hash::make('password'),
                'tenant_id' => $tenant->id,
                'role_id'   => $this->teacherRole->id,
                'status'    => 'active',
            ]);
        }
        return $users;
    }

    private function seedStaffRecords(Tenant $tenant, array $teacherUsers): array
    {
        $profRole = StaffRole::where('tenant_id', $tenant->id)->where('name', 'Professor')->first();
        $offRole  = StaffRole::where('tenant_id', $tenant->id)->where('name', 'Office Staff')->first();

        $staffData = [
            ['name' => 'Dr. Ravi Kumar',    'type' => 'teaching',     'subject' => 'Mathematics', 'salary' => 45000, 'role' => $profRole],
            ['name' => 'Mrs. Priya Sharma', 'type' => 'teaching',     'subject' => 'Physics',     'salary' => 40000, 'role' => $profRole],
            ['name' => 'Mr. Anil Reddy',    'type' => 'teaching',     'subject' => 'Chemistry',   'salary' => 35000, 'role' => $profRole],
            ['name' => 'Ms. Kavitha Rao',   'type' => 'teaching',     'subject' => 'Biology',     'salary' => 33000, 'role' => $profRole],
            ['name' => 'Mr. Suresh Naidu',  'type' => 'teaching',     'subject' => 'English',     'salary' => 30000, 'role' => $profRole],
            ['name' => 'Mr. Ramesh Clerk',  'type' => 'non_teaching', 'subject' => null,          'salary' => 18000, 'role' => $offRole],
            ['name' => 'Mrs. Sunita Peon',  'type' => 'non_teaching', 'subject' => null,          'salary' => 12000, 'role' => $offRole],
        ];

        $models = [];
        foreach ($staffData as $i => $s) {
            $models[] = Staff::updateOrCreate(
                ['tenant_id' => $tenant->id, 'name' => $s['name']],
                [
                    'tenant_id'                  => $tenant->id,
                    'staff_role_id'              => $s['role']?->id,
                    'staff_code'                 => 'STF-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                    'name'                       => $s['name'],
                    'staff_type'                 => $s['type'],
                    'subject'                    => $s['subject'],
                    'monthly_salary'             => $s['salary'],
                    'basic_salary'               => $s['salary'] * 0.6,
                    'hra'                        => $s['salary'] * 0.2,
                    'da'                         => $s['salary'] * 0.1,
                    'other_allowances'           => $s['salary'] * 0.1,
                    'pf_deduction'               => $s['salary'] * 0.12,
                    'allowed_holidays_per_month' => 2,
                    'salary_calculation_days'    => 30,
                    'date_of_joining'            => Carbon::now()->subYears(rand(1, 8))->toDateString(),
                    'status'                     => 'active',
                ]
            );
        }
        return $models;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STUDENTS
    // ─────────────────────────────────────────────────────────────────────────
    private function seedStudents(
        Tenant $tenant, AcademicYear $ay,
        Branch $mpc, Branch $bipc, Branch $hep, Branch $bcomG,
        User $adminUser, array $feeTypes, string $slug
    ): array {
        $year = now()->year;

        $data = [
            ['fn'=>'Ravi',    'ln'=>'Teja',    'branch'=>$mpc,   'cat'=>'GEN','gender'=>'male',  'phone'=>"81{$slug}0001",'m10'=>88,'m12'=>82,'vehicle'=>false],
            ['fn'=>'Sravani', 'ln'=>'Reddy',   'branch'=>$mpc,   'cat'=>'OBC','gender'=>'female','phone'=>"81{$slug}0002",'m10'=>91,'m12'=>87,'vehicle'=>true],
            ['fn'=>'Mahesh',  'ln'=>'Babu',    'branch'=>$mpc,   'cat'=>'SC', 'gender'=>'male',  'phone'=>"81{$slug}0003",'m10'=>75,'m12'=>70,'vehicle'=>false],
            ['fn'=>'Pooja',   'ln'=>'Sharma',  'branch'=>$bipc,  'cat'=>'GEN','gender'=>'female','phone'=>"81{$slug}0004",'m10'=>93,'m12'=>89,'vehicle'=>true],
            ['fn'=>'Aakash',  'ln'=>'Kumar',   'branch'=>$bipc,  'cat'=>'OBC','gender'=>'male',  'phone'=>"81{$slug}0005",'m10'=>80,'m12'=>76,'vehicle'=>false],
            ['fn'=>'Meghana', 'ln'=>'Rao',     'branch'=>$bipc,  'cat'=>'ST', 'gender'=>'female','phone'=>"81{$slug}0006",'m10'=>72,'m12'=>68,'vehicle'=>false],
            ['fn'=>'Charan',  'ln'=>'Tej',     'branch'=>$hep,   'cat'=>'GEN','gender'=>'male',  'phone'=>"81{$slug}0007",'m10'=>78,'m12'=>74,'vehicle'=>true],
            ['fn'=>'Bhavana', 'ln'=>'Nair',    'branch'=>$hep,   'cat'=>'EWS','gender'=>'female','phone'=>"81{$slug}0008",'m10'=>84,'m12'=>80,'vehicle'=>false],
            ['fn'=>'Naveen',  'ln'=>'Chandra', 'branch'=>$hep,   'cat'=>'OBC','gender'=>'male',  'phone'=>"81{$slug}0009",'m10'=>77,'m12'=>73,'vehicle'=>false],
            ['fn'=>'Keerthi', 'ln'=>'Suresh',  'branch'=>$bcomG, 'cat'=>'GEN','gender'=>'female','phone'=>"81{$slug}0010",'m10'=>86,'m12'=>83,'vehicle'=>true],
            ['fn'=>'Tarun',   'ln'=>'Varma',   'branch'=>$bcomG, 'cat'=>'SC', 'gender'=>'male',  'phone'=>"81{$slug}0011",'m10'=>71,'m12'=>67,'vehicle'=>false],
            ['fn'=>'Haritha', 'ln'=>'Menon',   'branch'=>$bcomG, 'cat'=>'GEN','gender'=>'female','phone'=>"81{$slug}0012",'m10'=>89,'m12'=>85,'vehicle'=>false],
        ];

        $students = [];
        foreach ($data as $i => $s) {
            $admNum = "EDU-{$year}-" . str_pad($i + 1, 4, '0', STR_PAD_LEFT) . strtoupper($slug);
            $email  = strtolower($s['fn']) . '.' . strtolower($s['ln']) . "@{$slug}.student.edu";

            $student = Student::updateOrCreate(
                ['tenant_id' => $tenant->id, 'phone' => $s['phone']],
                [
                    'tenant_id'             => $tenant->id,
                    'branch_id'             => $s['branch']->id,
                    'academic_year_id'      => $ay->id,
                    'admission_number'      => $admNum,
                    'admission_date'        => Carbon::now()->subMonths(rand(2, 8))->toDateString(),
                    'first_name'            => $s['fn'],
                    'last_name'             => $s['ln'],
                    'father_name'           => 'Mr. ' . $s['ln'],
                    'mother_name'           => 'Mrs. ' . $s['ln'],
                    'date_of_birth'         => Carbon::now()->subYears(rand(18, 22))->toDateString(),
                    'gender'                => $s['gender'],
                    'phone'                 => $s['phone'],
                    'email'                 => $email,
                    'address'               => rand(10, 99) . ' Gandhi Nagar',
                    'city'                  => 'Tirupati',
                    'state'                 => 'Andhra Pradesh',
                    'pincode'               => '517501',
                    'marks_10th'            => $s['m10'],
                    'marks_12th'            => $s['m12'],
                    'current_semester'      => rand(1, 3),
                    'current_year'          => 1,
                    'category'              => $s['cat'],
                    'scholarship_eligible'  => in_array($s['cat'], ['SC', 'ST', 'EWS']),
                    'vehicle_opted'         => $s['vehicle'],
                    'vehicle_start_date'    => $s['vehicle'] ? Carbon::now()->subMonths(rand(1, 4))->toDateString() : null,
                    'status'                => 'active',
                    'admission_step'        => 4,
                    'certificates_submitted'=> [],
                ]
            );

            // Student profile
            StudentProfile::updateOrCreate(['student_id' => $student->id], [
                'tenant_id'  => $tenant->id,
                'student_id' => $student->id,
                'blood_group'=> ['A+','B+','O+','AB+'][rand(0,3)],
            ]);

            // Guardian
            GuardianDetail::updateOrCreate(['student_id' => $student->id], [
                'tenant_id'    => $tenant->id,
                'student_id'   => $student->id,
                'father_name'  => 'Mr. ' . $s['ln'],
                'father_phone' => '9' . rand(100000000, 999999999),
                'mother_name'  => 'Mrs. ' . $s['ln'],
                'annual_income'=> rand(150000, 600000),
            ]);

            // Student login user
            if (!User::where('email', $email)->exists()) {
                $user = User::create([
                    'tenant_id' => $tenant->id,
                    'role_id'   => $this->studentRole->id,
                    'name'      => $s['fn'] . ' ' . $s['ln'],
                    'email'     => $email,
                    'phone'     => $s['phone'],
                    'password'  => Hash::make($s['phone']),
                    'status'    => 'active',
                ]);
                $student->update(['user_id' => $user->id]);
            }

            // Fee payments
            $this->seedStudentFees($student, $tenant, $ay, $adminUser, $feeTypes, $i);

            $students[] = $student;
        }
        return $students;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STUDENT FEE PAYMENTS
    // ─────────────────────────────────────────────────────────────────────────
    private function seedStudentFees(
        Student $student, Tenant $tenant, AcademicYear $ay,
        User $adminUser, array $feeTypes, int $index
    ): void {
        $year    = now()->year;
        $base    = 'RCP-' . $year . '-' . $tenant->slug . '-' . str_pad($index * 5 + 1, 5, '0', STR_PAD_LEFT);
        $isPaid  = fn() => rand(1, 10) > 2; // 80% paid

        // Uniform fee — always paid
        FeePayment::updateOrCreate(
            ['tenant_id' => $tenant->id, 'student_id' => $student->id, 'fee_type_id' => $feeTypes['UNIFORM']->id],
            [
                'tenant_id' => $tenant->id, 'student_id' => $student->id,
                'fee_type_id' => $feeTypes['UNIFORM']->id, 'academic_year_id' => $ay->id,
                'collected_by' => $adminUser->id, 'receipt_number' => $base . 'A',
                'amount_due' => 1500, 'amount_paid' => 1500, 'discount' => 0, 'fine' => 0,
                'payment_mode' => ['cash','upi','card'][rand(0,2)],
                'payment_date' => Carbon::now()->subDays(rand(5, 90))->toDateString(),
                'status' => 'paid', 'is_exempted' => false,
            ]
        );

        // Exam fee — 80% paid
        $paid = $isPaid();
        FeePayment::updateOrCreate(
            ['tenant_id' => $tenant->id, 'student_id' => $student->id, 'fee_type_id' => $feeTypes['EXAM']->id],
            [
                'tenant_id' => $tenant->id, 'student_id' => $student->id,
                'fee_type_id' => $feeTypes['EXAM']->id, 'academic_year_id' => $ay->id,
                'collected_by' => $paid ? $adminUser->id : null, 'receipt_number' => $base . 'B',
                'amount_due' => 800, 'amount_paid' => $paid ? 800 : 0,
                'discount' => in_array($student->category, ['SC','ST']) ? 200 : 0,
                'fine' => 0, 'semester' => 1,
                'payment_mode' => 'cash',
                'payment_date' => Carbon::now()->subDays(rand(1, 45))->toDateString(),
                'status' => $paid ? 'paid' : 'pending', 'is_exempted' => false,
            ]
        );

        // UDF fee — always paid
        FeePayment::updateOrCreate(
            ['tenant_id' => $tenant->id, 'student_id' => $student->id, 'fee_type_id' => $feeTypes['UDF']->id],
            [
                'tenant_id' => $tenant->id, 'student_id' => $student->id,
                'fee_type_id' => $feeTypes['UDF']->id, 'academic_year_id' => $ay->id,
                'collected_by' => $adminUser->id, 'receipt_number' => $base . 'C',
                'amount_due' => 500, 'amount_paid' => 500, 'discount' => 0, 'fine' => 0,
                'payment_mode' => 'upi',
                'payment_date' => Carbon::now()->subDays(rand(10, 60))->toDateString(),
                'status' => 'paid', 'is_exempted' => false,
            ]
        );

        // Record fee — only for B.Sc branches
        if ($student->branch?->has_record_fee) {
            FeePayment::updateOrCreate(
                ['tenant_id' => $tenant->id, 'student_id' => $student->id, 'fee_type_id' => $feeTypes['RECORD']->id],
                [
                    'tenant_id' => $tenant->id, 'student_id' => $student->id,
                    'fee_type_id' => $feeTypes['RECORD']->id, 'academic_year_id' => $ay->id,
                    'collected_by' => $adminUser->id, 'receipt_number' => $base . 'D',
                    'amount_due' => 300, 'amount_paid' => 300, 'discount' => 0, 'fine' => 0,
                    'semester' => 1, 'payment_mode' => 'cash',
                    'payment_date' => Carbon::now()->subDays(rand(5, 30))->toDateString(),
                    'status' => 'paid', 'is_exempted' => false,
                ]
            );
        }

        // Vehicle fee — for opted students
        if ($student->vehicle_opted) {
            FeePayment::updateOrCreate(
                ['tenant_id' => $tenant->id, 'student_id' => $student->id, 'fee_type_id' => $feeTypes['VEHICLE']->id],
                [
                    'tenant_id' => $tenant->id, 'student_id' => $student->id,
                    'fee_type_id' => $feeTypes['VEHICLE']->id, 'academic_year_id' => $ay->id,
                    'collected_by' => $adminUser->id, 'receipt_number' => $base . 'E',
                    'amount_due' => 600, 'amount_paid' => 600, 'discount' => 0, 'fine' => 0,
                    'month' => now()->month, 'payment_mode' => 'cash',
                    'payment_date' => Carbon::now()->subDays(rand(1, 20))->toDateString(),
                    'status' => 'paid', 'is_exempted' => false,
                ]
            );
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // ATTENDANCE
    // ─────────────────────────────────────────────────────────────────────────
    private function seedAttendance(Tenant $tenant, array $students, array $staffModels, User $adminUser): void
    {
        // Student attendance — last 25 working days
        foreach ($students as $student) {
            for ($day = 25; $day >= 1; $day--) {
                $date = Carbon::now()->subDays($day);
                if ($date->isSunday()) continue;

                $status = rand(1, 10) > 2 ? 'present' : 'absent';

                StudentAttendance::updateOrCreate(
                    ['student_id' => $student->id, 'attendance_date' => $date->toDateString(), 'subject' => 'General'],
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

        // Staff attendance — last 25 working days
        foreach ($staffModels as $staff) {
            for ($day = 25; $day >= 1; $day--) {
                $date = Carbon::now()->subDays($day);
                if ($date->isSunday()) continue;

                $status = rand(1, 10) > 1 ? 'present' : 'absent';

                StaffAttendance::updateOrCreate(
                    ['staff_id' => $staff->id, 'attendance_date' => $date->toDateString()],
                    [
                        'tenant_id' => $tenant->id,
                        'marked_by' => $adminUser->id,
                        'status'    => $status,
                    ]
                );
            }
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // FINANCE — EXPENSES + INCOME
    // ─────────────────────────────────────────────────────────────────────────
    private function seedFinance(Tenant $tenant, User $adminUser): void
    {
        // Expense categories
        $expCats = [
            ['name' => 'Vehicle Petrol',   'code' => 'PETROL'],
            ['name' => 'Electricity Bill', 'code' => 'ELECTRICITY'],
            ['name' => 'Water Bill',       'code' => 'WATER'],
            ['name' => 'Stationery',       'code' => 'STATIONERY'],
            ['name' => 'Travelling',       'code' => 'TRAVEL'],
            ['name' => 'Vehicle Repairs',  'code' => 'VEHICLE_REP'],
        ];
        $expCatModels = [];
        foreach ($expCats as $c) {
            $expCatModels[$c['code']] = ExpenseCategory::updateOrCreate(
                ['tenant_id' => $tenant->id, 'code' => $c['code']],
                ['tenant_id' => $tenant->id, 'name' => $c['name'], 'is_active' => true]
            );
        }

        // Expenses — last 3 months
        $expenseData = [
            ['cat' => 'PETROL',      'title' => 'Bus fuel - October',    'amount' => 4500,  'days_ago' => 5],
            ['cat' => 'ELECTRICITY', 'title' => 'Electricity - October', 'amount' => 8200,  'days_ago' => 8],
            ['cat' => 'WATER',       'title' => 'Water bill - October',  'amount' => 1200,  'days_ago' => 10],
            ['cat' => 'STATIONERY',  'title' => 'Office stationery',     'amount' => 3400,  'days_ago' => 15],
            ['cat' => 'TRAVEL',      'title' => 'Staff travel allowance','amount' => 2800,  'days_ago' => 20],
            ['cat' => 'PETROL',      'title' => 'Bus fuel - September',  'amount' => 4200,  'days_ago' => 35],
            ['cat' => 'ELECTRICITY', 'title' => 'Electricity - September','amount' => 7900, 'days_ago' => 38],
            ['cat' => 'VEHICLE_REP', 'title' => 'Bus tyre replacement',  'amount' => 12000, 'days_ago' => 45],
        ];

        foreach ($expenseData as $e) {
            Expense::create([
                'tenant_id'           => $tenant->id,
                'expense_category_id' => $expCatModels[$e['cat']]->id,
                'recorded_by'         => $adminUser->id,
                'title'               => $e['title'],
                'amount'              => $e['amount'],
                'expense_date'        => Carbon::now()->subDays($e['days_ago'])->toDateString(),
                'payment_mode'        => 'cash',
            ]);
        }

        // Income categories
        $incCats = [
            ['name' => 'Borrowings',  'code' => 'BORROW'],
            ['name' => 'Donations',   'code' => 'DONATION'],
            ['name' => 'Grants',      'code' => 'GRANT'],
        ];
        $incCatModels = [];
        foreach ($incCats as $c) {
            $incCatModels[$c['code']] = IncomeCategory::updateOrCreate(
                ['tenant_id' => $tenant->id, 'code' => $c['code']],
                ['tenant_id' => $tenant->id, 'name' => $c['name'], 'is_active' => true]
            );
        }

        // Income records
        $incomeData = [
            ['cat' => 'GRANT',    'title' => 'State Govt Education Grant', 'amount' => 150000, 'days_ago' => 30],
            ['cat' => 'DONATION', 'title' => 'Alumni donation',            'amount' => 25000,  'days_ago' => 45],
            ['cat' => 'BORROW',   'title' => 'Bank loan - infrastructure', 'amount' => 500000, 'days_ago' => 60],
        ];

        foreach ($incomeData as $inc) {
            Income::create([
                'tenant_id'          => $tenant->id,
                'income_category_id' => $incCatModels[$inc['cat']]->id,
                'recorded_by'        => $adminUser->id,
                'title'              => $inc['title'],
                'amount'             => $inc['amount'],
                'income_date'        => Carbon::now()->subDays($inc['days_ago'])->toDateString(),
                'payment_mode'       => 'online',
            ]);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CREDENTIALS REPORT
    // ─────────────────────────────────────────────────────────────────────────
    private function printCredentials(): void
    {
        $line = str_repeat('─', 70);
        $this->command->newLine();
        $this->command->line("<fg=cyan>{$line}</>");
        $this->command->line('<fg=cyan>  DEMO LOGIN CREDENTIALS — EduTenant ERP</>');
        $this->command->line("<fg=cyan>{$line}</>");

        $this->command->newLine();
        $this->command->line('<fg=yellow>  SUPER ADMIN</>');
        $this->command->line('  Email    : superadmin@erp.com');
        $this->command->line('  Password : password');
        $this->command->line('  Access   : All colleges, platform-wide');

        $this->command->newLine();
        $this->command->line('<fg=yellow>  COLLEGE 1 — Sri Venkateswara Degree College (Tirupati)</>');
        $this->command->line('  College Admin  : admin@svc.edu          / password');
        $this->command->line('  Staff          : staff@svc.edu          / password');
        $this->command->line('  Teacher 1      : ravi.kumar@svc.edu     / password');
        $this->command->line('  Teacher 2      : priya.sharma@svc.edu   / password');
        $this->command->line('  Teacher 3      : anil.reddy@svc.edu     / password');
        $this->command->line('  Teacher 4      : kavitha.rao@svc.edu    / password');
        $this->command->line('  Teacher 5      : suresh.naidu@svc.edu   / password');
        $this->command->line('  Student 1      : ravi.teja@svc.student.edu      / 81svc0001');
        $this->command->line('  Student 2      : sravani.reddy@svc.student.edu  / 81svc0002');
        $this->command->line('  Student 3      : mahesh.babu@svc.student.edu    / 81svc0003');
        $this->command->line('  Student 4      : pooja.sharma@svc.student.edu   / 81svc0004');
        $this->command->line('  Student 5      : aakash.kumar@svc.student.edu   / 81svc0005');

        $this->command->newLine();
        $this->command->line('<fg=yellow>  COLLEGE 2 — Sai Chaitanya Degree College (Vijayawada)</>');
        $this->command->line('  College Admin  : admin@scc.edu          / password');
        $this->command->line('  Staff          : staff@scc.edu          / password');
        $this->command->line('  Teacher 1      : ravi.kumar@scc.edu     / password');
        $this->command->line('  Teacher 2      : priya.sharma@scc.edu   / password');
        $this->command->line('  Student 1      : ravi.teja@scc.student.edu      / 81scc0001');
        $this->command->line('  Student 2      : sravani.reddy@scc.student.edu  / 81scc0002');
        $this->command->line('  Student 3      : mahesh.babu@scc.student.edu    / 81scc0003');

        $this->command->newLine();
        $this->command->line('<fg=yellow>  COLLEGE 3 — Narayana Degree College (Guntur)</>');
        $this->command->line('  College Admin  : admin@ndc.edu          / password');
        $this->command->line('  Staff          : staff@ndc.edu          / password');
        $this->command->line('  Teacher 1      : ravi.kumar@ndc.edu     / password');
        $this->command->line('  Student 1      : ravi.teja@ndc.student.edu      / 81ndc0001');
        $this->command->line('  Student 2      : sravani.reddy@ndc.student.edu  / 81ndc0002');

        $this->command->newLine();
        $this->command->line("<fg=cyan>{$line}</>");
        $this->command->line('<fg=green>  NOTE: Student passwords = their phone number</>');
        $this->command->line('<fg=green>  NOTE: All admin/staff/teacher passwords = "password"</>');
        $this->command->line("<fg=cyan>{$line}</>");
        $this->command->newLine();
    }
}
