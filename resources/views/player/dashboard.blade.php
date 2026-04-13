@extends('layouts.player')

@section('content')
<div class="page-header">
    <div class="page-title">Dashboard</div>
    <div class="page-subtitle">Welcome back, {{ $user->first_name }}.</div>
</div>

<div class="grid-3" style="margin-bottom:20px;">
    <div class="stat-card">
        <div class="stat-label">My Team</div>
        <div class="stat-value" style="font-size:18px;margin-top:4px;">{{ $team?->team_name ?? 'None' }}</div>
        <div class="stat-sub">{{ $team?->sport->sport_name ?? 'Not assigned' }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Upcoming Matches</div>
        <div class="stat-value">{{ $upcomingMatches->count() }}</div>
        <div class="stat-sub">Scheduled ahead</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Teammates</div>
        <div class="stat-value">{{ $team ? $team->playerProfiles->count() - 1 : 0 }}</div>
        <div class="stat-sub">On the roster</div>
    </div>
</div>

@if(!$team)
<div class="card" style="text-align:center;padding:40px;">
    <div style="font-family:'Manrope',sans-serif;font-size:18px;font-weight:700;margin-bottom:8px;">No Team Yet</div>
    <div style="font-size:13px;color:#64748b;">You haven't been added to a team. A coach or admin will assign you.</div>
</div>
@else

<div class="grid-2">
    <div class="card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
            <div style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:700;">Upcoming Matches</div>
            <a href="{{ route('player.matches.index') }}" style="font-size:11px;color:#a855f7;font-weight:600;text-decoration:none;">View all →</a>
        </div>
        @if($upcomingMatches->isEmpty())
            <div style="text-align:center;color:#94a3b8;padding:20px;font-size:13px;">No upcoming matches.</div>
        @else
        <div class="table-wrap">
            <table>
                <thead><tr><th>Opponent(s)</th><th>Tournament</th><th>Date</th></tr></thead>
                <tbody>
                    @foreach($upcomingMatches as $match)
                    <tr>
                        <td style="font-weight:600;font-size:12px;">
                            {{ $match->matchTeams->filter(fn($mt)=>$mt->team_id!==$team->id)->map(fn($mt)=>$mt->team->team_name??'?')->join(', ') ?: 'TBD' }}
                        </td>
                        <td style="font-size:11px;color:#64748b;">{{ Str::limit($match->tournament->tournament_name??'—',20) }}</td>
                        <td style="font-size:11px;color:#64748b;">{{ $match->match_date->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <div class="card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
            <div style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:700;">My Team — {{ $team->team_name }}</div>
            <a href="{{ route('player.team.show') }}" style="font-size:11px;color:#a855f7;font-weight:600;text-decoration:none;">View →</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Teammate</th><th></th></tr></thead>
                <tbody>
                    @foreach($team->playerProfiles->take(6) as $p)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div class="avatar">{{ strtoupper(substr($p->user->first_name??'?',0,1)) }}</div>
                                <div style="font-size:12px;font-weight:600;">{{ $p->user->first_name??'—' }} {{ $p->user->last_name??'' }}</div>
                            </div>
                        </td>
                        <td>
                            @if($p->user_id === $user->id)
                                <span class="badge badge-purple">YOU</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @if($team->playerProfiles->count() > 6)
                    <tr><td colspan="2" style="text-align:center;font-size:11px;color:#94a3b8;">+{{ $team->playerProfiles->count()-6 }} more</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection
