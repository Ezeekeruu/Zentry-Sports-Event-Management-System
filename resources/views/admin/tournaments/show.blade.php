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
        @if($tournament->status==='ongoing') <span class="badge badge-green">ONGOING</span>
        @elseif($tournament->status==='upcoming') <span class="badge badge-blue">UPCOMING</span>
        @else <span class="badge badge-gray">COMPLETED</span> @endif
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
                        <td style="font-size:11px;color:#64748b;">{{ $match->match_date->format('M d, Y') }}</td>
                        <td>
                            @if($match->status==='completed') <span class="badge badge-green">DONE</span>
                            @elseif($match->status==='live') <span class="badge badge-red">LIVE</span>
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

{{-- STANDINGS SECTION --}}
@if($tournament->status === 'completed' && $standings->isNotEmpty())

    {{-- Podium --}}
    @php $p1=$standings->get(0); $p2=$standings->get(1); $p3=$standings->get(2); @endphp
    <div class="card" style="margin-top:16px;padding:32px 24px 0;overflow:hidden;">
        <div style="text-align:center;margin-bottom:28px;">
            <div style="font-family:'Manrope',sans-serif;font-size:11px;font-weight:700;letter-spacing:.12em;color:#94a3b8;text-transform:uppercase;margin-bottom:4px;">Tournament Champions</div>
            <div style="font-family:'Manrope',sans-serif;font-size:22px;font-weight:900;color:#0f172a;">🏆 Final Podium</div>
        </div>
        <div style="display:flex;align-items:flex-end;justify-content:center;max-width:600px;margin:0 auto;">
            <div style="flex:1;display:flex;flex-direction:column;align-items:center;">
                @if($p2) <div style="font-size:30px;margin-bottom:6px;">🥈</div>
                <div style="font-size:13px;font-weight:800;color:#0f172a;text-align:center;margin-bottom:3px;padding:0 6px;">{{ $p2['team']->team_name }}</div>
                <div style="font-size:11px;color:#64748b;margin-bottom:10px;">{{ $p2['total_points'] }}pts · {{ $p2['wins'] }}W</div>
                @else <div style="height:80px;"></div> @endif
                <div style="width:100%;background:linear-gradient(180deg,#e2e8f0,#94a3b8);border-radius:10px 10px 0 0;height:100px;display:flex;align-items:center;justify-content:center;">
                    <span style="font-size:32px;font-weight:900;color:rgba(255,255,255,0.9);">2</span>
                </div>
            </div>
            <div style="flex:1;display:flex;flex-direction:column;align-items:center;position:relative;">
                @if($p1) <div style="font-size:38px;margin-bottom:6px;">🥇</div>
                <div style="font-size:15px;font-weight:900;color:#0f172a;text-align:center;margin-bottom:3px;padding:0 6px;">{{ $p1['team']->team_name }}</div>
                <div style="font-size:11px;color:#64748b;margin-bottom:10px;">{{ $p1['total_points'] }}pts · {{ $p1['wins'] }}W</div>
                @else <div style="height:80px;"></div> @endif
                <div style="width:100%;background:linear-gradient(180deg,#fbbf24,#d97706);border-radius:10px 10px 0 0;height:140px;display:flex;align-items:center;justify-content:center;box-shadow:0 -6px 24px rgba(251,191,36,0.4);">
                    <span style="font-size:42px;font-weight:900;color:#fff;">1</span>
                </div>
            </div>
            <div style="flex:1;display:flex;flex-direction:column;align-items:center;">
                @if($p3) <div style="font-size:26px;margin-bottom:6px;">🥉</div>
                <div style="font-size:13px;font-weight:800;color:#0f172a;text-align:center;margin-bottom:3px;padding:0 6px;">{{ $p3['team']->team_name }}</div>
                <div style="font-size:11px;color:#64748b;margin-bottom:10px;">{{ $p3['total_points'] }}pts · {{ $p3['wins'] }}W</div>
                @else <div style="height:80px;"></div> @endif
                <div style="width:100%;background:linear-gradient(180deg,#fb923c,#c2410c);border-radius:10px 10px 0 0;height:76px;display:flex;align-items:center;justify-content:center;">
                    <span style="font-size:28px;font-weight:900;color:#fff;">3</span>
                </div>
            </div>
        </div>
        <div style="background:#1a2233;height:14px;max-width:600px;margin:0 auto;border-radius:0 0 12px 12px;"></div>
    </div>

    {{-- Rankings table --}}
    <div class="card" style="margin-top:12px;">
        <div style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:800;margin-bottom:14px;">Complete Rankings</div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th style="width:48px;">Rank</th><th>Team</th><th>Played</th><th>Wins</th><th>Draws</th><th>Losses</th><th>Points</th><th>Win%</th></tr>
                </thead>
                <tbody>
                    @foreach($standings as $i => $row)
                    @php $wp = $row['matches_played'] > 0 ? round(($row['wins']/$row['matches_played'])*100) : 0; @endphp
                    @if($i === 3 && $standings->count() > 3)
                    <tr><td colspan="8" style="padding:6px 0 2px;">
                        <div style="border-top:1.5px dashed rgba(15,23,42,0.10);"></div>
                        <div style="font-size:10px;font-weight:700;letter-spacing:.1em;color:#94a3b8;text-transform:uppercase;padding:6px 0 0;">Remaining Teams</div>
                    </td></tr>
                    @endif
                    <tr style="{{ $i===0?'background:#fffbeb;':($i===1?'background:#f8faff;':($i===2?'background:#fff7f3;':'')) }}">
                        <td>
                            @if($i===0) <span style="font-size:20px;">🥇</span>
                            @elseif($i===1) <span style="font-size:20px;">🥈</span>
                            @elseif($i===2) <span style="font-size:20px;">🥉</span>
                            @else <span style="font-size:14px;font-weight:700;color:#94a3b8;">{{ $i+1 }}</span> @endif
                        </td>
                        <td style="font-weight:700;font-size:14px;">{{ $row['team']->team_name ?? '—' }}</td>
                        <td style="font-size:13px;color:#64748b;">{{ $row['matches_played'] }}</td>
                        <td><span style="font-size:16px;font-weight:800;color:#16a34a;">{{ $row['wins'] }}</span></td>
                        <td><span style="font-size:16px;font-weight:800;color:#0ea5e9;">{{ $row['draws'] }}</span></td>
                        <td><span style="font-size:16px;font-weight:800;color:#94a3b8;">{{ $row['losses'] }}</span></td>
                        <td><span style="font-size:16px;font-weight:800;">{{ $row['total_points'] }}</span></td>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div style="width:80px;height:6px;background:#f1f5f9;border-radius:3px;overflow:hidden;">
                                    <div style="width:{{ $wp }}%;height:100%;background:#3b82f6;border-radius:3px;"></div>
                                </div>
                                <span style="font-size:12px;font-weight:600;color:#64748b;">{{ $wp }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@elseif($standings->isNotEmpty())
    {{-- Ongoing standings — just the table, no podium --}}
    <div class="card" style="margin-top:16px;">
        <div style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:800;margin-bottom:14px;">Current Standings</div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th style="width:48px;">Rank</th><th>Team</th><th>Played</th><th>Wins</th><th>Draws</th><th>Losses</th><th>Points</th></tr>
                </thead>
                <tbody>
                    @foreach($standings as $i => $row)
                    <tr>
                        <td><span style="font-size:14px;font-weight:700;color:#94a3b8;">{{ $i+1 }}</span></td>
                        <td style="font-weight:700;font-size:14px;">{{ $row['team']->team_name ?? '—' }}</td>
                        <td style="font-size:13px;color:#64748b;">{{ $row['matches_played'] }}</td>
                        <td><span style="font-size:16px;font-weight:800;color:#16a34a;">{{ $row['wins'] }}</span></td>
                        <td><span style="font-size:16px;font-weight:800;color:#0ea5e9;">{{ $row['draws'] }}</span></td>
                        <td><span style="font-size:16px;font-weight:800;color:#94a3b8;">{{ $row['losses'] }}</span></td>
                        <td><span style="font-size:16px;font-weight:800;">{{ $row['total_points'] }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

@endsection