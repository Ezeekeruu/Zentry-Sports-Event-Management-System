@extends('layouts.player')

@section('content')
<div class="page-header">
    <div class="page-title">Results</div>
    @if($team)<div class="page-subtitle">{{ $team->team_name }} match history.</div>@endif
</div>

@if(!$team)
<div class="card" style="text-align:center;padding:40px;">
    <div style="font-size:13px;color:#64748b;">You need to be on a team to view results.</div>
</div>
@else

<div class="grid-3">
    <div class="stat-card">
        <div class="stat-label">Career Points</div>
        <div class="stat-value">{{ $careerTotals['total_points'] }}</div>
        <div class="stat-sub">Encoded personal points</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Games With Player Stats</div>
        <div class="stat-value">{{ $careerTotals['matches_with_stats'] }}</div>
        <div class="stat-sub">Completed matches only</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Other Totals</div>
        <div class="stat-value" style="font-size:18px;line-height:1.2;">
            @if(!empty($careerTotals['stat_totals']))
                {{ collect($careerTotals['stat_totals'])->except('points')->map(fn($value, $key) => ucfirst(str_replace('_', ' ', $key)).': '.$value)->take(2)->join(' | ') ?: '—' }}
            @else
                —
            @endif
        </div>
        <div class="stat-sub">Based on recorded individual stats</div>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Tournament</th><th>Match</th><th>Date</th><th>Points</th><th>Rank</th><th>Your Stats</th><th>Summary</th></tr></thead>
            <tbody>
                @forelse($matchTeams as $mt)
                @php
                    $myStats = $mt->playerStats->first();
                    $statLine = $myStats?->stat_line ?? [];
                    if (empty($statLine) && $myStats?->points !== null) {
                        $statLine = ['points' => $myStats->points];
                    }
                    $statSummary = collect($statLine)->map(function ($value, $key) {
                        return ucfirst(str_replace('_', ' ', $key)) . ': ' . $value;
                    })->join(' · ');
                @endphp
                <tr>
                    <td style="font-size:12px;font-weight:500;">{{ Str::limit($mt->match->tournament->tournament_name??'—',22) }}</td>
                    <td style="font-size:12px;color:#64748b;">{{ $mt->match->round_name ?: ('Match #'.$mt->match->id) }}</td>
                    <td style="font-size:11px;color:#64748b;">{{ $mt->match->match_date->format('M d, Y') }}</td>
                    <td>
                        @if($mt->points_scored !== null)
                            <span style="font-family:'Manrope',sans-serif;font-size:16px;font-weight:800;">{{ $mt->points_scored }}</span>
                            <span style="font-size:10px;color:#94a3b8;">pts</span>
                        @else
                            <span style="color:#94a3b8;">—</span>
                        @endif
                    </td>
                    <td>
                        @if($mt->rank_position === 1) <span class="badge badge-amber">🥇 1st</span>
                        @elseif($mt->rank_position === 2) <span class="badge badge-gray">🥈 2nd</span>
                        @elseif($mt->rank_position === 3) <span class="badge badge-gray">🥉 3rd</span>
                        @elseif($mt->rank_position) <span class="badge badge-gray">#{{ $mt->rank_position }}</span>
                        @else <span style="color:#94a3b8;font-size:12px;">—</span> @endif
                    </td>
                    <td style="font-size:12px;color:#334155;">
                        {{ $statSummary ?: 'No individual stat recorded yet' }}
                    </td>
                    <td style="font-size:12px;color:#64748b;">
                        {{ $mt->result?->summary ? Str::limit($mt->result->summary,40) : '—' }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;color:#94a3b8;padding:30px;">No completed matches yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($matchTeams->hasPages())
    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:16px;padding-top:12px;border-top:0.5px solid rgba(15,23,42,0.06);">
        <div style="font-size:12px;color:#94a3b8;">Showing {{ $matchTeams->firstItem() }}–{{ $matchTeams->lastItem() }} of {{ $matchTeams->total() }}</div>
        <div style="display:flex;gap:4px;">
            @foreach($matchTeams->getUrlRange(1,$matchTeams->lastPage()) as $page => $url)
                @if($page==$matchTeams->currentPage())
                    <span style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#fff;background:#1a2233;">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" style="padding:6px 10px;border-radius:5px;font-size:12px;color:#0f172a;border:0.5px solid rgba(15,23,42,0.1);background:#fff;text-decoration:none;">{{ $page }}</a>
                @endif
            @endforeach
        </div>
    </div>
    @endif
</div>
@endif
@endsection
