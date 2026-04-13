@extends('layouts.public')

@section('content')
<div style="margin-bottom:6px;">
    <a href="{{ route('public.tournaments.index') }}" style="font-size:12px;color:#64748b;text-decoration:none;">← All Tournaments</a>
</div>

<div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;flex-wrap:wrap;">
    <div class="page-title">{{ $tournament->tournament_name }}</div>
    @if($tournament->status==='ongoing') <span class="badge badge-green" style="font-size:11px;padding:5px 12px;">ONGOING</span>
    @elseif($tournament->status==='upcoming') <span class="badge badge-blue" style="font-size:11px;padding:5px 12px;">UPCOMING</span>
    @else <span class="badge badge-gray" style="font-size:11px;padding:5px 12px;">COMPLETED</span> @endif
</div>

{{-- Info strip --}}
<div class="card" style="margin-bottom:20px;">
    <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:16px;">
        <div>
            <div style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#94a3b8;margin-bottom:4px;">Sport</div>
            <div style="font-size:13px;font-weight:600;">{{ $tournament->sport->sport_name ?? '—' }}</div>
        </div>
        <div>
            <div style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#94a3b8;margin-bottom:4px;">Organizer</div>
            <div style="font-size:13px;font-weight:600;">{{ $tournament->organizer->first_name ?? '—' }} {{ $tournament->organizer->last_name ?? '' }}</div>
        </div>
        <div>
            <div style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#94a3b8;margin-bottom:4px;">Start Date</div>
            <div style="font-size:13px;font-weight:600;">{{ $tournament->start_date->format('M d, Y') }}</div>
        </div>
        <div>
            <div style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#94a3b8;margin-bottom:4px;">End Date</div>
            <div style="font-size:13px;font-weight:600;">{{ $tournament->end_date->format('M d, Y') }}</div>
        </div>
        <div>
            <div style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#94a3b8;margin-bottom:4px;">Teams</div>
            <div style="font-size:13px;font-weight:600;">
                {{ $tournament->registrations->where('status','approved')->count() }}
                {{ $tournament->max_teams ? ' / '.$tournament->max_teams : '' }}
            </div>
        </div>
    </div>
</div>

<div class="grid-2">
    {{-- Participating teams --}}
    <div class="card">
        <div style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:700;margin-bottom:14px;">
            Participating Teams
            <span style="font-size:11px;font-weight:400;color:#64748b;margin-left:6px;">{{ $tournament->registrations->where('status','approved')->count() }} approved</span>
        </div>
        @if($tournament->registrations->where('status','approved')->isEmpty())
            <div style="text-align:center;color:#94a3b8;padding:20px;font-size:13px;">No approved teams yet.</div>
        @else
        <div class="table-wrap">
            <table>
                <thead><tr><th>Team</th><th>Registered</th></tr></thead>
                <tbody>
                    @foreach($tournament->registrations->where('status','approved') as $reg)
                    <tr>
                        <td style="font-weight:600;font-size:13px;">{{ $reg->team->team_name ?? '—' }}</td>
                        <td style="font-size:11px;color:#64748b;">{{ $reg->registration_date?->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- Match schedule --}}
    <div class="card">
        <div style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:700;margin-bottom:14px;">
            Matches
            <span style="font-size:11px;font-weight:400;color:#64748b;margin-left:6px;">{{ $tournament->matches->count() }} scheduled</span>
        </div>
        @if($tournament->matches->isEmpty())
            <div style="text-align:center;color:#94a3b8;padding:20px;font-size:13px;">No matches scheduled yet.</div>
        @else
        <div class="table-wrap">
            <table>
                <thead><tr><th>Match</th><th>Date</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach($tournament->matches as $match)
                    <tr>
                        <td>
                            <div style="font-size:12px;font-weight:600;">
                                {{ $match->matchTeams->map(fn($mt)=>$mt->team->team_name??'?')->join(' vs ') }}
                            </div>
                            @if($match->round_name)<div style="font-size:10px;color:#94a3b8;">{{ $match->round_name }}</div>@endif
                            @if($match->venue)<div style="font-size:10px;color:#94a3b8;">📍 {{ $match->venue }}</div>@endif
                        </td>
                        <td>
                            <div style="font-size:12px;font-weight:500;">{{ $match->match_date->format('M d') }}</div>
                            @if($match->match_time)<div style="font-size:10px;color:#94a3b8;">{{ \Carbon\Carbon::parse($match->match_time)->format('h:i A') }}</div>@endif
                        </td>
                        <td>
                            @if($match->status==='live') <span class="badge badge-red">LIVE</span>
                            @elseif($match->status==='completed') <span class="badge badge-green">DONE</span>
                            @else <span class="badge badge-blue">SCHED</span> @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

<div style="margin-top:12px;text-align:center;">
    <a href="{{ route('public.standings.index') }}?tournament_id={{ $tournament->id }}"
       class="btn-primary">View Standings for this Tournament</a>
</div>
@endsection
