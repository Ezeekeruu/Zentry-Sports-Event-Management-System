@extends('layouts.admin')

@section('topbar-action')
    <a href="{{ route('admin.results.index') }}" class="btn-secondary">← Results</a>
@endsection

@section('content')
<div class="page-header">
    <div class="page-title">Standings</div>
    <div class="page-subtitle">All tournaments are shown by default. You can still filter one tournament below.</div>
</div>

<form method="GET" action="{{ route('admin.standings.index') }}" style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
    <select name="tournament_id" class="form-control" style="max-width:320px;">
        <option value="">Select a tournament...</option>
        @foreach($tournaments as $t)
            <option value="{{ $t->id }}" {{ request('tournament_id') == $t->id ? 'selected' : '' }}>
                {{ $t->tournament_name }} ({{ ucfirst($t->status) }})
            </option>
        @endforeach
    </select>
    <button type="submit" class="btn-primary">View Standings</button>
</form>

@if($tournament)
    <div class="card" style="margin-bottom:12px;padding:14px 20px;background:#f8faff;">
        <div style="display:flex;align-items:center;gap:16px;">
            <div>
                <div style="font-family:'Manrope',sans-serif;font-size:16px;font-weight:800;">{{ $tournament->tournament_name }}</div>
                <div style="font-size:12px;color:#64748b;margin-top:2px;">
                    {{ $tournament->sport->sport_name ?? '' }} &nbsp;·&nbsp;
                    {{ $tournament->start_date->format('M d') }} – {{ $tournament->end_date->format('M d, Y') }}
                </div>
            </div>
            @if($tournament->status === 'ongoing')
                <span class="badge badge-green" style="margin-left:auto;">ONGOING</span>
            @else
                <span class="badge badge-gray" style="margin-left:auto;">COMPLETED</span>
            @endif
        </div>
    </div>

    <div class="card">
        @if($standings->isEmpty())
            <div style="text-align:center;color:#94a3b8;padding:40px;font-size:13px;">
                No completed match results found for this tournament yet.
            </div>
        @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="width:48px;">#</th>
                        <th>Team</th>
                        <th>Played</th>
                        <th>Wins</th>
                        <th>Draws</th>
                        <th>Losses</th>
                        <th>Total Points</th>
                        <th>Win %</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($standings as $i => $row)
                    @php
                        $winPct = $row['matches_played'] > 0
                            ? round(($row['wins'] / $row['matches_played']) * 100)
                            : 0;
                    @endphp
                    <tr style="{{ $i === 0 ? 'background:#f0fdf4;' : '' }}">
                        <td>
                            @if($i === 0)
                                <span style="font-size:16px;">🥇</span>
                            @elseif($i === 1)
                                <span style="font-size:16px;">🥈</span>
                            @elseif($i === 2)
                                <span style="font-size:16px;">🥉</span>
                            @else
                                <span style="font-family:'Manrope',sans-serif;font-size:13px;font-weight:700;color:#94a3b8;">{{ $i + 1 }}</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div class="avatar">{{ strtoupper(substr($row['team']->team_name ?? '?', 0, 1)) }}</div>
                                <div style="font-weight:600;font-size:13px;">{{ $row['team']->team_name ?? '—' }}</div>
                            </div>
                        </td>
                        <td style="font-size:13px;font-weight:500;">{{ $row['matches_played'] }}</td>
                        <td>
                            <span style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:800;color:#16a34a;">{{ $row['wins'] }}</span>
                        </td>
                        <td>
                            <span style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:800;color:#0ea5e9;">{{ $row['draws'] }}</span>
                        </td>
                        <td>
                            <span style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:800;color:#94a3b8;">{{ $row['losses'] }}</span>
                        </td>
                        <td>
                            <span style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:800;color:#0f172a;">{{ $row['total_points'] }}</span>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div style="width:60px;height:5px;background:#f1f5f9;border-radius:3px;overflow:hidden;">
                                    <div style="width:{{ $winPct }}%;height:100%;background:#22c55e;border-radius:3px;"></div>
                                </div>
                                <span style="font-size:11px;font-weight:600;color:#64748b;">{{ $winPct }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <div class="card" style="margin-top:16px;">
        <div style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:800;margin-bottom:10px;">Bracket / Advancement</div>
        @if($bracketRounds->isEmpty())
            <div style="text-align:center;color:#94a3b8;padding:12px 0;font-size:13px;">No match rounds available yet.</div>
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
@elseif(request('tournament_id'))
    <div class="card">
        <div style="text-align:center;color:#94a3b8;padding:40px;">Tournament not found.</div>
    </div>
@else
    @forelse($allTournamentStandings as $group)
        <div class="card" style="margin-bottom:12px;padding:14px 20px;background:#f8faff;">
            <div style="display:flex;align-items:center;gap:16px;">
                <div>
                    <div style="font-family:'Manrope',sans-serif;font-size:16px;font-weight:800;">{{ $group['tournament']->tournament_name }}</div>
                    <div style="font-size:12px;color:#64748b;margin-top:2px;">
                        {{ $group['tournament']->sport->sport_name ?? '' }} &nbsp;·&nbsp;
                        {{ $group['tournament']->start_date->format('M d') }} – {{ $group['tournament']->end_date->format('M d, Y') }}
                    </div>
                </div>
                <span class="badge {{ $group['tournament']->status === 'ongoing' ? 'badge-green' : 'badge-gray' }}" style="margin-left:auto;">
                    {{ strtoupper($group['tournament']->status) }}
                </span>
            </div>
        </div>

        <div class="card" style="margin-bottom:18px;">
            @if($group['standings']->isEmpty())
                <div style="text-align:center;color:#94a3b8;padding:24px;font-size:13px;">No completed games yet.</div>
            @else
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th style="width:48px;">#</th>
                                <th>Team</th>
                                <th>W</th>
                                <th>L</th>
                                <th>D</th>
                                <th>WIN%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($group['standings'] as $i => $row)
                                @php($winPct = $row['matches_played'] > 0 ? number_format($row['wins'] / $row['matches_played'], 3) : '0.000')
                                <tr>
                                    <td style="font-weight:700;color:#334155;">{{ $i + 1 }}</td>
                                    <td style="font-weight:600;">{{ $row['team']->team_name ?? '—' }}</td>
                                    <td style="font-weight:700;color:#16a34a;">{{ $row['wins'] }}</td>
                                    <td style="font-weight:700;color:#64748b;">{{ $row['losses'] }}</td>
                                    <td style="font-weight:700;color:#0ea5e9;">{{ $row['draws'] }}</td>
                                    <td style="font-weight:700;">{{ $winPct }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @empty
        <div class="card">
            <div style="text-align:center;color:#94a3b8;padding:40px;">No active tournaments available.</div>
        </div>
    @endforelse
@endif
@endsection
