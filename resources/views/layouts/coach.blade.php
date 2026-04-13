<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Zentry — {{ $title ?? 'Coach' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #f8faff; color: #0f172a; display: flex; min-height: 100vh; }
        .sidebar { width: 220px; min-width: 220px; background: #1a2233; display: flex; flex-direction: column; position: fixed; top: 0; left: 0; height: 100vh; z-index: 100; }
        .sb-brand { padding: 20px 16px; border-bottom: 1px solid rgba(255,255,255,0.07); }
        .sb-logo { display: flex; align-items: center; gap: 10px; }
        .sb-icon { width: 32px; height: 32px; background: #312e00; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .sb-icon svg { width: 16px; height: 16px; fill: #eab308; }
        .sb-name { font-family: 'Manrope', sans-serif; font-size: 13px; font-weight: 800; color: #fff; letter-spacing: .02em; }
        .sb-sub { font-size: 8px; color: rgba(255,255,255,0.3); letter-spacing: .12em; margin-top: 1px; }
        .sb-nav { flex: 1; padding: 12px 0; overflow-y: auto; }
        .sb-section { padding: 12px 16px 4px; font-size: 8px; font-weight: 700; letter-spacing: .12em; color: rgba(255,255,255,0.3); text-transform: uppercase; }
        .sb-item { display: flex; align-items: center; gap: 10px; padding: 9px 16px; font-size: 12px; font-weight: 500; color: rgba(255,255,255,0.5); text-decoration: none; border-left: 2px solid transparent; transition: all .12s; }
        .sb-item:hover { color: rgba(255,255,255,0.85); background: rgba(255,255,255,0.05); }
        .sb-item.active { color: #fff; background: rgba(234,179,8,0.12); border-left-color: #eab308; }
        .sb-item svg { width: 14px; height: 14px; flex-shrink: 0; opacity: .6; }
        .sb-item.active svg { opacity: 1; }
        .sb-footer { padding: 12px 16px; border-top: 1px solid rgba(255,255,255,0.07); }
        .sb-avatar { width: 28px; height: 28px; border-radius: 50%; background: #312e00; font-size: 10px; font-weight: 700; color: #eab308; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .sb-uname { font-size: 11px; font-weight: 600; color: #fff; }
        .sb-urole { font-size: 8px; color: rgba(255,255,255,0.35); }
        .main-wrap { margin-left: 220px; flex: 1; display: flex; flex-direction: column; min-height: 100vh; }
        .topbar { background: #fff; border-bottom: 1px solid rgba(15,23,42,0.08); padding: 12px 24px; display: flex; align-items: center; justify-content: space-between; }
        .topbar-right { display: flex; align-items: center; gap: 12px; }
        .btn-primary { background: #eab308; color: #fff; border: none; border-radius: 7px; padding: 8px 16px; font-size: 12px; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: background .12s; }
        .btn-primary:hover { background: #ca8a04; color: #fff; }
        .btn-secondary { background: #fff; color: #0f172a; border: 1px solid rgba(15,23,42,0.12); border-radius: 7px; padding: 8px 16px; font-size: 12px; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: all .12s; }
        .btn-secondary:hover { background: #f8faff; }
        .btn-danger { background: #fee2e2; color: #991b1b; border: none; border-radius: 7px; padding: 6px 12px; font-size: 11px; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; text-decoration: none; transition: all .12s; }
        .btn-danger:hover { background: #fecaca; }
        .page-content { padding: 24px; flex: 1; }
        .page-header { margin-bottom: 20px; }
        .breadcrumb { font-size: 10px; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; color: #94a3b8; margin-bottom: 6px; background: transparent; padding: 0; }
        .breadcrumb span { color: #eab308; }
        .page-title { font-family: 'Manrope', sans-serif; font-size: 24px; font-weight: 800; color: #0f172a; }
        .page-subtitle { font-size: 13px; color: #64748b; margin-top: 4px; }
        .card { background: #fff; border-radius: 10px; border: 0.5px solid rgba(15,23,42,0.08); padding: 16px 20px; margin-bottom: 16px; }
        .stat-card { background: #fff; border-radius: 10px; border: 0.5px solid rgba(15,23,42,0.08); padding: 20px; }
        .stat-label { font-size: 10px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: #94a3b8; margin-bottom: 8px; }
        .stat-value { font-family: 'Manrope', sans-serif; font-size: 32px; font-weight: 800; color: #0f172a; line-height: 1; }
        .stat-sub { font-size: 11px; color: #eab308; font-weight: 600; margin-top: 6px; }
        .grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 20px; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead th { font-size: 9px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: #94a3b8; padding: 10px 12px; text-align: left; border-bottom: 1px solid rgba(15,23,42,0.06); }
        tbody td { padding: 14px 12px; font-size: 13px; color: #0f172a; border-bottom: 0.5px solid rgba(15,23,42,0.05); vertical-align: middle; }
        tbody tr:hover { background: #f8faff; }
        tbody tr:last-child td { border-bottom: none; }
        .badge { display: inline-block; font-size: 9px; font-weight: 700; letter-spacing: .06em; padding: 3px 8px; border-radius: 4px; text-transform: uppercase; }
        .badge-green { background: #dcfce7; color: #14532d; }
        .badge-blue { background: #dbeafe; color: #1e3a8a; }
        .badge-amber { background: #fef3c7; color: #78350f; }
        .badge-red { background: #fee2e2; color: #991b1b; }
        .badge-gray { background: #f1f5f9; color: #475569; }
        .alert-success { background: #fef9c3; border-left: 3px solid #eab308; color: #78350f; padding: 10px 14px; border-radius: 7px; font-size: 12px; font-weight: 500; margin-bottom: 16px; }
        .alert-error { background: #fee2e2; border-left: 3px solid #ef4444; color: #991b1b; padding: 10px 14px; border-radius: 7px; font-size: 12px; font-weight: 500; margin-bottom: 16px; }
        .form-group { margin-bottom: 16px; }
        .form-label { font-size: 11px; font-weight: 600; color: #374151; display: block; margin-bottom: 5px; letter-spacing: .02em; }
        .form-control { width: 100%; padding: 9px 12px; border: 1px solid rgba(15,23,42,0.12); border-radius: 7px; font-size: 13px; font-family: 'Inter', sans-serif; color: #0f172a; background: #fff; outline: none; transition: border .12s; }
        .form-control:focus { border-color: #eab308; box-shadow: none; }
        .form-error { font-size: 11px; color: #ef4444; margin-top: 4px; }
        .avatar { width: 34px; height: 34px; border-radius: 8px; background: #312e00; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; color: #eab308; flex-shrink: 0; }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="sb-brand">
        <div class="sb-logo">
            <div class="sb-icon"><svg viewBox="0 0 24 24"><path d="M12 2l2.4 7.4H22l-6.2 4.5 2.4 7.4L12 17l-6.2 4.3 2.4-7.4L2 9.4h7.6z"/></svg></div>
            <div><div class="sb-name">ZENTRY</div><div class="sb-sub">COACH PORTAL</div></div>
        </div>
    </div>
    <div class="sb-nav">
        <div class="sb-section">My Team</div>
        <a href="{{ route('coach.dashboard') }}" class="sb-item {{ request()->routeIs('coach.dashboard') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            Dashboard
        </a>
        <a href="{{ route('coach.team.show') }}" class="sb-item {{ request()->routeIs('coach.team.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            My Team
        </a>
        <a href="{{ route('coach.team.players') }}" class="sb-item {{ request()->routeIs('coach.team.players') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
            Players
        </a>
        <a href="{{ route('coach.matches.index') }}" class="sb-item {{ request()->routeIs('coach.matches.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
            Matches
        </a>
        <a href="{{ route('coach.results.index') }}" class="sb-item {{ request()->routeIs('coach.results.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 19v-6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v6a2 2 0 0 1 2 2h14a2 2 0 0 1 2-2v-6a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v6"/></svg>
            Results
        </a>
    </div>
    <div class="sb-footer">
        <div style="display:flex;align-items:center;gap:8px;">
            <div class="sb-avatar">{{ strtoupper(substr(auth()->user()->first_name, 0, 1)) }}</div>
            <div><div class="sb-uname">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div><div class="sb-urole">Coach</div></div>
        </div>
        <form method="POST" action="{{ route('logout') }}" style="margin-top:10px;">
            @csrf
            <button type="submit" style="width:100%;background:rgba(255,255,255,0.05);border:none;color:rgba(255,255,255,0.4);font-size:11px;padding:7px;border-radius:6px;cursor:pointer;font-family:'Inter',sans-serif;">Sign out</button>
        </form>
    </div>
</div>
<div class="main-wrap">
    <div class="topbar">
        <div style="font-size:13px;color:#94a3b8;font-weight:500;">{{ now()->format('l, F d, Y') }}</div>
        <div class="topbar-right">@yield('topbar-action')</div>
    </div>
    <div class="page-content">
        @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="alert-error">{{ session('error') }}</div>@endif
        @yield('content')
    </div>
</div>
</body>
</html>
