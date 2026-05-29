<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all();

        $categories = [
            ['code' => 'GEN',   'name' => 'General',                  'sort_order' => 1],
            ['code' => 'OBC',   'name' => 'Other Backward Class',      'sort_order' => 2],
            ['code' => 'SC',    'name' => 'Scheduled Caste',           'sort_order' => 3],
            ['code' => 'ST',    'name' => 'Scheduled Tribe',           'sort_order' => 4],
            ['code' => 'EWS',   'name' => 'Economically Weaker Section','sort_order' => 5],
            ['code' => 'OTHER', 'name' => 'Other',                     'sort_order' => 6],
        ];

        foreach ($tenants as $tenant) {
            foreach ($categories as $cat) {
                Category::updateOrCreate(
                    ['tenant_id' => $tenant->id, 'code' => $cat['code']],
                    array_merge($cat, ['tenant_id' => $tenant->id, 'is_active' => true])
                );
            }
        }

        $this->command->info('✅ Categories seeded for all tenants.');
    }
}
