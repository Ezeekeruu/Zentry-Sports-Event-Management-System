@extends('layouts.coach')

@section('content')
<div class="page-header">
    <div class="page-title">Dashboard</div>
    <div class="page-subtitle">Welcome back, Coach {{ auth()->user()->first_name }}.</div>
</div>

@if(!$team)
<div class="card" style="text-align:center;padding:40px;">
    <div style="font-family:'Manrope',sans-serif;font-size:18px;font-weight:700;margin-bottom:8px;">No Team Assigned</div>
    <div style="font-size:13px;color:#64748b;">You have not been assigned as coach of any team yet. Contact an administrator.</div>
</div>
@else

<div class="grid-4" style="margin-bottom:20px;">
    <div class="stat-card">
        <div class="stat-label">Team</div>
        <div class="stat-value" style="font-size:20px;margin-top:4px;">{{ $team->team_name }}</div>
        <div class="stat-sub">{{ $team->sport->sport_name ?? '' }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Players</div>
        <div class="stat-value">{{ $team->playerProfiles->count() }}</div>
        <div class="stat-sub">On roster</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Upcoming Matches</div>
        <div class="stat-value">{{ $upcomingMatches->count() }}</div>
        <div class="stat-sub">Scheduled</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Founded</div>
        <div class="stat-value" style="font-size:20px;margin-top:4px;">{{ $team->founded_at?->format('Y') ?? '—' }}</div>
        <div class="stat-sub">Year established</div>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
            <div style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:700;">Upcoming Matches</div>
            <a href="{{ route('coach.matches.index') }}" style="font-size:11px;color:#eab308;font-weight:600;text-decoration:none;">View all →</a>
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
            <div style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:700;">Roster</div>
            <a href="{{ route('coach.team.players') }}" style="font-size:11px;color:#eab308;font-weight:600;text-decoration:none;">Manage →</a>
        </div>
        @if($team->playerProfiles->isEmpty())
            <div style="text-align:center;color:#94a3b8;padding:20px;font-size:13px;">No players yet. <a href="{{ route('coach.team.players') }}" style="color:#eab308;text-decoration:none;">Add players →</a></div>
        @else
        <div class="table-wrap">
            <table>
                <thead><tr><th>Player</th><th>Email</th></tr></thead>
                <tbody>
                    @foreach($team->playerProfiles->take(6) as $profile)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div class="avatar">{{ strtoupper(substr($profile->user->first_name??'?',0,1)) }}</div>
                                <div style="font-size:12px;font-weight:600;">{{ $profile->user->first_name??'—' }} {{ $profile->user->last_name??'' }}</div>
                            </div>
                        </td>
                        <td style="font-size:11px;color:#64748b;">{{ $profile->user->email??'—' }}</td>
                    </tr>
                    @endforeach
                    @if($team->playerProfiles->count() > 6)
                    <tr><td colspan="2" style="text-align:center;font-size:11px;color:#94a3b8;padding:8px;">+{{ $team->playerProfiles->count()-6 }} more</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endif
@endsection
