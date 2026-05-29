<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Student Portal') — EduTenant ERP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
    /* ═══════════════════════════════════════════════════════════════
       STUDENT PORTAL — MODERN PURPLE GRADIENT
    ═══════════════════════════════════════════════════════════════ */
    :root {
        --sp:       #7C3AED;
        --sp-d:     #5B21B6;
        --sp-l:     #A78BFA;
        --sp-side:  #1E1B4B;
        --sw:       260px;
        --sc:       68px;
        --font:     'Inter', sans-serif;
        --radius:   12px;
        --tr:       all 0.22s ease;
        /* Light */
        --bg:       #F5F3FF;
        --surface:  #FFFFFF;
        --text:     #1E1B4B;
        --muted:         #6B7280;
        --text-muted:    #6B7280;
        --border:   #E5E7EB;
        --shadow:   0 1px 3px rgba(0,0,0,.08);
    }
    [data-theme="dark"] {
        --bg:      #0F0E1A;
        --surface: #1A1830;
        --text:    #E2E8F0;
        --muted:   #94A3B8;
        --border:  #2D2B4E;
        --shadow:  0 1px 3px rgba(0,0,0,.3);
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: var(--font); background: var(--bg); color: var(--text); overflow-x: hidden; transition: background .3s, color .3s; }

    /* ── LAYOUT ── */
    #sp-wrap { display: flex; min-height: 100vh; }

    /* ── SIDEBAR ── */
    #sp-side {
        width: var(--sw); min-height: 100vh;
        background: var(--sp-side);
        position: fixed; top: 0; left: 0; z-index: 1040;
        display: flex; flex-direction: column;
        transition: var(--tr); overflow: hidden;
    }
    #sp-side.collapsed { width: var(--sc); }

    /* Brand */
    .sp-brand {
        display: flex; align-items: center; justify-content: space-between;
        padding: 1.25rem 1rem; min-height: 68px;
        border-bottom: 1px solid rgba(255,255,255,.07);
    }
    .sp-logo {
        width: 38px; height: 38px; border-radius: 10px; flex-shrink: 0;
        background: linear-gradient(135deg, var(--sp), #4F46E5);
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 4px 15px rgba(124,58,237,.4);
    }

    /* Nav */
    .sp-nav { flex: 1; overflow-y: auto; overflow-x: hidden; padding: .5rem 0; }
    .sp-nav::-webkit-scrollbar { width: 3px; }
    .sp-nav::-webkit-scrollbar-thumb { background: rgba(124,58,237,.3); border-radius: 2px; }

    .sp-section {
        padding: .6rem 1.25rem .2rem;
        font-size: .6rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: .1em;
        color: rgba(255,255,255,.25);
    }
    .sp-link {
        display: flex; align-items: center; gap: .75rem;
        padding: .6rem 1rem; margin: .1rem .5rem;
        color: rgba(255,255,255,.55); text-decoration: none;
        font-size: .84rem; font-weight: 500;
        border-radius: 8px; transition: var(--tr); white-space: nowrap;
    }
    .sp-link:hover { color: #fff; background: rgba(255,255,255,.08); }
    .sp-link.active {
        color: #fff;
        background: linear-gradient(135deg, rgba(124,58,237,.55), rgba(79,70,229,.35));
        box-shadow: 0 2px 12px rgba(124,58,237,.25);
    }
    .sp-icon { font-size: .95rem; width: 20px; text-align: center; flex-shrink: 0; }
    .sp-label { flex: 1; overflow: hidden; text-overflow: ellipsis; }

    /* User footer */
    .sp-foot { border-top: 1px solid rgba(255,255,255,.07); padding: .875rem 1rem; }

    /* ── MAIN ── */
    #sp-main { margin-left: var(--sw); flex: 1; display: flex; flex-direction: column; transition: var(--tr); min-height: 100vh; }
    #sp-main.expanded { margin-left: var(--sc); }
    .sp-content { padding: 1.75rem; flex: 1; }

    /* ── TOPNAV ── */
    #sp-top {
        height: 64px; background: var(--surface);
        border-bottom: 1px solid var(--border);
        display: flex; align-items: center; padding: 0 1.5rem; gap: 1rem;
        position: sticky; top: 0; z-index: 1030;
        box-shadow: var(--shadow);
    }

    /* ── CARDS ── */
    .card {
        background: var(--surface); border: 1px solid var(--border);
        border-radius: var(--radius); transition: var(--tr);
        box-shadow: var(--shadow);
    }
    .card:hover { box-shadow: 0 4px 16px rgba(124,58,237,.1); transform: translateY(-1px); }
    .card-header {
        background: transparent; border-bottom: 1px solid var(--border);
        padding: 1rem 1.25rem; font-weight: 600; color: var(--text);
    }
    .card-body { padding: 1.25rem; }

    /* ── STAT CARDS ── */
    .stat-card {
        border-radius: var(--radius); padding: 1.5rem; color: #fff;
        position: relative; overflow: hidden; transition: var(--tr);
        box-shadow: 0 4px 15px rgba(0,0,0,.15);
    }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,.2); }
    .stat-card::after { content:''; position:absolute; top:-30px; right:-30px; width:100px; height:100px; border-radius:50%; background:rgba(255,255,255,.1); }
    .stat-card.blue   { background: linear-gradient(135deg,#4F46E5,#7C3AED); }
    .stat-card.green  { background: linear-gradient(135deg,#059669,#10B981); }
    .stat-card.orange { background: linear-gradient(135deg,#D97706,#F59E0B); }
    .stat-card.purple { background: linear-gradient(135deg,#7C3AED,#A855F7); }
    .stat-card.teal   { background: linear-gradient(135deg,#0891B2,#06B6D4); }
    .stat-card .stat-value { font-size: 2rem; font-weight: 800; line-height: 1; }
    .stat-card .stat-label { font-size: .82rem; opacity: .9; font-weight: 500; margin-top: .25rem; }
    .stat-card .stat-icon  { font-size: 2.5rem; opacity: .8; }

    /* ── TABLES ── */
    .table { color: var(--text); }
    .table thead th { background: var(--bg); color: var(--muted); font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; border-bottom: 2px solid var(--border); padding: .75rem 1rem; }
    .table tbody td { padding: .875rem 1rem; border-bottom: 1px solid var(--border); vertical-align: middle; }
    .table tbody tr:hover { background: rgba(124,58,237,.04); }

    /* ── FORMS ── */
    .form-control, .form-select { background: var(--surface); border: 1px solid var(--border); color: var(--text); border-radius: 8px; transition: var(--tr); }
    .form-control:focus, .form-select:focus { border-color: var(--sp); box-shadow: 0 0 0 3px rgba(124,58,237,.15); background: var(--surface); color: var(--text); }
    .form-label { font-weight: 500; font-size: .875rem; color: var(--text); margin-bottom: .375rem; }

    /* ── BUTTONS ── */
    .btn-primary { background: linear-gradient(135deg, var(--sp), var(--sp-d)); border: none; font-weight: 600; color: #fff; border-radius: 8px; }
    .btn-primary:hover { opacity: .9; transform: translateY(-1px); box-shadow: 0 4px 15px rgba(124,58,237,.4); color: #fff; }
    .btn-outline-primary { border-color: var(--sp); color: var(--sp); border-radius: 8px; }
    .btn-outline-primary:hover { background: var(--sp); color: #fff; }
    .btn-outline-secondary { border-color: var(--border); color: var(--muted); border-radius: 8px; }
    .btn-outline-secondary:hover { background: var(--border); color: var(--text); }
    .btn-danger { background: linear-gradient(135deg,#DC2626,#EF4444); border: none; color: #fff; border-radius: 8px; }
    .btn-outline-danger { border-color: rgba(220,38,38,.4); color: #DC2626; border-radius: 8px; }

    /* ── BADGES ── */
    .badge { font-weight: 600; font-size: .7rem; padding: .3em .7em; border-radius: 6px; }

    /* ── PAGE HEADER ── */
    .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.75rem; flex-wrap: wrap; gap: .75rem; }
    .page-title  { font-size: 1.5rem; font-weight: 700; color: var(--text); margin: 0; }

    /* ── EMPTY STATE ── */
    .empty-state { text-align: center; padding: 3rem 2rem; color: var(--muted); }
    .empty-state i { font-size: 3rem; opacity: .25; margin-bottom: 1rem; display: block; }

    /* ── DROPDOWN ── */
    .dropdown-menu { background: var(--surface); border: 1px solid var(--border); border-radius: 10px; box-shadow: 0 8px 24px rgba(0,0,0,.12); }
    .dropdown-item { color: var(--text); font-size: .85rem; }
    .dropdown-item:hover { background: rgba(124,58,237,.08); color: var(--sp); }

    /* ── COLLAPSED ── */
    #sp-side.collapsed .sp-label,
    #sp-side.collapsed .sp-section,
    #sp-side.collapsed .sp-brand-text { display: none !important; }
    #sp-side.collapsed .sp-link { justify-content: center; padding: .625rem; margin: .1rem .375rem; }
    #sp-side.collapsed .sp-brand { justify-content: center; }
    #sp-side.collapsed .sp-foot { padding: .75rem .5rem; }
    #sp-side.collapsed .sp-foot .d-flex { justify-content: center; }
    #sp-side.collapsed .sp-foot .sp-label { display: none !important; }

    /* ── MOBILE ── */
    @media (max-width: 768px) {
        #sp-side { transform: translateX(-100%); }
        #sp-side.open { transform: translateX(0); }
        #sp-main { margin-left: 0 !important; }
        .sp-content { padding: 1rem; }
    }
    #sp-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 1039; }
    #sp-overlay.on { display: block; }

    /* ── DARK MODE ── */
    [data-theme="dark"] .table thead th { background: var(--bg); }
    [data-theme="dark"] .form-control, [data-theme="dark"] .form-select { background: var(--surface); border-color: var(--border); color: var(--text); }
    [data-theme="dark"] .dropdown-menu { background: var(--surface); border-color: var(--border); }
    [data-theme="dark"] .dropdown-item { color: var(--text); }

    /* ── ANIMATIONS ── */
    @keyframes fadeUp { from { opacity:0; transform:translateY(14px); } to { opacity:1; transform:translateY(0); } }
    .fade-in-up { animation: fadeUp .3s ease forwards; }

    /* ── SCROLLBAR ── */
    ::-webkit-scrollbar { width: 5px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: rgba(124,58,237,.3); border-radius: 3px; }

    /* ── PAGINATION ── */
    .pagination .page-link { background: var(--surface); border-color: var(--border); color: var(--muted); }
    .pagination .page-link:hover { background: rgba(124,58,237,.1); color: var(--sp); }
    .pagination .page-item.active .page-link { background: var(--sp); border-color: var(--sp); color: #fff; }
    </style>
    @stack('styles')
</head>
<body>
<div id="sp-wrap">
    <div id="sp-overlay"></div>

    {{-- ═══ SIDEBAR ═══ --}}
    <aside id="sp-side">
        <div class="sp-brand">
            <div class="d-flex align-items-center gap-2">
                <div class="sp-logo">
                    <i class="fa-solid fa-graduation-cap text-white" style="font-size:1rem;"></i>
                </div>
                <div class="sp-brand-text overflow-hidden">
                    <div style="font-size:.88rem;font-weight:700;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:155px;">
                        {{ auth()->user()->tenant?->name ?? 'Student Portal' }}
                    </div>
                    <div style="font-size:.65rem;color:rgba(255,255,255,.4);margin-top:1px;">Student Portal</div>
                </div>
            </div>
            <button id="sp-toggle" class="btn btn-sm p-1 border-0" style="color:rgba(255,255,255,.5);background:transparent;">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>

        <nav class="sp-nav">
            <a href="{{ route('student.dashboard') }}" class="sp-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-gauge sp-icon"></i><span class="sp-label">Dashboard</span>
            </a>

            <div class="sp-section mt-1">Academics</div>
            <a href="{{ route('student.fees.index') }}" class="sp-link {{ request()->routeIs('student.fees.*') ? 'active' : '' }}">
                <i class="fa-solid fa-indian-rupee-sign sp-icon"></i><span class="sp-label">My Fees</span>
            </a>
            <a href="{{ route('student.attendance.index') }}" class="sp-link {{ request()->routeIs('student.attendance.*') ? 'active' : '' }}">
                <i class="fa-solid fa-calendar-check sp-icon"></i><span class="sp-label">Attendance</span>
            </a>
            <a href="{{ route('student.certificates.index') }}" class="sp-link {{ request()->routeIs('student.certificates.*') ? 'active' : '' }}">
                <i class="fa-solid fa-file-certificate sp-icon"></i><span class="sp-label">Certificates</span>
            </a>

            @php $studentModel = auth()->user()->student; @endphp
            @if($studentModel)
            <a href="{{ route('admin.admissions.receipt', $studentModel) }}" class="sp-link">
                <i class="fa-solid fa-id-card sp-icon"></i><span class="sp-label">Admission Receipt</span>
            </a>
            @endif

            <div class="sp-section mt-1">Account</div>
            <a href="{{ route('student.profile.index') }}" class="sp-link {{ request()->routeIs('student.profile.*') ? 'active' : '' }}">
                <i class="fa-solid fa-user sp-icon"></i><span class="sp-label">My Profile</span>
            </a>
            <a href="{{ route('student.settings') }}" class="sp-link {{ request()->routeIs('student.settings') ? 'active' : '' }}">
                <i class="fa-solid fa-gear sp-icon"></i><span class="sp-label">Settings</span>
            </a>
        </nav>

        <div class="sp-foot">
            <div class="d-flex align-items-center gap-2">
                <img src="{{ auth()->user()->avatar_url }}" class="rounded-circle" style="width:34px;height:34px;object-fit:cover;flex-shrink:0;border:2px solid rgba(124,58,237,.4);">
                <div class="sp-label overflow-hidden">
                    <div style="font-size:.82rem;font-weight:600;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->name }}</div>
                    <div style="font-size:.65rem;color:rgba(167,139,250,.8);">Student</div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="ms-auto">@csrf
                    <button type="submit" class="btn btn-sm p-1 border-0" style="color:rgba(255,255,255,.4);background:transparent;" title="Logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ═══ MAIN ═══ --}}
    <div id="sp-main">
        {{-- Topnav --}}
        <nav id="sp-top">
            <button id="sp-top-toggle" class="btn btn-sm p-2 border-0" style="color:var(--muted);background:transparent;">
                <i class="fa-solid fa-bars" style="font-size:1.1rem;"></i>
            </button>
            <nav aria-label="breadcrumb" class="d-none d-md-block flex-1">
                <ol class="breadcrumb mb-0" style="font-size:.82rem;">
                    <li class="breadcrumb-item">
                        <a href="{{ route('student.dashboard') }}" style="color:var(--sp);text-decoration:none;">
                            <i class="fa-solid fa-house me-1"></i>Home
                        </a>
                    </li>
                    @yield('breadcrumb')
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2 ms-auto">
                {{-- Dark mode toggle --}}
                <button id="sp-theme" class="btn btn-sm p-2 border-0 rounded-2" style="color:var(--muted);background:var(--bg);" title="Toggle Dark Mode">
                    <i class="fa-solid fa-moon" id="sp-theme-icon" style="font-size:1rem;"></i>
                </button>
                {{-- Profile dropdown --}}
                <div class="dropdown">
                    <button class="btn btn-sm p-1 border-0 d-flex align-items-center gap-2" style="background:transparent;" data-bs-toggle="dropdown">
                        <img src="{{ auth()->user()->avatar_url }}" class="rounded-circle" style="width:32px;height:32px;object-fit:cover;border:2px solid var(--border);">
                        <div class="d-none d-md-block text-start">
                            <div style="font-size:.82rem;font-weight:600;color:var(--text);line-height:1.2;">{{ auth()->user()->name }}</div>
                            <div style="font-size:.68rem;color:var(--sp);">Student</div>
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg mt-1" style="min-width:200px;">
                        <li class="px-3 py-2" style="border-bottom:1px solid var(--border);">
                            <div style="font-size:.875rem;font-weight:600;color:var(--text);">{{ auth()->user()->name }}</div>
                            <div style="font-size:.75rem;color:var(--muted);">{{ auth()->user()->email }}</div>
                        </li>
                        <li><a class="dropdown-item py-2 px-3" href="{{ route('student.profile.index') }}"><i class="fa-solid fa-user me-2" style="color:var(--sp);width:16px;"></i>My Profile</a></li>
                        <li><a class="dropdown-item py-2 px-3" href="{{ route('student.settings') }}"><i class="fa-solid fa-gear me-2" style="color:var(--muted);width:16px;"></i>Settings</a></li>
                        <li><hr class="dropdown-divider" style="border-color:var(--border);"></li>
                        <li><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="dropdown-item py-2 px-3" style="color:#DC2626;"><i class="fa-solid fa-right-from-bracket me-2" style="width:16px;"></i>Sign Out</button></form></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="sp-content fade-in-up">
            @yield('content')
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script>
(function(){
    const side=document.getElementById('sp-side'),main=document.getElementById('sp-main'),ov=document.getElementById('sp-overlay');
    function tog(){
        if(window.innerWidth<=768){side.classList.toggle('open');ov.classList.toggle('on');}
        else{side.classList.toggle('collapsed');main.classList.toggle('expanded');}
    }
    document.getElementById('sp-toggle')?.addEventListener('click',tog);
    document.getElementById('sp-top-toggle')?.addEventListener('click',tog);
    ov?.addEventListener('click',()=>{side.classList.remove('open');ov.classList.remove('on');});

    // Dark mode
    const themeBtn=document.getElementById('sp-theme'),themeIcon=document.getElementById('sp-theme-icon'),html=document.documentElement;
    const saved=localStorage.getItem('theme')||'light';
    html.setAttribute('data-theme',saved);
    if(saved==='dark')themeIcon?.classList.replace('fa-moon','fa-sun');
    themeBtn?.addEventListener('click',()=>{
        const c=html.getAttribute('data-theme'),n=c==='dark'?'light':'dark';
        html.setAttribute('data-theme',n);localStorage.setItem('theme',n);
        themeIcon?.classList.toggle('fa-moon',n==='light');
        themeIcon?.classList.toggle('fa-sun',n==='dark');
    });

    @if(session('success'))Swal.fire({icon:'success',title:'Success',text:@json(session('success')),timer:3000,showConfirmButton:false,toast:true,position:'top-end'});@endif
    @if(session('error'))Swal.fire({icon:'error',title:'Error',text:@json(session('error')),timer:4000,showConfirmButton:false,toast:true,position:'top-end'});@endif
})();
</script>
@stack('scripts')
</body>
</html>

