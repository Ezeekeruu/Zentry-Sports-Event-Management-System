@extends('layouts.coach')

@section('content')

@if(!$team)
<div class="card" style="text-align:center;padding:48px;">
    <div style="font-size:32px;margin-bottom:12px;">📋</div>
    <div style="font-family:'Manrope',sans-serif;font-size:18px;font-weight:700;margin-bottom:6px;">No Team Assigned</div>
    <div style="font-size:13px;color:#64748b;">You have not been assigned as a coach for any team yet. Contact an admin.</div>
</div>
@else

<div class="page-header">
    <div class="page-title">Match Results</div>
    <div class="page-subtitle">{{ $team->team_name }} performance history.</div>
</div>

{{-- Filters --}}
<div style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap;align-items:center;">
    <form method="GET" action="{{ route('coach.results.index') }}" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">

        {{-- Tournament filter --}}
        <select name="tournament_id" onchange="this.form.submit()"
                style="padding:7px 12px;border:1px solid rgba(15,23,42,0.12);border-radius:7px;font-size:12px;font-family:'Inter',sans-serif;color:#0f172a;background:#fff;outline:none;cursor:pointer;">
            <option value="">All Tournaments</option>
            @foreach($tournaments as $t)
                <option value="{{ $t->id }}" {{ request('tournament_id') == $t->id ? 'selected' : '' }}>
                    {{ $t->tournament_name }}
                </option>
            @endforeach
        </select>

        {{-- Outcome filter --}}
        <select name="outcome" onchange="this.form.submit()"
                style="padding:7px 12px;border:1px solid rgba(15,23,42,0.12);border-radius:7px;font-size:12px;font-family:'Inter',sans-serif;color:#0f172a;background:#fff;outline:none;cursor:pointer;">
            <option value="">All Outcomes</option>
            <option value="win"  {{ request('outcome') === 'win'  ? 'selected' : '' }}>Wins Only</option>
            <option value="loss" {{ request('outcome') === 'loss' ? 'selected' : '' }}>Losses Only</option>
        </select>

        @if(request('tournament_id') || request('outcome'))
            <a href="{{ route('coach.results.index') }}" class="btn-secondary" style="padding:7px 12px;font-size:12px;">Clear</a>
        @endif
    </form>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Opponent</th>
                    <th>Round</th>
                    <th>Tournament</th>
                    <th>Date</th>
                    <th>Score</th>
                    <th>Outcome</th>
                </tr>
            </thead>
            <tbody>
                @forelse($matchTeams as $mt)
                @php
                    // Get opponent team(s) from the same match
                    $opponents = $mt->match->matchTeams
                        ->where('team_id', '!=', $team->id)
                        ->map(fn($o) => $o->team->team_name ?? '?')
                        ->join(' & ');

                    // Our score vs their score
                    $ourScore  = $mt->points_scored;
                    $theirMt   = $mt->match->matchTeams->where('team_id', '!=', $team->id)->first();
                    $theirScore = $theirMt?->points_scored;

                    // Outcome
                    $isWin  = $mt->rank_position === 1;
                    $isDraw = !$isWin && $mt->rank_position !== null && $mt->match->matchTeams->where('rank_position', 1)->count() === 0;
                @endphp
                <tr>
                    <td>
                        <div style="font-weight:700;font-size:13px;">vs {{ $opponents ?: 'TBD' }}</div>
                    </td>
                    <td style="font-size:12px;font-weight:600;color:#334155;">
                        {{ $mt->match->round_name ?: 'Match #' . $mt->match->id }}
                    </td>
                    <td style="font-size:12px;color:#64748b;">
                        {{ Str::limit($mt->match->tournament->tournament_name ?? '—', 22) }}
                    </td>
                    <td style="font-size:11px;color:#64748b;">
                        {{ $mt->match->match_date->format('M d, Y') }}
                    </td>
                    <td>
                        @if($ourScore !== null && $theirScore !== null)
                            <span style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:900;color:#0f172a;">
                                {{ $ourScore }} – {{ $theirScore }}
                            </span>
                        @elseif($ourScore !== null)
                            <span style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:900;color:#0f172a;">{{ $ourScore }} pts</span>
                        @else
                            <span style="color:#94a3b8;font-size:12px;">—</span>
                        @endif
                    </td>
                    <td>
                        @if($mt->rank_position === 1)
                            <span class="badge badge-green">🏆 WIN</span>
                        @elseif($mt->rank_position === null)
                            <span class="badge badge-gray">—</span>
                        @else
                            <span class="badge badge-red">LOSS</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;color:#94a3b8;padding:30px;">No results found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($matchTeams->hasPages())
    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:16px;padding-top:12px;border-top:0.5px solid rgba(15,23,42,0.06);">
        <div style="font-size:12px;color:#94a3b8;">Showing {{ $matchTeams->firstItem() }}–{{ $matchTeams->lastItem() }} of {{ $matchTeams->total() }}</div>
        <div style="display:flex;gap:4px;">
            @foreach($matchTeams->getUrlRange(1,$matchTeams->lastPage()) as $page => $url)
                @if($page == $matchTeams->currentPage())
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