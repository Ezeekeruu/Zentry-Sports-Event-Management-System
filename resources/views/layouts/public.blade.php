<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zentry — {{ $title ?? 'Tournament Hub' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #f8faff; color: #0f172a; min-height: 100vh; }

        .topnav { background: #1a2233; padding: 0 32px; height: 56px; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 100; }
        .nav-brand { display: flex; align-items: center; gap: 10px; text-decoration: none; }
        .nav-icon { width: 30px; height: 30px; background: rgba(34,197,94,0.15); border-radius: 7px; display: flex; align-items: center; justify-content: center; }
        .nav-icon svg { width: 14px; height: 14px; fill: #22c55e; }
        .nav-name { font-family: 'Manrope', sans-serif; font-size: 14px; font-weight: 800; color: #fff; letter-spacing: .04em; }

        .nav-links { display: flex; align-items: center; gap: 4px; }
        .nav-link { padding: 6px 12px; font-size: 12px; font-weight: 500; color: rgba(255,255,255,0.55); text-decoration: none; border-radius: 6px; transition: all .12s; }
        .nav-link:hover { color: #fff; background: rgba(255,255,255,0.07); }
        .nav-link.active { color: #fff; background: rgba(34,197,94,0.15); }

        .nav-actions { display: flex; align-items: center; gap: 8px; }
        .btn-nav-login { padding: 6px 14px; font-size: 12px; font-weight: 600; color: rgba(255,255,255,0.7); text-decoration: none; border-radius: 6px; border: 1px solid rgba(255,255,255,0.15); transition: all .12s; }
        .btn-nav-login:hover { color: #fff; border-color: rgba(255,255,255,0.3); }
        .btn-nav-cta { padding: 6px 14px; font-size: 12px; font-weight: 600; color: #fff; text-decoration: none; border-radius: 6px; background: #22c55e; transition: background .12s; }
        .btn-nav-cta:hover { background: #16a34a; color: #fff; }

        .page-wrap { max-width: 1100px; margin: 0 auto; padding: 28px 24px; }

        .page-title { font-family: 'Manrope', sans-serif; font-size: 26px; font-weight: 800; color: #0f172a; }
        .page-subtitle { font-size: 13px; color: #64748b; margin-top: 4px; }

        .card { background: #fff; border-radius: 10px; border: 0.5px solid rgba(15,23,42,0.08); padding: 16px 20px; margin-bottom: 16px; }
        .stat-card { background: #fff; border-radius: 10px; border: 0.5px solid rgba(15,23,42,0.08); padding: 20px; }

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

        .btn-primary { background: #22c55e; color: #fff; border: none; border-radius: 7px; padding: 8px 16px; font-size: 12px; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: background .12s; }
        .btn-primary:hover { background: #16a34a; color: #fff; }
        .btn-secondary { background: #fff; color: #0f172a; border: 1px solid rgba(15,23,42,0.12); border-radius: 7px; padding: 8px 16px; font-size: 12px; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: all .12s; }
        .btn-secondary:hover { background: #f8faff; }

        .grid-3 { display: grid; grid-template-columns: repeat(3,1fr); gap: 12px; margin-bottom: 20px; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

        .form-control { width: 100%; padding: 9px 12px; border: 1px solid rgba(15,23,42,0.12); border-radius: 7px; font-size: 13px; font-family: 'Inter', sans-serif; color: #0f172a; background: #fff; outline: none; transition: border .12s; }
        .form-control:focus { border-color: #22c55e; }

        footer { background: #1a2233; padding: 20px 32px; text-align: center; font-size: 11px; color: rgba(255,255,255,0.3); margin-top: 48px; }
    </style>
</head>
<body>

<nav class="topnav">
    <a href="{{ route('public.tournaments.index') }}" class="nav-brand">
        <div class="nav-icon">
            <svg viewBox="0 0 24 24"><path d="M12 2l2.4 7.4H22l-6.2 4.5 2.4 7.4L12 17l-6.2 4.3 2.4-7.4L2 9.4h7.6z"/></svg>
        </div>
        <span class="nav-name">ZENTRY</span>
    </a>

    <div class="nav-links">
        <a href="{{ route('public.tournaments.index') }}"
           class="nav-link {{ request()->routeIs('public.tournaments.*') ? 'active' : '' }}">Tournaments</a>
        <a href="{{ route('public.schedule.index') }}"
           class="nav-link {{ request()->routeIs('public.schedule.*') ? 'active' : '' }}">Schedule</a>
        <a href="{{ route('public.standings.index') }}"
           class="nav-link {{ request()->routeIs('public.standings.*') ? 'active' : '' }}">Standings</a>
    </div>

    <div class="nav-actions">
        @auth
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="btn-nav-cta">Admin Panel</a>
            @elseif(auth()->user()->role === 'organizer')
                <a href="{{ route('organizer.tournaments.index') }}" class="btn-nav-cta">My Tournaments</a>
            @elseif(auth()->user()->role === 'coach')
                <a href="{{ route('coach.dashboard') }}" class="btn-nav-cta">Coach Panel</a>
            @elseif(auth()->user()->role === 'player')
                <a href="{{ route('player.dashboard') }}" class="btn-nav-cta">Player Panel</a>
            @else
                <span style="font-size:12px;color:rgba(255,255,255,0.5);">{{ auth()->user()->first_name }}</span>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-nav-login" style="cursor:pointer;border:none;background:transparent;padding:6px 14px;font-size:12px;font-weight:600;color:rgba(255,255,255,0.7);">Sign out</button>
                </form>
            @endif
        @else
            <a href="{{ route('login') }}" class="btn-nav-login">Sign in</a>
            <a href="{{ route('register') }}" class="btn-nav-cta">Register</a>
        @endauth
    </div>
</nav>

<div class="page-wrap">
    @yield('content')
</div>

<footer>
    © {{ date('Y') }} Zentry Tournament Engine. All rights reserved.
</footer>

</body>
</html>
