@extends('layouts.organizer')

@section('topbar-action')
    <div style="display:flex;gap:8px;">
        <a href="{{ route('organizer.tournaments.edit', $tournament) }}" class="btn-primary">Edit</a>
        <a href="{{ route('organizer.tournaments.index') }}" class="btn-secondary">← Back</a>
    </div>
@endsection

@section('content')
<div class="page-header">
    <div style="display:flex;align-items:center;gap:12px;margin-top:4px;">
        <div class="page-title">{{ $tournament->tournament_name }}</div>
        @if($tournament->status==='ongoing') <span class="badge badge-green" style="font-size:11px;padding:4px 10px;">ONGOING</span>
        @elseif($tournament->status==='upcoming') <span class="badge badge-blue" style="font-size:11px;padding:4px 10px;">UPCOMING</span>
        @else <span class="badge badge-gray" style="font-size:11px;padding:4px 10px;">COMPLETED</span> @endif
    </div>
</div>

<div class="card" style="padding:14px 20px;margin-bottom:16px;">
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;">
        <div>
            <div style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#94a3b8;margin-bottom:3px;">Sport</div>
            <div style="font-size:13px;font-weight:600;">{{ $tournament->sport->sport_name ?? '—' }}</div>
        </div>
        <div>
            <div style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#94a3b8;margin-bottom:3px;">Start</div>
            <div style="font-size:13px;font-weight:600;">{{ $tournament->start_date->format('M d, Y') }}</div>
        </div>
        <div>
            <div style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#94a3b8;margin-bottom:3px;">End</div>
            <div style="font-size:13px;font-weight:600;">{{ $tournament->end_date->format('M d, Y') }}</div>
        </div>
        <div>
            <div style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#94a3b8;margin-bottom:3px;">Max Teams</div>
            <div style="font-size:13px;font-weight:600;">{{ $tournament->max_teams ?? 'Unlimited' }}</div>
        </div>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
            <div style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:700;">Registrations <span style="font-size:11px;font-weight:400;color:#64748b;">{{ $tournament->registrations->count() }}</span></div>
            <a href="{{ route('organizer.registrations.index') }}" style="font-size:11px;color:#3b82f6;font-weight:600;text-decoration:none;">Manage →</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Team</th><th>Date</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($tournament->registrations as $reg)
                    <tr>
                        <td style="font-weight:600;font-size:12px;">{{ $reg->team->team_name ?? '—' }}</td>
                        <td style="font-size:11px;color:#64748b;">{{ $reg->registration_date?->format('M d, Y') }}</td>
                        <td>
                            @if($reg->status==='approved') <span class="badge badge-green">APPROVED</span>
                            @elseif($reg->status==='pending') <span class="badge badge-amber">PENDING</span>
                            @else <span class="badge badge-red">REJECTED</span> @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align:center;color:#94a3b8;font-size:12px;padding:16px;">No registrations yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
            <div style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:700;">Matches <span style="font-size:11px;font-weight:400;color:#64748b;">{{ $tournament->matches->count() }}</span></div>
            <a href="{{ route('organizer.matches.create') }}" style="font-size:11px;color:#3b82f6;font-weight:600;text-decoration:none;">+ Schedule →</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Teams</th><th>Date</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($tournament->matches->take(6) as $match)
                    <tr>
                        <td style="font-size:12px;font-weight:600;">{{ $match->matchTeams->map(fn($mt)=>$mt->team->team_name??'?')->join(' vs ') }}</td>
                        <td style="font-size:11px;color:#64748b;">{{ $match->match_date->format('M d') }}</td>
                        <td>
                            @if($match->status==='live') <span class="badge badge-red">LIVE</span>
                            @elseif($match->status==='completed') <span class="badge badge-green">DONE</span>
                            @else <span class="badge badge-blue">SCHED</span> @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align:center;color:#94a3b8;font-size:12px;padding:16px;">No matches yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
