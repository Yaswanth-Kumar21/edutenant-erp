<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — EduTenant ERP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
    /* ═══════════════════════════════════════════════════════════════
       ADMIN LAYOUT — ROLE-AWARE DESIGN SYSTEM
       College Admin: Blue  (#1E40AF sidebar, #2563EB primary)
       Staff:         Teal  (#0F766E sidebar, #14B8A6 primary)
       Teacher:       Coffee(#3D2B1F sidebar, #8B6B4A primary)
    ═══════════════════════════════════════════════════════════════ */
    :root {
        /* Default: College Admin */
        --primary:    #2563EB;
        --primary-d:  #1D4ED8;
        --primary-l:  #3B82F6;
        --primary-xl: #DBEAFE;
        --sidebar-bg: #1E3A8A;
        --sw:         260px;
        --sc:         68px;
        --font:       'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        --radius:     8px;
        --radius-lg:  12px;
        --tr:         all 0.18s ease;
        /* Light */
        --bg:         #F8FAFC;
        --surface:    #FFFFFF;
        --surface2:   #F1F5F9;
        --text:       #0F172A;
        --text2:      #334155;
        --muted:      #64748B;
        --text-muted: #64748B;
        --border:     #E2E8F0;
        --border2:    #CBD5E1;
        --shadow:     0 1px 2px rgba(0,0,0,.05);
        --shadow-sm:  0 1px 3px rgba(0,0,0,.08);
        --shadow-md:  0 4px 6px rgba(0,0,0,.07);
        --shadow-lg:  0 10px 15px rgba(0,0,0,.1);
        /* Semantic colors */
        --success:    #10B981;
        --warning:    #F59E0B;
        --danger:     #EF4444;
        --info:       #3B82F6;
    }
    /* Staff override */
    body[data-role="staff"] {
        --primary:    #14B8A6;
        --primary-d:  #0F766E;
        --primary-l:  #5EEAD4;
        --sidebar-bg: #0F766E;
        --bg:         #F0FDFA;
    }
    /* Teacher override */
    body[data-role="teacher"] {
        --primary:    #8B6B4A;
        --primary-d:  #5C4033;
        --primary-l:  #D4A574;
        --sidebar-bg: #3D2B1F;
        --bg:         #FFF8F0;
        --surface:    #FFFDF9;
        --border:     #E8D5C0;
        --text:       #2C1810;
        --muted:      #8B6B4A;
    }
    [data-theme="dark"] {
        --bg:         #0F172A;
        --surface:    #1E293B;
        --surface2:   #0F172A;
        --text:       #F1F5F9;
        --text2:      #CBD5E1;
        --muted:      #64748B;
        --text-muted: #64748B;
        --border:     #334155;
        --border2:    #475569;
        --shadow:     0 1px 2px rgba(0,0,0,.3);
        --shadow-sm:  0 1px 3px rgba(0,0,0,.4);
        --shadow-md:  0 4px 6px rgba(0,0,0,.3);
        --shadow-lg:  0 10px 15px rgba(0,0,0,.4);
        --primary-xl: rgba(37,99,235,.15);
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: var(--font); background: var(--bg); color: var(--text); overflow-x: hidden; transition: background .3s, color .3s; }

    /* ── LAYOUT ── */
    #app-wrapper { display: flex; min-height: 100vh; }

    /* ── SIDEBAR ── */
    #sidebar {
        width: var(--sw); min-height: 100vh;
        background: var(--sidebar-bg);
        position: fixed; top: 0; left: 0; z-index: 1040;
        display: flex; flex-direction: column;
        transition: var(--tr); overflow: hidden;
    }
    #sidebar.collapsed { width: var(--sc); }

    /* Brand */
    .sidebar-brand {
        display: flex; align-items: center; justify-content: space-between;
        padding: 1.25rem 1rem; min-height: 68px;
        border-bottom: 1px solid rgba(255,255,255,.08);
    }
    .brand-icon {
        width: 38px; height: 38px; border-radius: 10px; flex-shrink: 0;
        background: rgba(255,255,255,.2);
        display: flex; align-items: center; justify-content: center;
    }

    /* Nav */
    .sidebar-nav { flex: 1; overflow-y: auto; overflow-x: hidden; padding: .5rem 0; }
    .sidebar-nav::-webkit-scrollbar { width: 3px; }
    .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.2); border-radius: 2px; }

    .sidebar-section {
        padding: .6rem 1.25rem .2rem;
        font-size: .6rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: .1em;
        color: rgba(255,255,255,.3);
    }
    .sidebar-link {
        display: flex; align-items: center; gap: .75rem;
        padding: .6rem 1rem; margin: .1rem .5rem;
        color: rgba(255,255,255,.6); text-decoration: none;
        font-size: .84rem; font-weight: 500;
        border-radius: 8px; transition: var(--tr); white-space: nowrap;
        cursor: pointer; background: transparent; border: none; width: calc(100% - 1rem);
    }
    .sidebar-link:hover { color: #fff; background: rgba(255,255,255,.1); }
    .sidebar-link.active { color: #fff; background: rgba(255,255,255,.2); box-shadow: 0 2px 8px rgba(0,0,0,.15); }
    .sidebar-icon { font-size: .95rem; width: 20px; text-align: center; flex-shrink: 0; }
    .sidebar-label { flex: 1; overflow: hidden; text-overflow: ellipsis; }
    .sidebar-arrow { font-size: .65rem; transition: transform .2s; flex-shrink: 0; }
    .sidebar-collapse-btn[aria-expanded="true"] .sidebar-arrow { transform: rotate(90deg); }

    .sidebar-sub-link {
        display: flex; align-items: center;
        padding: .45rem 1rem .45rem 2.75rem; margin: .08rem .5rem;
        color: rgba(255,255,255,.5); text-decoration: none;
        font-size: .82rem; font-weight: 400;
        border-radius: 6px; transition: var(--tr); white-space: nowrap;
    }
    .sidebar-sub-link:hover { color: rgba(255,255,255,.9); background: rgba(255,255,255,.08); }
    .sidebar-sub-link.active { color: #fff; background: rgba(255,255,255,.15); }

    /* User footer */
    .sidebar-user { border-top: 1px solid rgba(255,255,255,.08); padding: .875rem 1rem; }

    /* ── MAIN ── */
    #main-content { margin-left: var(--sw); flex: 1; display: flex; flex-direction: column; transition: var(--tr); min-height: 100vh; }
    #main-content.expanded { margin-left: var(--sc); }
    .page-content { padding: 1.75rem; flex: 1; }

    /* ── TOPNAV ── */
    .topnav {
        height: 64px; background: var(--surface);
        border-bottom: 1px solid var(--border);
        display: flex; align-items: center; padding: 0 1.5rem; gap: 1rem;
        position: sticky; top: 0; z-index: 1030;
        box-shadow: var(--shadow);
    }

    /* ── CARDS ── */
    .card {
        background: var(--surface); border: 1px solid var(--border);
        border-radius: var(--radius-lg, 12px); transition: var(--tr);
        box-shadow: var(--shadow);
    }
    .card:hover { box-shadow: var(--shadow-sm); }
    .card-header {
        background: transparent; border-bottom: 1px solid var(--border);
        padding: 14px 20px; font-weight: 600; font-size: 14px; color: var(--text);
        display: flex; align-items: center; justify-content: space-between;
    }
    .card-body { padding: 20px; }

    /* ── STAT CARDS ── */
    .stat-card {
        border-radius: var(--radius); padding: 1.5rem; color: #fff;
        position: relative; overflow: hidden; transition: var(--tr);
        box-shadow: 0 4px 15px rgba(0,0,0,.12);
    }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,.18); }
    .stat-card::after { content:''; position:absolute; top:-30px; right:-30px; width:100px; height:100px; border-radius:50%; background:rgba(255,255,255,.1); }
    .stat-card.blue   { background: linear-gradient(135deg,#2563EB,#4F46E5); }
    .stat-card.green  { background: linear-gradient(135deg,#059669,#10B981); }
    .stat-card.orange { background: linear-gradient(135deg,#D97706,#F59E0B); }
    .stat-card.purple { background: linear-gradient(135deg,#7C3AED,#A855F7); }
    .stat-card.teal   { background: linear-gradient(135deg,#0891B2,#06B6D4); }
    .stat-card.amber  { background: linear-gradient(135deg,#8B6B4A,#B08968); }
    .stat-card.red    { background: linear-gradient(135deg,#DC2626,#EF4444); }
    .stat-card .stat-value { font-size: 2rem; font-weight: 800; line-height: 1; }
    .stat-card .stat-label { font-size: .82rem; opacity: .9; font-weight: 500; margin-top: .25rem; }
    .stat-card .stat-icon  { font-size: 2.5rem; opacity: .8; }

    /* ── TABLES ── */
    .table { color: var(--text); font-size: 13px; }
    .table thead th { background: var(--surface2, #F1F5F9); color: var(--muted); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; border-bottom: 1px solid var(--border); padding: 10px 16px; white-space: nowrap; }
    .table tbody td { padding: 13px 16px; border-bottom: 1px solid var(--border); vertical-align: middle; color: var(--text); }
    .table tbody tr:last-child td { border-bottom: none; }
    .table tbody tr:hover td { background: var(--surface2, #F8FAFC); }

    /* ── FORMS ── */
    .form-control, .form-select { background: var(--surface); border: 1px solid var(--border); color: var(--text); border-radius: 8px; transition: var(--tr); font-size: 13px; padding: 8px 12px; }
    .form-control:focus, .form-select:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37,99,235,.1); background: var(--surface); color: var(--text); outline: none; }
    .form-control::placeholder { color: var(--muted); }
    .form-label { font-weight: 500; font-size: 13px; color: var(--text2, #334155); margin-bottom: 6px; display: block; }
    .form-select option { background: var(--surface); color: var(--text); }
    .form-text { font-size: 11px; color: var(--muted); margin-top: 4px; }
    .input-group-text { background: var(--surface2, #F1F5F9); border-color: var(--border); color: var(--muted); font-size: 13px; }

    /* ── BUTTONS ── */
    .btn-primary { background: linear-gradient(135deg, var(--primary), var(--primary-d)); border: none; font-weight: 600; color: #fff; border-radius: 8px; }
    .btn-primary:hover { opacity: .9; transform: translateY(-1px); box-shadow: 0 4px 15px rgba(0,0,0,.2); color: #fff; }
    .btn-outline-primary { border-color: var(--primary); color: var(--primary); border-radius: 8px; }
    .btn-outline-primary:hover { background: var(--primary); color: #fff; }
    .btn-outline-secondary { border-color: var(--border); color: var(--muted); border-radius: 8px; }
    .btn-outline-secondary:hover { background: var(--border); color: var(--text); border-color: var(--border); }
    .btn-success { background: linear-gradient(135deg,#059669,#10B981); border: none; color: #fff; border-radius: 8px; font-weight: 600; }
    .btn-danger  { background: linear-gradient(135deg,#DC2626,#EF4444); border: none; color: #fff; border-radius: 8px; font-weight: 600; }
    .btn-outline-danger { border-color: rgba(220,38,38,.4); color: #DC2626; border-radius: 8px; }
    .btn-outline-danger:hover { background: rgba(220,38,38,.08); color: #DC2626; }
    .btn-warning { background: linear-gradient(135deg,#D97706,#F59E0B); border: none; color: #fff; border-radius: 8px; font-weight: 600; }

    /* ── BADGES ── */
    .badge { font-weight: 600; font-size: 11px; padding: 3px 8px; border-radius: 20px; }

    /* ── PAGE HEADER ── */
    .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
    .page-title  { font-size: 20px; font-weight: 700; color: var(--text); margin: 0 0 4px; line-height: 1.2; }
    .page-sub    { font-size: 13px; color: var(--muted); margin: 0; }

    /* ── EMPTY STATE ── */
    .empty-state { text-align: center; padding: 48px 24px; color: var(--muted); }
    .empty-state i { font-size: 2.5rem; opacity: .2; margin-bottom: 12px; display: block; }
    .empty-state h5 { font-size: 15px; font-weight: 600; color: var(--text); margin: 0 0 6px; }
    .empty-state p  { font-size: 13px; color: var(--muted); margin: 0 0 16px; }

    /* ── DROPDOWN ── */
    .dropdown-menu { background: var(--surface); border: 1px solid var(--border); border-radius: 10px; box-shadow: 0 8px 24px rgba(0,0,0,.1); padding: 4px; }
    .dropdown-item { color: var(--text); font-size: 13px; padding: 8px 12px; border-radius: 6px; display: flex; align-items: center; gap: 8px; }
    .dropdown-item:hover { background: var(--surface2, #F1F5F9); color: var(--text); }

    /* ── MODAL ── */
    .modal-content { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); color: var(--text); }
    .modal-header, .modal-footer { border-color: var(--border); }

    /* ── INPUT GROUP ── */
    .input-group-text { background: var(--bg); border-color: var(--border); color: var(--muted); }

    /* ── COLLAPSED ── */
    #sidebar.collapsed .sidebar-label,
    #sidebar.collapsed .sidebar-arrow,
    #sidebar.collapsed .brand-text,
    #sidebar.collapsed .sidebar-user .user-info,
    #sidebar.collapsed .sidebar-section,
    #sidebar.collapsed .sidebar-sub-link { display: none !important; }
    #sidebar.collapsed .sidebar-link { justify-content: center; padding: .625rem; margin: .1rem .375rem; width: calc(100% - .75rem); }
    #sidebar.collapsed .sidebar-icon { width: auto; }
    #sidebar.collapsed .sidebar-brand { justify-content: center; }
    #sidebar.collapsed .sidebar-user { padding: .75rem .5rem; }
    #sidebar.collapsed .sidebar-user .d-flex { justify-content: center; }
    #sidebar.collapsed .collapse { display: none !important; }

    /* ── MOBILE ── */
    @media (max-width: 768px) {
        #sidebar { transform: translateX(-100%); }
        #sidebar.mobile-open { transform: translateX(0); }
        #main-content { margin-left: 0 !important; }
        .page-content { padding: 1rem; }
    }
    #sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 1039; }
    #sidebar-overlay.active { display: block; }

    /* ── DARK MODE ── */
    [data-theme="dark"] .table thead th { background: var(--bg); color: var(--muted); }
    [data-theme="dark"] .table tbody td { color: var(--text); border-color: var(--border); }
    [data-theme="dark"] .form-control, [data-theme="dark"] .form-select { background: var(--surface); border-color: var(--border); color: var(--text); }
    [data-theme="dark"] .form-control::placeholder { color: var(--muted); }
    [data-theme="dark"] .card { background: var(--surface); border-color: var(--border); }
    [data-theme="dark"] .card-header { border-color: var(--border); color: var(--text); }
    [data-theme="dark"] .dropdown-menu { background: var(--surface); border-color: var(--border); }
    [data-theme="dark"] .dropdown-item { color: var(--text); }
    [data-theme="dark"] .modal-content { background: var(--surface); border-color: var(--border); color: var(--text); }
    [data-theme="dark"] .input-group-text { background: var(--surface); border-color: var(--border); color: var(--muted); }
    [data-theme="dark"] .btn-outline-secondary { color: var(--muted); border-color: var(--border); }
    [data-theme="dark"] .btn-outline-secondary:hover { background: var(--border); color: var(--text); }
    [data-theme="dark"] .page-title { color: var(--text); }
    [data-theme="dark"] .breadcrumb-item a { color: var(--primary-l); }
    [data-theme="dark"] .breadcrumb-item.active { color: var(--muted); }
    [data-theme="dark"] .breadcrumb-item + .breadcrumb-item::before { color: var(--muted); }

    /* ── ANIMATIONS ── */
    @keyframes fadeUp { from { opacity:0; transform:translateY(14px); } to { opacity:1; transform:translateY(0); } }
    .fade-in-up { animation: fadeUp .3s ease forwards; }

    /* ── SCROLLBAR ── */
    ::-webkit-scrollbar { width: 5px; height: 5px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: rgba(0,0,0,.15); border-radius: 3px; }

    /* ── PAGINATION ── */
    .pagination .page-link { background: var(--surface); border-color: var(--border); color: var(--muted); border-radius: 6px; }
    .pagination .page-link:hover { background: rgba(37,99,235,.08); color: var(--primary); }
    .pagination .page-item.active .page-link { background: var(--primary); border-color: var(--primary); color: #fff; }

    /* ── ALERTS ── */
    .alert-danger  { background: rgba(220,38,38,.08);  border-color: rgba(220,38,38,.2);  color: #B91C1C; }
    .alert-success { background: rgba(5,150,105,.08);  border-color: rgba(5,150,105,.2);  color: #065F46; }
    .alert-warning { background: rgba(217,119,6,.08);  border-color: rgba(217,119,6,.2);  color: #92400E; }
    .alert-info    { background: rgba(8,145,178,.08);  border-color: rgba(8,145,178,.2);  color: #0E7490; }
    [data-theme="dark"] .alert-danger  { color: #FCA5A5; }
    [data-theme="dark"] .alert-success { color: #6EE7B7; }
    [data-theme="dark"] .alert-warning { color: #FCD34D; }
    [data-theme="dark"] .alert-info    { color: #67E8F9; }

    /* ── INVALID FEEDBACK ── */
    .is-invalid { border-color: #DC2626 !important; }
    .invalid-feedback { color: #DC2626; font-size: .78rem; }
    [data-theme="dark"] .invalid-feedback { color: #F87171; }

    /* ── PROGRESS ── */
    .progress { background: var(--border); border-radius: 4px; }
    </style>
    @stack('styles')
</head>
<body data-role="{{ auth()->user()?->role?->name ?? 'guest' }}">

<div id="app-wrapper">
    <div id="sidebar-overlay"></div>
    @include('layouts.sidebar')
    <div id="main-content">
        @include('layouts.topnav')
        <div class="page-content fade-in-up">
            @yield('content')
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
@include('layouts.scripts')
@stack('scripts')
</body>
</html>
