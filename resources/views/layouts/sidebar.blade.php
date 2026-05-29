<aside id="sidebar">

    {{-- ── Brand ──────────────────────────────────────────────────────── --}}
    <div class="sidebar-brand d-flex align-items-center justify-content-between px-3 py-3"
         style="border-bottom:1px solid rgba(255,255,255,0.08);min-height:64px;">
        <div class="d-flex align-items-center gap-2 brand-full">
            <div class="brand-icon d-flex align-items-center justify-content-center rounded-2"
                 style="width:38px;height:38px;background:rgba(255,255,255,0.15);flex-shrink:0;">
                <i class="fa-solid fa-graduation-cap text-white" style="font-size:1.1rem;"></i>
            </div>
            <div class="brand-text overflow-hidden">
                <div class="text-white lh-1"
                     style="font-size:0.9rem;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:160px;">
                    @if(auth()->user()->isSuperAdmin())
                        EduTenant ERP
                    @else
                        {{ auth()->user()->tenant?->name ?? 'EduTenant ERP' }}
                    @endif
                </div>
                <div style="font-size:0.68rem;color:rgba(255,255,255,0.45);margin-top:2px;">
                    {{ auth()->user()->getRoleDisplayName() }}
                </div>
            </div>
        </div>
        <button id="sidebar-toggle" class="btn btn-sm p-1 border-0"
                style="color:rgba(255,255,255,0.6);background:transparent;flex-shrink:0;">
            <i class="fa-solid fa-bars" style="font-size:1rem;"></i>
        </button>
    </div>

    {{-- ── Navigation ──────────────────────────────────────────────────── --}}
    <nav class="sidebar-nav flex-1 py-2" style="overflow-y:auto;overflow-x:hidden;">

        @php $user = auth()->user(); @endphp

        {{-- ── SUPER ADMIN MENU ──────────────────────────────────────── --}}
        @if($user->isSuperAdmin())

            <div class="sidebar-section">Platform</div>

            <a href="{{ route('dashboard') }}"
               class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-gauge sidebar-icon"></i>
                <span class="sidebar-label">Dashboard</span>
            </a>

            <a href="{{ route('super.tenants.index') }}"
               class="sidebar-link {{ request()->routeIs('super.tenants.index') ? 'active' : '' }}">
                <i class="fa-solid fa-building sidebar-icon"></i>
                <span class="sidebar-label">Institutions</span>
            </a>

            <a href="{{ route('super.tenants.create') }}"
               class="sidebar-link {{ request()->routeIs('super.tenants.create') ? 'active' : '' }}">
                <i class="fa-solid fa-plus-circle sidebar-icon"></i>
                <span class="sidebar-label">Add Institution</span>
            </a>

            <div class="sidebar-section mt-2">Account</div>

            <a href="{{ route('admin.profile.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-shield sidebar-icon"></i>
                <span class="sidebar-label">My Profile</span>
            </a>

            <a href="{{ route('admin.settings.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <i class="fa-solid fa-gear sidebar-icon"></i>
                <span class="sidebar-label">Settings</span>
            </a>

        {{-- ── COLLEGE ADMIN MENU ─────────────────────────────────────── --}}
        @elseif($user->isCollegeAdmin())

            <a href="{{ route('dashboard') }}"
               class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-gauge sidebar-icon"></i>
                <span class="sidebar-label">Dashboard</span>
            </a>

            <div class="sidebar-section mt-1">Academic</div>

            <div class="sidebar-group">
                <button class="sidebar-link sidebar-collapse-btn w-100 text-start border-0
                    {{ request()->routeIs('admin.setup.*') ? 'active' : '' }}"
                    data-bs-toggle="collapse" data-bs-target="#menu-setup"
                    aria-expanded="{{ request()->routeIs('admin.setup.*') ? 'true' : 'false' }}">
                    <i class="fa-solid fa-building sidebar-icon"></i>
                    <span class="sidebar-label">College Setup</span>
                    <i class="fa-solid fa-chevron-right sidebar-arrow ms-auto"></i>
                </button>
                <div id="menu-setup" class="collapse {{ request()->routeIs('admin.setup.*') ? 'show' : '' }}">
                    <a href="{{ route('admin.setup.streams.index') }}" class="sidebar-sub-link {{ request()->routeIs('admin.setup.streams*') ? 'active' : '' }}"><i class="fa-solid fa-layer-group me-2" style="font-size:0.75rem;"></i> Streams</a>
                    <a href="{{ route('admin.setup.courses.index') }}" class="sidebar-sub-link {{ request()->routeIs('admin.setup.courses*') ? 'active' : '' }}"><i class="fa-solid fa-book me-2" style="font-size:0.75rem;"></i> Courses</a>
                    <a href="{{ route('admin.setup.branches.index') }}" class="sidebar-sub-link {{ request()->routeIs('admin.setup.branches*') ? 'active' : '' }}"><i class="fa-solid fa-code-branch me-2" style="font-size:0.75rem;"></i> Branches</a>
                    <a href="{{ route('admin.setup.academic-years.index') }}" class="sidebar-sub-link {{ request()->routeIs('admin.setup.academic-years*') ? 'active' : '' }}"><i class="fa-solid fa-calendar me-2" style="font-size:0.75rem;"></i> Academic Years</a>
                </div>
            </div>

            <a href="{{ route('admin.students.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.students*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-graduate sidebar-icon"></i>
                <span class="sidebar-label">Students</span>
            </a>

            <a href="{{ route('admin.admissions.create') }}"
               class="sidebar-link {{ request()->routeIs('admin.admissions*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-plus sidebar-icon"></i>
                <span class="sidebar-label">New Admission</span>
            </a>

            <div class="sidebar-section mt-1">Finance</div>

            <div class="sidebar-group">
                <button class="sidebar-link sidebar-collapse-btn w-100 text-start border-0
                    {{ request()->routeIs('admin.fees.*') ? 'active' : '' }}"
                    data-bs-toggle="collapse" data-bs-target="#menu-fees"
                    aria-expanded="{{ request()->routeIs('admin.fees.*') ? 'true' : 'false' }}">
                    <i class="fa-solid fa-indian-rupee-sign sidebar-icon"></i>
                    <span class="sidebar-label">Fee Management</span>
                    <i class="fa-solid fa-chevron-right sidebar-arrow ms-auto"></i>
                </button>
                <div id="menu-fees" class="collapse {{ request()->routeIs('admin.fees.*') ? 'show' : '' }}">
                    <a href="{{ route('admin.fees.dashboard') }}" class="sidebar-sub-link {{ request()->routeIs('admin.fees.dashboard') ? 'active' : '' }}"><i class="fa-solid fa-gauge me-2" style="font-size:0.75rem;"></i> Fee Dashboard</a>
                    <a href="{{ route('admin.fees.payments.create') }}" class="sidebar-sub-link {{ request()->routeIs('admin.fees.payments.create') ? 'active' : '' }}"><i class="fa-solid fa-hand-holding-dollar me-2" style="font-size:0.75rem;"></i> Collect Fee</a>
                    <a href="{{ route('admin.fees.payments.index') }}" class="sidebar-sub-link {{ request()->routeIs('admin.fees.payments.index') ? 'active' : '' }}"><i class="fa-solid fa-clock-rotate-left me-2" style="font-size:0.75rem;"></i> Payment History</a>
                    <a href="{{ route('admin.fees.structures.index') }}" class="sidebar-sub-link {{ request()->routeIs('admin.fees.structures*') ? 'active' : '' }}"><i class="fa-solid fa-layer-group me-2" style="font-size:0.75rem;"></i> Fee Structures</a>
                    <a href="{{ route('admin.fees.types.index') }}" class="sidebar-sub-link {{ request()->routeIs('admin.fees.types*') ? 'active' : '' }}"><i class="fa-solid fa-tags me-2" style="font-size:0.75rem;"></i> Fee Types</a>
                    <a href="{{ route('admin.fees.exemptions.index') }}" class="sidebar-sub-link {{ request()->routeIs('admin.fees.exemptions*') ? 'active' : '' }}"><i class="fa-solid fa-hand-holding-heart me-2" style="font-size:0.75rem;"></i> Exemptions</a>
                    <a href="{{ route('admin.fees.transport.index') }}" class="sidebar-sub-link {{ request()->routeIs('admin.fees.transport*') ? 'active' : '' }}"><i class="fa-solid fa-bus me-2" style="font-size:0.75rem;"></i> Transport</a>
                </div>
            </div>

            <a href="{{ route('admin.expenses.index') }}" class="sidebar-link {{ request()->routeIs('admin.expenses*') ? 'active' : '' }}">
                <i class="fa-solid fa-receipt sidebar-icon"></i><span class="sidebar-label">Expenses</span>
            </a>
            <a href="{{ route('admin.incomes.index') }}" class="sidebar-link {{ request()->routeIs('admin.incomes*') ? 'active' : '' }}">
                <i class="fa-solid fa-arrow-trend-up sidebar-icon"></i><span class="sidebar-label">Income</span>
            </a>

            <div class="sidebar-section mt-1">Operations</div>

            <div class="sidebar-group">
                <button class="sidebar-link sidebar-collapse-btn w-100 text-start border-0 {{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}"
                    data-bs-toggle="collapse" data-bs-target="#menu-attendance"
                    aria-expanded="{{ request()->routeIs('admin.attendance.*') ? 'true' : 'false' }}">
                    <i class="fa-solid fa-calendar-check sidebar-icon"></i>
                    <span class="sidebar-label">Attendance</span>
                    <i class="fa-solid fa-chevron-right sidebar-arrow ms-auto"></i>
                </button>
                <div id="menu-attendance" class="collapse {{ request()->routeIs('admin.attendance.*') ? 'show' : '' }}">
                    <a href="{{ route('admin.attendance.students') }}" class="sidebar-sub-link {{ request()->routeIs('admin.attendance.students') && !request()->routeIs('admin.attendance.students.*') ? 'active' : '' }}"><i class="fa-solid fa-user-graduate me-2" style="font-size:0.75rem;"></i> Mark Students</a>
                    <a href="{{ route('admin.attendance.students.report') }}" class="sidebar-sub-link {{ request()->routeIs('admin.attendance.students.report') ? 'active' : '' }}"><i class="fa-solid fa-chart-bar me-2" style="font-size:0.75rem;"></i> Student Report</a>
                    <a href="{{ route('admin.attendance.students.analytics') }}" class="sidebar-sub-link {{ request()->routeIs('admin.attendance.students.analytics') ? 'active' : '' }}"><i class="fa-solid fa-chart-line me-2" style="font-size:0.75rem;"></i> Analytics</a>
                    <a href="{{ route('admin.attendance.staff') }}" class="sidebar-sub-link {{ request()->routeIs('admin.attendance.staff') && !request()->routeIs('admin.attendance.staff.*') ? 'active' : '' }}"><i class="fa-solid fa-users me-2" style="font-size:0.75rem;"></i> Mark Staff</a>
                    <a href="{{ route('admin.attendance.staff.report') }}" class="sidebar-sub-link {{ request()->routeIs('admin.attendance.staff.report') ? 'active' : '' }}"><i class="fa-solid fa-file-lines me-2" style="font-size:0.75rem;"></i> Staff Report</a>
                </div>
            </div>

            <div class="sidebar-group">
                <button class="sidebar-link sidebar-collapse-btn w-100 text-start border-0 {{ request()->routeIs('admin.staff*') || request()->routeIs('admin.payroll*') ? 'active' : '' }}"
                    data-bs-toggle="collapse" data-bs-target="#menu-staff"
                    aria-expanded="{{ request()->routeIs('admin.staff*') || request()->routeIs('admin.payroll*') ? 'true' : 'false' }}">
                    <i class="fa-solid fa-users sidebar-icon"></i>
                    <span class="sidebar-label">Staff & Payroll</span>
                    <i class="fa-solid fa-chevron-right sidebar-arrow ms-auto"></i>
                </button>
                <div id="menu-staff" class="collapse {{ request()->routeIs('admin.staff*') || request()->routeIs('admin.payroll*') ? 'show' : '' }}">
                    <a href="{{ route('admin.staff.index') }}" class="sidebar-sub-link {{ request()->routeIs('admin.staff.index') ? 'active' : '' }}"><i class="fa-solid fa-users me-2" style="font-size:.75rem;"></i> All Staff</a>
                    <a href="{{ route('admin.staff.leaves') }}" class="sidebar-sub-link {{ request()->routeIs('admin.staff.leaves*') ? 'active' : '' }}"><i class="fa-solid fa-calendar-xmark me-2" style="font-size:.75rem;"></i> Leave Requests</a>
                    <a href="{{ route('admin.payroll.index') }}" class="sidebar-sub-link {{ request()->routeIs('admin.payroll*') ? 'active' : '' }}"><i class="fa-solid fa-file-invoice-dollar me-2" style="font-size:.75rem;"></i> Payroll</a>
                </div>
            </div>

            <a href="{{ route('admin.messages.index') }}" class="sidebar-link {{ request()->routeIs('admin.messages*') ? 'active' : '' }}">
                <i class="fa-solid fa-envelope sidebar-icon"></i><span class="sidebar-label">Messages</span>
            </a>

            <div class="sidebar-group">
                <button class="sidebar-link sidebar-collapse-btn w-100 text-start border-0 {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"
                    data-bs-toggle="collapse" data-bs-target="#menu-reports"
                    aria-expanded="{{ request()->routeIs('admin.reports.*') ? 'true' : 'false' }}">
                    <i class="fa-solid fa-chart-bar sidebar-icon"></i>
                    <span class="sidebar-label">Reports</span>
                    <i class="fa-solid fa-chevron-right sidebar-arrow ms-auto"></i>
                </button>
                <div id="menu-reports" class="collapse {{ request()->routeIs('admin.reports.*') ? 'show' : '' }}">
                    <a href="{{ route('admin.reports.daily') }}" class="sidebar-sub-link {{ request()->routeIs('admin.reports.daily') ? 'active' : '' }}"><i class="fa-solid fa-calendar-day me-2" style="font-size:0.75rem;"></i> Daily</a>
                    <a href="{{ route('admin.reports.annual') }}" class="sidebar-sub-link {{ request()->routeIs('admin.reports.annual') ? 'active' : '' }}"><i class="fa-solid fa-calendar me-2" style="font-size:0.75rem;"></i> Annual</a>
                    <a href="{{ route('admin.reports.students') }}" class="sidebar-sub-link {{ request()->routeIs('admin.reports.students') ? 'active' : '' }}"><i class="fa-solid fa-user-graduate me-2" style="font-size:0.75rem;"></i> Students</a>
                    <a href="{{ route('admin.reports.fees') }}" class="sidebar-sub-link {{ request()->routeIs('admin.reports.fees') ? 'active' : '' }}"><i class="fa-solid fa-file-invoice-dollar me-2" style="font-size:0.75rem;"></i> Fees</a>
                </div>
            </div>

            <div class="sidebar-section mt-1">Account</div>
            <a href="{{ route('admin.profile.index') }}" class="sidebar-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-circle sidebar-icon"></i><span class="sidebar-label">My Profile</span>
            </a>
            <a href="{{ route('admin.settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <i class="fa-solid fa-gear sidebar-icon"></i><span class="sidebar-label">Settings</span>
            </a>

        {{-- ── STAFF MENU ─────────────────────────────────────────────── --}}
        @elseif($user->isStaff())

            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-gauge sidebar-icon"></i><span class="sidebar-label">Dashboard</span>
            </a>

            <div class="sidebar-section mt-1">Admissions</div>
            <a href="{{ route('admin.admissions.create') }}" class="sidebar-link {{ request()->routeIs('admin.admissions*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-plus sidebar-icon"></i><span class="sidebar-label">New Admission</span>
            </a>
            <a href="{{ route('admin.students.index') }}" class="sidebar-link {{ request()->routeIs('admin.students*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-graduate sidebar-icon"></i><span class="sidebar-label">Students</span>
            </a>

            <div class="sidebar-section mt-1">Fees</div>
            <a href="{{ route('admin.fees.payments.create') }}" class="sidebar-link {{ request()->routeIs('admin.fees.payments.create') ? 'active' : '' }}">
                <i class="fa-solid fa-hand-holding-dollar sidebar-icon"></i><span class="sidebar-label">Collect Fee</span>
            </a>
            <a href="{{ route('admin.fees.payments.index') }}" class="sidebar-link {{ request()->routeIs('admin.fees.payments.index') ? 'active' : '' }}">
                <i class="fa-solid fa-clock-rotate-left sidebar-icon"></i><span class="sidebar-label">Payment History</span>
            </a>

            <div class="sidebar-section mt-1">Attendance</div>
            <a href="{{ route('admin.attendance.students') }}" class="sidebar-link {{ request()->routeIs('admin.attendance.students') && !request()->routeIs('admin.attendance.students.*') ? 'active' : '' }}">
                <i class="fa-solid fa-calendar-check sidebar-icon"></i><span class="sidebar-label">Mark Attendance</span>
            </a>
            <a href="{{ route('admin.attendance.students.report') }}" class="sidebar-link {{ request()->routeIs('admin.attendance.students.report') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-bar sidebar-icon"></i><span class="sidebar-label">Attendance Report</span>
            </a>

            <div class="sidebar-section mt-1">Communication</div>
            <a href="{{ route('admin.messages.index') }}" class="sidebar-link {{ request()->routeIs('admin.messages*') ? 'active' : '' }}">
                <i class="fa-solid fa-envelope sidebar-icon"></i><span class="sidebar-label">Messages</span>
            </a>

            <div class="sidebar-section mt-1">Account</div>
            <a href="{{ route('admin.profile.index') }}" class="sidebar-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-circle sidebar-icon"></i><span class="sidebar-label">My Profile</span>
            </a>
            <a href="{{ route('admin.settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <i class="fa-solid fa-gear sidebar-icon"></i><span class="sidebar-label">Settings</span>
            </a>

        {{-- ── TEACHER MENU ────────────────────────────────────────────── --}}
        @elseif($user->isTeacher())

            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-gauge sidebar-icon"></i><span class="sidebar-label">Dashboard</span>
            </a>

            <div class="sidebar-section mt-1">Classroom</div>
            <a href="{{ route('admin.students.index') }}" class="sidebar-link {{ request()->routeIs('admin.students*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-graduate sidebar-icon"></i><span class="sidebar-label">My Students</span>
            </a>
            <a href="{{ route('admin.attendance.students') }}" class="sidebar-link {{ request()->routeIs('admin.attendance.students') && !request()->routeIs('admin.attendance.students.*') ? 'active' : '' }}">
                <i class="fa-solid fa-calendar-check sidebar-icon"></i><span class="sidebar-label">Mark Attendance</span>
            </a>
            <a href="{{ route('admin.attendance.students.report') }}" class="sidebar-link {{ request()->routeIs('admin.attendance.students.report') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-bar sidebar-icon"></i><span class="sidebar-label">Attendance Report</span>
            </a>
            <a href="{{ route('admin.attendance.students.analytics') }}" class="sidebar-link {{ request()->routeIs('admin.attendance.students.analytics') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line sidebar-icon"></i><span class="sidebar-label">Analytics</span>
            </a>

            <div class="sidebar-section mt-1">Communication</div>
            <a href="{{ route('admin.messages.index') }}" class="sidebar-link {{ request()->routeIs('admin.messages*') ? 'active' : '' }}">
                <i class="fa-solid fa-envelope sidebar-icon"></i><span class="sidebar-label">Messages</span>
            </a>

            <div class="sidebar-section mt-1">Account</div>
            <a href="{{ route('admin.profile.index') }}" class="sidebar-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-circle sidebar-icon"></i><span class="sidebar-label">My Profile</span>
            </a>
            <a href="{{ route('admin.settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <i class="fa-solid fa-gear sidebar-icon"></i><span class="sidebar-label">Settings</span>
            </a>

        @endif

    </nav>

    {{-- ── User Footer ──────────────────────────────────────────────────── --}}
    <div class="sidebar-user" style="border-top:1px solid rgba(255,255,255,0.08);padding:0.875rem 1rem;">
        <div class="d-flex align-items-center gap-2">
            <img src="{{ auth()->user()->avatar_url }}"
                 alt="{{ auth()->user()->name }}"
                 class="rounded-circle"
                 style="width:36px;height:36px;object-fit:cover;flex-shrink:0;">
            <div class="user-info overflow-hidden flex-1">
                <div class="text-white lh-1"
                     style="font-size:0.85rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    {{ auth()->user()->name }}
                </div>
                <span class="badge mt-1"
                      style="background:rgba(255,255,255,0.15);color:rgba(255,255,255,0.8);font-size:0.65rem;">
                    {{ auth()->user()->getRoleDisplayName() }}
                </span>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="ms-auto">
                @csrf
                <button type="submit" class="btn btn-sm p-1 border-0"
                        style="color:rgba(255,255,255,0.5);background:transparent;" title="Logout">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

<style>
/* ── Sidebar base — uses CSS variable from app.blade.php ── */
#sidebar {
    background: var(--sidebar-bg, #1E40AF);
}

.sidebar-section {
    padding: 0.4rem 1.25rem 0.2rem;
    font-size: 0.62rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: rgba(255,255,255,0.3);
}

.sidebar-link {
    display:flex;align-items:center;gap:0.75rem;padding:0.6rem 1rem;
    color:rgba(255,255,255,0.65);text-decoration:none;font-size:0.85rem;
    font-weight:500;border-radius:0.5rem;margin:0.1rem 0.5rem;
    transition:all 0.2s ease;background:transparent;cursor:pointer;white-space:nowrap;
}
.sidebar-link:hover { color:#fff; background:rgba(255,255,255,0.1); }
.sidebar-link.active {
    color:#fff;
    background:rgba(255,255,255,0.18);
    box-shadow:0 2px 8px rgba(0,0,0,0.2);
}
.sidebar-icon { font-size:0.95rem;width:20px;text-align:center;flex-shrink:0; }
.sidebar-label { flex:1;overflow:hidden;text-overflow:ellipsis; }
.sidebar-arrow { font-size:0.65rem;transition:transform 0.2s ease;flex-shrink:0; }
.sidebar-collapse-btn[aria-expanded="true"] .sidebar-arrow { transform:rotate(90deg); }
.sidebar-sub-link {
    display:flex;align-items:center;padding:0.45rem 1rem 0.45rem 2.75rem;
    color:rgba(255,255,255,0.5);text-decoration:none;font-size:0.82rem;
    font-weight:400;border-radius:0.375rem;margin:0.08rem 0.5rem;
    transition:all 0.2s ease;white-space:nowrap;
}
.sidebar-sub-link:hover { color:rgba(255,255,255,0.9);background:rgba(255,255,255,0.08); }
.sidebar-sub-link.active { color:#fff;background:rgba(255,255,255,0.15); }

/* Collapsed state */
#sidebar.collapsed .sidebar-label,
#sidebar.collapsed .sidebar-arrow,
#sidebar.collapsed .brand-text,
#sidebar.collapsed .user-info,
#sidebar.collapsed .sidebar-section,
#sidebar.collapsed .sidebar-sub-link { display:none !important; }
#sidebar.collapsed .sidebar-link { justify-content:center;padding:0.625rem;margin:0.125rem 0.375rem; }
#sidebar.collapsed .sidebar-icon { width:auto; }
#sidebar.collapsed .sidebar-brand { justify-content:center; }
#sidebar.collapsed .sidebar-user { padding:0.75rem 0.5rem; }
#sidebar.collapsed .sidebar-user .d-flex { justify-content:center; }
#sidebar.collapsed .collapse { display:none !important; }

/* Scrollbar */
.sidebar-nav::-webkit-scrollbar { width:4px; }
.sidebar-nav::-webkit-scrollbar-track { background:transparent; }
.sidebar-nav::-webkit-scrollbar-thumb { background:rgba(255,255,255,0.15);border-radius:2px; }
</style>
