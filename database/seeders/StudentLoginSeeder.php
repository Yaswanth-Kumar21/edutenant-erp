<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Student;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * StudentLoginSeeder
 *
 * Creates login User accounts for all existing seeded students
 * who have an email address but no user_id linked yet.
 *
 * Default password for all students = their phone number.
 * If no phone, default password = their admission number.
 */
class StudentLoginSeeder extends Seeder
{
    public function run(): void
    {
        $studentRole = Role::where('name', 'student')->first();

        if (!$studentRole) {
            $this->command->error('Student role not found. Run RoleSeeder first.');
            return;
        }

        // ── Define students with explicit login credentials ────────────────────
        // These match the students seeded by DummyDataSeeder and SriVenkateswaraDataSeeder
        $studentLogins = [

            // ── Sri Venkateswara College (demo-college) ───────────────────────
            [
                'phone'      => '8100000001',
                'email'      => 'ravi.teja@svdstudent.edu',
                'name'       => 'Ravi Teja',
                'password'   => '8100000001',   // phone number
                'tenant_slug'=> 'demo-college',
            ],
            [
                'phone'      => '8100000002',
                'email'      => 'sravani.reddy@svdstudent.edu',
                'name'       => 'Sravani Reddy',
                'password'   => '8100000002',
                'tenant_slug'=> 'demo-college',
            ],
            [
                'phone'      => '8100000003',
                'email'      => 'mahesh.babu@svdstudent.edu',
                'name'       => 'Mahesh Babu',
                'password'   => '8100000003',
                'tenant_slug'=> 'demo-college',
            ],
            [
                'phone'      => '8100000004',
                'email'      => 'pooja.sharma@svdstudent.edu',
                'name'       => 'Pooja Sharma',
                'password'   => '8100000004',
                'tenant_slug'=> 'demo-college',
            ],
            [
                'phone'      => '8100000005',
                'email'      => 'aakash.kumar@svdstudent.edu',
                'name'       => 'Aakash Kumar',
                'password'   => '8100000005',
                'tenant_slug'=> 'demo-college',
            ],

            // ── Nagarjuna College ─────────────────────────────────────────────
            [
                'phone'      => '9000000001',
                'email'      => 'arjun.sharma@student.edu',
                'name'       => 'Arjun Sharma',
                'password'   => '9000000001',
                'tenant_slug'=> 'nagarjuna-college',
            ],
            [
                'phone'      => '9000000002',
                'email'      => 'priya.reddy@student.edu',
                'name'       => 'Priya Reddy',
                'password'   => '9000000002',
                'tenant_slug'=> 'nagarjuna-college',
            ],
            [
                'phone'      => '9000000003',
                'email'      => 'kiran.kumar@student.edu',
                'name'       => 'Kiran Kumar',
                'password'   => '9000000003',
                'tenant_slug'=> 'nagarjuna-college',
            ],

            // ── Krishna College ───────────────────────────────────────────────
            [
                'phone'      => '9000000001',  // same phone, different tenant
                'email'      => 'arjun.sharma@krishnastudent.edu',
                'name'       => 'Arjun Sharma',
                'password'   => '9000000001',
                'tenant_slug'=> 'krishna-college',
            ],
            [
                'phone'      => '9000000002',
                'email'      => 'priya.reddy@krishnastudent.edu',
                'name'       => 'Priya Reddy',
                'password'   => '9000000002',
                'tenant_slug'=> 'krishna-college',
            ],
        ];

        $created = 0;
        $skipped = 0;

        foreach ($studentLogins as $loginData) {
            $tenant = Tenant::where('slug', $loginData['tenant_slug'])->first();
            if (!$tenant) {
                $this->command->warn("Tenant [{$loginData['tenant_slug']}] not found — skipping {$loginData['name']}");
                continue;
            }

            // Find the student by phone + tenant
            $student = Student::where('tenant_id', $tenant->id)
                ->where('phone', $loginData['phone'])
                ->first();

            if (!$student) {
                $this->command->warn("Student [{$loginData['name']}] not found in [{$loginData['tenant_slug']}] — skipping");
                continue;
            }

            // Skip if already has a login
            if ($student->user_id) {
                $skipped++;
                continue;
            }

            // Skip if email already taken
            if (User::where('email', $loginData['email'])->exists()) {
                // Just link the existing user
                $existing = User::where('email', $loginData['email'])->first();
                $student->update(['user_id' => $existing->id]);
                $skipped++;
                continue;
            }

            // Create the user account
            $user = User::create([
                'tenant_id' => $tenant->id,
                'role_id'   => $studentRole->id,
                'name'      => $loginData['name'],
                'email'     => $loginData['email'],
                'phone'     => $loginData['phone'],
                'password'  => Hash::make($loginData['password']),
                'status'    => 'active',
            ]);

            // Link user to student
            $student->update(['user_id' => $user->id]);
            $created++;
        }

        $this->command->info("✅ Student login accounts created: {$created} new, {$skipped} skipped.");
        $this->command->newLine();
        $this->command->info('─────────────────────────────────────────────────────────────');
        $this->command->info('STUDENT LOGIN CREDENTIALS');
        $this->command->info('─────────────────────────────────────────────────────────────');
        $this->command->info('Sri Venkateswara College:');
        $this->command->info('  ravi.teja@svdstudent.edu          / 8100000001');
        $this->command->info('  sravani.reddy@svdstudent.edu      / 8100000002');
        $this->command->info('  mahesh.babu@svdstudent.edu        / 8100000003');
        $this->command->info('  pooja.sharma@svdstudent.edu       / 8100000004');
        $this->command->info('  aakash.kumar@svdstudent.edu       / 8100000005');
        $this->command->info('Nagarjuna College:');
        $this->command->info('  arjun.sharma@student.edu          / 9000000001');
        $this->command->info('  priya.reddy@student.edu           / 9000000002');
        $this->command->info('  kiran.kumar@student.edu           / 9000000003');
        $this->command->info('─────────────────────────────────────────────────────────────');
    }
}
