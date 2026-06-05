# EduTenant ERP

A multi-tenant, role-based institution management platform built with Laravel 13. Each institution (tenant) operates in complete isolation — separate data, separate admin accounts, shared application infrastructure.

> **Live Demo:** The application is deployed on AWS EC2 (Free Tier).
> Contact the maintainer to start the instance before testing.
> Demo credentials are listed in the [Default Accounts](#default-accounts-after-seeding) section.

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
| Server | Nginx + PHP-FPM |
| Queue Manager | Supervisor |
| Cloud | AWS EC2 (t3.micro, eu-north-1) |

---

## Installation

### Requirements

- PHP 8.4+
- MySQL 8.0+
- Composer 2.x
- Node.js 20+ and npm
- A Brevo account (free — 300 emails/day)

### Quick Start

```bash
# 1. Clone the repository
git clone https://github.com/Yaswanth-Kumar21/edutenant-erp.git
cd edutenant-erp

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

# 7. Seed initial data (roles + super admin + demo colleges)
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

Copy `.env.example` to `.env` and update the values below.

> **Never commit your `.env` file to version control. It is listed in `.gitignore`.**

```dotenv
APP_NAME="EduTenant ERP"
APP_ENV=local
APP_KEY=                    # generated by php artisan key:generate
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=edutenant_erp
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password

QUEUE_CONNECTION=database   # required for async email notifications

SESSION_DRIVER=database
CACHE_STORE=database
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
MAIL_PASSWORD=your_brevo_smtp_key        # keep this secret
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

> `MAIL_FROM_ADDRESS` must match a verified sender in your Brevo account.

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

---

## Queue Configuration

Attendance alerts, fee receipts, and payroll notifications are sent asynchronously via the database queue.

```dotenv
QUEUE_CONNECTION=database
```

### Running the queue worker

```bash
# Development
php artisan queue:listen --tries=3 --timeout=60

# Production (use Supervisor — see AWS Deployment section)
php artisan queue:work --sleep=3 --tries=3 --max-time=3600
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

```bash
composer run dev
```

This starts concurrently:
- `php artisan serve` — web server on :8000
- `php artisan queue:listen` — processes async email jobs
- `php artisan pail` — real-time log viewer
- `npm run dev` — Vite asset watcher

### Default Accounts (after seeding)

> These are demo accounts for testing only. **Change all passwords before going to production.**

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
| Teacher | `priya.sharma@svc.edu` | `password` |
| Teacher | `anil.reddy@svc.edu` | `password` |
| Student | `ravi.teja@svc.student.edu` | `81svc0001` |
| Student | `sravani.reddy@svc.student.edu` | `81svc0002` |
| Student | `mahesh.babu@svc.student.edu` | `81svc0003` |
| Student | `pooja.sharma@svc.student.edu` | `81svc0004` |
| Student | `aakash.kumar@svc.student.edu` | `81svc0005` |

#### College 2 — Sai Chaitanya Degree College (Vijayawada)

| Role | Email | Password |
|---|---|---|
| College Admin | `admin@scc.edu` | `password` |
| Staff | `staff@scc.edu` | `password` |
| Teacher | `ravi.kumar@scc.edu` | `password` |
| Student | `ravi.teja@scc.student.edu` | `81scc0001` |
| Student | `sravani.reddy@scc.student.edu` | `81scc0002` |
| Student | `mahesh.babu@scc.student.edu` | `81scc0003` |

> Student password format: `81{college-slug}{sequence-number}`

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
Send email messages to students by branch or individually.

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

## AWS Deployment

The application is deployed on **AWS EC2 Free Tier** in the `eu-north-1` (Stockholm) region.

### Infrastructure

| Component | Details |
|---|---|
| Server | AWS EC2 t3.micro (1 vCPU, 1GB RAM) |
| OS | Ubuntu 24.04 LTS |
| Web Server | Nginx 1.24 |
| PHP | 8.4 (php8.4-fpm) |
| Database | MySQL 8 (local, same instance) |
| Storage | 30GB gp3 EBS volume |
| IP | Elastic IP (static, persists on restart) |
| Queue | Supervisor-managed queue:work |
| Scheduler | Crontab — runs every minute |

### Server Setup Summary

#### 1. System packages
```bash
sudo apt update && sudo apt upgrade -y
sudo add-apt-repository ppa:ondrej/php -y
sudo apt install -y php8.4 php8.4-fpm php8.4-mysql php8.4-xml \
  php8.4-mbstring php8.4-curl php8.4-zip php8.4-gd php8.4-bcmath \
  php8.4-intl php8.4-tokenizer php8.4-cli nginx mysql-server supervisor
```

#### 2. Node.js and Composer
```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

#### 3. Clone and install
```bash
git clone https://github.com/Yaswanth-Kumar21/edutenant-erp.git /var/www/college-erp
cd /var/www/college-erp
composer install --no-dev --optimize-autoloader
npm install && npm run build
```

#### 4. Environment configuration
```bash
cp .env.example .env
# Edit .env with production values — never expose real credentials in README
php artisan key:generate
```

**Production `.env` checklist:**
```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=http://your-server-ip-or-domain

DB_DATABASE=edutenant_erp
DB_USERNAME=<your_db_user>        # do not hardcode here
DB_PASSWORD=<your_db_password>    # do not hardcode here

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

#### 5. Database
```bash
# Create database and user (run inside mysql)
sudo mysql
CREATE DATABASE edutenant_erp;
CREATE USER 'your_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON edutenant_erp.* TO 'your_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Run migrations and seed
php artisan migrate --force
php artisan db:seed --force
```

#### 6. Storage permissions
```bash
sudo usermod -a -G www-data ubuntu
sudo chown -R ubuntu:www-data /var/www/college-erp/storage
sudo chown -R ubuntu:www-data /var/www/college-erp/bootstrap/cache
sudo chmod -R 775 /var/www/college-erp/storage
sudo chmod -R 775 /var/www/college-erp/bootstrap/cache
php artisan storage:link
```

#### 7. Laravel caching
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### 8. Nginx configuration
Create `/etc/nginx/sites-available/college-erp`:

```nginx
server {
    listen 80;
    server_name your-server-ip-or-domain;
    root /var/www/college-erp/public;
    index index.php;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
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
```

#### 9. Supervisor (queue worker)
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
sudo supervisorctl reread && sudo supervisorctl update
sudo supervisorctl start edutenant-queue
```

#### 10. Cron scheduler
```bash
(crontab -l 2>/dev/null; echo "* * * * * cd /var/www/college-erp && php artisan schedule:run >> /dev/null 2>&1") | crontab -
```

### SSL (HTTPS) — optional

To enable HTTPS with a domain name:

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com
```

Then update `APP_URL` in `.env` and re-enable `URL::forceScheme('https')` in `AppServiceProvider.php`.

### AWS Security Group — required ports

| Port | Protocol | Source | Purpose |
|---|---|---|---|
| 22 | TCP | Your IP only | SSH access |
| 80 | TCP | 0.0.0.0/0 | HTTP |
| 443 | TCP | 0.0.0.0/0 | HTTPS |

> **Security note:** Restrict port 22 to your IP only. Never open SSH to 0.0.0.0/0.

### Instance management

The EC2 instance is stopped when not in use to conserve credits.

**To start the server:**
```
AWS Console → EC2 → Instances → edutenant-server → Instance state → Start
```
Wait ~1 minute for the instance to boot. All services (Nginx, PHP-FPM, MySQL, Supervisor) start automatically.

**To stop the server:**
```
AWS Console → EC2 → Instances → edutenant-server → Instance state → Stop
```

> All data, code, and configuration is preserved on the EBS volume when stopped.

---

## Security Features

- **Tenant isolation** — every query scoped to `tenant_id`; `TenantScoped::assertTenant()` enforces ownership on every model operation
- **Forced password change** — College Admins must change temporary password on first login (`must_change_password` flag + `ForcePasswordChange` middleware)
- **Password history** — stored in `password_histories` table (hashed, never plain text)
- **Impersonation audit** — every Super Admin impersonation logged in `impersonation_logs` with IP, start time, and end time
- **Email logs** — all outbound emails recorded in `email_logs` with delivery status and error messages
- **Razorpay signature verification** — payment webhook and callback signatures verified with `hash_hmac`
- **API token scoping** — Sanctum tokens limited to 30-day TTL; revoked on logout
- **Public registration disabled in production** — `RegisteredUserController` returns 403 when `APP_ENV=production`
- **SSH access restricted** — security group limits port 22 to known IP only
- **No secrets in repository** — `.env` is gitignored; all credentials managed via environment variables

### Files excluded from version control

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
```

---

## License

MIT License. See [LICENSE](LICENSE) for details.

---

*EduTenant ERP — Built with Laravel 13 · PHP 8.4 · MySQL 8 · Deployed on AWS EC2*
