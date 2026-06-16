<?php

namespace Tests\Browser\Student;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Test Case: Student Dashboard
 *
 * Purpose   : Verify the student portal dashboard loads with all key sections:
 *             fee summary, attendance summary, personal info.
 * Module    : Student Dashboard
 * Actor     : Student — ravi.teja@svc.student.edu / 81svc0001
 * Route     : GET /student/dashboard
 */
class StudentDashboardTest extends DuskTestCase
{
    /**
     * TC-18: Student dashboard page loads after login.
     */
    public function test_student_dashboard_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsStudent($browser);

            $browser->assertPathIs('/student/dashboard')
                    ->assertSee('Dashboard')
                    ->screenshot('tc18_student_dashboard_loaded');
        });
    }

    /**
     * TC-19: Dashboard shows attendance section.
     */
    public function test_student_dashboard_shows_attendance_section(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsStudent($browser);

            $browser->visit('http://13.62.14.215/student/dashboard')
                    ->assertSee('Attendance')
                    ->screenshot('tc19_attendance_section_visible');
        });
    }

    /**
     * TC-20: Dashboard shows fee section.
     */
    public function test_student_dashboard_shows_fee_section(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsStudent($browser);

            $browser->visit('http://13.62.14.215/student/dashboard')
                    ->assertSee('Fee')
                    ->screenshot('tc20_fee_section_visible');
        });
    }

    /**
     * TC-21: Dashboard navigation links exist (Profile, Fees, Attendance).
     */
    public function test_student_dashboard_navigation_links_present(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsStudent($browser);

            $browser->visit('http://13.62.14.215/student/dashboard')
                    ->assertPresent('a[href*="student/profile"], a[href*="student/fees"], a[href*="student/attendance"]')
                    ->screenshot('tc21_nav_links_present');
        });
    }

    /**
     * TC-22: Unauthenticated access to student dashboard redirects to login.
     */
    public function test_unauthenticated_user_redirected_from_student_dashboard(): void
    {
        $this->browse(function (Browser $browser) {
            // Ensure no session exists
            $browser->driver->manage()->deleteAllCookies();

            $browser->visit('http://13.62.14.215/student/dashboard')
                    ->waitForLocation('/login', 15)
                    ->assertPathIs('/login')
                    ->screenshot('tc22_redirect_to_login');
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
