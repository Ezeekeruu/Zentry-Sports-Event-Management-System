@extends('layouts.admin')

@section('topbar-action')
    <a href="{{ route('admin.standings.index') }}" class="btn-secondary">View Standings</a>
@endsection

@section('content')
<div class="page-title" style="margin-bottom:20px;">Results & Standings</div>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;gap:12px;">
    <form method="GET" action="{{ route('admin.results.index') }}" style="display:flex;align-items:center;gap:8px;">
        <input type="hidden" name="per_page" value="{{ request('per_page', 15) }}">
        <label style="font-size:12px;color:#64748b;font-weight:500;">Filter tournament:</label>
        <select name="tournament_id" onchange="this.form.submit()"
                style="padding:7px 10px;border:1px solid rgba(15,23,42,0.12);border-radius:7px;font-size:12px;font-family:'Inter',sans-serif;color:#0f172a;background:#fff;outline:none;cursor:pointer;min-width:200px;">
            <option value="">All Tournaments</option>
            @foreach($tournaments as $t)
                <option value="{{ $t->id }}" {{ request('tournament_id') == $t->id ? 'selected' : '' }}>
                    {{ $t->tournament_name }}
                </option>
            @endforeach
        </select>
        @if(request('tournament_id'))
            <a href="{{ route('admin.results.index') }}" class="btn-secondary" style="padding:7px 12px;font-size:12px;">Clear</a>
        @endif
    </form>

    <form method="GET" action="{{ route('admin.results.index') }}" style="display:flex;align-items:center;gap:8px;">
        <input type="hidden" name="tournament_id" value="{{ request('tournament_id') }}">
        <label style="font-size:12px;color:#64748b;font-weight:500;">Show</label>
        <select name="per_page" onchange="this.form.submit()"
                style="padding:7px 10px;border:1px solid rgba(15,23,42,0.12);border-radius:7px;font-size:12px;font-family:'Inter',sans-serif;color:#0f172a;background:#fff;outline:none;cursor:pointer;">
            @foreach([5, 10, 15, 25, 50] as $option)
                <option value="{{ $option }}" {{ request('per_page', 15) == $option ? 'selected' : '' }}>{{ $option }}</option>
            @endforeach
        </select>
        <label style="font-size:12px;color:#64748b;font-weight:500;">per page</label>
    </form>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Team</th>
                    <th>Match</th>
                    <th>Tournament</th>
                    <th>Date</th>
                    <th>Points</th>
                    <th>Rank</th>
                    <th>Result</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($matchTeams as $mt)
                <tr>
                    <td>
                        <div style="font-weight:600;font-size:13px;">{{ $mt->team->team_name ?? '—' }}</div>
                    </td>
                    <td style="font-size:12px;color:#64748b;">
                        {{ $mt->match->round_name ?: ('Match #' . $mt->match->id) }}
                    </td>
                    <td style="font-size:12px;">{{ Str::limit($mt->match->tournament->tournament_name ?? '—', 25) }}</td>
                    <td style="font-size:11px;color:#64748b;">{{ $mt->match->match_date->format('M d, Y') }}</td>
                    <td>
                        @if($mt->points_scored !== null)
                            <span style="font-family:'Manrope',sans-serif;font-size:16px;font-weight:800;color:#0f172a;">{{ $mt->points_scored }}</span>
                            <span style="font-size:10px;color:#94a3b8;">pts</span>
                        @else
                            <span style="color:#94a3b8;font-size:12px;">—</span>
                        @endif
                    </td>
                    <td>
                        @if($mt->rank_position !== null)
                            @if($mt->rank_position === 1)
                                <span class="badge badge-amber">🥇 1st</span>
                            @elseif($mt->rank_position === 2)
                                <span class="badge badge-gray">🥈 2nd</span>
                            @elseif($mt->rank_position === 3)
                                <span class="badge badge-gray">🥉 3rd</span>
                            @else
                                <span class="badge badge-gray">#{{ $mt->rank_position }}</span>
                            @endif
                        @else
                            <span style="color:#94a3b8;font-size:12px;">—</span>
                        @endif
                    </td>
                    <td>
                        @if($mt->result)
                            <span class="badge badge-green">RECORDED</span>
                        @else
                            <span class="badge badge-amber">PENDING</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.results.edit', $mt) }}"
                           class="btn-secondary" style="padding:5px 10px;font-size:11px;">
                            {{ $mt->result ? 'Edit' : 'Record' }}
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;color:#94a3b8;padding:30px;">
                        No completed matches found. Results appear here once matches are marked as completed.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($matchTeams->hasPages())
    <div style="display:flex;align-items:center;justify-content:space-between;margin-top:16px;padding-top:12px;border-top:0.5px solid rgba(15,23,42,0.06);">
        <div style="font-size:12px;color:#94a3b8;">
            Showing {{ $matchTeams->firstItem() }} to {{ $matchTeams->lastItem() }} of {{ $matchTeams->total() }} results
        </div>
        <div style="display:flex;gap:4px;">
            @if($matchTeams->onFirstPage())
                <span style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#d1d5db;border:0.5px solid rgba(15,23,42,0.1);background:#fff;">‹</span>
            @else
                <a href="{{ $matchTeams->previousPageUrl() }}&tournament_id={{ request('tournament_id') }}&per_page={{ request('per_page', 15) }}" style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#0f172a;border:0.5px solid rgba(15,23,42,0.1);background:#fff;text-decoration:none;">‹</a>
            @endif
            @foreach($matchTeams->getUrlRange(1, $matchTeams->lastPage()) as $page => $url)
                @if($page == $matchTeams->currentPage())
                    <span style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#fff;background:#1a2233;border:0.5px solid #1a2233;">{{ $page }}</span>
                @else
                    <a href="{{ $url }}&tournament_id={{ request('tournament_id') }}&per_page={{ request('per_page', 15) }}" style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#0f172a;border:0.5px solid rgba(15,23,42,0.1);background:#fff;text-decoration:none;">{{ $page }}</a>
                @endif
            @endforeach
            @if($matchTeams->hasMorePages())
                <a href="{{ $matchTeams->nextPageUrl() }}&tournament_id={{ request('tournament_id') }}&per_page={{ request('per_page', 15) }}" style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#0f172a;border:0.5px solid rgba(15,23,42,0.1);background:#fff;text-decoration:none;">›</a>
            @else
                <span style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#d1d5db;border:0.5px solid rgba(15,23,42,0.1);background:#fff;">›</span>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
