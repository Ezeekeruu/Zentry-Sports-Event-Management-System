@extends('layouts.public')

@section('content')
<div style="margin-bottom:24px;">
    <div class="page-title">Standings</div>
    <div class="page-subtitle">Select a tournament to view team rankings.</div>
</div>

<form method="GET" action="{{ route('public.standings.index') }}" style="display:flex;align-items:center;gap:10px;margin-bottom:24px;flex-wrap:wrap;">
    <select name="tournament_id"
            style="padding:9px 14px;border:1px solid rgba(15,23,42,0.12);border-radius:7px;font-size:13px;font-family:'Inter',sans-serif;color:#0f172a;background:#fff;outline:none;min-width:280px;cursor:pointer;">
        <option value="">Select a tournament...</option>
        @foreach($tournaments as $t)
            <option value="{{ $t->id }}" {{ request('tournament_id')==$t->id?'selected':'' }}>
                {{ $t->tournament_name }} ({{ ucfirst($t->status) }})
            </option>
        @endforeach
    </select>
    <button type="submit" class="btn-primary">View Standings</button>
</form>

@if($tournament)

<div class="card" style="background:linear-gradient(135deg,#1a2233 0%,#243048 100%);border:none;margin-bottom:20px;padding:20px 24px;">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <div style="font-family:'Manrope',sans-serif;font-size:20px;font-weight:800;color:#fff;">{{ $tournament->tournament_name }}</div>
            <div style="font-size:12px;color:rgba(255,255,255,0.5);margin-top:3px;">
                {{ $tournament->sport->sport_name ?? '' }} &nbsp;·&nbsp;
                {{ $tournament->start_date->format('M d') }} – {{ $tournament->end_date->format('M d, Y') }}
            </div>
        </div>
        @if($tournament->status==='ongoing')
            <span style="background:rgba(34,197,94,0.2);color:#22c55e;font-size:10px;font-weight:700;letter-spacing:.08em;padding:5px 12px;border-radius:20px;">ONGOING</span>
        @else
            <span style="background:rgba(255,215,0,0.15);color:#fbbf24;font-size:10px;font-weight:700;letter-spacing:.08em;padding:5px 12px;border-radius:20px;">🏁 COMPLETED</span>
        @endif
    </div>
</div>

@if($standings->isEmpty())
<div class="card" style="text-align:center;padding:48px;">
    <div style="font-size:32px;margin-bottom:12px;">📊</div>
    <div style="font-family:'Manrope',sans-serif;font-size:18px;font-weight:700;margin-bottom:6px;">No results yet</div>
    <div style="font-size:13px;color:#64748b;">Standings will appear once matches are completed and results are recorded.</div>
</div>

@else

{{-- PODIUM — completed tournaments only --}}
@if($tournament->status === 'completed')
@php
    $p1 = $standings->get(0);
    $p2 = $standings->get(1);
    $p3 = $standings->get(2);
@endphp
<div class="card" style="margin-bottom:20px;padding:32px 24px 0;overflow:hidden;">
    <div style="text-align:center;margin-bottom:28px;">
        <div style="font-family:'Manrope',sans-serif;font-size:11px;font-weight:700;letter-spacing:.12em;color:#94a3b8;text-transform:uppercase;margin-bottom:4px;">Tournament Champions</div>
        <div style="font-family:'Manrope',sans-serif;font-size:22px;font-weight:900;color:#0f172a;">🏆 Final Podium</div>
    </div>

    <div style="display:flex;align-items:flex-end;justify-content:center;gap:0;max-width:600px;margin:0 auto;">

        {{-- Silver 2nd --}}
        <div style="flex:1;display:flex;flex-direction:column;align-items:center;">
            @if($p2)
                <div style="font-size:30px;margin-bottom:6px;">🥈</div>
                <div style="font-family:'Manrope',sans-serif;font-size:13px;font-weight:800;color:#0f172a;text-align:center;line-height:1.3;margin-bottom:3px;padding:0 6px;">{{ $p2['team']->team_name ?? '—' }}</div>
                <div style="font-size:11px;color:#64748b;margin-bottom:10px;font-weight:600;">{{ $p2['total_points'] }} pts · {{ $p2['wins'] }}W</div>
            @else
                <div style="height:80px;"></div>
            @endif
            <div style="width:100%;background:linear-gradient(180deg,#e2e8f0 0%,#94a3b8 100%);border-radius:10px 10px 0 0;height:100px;display:flex;align-items:center;justify-content:center;">
                <span style="font-family:'Manrope',sans-serif;font-size:32px;font-weight:900;color:rgba(255,255,255,0.9);">2</span>
            </div>
        </div>

        {{-- Gold 1st --}}
        <div style="flex:1;display:flex;flex-direction:column;align-items:center;position:relative;">
            @if($p1)
                <div style="position:absolute;top:-18px;left:50%;transform:translateX(-50%);width:60px;height:60px;background:radial-gradient(circle,rgba(251,191,36,0.35) 0%,transparent 70%);border-radius:50%;pointer-events:none;"></div>
                <div style="font-size:38px;margin-bottom:6px;position:relative;">🥇</div>
                <div style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:900;color:#0f172a;text-align:center;line-height:1.3;margin-bottom:3px;padding:0 6px;">{{ $p1['team']->team_name ?? '—' }}</div>
                <div style="font-size:11px;color:#64748b;margin-bottom:10px;font-weight:600;">{{ $p1['total_points'] }} pts · {{ $p1['wins'] }}W</div>
            @else
                <div style="height:80px;"></div>
            @endif
            <div style="width:100%;background:linear-gradient(180deg,#fbbf24 0%,#d97706 100%);border-radius:10px 10px 0 0;height:140px;display:flex;align-items:center;justify-content:center;box-shadow:0 -6px 24px rgba(251,191,36,0.4);">
                <span style="font-family:'Manrope',sans-serif;font-size:42px;font-weight:900;color:#fff;text-shadow:0 2px 8px rgba(0,0,0,0.2);">1</span>
            </div>
        </div>

        {{-- Bronze 3rd --}}
        <div style="flex:1;display:flex;flex-direction:column;align-items:center;">
            @if($p3)
                <div style="font-size:26px;margin-bottom:6px;">🥉</div>
                <div style="font-family:'Manrope',sans-serif;font-size:13px;font-weight:800;color:#0f172a;text-align:center;line-height:1.3;margin-bottom:3px;padding:0 6px;">{{ $p3['team']->team_name ?? '—' }}</div>
                <div style="font-size:11px;color:#64748b;margin-bottom:10px;font-weight:600;">{{ $p3['total_points'] }} pts · {{ $p3['wins'] }}W</div>
            @else
                <div style="height:80px;"></div>
            @endif
            <div style="width:100%;background:linear-gradient(180deg,#fb923c 0%,#c2410c 100%);border-radius:10px 10px 0 0;height:76px;display:flex;align-items:center;justify-content:center;">
                <span style="font-family:'Manrope',sans-serif;font-size:28px;font-weight:900;color:#fff;">3</span>
            </div>
        </div>

    </div>
    <div style="background:#1a2233;height:14px;max-width:600px;margin:0 auto;border-radius:0 0 12px 12px;"></div>
</div>
@endif
{{-- END PODIUM --}}

{{-- Full standings table --}}
<div class="card">
    @if($tournament->status === 'completed')
        <div style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:800;margin-bottom:14px;color:#0f172a;">Complete Rankings</div>
    @endif
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:48px;">Rank</th>
                    <th>Team</th>
                    <th>Played</th>
                    <th>Wins</th>
                    <th>Draws</th>
                    <th>Losses</th>
                    <th>Points</th>
                    <th>Win Rate</th>
                </tr>
            </thead>
            <tbody>
                @foreach($standings as $i => $row)
                @php $winPct = $row['matches_played'] > 0 ? round(($row['wins']/$row['matches_played'])*100) : 0; @endphp

                @if($tournament->status === 'completed' && $i === 3 && $standings->count() > 3)
                <tr>
                    <td colspan="8" style="padding:6px 0 2px;">
                        <div style="border-top:1.5px dashed rgba(15,23,42,0.10);"></div>
                        <div style="font-size:10px;font-weight:700;letter-spacing:.1em;color:#94a3b8;text-transform:uppercase;padding:6px 0 0;">Remaining Teams</div>
                    </td>
                </tr>
                @endif

                <tr style="{{ $i===0 && $tournament->status==='completed' ? 'background:#fffbeb;' : ($i===1 && $tournament->status==='completed' ? 'background:#f8faff;' : ($i===2 && $tournament->status==='completed' ? 'background:#fff7f3;' : '')) }}">
                    <td>
                        @if($i===0) <span style="font-size:20px;">🥇</span>
                        @elseif($i===1) <span style="font-size:20px;">🥈</span>
                        @elseif($i===2) <span style="font-size:20px;">🥉</span>
                        @else <span style="font-family:'Manrope',sans-serif;font-size:14px;font-weight:700;color:#94a3b8;">{{ $i+1 }}</span>
                        @endif
                    </td>
                    <td><div style="font-weight:700;font-size:14px;">{{ $row['team']->team_name ?? '—' }}</div></td>
                    <td style="font-size:13px;color:#64748b;">{{ $row['matches_played'] }}</td>
                    <td><span style="font-family:'Manrope',sans-serif;font-size:16px;font-weight:800;color:#16a34a;">{{ $row['wins'] }}</span></td>
                    <td><span style="font-family:'Manrope',sans-serif;font-size:16px;font-weight:800;color:#0ea5e9;">{{ $row['draws'] }}</span></td>
                    <td><span style="font-family:'Manrope',sans-serif;font-size:16px;font-weight:800;color:#94a3b8;">{{ $row['losses'] }}</span></td>
                    <td><span style="font-family:'Manrope',sans-serif;font-size:16px;font-weight:800;color:#0f172a;">{{ $row['total_points'] }}</span></td>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="width:80px;height:6px;background:#f1f5f9;border-radius:3px;overflow:hidden;">
                                <div style="width:{{ $winPct }}%;height:100%;background:#22c55e;border-radius:3px;"></div>
                            </div>
                            <span style="font-size:12px;font-weight:600;color:#64748b;">{{ $winPct }}%</span>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endif {{-- end standings not empty --}}

{{-- Bracket shown only for ongoing --}}
@if($tournament->status === 'ongoing')
<div class="card" style="margin-top:16px;">
    <div style="font-family:'Manrope',sans-serif;font-size:16px;font-weight:800;margin-bottom:10px;">Bracket / Advancement</div>
    @if($bracketRounds->isEmpty())
        <div style="text-align:center;color:#94a3b8;padding:10px 0;font-size:13px;">No bracket data yet.</div>
    @else
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(230px,1fr));gap:12px;">
            @foreach($bracketRounds as $round => $roundMatches)
                <div style="border:0.5px solid rgba(15,23,42,0.08);border-radius:10px;padding:10px;background:#fafcff;">
                    <div style="font-size:11px;font-weight:700;letter-spacing:.06em;color:#64748b;text-transform:uppercase;margin-bottom:8px;">{{ $round }}</div>
                    @foreach($roundMatches as $slot)
                        <div style="border:0.5px solid rgba(15,23,42,0.08);border-radius:8px;background:#fff;padding:8px;margin-bottom:8px;">
                            <div style="font-size:11px;color:#94a3b8;margin-bottom:4px;">
                                {{ $slot['match']->match_date?->format('M d, Y') ?? 'TBD' }}
                            </div>
                            @foreach($slot['teams'] as $teamName)
                                <div style="font-size:12px;font-weight:600;">{{ $teamName }}</div>
                            @endforeach
                            <div style="font-size:11px;color:#334155;margin-top:6px;">
                                @if($slot['winners']->isNotEmpty())
                                    Advances: {{ $slot['winners']->join(', ') }}
                                @elseif($slot['drawn']->isNotEmpty())
                                    Draw: {{ $slot['drawn']->join(', ') }}
                                @else
                                    Awaiting result
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    @endif
</div>
@endif

@elseif(request('tournament_id'))
<div class="card" style="text-align:center;padding:40px;color:#94a3b8;">Tournament not found.</div>

@elseif($tournaments->isEmpty())
<div class="card" style="text-align:center;padding:48px;">
    <div style="font-size:32px;margin-bottom:12px;">🏟️</div>
    <div style="font-family:'Manrope',sans-serif;font-size:18px;font-weight:700;margin-bottom:6px;">No tournaments available</div>
    <div style="font-size:13px;color:#64748b;">Standings appear for ongoing and completed tournaments.</div>
</div>
@endif
@endsection