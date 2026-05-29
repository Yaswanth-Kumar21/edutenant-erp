<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Center') — EduTenant Platform</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
    /* ═══════════════════════════════════════════════════════════
       EDUTENANT SUPER ADMIN — ENTERPRISE SAAS DESIGN SYSTEM
       Inspired by: Microsoft Admin Center, Atlassian, Salesforce
    ═══════════════════════════════════════════════════════════ */
    :root {
        /* Brand */
        --blue:       #2563EB;
        --blue-d:     #1D4ED8;
        --blue-l:     #3B82F6;
        --blue-xl:    #DBEAFE;
        --indigo:     #4F46E5;
        --green:      #059669;
        --green-l:    #D1FAE5;
        --orange:     #D97706;
        --orange-l:   #FEF3C7;
        --red:        #DC2626;
        --red-l:      #FEE2E2;
        --purple:     #7C3AED;
        --purple-l:   #EDE9FE;
        --cyan:       #0891B2;
        --cyan-l:     #CFFAFE;
        /* Light mode (default) */
        --bg:         #F8FAFC;
        --surface:    #FFFFFF;
        --surface2:   #F1F5F9;
        --sidebar:    #1E293B;
        --sidebar2:   #0F172A;
        --text:       #0F172A;
        --text2:      #334155;
        --muted:      #64748B;
        --text-muted: #64748B;
        --border:     #E2E8F0;
        --border2:    #CBD5E1;
        --shadow-sm:  0 1px 2px rgba(0,0,0,.05);
        --shadow:     0 1px 3px rgba(0,0,0,.1), 0 1px 2px rgba(0,0,0,.06);
        --shadow-md:  0 4px 6px rgba(0,0,0,.07), 0 2px 4px rgba(0,0,0,.06);
        --shadow-lg:  0 10px 15px rgba(0,0,0,.1), 0 4px 6px rgba(0,0,0,.05);
        --primary:    #2563EB;
        --primary-d:  #1D4ED8;
        --primary-l:  #3B82F6;
        --primary-xl: #DBEAFE;
        --accent:     #3B82F6;
        --radius:     8px;
        --radius-lg:  12px;
        --radius-xl:  16px;
        --sw:         256px;
        --sc:         64px;
        --font:       'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        --tr:         all 0.15s ease;
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
        --shadow-sm:  0 1px 2px rgba(0,0,0,.3);
        --shadow:     0 1px 3px rgba(0,0,0,.4);
        --shadow-md:  0 4px 6px rgba(0,0,0,.3);
        --shadow-lg:  0 10px 15px rgba(0,0,0,.4);
        --primary-xl: rgba(37,99,235,.15);
        --blue-xl:    rgba(37,99,235,.15);
        --green-l:    rgba(5,150,105,.15);
        --orange-l:   rgba(217,119,6,.15);
        --red-l:      rgba(220,38,38,.15);
        --purple-l:   rgba(124,58,237,.15);
        --cyan-l:     rgba(8,145,178,.15);
    }
    /* ── RESET & BASE ── */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { height: 100%; scroll-behavior: smooth; }
    body { font-family: var(--font); background: var(--bg); color: var(--text); overflow-x: hidden; font-size: 14px; line-height: 1.5; transition: background .2s, color .2s; }
    a { text-decoration: none; color: inherit; }
    /* ── LAYOUT ── */
    #sa-wrap { display: flex; min-height: 100vh; }
    /* ── SIDEBAR ── */
    #sa-side {
        width: var(--sw); min-height: 100vh;
        background: var(--sidebar);
        position: fixed; top: 0; left: 0; z-index: 1040;
        display: flex; flex-direction: column;
        transition: width .2s ease; overflow: hidden;
        border-right: 1px solid rgba(255,255,255,.06);
    }
    #sa-side.collapsed { width: var(--sc); }
    /* Brand */
    .sa-brand {
        display: flex; align-items: center; gap: 10px;
        padding: 0 16px; height: 60px; min-height: 60px;
        border-bottom: 1px solid rgba(255,255,255,.06);
        flex-shrink: 0;
    }
    .sa-brand-icon {
        width: 32px; height: 32px; border-radius: 8px; flex-shrink: 0;
        background: linear-gradient(135deg, var(--blue), var(--indigo));
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 2px 8px rgba(37,99,235,.4);
    }
    .sa-brand-text { overflow: hidden; flex: 1; }
    .sa-brand-name { font-size: 13px; font-weight: 700; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.2; }
    .sa-brand-sub  { font-size: 10px; color: rgba(255,255,255,.4); margin-top: 1px; }
    .sa-toggle-btn { width: 28px; height: 28px; border-radius: 6px; border: none; background: rgba(255,255,255,.06); color: rgba(255,255,255,.5); cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: var(--tr); }
    .sa-toggle-btn:hover { background: rgba(255,255,255,.12); color: #fff; }
    /* Nav */
    .sa-nav { flex: 1; overflow-y: auto; overflow-x: hidden; padding: 8px 0; }
    .sa-nav::-webkit-scrollbar { width: 3px; }
    .sa-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.15); border-radius: 2px; }
    .sa-section { padding: 16px 16px 4px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: rgba(255,255,255,.25); white-space: nowrap; overflow: hidden; }
    .sa-link {
        display: flex; align-items: center; gap: 10px;
        padding: 8px 12px; margin: 1px 8px;
        color: rgba(255,255,255,.55); font-size: 13px; font-weight: 500;
        border-radius: 6px; transition: var(--tr); white-space: nowrap;
        cursor: pointer; background: transparent; border: none; width: calc(100% - 16px);
    }
    .sa-link:hover { color: rgba(255,255,255,.9); background: rgba(255,255,255,.07); }
    .sa-link.active { color: #fff; background: rgba(37,99,235,.35); }
    .sa-link.active .sa-icon { color: #60A5FA; }
    .sa-icon { font-size: 14px; width: 18px; text-align: center; flex-shrink: 0; color: rgba(255,255,255,.4); transition: var(--tr); }
    .sa-link:hover .sa-icon { color: rgba(255,255,255,.8); }
    .sa-label { flex: 1; overflow: hidden; text-overflow: ellipsis; }
    .sa-badge-count { background: var(--blue); color: #fff; font-size: 10px; font-weight: 700; padding: 1px 6px; border-radius: 10px; flex-shrink: 0; }
    /* User footer */
    .sa-foot { border-top: 1px solid rgba(255,255,255,.06); padding: 12px 16px; flex-shrink: 0; }
    /* ── MAIN ── */
    #sa-main { margin-left: var(--sw); flex: 1; display: flex; flex-direction: column; transition: margin-left .2s ease; min-height: 100vh; }
    #sa-main.expanded { margin-left: var(--sc); }
    .sa-content { padding: 24px; flex: 1; max-width: 1400px; }
    /* ── TOPNAV ── */
    #sa-top {
        height: 60px; background: var(--surface);
        border-bottom: 1px solid var(--border);
        display: flex; align-items: center; padding: 0 24px; gap: 12px;
        position: sticky; top: 0; z-index: 1030;
        box-shadow: var(--shadow-sm);
    }
    /* ── SEARCH BAR ── */
    .sa-search { position: relative; }
    .sa-search input { background: var(--surface2); border: 1px solid var(--border); color: var(--text); border-radius: 6px; padding: 6px 12px 6px 34px; font-size: 13px; width: 240px; transition: var(--tr); }
    .sa-search input:focus { outline: none; border-color: var(--blue); box-shadow: 0 0 0 3px rgba(37,99,235,.12); width: 280px; }
    .sa-search input::placeholder { color: var(--muted); }
    .sa-search .sa-search-icon { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 12px; pointer-events: none; }
    /* ── CARDS ── */
    .card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); transition: var(--tr); }
    .card:hover { box-shadow: var(--shadow); }
    .card-header { background: transparent; border-bottom: 1px solid var(--border); padding: 16px 20px; font-weight: 600; font-size: 14px; color: var(--text); display: flex; align-items: center; justify-content: space-between; }
    .card-body { padding: 20px; }
    /* ── KPI CARDS ── */
    .kpi-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 20px; transition: var(--tr); position: relative; overflow: hidden; }
    .kpi-card:hover { box-shadow: var(--shadow-md); transform: translateY(-1px); }
    .kpi-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; border-radius: var(--radius-lg) var(--radius-lg) 0 0; }
    .kpi-card.blue::before   { background: linear-gradient(90deg, var(--blue), var(--blue-l)); }
    .kpi-card.green::before  { background: linear-gradient(90deg, var(--green), #10B981); }
    .kpi-card.orange::before { background: linear-gradient(90deg, var(--orange), #F59E0B); }
    .kpi-card.purple::before { background: linear-gradient(90deg, var(--purple), #A855F7); }
    .kpi-card.cyan::before   { background: linear-gradient(90deg, var(--cyan), #06B6D4); }
    .kpi-card.red::before    { background: linear-gradient(90deg, var(--red), #EF4444); }
    .kpi-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }
    .kpi-icon.blue   { background: var(--blue-xl);   color: var(--blue); }
    .kpi-icon.green  { background: var(--green-l);   color: var(--green); }
    .kpi-icon.orange { background: var(--orange-l);  color: var(--orange); }
    .kpi-icon.purple { background: var(--purple-l);  color: var(--purple); }
    .kpi-icon.cyan   { background: var(--cyan-l);    color: var(--cyan); }
    .kpi-icon.red    { background: var(--red-l);     color: var(--red); }
    .kpi-value { font-size: 28px; font-weight: 800; color: var(--text); line-height: 1; margin: 8px 0 4px; }
    .kpi-label { font-size: 12px; font-weight: 500; color: var(--muted); text-transform: uppercase; letter-spacing: .04em; }
    .kpi-change { font-size: 12px; font-weight: 600; display: flex; align-items: center; gap: 3px; margin-top: 6px; }
    .kpi-change.up   { color: var(--green); }
    .kpi-change.down { color: var(--red); }
    /* ── TABLES ── */
    .sa-table { width: 100%; border-collapse: collapse; }
    .sa-table thead th { background: var(--surface2); color: var(--muted); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; padding: 10px 16px; border-bottom: 1px solid var(--border); white-space: nowrap; }
    .sa-table tbody td { padding: 14px 16px; border-bottom: 1px solid var(--border); font-size: 13px; color: var(--text); vertical-align: middle; }
    .sa-table tbody tr:last-child td { border-bottom: none; }
    .sa-table tbody tr:hover td { background: var(--surface2); }
    /* ── BUTTONS ── */
    .btn-primary { background: var(--blue); border: 1px solid var(--blue-d); color: #fff; font-weight: 600; font-size: 13px; border-radius: var(--radius); padding: 8px 16px; transition: var(--tr); cursor: pointer; display: inline-flex; align-items: center; gap: 6px; }
    .btn-primary:hover { background: var(--blue-d); color: #fff; box-shadow: 0 4px 12px rgba(37,99,235,.3); transform: translateY(-1px); }
    .btn-secondary { background: var(--surface); border: 1px solid var(--border); color: var(--text2); font-weight: 500; font-size: 13px; border-radius: var(--radius); padding: 8px 16px; transition: var(--tr); cursor: pointer; display: inline-flex; align-items: center; gap: 6px; }
    .btn-secondary:hover { background: var(--surface2); border-color: var(--border2); color: var(--text); }
    .btn-danger { background: var(--red); border: 1px solid #B91C1C; color: #fff; font-weight: 600; font-size: 13px; border-radius: var(--radius); padding: 8px 16px; transition: var(--tr); cursor: pointer; display: inline-flex; align-items: center; gap: 6px; }
    .btn-danger:hover { background: #B91C1C; color: #fff; }
    .btn-ghost { background: transparent; border: 1px solid transparent; color: var(--muted); font-size: 13px; border-radius: var(--radius); padding: 6px 10px; transition: var(--tr); cursor: pointer; display: inline-flex; align-items: center; gap: 6px; }
    .btn-ghost:hover { background: var(--surface2); color: var(--text); border-color: var(--border); }
    .btn-icon { width: 32px; height: 32px; border-radius: var(--radius); border: 1px solid var(--border); background: var(--surface); color: var(--muted); display: inline-flex; align-items: center; justify-content: center; font-size: 13px; cursor: pointer; transition: var(--tr); }
    .btn-icon:hover { background: var(--surface2); color: var(--text); border-color: var(--border2); }
    .btn-icon.danger:hover { background: var(--red-l); color: var(--red); border-color: rgba(220,38,38,.3); }
    /* ── FORMS ── */
    .form-control, .form-select { background: var(--surface); border: 1px solid var(--border); color: var(--text); border-radius: var(--radius); padding: 8px 12px; font-size: 13px; font-family: var(--font); transition: var(--tr); width: 100%; }
    .form-control:focus, .form-select:focus { outline: none; border-color: var(--blue); box-shadow: 0 0 0 3px rgba(37,99,235,.1); background: var(--surface); color: var(--text); }
    .form-control::placeholder { color: var(--muted); }
    .form-label { font-size: 13px; font-weight: 500; color: var(--text2); margin-bottom: 6px; display: block; }
    .form-hint { font-size: 11px; color: var(--muted); margin-top: 4px; }
    .form-select option { background: var(--surface); color: var(--text); }
    .input-group { display: flex; }
    .input-group .form-control { border-radius: var(--radius) 0 0 var(--radius); }
    .input-group .input-group-text { background: var(--surface2); border: 1px solid var(--border); border-left: none; color: var(--muted); padding: 8px 12px; font-size: 13px; border-radius: 0 var(--radius) var(--radius) 0; }
    /* ── BADGES ── */
    .badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; }
    .badge-active   { background: var(--green-l);  color: #065F46; }
    .badge-inactive { background: var(--red-l);    color: #991B1B; }
    .badge-pending  { background: var(--orange-l); color: #92400E; }
    .badge-blue     { background: var(--blue-xl);  color: #1E40AF; }
    .badge-purple   { background: var(--purple-l); color: #5B21B6; }
    /* ── PAGE HEADER ── */
    .page-header { margin-bottom: 24px; }
    .page-title { font-size: 20px; font-weight: 700; color: var(--text); margin: 0 0 4px; }
    .page-sub   { font-size: 13px; color: var(--muted); margin: 0; }
    /* ── DIVIDER ── */
    .sa-divider { border: none; border-top: 1px solid var(--border); margin: 0; }
    /* ── EMPTY STATE ── */
    .empty-state { text-align: center; padding: 48px 24px; }
    .empty-state-icon { width: 56px; height: 56px; border-radius: 14px; background: var(--surface2); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 22px; color: var(--muted); }
    .empty-state h5 { font-size: 15px; font-weight: 600; color: var(--text); margin: 0 0 6px; }
    .empty-state p  { font-size: 13px; color: var(--muted); margin: 0 0 16px; }
    /* ── DROPDOWN ── */
    .dropdown-menu { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); padding: 4px; min-width: 180px; }
    .dropdown-item { font-size: 13px; color: var(--text2); padding: 8px 12px; border-radius: 6px; display: flex; align-items: center; gap: 8px; }
    .dropdown-item:hover { background: var(--surface2); color: var(--text); }
    .dropdown-item.danger { color: var(--red); }
    .dropdown-item.danger:hover { background: var(--red-l); }
    .dropdown-divider { border-color: var(--border); margin: 4px 0; }
    /* ── MODAL ── */
    .modal-content { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-xl); box-shadow: var(--shadow-lg); color: var(--text); }
    .modal-header { border-bottom: 1px solid var(--border); padding: 20px 24px; }
    .modal-body   { padding: 24px; }
    .modal-footer { border-top: 1px solid var(--border); padding: 16px 24px; }
    .modal-title  { font-size: 16px; font-weight: 700; color: var(--text); }
    .btn-close    { filter: none; opacity: .5; }
    [data-theme="dark"] .btn-close { filter: invert(1); }
    /* ── ALERTS ── */
    .alert { border-radius: var(--radius); padding: 12px 16px; font-size: 13px; border: 1px solid; display: flex; align-items: flex-start; gap: 10px; }
    .alert-info    { background: rgba(37,99,235,.08);  border-color: rgba(37,99,235,.2);  color: #1E40AF; }
    .alert-success { background: rgba(5,150,105,.08);  border-color: rgba(5,150,105,.2);  color: #065F46; }
    .alert-warning { background: rgba(217,119,6,.08);  border-color: rgba(217,119,6,.2);  color: #92400E; }
    .alert-danger  { background: rgba(220,38,38,.08);  border-color: rgba(220,38,38,.2);  color: #991B1B; }
    [data-theme="dark"] .alert-info    { color: #93C5FD; }
    [data-theme="dark"] .alert-success { color: #6EE7B7; }
    [data-theme="dark"] .alert-warning { color: #FCD34D; }
    [data-theme="dark"] .alert-danger  { color: #FCA5A5; }
    /* ── TABS ── */
    .sa-tabs { display: flex; gap: 2px; border-bottom: 1px solid var(--border); margin-bottom: 24px; }
    .sa-tab { padding: 10px 16px; font-size: 13px; font-weight: 500; color: var(--muted); border: none; background: transparent; cursor: pointer; border-bottom: 2px solid transparent; margin-bottom: -1px; transition: var(--tr); display: flex; align-items: center; gap: 6px; border-radius: 6px 6px 0 0; }
    .sa-tab:hover { color: var(--text); background: var(--surface2); }
    .sa-tab.active { color: var(--blue); border-bottom-color: var(--blue); background: transparent; }
    /* ── PROGRESS ── */
    .sa-progress { height: 6px; background: var(--surface2); border-radius: 3px; overflow: hidden; }
    .sa-progress-bar { height: 100%; border-radius: 3px; transition: width .4s ease; }
    /* ── PAGINATION ── */
    .pagination .page-link { background: var(--surface); border-color: var(--border); color: var(--muted); font-size: 13px; padding: 6px 12px; }
    .pagination .page-link:hover { background: var(--surface2); color: var(--text); }
    .pagination .page-item.active .page-link { background: var(--blue); border-color: var(--blue); color: #fff; }
    /* ── INVALID ── */
    .is-invalid { border-color: var(--red) !important; }
    .invalid-feedback { font-size: 12px; color: var(--red); margin-top: 4px; }
    /* ── COLLAPSED SIDEBAR ── */
    #sa-side.collapsed .sa-label, #sa-side.collapsed .sa-section,
    #sa-side.collapsed .sa-brand-text, #sa-side.collapsed .sa-badge-count { display: none !important; }
    #sa-side.collapsed .sa-link { justify-content: center; padding: 8px; margin: 1px 6px; width: calc(100% - 12px); }
    #sa-side.collapsed .sa-brand { justify-content: center; padding: 0 12px; }
    #sa-side.collapsed .sa-toggle-btn { display: none; }
    #sa-side.collapsed .sa-foot { padding: 12px 8px; }
    #sa-side.collapsed .sa-foot .d-flex { justify-content: center; }
    #sa-side.collapsed .sa-foot .sa-label { display: none !important; }
    /* ── MOBILE ── */
    @media (max-width: 768px) { #sa-side { transform: translateX(-100%); } #sa-side.open { transform: translateX(0); } #sa-main { margin-left: 0 !important; } .sa-content { padding: 16px; } }
    #sa-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.4); z-index: 1039; backdrop-filter: blur(2px); }
    #sa-overlay.on { display: block; }
    /* ── ANIMATIONS ── */
    @keyframes fadeUp { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }
    .fade-in-up { animation: fadeUp .25s ease forwards; }
    /* ── SCROLLBAR ── */
    ::-webkit-scrollbar { width: 5px; height: 5px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: var(--border2); border-radius: 3px; }
    /* ── MISC ── */
    code { background: var(--surface2); color: var(--blue); padding: 2px 6px; border-radius: 4px; font-size: 12px; border: 1px solid var(--border); }
    .text-muted { color: var(--muted) !important; }
    .text-primary { color: var(--blue) !important; }
    .border-bottom { border-bottom: 1px solid var(--border) !important; }
    .form-check-input:checked { background-color: var(--blue); border-color: var(--blue); }
    .form-switch .form-check-input { background-color: var(--border2); }
    .nav-tabs .nav-link { color: var(--muted); border: none; border-bottom: 2px solid transparent; font-size: 13px; font-weight: 500; padding: 10px 16px; border-radius: 0; }
    .nav-tabs .nav-link.active { color: var(--blue); border-bottom-color: var(--blue); background: transparent; }
    .nav-tabs { border-bottom: 1px solid var(--border); }
    </style>
    @stack('styles')
</head>
<body>
<div id="sa-wrap">
<div id="sa-overlay"></div>

{{-- ═══ SIDEBAR ═══ --}}
<aside id="sa-side">
    <div class="sa-brand">
        <div class="sa-brand-icon"><i class="fa-solid fa-shield-halved" style="color:#fff;font-size:13px;"></i></div>
        <div class="sa-brand-text">
            <div class="sa-brand-name">EduTenant ERP</div>
            <div class="sa-brand-sub">Admin Center</div>
        </div>
        <button class="sa-toggle-btn" id="sa-toggle" title="Collapse"><i class="fa-solid fa-chevron-left" style="font-size:11px;"></i></button>
    </div>
    <nav class="sa-nav">
        <div class="sa-section">Overview</div>
        <a href="{{ route('dashboard') }}" class="sa-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-gauge sa-icon"></i><span class="sa-label">Dashboard</span>
        </a>
        <div class="sa-section" style="margin-top:8px;">Institutions</div>
        <a href="{{ route('super.tenants.index') }}" class="sa-link {{ request()->routeIs('super.tenants.index') ? 'active' : '' }}">
            <i class="fa-solid fa-building sa-icon"></i><span class="sa-label">All Institutions</span>
        </a>
        <a href="{{ route('super.tenants.create') }}" class="sa-link {{ request()->routeIs('super.tenants.create') ? 'active' : '' }}">
            <i class="fa-solid fa-plus sa-icon"></i><span class="sa-label">Add Institution</span>
        </a>
        <div class="sa-section" style="margin-top:8px;">Account</div>
        <a href="{{ route('admin.profile.index') }}" class="sa-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
            <i class="fa-solid fa-user sa-icon"></i><span class="sa-label">My Profile</span>
        </a>
        <a href="{{ route('admin.settings.index') }}" class="sa-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <i class="fa-solid fa-gear sa-icon"></i><span class="sa-label">Settings</span>
        </a>
    </nav>
    <div class="sa-foot">
        <div class="d-flex align-items-center gap-2">
            <img src="{{ auth()->user()->avatar_url }}" class="rounded-circle flex-shrink-0" style="width:32px;height:32px;object-fit:cover;border:2px solid rgba(255,255,255,.15);">
            <div class="sa-label overflow-hidden">
                <div style="font-size:12px;font-weight:600;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->name }}</div>
                <div style="font-size:10px;color:rgba(255,255,255,.4);">Super Admin</div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="ms-auto flex-shrink-0">@csrf
                <button type="submit" class="btn-ghost" style="padding:4px 6px;color:rgba(255,255,255,.4);" title="Sign out"><i class="fa-solid fa-right-from-bracket" style="font-size:12px;"></i></button>
            </form>
        </div>
    </div>
</aside>

{{-- ═══ MAIN ═══ --}}
<div id="sa-main">
    {{-- Topnav --}}
    <nav id="sa-top">
        <button class="btn-ghost" id="sa-top-toggle" style="padding:6px 8px;"><i class="fa-solid fa-bars" style="font-size:14px;color:var(--muted);"></i></button>
        <nav aria-label="breadcrumb" class="d-none d-md-block flex-1">
            <ol class="breadcrumb mb-0" style="font-size:12px;">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:var(--blue);"><i class="fa-solid fa-house me-1"></i>Admin Center</a></li>
                @yield('breadcrumb')
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-2 ms-auto">
            <button id="sa-theme-btn" class="btn-icon" title="Toggle theme"><i class="fa-solid fa-moon" id="sa-theme-icon" style="font-size:13px;"></i></button>
            <div class="dropdown">
                <button class="d-flex align-items-center gap-2 btn-ghost" style="padding:4px 8px;" data-bs-toggle="dropdown">
                    <img src="{{ auth()->user()->avatar_url }}" class="rounded-circle" style="width:28px;height:28px;object-fit:cover;border:2px solid var(--border);">
                    <div class="d-none d-md-block text-start">
                        <div style="font-size:12px;font-weight:600;color:var(--text);line-height:1.2;">{{ auth()->user()->name }}</div>
                        <div style="font-size:10px;color:var(--muted);">Super Admin</div>
                    </div>
                    <i class="fa-solid fa-chevron-down d-none d-md-block" style="font-size:10px;color:var(--muted);"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end mt-1">
                    <li><a class="dropdown-item" href="{{ route('admin.profile.index') }}"><i class="fa-solid fa-user" style="width:14px;color:var(--blue);"></i>My Profile</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.settings.index') }}"><i class="fa-solid fa-gear" style="width:14px;color:var(--muted);"></i>Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="dropdown-item danger"><i class="fa-solid fa-right-from-bracket" style="width:14px;"></i>Sign Out</button></form></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="sa-content fade-in-up">@yield('content')</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script>
(function(){
    const side=document.getElementById('sa-side'),main=document.getElementById('sa-main'),ov=document.getElementById('sa-overlay');
    function tog(){if(window.innerWidth<=768){side.classList.toggle('open');ov.classList.toggle('on');}else{side.classList.toggle('collapsed');main.classList.toggle('expanded');localStorage.setItem('sa_collapsed',side.classList.contains('collapsed'));}}
    document.getElementById('sa-toggle')?.addEventListener('click',tog);
    document.getElementById('sa-top-toggle')?.addEventListener('click',tog);
    ov?.addEventListener('click',()=>{side.classList.remove('open');ov.classList.remove('on');});
    if(window.innerWidth>768&&localStorage.getItem('sa_collapsed')==='true'){side.classList.add('collapsed');main.classList.add('expanded');}
    const themeBtn=document.getElementById('sa-theme-btn'),themeIcon=document.getElementById('sa-theme-icon'),html=document.documentElement;
    const saved=localStorage.getItem('sa_theme')||'light';html.setAttribute('data-theme',saved);
    if(saved==='dark')themeIcon?.classList.replace('fa-moon','fa-sun');
    themeBtn?.addEventListener('click',()=>{const c=html.getAttribute('data-theme'),n=c==='dark'?'light':'dark';html.setAttribute('data-theme',n);localStorage.setItem('sa_theme',n);themeIcon?.classList.toggle('fa-moon',n==='light');themeIcon?.classList.toggle('fa-sun',n==='dark');});
    document.querySelectorAll('[data-confirm-delete]').forEach(btn=>{btn.addEventListener('click',function(){const fid=this.dataset.confirmDelete,name=this.dataset.name||'this record';Swal.fire({title:'Delete '+name+'?',text:'This cannot be undone.',icon:'warning',showCancelButton:true,confirmButtonColor:'#DC2626',cancelButtonColor:'#64748B',confirmButtonText:'Delete',cancelButtonText:'Cancel',customClass:{popup:'rounded-3'}}).then(r=>{if(r.isConfirmed)document.getElementById(fid).submit();});});});
    @if(session('success'))Swal.fire({icon:'success',title:'Success',text:@json(session('success')),timer:3000,showConfirmButton:false,toast:true,position:'top-end'});@endif
    @if(session('error'))Swal.fire({icon:'error',title:'Error',text:@json(session('error')),timer:4000,showConfirmButton:false,toast:true,position:'top-end'});@endif
    @if(session('info'))Swal.fire({icon:'info',title:'Info',text:@json(session('info')),timer:3500,showConfirmButton:false,toast:true,position:'top-end'});@endif
})();
</script>
@stack('scripts')
</body>
</html>
