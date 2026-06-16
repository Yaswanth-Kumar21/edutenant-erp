# Laravel Dusk — Internship Learning Notes

**Project:** EduTenant ERP  
**Intern:** Yaswanth Kumar  
**Module Assigned:** Admissions & Student Dashboard  
**Date:** June 2026

---

## 1. What is Testing?

Testing is the process of verifying that software behaves as expected. It involves
running a program with specific inputs and comparing the actual output against the
expected output.

Testing answers three questions:
- Does the feature work correctly?
- Does it break when given invalid input?
- Does it continue working after new code is added?

### Types of Testing

| Type | Description | Example |
|---|---|---|
| Unit | Tests a single function or class in isolation | `AdmissionService::generateAdmissionNumber()` returns correct format |
| Integration | Tests multiple classes working together | Controller + Service + Model interaction |
| Feature | Tests an HTTP request-response cycle | POST /admin/admissions returns 302 redirect |
| End-to-End | Tests the full flow through a real browser | Fill form → Submit → See receipt page |

---

## 2. What is End-to-End (E2E) Testing?

End-to-End testing simulates real user behaviour through a real browser. It tests
the entire application stack — from the frontend UI down to the database — in one
complete flow.

### Why E2E Testing Matters

- Catches bugs that unit/feature tests miss (JavaScript issues, CSS hiding elements, modal problems)
- Validates the actual user experience, not just the API response
- Confirms that all layers (View → Controller → Service → DB) work together correctly
- Can run as a regression suite before every deployment

### Example E2E Flow (Admission)

```
Browser opens /login
→ Types admin@svc.edu / password
→ Clicks Sign In
→ Browser navigates to /dashboard  [Auth verified]
→ Browser opens /admin/admissions/create
→ Fills 7-step wizard (Personal, Academic, Category, Guardian, Scholarship, Documents, Receipt)
→ Clicks Submit
→ Browser sees "admitted successfully" and the receipt page  [Feature verified]
→ Database check: Student, Profile, Guardian, Receipt, User records created  [DB verified]
```

---

## 3. What is Laravel Dusk?

Laravel Dusk is an official Laravel package that provides browser automation testing
for Laravel applications. It uses ChromeDriver (a WebDriver implementation for Chrome)
to control a real Chrome browser programmatically from PHP.

Dusk was introduced in Laravel 5.4 and is now a first-class testing tool for the
Laravel ecosystem.

**Package:** `laravel/dusk`  
**Version used in this project:** v8.6.0  
**ChromeDriver version downloaded:** 149.0.7827.115

### What Dusk Can Do

- Open URLs in a real Chrome browser
- Type into form fields, click buttons, select dropdowns
- Wait for pages and elements to load (AJAX, redirects)
- Take screenshots at any point in a test
- Assert text is visible on the page
- Assert URL path changed
- Run JavaScript in the browser context
- Assert database records exist (inherits from PHPUnit)

---

## 4. Why Is Dusk Used?

### Problem Without Dusk

Standard Laravel feature tests (using `$this->post('/login', [...])`) only test
the HTTP layer. They cannot:

- Run JavaScript (admission wizard uses JS to show/hide steps)
- Verify actual browser UI renders correctly
- Test multi-step wizard navigation
- Detect CSS hiding an element that should be visible
- Click a button that triggers an AJAX call

### Why Dusk Solves This

Dusk launches a real Chrome browser (headless by default). Every user interaction
is performed exactly as a real user would. This makes it ideal for:

| Scenario | Why Dusk Works |
|---|---|
| 7-step admission wizard | JS controls step visibility — only Dusk can click through it |
| Student login and role-based redirect | Tests the full auth → session → redirect chain |
| Admission receipt page | Verifies the complete success flow including the rendered view |
| Student dashboard widgets | Confirms fee/attendance data actually appears on screen |

---

## 5. Difference Between PHPUnit and Dusk

| Aspect | PHPUnit (Feature Tests) | Laravel Dusk |
|---|---|---|
| Browser | No browser — HTTP simulation only | Real Chrome browser (ChromeDriver) |
| JavaScript | Not executed | Fully executed |
| Speed | Very fast (milliseconds per test) | Slower (seconds per test, browser startup) |
| Use case | Unit, Integration, HTTP-level Feature tests | End-to-End browser automation |
| Database | Uses in-memory SQLite or test DB with rollback | Uses the actual configured database |
| Screenshots | Not possible | `->screenshot('name')` — auto-saved |
| File location | `tests/Feature/`, `tests/Unit/` | `tests/Browser/` |
| Base class | `Tests\TestCase` | `Tests\DuskTestCase` |
| Run command | `php artisan test` | `php artisan dusk` |
| Headless | N/A | Headless by default, `--no-headless` to watch |

### Code Comparison

**PHPUnit Feature Test (HTTP only):**
```php
public function test_login(): void
{
    $response = $this->post('/login', [
        'email'    => 'superadmin@erp.com',
        'password' => 'password',
    ]);
    $response->assertRedirect('/dashboard');
}
```

**Laravel Dusk Test (Real Browser):**
```php
public function test_login(): void
{
    $this->browse(function (Browser $browser) {
        $browser->visit('/login')
                ->type('#email', 'superadmin@erp.com')
                ->type('#password', 'password')
                ->press('Sign In')
                ->assertPathIs('/dashboard');
    });
}
```

The Dusk test actually opens Chrome, types into the fields, clicks the button,
and verifies the URL changed — exactly what a human tester would do.

---

## 6. How Dusk Works Internally

```
php artisan dusk
       │
       ▼
DuskTestCase::prepare()
  └── Starts ChromeDriver binary on port 9515
            │
            ▼
      RemoteWebDriver::create('http://localhost:9515')
        └── Opens a new Chrome window (headless)
                    │
                    ▼
            Browser::visit(url)
              └── ChromeDriver navigates Chrome to the URL
                          │
                          ▼
                  Browser::type('#email', value)
                    └── ChromeDriver finds the element by CSS selector
                        and sends keystrokes
                                  │
                                  ▼
                          Browser::press('Sign In')
                            └── ChromeDriver clicks the button
                                        │
                                        ▼
                                Browser::assertPathIs('/dashboard')
                                  └── ChromeDriver reads current URL
                                      PHPUnit assertion passes or fails
```

### WebDriver Protocol

Dusk uses the **W3C WebDriver protocol** — a standardized HTTP API for browser
automation. Commands like "click element" and "type text" are HTTP POST requests
to ChromeDriver, which translates them into Chrome DevTools Protocol (CDP) commands.

### ChromeDriver

ChromeDriver is a separate executable that acts as a bridge between Dusk's PHP code
and the Chrome browser. Dusk automatically downloads the correct ChromeDriver version
matching the installed Chrome via `php artisan dusk:chrome-driver`.

### Headless Mode

By default Dusk runs Chrome in **headless mode** — Chrome runs silently in the
background with no visible window. To watch tests run live:

```bash
php artisan dusk --env=local
# or set DUSK_HEADLESS_DISABLED=true in .env.dusk.local
```

---

## 7. Installation Steps Performed

```bash
# 1. Install Dusk package
composer require --dev laravel/dusk

# 2. Fix autoload (post-install autoload dump was interrupted)
composer dump-autoload -o

# 3. Install scaffolding and download ChromeDriver
php artisan dusk:install

# 4. Create environment file for Dusk
# Created: .env.dusk.local
# Set APP_URL=http://13.62.14.215
# Set DUSK_DRIVER_URL=http://localhost:9515

# 5. Created phpunit.dusk.xml (separate config for browser tests)
```

---

## 8. Project-Specific Challenges Faced

### Challenge 1: Autoload Not Updated After Install
**Problem:** After `composer require laravel/dusk`, the class `DuskServiceProvider`
was not found when running `php artisan dusk:install`.

**Root Cause:** The `composer require` command timed out before the
`post-autoload-dump` scripts ran, leaving the classmap incomplete.

**Fix:** Manually ran `composer dump-autoload -o` which regenerated the full
optimised classmap including Dusk's classes.

---

### Challenge 2: Multi-Step JavaScript Wizard
**Problem:** The 7-step admission form uses JavaScript to show/hide step panels.
Standard feature tests cannot interact with this because PHP HTTP clients don't
execute JS.

**Solution:** Dusk runs real Chrome, so JS executes normally. Used `->click()` on
the Next buttons and `->pause(600)` to allow the JS animation to complete before
moving to the next step. For dropdowns populated dynamically, used `->script()` to
directly trigger JS events.

---

### Challenge 3: Logout Form (POST-only)
**Problem:** Laravel's logout route is `POST /logout` (no GET), so clicking a
direct link would fail.

**Solution:** Used `->script("document.querySelector('form[action*=\"logout\"]').submit()")` 
to programmatically submit the logout form via JavaScript in the browser context.

---

### Challenge 4: Remote Server (AWS EC2)
**Problem:** The app runs on `http://13.62.14.215` (AWS EC2). The EC2 instance is
stopped when not in use to save costs.

**Impact:** Tests cannot run if the server is offline.

**Solution:** Tests point to the remote URL. Before running, the instance must be
started from AWS Console. ChromeDriver runs locally on port 9515 and Dusk drives
the remote URL.

---

### Challenge 5: Tenant Middleware
**Problem:** The EduTenant ERP uses a `TenantMiddleware` that resolves the current
tenant from the session. If the wrong user logs in or the session is stale, pages
return 403.

**Solution:** Each test class has its own `loginAsCollegeAdmin()` / `loginAsStudent()`
helper that logs in fresh. This ensures the session and tenant context are correctly
set before each test.

---

## 9. Test Files Created

| File | Test Cases | Phase |
|---|---|---|
| `tests/Browser/Auth/SuperAdminLoginTest.php` | TC-01 to TC-04 | Phase 3 |
| `tests/Browser/Auth/CollegeAdminLoginTest.php` | TC-05 to TC-07 | Phase 3 |
| `tests/Browser/Auth/StudentLoginTest.php` | TC-08 to TC-10 | Phase 3 |
| `tests/Browser/Admissions/AdmissionCreationTest.php` | TC-11 to TC-13 | Phase 3 |
| `tests/Browser/Admissions/StudentListTest.php` | TC-14 to TC-17 | Phase 3 |
| `tests/Browser/Student/StudentDashboardTest.php` | TC-18 to TC-22 | Phase 3 |
| `tests/Browser/Student/StudentProfileTest.php` | TC-23 to TC-26 | Phase 3 |
| `tests/Browser/Student/StudentAttendanceTest.php` | TC-27 to TC-30 | Phase 3 |
| `tests/Browser/Student/StudentScholarshipTest.php` | TC-31 to TC-34 | Phase 3 |
| `tests/Browser/Admissions/AdmissionDatabaseTest.php` | TC-DB-01 to TC-DB-03 | Phase 4 |
| `tests/Browser/Student/StudentDatabaseTest.php` | TC-DB-04 to TC-DB-10 | Phase 4 |

**Total Test Cases: 44**

---

## 10. Commands Reference

```bash
# Run all Dusk tests
php artisan dusk

# Run a specific test file
php artisan dusk tests/Browser/Auth/SuperAdminLoginTest.php

# Run a specific test method
php artisan dusk --filter test_super_admin_can_login

# Run with visible browser (disable headless)
php artisan dusk --env=local  # set DUSK_HEADLESS_DISABLED=true in .env.dusk.local

# Download correct ChromeDriver for your Chrome version
php artisan dusk:chrome-driver

# Create a new Dusk test file
php artisan dusk:make MyNewTest
```

---

## 11. Learning Outcomes

1. **Understood the gap** between HTTP-level testing and real browser testing.
   PHPUnit feature tests are fast and good for APIs, but cannot replace Dusk
   for JavaScript-heavy UI flows like the 7-step admission wizard.

2. **Learned the WebDriver architecture** — ChromeDriver sits between PHP code
   and Chrome, translating Dusk assertions into browser actions via W3C WebDriver.

3. **Practiced selector strategies** — using CSS selectors (`input[name="email"]`),
   ID selectors (`#email`), and attribute selectors for reliable element targeting.

4. **Learned to handle async content** — using `->waitForText()`,
   `->waitForLocation()`, and `->pause()` to handle redirects, AJAX, and
   JS animations.

5. **Applied tenant isolation verification** — wrote DB assertions that confirm
   multi-tenant data separation at the database level, not just the UI level.

6. **Learned screenshot-driven debugging** — Dusk's `->screenshot()` method
   creates visual proof of every test step, which is invaluable for diagnosing
   failures in headless tests.

7. **Understood the importance of test independence** — each test logs in fresh
   rather than relying on shared state, preventing test order dependencies.

---

*EduTenant ERP — Laravel Dusk Internship Notes | June 2026*
