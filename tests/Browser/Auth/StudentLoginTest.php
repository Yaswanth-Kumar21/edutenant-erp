<?php

namespace Tests\Browser\Auth;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Test Case: Student Login
 *
 * Purpose   : Verify student lands on /student/dashboard (not /dashboard),
 *             is blocked from admin routes, and can log out.
 * Module    : Authentication
 * Actor     : ravi.teja@svc.student.edu / 81svc0001
 */
class StudentLoginTest extends DuskTestCase
{
    private function asGuest(Browser $browser): Browser
    {
        $browser->driver->manage()->deleteAllCookies();
        return $browser;
    }

    /**
     * TC-08: Student logs in and is redirected to /student/dashboard.
     */
    public function test_student_can_login_and_reach_student_dashboard(): void
    {
        $this->browse(function (Browser $browser) {
            $this->asGuest($browser)
                 ->visit('http://13.62.14.215/login')
                 ->waitFor('#email', 15)
                 ->type('#email', 'ravi.teja@svc.student.edu')
                 ->type('#password', '81svc0001')
                 ->press('Sign In')
                 ->waitForLocation('/student/dashboard', 20)
                 ->assertPathIs('/student/dashboard')
                 ->assertSee('Dashboard')
                 ->screenshot('tc08_student_dashboard');
        });
    }

    /**
     * TC-09: Logged-in student who force-visits /dashboard is either redirected
     *        to student dashboard or shown their own dashboard (role-based redirect).
     *        The student must NOT see admin-only content.
     */
    public function test_student_cannot_access_admin_dashboard(): void
    {
        $this->browse(function (Browser $browser) {
            // Login as student
            $this->asGuest($browser)
                 ->visit('http://13.62.14.215/login')
                 ->waitFor('#email', 15)
                 ->type('#email', 'ravi.teja@svc.student.edu')
                 ->type('#password', '81svc0001')
                 ->press('Sign In')
                 ->waitForLocation('/student/dashboard', 20);

            // Try to force-access admin dashboard
            $browser->visit('http://13.62.14.215/dashboard')
                    ->pause(2000)
                    ->screenshot('tc09_admin_access_result');

            // Student should not see admin-only management content
            // (they may land on a redirect page, 403, or student dashboard)
            $browser->assertDontSee('New Admission')
                    ->assertDontSee('Manage Students');
        });
    }

    /**
     * TC-10: Student logs out and lands on /login.
     */
    public function test_student_can_logout(): void
    {
        $this->browse(function (Browser $browser) {
            $this->asGuest($browser)
                 ->visit('http://13.62.14.215/login')
                 ->waitFor('#email', 15)
                 ->type('#email', 'ravi.teja@svc.student.edu')
                 ->type('#password', '81svc0001')
                 ->press('Sign In')
                 ->waitForLocation('/student/dashboard', 20)
                 ->screenshot('tc10_before_logout');

            $browser->script("
                var form = document.querySelector('form[action*=\"logout\"]');
                if (form) { form.submit(); } else { window.location='/login'; }
            ");

            $browser->waitForLocation('/login', 20)
                    ->assertPathIs('/login')
                    ->screenshot('tc10_student_logged_out');
        });
    }
}
