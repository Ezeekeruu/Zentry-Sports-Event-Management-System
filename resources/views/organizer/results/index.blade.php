@extends('layouts.organizer')

@section('content')
<div class="page-title" style="margin-bottom:20px;">Results</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Team</th><th>Match</th><th>Tournament</th><th>Date</th><th>Points</th><th>Rank</th><th>Result</th><th>Action</th></tr></thead>
            <tbody>
                @forelse($matchTeams as $mt)
                <tr>
                    <td style="font-weight:600;font-size:13px;">{{ $mt->team->team_name ?? '—' }}</td>
                    <td style="font-size:12px;color:#64748b;">{{ $mt->match->round_name ?: ('Match #'.$mt->match->id) }}</td>
                    <td style="font-size:12px;">{{ Str::limit($mt->match->tournament->tournament_name??'—',22) }}</td>
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
                    <td>
                        @if($mt->result) <span class="badge badge-green">RECORDED</span>
                        @else <span class="badge badge-amber">PENDING</span> @endif
                    </td>
                    <td>
                        <a href="{{ route('organizer.results.edit', $mt) }}"
                           class="btn-secondary" style="padding:5px 10px;font-size:11px;">
                            {{ $mt->result ? 'Edit' : 'Record' }}
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;color:#94a3b8;padding:30px;">No completed matches yet. Mark matches as completed to record results.</td></tr>
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
@endsection
