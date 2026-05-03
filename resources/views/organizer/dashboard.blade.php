@extends('layouts.organizer')

@section('content')
<div class="page-header">
    <div class="page-title">Dashboard</div>
    <div class="page-subtitle">Welcome back, {{ auth()->user()->first_name }}.</div>
</div>

<div class="grid-3" style="margin-bottom:20px;">
    <a href="{{ route('organizer.tournaments.index') }}" style="text-decoration:none;">
        <div class="stat-card" style="cursor:pointer;transition:box-shadow .15s,transform .15s;" onmouseover="this.style.boxShadow='0 4px 24px rgba(15,23,42,0.10)';this.style.transform='translateY(-2px)'" onmouseout="this.style.boxShadow='';this.style.transform=''">
            <div class="stat-label">My Tournaments</div>
            <div class="stat-value">{{ $myTournaments }}</div>
            <div class="stat-sub">Total created</div>
        </div>
    </a>
    <a href="{{ route('organizer.tournaments.index', ['status' => 'ongoing']) }}" style="text-decoration:none;">
        <div class="stat-card" style="cursor:pointer;transition:box-shadow .15s,transform .15s;" onmouseover="this.style.boxShadow='0 4px 24px rgba(15,23,42,0.10)';this.style.transform='translateY(-2px)'" onmouseout="this.style.boxShadow='';this.style.transform=''">
            <div class="stat-label">Ongoing</div>
            <div class="stat-value">{{ $ongoingTournaments }}</div>
            <div class="stat-sub">Active right now</div>
        </div>
    </a>
    <a href="{{ route('organizer.registrations.index') }}" style="text-decoration:none;">
        <div class="stat-card" style="cursor:pointer;transition:box-shadow .15s,transform .15s;" onmouseover="this.style.boxShadow='0 4px 24px rgba(15,23,42,0.10)';this.style.transform='translateY(-2px)'" onmouseout="this.style.boxShadow='';this.style.transform=''">
            <div class="stat-label">Pending Registrations</div>
            <div class="stat-value">{{ $pendingRegistrations }}</div>
            <div class="stat-sub">
                @if($pendingRegistrations > 0) Review now → @else All caught up @endif
            </div>
        </div>
    </a>
</div>

<div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
        <div style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:700;">My Recent Tournaments</div>
        <a href="{{ route('organizer.tournaments.create') }}" class="btn-primary" style="padding:6px 12px;font-size:11px;">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
            New Tournament
        </a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Tournament</th>
                    <th>Sport</th>
                    <th>Dates</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentTournaments as $t)
                <tr onclick="window.location='{{ route('organizer.tournaments.show', $t) }}'"
                    style="cursor:pointer;transition:background .12s;"
                    onmouseover="this.style.background='#eff6ff'"
                    onmouseout="this.style.background=''">
                    <td style="font-weight:600;font-size:13px;">{{ $t->tournament_name }}</td>
                    <td><span class="badge badge-blue">{{ $t->sport->sport_name ?? '—' }}</span></td>
                    <td style="font-size:11px;color:#64748b;">
                        {{ $t->start_date->format('M d') }} – {{ $t->end_date->format('M d, Y') }}
                    </td>
                    <td>
                        @if($t->status === 'ongoing') <span class="badge badge-green">ONGOING</span>
                        @elseif($t->status === 'upcoming') <span class="badge badge-blue">UPCOMING</span>
                        @else <span class="badge badge-gray">COMPLETED</span> @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center;color:#94a3b8;padding:24px;font-size:13px;">
                    No tournaments yet. <a href="{{ route('organizer.tournaments.create') }}" style="color:#3b82f6;text-decoration:none;">Create one →</a>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection