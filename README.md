# EduTenant ERP

A multi-tenant, role-based institution management platform built with Laravel 13. Each institution (tenant) operates in complete isolation — separate data, separate admin accounts, shared application infrastructure.

---

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Multi-Tenant Architecture](#multi-tenant-architecture)
- [Technology Stack](#technology-stack)
- [Installation](#installation)
- [Environment Setup](#environment-setup)
- [Mail Configuration](#mail-configuration)
- [Queue Configuration](#queue-configuration)
- [Database Setup](#database-setup)
- [Running Locally](#running-locally)
- [User Roles](#user-roles)
- [Modules](#modules)
- [API Reference](#api-reference)
- [Deployment](#deployment)
- [Security Features](#security-features)
- [License](#license)

---

## Overview

EduTenant ERP provides a complete administrative system for degree colleges and educational institutions. A single Super Admin manages multiple institutions (tenants). Each institution gets its own isolated College Admin dashboard, student portal, fee management system, payroll, and reporting.

Designed for:
- Degree colleges and autonomous institutions
- Multi-campus setups under a single platform operator
- SaaS deployment where institutions are onboarded by a central admin

---

## Features

### Institution Management
- Multi-tenant architecture with complete data isolation per tenant
- Super Admin portal for institution onboarding and monitoring
- Auto-provisioning of College Admin accounts with secure credential delivery
- Impersonation — Super Admin can log in as any College Admin for support
- Onboarding progress tracker (5-step checklist per institution)

### Admissions
- Step-by-step admission wizard
- Auto-generated admission numbers
- Certificate upload during admission
- Printable admission receipts (PDF)
- Guardian / parent detail capture

### Fee Management
- Fee type and structure configuration per institution
- Bulk fee assignment across branches
- Fee collection with receipt generation
- Online payment via Razorpay (order creation + signature verification + webhook)
- Fee exemptions per student
- Transport fee management
- Student fee profile with outstanding balance view
- Excel export of fee payments

### Student Portal
- Personal dashboard with fee and attendance summary
- Fee history and receipt download
- Attendance calendar view
- Certificate download
- Profile and password management

### Attendance
- Student attendance marking by branch and date
- Staff attendance tracking
- Monthly reports and analytics
- Low-attendance alert emails

### Staff & Payroll
- Staff profile management
- Role assignment
- Leave requests with approval/rejection workflow
- Payroll generation, approval, and mark-as-paid
- Payroll slip PDF generation
- Excel payroll export

### Reports
- Daily collection report
- Annual financial summary
- Student-wise reports
- Fee collection analytics

### Notifications
- Email dispatch for admissions, fee receipts, payroll, attendance alerts
- Queue-based async delivery for bulk notifications
- Email delivery logs with status tracking

### API (Mobile)
- Sanctum token authentication
- Student dashboard, fees, attendance, notifications endpoints
- Tenant-isolated data access

---

## Multi-Tenant Architecture

EduTenant uses a **shared database, isolated by `tenant_id`** model.

```
Super Admin
    │
    ├── Tenant: Sri Venkateswara Degree College  (slug: svc)
    │       └── College Admin, Staff, Teachers, Students, Fees, Attendance
    │
    └── Tenant: Sai Chaitanya Degree College     (slug: scc)
            └── College Admin, Staff, Teachers, Students, Fees, Attendance
```

**Isolation is enforced at three layers:**

1. `TenantMiddleware` — resolves the tenant from session on every request
2. `TenantScoped` trait — `assertTenant()` method verifies every model belongs to the active tenant before any operation
3. All queries explicitly filter by `tenant_id`

**Tenant Resolution Priority:**
1. Custom domain match (`tenants.domain`)
2. Subdomain match (`tenants.slug`)
3. Session-based (Super Admin panel switching)

---

## Technology Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 13.9 |
| PHP | 8.4 |
| Database | MySQL 8 |
| Auth | Laravel Breeze + Sanctum (API) |
| Mail | Brevo SMTP (smtp-relay.brevo.com:587) |
| PDF | barryvdh/laravel-dompdf 3.1 |
| Excel | maatwebsite/excel 3.1 |
| Payments | Razorpay SDK 2.9 |
| Queue | Database driver |
| Assets | Vite + Bootstrap |

---

## Installation

### Requirements

- PHP 8.3+
- MySQL 8.0+
- Composer 2.x
- Node.js 18+ and npm
- A Brevo account (free — 300 emails/day)

### Quick Start

```bash
# 1. Clone the repository
git clone https://github.com/your-org/college-erp.git
cd college-erp

# 2. Install dependencies
composer install
npm install

# 3. Copy environment file
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Configure your .env (see Environment Setup below)

# 6. Run migrations
php artisan migrate

# 7. Seed initial data (roles + super admin)
php artisan db:seed

# 8. Build frontend assets
npm run build

# 9. Start the server
php artisan serve
```

Or use the built-in setup script:

```bash
composer run setup
```

---

## Environment Setup

Copy `.env.example` to `.env` and update:

```dotenv
APP_NAME="EduTenant ERP"
APP_ENV=local
APP_KEY=                    # generated by php artisan key:generate
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=college_erp
DB_USERNAME=root
DB_PASSWORD=your_password

QUEUE_CONNECTION=database   # required for async email notifications

SESSION_DRIVER=cookie
CACHE_STORE=file
```

---

## Mail Configuration

EduTenant uses **Brevo SMTP** for email delivery. Brevo provides 300 free emails/day with no credit card.

### Setup Brevo

1. Create a free account at [brevo.com](https://brevo.com)
2. Go to **Settings → SMTP & API → SMTP**
3. Copy your SMTP key
4. Add your sender email at **Senders & IP → Senders**

### .env Mail Settings

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=your_brevo_login@email.com
MAIL_PASSWORD=xsmtpsib-your-smtp-key-here
MAIL_FROM_ADDRESS="noreply@yourdomain.com"   # Must be verified in Brevo Senders
MAIL_FROM_NAME="${APP_NAME}"
```

> **Important:** `MAIL_FROM_ADDRESS` must match an email address added and verified in your Brevo Senders list. Using an unverified sender causes delivery failures.

### Email Delivery Flow

When a new institution is created:

```
TenantController::store()
  → CollegeAdminProvisioningService::provision()
      → generates temporary password
      → creates User record (College Admin role)
      → MailLogService::send()
          → Mail::to($principal_email)->send(CollegeAdminWelcomeMail)
          → email_logs table updated with status: sent / failed
```

All welcome emails are sent **synchronously** (not queued) so delivery status is known immediately and shown on the institution detail page.

---

## Queue Configuration

Attendance alerts, fee receipts, and payroll notifications are sent asynchronously via the database queue.

```dotenv
QUEUE_CONNECTION=database
```

### Running the queue worker

```bash
# Development (auto-restarts on failure)
php artisan queue:listen --tries=3 --timeout=60

# Production (use Supervisor)
php artisan queue:work --sleep=3 --tries=3 --max-time=3600
```

### Supervisor config (production)

```ini
[program:edutenant-queue]
command=php /var/www/college-erp/artisan queue:work --sleep=3 --tries=3 --max-time=3600
directory=/var/www/college-erp
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/log/edutenant-queue.log
```

---

## Database Setup

### Schema Overview

38 migrations covering:

| Group | Tables |
|---|---|
| Core | `tenants`, `roles`, `users` |
| Academics | `streams`, `courses`, `branches`, `academic_years` |
| Students | `students`, `student_profiles`, `guardian_details`, `student_certificates`, `admission_receipts`, `student_scholarships`, `student_transport`, `categories` |
| Fees | `fee_types`, `fee_payments`, `fee_structures` |
| Staff | `staff`, `staff_roles`, `staff_attendance`, `leave_requests`, `payrolls`, `attendance_reports` |
| Finance | `expenses`, `expense_categories`, `incomes`, `income_categories` |
| Messaging | `messages`, `notifications`, `email_logs` |
| System | `password_histories`, `impersonation_logs`, `jobs`, `failed_jobs`, `cache`, `personal_access_tokens` |

### Fresh install

```bash
php artisan migrate
php artisan db:seed
```

### Reset (development only)

```bash
php artisan migrate:fresh --seed
```

---

## Running Locally

### All-in-one (uses `composer run dev`)

```bash
composer run dev
```

This starts concurrently:
- `php artisan serve` — web server on :8000
- `php artisan queue:listen` — processes async email jobs
- `php artisan pail` — real-time log viewer
- `npm run dev` — Vite asset watcher

### Manual start

```bash
php artisan serve            # Terminal 1
php artisan queue:listen     # Terminal 2
npm run dev                  # Terminal 3
```

### Default Accounts (after seeding)

> Run `php artisan db:seed` to create all demo accounts below.

#### Super Admin

| Role | Email | Password |
|---|---|---|
| Super Admin | `superadmin@erp.com` | `password` |

#### College 1 — Sri Venkateswara Degree College (Tirupati)

| Role | Email | Password |
|---|---|---|
| College Admin | `admin@svc.edu` | `password` |
| Staff | `staff@svc.edu` | `password` |
| Teacher | `ravi.kumar@svc.edu` | `password` |
| Student | `ravi.teja@svc.student.edu` | `81svc0001` |
| Student | `sravani.reddy@svc.student.edu` | `81svc0002` |
| Student | `mahesh.babu@svc.student.edu` | `81svc0003` |

#### College 2 — Sai Chaitanya Degree College (Vijayawada)

| Role | Email | Password |
|---|---|---|
| College Admin | `admin@scc.edu` | `password` |
| Staff | `staff@scc.edu` | `password` |
| Teacher | `ravi.kumar@scc.edu` | `password` |
| Student | `ravi.teja@scc.student.edu` | `81scc0001` |

> **Note:** Narayana Degree College (College 3) has been removed from the platform. Only SVC and SCC are active demo institutions. Change all credentials immediately after first login.

---

## User Roles

### Super Admin
- Manages all institutions (tenants) on the platform
- Creates institutions and auto-provisions College Admin accounts
- Monitors onboarding progress per institution
- Can impersonate any College Admin for support
- Views platform-wide statistics

### College Admin
- Full control over their institution's data
- Manages streams, courses, branches, academic years
- Handles student admissions and profiles
- Configures fee structures and processes payments
- Manages staff, leave requests, and payroll
- Sends notifications and generates reports

### Staff
- Dashboard with fee collection and admission summary
- Access to student management and fee collection
- Attendance marking

### Teacher
- Attendance-focused dashboard
- Student attendance marking and reports
- Low-attendance monitoring

### Student
- Personal portal: fees, attendance, certificates, profile
- Mobile API access via Sanctum tokens
- Read-only view of their own data

---

## Modules

### Institution Management (`/super-admin/tenants`)
Create and manage institutions. Each creation auto-provisions a College Admin account and sends welcome email with temporary credentials.

### Admissions (`/admin/admissions`)
Multi-step admission form with certificate upload. Generates unique admission numbers and printable receipts.

### Fee Management (`/admin/fees`)
Configure fee types and semester-wise structures. Collect payments, generate receipts, handle exemptions, and manage transport fees. Razorpay integration for online payment.

### Scholarship Management (`/admin/scholarships`)
Create and assign scholarships to students with discount tracking.

### Student Portal (`/student/dashboard`)
Self-service portal for enrolled students. View fees, download receipts, check attendance, download certificates.

### Attendance (`/admin/attendance`)
Mark student and staff attendance. Monthly reports and analytics with branch-wise breakdowns.

### Staff & Payroll (`/admin/staff`, `/admin/payroll`)
Staff profiles, role assignment, leave management, monthly payroll processing with PDF slip generation.

### Finance (`/admin/expenses`, `/admin/incomes`)
Track institutional income and expenses by category. Excel export.

### Reports (`/admin/reports`)
Daily, annual, student, and fee collection reports.

### Notifications (`/admin/messages`)
Send SMS/email/WhatsApp messages to students by branch or individually.

---

## API Reference

Base URL: `/api/v1/`  
Authentication: Bearer token (Sanctum)

| Method | Endpoint | Description |
|---|---|---|
| `POST` | `/api/v1/login` | Authenticate, receive Bearer token |
| `POST` | `/api/v1/logout` | Revoke current token |
| `GET` | `/api/v1/me` | Authenticated user profile |
| `GET` | `/api/v1/student/dashboard` | Student dashboard stats |
| `GET` | `/api/v1/student/fees` | Fee payment history |
| `GET` | `/api/v1/student/attendance` | Attendance records |
| `GET` | `/api/v1/student/notifications` | Fee alerts + messages |

Tokens expire after 30 days. Student endpoints require `role:student` middleware.

---

## Deployment

### Environment checklist

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

# Force HTTPS (handled in AppServiceProvider)
# URL::forceScheme('https') is active when APP_ENV=production
```

### Deploy steps

```bash
composer install --no-dev --optimize-autoloader
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
```

### Scheduled tasks

Add to server crontab:

```bash
* * * * * cd /var/www/college-erp && php artisan schedule:run >> /dev/null 2>&1
```

---

## Security Features

- **Tenant isolation** — every query scoped to `tenant_id`; `TenantScoped::assertTenant()` enforces ownership on every model operation
- **Forced password change** — College Admins must change temporary password on first login (`must_change_password` flag + `ForcePasswordChange` middleware)
- **Password history** — stored in `password_histories` table (hash only, never plain text)
- **Impersonation audit** — every Super Admin impersonation logged in `impersonation_logs` with IP, start time, and end time
- **Email logs** — all outbound emails recorded in `email_logs` with delivery status and error messages
- **Razorpay signature verification** — payment webhook and callback signatures verified with `hash_hmac`
- **API token scoping** — Sanctum tokens limited to 30-day TTL; revoked on logout
- **Public registration disabled in production** — `RegisteredUserController` returns 403 when `APP_ENV=production`; all users provisioned via Super Admin

---

## .gitignore Review

Ensure the following are excluded before any commit:

```
/vendor/
/node_modules/
.env
/storage/logs/*.log
/storage/framework/cache/
/storage/framework/sessions/
/storage/framework/views/
/public/build/
/public/hot
.phpunit.cache/
.DS_Store
Thumbs.db
*.swp
*.swo
/.idea/
/.vscode/
```

---

## License

MIT License. See [LICENSE](LICENSE) for details.

---

*EduTenant ERP — Built with Laravel 13 · PHP 8.4 · MySQL 8*
