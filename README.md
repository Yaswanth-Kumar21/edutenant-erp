<h1 align="center">
  <br>
  🎓 EduTenant ERP
  <br>
</h1>

<p align="center">
  <strong>A Multi-Tenant College ERP System built with Laravel 13</strong><br>
  Manage multiple colleges, students, fees, attendance, payroll, and more — all from a single platform.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" />
  <img src="https://img.shields.io/badge/PHP-8.3+-777BB4?style=for-the-badge&logo=php&logoColor=white" />
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" />
  <img src="https://img.shields.io/badge/TailwindCSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" />
  <img src="https://img.shields.io/badge/Vite-8.x-646CFF?style=for-the-badge&logo=vite&logoColor=white" />
  <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" />
</p>

---

## 👨‍💻 Author

**Abotula Yaswanth Kumar**

- 🌐 GitHub: [@Yaswanth-Kumar21](https://github.com/Yaswanth-Kumar21)
- 💼 LinkedIn: [abotula](https://www.linkedin.com/in/abotula/)
- 📧 Email: [yaswanth85003@gmail.com](mailto:yaswanth85003@gmail.com)

> This project was designed, architected, and built entirely by **Abotula Yaswanth Kumar** as a full-stack multi-tenant ERP system for educational institutions.

---

## 📌 Table of Contents

- [About the Project](#-about-the-project)
- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Architecture](#-architecture)
- [Role-Based UI System](#-role-based-ui-system)
- [Demo Credentials](#-demo-credentials)
- [Prerequisites](#-prerequisites)
- [Installation — Standard (php artisan serve)](#-installation--standard-php-artisan-serve)
- [Installation — Laravel Herd](#-installation--laravel-herd)
- [Environment Configuration](#-environment-configuration)
- [API Reference](#-api-reference)
- [PDF & Export Features](#-pdf--export-features)
- [Production Checklist](#-production-checklist)
- [License](#-license)

---

## 📖 About the Project

**EduTenant ERP** is a production-grade, multi-tenant College ERP system that allows a single platform (Super Admin) to onboard and manage multiple colleges. Each college operates in complete isolation with its own data, users, roles, and configurations — all within a single database using a tenant-scoped architecture.

The system supports 5 distinct roles — Super Admin, College Admin, Staff, Teacher, and Student — each with a fully themed, role-specific UI and dedicated feature set.

---

## ✨ Features

### 🏫 Multi-Tenancy
- Single platform, multiple colleges
- Tenant-scoped data isolation
- Super Admin can onboard/manage all tenants

### 👥 Role-Based Access Control
- 5 roles: `super_admin`, `college_admin`, `staff`, `teacher`, `student`
- Each role has a unique UI theme and dedicated layout
- Middleware-enforced route protection

### 🎓 Student Management
- Admissions with document uploads
- Student profiles, categories, branches, streams
- Certificate generation (PDF)
- Student portal with self-service access

### 💰 Fee Management
- Fee structure builder (per course/branch/year)
- Fee assignment to students
- Fee collection with Razorpay payment gateway integration
- Fee exemptions and waivers
- Transport fee management
- Fee receipts (PDF download)
- Fee dashboard with analytics

### 📅 Attendance
- Mark attendance by class/subject
- Attendance reports per student
- Excel/CSV export
- Student-facing attendance calendar

### 👨‍🏫 Staff & Payroll
- Staff profiles and management
- Payroll generation and slip download (PDF)
- Payroll Excel export

### 📊 Reports & Analytics
- Dashboard with charts (Chart.js)
- Income & Expense tracking
- Excel/CSV exports for all major modules
- PDF reports for students, attendance, payroll

### 🔔 Notifications
- In-app notification system
- Email queue support
- Notification dispatch controller

### 📱 Mobile API
- Laravel Sanctum token-based API
- Student dashboard, fees, attendance, notifications via API

### ⚙️ Settings & Profile
- Role-aware settings pages
- SMTP configuration (Super Admin)
- Razorpay integration settings
- Profile photo upload

---

## 🛠 Tech Stack

| Layer | Technology |
|---|---|
| Backend Framework | Laravel 13.x |
| Language | PHP 8.3+ |
| Database | MySQL 8.0 |
| Authentication | Laravel Breeze + Sanctum |
| Frontend | Blade + Tailwind CSS 3 + Alpine.js |
| Charts | Chart.js 4 |
| Icons | Font Awesome 6 |
| PDF Generation | barryvdh/laravel-dompdf v3.1 |
| Excel/CSV Export | maatwebsite/excel v3.1 |
| Payment Gateway | Razorpay v2.9 |
| Asset Bundling | Vite 8 |
| API Auth | Laravel Sanctum v4 |

---

## 🏗 Architecture

```
EduTenant ERP
├── Multi-Tenant (single DB, tenant_id scoped)
├── Role-Based Access (5 roles, middleware enforced)
├── 206 Routes registered
│   ├── Auth (Breeze) — ~10 routes
│   ├── Student Portal — 11 routes
│   ├── Admin Panel — ~150 routes
│   ├── Super Admin — ~10 routes
│   ├── Profile & Settings — 11 routes
│   └── API v1 (Sanctum) — 7 routes
├── PDF Downloads (5 types)
├── Excel/CSV Exports (6 types)
└── Mobile API (7 endpoints)
```

---

## 🎨 Role-Based UI System

Each role has a completely unique visual theme applied via CSS variables and `data-role` attributes on the body tag.

| Role | Sidebar Color | Primary Color | Background | Theme Name |
|---|---|---|---|---|
| `super_admin` | `#111827` | `#8B5CF6` | `#0B1120` | Dark Enterprise SaaS |
| `college_admin` | `#1E40AF` | `#2563EB` | `#F8FAFC` | Academic Blue |
| `staff` | `#0F766E` | `#14B8A6` | `#F0FDFA` | Teal Operational |
| `teacher` | `#5C4033` | `#8B6B4A` | `#FFF8F0` | Coffee / Warm Gold |
| `student` | `#312E81` | `#8B5CF6` | `#ffffff` | Purple Modern |

---

## 🔐 Demo Credentials

### Super Admin
| Email | Password |
|---|---|
| superadmin@erp.com | password |

### College 1 — Sri Venkateswara Degree College (Tirupati)
| Role | Email | Password |
|---|---|---|
| College Admin | admin@svc.edu | password |
| Staff | staff@svc.edu | password |
| Teacher | ravi.kumar@svc.edu | password |
| Student | ravi.teja@svc.student.edu | 81svc0001 |
| Student | sravani.reddy@svc.student.edu | 81svc0002 |
| Student | mahesh.babu@svc.student.edu | 81svc0003 |

### College 2 — Sai Chaitanya Degree College (Vijayawada)
| Role | Email | Password |
|---|---|---|
| College Admin | admin@scc.edu | password |
| Staff | staff@scc.edu | password |
| Teacher | ravi.kumar@scc.edu | password |
| Student | ravi.teja@scc.student.edu | 81scc0001 |

### College 3 — Narayana Degree College (Guntur)
| Role | Email | Password |
|---|---|---|
| College Admin | admin@ndc.edu | password |
| Staff | staff@ndc.edu | password |
| Teacher | ravi.kumar@ndc.edu | password |
| Student | ravi.teja@ndc.student.edu | 81ndc0001 |

---

## 📋 Prerequisites

Make sure you have the following installed:

- **PHP** >= 8.3
- **Composer** >= 2.x
- **Node.js** >= 18.x & **npm** >= 9.x
- **MySQL** >= 8.0
- **Git**

**For Herd users:**
- [Laravel Herd](https://herd.laravel.com/) (includes PHP, Nginx, and MySQL)

---

## 🚀 Installation — Standard (php artisan serve)

Follow these steps to run the project using the built-in PHP development server.

### 1. Clone the Repository

```bash
git clone https://github.com/Yaswanth-Kumar21/edutenant-erp.git
cd edutenant-erp
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node Dependencies

```bash
npm install
```

### 4. Set Up Environment File

```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configure the Database

Open `.env` and update the database settings:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=edutenant_erp
DB_USERNAME=root
DB_PASSWORD=your_password
```

Create the database in MySQL:

```sql
CREATE DATABASE edutenant_erp;
```

### 6. Run Migrations & Seed Demo Data

```bash
php artisan migrate:fresh --seed
```

### 7. Build Frontend Assets

```bash
npm run build
```

### 8. Create Storage Symlink

```bash
php artisan storage:link
```

### 9. Start the Development Server

```bash
php artisan serve
```

Visit: **http://localhost:8000**

### 10. (Optional) Start the Queue Worker

In a separate terminal, run:

```bash
php artisan queue:work --tries=3
```

---

## 🐎 Installation — Laravel Herd

Laravel Herd provides a zero-config local development environment for macOS and Windows. Follow these steps to run EduTenant ERP with Herd.

### 1. Install Laravel Herd

Download and install from: **https://herd.laravel.com/**

Herd automatically manages PHP, Nginx, and provides `.test` domains.

### 2. Clone the Repository into Herd's Sites Directory

**macOS:**
```bash
cd ~/Herd
git clone https://github.com/Yaswanth-Kumar21/edutenant-erp.git
cd edutenant-erp
```

**Windows:**
```cmd
cd %USERPROFILE%\Herd
git clone https://github.com/Yaswanth-Kumar21/edutenant-erp.git
cd edutenant-erp
```

### 3. Install PHP Dependencies

```bash
composer install
```

### 4. Install Node Dependencies

```bash
npm install
```

### 5. Set Up Environment File

**macOS / Git Bash:**
```bash
cp .env.example .env
php artisan key:generate
```

**Windows CMD:**
```cmd
copy .env.example .env
php artisan key:generate
```

### 6. Configure the Database

Open `.env` and update:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=edutenant_erp
DB_USERNAME=root
DB_PASSWORD=
```

> Herd's bundled MySQL has no password by default. Use **TablePlus** (bundled with Herd) or any MySQL client to create the database:
> ```sql
> CREATE DATABASE edutenant_erp;
> ```

### 7. Run Migrations & Seed Demo Data

```bash
php artisan migrate:fresh --seed
```

### 8. Build Frontend Assets

```bash
npm run build
```

### 9. Create Storage Symlink

```bash
php artisan storage:link
```

### 10. Access the App

Herd automatically serves the project at:

**http://edutenant-erp.test**

> Make sure the folder name matches the `.test` domain. If you cloned into a folder named `edutenant-erp`, the URL will be `http://edutenant-erp.test`.

### 11. (Optional) Start the Queue Worker

```bash
php artisan queue:work --tries=3
```

---

## ⚙️ Environment Configuration

Key `.env` variables to configure:

```env
# App
APP_NAME="EduTenant ERP"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000   # or http://edutenant-erp.test for Herd

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=edutenant_erp
DB_USERNAME=root
DB_PASSWORD=

# Mail (optional for local dev — defaults to log driver)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS="noreply@edutenant.com"
MAIL_FROM_NAME="EduTenant ERP"

# Razorpay (optional — for payment gateway)
RAZORPAY_KEY=your_razorpay_key
RAZORPAY_SECRET=your_razorpay_secret
```

---

## 📡 API Reference

Base URL: `/api/v1/`

All authenticated endpoints require: `Authorization: Bearer {token}`

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| POST | `/api/v1/login` | Public | Login and get token |
| POST | `/api/v1/logout` | Bearer | Logout |
| GET | `/api/v1/me` | Bearer | Get authenticated user |
| GET | `/api/v1/student/dashboard` | Bearer + student | Student dashboard data |
| GET | `/api/v1/student/fees` | Bearer + student | Student fee history |
| GET | `/api/v1/student/attendance` | Bearer + student | Student attendance |
| GET | `/api/v1/student/notifications` | Bearer + student | Student notifications |

---

## 📄 PDF & Export Features

### PDF Downloads

| URL | Description |
|---|---|
| `/admin/pdf/fee-receipt/{payment}` | Fee Receipt |
| `/admin/pdf/payroll-slip/{payroll}` | Payroll Slip |
| `/admin/pdf/student-report/{student}` | Student Report |
| `/admin/pdf/admission/{student}` | Admission Receipt |
| `/admin/pdf/attendance-report` | Attendance Report |

### Excel / CSV Exports

Append `?format=csv` for CSV output. Default is `.xlsx`.

| URL | Description |
|---|---|
| `/admin/exports/students` | Students list |
| `/admin/exports/fee-payments` | Fee payments |
| `/admin/exports/attendance` | Attendance records |
| `/admin/exports/payroll` | Payroll data |
| `/admin/exports/expenses` | Expenses |
| `/admin/exports/income` | Income records |

---

## ✅ Production Checklist

Before deploying to production:

```bash
# Set environment to production
APP_ENV=production
APP_DEBUG=false

# Cache everything
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run queue worker as a daemon (use Supervisor)
php artisan queue:work --tries=3 --daemon

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

- [ ] Set `APP_DEBUG=false`
- [ ] Configure real SMTP mail server
- [ ] Add Razorpay live keys
- [ ] Set up Supervisor for queue worker
- [ ] Configure proper file permissions on `storage/` and `bootstrap/cache/`
- [ ] Enable HTTPS

---

## 📁 Project Structure

```
edutenant-erp/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/          # College admin, staff, teacher controllers
│   │   │   └── Fees/       # Fee management controllers
│   │   ├── Api/            # Mobile API controllers
│   │   └── Auth/           # Authentication controllers
│   ├── Models/             # Eloquent models
│   ├── Exports/            # Excel export classes
│   └── Helpers/            # Utility helpers
├── database/
│   ├── migrations/         # All database migrations
│   └── seeders/            # Demo data seeders
├── resources/
│   └── views/
│       ├── layouts/        # Role-specific layouts
│       ├── admin/          # Admin panel views
│       └── student/        # Student portal views
├── routes/
│   ├── web.php             # Main web routes
│   ├── api.php             # API routes
│   └── modules/            # Modular route files
└── public/                 # Web root
```

---

## 📜 License

This project is open-sourced under the [MIT License](LICENSE).

---

<p align="center">
  Built with ❤️ by <strong>Abotula Yaswanth Kumar</strong><br>
  <a href="https://github.com/Yaswanth-Kumar21">GitHub</a> •
  <a href="https://www.linkedin.com/in/abotula/">LinkedIn</a> •
  <a href="mailto:yaswanth85003@gmail.com">Email</a>
</p>
