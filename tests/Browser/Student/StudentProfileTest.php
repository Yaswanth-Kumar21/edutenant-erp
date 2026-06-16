<?php

namespace Tests\Browser\Student;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Test Case: Student Profile
 *
 * Purpose   : Verify that a student can access their profile page and view
 *             personal information, academic details, and guardian info.
 * Module    : Student Dashboard → Profile
 * Actor     : Student — ravi.teja@svc.student.edu / 81svc0001
 * Route     : GET /student/profile
 */
class StudentProfileTest extends DuskTestCase
{
    /**
     * TC-23: Student profile page loads successfully.
     */
    public function test_student_profile_page_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsStudent($browser);

            $browser->visit('http://13.62.14.215/student/profile')
                    ->assertSee('Profile')
                    ->screenshot('tc23_student_profile_loaded');
        });
    }

    /**
     * TC-24: Student profile shows student's name.
     */
    public function test_student_profile_shows_student_name(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsStudent($browser);

            $browser->visit('http://13.62.14.215/student/profile')
                    ->assertSee('Ravi')          // From seeder: ravi.teja
                    ->screenshot('tc24_student_name_on_profile');
        });
    }

    /**
     * TC-25: Profile page shows academic details section (course/stream info).
     */
    public function test_student_profile_shows_academic_details(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsStudent($browser);

            $browser->visit('http://13.62.14.215/student/profile')
                    ->assertSee('Admission')   // Admission number is always shown
                    ->screenshot('tc25_academic_details_visible');
        });
    }

    /**
     * TC-26: Password change page is accessible from profile.
     */
    public function test_student_password_change_page_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsStudent($browser);

            $browser->visit('http://13.62.14.215/student/profile/password')
                    ->assertSee('Password')
                    ->assertPresent('input[name="current_password"]')
                    ->assertPresent('input[name="password"]')
                    ->assertPresent('input[name="password_confirmation"]')
                    ->screenshot('tc26_password_change_page');
        });
    }

    // ─── Helper ──────────────────────────────────────────────────────────────

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
}
