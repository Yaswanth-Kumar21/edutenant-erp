<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Staff Portal') — EduTenant ERP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        :root {
            --st-primary:   #0891b2;
            --st-dark:      #0e7490;
            --st-accent:    #06b6d4;
            --st-green:     #059669;
            --st-sidebar:   #0c1a2e;
            --st-sidebar-w: 255px;
            --st-sidebar-c: 68px;
            --font:         'Inter', sans-serif;
            --radius:       0.75rem;
            --transition:   all 0.25s ease;
            --bg:           #f0fdfe;
            --surface:      #ffffff;
            --text:         #0c1a2e;
            --text-muted:   #64748b;
            --border:       #e2e8f0;
        }
        [data-theme="dark"] {
            --bg:       #060f1a;
            --surface:  #0c1a2e;
            --text:     #e2e8f0;
            --text-muted:#64748b;
            --border:   #1e3a5f;
        }
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family:var(--font); background:var(--bg); color:var(--text); margin:0; overflow-x:hidden; }
        #st-wrapper { display:flex; min-height:100vh; }
        #st-sidebar {
            width:var(--st-sidebar-w); min-height:100vh;
            background:var(--st-sidebar);
            position:fixed; top:0; left:0; z-index:1040;
            display:flex; flex-direction:column;
            transition:var(--transition); overflow:hidden;
        }
        #st-sidebar.collapsed { width:var(--st-sidebar-c); }
        #st-main { margin-left:var(--st-sidebar-w); flex:1; min-height:100vh; display:flex; flex-direction:column; transition:var(--transition); }
        #st-main.expanded { margin-left:var(--st-sidebar-c); }
        .st-content { padding:1.5rem; flex:1; }
        /* Brand */
        .st-brand { padding:1.1rem 1rem; border-bottom:1px solid rgba(255,255,255,.07); display:flex; align-items:center; justify-content:space-between; min-height:64px; }
        .st-brand-icon { width:38px; height:38px; border-radius:9px; flex-shrink:0; background:linear-gradient(135deg,#0891b2,#06b6d4); display:flex; align-items:center; justify-content:center; }
        /* Nav */
        .st-link { display:flex; align-items:center; gap:.75rem; padding:.6rem 1rem; margin:.1rem .5rem; color:rgba(255,255,255,.55); text-decoration:none; font-size:.85rem; font-weight:500; border-radius:.5rem; transition:var(--transition); white-space:nowrap; }
        .st-link:hover { color:#fff; background:rgba(8,145,178,.2); }
        .st-link.active { color:#fff; background:linear-gradient(135deg,rgba(8,145,178,.5),rgba(6,182,212,.3)); box-shadow:0 2px 10px rgba(8,145,178,.25); }
        .st-icon { font-size:.95rem; width:20px; text-align:center; flex-shrink:0; }
        .st-label { flex:1; overflow:hidden; text-overflow:ellipsis; }
        .st-section { padding:.5rem 1.5rem .2rem; font-size:.62rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:rgba(255,255,255,.25); }
        /* Topnav */
        #st-topnav { height:64px; background:var(--surface); border-bottom:1px solid var(--border); display:flex; align-items:center; padding:0 1.5rem; gap:1rem; position:sticky; top:0; z-index:1030; box-shadow:0 1px 3px rgba(0,0,0,.06); }
        /* Cards */
        .card { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); transition:var(--transition); }
        .card:hover { box-shadow:0 4px 12px rgba(8,145,178,.08); transform:translateY(-1px); }
        .card-header { background:transparent; border-bottom:1px solid var(--border); padding:1rem 1.25rem; font-weight:600; }
        /* Stat cards */
        .stat-card { border-radius:var(--radius); padding:1.5rem; color:#fff; position:relative; overflow:hidden; transition:var(--transition); }
        .stat-card:hover { transform:translateY(-3px); }
        .stat-card.teal   { background:linear-gradient(135deg,#0891b2,#06b6d4); }
        .stat-card.green  { background:linear-gradient(135deg,#059669,#10b981); }
        .stat-card.orange { background:linear-gradient(135deg,#d97706,#f59e0b); }
        .stat-card.purple { background:linear-gradient(135deg,#7c3aed,#a855f7); }
        .stat-card .stat-value { font-size:2rem; font-weight:800; line-height:1; }
        .stat-card .stat-label { font-size:.85rem; opacity:.9; font-weight:500; }
        .stat-card .stat-icon  { font-size:2.5rem; opacity:.85; }
        /* Quick action cards */
        .action-card { background:var(--surface); border:2px solid var(--border); border-radius:var(--radius); padding:1.5rem; text-align:center; text-decoration:none; color:var(--text); transition:var(--transition); display:block; }
        .action-card:hover { border-color:var(--st-primary); color:var(--st-primary); transform:translateY(-3px); box-shadow:0 8px 20px rgba(8,145,178,.12); }
        .action-card .action-icon { font-size:2rem; margin-bottom:.75rem; }
        .action-card .action-label { font-size:.875rem; font-weight:600; }
        /* Tables */
        .table { color:var(--text); }
        .table thead th { background:var(--bg); color:var(--muted); font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; border-bottom:2px solid var(--border); padding:.75rem 1rem; }
        .table tbody td { padding:.875rem 1rem; border-bottom:1px solid var(--border); vertical-align:middle; }
        .table tbody tr:hover { background:rgba(8,145,178,.04); }
        /* Buttons */
        .btn-primary { background:linear-gradient(135deg,var(--st-primary),var(--st-accent)); border:none; font-weight:500; }
        .btn-primary:hover { opacity:.9; transform:translateY(-1px); }
        /* Forms */
        .form-control,.form-select { background:var(--surface); border:1px solid var(--border); color:var(--text); border-radius:.5rem; }
        .form-control:focus,.form-select:focus { border-color:var(--st-primary); box-shadow:0 0 0 3px rgba(8,145,178,.15); }
        /* Page header */
        .page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; flex-wrap:wrap; gap:.75rem; }
        .page-title { font-size:1.5rem; font-weight:700; color:var(--text); margin:0; }
        /* Collapsed */
        #st-sidebar.collapsed .st-label,#st-sidebar.collapsed .st-section,#st-sidebar.collapsed .st-brand-text { display:none !important; }
        #st-sidebar.collapsed .st-link { justify-content:center; padding:.625rem; margin:.125rem .375rem; }
        #st-sidebar.collapsed .st-brand { justify-content:center; }
        /* Mobile */
        @media (max-width:768px) { #st-sidebar { transform:translateX(-100%); } #st-sidebar.mobile-open { transform:translateX(0); } #st-main { margin-left:0 !important; } .st-content { padding:1rem; } }
        #st-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:1039; }
        #st-overlay.active { display:block; }
        ::-webkit-scrollbar { width:5px; } ::-webkit-scrollbar-track { background:transparent; } ::-webkit-scrollbar-thumb { background:rgba(8,145,178,.3); border-radius:3px; }
        @keyframes fadeInUp { from{opacity:0;transform:translateY(12px);}to{opacity:1;transform:translateY(0);} }
        .fade-in-up { animation:fadeInUp .3s ease forwards; }
        .badge { font-weight:500; font-size:.72rem; padding:.3em .65em; border-radius:.375rem; }
        .empty-state { text-align:center; padding:3rem 2rem; color:var(--muted); }
        .empty-state i { font-size:3rem; opacity:.25; margin-bottom:1rem; }
        [data-theme="dark"] .form-control,[data-theme="dark"] .form-select { background:#0c1a2e; border-color:#1e3a5f; color:#e2e8f0; }
        [data-theme="dark"] .table thead th { background:#060f1a; }
    </style>
    @stack('styles')
</head>
<body>
<div id="st-wrapper">
    <div id="st-overlay"></div>
    <aside id="st-sidebar">
        <div class="st-brand">
            <div class="d-flex align-items-center gap-2">
                <div class="st-brand-icon"><i class="fa-solid fa-briefcase text-white" style="font-size:1rem;"></i></div>
                <div class="st-brand-text overflow-hidden">
                    <div style="font-size:.88rem;font-weight:700;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:155px;">{{ auth()->user()->tenant?->name ?? 'EduTenant ERP' }}</div>
                    <div style="font-size:.65rem;color:rgba(255,255,255,.4);margin-top:1px;">Staff Portal</div>
                </div>
            </div>
            <button id="st-toggle" class="btn btn-sm p-1 border-0" style="color:rgba(255,255,255,.5);background:transparent;"><i class="fa-solid fa-bars"></i></button>
        </div>
        <nav class="flex-1 py-2" style="overflow-y:auto;overflow-x:hidden;">
            <a href="{{ route('dashboard') }}" class="st-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-gauge st-icon"></i><span class="st-label">Dashboard</span>
            </a>
            <div class="st-section mt-1">Admissions</div>
            <a href="{{ route('admin.admissions.create') }}" class="st-link {{ request()->routeIs('admin.admissions*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-plus st-icon"></i><span class="st-label">New Admission</span>
            </a>
            <a href="{{ route('admin.students.index') }}" class="st-link {{ request()->routeIs('admin.students*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-graduate st-icon"></i><span class="st-label">Students</span>
            </a>
            <div class="st-section mt-1">Fees</div>
            <a href="{{ route('admin.fees.payments.create') }}" class="st-link {{ request()->routeIs('admin.fees.payments.create') ? 'active' : '' }}">
                <i class="fa-solid fa-hand-holding-dollar st-icon"></i><span class="st-label">Collect Fee</span>
            </a>
            <a href="{{ route('admin.fees.payments.index') }}" class="st-link {{ request()->routeIs('admin.fees.payments.index') ? 'active' : '' }}">
                <i class="fa-solid fa-clock-rotate-left st-icon"></i><span class="st-label">Payment History</span>
            </a>
            <div class="st-section mt-1">Attendance</div>
            <a href="{{ route('admin.attendance.students') }}" class="st-link {{ request()->routeIs('admin.attendance.students') && !request()->routeIs('admin.attendance.students.*') ? 'active' : '' }}">
                <i class="fa-solid fa-calendar-check st-icon"></i><span class="st-label">Mark Attendance</span>
            </a>
            <a href="{{ route('admin.attendance.students.report') }}" class="st-link {{ request()->routeIs('admin.attendance.students.report') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-bar st-icon"></i><span class="st-label">Attendance Report</span>
            </a>
            <div class="st-section mt-1">Communication</div>
            <a href="{{ route('admin.messages.index') }}" class="st-link {{ request()->routeIs('admin.messages*') ? 'active' : '' }}">
                <i class="fa-solid fa-envelope st-icon"></i><span class="st-label">Messages</span>
            </a>
        </nav>
        <div style="border-top:1px solid rgba(255,255,255,.07);padding:.875rem 1rem;">
            <div class="d-flex align-items-center gap-2">
                <img src="{{ auth()->user()->avatar_url }}" class="rounded-circle" style="width:34px;height:34px;object-fit:cover;flex-shrink:0;">
                <div class="st-label overflow-hidden">
                    <div style="font-size:.82rem;font-weight:600;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->name }}</div>
                    <span style="font-size:.65rem;color:var(--st-accent);">Staff</span>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="ms-auto">@csrf<button type="submit" class="btn btn-sm p-1 border-0" style="color:rgba(255,255,255,.4);background:transparent;" title="Logout"><i class="fa-solid fa-right-from-bracket"></i></button></form>
            </div>
        </div>
    </aside>
    <div id="st-main">
        <nav id="st-topnav">
            <button id="st-topnav-toggle" class="btn btn-sm p-2 border-0" style="color:var(--muted);background:transparent;"><i class="fa-solid fa-bars" style="font-size:1.1rem;"></i></button>
            <nav aria-label="breadcrumb" class="d-none d-md-block flex-1">
                <ol class="breadcrumb mb-0" style="font-size:.82rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:var(--st-primary);text-decoration:none;"><i class="fa-solid fa-house me-1"></i>Home</a></li>
                    @yield('breadcrumb')
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2 ms-auto">
                <button id="st-theme-toggle" class="btn btn-sm p-2 border-0 rounded-2" style="color:var(--muted);background:var(--bg);" title="Toggle Dark Mode"><i class="fa-solid fa-moon" id="st-theme-icon" style="font-size:1rem;"></i></button>
                <div class="dropdown">
                    <button class="btn btn-sm p-1 border-0 d-flex align-items-center gap-2" style="background:transparent;" data-bs-toggle="dropdown">
                        <img src="{{ auth()->user()->avatar_url }}" class="rounded-circle" style="width:32px;height:32px;object-fit:cover;border:2px solid var(--border);">
                        <div class="d-none d-md-block text-start">
                            <div style="font-size:.82rem;font-weight:600;color:var(--text);line-height:1.2;">{{ auth()->user()->name }}</div>
                            <div style="font-size:.68rem;color:var(--st-primary);">Staff</div>
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-1" style="min-width:200px;border-radius:.75rem;background:var(--surface);">
                        <li><a class="dropdown-item py-2 px-3" href="{{ route('admin.profile.index') }}" style="font-size:.85rem;color:var(--text);"><i class="fa-solid fa-user me-2" style="color:var(--st-primary);width:16px;"></i>My Profile</a></li>
                        <li><hr class="dropdown-divider" style="border-color:var(--border);"></li>
                        <li><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="dropdown-item py-2 px-3" style="font-size:.85rem;color:#dc2626;"><i class="fa-solid fa-right-from-bracket me-2" style="width:16px;"></i>Sign Out</button></form></li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="st-content fade-in-up">@yield('content')</div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script>
(function(){
    const sidebar=document.getElementById('st-sidebar'),main=document.getElementById('st-main'),overlay=document.getElementById('st-overlay');
    function toggle(){if(window.innerWidth<=768){sidebar.classList.toggle('mobile-open');overlay.classList.toggle('active');}else{sidebar.classList.toggle('collapsed');main.classList.toggle('expanded');}}
    document.getElementById('st-toggle')?.addEventListener('click',toggle);
    document.getElementById('st-topnav-toggle')?.addEventListener('click',toggle);
    overlay?.addEventListener('click',()=>{sidebar.classList.remove('mobile-open');overlay.classList.remove('active');});
    const themeBtn=document.getElementById('st-theme-toggle'),themeIcon=document.getElementById('st-theme-icon'),html=document.documentElement;
    const saved=localStorage.getItem('theme')||'light';html.setAttribute('data-theme',saved);
    if(saved==='dark')themeIcon?.classList.replace('fa-moon','fa-sun');
    themeBtn?.addEventListener('click',()=>{const c=html.getAttribute('data-theme'),n=c==='dark'?'light':'dark';html.setAttribute('data-theme',n);localStorage.setItem('theme',n);themeIcon?.classList.toggle('fa-moon',n==='light');themeIcon?.classList.toggle('fa-sun',n==='dark');});
    @if(session('success'))Swal.fire({icon:'success',title:'Success',text:'{{ session('success') }}',timer:3000,showConfirmButton:false,toast:true,position:'top-end'});@endif
    @if(session('error'))Swal.fire({icon:'error',title:'Error',text:'{{ session('error') }}',timer:4000,showConfirmButton:false,toast:true,position:'top-end'});@endif
})();
</script>
@stack('scripts')
</body>
</html>


