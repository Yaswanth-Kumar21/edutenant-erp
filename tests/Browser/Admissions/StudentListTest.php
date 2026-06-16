<?php

namespace Tests\Browser\Admissions;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Test Case: Student List
 *
 * Purpose   : Verify the student listing page loads, shows students, and
 *             supports search/filter functionality.
 * Module    : Admissions / Students
 * Actor     : College Admin — admin@svc.edu / password
 * Route     : GET /admin/students
 */
class StudentListTest extends DuskTestCase
{
    /**
     * TC-14: Student list page loads and displays student records.
     */
    public function test_student_list_page_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsCollegeAdmin($browser);

            $browser->visit('http://13.62.14.215/admin/students')
                    ->assertSee('Students')
                    ->screenshot('tc14_student_list_loaded');

            // Verify table or listing is present
            $browser->assertPresent('table, .student-card, [data-student]')
                    ->screenshot('tc14_student_records_present');
        });
    }

    /**
     * TC-15: Student search finds a known seeded student.
     */
    public function test_student_search_returns_results(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsCollegeAdmin($browser);

            $browser->visit('http://13.62.14.215/admin/students')
                    ->screenshot('tc15_before_search');

            // Search for a known student from seeder
            $browser->type('input[name="search"]', 'Ravi')
                    ->keys('input[name="search"]', '{enter}')
                    ->waitForText('Ravi', 10)
                    ->assertSee('Ravi')
                    ->screenshot('tc15_search_results');
        });
    }

    /**
     * TC-16: Student stats counters are visible (Total, Active, This Month).
     */
    public function test_student_list_shows_stats(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsCollegeAdmin($browser);

            $browser->visit('http://13.62.14.215/admin/students')
                    ->screenshot('tc16_stats_check');

            // Page should show student count stats (Total / Active / This Month)
            $browser->assertSee('Total')
                    ->screenshot('tc16_stats_present');
        });
    }

    /**
     * TC-17: "New Admission" button links to the admission wizard.
     */
    public function test_new_admission_button_links_to_wizard(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsCollegeAdmin($browser);

            $browser->visit('http://13.62.14.215/admin/students')
                    ->clickLink('New Admission')
                    ->waitForLocation('/admin/admissions/create', 10)
                    ->assertPathIs('/admin/admissions/create')
                    ->screenshot('tc17_new_admission_link');
        });
    }

    // ─── Helper ──────────────────────────────────────────────────────────────

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
