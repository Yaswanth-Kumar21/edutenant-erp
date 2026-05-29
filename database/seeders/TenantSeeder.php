<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Branch;
use App\Models\Course;
use App\Models\FeeType;
use App\Models\Role;
use App\Models\Stream;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        // ── Create Demo Tenant (College) ──────────────────────────────────────
        $tenant = Tenant::updateOrCreate(
            ['slug' => 'demo-college'],
            [
                'name'             => 'Sri Venkateswara Degree College',
                'slug'             => 'demo-college',
                'email'            => 'admin@svdcollege.edu',
                'phone'            => '9876543210',
                'address'          => '123 College Road, Tirupati',
                'city'             => 'Tirupati',
                'state'            => 'Andhra Pradesh',
                'pincode'          => '517501',
                'principal_name'   => 'Dr. Ramesh Kumar',
                'affiliation_number' => 'AU/2024/001',
                'status'           => 'active',
                'subscription_start' => now(),
                'subscription_end'   => now()->addYear(),
            ]
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
            [
                'tenant_id'       => $tenant->id,
                'stream_id'       => $science->id,
                'name'            => 'B.Sc',
                'code'            => 'BSC',
                'duration_years'  => 3,
                'total_semesters' => 6,
                'has_record_fee'  => true,
                'is_active'       => true,
            ]
        );

        $ba = Course::updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'BA'],
            [
                'tenant_id'       => $tenant->id,
                'stream_id'       => $arts->id,
                'name'            => 'BA',
                'code'            => 'BA',
                'duration_years'  => 3,
                'total_semesters' => 6,
                'has_record_fee'  => false,
                'is_active'       => true,
            ]
        );

        $bcom = Course::updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'BCom'],
            [
                'tenant_id'       => $tenant->id,
                'stream_id'       => $arts->id,
                'name'            => 'BCom',
                'code'            => 'BCOM',
                'duration_years'  => 3,
                'total_semesters' => 6,
                'has_record_fee'  => false,
                'is_active'       => true,
            ]
        );

        // ── Branches ──────────────────────────────────────────────────────────
        $branches = [
            ['course_id' => $bsc->id,  'name' => 'MPC',   'code' => 'MPC',   'tuition_fee_student' => 8000,  'has_record_fee' => true],
            ['course_id' => $bsc->id,  'name' => 'MPCs',  'code' => 'MPCS',  'tuition_fee_student' => 8000,  'has_record_fee' => true],
            ['course_id' => $bsc->id,  'name' => 'BiPC',  'code' => 'BIPC',  'tuition_fee_student' => 9000,  'has_record_fee' => true],
            ['course_id' => $bsc->id,  'name' => 'CZBt',  'code' => 'CZBT',  'tuition_fee_student' => 9000,  'has_record_fee' => true],
            ['course_id' => $ba->id,   'name' => 'HEP',   'code' => 'HEP',   'tuition_fee_student' => 5000,  'has_record_fee' => false],
            ['course_id' => $ba->id,   'name' => 'HPS',   'code' => 'HPS',   'tuition_fee_student' => 5000,  'has_record_fee' => false],
            ['course_id' => $bcom->id, 'name' => 'General','code' => 'BCOMG', 'tuition_fee_student' => 6000,  'has_record_fee' => false],
        ];

        foreach ($branches as $branchData) {
            Branch::updateOrCreate(
                ['tenant_id' => $tenant->id, 'name' => $branchData['name']],
                array_merge($branchData, [
                    'tenant_id'        => $tenant->id,
                    'intake_capacity'  => 60,
                    'tuition_fee_govt' => 15000,
                    'is_active'        => true,
                ])
            );
        }

        // ── Fee Types ─────────────────────────────────────────────────────────
        $feeTypes = [
            ['code' => 'UNIFORM',    'name' => 'Uniform Fee',    'frequency' => 'one_time',    'amount' => 1500,  'applicable_all_streams' => true,  'applicable_all_branches' => true,  'can_be_exempted' => true,  'sort_order' => 1],
            ['code' => 'EXAM',       'name' => 'Exam Fee',       'frequency' => 'per_semester','amount' => 800,   'applicable_all_streams' => true,  'applicable_all_branches' => true,  'can_be_exempted' => true,  'sort_order' => 2],
            ['code' => 'UDF',        'name' => 'UDF Fee',        'frequency' => 'per_year',    'amount' => 500,   'applicable_all_streams' => true,  'applicable_all_branches' => true,  'can_be_exempted' => true,  'sort_order' => 3],
            ['code' => 'RECORD',     'name' => 'Record Fee',     'frequency' => 'per_semester','amount' => 300,   'applicable_all_streams' => false, 'applicable_all_branches' => false, 'can_be_exempted' => true,  'sort_order' => 4],
            ['code' => 'VEHICLE',    'name' => 'Vehicle Fee',    'frequency' => 'monthly',     'amount' => 600,   'applicable_all_streams' => true,  'applicable_all_branches' => true,  'can_be_exempted' => true,  'sort_order' => 5],
            ['code' => 'TUITION',    'name' => 'Tuition Fee',    'frequency' => 'per_year',    'amount' => 0,     'applicable_all_streams' => true,  'applicable_all_branches' => true,  'can_be_exempted' => true,  'sort_order' => 6],
            ['code' => 'OTHER',      'name' => 'Other Fee',      'frequency' => 'one_time',    'amount' => 0,     'applicable_all_streams' => true,  'applicable_all_branches' => true,  'can_be_exempted' => true,  'sort_order' => 7],
            ['code' => 'INTERNSHIP', 'name' => 'Internship Fee', 'frequency' => 'one_time',    'amount' => 1000,  'applicable_all_streams' => true,  'applicable_all_branches' => true,  'can_be_exempted' => true,  'sort_order' => 8],
        ];

        foreach ($feeTypes as $ft) {
            FeeType::updateOrCreate(
                ['code' => $ft['code']],
                array_merge($ft, ['tenant_id' => $tenant->id, 'is_active' => true])
            );
        }

        // ── Users ─────────────────────────────────────────────────────────────
        $superAdminRole  = Role::where('name', 'super_admin')->first();
        $collegeAdminRole = Role::where('name', 'college_admin')->first();
        $staffRole       = Role::where('name', 'staff')->first();

        // Super Admin (no tenant)
        User::updateOrCreate(
            ['email' => 'superadmin@edutenant.com'],
            [
                'name'           => 'Super Admin',
                'email'          => 'superadmin@edutenant.com',
                'password'       => Hash::make('password'),
                'role_id'        => $superAdminRole?->id,
                'is_super_admin' => true,
                'status'         => 'active',
            ]
        );

        // College Admin
        User::updateOrCreate(
            ['email' => 'admin@svdcollege.edu'],
            [
                'name'      => 'College Admin',
                'email'     => 'admin@svdcollege.edu',
                'password'  => Hash::make('password'),
                'tenant_id' => $tenant->id,
                'role_id'   => $collegeAdminRole?->id,
                'status'    => 'active',
            ]
        );

        // Staff User
        User::updateOrCreate(
            ['email' => 'staff@svdcollege.edu'],
            [
                'name'      => 'Admission Staff',
                'email'     => 'staff@svdcollege.edu',
                'password'  => Hash::make('password'),
                'tenant_id' => $tenant->id,
                'role_id'   => $staffRole?->id,
                'status'    => 'active',
            ]
        );

        $this->command->info('✅ Tenant, streams, courses, branches, fee types, and users seeded.');
        $this->command->info('   Super Admin  → superadmin@edutenant.com / password');
        $this->command->info('   College Admin → admin@svdcollege.edu / password');
        $this->command->info('   Staff         → staff@svdcollege.edu / password');
    }
}
