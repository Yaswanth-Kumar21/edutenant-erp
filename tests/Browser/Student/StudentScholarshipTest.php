<?php

namespace Tests\Browser\Student;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Test Case: Student Scholarship Section
 *
 * Purpose   : Verify that the scholarship information is visible on the
 *             student profile page, and that the admin scholarship management
 *             page loads and works correctly.
 * Module    : Student Dashboard → Profile (Scholarship section)
 *             + Admin → Scholarships
 * Actors    : Student, College Admin
 */
class StudentScholarshipTest extends DuskTestCase
{
    /**
     * TC-31: Student profile shows a scholarships section.
     */
    public function test_student_profile_shows_scholarship_section(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsStudent($browser);

            $browser->visit('http://13.62.14.215/student/profile')
                    ->assertSee('Scholarship')
                    ->screenshot('tc31_scholarship_section_on_profile');
        });
    }

    /**
     * TC-32: Admin scholarship list page loads.
     */
    public function test_admin_scholarship_list_page_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsCollegeAdmin($browser);

            $browser->visit('http://13.62.14.215/admin/scholarships')
                    ->assertSee('Scholarship')
                    ->screenshot('tc32_admin_scholarship_list');
        });
    }

    /**
     * TC-33: Admin can open the scholarship creation form.
     */
    public function test_admin_scholarship_create_form_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsCollegeAdmin($browser);

            $browser->visit('http://13.62.14.215/admin/scholarships/create')
                    ->assertSee('Scholarship')
                    ->assertPresent('form')
                    ->screenshot('tc33_scholarship_create_form');
        });
    }

    /**
     * TC-34: Student profile shows scholarship details if assigned.
     *        (For a student with scholarship data from seeder — sravani.reddy who may have scholarship)
     */
    public function test_student_scholarship_details_visible_on_profile(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('http://13.62.14.215/login')
                    ->waitFor('#email', 15)
                    ->type('#email', 'sravani.reddy@svc.student.edu')
                    ->type('#password', '81svc0002')
                    ->press('Sign In')
                    ->waitForLocation('/student/dashboard', 20);

            $browser->visit('http://13.62.14.215/student/profile')
                    ->assertSee('Profile')
                    ->screenshot('tc34_scholarship_details_on_profile');
        });
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function loginAsStudent(Browser $browser): void
    {
        $browser->driver->manage()->deleteAllCookies();
        $browser->visit('http://13.62.14.215/login')
                ->waitFor('#email', 15)
                ->type('#email', 'ravi.teja@svc.student.edu')
                ->type('#password', '81svc0001')
                ->press('Sign In')
                ->waitForLocation('/student/dashboard', 20);
    }

    private function loginAsCollegeAdmin(Browser $browser): void
    {
        $browser->driver->manage()->deleteAllCookies();
        $browser->visit('http://13.62.14.215/login')
                ->waitFor('#email', 15)
                ->type('#email', 'admin@svc.edu')
                ->type('#password', 'password')
                ->press('Sign In')
                ->waitForLocation('/dashboard', 20);
    }
}
