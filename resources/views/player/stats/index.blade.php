@extends('layouts.player')

@section('content')
<div style="max-width:980px;margin:0 auto;">
    <div class="page-header">
        <div class="page-title">My Stats</div>
        <div class="page-subtitle">Select a tournament to view your stats there, or leave as all tournaments.</div>
    </div>

    <form method="GET" action="{{ route('player.stats.index') }}" style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
        <label style="font-size:12px;color:#64748b;font-weight:500;">Tournament:</label>
        <select name="tournament_id" onchange="this.form.submit()" class="form-control" style="max-width:320px;">
            <option value="">All tournaments</option>
            @foreach($tournamentOptions as $tournament)
                <option value="{{ $tournament->id }}" {{ (string) $selectedTournamentId === (string) $tournament->id ? 'selected' : '' }}>
                    {{ $tournament->tournament_name }}
                </option>
            @endforeach
        </select>
        @if($selectedTournamentId)
            <a href="{{ route('player.stats.index') }}" class="btn-secondary" style="padding:8px 12px;font-size:12px;">Clear</a>
        @endif
    </form>

    <div class="grid-3">
        <div class="stat-card">
            <div class="stat-label">Matches Played</div>
            <div class="stat-value">{{ $overallStats['matches_played'] }}</div>
            <div class="stat-sub">Completed games</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Wins</div>
            <div class="stat-value">{{ $overallStats['wins'] }}</div>
            <div class="stat-sub">{{ $overallStats['podiums'] }} podium finishes</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Points</div>
            <div class="stat-value">{{ $overallStats['total_points'] }}</div>
            <div class="stat-sub">Avg {{ $overallStats['avg_points'] }} per game</div>
        </div>
    </div>

    <div class="card">
        @if(empty($totals))
            <div style="font-size:13px;color:#64748b;">No individual stats have been recorded yet for this selection.</div>
        @else
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:10px;">
                @foreach($totals as $key => $value)
                    <div style="border:0.5px solid rgba(15,23,42,0.08);border-radius:8px;padding:12px;background:#fafcff;">
                        <div style="font-size:10px;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;font-weight:700;">{{ str_replace('_', ' ', $key) }}</div>
                        <div style="font-family:'Manrope',sans-serif;font-size:24px;font-weight:800;color:#0f172a;margin-top:4px;">{{ $value }}</div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="grid-2">
        <div class="card">
            <div style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:700;margin-bottom:14px;">Per-Game Player Stats</div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Tournament</th>
                            <th>Match</th>
                            <th>Date</th>
                            <th>Points</th>
                            <th>Rank</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stats->take(30) as $item)
                            <tr>
                                <td style="font-size:12px;font-weight:600;">{{ Str::limit($item->matchTeam->match->tournament->tournament_name ?? '—', 24) }}</td>
                                <td style="font-size:11px;color:#64748b;">{{ $item->matchTeam->match->round_name ?: ('Match #'.$item->matchTeam->match->id) }}</td>
                                <td style="font-size:11px;color:#64748b;">{{ $item->matchTeam->match->match_date->format('M d, Y') }}</td>
                                <td style="font-size:12px;font-weight:700;">{{ $item->points ?? 0 }}</td>
                                <td>
                                    @if($item->matchTeam->rank_position)
                                        <span class="badge badge-purple">#{{ $item->matchTeam->rank_position }}</span>
                                    @else
                                        <span style="font-size:12px;color:#94a3b8;">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" style="text-align:center;color:#94a3b8;padding:24px;font-size:12px;">No completed match stats yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:700;margin-bottom:14px;">Tournament Overall</div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Tournament</th>
                            <th>Played</th>
                            <th>Wins</th>
                            <th>Points</th>
                            <th>Win Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tournamentStats as $row)
                            <tr>
                                <td style="font-size:12px;font-weight:600;">{{ Str::limit($row['tournament']->tournament_name ?? '—', 22) }}</td>
                                <td>{{ $row['matches_played'] }}</td>
                                <td>{{ $row['wins'] }}</td>
                                <td>{{ $row['total_points'] }}</td>
                                <td><span class="badge badge-blue">{{ $row['win_rate'] }}%</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" style="text-align:center;color:#94a3b8;padding:24px;font-size:12px;">No tournament data yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
