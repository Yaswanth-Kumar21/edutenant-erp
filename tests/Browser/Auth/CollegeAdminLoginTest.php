<?php

namespace Tests\Browser\Auth;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Test Case: College Admin Login
 *
 * Purpose   : Verify both college admins can log in and access the dashboard.
 * Module    : Authentication
 * Actors    : admin@svc.edu / password, admin@scc.edu / password
 */
class CollegeAdminLoginTest extends DuskTestCase
{
    private function asGuest(Browser $browser): Browser
    {
        $browser->driver->manage()->deleteAllCookies();
        return $browser;
    }

    /**
     * TC-05: SVC College Admin logs in successfully.
     */
    public function test_college_admin_svc_can_login(): void
    {
        $this->browse(function (Browser $browser) {
            $this->asGuest($browser)
                 ->visit('http://13.62.14.215/login')
                 ->waitFor('#email', 15)
                 ->type('#email', 'admin@svc.edu')
                 ->type('#password', 'password')
                 ->press('Sign In')
                 ->waitForLocation('/dashboard', 20)
                 ->assertPathIs('/dashboard')
                 ->screenshot('tc05_svc_admin_dashboard');
        });
    }

    /**
     * TC-06: SCC College Admin (second tenant) logs in successfully.
     */
    public function test_college_admin_scc_can_login(): void
    {
        $this->browse(function (Browser $browser) {
            $this->asGuest($browser)
                 ->visit('http://13.62.14.215/login')
                 ->waitFor('#email', 15)
                 ->type('#email', 'admin@scc.edu')
                 ->type('#password', 'password')
                 ->press('Sign In')
                 ->waitForLocation('/dashboard', 20)
                 ->assertPathIs('/dashboard')
                 ->screenshot('tc06_scc_admin_dashboard');
        });
    }

    /**
     * TC-07: College Admin logs out and returns to /login.
     */
    public function test_college_admin_can_logout(): void
    {
        $this->browse(function (Browser $browser) {
            $this->asGuest($browser)
                 ->visit('http://13.62.14.215/login')
                 ->waitFor('#email', 15)
                 ->type('#email', 'admin@svc.edu')
                 ->type('#password', 'password')
                 ->press('Sign In')
                 ->waitForLocation('/dashboard', 20)
                 ->screenshot('tc07_before_logout');

            $browser->script("
                var form = document.querySelector('form[action*=\"logout\"]');
                if (form) { form.submit(); } else { window.location='/login'; }
            ");

            $browser->waitForLocation('/login', 20)
                    ->assertPathIs('/login')
                    ->screenshot('tc07_admin_logged_out');
        });
    }
}
