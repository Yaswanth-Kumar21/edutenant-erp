<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name'         => 'super_admin',
                'display_name' => 'Super Admin',
                'description'  => 'Full system access across all tenants',
                'is_system'    => true,
                'permissions'  => ['*'], // All permissions
            ],
            [
                'name'         => 'college_admin',
                'display_name' => 'College Admin',
                'description'  => 'Full access within their college tenant',
                'is_system'    => true,
                'permissions'  => [
                    'manage_students', 'manage_staff', 'manage_fees',
                    'manage_attendance', 'manage_reports', 'manage_settings',
                    'send_messages', 'manage_expenses',
                ],
            ],
            [
                'name'         => 'staff',
                'display_name' => 'Staff',
                'description'  => 'Administrative staff with limited access',
                'is_system'    => true,
                'permissions'  => [
                    'manage_students', 'collect_fees', 'view_reports',
                    'mark_attendance', 'send_messages',
                ],
            ],
            [
                'name'         => 'teacher',
                'display_name' => 'Teacher',
                'description'  => 'Teaching staff - attendance and student view',
                'is_system'    => true,
                'permissions'  => [
                    'mark_attendance', 'view_students', 'send_messages',
                ],
            ],
            [
                'name'         => 'student',
                'display_name' => 'Student',
                'description'  => 'Student portal access',
                'is_system'    => true,
                'permissions'  => [
                    'view_own_profile', 'view_own_fees', 'view_own_attendance',
                ],
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['name' => $role['name']], $role);
        }

        $this->command->info('✅ Roles seeded successfully.');
    }
}
