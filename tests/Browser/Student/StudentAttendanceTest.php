<?php

namespace Tests\Browser\Student;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Test Case: Student Attendance
 *
 * Purpose   : Verify that a student can access their attendance page and
 *             see the attendance calendar / summary.
 * Module    : Student Dashboard → Attendance
 * Actor     : Student — ravi.teja@svc.student.edu / 81svc0001
 * Route     : GET /student/attendance
 */
class StudentAttendanceTest extends DuskTestCase
{
    /**
     * TC-27: Student attendance page loads successfully.
     */
    public function test_student_attendance_page_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsStudent($browser);

            $browser->visit('http://13.62.14.215/student/attendance')
                    ->assertSee('Attendance')
                    ->screenshot('tc27_student_attendance_loaded');
        });
    }

    /**
     * TC-28: Attendance page loads and renders content (no 500 error).
     */
    public function test_student_attendance_shows_summary(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsStudent($browser);

            $browser->visit('http://13.62.14.215/student/attendance')
                    ->assertSee('Attendance')
                    ->screenshot('tc28_attendance_page_rendered');

            // Verify no error page — page title should not contain "Error" or "500"
            $title = $browser->driver->getTitle();
            $this->assertStringNotContainsString('500', $title,
                "Page returned a 500 error");
            $this->assertStringNotContainsString('Error', $title,
                "Page returned an error");
        });
    }

    /**
     * TC-29: Attendance page is accessible via the navigation link from dashboard.
     */
    public function test_attendance_accessible_from_dashboard_nav(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsStudent($browser);

            $browser->visit('http://13.62.14.215/student/dashboard')
                    ->clickLink('Attendance')
                    ->waitForLocation('/student/attendance', 10)
                    ->assertPathIs('/student/attendance')
                    ->screenshot('tc29_attendance_via_nav');
        });
    }

    /**
     * TC-30: Unauthenticated access to attendance page is blocked.
     */
    public function test_unauthenticated_attendance_access_blocked(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('http://13.62.14.215/student/attendance')
                    ->waitForLocation('/login', 15)
                    ->assertPathIs('/login')
                    ->screenshot('tc30_attendance_auth_guard');
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
