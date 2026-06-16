<div align="center">

# 🎓 EduTenant ERP

**A multi-tenant, role-based institution management platform**

Built with Laravel 13 · PHP 8.4 · MySQL 8 · Deployed on AWS EC2

[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=flat&logo=php)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-13.9-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat&logo=mysql)](https://mysql.com)
[![AWS](https://img.shields.io/badge/AWS-EC2%20Free%20Tier-FF9900?style=flat&logo=amazonaws)](https://aws.amazon.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

</div>

---

> **🚀 Live Demo:** Deployed on AWS EC2 Free Tier.
> The instance is stopped when not in use to conserve resources.
> **Contact the maintainer** to start the instance before testing.
> Demo login credentials are listed in the [Default Accounts](#default-accounts-after-seeding) section.

---

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Multi-Tenant Architecture](#multi-tenant-architecture)
- [Technology Stack](#technology-stack)
- [Local Installation](#local-installation)
- [Environment Setup](#environment-setup)
- [Mail Configuration](#mail-configuration)
- [Queue Configuration](#queue-configuration)
- [Database Setup](#database-setup)
- [Running Locally](#running-locally)
- [Default Accounts](#default-accounts-after-seeding)
- [User Roles](#user-roles)
- [Modules](#modules)
- [API Reference](#api-reference)
- [AWS Deployment](#aws-deployment)
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

### 🏫 Institution Management
- Multi-tenant architecture with complete data isolation per tenant
- Super Admin portal for institution onboarding and monitoring
- Auto-provisioning of College Admin accounts with secure credential delivery
- Impersonation — Super Admin can log in as any College Admin for support
- Onboarding progress tracker (5-step checklist per institution)

### 📋 Admissions
- Step-by-step admission wizard
- Auto-generated admission numbers
- Certificate upload during admission
- Printable admission receipts (PDF)
- Guardian / parent detail capture

### 💰 Fee Management
- Fee type and structure configuration per institution
- Bulk fee assignment across branches
- Fee collection with receipt generation
- Online payment via Razorpay (order creation + signature verification + webhook)
- Fee exemptions per student
- Transport fee management
- Student fee profile with outstanding balance view
- Excel export of fee payments

### 🎓 Student Portal
- Personal dashboard with fee and attendance summary
- Fee history and receipt download
- Attendance calendar view
- Certificate download
- Profile and password management

### 📅 Attendance
- Student attendance marking by branch and date
- Staff attendance tracking
- Monthly reports and analytics
- Low-attendance alert emails

### 👨‍💼 Staff & Payroll
- Staff profile management
- Role assignment
- Leave requests with approval/rejection workflow
- Payroll generation, approval, and mark-as-paid
- Payroll slip PDF generation
- Excel payroll export

### 📊 Reports
- Daily collection report
- Annual financial summary
- Student-wise reports
- Fee collection analytics

### 🔔 Notifications
- Email dispatch for admissions, fee receipts, payroll, attendance alerts
- Queue-based async delivery for bulk notifications
- Email delivery logs with status tracking

### 📱 API (Mobile)
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

| Layer | Technology | Version |
|---|---|---|
| Framework | Laravel | 13.9 |
| Language | PHP | 8.4 |
| Database | MySQL | 8.0 |
| Authentication | Laravel Breeze + Sanctum | — |
| Mail | Brevo SMTP | smtp-relay.brevo.com:587 |
| PDF Generation | barryvdh/laravel-dompdf | 3.1 |
| Excel Export | maatwebsite/excel | 3.1 |
| Payments | Razorpay SDK | 2.9 |
| Queue Driver | Database | — |
| Frontend Assets | Vite + Bootstrap | — |
| Web Server | Nginx + PHP-FPM | 1.24 |
| Process Manager | Supervisor | 4.x |
| Cloud Platform | AWS EC2 | t3.micro, eu-north-1 |

---

## Local Installation

### Requirements

| Tool | Version |
|---|---|
| PHP | 8.4+ |
| MySQL | 8.0+ |
| Composer | 2.x |
| Node.js | 20+ |
| npm | 10+ |

### Quick Start

```bash
# 1. Clone the repository
git clone https://github.com/Yaswanth-Kumar21/edutenant-erp.git
cd edutenant-erp

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies
npm install

# 4. Copy environment file
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Configure your .env (database, mail — see Environment Setup below)

# 7. Run database migrations
php artisan migrate

# 8. Seed demo data (roles + super admin + demo colleges)
php artisan db:seed

# 9. Build frontend assets
npm run build

# 10. Start the development server
php artisan serve
```

Or run everything in one command:

```bash
composer run setup
```

---

## Environment Setup

Copy `.env.example` to `.env` and fill in your values.

> ⚠️ **Never commit your `.env` file to version control. It is already listed in `.gitignore`.**

```dotenv
APP_NAME="EduTenant ERP"
APP_ENV=local
APP_KEY=                        # auto-generated by php artisan key:generate
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=edutenant_erp
DB_USERNAME=your_db_username    # replace with your MySQL username
DB_PASSWORD=your_db_password    # replace with your MySQL password

QUEUE_CONNECTION=database       # required for async email notifications

SESSION_DRIVER=database
CACHE_STORE=database
```

---

## Mail Configuration

EduTenant uses **Brevo SMTP** for email delivery. Brevo provides 300 free emails/day with no credit card required.

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
MAIL_PASSWORD=your_brevo_smtp_key       # ⚠️ keep this secret — never commit
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

> `MAIL_FROM_ADDRESS` must match a verified sender address in your Brevo account.

### Email Delivery Flow

```
TenantController::store()
  → CollegeAdminProvisioningService::provision()
      → generates temporary password
      → creates User record (College Admin role)
      → MailLogService::send()
          → Mail::to($principal_email)->send(CollegeAdminWelcomeMail)
          → email_logs table updated with status: sent / failed
```

All institution welcome emails are sent **synchronously** so delivery status is immediately visible on the institution detail page.

---

## Queue Configuration

Attendance alerts, fee receipts, and payroll notifications are sent asynchronously via the database queue driver.

```dotenv
QUEUE_CONNECTION=database
```

### Running the queue worker

```bash
# Development — auto-restarts on failure
php artisan queue:listen --tries=3 --timeout=60

# Production — use Supervisor (see AWS Deployment section)
php artisan queue:work --sleep=3 --tries=3 --max-time=3600
```

---

## Database Setup

### Schema Overview

38 migrations across 8 functional groups:

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

### Commands

```bash
# Run all migrations
php artisan migrate

# Seed demo data
php artisan db:seed

# Full reset (development only — destroys all data)
php artisan migrate:fresh --seed
```

---

## Running Locally

```bash
composer run dev
```

This starts four processes concurrently:

| Process | Command | Purpose |
|---|---|---|
| Web server | `php artisan serve` | HTTP on port 8000 |
| Queue worker | `php artisan queue:listen` | Processes async emails |
| Log viewer | `php artisan pail` | Real-time log output |
| Asset watcher | `npm run dev` | Vite hot reload |

---

## Default Accounts (after seeding)

> ⚠️ These are **demo accounts for testing only**.
> Change all passwords before using in production.

### Super Admin

| Role | Email | Password |
|---|---|---|
| Super Admin | `superadmin@erp.com` | `password` |

### College 1 — Sri Venkateswara Degree College (Tirupati)

| Role | Email | Password |
|---|---|---|
| College Admin | `admin@svc.edu` | `password` |
| Staff | `staff@svc.edu` | `password` |
| Teacher | `ravi.kumar@svc.edu` | `password` |
| Teacher | `priya.sharma@svc.edu` | `password` |
| Teacher | `anil.reddy@svc.edu` | `password` |
| Student | `ravi.teja@svc.student.edu` | `81svc0001` |
| Student | `sravani.reddy@svc.student.edu` | `81svc0002` |
| Student | `mahesh.babu@svc.student.edu` | `81svc0003` |
| Student | `pooja.sharma@svc.student.edu` | `81svc0004` |
| Student | `aakash.kumar@svc.student.edu` | `81svc0005` |

### College 2 — Sai Chaitanya Degree College (Vijayawada)

| Role | Email | Password |
|---|---|---|
| College Admin | `admin@scc.edu` | `password` |
| Staff | `staff@scc.edu` | `password` |
| Teacher | `ravi.kumar@scc.edu` | `password` |
| Student | `ravi.teja@scc.student.edu` | `81scc0001` |
| Student | `sravani.reddy@scc.student.edu` | `81scc0002` |
| Student | `mahesh.babu@scc.student.edu` | `81scc0003` |

> **Student password format:** `81{college-slug}{sequence}` — e.g. `81svc0001`

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

| Module | Route | Description |
|---|---|---|
| Institution Management | `/super-admin/tenants` | Create and manage institutions with auto College Admin provisioning |
| Admissions | `/admin/admissions` | Multi-step form with certificate upload and printable receipts |
| Fee Management | `/admin/fees` | Fee structures, collection, receipts, exemptions, Razorpay online payment |
| Scholarships | `/admin/scholarships` | Create and assign scholarships with discount tracking |
| Student Portal | `/student/dashboard` | Self-service portal — fees, attendance, certificates |
| Attendance | `/admin/attendance` | Student and staff attendance with monthly analytics |
| Staff & Payroll | `/admin/staff`, `/admin/payroll` | Profiles, leave management, payroll PDF generation |
| Finance | `/admin/expenses`, `/admin/incomes` | Income and expense tracking by category |
| Reports | `/admin/reports` | Daily, annual, student, and fee collection reports |
| Notifications | `/admin/messages` | Email messages to students by branch or individually |

---

## API Reference

**Base URL:** `/api/v1/`
**Authentication:** Bearer token via Laravel Sanctum

```
POST /api/v1/login     → returns Bearer token
Authorization: Bearer {token}
```

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| `POST` | `/api/v1/login` | No | Authenticate, receive Bearer token |
| `POST` | `/api/v1/logout` | Yes | Revoke current token |
| `GET` | `/api/v1/me` | Yes | Authenticated user profile |
| `GET` | `/api/v1/student/dashboard` | Yes | Student dashboard stats |
| `GET` | `/api/v1/student/fees` | Yes | Fee payment history |
| `GET` | `/api/v1/student/attendance` | Yes | Attendance records |
| `GET` | `/api/v1/student/notifications` | Yes | Fee alerts and messages |

- Tokens expire after **30 days**
- Student endpoints require `role:student` middleware
- All responses are tenant-scoped

---

## AWS Deployment

The application is deployed on **AWS EC2 Free Tier** (`eu-north-1` — Stockholm region).

### Infrastructure Overview

| Component | Details |
|---|---|
| Cloud Provider | AWS EC2 Free Tier |
| Instance Type | t3.micro (1 vCPU, 1GB RAM) |
| Operating System | Ubuntu 24.04 LTS |
| Region | eu-north-1 (Stockholm) |
| Web Server | Nginx 1.24 + PHP-FPM 8.4 |
| Database | MySQL 8 (on same instance) |
| Storage | 30GB gp3 EBS volume |
| Network | Elastic IP (static — persists on restart) |
| Queue Manager | Supervisor (auto-restart on crash) |
| Scheduler | Crontab — every minute |
| Security Group | SSH (My IP only), HTTP/HTTPS (public) |

---

### Step-by-Step Deployment Guide

#### Step 1 — Launch EC2 Instance

1. Go to **AWS Console → EC2 → Launch Instance**
2. Select **Ubuntu 24.04 LTS** (Free tier eligible)
3. Choose **t3.micro** instance type
4. Create a new key pair (`.pem` file) — save it securely
5. Configure security group:

| Port | Source | Purpose |
|---|---|---|
| 22 | Your IP only | SSH |
| 80 | 0.0.0.0/0 | HTTP |
| 443 | 0.0.0.0/0 | HTTPS |

6. Set storage to **30 GiB gp3**
7. Launch and allocate an **Elastic IP** — associate it with the instance

#### Step 2 — Connect via SSH

```bash
# Fix key permissions (Windows)
icacls "edutenant-key.pem" /inheritance:r /grant:r "YourUsername:R"

# Connect
ssh -i "edutenant-key.pem" ubuntu@YOUR_ELASTIC_IP
```

#### Step 3 — Install System Dependencies

```bash
sudo apt update && sudo apt upgrade -y
sudo reboot
# reconnect after reboot

sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.4 php8.4-fpm php8.4-mysql php8.4-xml \
  php8.4-mbstring php8.4-curl php8.4-zip php8.4-gd php8.4-bcmath \
  php8.4-intl php8.4-tokenizer php8.4-cli nginx mysql-server supervisor git

# Node.js 20
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

#### Step 4 — Configure MySQL

```bash
sudo mysql_secure_installation   # answer prompts to secure MySQL

sudo mysql
```

```sql
CREATE DATABASE edutenant_erp;
CREATE USER 'your_db_user'@'localhost' IDENTIFIED BY 'your_strong_password';
GRANT ALL PRIVILEGES ON edutenant_erp.* TO 'your_db_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### Step 5 — Clone and Install Project

```bash
sudo mkdir -p /var/www/college-erp
sudo chown ubuntu:ubuntu /var/www/college-erp
git clone https://github.com/Yaswanth-Kumar21/edutenant-erp.git /var/www/college-erp
cd /var/www/college-erp

composer install --no-dev --optimize-autoloader
npm install
npm run build
```

#### Step 6 — Configure Environment

```bash
cp .env.example .env
# Edit .env — set DB credentials, APP_URL, mail settings
# Never expose actual passwords in README or version control
php artisan key:generate
```

**Key production `.env` values:**

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=http://your-elastic-ip

DB_DATABASE=edutenant_erp
DB_USERNAME=your_db_user        # use placeholder in docs
DB_PASSWORD=your_db_password    # use placeholder in docs

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=your_brevo_username
MAIL_PASSWORD=your_brevo_smtp_key
```

#### Step 7 — Migrate and Seed

```bash
php artisan migrate --force
php artisan db:seed --force
```

#### Step 8 — Storage Permissions

```bash
sudo usermod -a -G www-data ubuntu
sudo chown -R ubuntu:www-data /var/www/college-erp/storage
sudo chown -R ubuntu:www-data /var/www/college-erp/bootstrap/cache
sudo chmod -R 775 /var/www/college-erp/storage
sudo chmod -R 775 /var/www/college-erp/bootstrap/cache
php artisan storage:link
```

#### Step 9 — Cache for Performance

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### Step 10 — Nginx Configuration

Create `/etc/nginx/sites-available/college-erp`:

```nginx
server {
    listen 80;
    server_name your-elastic-ip-or-domain;
    root /var/www/college-erp/public;
    index index.php;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
sudo ln -s /etc/nginx/sites-available/college-erp /etc/nginx/sites-enabled/
sudo rm /etc/nginx/sites-enabled/default
sudo nginx -t && sudo systemctl reload nginx
sudo systemctl enable nginx
```

#### Step 11 — Supervisor (Queue Worker)

Create `/etc/supervisor/conf.d/edutenant-queue.conf`:

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

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start edutenant-queue
sudo supervisorctl status
```

#### Step 12 — Cron Scheduler

```bash
(crontab -l 2>/dev/null; echo "* * * * * cd /var/www/college-erp && php artisan schedule:run >> /dev/null 2>&1") | crontab -
```

---

### SSL with Let's Encrypt (requires a domain name)

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com
sudo systemctl reload nginx
```

Then:
- Update `APP_URL=https://yourdomain.com` in `.env`
- Re-enable `URL::forceScheme('https')` in `app/Providers/AppServiceProvider.php`
- Run `php artisan config:cache`

---

### Managing the EC2 Instance

The instance is **stopped when not in use** to conserve AWS credits.

**Start the server (when needed for testing):**
```
AWS Console → EC2 → Instances → edutenant-server → Instance state → Start instance
```
Wait ~60 seconds. All services start automatically on boot.

**Stop the server:**
```
AWS Console → EC2 → Instances → edutenant-server → Instance state → Stop instance
```

> ✅ All data, code, database, and configuration is safely preserved on the EBS volume.
> ❌ Never click **Terminate** — that permanently deletes everything.

---

## Security Features

| Feature | Implementation |
|---|---|
| Tenant isolation | Every query scoped to `tenant_id`; `TenantScoped::assertTenant()` on every model |
| Forced password change | College Admins must change temp password on first login (`must_change_password` flag) |
| Password history | Hashed and stored in `password_histories` — prevents reuse |
| Impersonation audit | Every Super Admin impersonation logged with IP, start, and end time |
| Email logs | All outbound emails recorded with delivery status and error details |
| Razorpay verification | Payment signatures verified with `hash_hmac` on webhook and callback |
| API token scoping | Sanctum tokens expire after 30 days; revoked on logout |
| No public registration | `RegisteredUserController` returns 403 in production |
| SSH restriction | Port 22 limited to known IP in security group |
| No secrets in repo | `.env` is gitignored; all credentials via environment variables |
| HTTPS enforcement | `URL::forceScheme('https')` active in production (requires SSL cert) |

### Files excluded from version control

```gitignore
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
```

---

## Automated Testing — Laravel Dusk

This project includes a full suite of end-to-end browser tests built with **Laravel Dusk v8.6.0**, covering the **Admissions** and **Student Dashboard** modules.

### Test Results

| Metric | Value |
|---|---|
| Total Tests | 41 |
| Passed | ✅ 41 |
| Failed | 0 |
| Assertions | 78 |
| Browser | Chrome 149 (Headless) |
| Target URL | http://13.62.14.215 |

### Test Structure

```
tests/Browser/
├── Auth/
│   ├── SuperAdminLoginTest.php       # TC-01 to TC-04
│   ├── CollegeAdminLoginTest.php     # TC-05 to TC-07
│   └── StudentLoginTest.php          # TC-08 to TC-10
├── Admissions/
│   ├── AdmissionCreationTest.php     # TC-11 to TC-13  (7-step wizard)
│   ├── StudentListTest.php           # TC-14 to TC-17
│   └── AdmissionDatabaseTest.php     # TC-DB-01 to TC-DB-03
└── Student/
    ├── StudentDashboardTest.php      # TC-18 to TC-22
    ├── StudentProfileTest.php        # TC-23 to TC-26
    ├── StudentAttendanceTest.php     # TC-27 to TC-30
    ├── StudentScholarshipTest.php    # TC-31 to TC-34
    └── StudentDatabaseTest.php       # TC-DB-04 to TC-DB-10
```

### Running the Tests

```bash
# Prerequisites — EC2 instance must be running at http://13.62.14.215

# Run all Dusk tests
php artisan dusk

# Run a specific module
php artisan dusk tests/Browser/Auth/
php artisan dusk tests/Browser/Admissions/
php artisan dusk tests/Browser/Student/

# Run a single test file
php artisan dusk tests/Browser/Auth/SuperAdminLoginTest.php

# Run with visible browser (disable headless)
# Set DUSK_HEADLESS_DISABLED=true in .env.dusk.local first
php artisan dusk
```

### Configuration Files

| File | Purpose |
|---|---|
| `.env.dusk.local` | Sets `APP_URL=http://13.62.14.215` for browser tests |
| `phpunit.dusk.xml` | Separate PHPUnit config pointing to `tests/Browser/` |
| `tests/DuskTestCase.php` | Base class — starts ChromeDriver on port 9515 |

### Reports

| File | Description |
|---|---|
| `TESTING_REPORT.md` | Full test results, bug fixes, screenshots index |
| `LARAVEL_DUSK_NOTES.md` | Internship learning notes — what Dusk is and how it works |
| `INTERNSHIP_REPORT.html` | Print-ready report — open in Chrome and Save as PDF |

---

## License

MIT License. See [LICENSE](LICENSE) for details.

---

<div align="center">

*EduTenant ERP — Built with ❤️ using Laravel 13 · PHP 8.4 · MySQL 8*

*Deployed on AWS EC2 Free Tier · eu-north-1 (Stockholm)*

</div>
