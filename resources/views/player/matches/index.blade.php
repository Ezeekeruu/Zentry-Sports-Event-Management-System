@extends('layouts.player')

@section('content')
<div class="page-header">
    <div class="breadcrumb">PLAYER <span>› MATCHES</span></div>
    <div class="page-title">Matches</div>
    @if($team)<div class="page-subtitle">{{ $team->team_name }} schedule.</div>@endif
</div>

@if(!$team)
<div class="card" style="text-align:center;padding:40px;">
    <div style="font-size:13px;color:#64748b;">You need to be on a team to view match schedules.</div>
</div>
@else

<div style="display:flex;gap:8px;margin-bottom:14px;">
    <form method="GET" action="{{ route('player.matches.index') }}" style="display:flex;gap:8px;">
        <select name="status" onchange="this.form.submit()"
                style="padding:7px 10px;border:1px solid rgba(15,23,42,0.12);border-radius:7px;font-size:12px;font-family:'Inter',sans-serif;color:#0f172a;background:#fff;outline:none;cursor:pointer;">
            <option value="">All Matches</option>
            <option value="scheduled" {{ request('status')==='scheduled'?'selected':'' }}>Scheduled</option>
            <option value="live"      {{ request('status')==='live'?'selected':'' }}>Live</option>
            <option value="completed" {{ request('status')==='completed'?'selected':'' }}>Completed</option>
        </select>
    </form>
    @if(request('status'))
        <a href="{{ route('player.matches.index') }}" class="btn-secondary" style="padding:7px 12px;font-size:12px;">Clear</a>
    @endif
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Opponent(s)</th><th>Tournament</th><th>Date & Time</th><th>Venue</th><th>Round</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($matches as $match)
                <tr>
                    <td style="font-weight:600;font-size:13px;">
                        {{ $match->matchTeams->filter(fn($mt)=>$mt->team_id!==$team->id)->map(fn($mt)=>$mt->team->team_name??'?')->join(' & ') ?: 'TBD' }}
                    </td>
                    <td style="font-size:12px;">{{ Str::limit($match->tournament->tournament_name??'—',22) }}</td>
                    <td>
                        <div style="font-size:12px;font-weight:500;">{{ $match->match_date->format('M d, Y') }}</div>
                        @if($match->match_time)<div style="font-size:10px;color:#94a3b8;">{{ \Carbon\Carbon::parse($match->match_time)->format('h:i A') }}</div>@endif
                    </td>
                    <td style="font-size:12px;color:#64748b;">{{ $match->venue ?: '—' }}</td>
                    <td style="font-size:12px;color:#64748b;">{{ $match->round_name ?: '—' }}</td>
                    <td>
                        @if($match->status==='live') <span class="badge badge-red">LIVE</span>
                        @elseif($match->status==='completed') <span class="badge badge-green">DONE</span>
                        @else <span class="badge badge-blue">SCHED</span> @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;color:#94a3b8;padding:30px;">No matches found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($matches->hasPages())
    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:16px;padding-top:12px;border-top:0.5px solid rgba(15,23,42,0.06);">
        <div style="font-size:12px;color:#94a3b8;">Showing {{ $matches->firstItem() }}–{{ $matches->lastItem() }} of {{ $matches->total() }}</div>
        <div style="display:flex;gap:4px;">
            @foreach($matches->getUrlRange(1,$matches->lastPage()) as $page => $url)
                @if($page==$matches->currentPage())
                    <span style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#fff;background:#1a2233;">{{ $page }}</span>
                @else
                    <a href="{{ $url }}&status={{ request('status') }}" style="padding:6px 10px;border-radius:5px;font-size:12px;color:#0f172a;border:0.5px solid rgba(15,23,42,0.1);background:#fff;text-decoration:none;">{{ $page }}</a>
                @endif
            @endforeach
        </div>
    </div>
    @endif
</div>
@endif
@endsection
