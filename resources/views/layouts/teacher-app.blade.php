<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Teacher Portal') — EduTenant ERP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        :root {
            --tc-primary:   #d97706;
            --tc-dark:      #b45309;
            --tc-accent:    #f59e0b;
            --tc-sidebar:   #1c1208;
            --tc-sidebar-w: 255px;
            --tc-sidebar-c: 68px;
            --font:         'Inter', sans-serif;
            --radius:       0.75rem;
            --transition:   all 0.25s ease;
            --bg:           #fffbeb;
            --surface:      #ffffff;
            --text:         #1c1208;
            --text-muted:   #78716c;
            --border:       #e7e5e4;
        }
        [data-theme="dark"] {
            --bg:       #0c0a04;
            --surface:  #1c1208;
            --text:     #fef3c7;
            --text-muted:#a8a29e;
            --border:   #292524;
        }
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family:var(--font); background:var(--bg); color:var(--text); margin:0; overflow-x:hidden; }
        #tc-wrapper { display:flex; min-height:100vh; }
        #tc-sidebar {
            width:var(--tc-sidebar-w); min-height:100vh;
            background:var(--tc-sidebar);
            position:fixed; top:0; left:0; z-index:1040;
            display:flex; flex-direction:column;
            transition:var(--transition); overflow:hidden;
        }
        #tc-sidebar.collapsed { width:var(--tc-sidebar-c); }
        #tc-main { margin-left:var(--tc-sidebar-w); flex:1; min-height:100vh; display:flex; flex-direction:column; transition:var(--transition); }
        #tc-main.expanded { margin-left:var(--tc-sidebar-c); }
        .tc-content { padding:1.5rem; flex:1; }
        .tc-brand { padding:1.1rem 1rem; border-bottom:1px solid rgba(255,255,255,.06); display:flex; align-items:center; justify-content:space-between; min-height:64px; }
        .tc-brand-icon { width:38px; height:38px; border-radius:9px; flex-shrink:0; background:linear-gradient(135deg,#d97706,#f59e0b); display:flex; align-items:center; justify-content:center; }
        .tc-link { display:flex; align-items:center; gap:.75rem; padding:.6rem 1rem; margin:.1rem .5rem; color:rgba(255,255,255,.5); text-decoration:none; font-size:.85rem; font-weight:500; border-radius:.5rem; transition:var(--transition); white-space:nowrap; }
        .tc-link:hover { color:#fff; background:rgba(217,119,6,.2); }
        .tc-link.active { color:#fff; background:linear-gradient(135deg,rgba(217,119,6,.5),rgba(245,158,11,.3)); box-shadow:0 2px 10px rgba(217,119,6,.25); }
        .tc-icon { font-size:.95rem; width:20px; text-align:center; flex-shrink:0; }
        .tc-label { flex:1; overflow:hidden; text-overflow:ellipsis; }
        .tc-section { padding:.5rem 1.5rem .2rem; font-size:.62rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:rgba(255,255,255,.2); }
        #tc-topnav { height:64px; background:var(--surface); border-bottom:1px solid var(--border); display:flex; align-items:center; padding:0 1.5rem; gap:1rem; position:sticky; top:0; z-index:1030; box-shadow:0 1px 3px rgba(0,0,0,.06); }
        .card { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); transition:var(--transition); }
        .card:hover { box-shadow:0 4px 12px rgba(217,119,6,.08); transform:translateY(-1px); }
        .card-header { background:transparent; border-bottom:1px solid var(--border); padding:1rem 1.25rem; font-weight:600; }
        .stat-card { border-radius:var(--radius); padding:1.5rem; color:#fff; position:relative; overflow:hidden; transition:var(--transition); }
        .stat-card:hover { transform:translateY(-3px); }
        .stat-card.amber  { background:linear-gradient(135deg,#d97706,#f59e0b); }
        .stat-card.green  { background:linear-gradient(135deg,#059669,#10b981); }
        .stat-card.red    { background:linear-gradient(135deg,#dc2626,#ef4444); }
        .stat-card.blue   { background:linear-gradient(135deg,#2563eb,#3b82f6); }
        .stat-card .stat-value { font-size:2rem; font-weight:800; line-height:1; }
        .stat-card .stat-label { font-size:.85rem; opacity:.9; font-weight:500; }
        .stat-card .stat-icon  { font-size:2.5rem; opacity:.85; }
        .table { color:var(--text); }
        .table thead th { background:var(--bg); color:var(--muted); font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; border-bottom:2px solid var(--border); padding:.75rem 1rem; }
        .table tbody td { padding:.875rem 1rem; border-bottom:1px solid var(--border); vertical-align:middle; }
        .table tbody tr:hover { background:rgba(217,119,6,.04); }
        .btn-primary { background:linear-gradient(135deg,var(--tc-primary),var(--tc-accent)); border:none; font-weight:500; }
        .btn-primary:hover { opacity:.9; transform:translateY(-1px); }
        .form-control,.form-select { background:var(--surface); border:1px solid var(--border); color:var(--text); border-radius:.5rem; }
        .form-control:focus,.form-select:focus { border-color:var(--tc-primary); box-shadow:0 0 0 3px rgba(217,119,6,.15); }
        .page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; flex-wrap:wrap; gap:.75rem; }
        .page-title { font-size:1.5rem; font-weight:700; color:var(--text); margin:0; }
        #tc-sidebar.collapsed .tc-label,#tc-sidebar.collapsed .tc-section,#tc-sidebar.collapsed .tc-brand-text { display:none !important; }
        #tc-sidebar.collapsed .tc-link { justify-content:center; padding:.625rem; margin:.125rem .375rem; }
        #tc-sidebar.collapsed .tc-brand { justify-content:center; }
        @media (max-width:768px) { #tc-sidebar { transform:translateX(-100%); } #tc-sidebar.mobile-open { transform:translateX(0); } #tc-main { margin-left:0 !important; } .tc-content { padding:1rem; } }
        #tc-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:1039; }
        #tc-overlay.active { display:block; }
        ::-webkit-scrollbar { width:5px; } ::-webkit-scrollbar-track { background:transparent; } ::-webkit-scrollbar-thumb { background:rgba(217,119,6,.3); border-radius:3px; }
        @keyframes fadeInUp { from{opacity:0;transform:translateY(12px);}to{opacity:1;transform:translateY(0);} }
        .fade-in-up { animation:fadeInUp .3s ease forwards; }
        .badge { font-weight:500; font-size:.72rem; padding:.3em .65em; border-radius:.375rem; }
        .empty-state { text-align:center; padding:3rem 2rem; color:var(--muted); }
        .empty-state i { font-size:3rem; opacity:.25; margin-bottom:1rem; }
        [data-theme="dark"] .form-control,[data-theme="dark"] .form-select { background:#1c1208; border-color:#292524; color:#fef3c7; }
        [data-theme="dark"] .table thead th { background:#0c0a04; }
    </style>
    @stack('styles')
</head>
<body>
<div id="tc-wrapper">
    <div id="tc-overlay"></div>
    <aside id="tc-sidebar">
        <div class="tc-brand">
            <div class="d-flex align-items-center gap-2">
                <div class="tc-brand-icon"><i class="fa-solid fa-chalkboard-user text-white" style="font-size:1rem;"></i></div>
                <div class="tc-brand-text overflow-hidden">
                    <div style="font-size:.88rem;font-weight:700;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:155px;">{{ auth()->user()->tenant?->name ?? 'EduTenant ERP' }}</div>
                    <div style="font-size:.65rem;color:rgba(255,255,255,.35);margin-top:1px;">Teacher Portal</div>
                </div>
            </div>
            <button id="tc-toggle" class="btn btn-sm p-1 border-0" style="color:rgba(255,255,255,.4);background:transparent;"><i class="fa-solid fa-bars"></i></button>
        </div>
        <nav class="flex-1 py-2" style="overflow-y:auto;overflow-x:hidden;">
            <a href="{{ route('dashboard') }}" class="tc-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-gauge tc-icon"></i><span class="tc-label">Dashboard</span>
            </a>
            <div class="tc-section mt-1">Classroom</div>
            <a href="{{ route('admin.students.index') }}" class="tc-link {{ request()->routeIs('admin.students*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-graduate tc-icon"></i><span class="tc-label">My Students</span>
            </a>
            <a href="{{ route('admin.attendance.students') }}" class="tc-link {{ request()->routeIs('admin.attendance.students') && !request()->routeIs('admin.attendance.students.*') ? 'active' : '' }}">
                <i class="fa-solid fa-calendar-check tc-icon"></i><span class="tc-label">Mark Attendance</span>
            </a>
            <a href="{{ route('admin.attendance.students.report') }}" class="tc-link {{ request()->routeIs('admin.attendance.students.report') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-bar tc-icon"></i><span class="tc-label">Attendance Report</span>
            </a>
            <a href="{{ route('admin.attendance.students.analytics') }}" class="tc-link {{ request()->routeIs('admin.attendance.students.analytics') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line tc-icon"></i><span class="tc-label">Analytics</span>
            </a>
            <div class="tc-section mt-1">Communication</div>
            <a href="{{ route('admin.messages.index') }}" class="tc-link {{ request()->routeIs('admin.messages*') ? 'active' : '' }}">
                <i class="fa-solid fa-envelope tc-icon"></i><span class="tc-label">Messages</span>
            </a>
            <div class="tc-section mt-1">Account</div>
            <a href="{{ route('admin.profile.index') }}" class="tc-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <i class="fa-solid fa-user tc-icon"></i><span class="tc-label">My Profile</span>
            </a>
        </nav>
        <div style="border-top:1px solid rgba(255,255,255,.06);padding:.875rem 1rem;">
            <div class="d-flex align-items-center gap-2">
                <img src="{{ auth()->user()->avatar_url }}" class="rounded-circle" style="width:34px;height:34px;object-fit:cover;flex-shrink:0;">
                <div class="tc-label overflow-hidden">
                    <div style="font-size:.82rem;font-weight:600;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->name }}</div>
                    <span style="font-size:.65rem;color:var(--tc-accent);">Teacher</span>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="ms-auto">@csrf<button type="submit" class="btn btn-sm p-1 border-0" style="color:rgba(255,255,255,.35);background:transparent;" title="Logout"><i class="fa-solid fa-right-from-bracket"></i></button></form>
            </div>
        </div>
    </aside>
    <div id="tc-main">
        <nav id="tc-topnav">
            <button id="tc-topnav-toggle" class="btn btn-sm p-2 border-0" style="color:var(--muted);background:transparent;"><i class="fa-solid fa-bars" style="font-size:1.1rem;"></i></button>
            <nav aria-label="breadcrumb" class="d-none d-md-block flex-1">
                <ol class="breadcrumb mb-0" style="font-size:.82rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:var(--tc-primary);text-decoration:none;"><i class="fa-solid fa-house me-1"></i>Home</a></li>
                    @yield('breadcrumb')
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2 ms-auto">
                <button id="tc-theme-toggle" class="btn btn-sm p-2 border-0 rounded-2" style="color:var(--muted);background:var(--bg);" title="Toggle Dark Mode"><i class="fa-solid fa-moon" id="tc-theme-icon" style="font-size:1rem;"></i></button>
                <div class="dropdown">
                    <button class="btn btn-sm p-1 border-0 d-flex align-items-center gap-2" style="background:transparent;" data-bs-toggle="dropdown">
                        <img src="{{ auth()->user()->avatar_url }}" class="rounded-circle" style="width:32px;height:32px;object-fit:cover;border:2px solid var(--border);">
                        <div class="d-none d-md-block text-start">
                            <div style="font-size:.82rem;font-weight:600;color:var(--text);line-height:1.2;">{{ auth()->user()->name }}</div>
                            <div style="font-size:.68rem;color:var(--tc-primary);">Teacher</div>
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-1" style="min-width:200px;border-radius:.75rem;background:var(--surface);">
                        <li><a class="dropdown-item py-2 px-3" href="{{ route('admin.profile.index') }}" style="font-size:.85rem;color:var(--text);"><i class="fa-solid fa-user me-2" style="color:var(--tc-primary);width:16px;"></i>My Profile</a></li>
                        <li><hr class="dropdown-divider" style="border-color:var(--border);"></li>
                        <li><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="dropdown-item py-2 px-3" style="font-size:.85rem;color:#dc2626;"><i class="fa-solid fa-right-from-bracket me-2" style="width:16px;"></i>Sign Out</button></form></li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="tc-content fade-in-up">@yield('content')</div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script>
(function(){
    const sidebar=document.getElementById('tc-sidebar'),main=document.getElementById('tc-main'),overlay=document.getElementById('tc-overlay');
    function toggle(){if(window.innerWidth<=768){sidebar.classList.toggle('mobile-open');overlay.classList.toggle('active');}else{sidebar.classList.toggle('collapsed');main.classList.toggle('expanded');}}
    document.getElementById('tc-toggle')?.addEventListener('click',toggle);
    document.getElementById('tc-topnav-toggle')?.addEventListener('click',toggle);
    overlay?.addEventListener('click',()=>{sidebar.classList.remove('mobile-open');overlay.classList.remove('active');});
    const themeBtn=document.getElementById('tc-theme-toggle'),themeIcon=document.getElementById('tc-theme-icon'),html=document.documentElement;
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


