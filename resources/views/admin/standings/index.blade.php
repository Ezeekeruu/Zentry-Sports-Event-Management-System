@extends('layouts.admin')

@section('topbar-action')
    <a href="{{ route('admin.results.index') }}" class="btn-secondary">← Results</a>
@endsection

@section('content')
<div class="page-header">
    <div class="page-title">Standings</div>
    <div class="page-subtitle">View standings and podium for all tournaments.</div>
</div>

{{-- Filter Card --}}
<div class="card" style="margin-bottom:20px;padding:16px 20px;">
    {{-- Status buttons --}}
    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:14px;">
        <span style="font-size:12px;font-weight:600;color:#64748b;margin-right:4px;">Filter:</span>
        <a href="{{ route('admin.standings.index') }}"
           style="padding:6px 16px;border-radius:7px;font-size:12px;font-weight:600;text-decoration:none;border:1px solid rgba(15,23,42,0.12);
                  background:{{ !request('status') ? '#1a2233' : '#fff' }};
                  color:{{ !request('status') ? '#fff' : '#0f172a' }};">All</a>
        <a href="{{ route('admin.standings.index', ['status' => 'ongoing']) }}"
           style="padding:6px 16px;border-radius:7px;font-size:12px;font-weight:600;text-decoration:none;border:1px solid rgba(15,23,42,0.12);
                  background:{{ request('status')==='ongoing' ? '#22c55e' : '#fff' }};
                  color:{{ request('status')==='ongoing' ? '#fff' : '#0f172a' }};">Ongoing</a>
        <a href="{{ route('admin.standings.index', ['status' => 'completed']) }}"
           style="padding:6px 16px;border-radius:7px;font-size:12px;font-weight:600;text-decoration:none;border:1px solid rgba(15,23,42,0.12);
                  background:{{ request('status')==='completed' ? '#1a2233' : '#fff' }};
                  color:{{ request('status')==='completed' ? '#fff' : '#0f172a' }};">Completed</a>
        <a href="{{ route('admin.standings.index', ['status' => 'upcoming']) }}"
           style="padding:6px 16px;border-radius:7px;font-size:12px;font-weight:600;text-decoration:none;border:1px solid rgba(15,23,42,0.12);
                  background:{{ request('status')==='upcoming' ? '#3b82f6' : '#fff' }};
                  color:{{ request('status')==='upcoming' ? '#fff' : '#0f172a' }};">Upcoming</a>
    </div>
    {{-- Tournament selector --}}
    <form method="GET" action="{{ route('admin.standings.index') }}" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
        <input type="hidden" name="status" value="{{ request('status') }}">
        <select name="tournament_id" class="form-control" style="flex:1;max-width:380px;">
            <option value="">All tournaments</option>
            @foreach($tournaments as $t)
                <option value="{{ $t->id }}" {{ request('tournament_id') == $t->id ? 'selected' : '' }}>
                    {{ $t->tournament_name }} ({{ ucfirst($t->status) }})
                </option>
            @endforeach
        </select>
        <button type="submit" class="btn-primary">View</button>
        @if(request('tournament_id'))
            <a href="{{ route('admin.standings.index', ['status' => request('status')]) }}" class="btn-secondary">Clear</a>
        @endif
    </form>
</div>

@if($tournament)

    {{-- Tournament Header --}}
    <div class="card" style="background:linear-gradient(135deg,#1a2233 0%,#243048 100%);border:none;margin-bottom:20px;padding:20px 24px;">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
            <div>
                <div style="font-family:'Manrope',sans-serif;font-size:20px;font-weight:800;color:#fff;">{{ $tournament->tournament_name }}</div>
                <div style="font-size:12px;color:rgba(255,255,255,0.5);margin-top:3px;">
                    {{ $tournament->sport->sport_name ?? '' }} &nbsp;·&nbsp;
                    {{ $tournament->start_date->format('M d') }} – {{ $tournament->end_date->format('M d, Y') }}
                </div>
            </div>
            @if($tournament->status === 'ongoing')
                <span style="background:rgba(34,197,94,0.2);color:#22c55e;font-size:10px;font-weight:700;letter-spacing:.08em;padding:5px 12px;border-radius:20px;">ONGOING</span>
            @elseif($tournament->status === 'upcoming')
                <span style="background:rgba(59,130,246,0.2);color:#3b82f6;font-size:10px;font-weight:700;letter-spacing:.08em;padding:5px 12px;border-radius:20px;">UPCOMING</span>
            @else
                <span style="background:rgba(255,215,0,0.15);color:#fbbf24;font-size:10px;font-weight:700;letter-spacing:.08em;padding:5px 12px;border-radius:20px;">🏁 COMPLETED</span>
            @endif
        </div>
    </div>

    @if($standings->isEmpty())
        <div class="card" style="text-align:center;padding:48px;">
            <div style="font-size:32px;margin-bottom:12px;">📊</div>
            <div style="font-family:'Manrope',sans-serif;font-size:18px;font-weight:700;margin-bottom:6px;">No results yet</div>
            <div style="font-size:13px;color:#64748b;">Standings appear once match results are recorded.</div>
        </div>
    @else

        {{-- PODIUM — completed only --}}
        @if($tournament->status === 'completed')
        @php $p1=$standings->get(0); $p2=$standings->get(1); $p3=$standings->get(2); @endphp
        <div class="card" style="margin-bottom:20px;padding:32px 24px 0;overflow:hidden;">
            <div style="text-align:center;margin-bottom:28px;">
                <div style="font-family:'Manrope',sans-serif;font-size:11px;font-weight:700;letter-spacing:.12em;color:#94a3b8;text-transform:uppercase;margin-bottom:4px;">Tournament Champions</div>
                <div style="font-family:'Manrope',sans-serif;font-size:22px;font-weight:900;color:#0f172a;">🏆 Final Podium</div>
            </div>
            <div style="display:flex;align-items:flex-end;justify-content:center;max-width:600px;margin:0 auto;">
                <div style="flex:1;display:flex;flex-direction:column;align-items:center;">
                    @if($p2) <div style="font-size:30px;margin-bottom:6px;">🥈</div>
                    <div style="font-family:'Manrope',sans-serif;font-size:13px;font-weight:800;color:#0f172a;text-align:center;line-height:1.3;margin-bottom:3px;padding:0 6px;">{{ $p2['team']->team_name ?? '—' }}</div>
                    <div style="font-size:11px;color:#64748b;margin-bottom:10px;font-weight:600;">{{ $p2['total_points'] }} pts · {{ $p2['wins'] }}W</div>
                    @else <div style="height:80px;"></div> @endif
                    <div style="width:100%;background:linear-gradient(180deg,#e2e8f0,#94a3b8);border-radius:10px 10px 0 0;height:100px;display:flex;align-items:center;justify-content:center;">
                        <span style="font-family:'Manrope',sans-serif;font-size:32px;font-weight:900;color:rgba(255,255,255,0.9);">2</span>
                    </div>
                </div>
                <div style="flex:1;display:flex;flex-direction:column;align-items:center;position:relative;">
                    @if($p1) <div style="position:absolute;top:-18px;left:50%;transform:translateX(-50%);width:60px;height:60px;background:radial-gradient(circle,rgba(251,191,36,0.35) 0%,transparent 70%);border-radius:50%;pointer-events:none;"></div>
                    <div style="font-size:38px;margin-bottom:6px;position:relative;">🥇</div>
                    <div style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:900;color:#0f172a;text-align:center;line-height:1.3;margin-bottom:3px;padding:0 6px;">{{ $p1['team']->team_name ?? '—' }}</div>
                    <div style="font-size:11px;color:#64748b;margin-bottom:10px;font-weight:600;">{{ $p1['total_points'] }} pts · {{ $p1['wins'] }}W</div>
                    @else <div style="height:80px;"></div> @endif
                    <div style="width:100%;background:linear-gradient(180deg,#fbbf24,#d97706);border-radius:10px 10px 0 0;height:140px;display:flex;align-items:center;justify-content:center;box-shadow:0 -6px 24px rgba(251,191,36,0.4);">
                        <span style="font-family:'Manrope',sans-serif;font-size:42px;font-weight:900;color:#fff;text-shadow:0 2px 8px rgba(0,0,0,0.2);">1</span>
                    </div>
                </div>
                <div style="flex:1;display:flex;flex-direction:column;align-items:center;">
                    @if($p3) <div style="font-size:26px;margin-bottom:6px;">🥉</div>
                    <div style="font-family:'Manrope',sans-serif;font-size:13px;font-weight:800;color:#0f172a;text-align:center;line-height:1.3;margin-bottom:3px;padding:0 6px;">{{ $p3['team']->team_name ?? '—' }}</div>
                    <div style="font-size:11px;color:#64748b;margin-bottom:10px;font-weight:600;">{{ $p3['total_points'] }} pts · {{ $p3['wins'] }}W</div>
                    @else <div style="height:80px;"></div> @endif
                    <div style="width:100%;background:linear-gradient(180deg,#fb923c,#c2410c);border-radius:10px 10px 0 0;height:76px;display:flex;align-items:center;justify-content:center;">
                        <span style="font-family:'Manrope',sans-serif;font-size:28px;font-weight:900;color:#fff;">3</span>
                    </div>
                </div>
            </div>
            <div style="background:#1a2233;height:14px;max-width:600px;margin:0 auto;border-radius:0 0 12px 12px;"></div>
        </div>
        @endif

        {{-- Full standings table --}}
        <div class="card">
            @if($tournament->status === 'completed')
                <div style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:800;margin-bottom:14px;">Complete Rankings</div>
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
                            <td>
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <div class="avatar">{{ strtoupper(substr($row['team']->team_name ?? '?', 0, 1)) }}</div>
                                    <div style="font-weight:700;font-size:14px;">{{ $row['team']->team_name ?? '—' }}</div>
                                </div>
                            </td>
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
    @endif

@elseif(request('tournament_id'))
    <div class="card"><div style="text-align:center;color:#94a3b8;padding:40px;">Tournament not found.</div></div>

@else
    {{-- All tournaments list --}}
    @forelse($allTournamentStandings as $group)
    @php $t = $group['tournament']; $rows = $group['standings']; @endphp

    <div class="card" style="background:linear-gradient(135deg,#1a2233 0%,#243048 100%);border:none;margin-bottom:8px;padding:16px 20px;">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
            <div>
                <div style="font-family:'Manrope',sans-serif;font-size:16px;font-weight:800;color:#fff;">{{ $t->tournament_name }}</div>
                <div style="font-size:11px;color:rgba(255,255,255,0.5);margin-top:2px;">
                    {{ $t->sport->sport_name ?? '' }} · {{ $t->start_date->format('M d') }} – {{ $t->end_date->format('M d, Y') }}
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                @if($t->status === 'ongoing')
                    <span style="background:rgba(34,197,94,0.2);color:#22c55e;font-size:10px;font-weight:700;padding:4px 10px;border-radius:20px;">ONGOING</span>
                @elseif($t->status === 'completed')
                    <span style="background:rgba(255,215,0,0.15);color:#fbbf24;font-size:10px;font-weight:700;padding:4px 10px;border-radius:20px;">🏁 COMPLETED</span>
                @else
                    <span style="background:rgba(59,130,246,0.2);color:#3b82f6;font-size:10px;font-weight:700;padding:4px 10px;border-radius:20px;">UPCOMING</span>
                @endif
                <a href="{{ route('admin.standings.index', ['tournament_id' => $t->id, 'status' => request('status')]) }}"
                   style="font-size:11px;color:rgba(255,255,255,0.6);font-weight:600;text-decoration:none;">View Details →</a>
            </div>
        </div>
    </div>

    {{-- Mini podium for completed --}}
    @if($t->status === 'completed' && $rows->count() >= 1)
    @php $r1=$rows->get(0); $r2=$rows->get(1); $r3=$rows->get(2); @endphp
    <div class="card" style="margin-bottom:8px;padding:20px 20px 0;">
        <div style="display:flex;align-items:flex-end;justify-content:center;max-width:480px;margin:0 auto;">
            <div style="flex:1;display:flex;flex-direction:column;align-items:center;">
                @if($r2) <div style="font-size:22px;margin-bottom:4px;">🥈</div>
                <div style="font-size:12px;font-weight:800;color:#0f172a;text-align:center;margin-bottom:2px;">{{ $r2['team']->team_name ?? '—' }}</div>
                <div style="font-size:10px;color:#64748b;margin-bottom:8px;">{{ $r2['total_points'] }}pts</div>
                @else <div style="height:60px;"></div> @endif
                <div style="width:100%;background:linear-gradient(180deg,#e2e8f0,#94a3b8);border-radius:8px 8px 0 0;height:70px;display:flex;align-items:center;justify-content:center;">
                    <span style="font-size:22px;font-weight:900;color:#fff;">2</span>
                </div>
            </div>
            <div style="flex:1;display:flex;flex-direction:column;align-items:center;">
                @if($r1) <div style="font-size:28px;margin-bottom:4px;">🥇</div>
                <div style="font-size:13px;font-weight:900;color:#0f172a;text-align:center;margin-bottom:2px;">{{ $r1['team']->team_name ?? '—' }}</div>
                <div style="font-size:10px;color:#64748b;margin-bottom:8px;">{{ $r1['total_points'] }}pts</div>
                @else <div style="height:60px;"></div> @endif
                <div style="width:100%;background:linear-gradient(180deg,#fbbf24,#d97706);border-radius:8px 8px 0 0;height:100px;display:flex;align-items:center;justify-content:center;box-shadow:0 -4px 16px rgba(251,191,36,0.35);">
                    <span style="font-size:30px;font-weight:900;color:#fff;">1</span>
                </div>
            </div>
            <div style="flex:1;display:flex;flex-direction:column;align-items:center;">
                @if($r3) <div style="font-size:20px;margin-bottom:4px;">🥉</div>
                <div style="font-size:12px;font-weight:800;color:#0f172a;text-align:center;margin-bottom:2px;">{{ $r3['team']->team_name ?? '—' }}</div>
                <div style="font-size:10px;color:#64748b;margin-bottom:8px;">{{ $r3['total_points'] }}pts</div>
                @else <div style="height:60px;"></div> @endif
                <div style="width:100%;background:linear-gradient(180deg,#fb923c,#c2410c);border-radius:8px 8px 0 0;height:52px;display:flex;align-items:center;justify-content:center;">
                    <span style="font-size:20px;font-weight:900;color:#fff;">3</span>
                </div>
            </div>
        </div>
        <div style="background:#1a2233;height:10px;max-width:480px;margin:0 auto;border-radius:0 0 8px 8px;"></div>
    </div>
    @endif

    <div class="card" style="margin-bottom:24px;">
        @if($rows->isEmpty())
            <div style="text-align:center;color:#94a3b8;padding:20px;font-size:13px;">No completed games yet.</div>
        @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th style="width:40px;">Rank</th><th>Team</th><th>W</th><th>D</th><th>L</th><th>Points</th><th>Win%</th></tr>
                </thead>
                <tbody>
                    @foreach($rows as $i => $row)
                    @php $wp = $row['matches_played'] > 0 ? round(($row['wins']/$row['matches_played'])*100) : 0; @endphp
                    <tr>
                        <td>
                            @if($i===0) 🥇 @elseif($i===1) 🥈 @elseif($i===2) 🥉
                            @else <span style="font-size:13px;font-weight:700;color:#94a3b8;">{{ $i+1 }}</span> @endif
                        </td>
                        <td style="font-weight:600;font-size:13px;">{{ $row['team']->team_name ?? '—' }}</td>
                        <td style="font-weight:700;color:#16a34a;">{{ $row['wins'] }}</td>
                        <td style="font-weight:700;color:#0ea5e9;">{{ $row['draws'] }}</td>
                        <td style="font-weight:700;color:#94a3b8;">{{ $row['losses'] }}</td>
                        <td style="font-weight:800;font-family:'Manrope',sans-serif;">{{ $row['total_points'] }}</td>
                        <td style="font-size:12px;color:#64748b;">{{ $wp }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
    @empty
        <div class="card"><div style="text-align:center;color:#94a3b8;padding:40px;">No tournaments found.</div></div>
    @endforelse
@endif
@endsection