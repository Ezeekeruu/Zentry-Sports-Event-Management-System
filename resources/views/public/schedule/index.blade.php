@extends('layouts.public')

@section('content')
<div style="margin-bottom:24px;">
    <div class="page-title">Match Schedule</div>
    <div class="page-subtitle">All upcoming and recent matches across tournaments.</div>
</div>

<div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;flex-wrap:wrap;">
    <form method="GET" action="{{ route('public.schedule.index') }}" style="display:flex;gap:8px;flex-wrap:wrap;">
        <select name="tournament_id" onchange="this.form.submit()"
                style="padding:8px 12px;border:1px solid rgba(15,23,42,0.12);border-radius:7px;font-size:12px;font-family:'Inter',sans-serif;color:#0f172a;background:#fff;outline:none;cursor:pointer;min-width:200px;">
            <option value="">All Tournaments</option>
            @foreach($tournaments as $t)
                <option value="{{ $t->id }}" {{ request('tournament_id')==$t->id?'selected':'' }}>{{ Str::limit($t->tournament_name,32) }}</option>
            @endforeach
        </select>
        <select name="status" onchange="this.form.submit()"
                style="padding:8px 12px;border:1px solid rgba(15,23,42,0.12);border-radius:7px;font-size:12px;font-family:'Inter',sans-serif;color:#0f172a;background:#fff;outline:none;cursor:pointer;">
            <option value="">All Statuses</option>
            <option value="scheduled" {{ request('status')==='scheduled'?'selected':'' }}>Scheduled</option>
            <option value="live"      {{ request('status')==='live'?'selected':'' }}>Live</option>
            <option value="completed" {{ request('status')==='completed'?'selected':'' }}>Completed</option>
        </select>
        @if(request('tournament_id') || request('status'))
            <a href="{{ route('public.schedule.index') }}" class="btn-secondary" style="padding:8px 14px;">Clear</a>
        @endif
    </form>
</div>

@if($matches->isEmpty())
<div class="card" style="text-align:center;padding:48px;">
    <div style="font-size:32px;margin-bottom:12px;">📅</div>
    <div style="font-family:'Manrope',sans-serif;font-size:18px;font-weight:700;margin-bottom:6px;">No matches found</div>
    <div style="font-size:13px;color:#64748b;">Try adjusting your filters.</div>
</div>
@else
<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Match</th>
                    <th>Tournament</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Venue</th>
                    <th>Round</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($matches as $match)
                <tr>
                    <td>
                        <div style="font-weight:700;font-size:13px;">
                            {{ $match->matchTeams->map(fn($mt)=>$mt->team->team_name??'?')->join(' vs ') ?: 'TBD' }}
                        </div>
                        <div style="font-size:10px;color:#94a3b8;margin-top:2px;">
                            {{ $match->tournament->sport->sport_name ?? '' }}
                        </div>
                    </td>
                    <td style="font-size:12px;">{{ Str::limit($match->tournament->tournament_name??'—',26) }}</td>
                    <td style="font-size:13px;font-weight:500;">{{ $match->match_date->format('M d, Y') }}</td>
                    <td style="font-size:12px;color:#64748b;">
                        {{ $match->match_time ? \Carbon\Carbon::parse($match->match_time)->format('h:i A') : '—' }}
                    </td>
                    <td style="font-size:12px;color:#64748b;">{{ $match->venue ?: '—' }}</td>
                    <td style="font-size:12px;color:#64748b;">{{ $match->round_name ?: '—' }}</td>
                    <td>
                        @if($match->status==='live')
                            <span class="badge badge-red" style="animation:pulse 1.5s infinite;">● LIVE</span>
                        @elseif($match->status==='completed')
                            <span class="badge badge-green">DONE</span>
                        @else
                            <span class="badge badge-blue">SCHED</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($matches->hasPages())
    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:16px;padding-top:12px;border-top:0.5px solid rgba(15,23,42,0.06);">
        <div style="font-size:12px;color:#94a3b8;">Showing {{ $matches->firstItem() }}–{{ $matches->lastItem() }} of {{ $matches->total() }} matches</div>
        <div style="display:flex;gap:4px;">
            @if(!$matches->onFirstPage())
                <a href="{{ $matches->previousPageUrl() }}&tournament_id={{ request('tournament_id') }}&status={{ request('status') }}" style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#0f172a;border:0.5px solid rgba(15,23,42,0.1);background:#fff;text-decoration:none;">‹</a>
            @endif
            @foreach($matches->getUrlRange(1,$matches->lastPage()) as $page => $url)
                @if($page==$matches->currentPage())
                    <span style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#fff;background:#1a2233;">{{ $page }}</span>
                @else
                    <a href="{{ $url }}&tournament_id={{ request('tournament_id') }}&status={{ request('status') }}" style="padding:6px 10px;border-radius:5px;font-size:12px;color:#0f172a;border:0.5px solid rgba(15,23,42,0.1);background:#fff;text-decoration:none;">{{ $page }}</a>
                @endif
            @endforeach
            @if($matches->hasMorePages())
                <a href="{{ $matches->nextPageUrl() }}&tournament_id={{ request('tournament_id') }}&status={{ request('status') }}" style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#0f172a;border:0.5px solid rgba(15,23,42,0.1);background:#fff;text-decoration:none;">›</a>
            @endif
        </div>
    </div>
    @endif
</div>
@endif
@endsection
