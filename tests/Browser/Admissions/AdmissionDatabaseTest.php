<?php

namespace Tests\Browser\Admissions;

use App\Models\AdmissionReceipt;
use App\Models\GuardianDetail;
use App\Models\Student;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * PHASE 4 — Database Verification: Admission
 *
 * Purpose   : After submitting the admission wizard via the browser,
 *             verify that all expected database records were created:
 *               ✔ students table — core student row
 *               ✔ student_profiles table — profile row
 *               ✔ guardian_details table — guardian row
 *               ✔ admission_receipts table — receipt row
 *               ✔ users table — login account
 * Module    : Admissions
 * Actor     : College Admin — admin@svc.edu / password
 */
class AdmissionDatabaseTest extends DuskTestCase
{
    /**
     * TC-DB-01: Full admission flow creates all 5 expected database records.
     */
    public function test_admission_creates_all_database_records(): void
    {
        $uniqueSuffix = 'db' . substr(uniqid(), -6);
        $firstName    = 'DBTest';
        $lastName     = 'Verify' . $uniqueSuffix;
        $phone        = '9' . rand(100000000, 999999999);
        $email        = "dbtest.{$uniqueSuffix}@dusk.test";

        // ── Browser: Submit the admission form ────────────────────────────
        $this->browse(function (Browser $browser) use ($firstName, $lastName, $phone, $email) {
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('http://13.62.14.215/login')
                    ->waitFor('#email', 15)
                    ->type('#email', 'admin@svc.edu')
                    ->type('#password', 'password')
                    ->press('Sign In')
                    ->waitForLocation('/dashboard', 20);

            $browser->visit('http://13.62.14.215/admin/admissions/create');

            // Step 1 — Personal
            $browser->type('input[name="first_name"]',   $firstName)
                    ->type('input[name="last_name"]',    $lastName)
                    ->select('select[name="gender"]',    'male')
                    ->type('input[name="phone"]',        $phone)
                    ->type('input[name="email"]',        $email)
                    ->type('input[name="date_of_birth"]', '2001-03-10')
                    ->type('input[name="father_name"]',  '')
                    ->click('button[onclick="nextStep(1)"]')
                    ->pause(600);

            // Step 2 — Academic (auto-select first branch and year)
            $browser->script("
                var b = document.querySelector('select[name=\"branch_id\"]');
                if(b && b.options.length > 1){ b.selectedIndex=1; b.dispatchEvent(new Event('change')); }
                var y = document.querySelector('select[name=\"academic_year_id\"]');
                if(y && y.options.length > 1){ y.selectedIndex=1; y.dispatchEvent(new Event('change')); }
            ");
            $browser->click('button[onclick="nextStep(2)"]')
                    ->pause(600);

            // Step 3 — Category
            $browser->type('input[name="admission_date"]', now()->format('Y-m-d'))
                    ->click('button[onclick="nextStep(3)"]')
                    ->pause(600);

            // Step 4 — Guardian
            $browser->type('input[name="father_name"]', 'DB Father Test')
                    ->click('button[onclick="nextStep(4)"]')
                    ->pause(600);

            // Step 5 — Skip scholarships
            $browser->click('button[onclick="nextStep(5)"]')->pause(600);

            // Step 6 — Skip documents
            $browser->click('button[onclick="nextStep(6)"]')->pause(600);

            // Step 7 — Receipt
            $browser->type('input[name="admission_fee"]', '200')
                    ->type('input[name="amount_paid"]',   '200')
                    ->press('button[type="submit"]')
                    ->waitForText('admitted successfully', 20)
                    ->screenshot('tc_db01_admission_success_page');
        });

        // ── Database Assertions ───────────────────────────────────────────

        // 1. Student record created
        $student = Student::where('first_name', $firstName)
                          ->where('last_name', $lastName)
                          ->where('phone', $phone)
                          ->first();

        $this->assertNotNull($student,
            "FAIL: No student record found in `students` table for {$firstName} {$lastName}");
        $this->assertEquals('active', $student->status,
            "FAIL: Student status should be 'active'");
        $this->assertNotNull($student->admission_number,
            "FAIL: Admission number was not generated");
        $this->assertStringStartsWith('EDU-', $student->admission_number,
            "FAIL: Admission number format wrong — expected EDU-YYYY-NNNN");

        // 2. StudentProfile record created
        $profile = StudentProfile::where('student_id', $student->id)->first();
        $this->assertNotNull($profile,
            "FAIL: No student_profile record found for student_id={$student->id}");

        // 3. GuardianDetail record created
        $guardian = GuardianDetail::where('student_id', $student->id)->first();
        $this->assertNotNull($guardian,
            "FAIL: No guardian_details record found for student_id={$student->id}");
        $this->assertEquals('DB Father Test', $guardian->father_name,
            "FAIL: Guardian father_name mismatch");

        // 4. AdmissionReceipt created
        $receipt = AdmissionReceipt::where('student_id', $student->id)->first();
        $this->assertNotNull($receipt,
            "FAIL: No admission_receipt record found for student_id={$student->id}");
        $this->assertStringStartsWith('ADM-RCPT-', $receipt->receipt_number,
            "FAIL: Receipt number format wrong");
        $this->assertEquals(200.00, (float) $receipt->amount_paid,
            "FAIL: Receipt amount_paid mismatch");

        // 5. User login account created (only if email provided)
        $user = User::where('email', $email)->first();
        $this->assertNotNull($user,
            "FAIL: No user login account created for email={$email}");
        $this->assertEquals($student->tenant_id, $user->tenant_id,
            "FAIL: User tenant_id does not match student tenant_id");
    }

    /**
     * TC-DB-02: Seeded students exist in the database.
     */
    public function test_seeded_students_exist_in_database(): void
    {
        // Verify known seeded data
        $this->assertDatabaseHas('students', [
            'email' => 'ravi.teja@svc.student.edu',
        ]);
        $this->assertDatabaseHas('students', [
            'email' => 'sravani.reddy@svc.student.edu',
        ]);
        $this->assertDatabaseHas('users', [
            'email' => 'superadmin@erp.com',
        ]);
        $this->assertDatabaseHas('users', [
            'email' => 'admin@svc.edu',
        ]);
        $this->assertDatabaseHas('users', [
            'email' => 'admin@scc.edu',
        ]);
    }

    /**
     * TC-DB-03: Verify tenant isolation — SVC students are not visible to SCC tenant queries.
     */
    public function test_tenant_isolation_between_colleges(): void
    {
        $svcStudent = Student::where('email', 'ravi.teja@svc.student.edu')->first();
        $sccStudent = Student::where('email', 'ravi.teja@scc.student.edu')->first();

        $this->assertNotNull($svcStudent, "SVC student not found");
        $this->assertNotNull($sccStudent, "SCC student not found");

        // Tenant IDs must differ
        $this->assertNotEquals(
            $svcStudent->tenant_id,
            $sccStudent->tenant_id,
            "FAIL: SVC and SCC students should have different tenant IDs"
        );
    }
}
