<?php

namespace Database\Seeders;

use App\Models\StaffRole;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class StaffRoleSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all();

        $roles = [
            ['name' => 'Principal',        'department' => 'Administration', 'staff_type' => 'both'],
            ['name' => 'HOD',              'department' => 'Academic',        'staff_type' => 'teaching'],
            ['name' => 'Professor',        'department' => 'Academic',        'staff_type' => 'teaching'],
            ['name' => 'Associate Professor','department'=> 'Academic',       'staff_type' => 'teaching'],
            ['name' => 'Assistant Professor','department'=> 'Academic',       'staff_type' => 'teaching'],
            ['name' => 'Lecturer',         'department' => 'Academic',        'staff_type' => 'teaching'],
            ['name' => 'Lab Assistant',    'department' => 'Science',         'staff_type' => 'non_teaching'],
            ['name' => 'Office Staff',     'department' => 'Administration',  'staff_type' => 'non_teaching'],
            ['name' => 'Accountant',       'department' => 'Finance',         'staff_type' => 'non_teaching'],
            ['name' => 'Librarian',        'department' => 'Library',         'staff_type' => 'non_teaching'],
            ['name' => 'Peon',             'department' => 'Support',         'staff_type' => 'non_teaching'],
            ['name' => 'Security',         'department' => 'Support',         'staff_type' => 'non_teaching'],
        ];

        foreach ($tenants as $tenant) {
            foreach ($roles as $role) {
                StaffRole::updateOrCreate(
                    ['tenant_id' => $tenant->id, 'name' => $role['name']],
                    array_merge($role, ['tenant_id' => $tenant->id, 'is_active' => true])
                );
            }
        }

        $this->command->info('✅ Staff roles seeded.');
    }
}
