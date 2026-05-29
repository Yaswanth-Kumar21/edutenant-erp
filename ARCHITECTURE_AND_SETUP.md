# EduTenant ERP — Architecture & Setup Guide
## FINAL ENTERPRISE PRODUCTION BUILD — Day 5 + UI Refactor Complete

---

## Tech Stack
- **Laravel** 13.9 / PHP 8.4
- **MySQL** (multi-tenant, single database)
- **Bootstrap** 5.3 + Font Awesome 6 + Chart.js 4
- **Laravel Breeze** (Auth scaffolding)
- **Laravel Sanctum** v4.3 (API token auth)
- **barryvdh/laravel-dompdf** v3.1 (PDF generation)
- **maatwebsite/excel** v3.1 (Excel/CSV exports)
- **razorpay/razorpay** v2.9 (Payment gateway)
- **Vite** (Frontend asset bundling)

---

## Role-Based UI System

Each role has its own **dedicated layout** with a unique theme. All pages for a role use the SAME layout — no switching.

| Role | Layout | Sidebar Color | Primary | Background | Theme |
|---|---|---|---|---|---|
| super_admin | `super-admin-app` | `#111827` | `#8B5CF6` | `#0B1120` | Dark Enterprise SaaS |
| college_admin | `app` | `#1E40AF` | `#2563EB` | `#F8FAFC` | Academic Blue |
| staff | `app` (data-role=staff) | `#0F766E` | `#14B8A6` | `#F0FDFA` | Teal Operational |
| teacher | `app` (data-role=teacher) | `#5C4033` | `#8B6B4A` | `#FFF8F0` | Coffee/Warm Gold |
| student | `student-app` | `#312E81` | `#8B5CF6` | `#ffffff` | Purple Modern |

### How Role Theming Works
`app.blade.php` applies `data-role="{{ auth()->user()->role->name }}"` to the body tag.
CSS variables are overridden per role:
```css
body[data-role="staff"]   { --primary: #14B8A6; --sidebar-bg: #0F766E; --bg: #F0FDFA; }
body[data-role="teacher"] { --primary: #8B6B4A; --sidebar-bg: #5C4033; --bg: #FFF8F0; }
```
This means ALL admin module pages (students, fees, attendance, etc.) automatically get the correct role theme.

---

## Layout Files

| File | Used By | Theme |
|---|---|---|
| `layouts/app.blade.php` | college_admin, staff, teacher | Role-aware via CSS variables |
| `layouts/super-admin-app.blade.php` | super_admin | Dark enterprise (#0B1120) |
| `layouts/student-app.blade.php` | student | Purple (#312E81 sidebar) |
| `layouts/guest.blade.php` | Login/register pages | Clean white |

---

## Profile & Settings System

### Admin Profile (`/admin/profile`)
- Photo upload
- Personal information (name, email, phone)
- Change password
- Institution information
- Account info + recent activity

### Admin Settings (`/admin/settings`)
- **College Admin**: College info, notifications, security
- **Super Admin**: Platform status, SMTP config, integrations (Razorpay, SMS/WhatsApp), notifications, security
- **Staff/Teacher**: Notifications, security

### Student Settings (`/student/settings`)
- Theme preference (light/dark)
- Notification preferences
- Change password
- Account security info

---

## Routes Summary

| Route Group | Count | Middleware |
|---|---|---|
| Auth (Breeze) | ~10 | — |
| Dashboard | 1 | auth, tenant, role |
| Student Portal | 11 | auth, tenant, role:student |
| Admin Panel | ~150 | auth, tenant, role:college_admin,staff,teacher |
| Profile & Settings | 11 | auth |
| Super Admin | ~10 | auth, role:super_admin |
| API v1 | 7 | — / sanctum |
| Webhook | 1 | — |
| **Total** | **206** | |

---

## Student Portal Pages

| URL | Page |
|---|---|
| /student/dashboard | Dashboard |
| /student/fees | Fee History |
| /student/fees/{id} | Fee Receipt Detail |
| /student/fees/{id}/receipt | Download PDF |
| /student/attendance | Attendance Calendar |
| /student/certificates | Certificates |
| /student/profile | My Profile |
| /student/profile/password | Change Password |
| /student/settings | Settings |

---

## PDF Downloads

| URL | PDF |
|---|---|
| /admin/pdf/fee-receipt/{payment} | Fee Receipt |
| /admin/pdf/payroll-slip/{payroll} | Payroll Slip |
| /admin/pdf/student-report/{student} | Student Report |
| /admin/pdf/admission/{student} | Admission Receipt |
| /admin/pdf/attendance-report | Attendance Report |

---

## Excel/CSV Exports

| URL | Export |
|---|---|
| /admin/exports/students | Students |
| /admin/exports/fee-payments | Fee Payments |
| /admin/exports/attendance | Attendance |
| /admin/exports/payroll | Payroll |
| /admin/exports/expenses | Expenses |
| /admin/exports/income | Income |

Add `?format=csv` for CSV, default is xlsx.

---

## Mobile API (Sanctum)

Base URL: `/api/v1/`

| Method | Endpoint | Auth |
|---|---|---|
| POST | /api/v1/login | Public |
| POST | /api/v1/logout | Bearer |
| GET | /api/v1/me | Bearer |
| GET | /api/v1/student/dashboard | Bearer+student |
| GET | /api/v1/student/fees | Bearer+student |
| GET | /api/v1/student/attendance | Bearer+student |
| GET | /api/v1/student/notifications | Bearer+student |

---

## Demo Login Credentials

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
| Student | pooja.sharma@svc.student.edu | 81svc0004 |
| Student | aakash.kumar@svc.student.edu | 81svc0005 |

### College 2 — Sai Chaitanya Degree College (Vijayawada)
| Role | Email | Password |
|---|---|---|
| College Admin | admin@scc.edu | password |
| Staff | staff@scc.edu | password |
| Teacher | ravi.kumar@scc.edu | password |
| Student | ravi.teja@scc.student.edu | 81scc0001 |
| Student | sravani.reddy@scc.student.edu | 81scc0002 |

### College 3 — Narayana Degree College (Guntur)
| Role | Email | Password |
|---|---|---|
| College Admin | admin@ndc.edu | password |
| Staff | staff@ndc.edu | password |
| Teacher | ravi.kumar@ndc.edu | password |
| Student | ravi.teja@ndc.student.edu | 81ndc0001 |

---

## Setup Commands

```bash
# Fresh install
composer install
cp .env.example .env
php artisan key:generate

# Configure .env with MySQL credentials, then:
php artisan migrate:fresh --seed

# Build frontend assets
npm install && npm run build

# Start server
php artisan serve
# → http://localhost:8000

# Process email queue (separate terminal)
php artisan queue:work --tries=3

# Clear all caches
php artisan optimize:clear

# Production optimization
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

---

## Final QA Audit Report

### Issues Fixed in UI Refactor Phase

| Issue | Fix Applied |
|---|---|
| Staff/Teacher dashboards used separate layouts | Moved to `layouts.app` with `data-role` CSS overrides |
| All admin module pages used same indigo theme regardless of role | CSS variable overrides per `data-role` attribute |
| Profile page used Breeze `profile.edit` with wrong layout | Built `admin/profile/index.blade.php` using `layouts.app` |
| No settings pages existed | Built `admin/settings/index.blade.php` (role-aware tabs) |
| No student settings page | Built `student/settings.blade.php` |
| Super-admin tenant views used `layouts.app` | Switched to `layouts.super-admin-app` |
| Topnav profile link pointed to Breeze `profile.edit` | Updated to `admin.profile.index` |
| Topnav settings link was `#` (broken) | Updated to `admin.settings.index` |
| Sidebar had no profile/settings links | Added to all role menus in `sidebar.blade.php` |
| Super-admin sidebar had no profile/settings | Added to `super-admin-app.blade.php` |
| Dark mode had invisible text in tables/forms | Added comprehensive dark mode CSS overrides |
| Teacher theme was orange (wrong) | Replaced with coffee/warm gold (`#5C4033`, `#8B6B4A`) |
| Super-admin colors were wrong | Updated to `#0B1120` bg, `#111827` sidebar, `#8B5CF6` primary |
| Student sidebar was dark navy | Updated to purple `#312E81` |
| Hardcoded sidebar color in CSS | Now uses `var(--sidebar-bg)` CSS variable |
| Breadcrumb "Home" same for all roles | Now role-aware (Platform/Staff Portal/Faculty Portal/Home) |

### Final Status

| Module | Status |
|---|---|
| Layout consistency (all roles) | ✅ FIXED |
| Profile pages (all roles) | ✅ BUILT |
| Settings pages (all roles) | ✅ BUILT |
| Student settings | ✅ BUILT |
| Dark mode text visibility | ✅ FIXED |
| Role-specific themes | ✅ APPLIED |
| Super-admin dark enterprise theme | ✅ APPLIED |
| Teacher coffee/warm gold theme | ✅ APPLIED |
| Staff teal theme | ✅ APPLIED |
| Student purple theme | ✅ APPLIED |
| Navigation consistency | ✅ FIXED |
| 206 routes registered | ✅ |
| Zero syntax errors | ✅ |
| All caches built | ✅ |
| Frontend assets compiled | ✅ |

### Production Checklist

- [x] All packages installed
- [x] All migrations run
- [x] Storage symlink created
- [x] Frontend assets built (`npm run build`)
- [x] Config cached
- [x] Routes cached
- [x] Views cached
- [x] Zero syntax errors
- [x] 206 routes registered
- [x] Profile pages for all roles
- [x] Settings pages for all roles
- [x] Role-specific UI themes
- [ ] Set `APP_DEBUG=false` for production
- [ ] Configure real SMTP mail server
- [ ] Add Razorpay live keys
- [ ] Run `php artisan queue:work` for emails
