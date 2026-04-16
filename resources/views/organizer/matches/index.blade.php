@extends('layouts.organizer')

@section('topbar-action')
    <a href="{{ route('organizer.matches.create') }}" class="btn-primary">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
        Schedule Match
    </a>
@endsection

@section('content')
<div class="page-title" style="margin-bottom:20px;">Match Schedule</div>

<div style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap;">
    <form method="GET" action="{{ route('organizer.matches.index') }}" style="display:flex;gap:8px;">
        <input type="hidden" name="per_page" value="{{ request('per_page',10) }}">
        <select name="status" onchange="this.form.submit()"
                style="padding:7px 10px;border:1px solid rgba(15,23,42,0.12);border-radius:7px;font-size:12px;font-family:'Inter',sans-serif;color:#0f172a;background:#fff;outline:none;cursor:pointer;">
            <option value="">All Statuses</option>
            <option value="scheduled" {{ request('status')==='scheduled'?'selected':'' }}>Scheduled</option>
            <option value="live"      {{ request('status')==='live'?'selected':'' }}>Live</option>
            <option value="completed" {{ request('status')==='completed'?'selected':'' }}>Completed</option>
        </select>
    </form>
    <form method="GET" action="{{ route('organizer.matches.index') }}" style="display:flex;align-items:center;gap:8px;">
        <input type="hidden" name="status" value="{{ request('status') }}">
        <select name="per_page" onchange="this.form.submit()"
                style="padding:7px 10px;border:1px solid rgba(15,23,42,0.12);border-radius:7px;font-size:12px;font-family:'Inter',sans-serif;color:#0f172a;background:#fff;outline:none;cursor:pointer;">
            @foreach([5, 10, 15,20, 25, 50] as $o)
                <option value="{{ $o }}" {{ request('per_page',10)==$o?'selected':'' }}>{{ $o }}</option>
            @endforeach
        </select>
        <label style="font-size:12px;color:#64748b;">per page</label>
    </form>
    @if(request('status'))
        <a href="{{ route('organizer.matches.index') }}" class="btn-secondary" style="padding:7px 12px;font-size:12px;">Clear</a>
    @endif
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Match</th><th>Tournament</th><th>Date</th><th>Venue</th><th>Round</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($matches as $match)
                <tr>
                    <td style="font-weight:600;font-size:13px;">{{ $match->matchTeams->map(fn($mt)=>$mt->team->team_name??'?')->join(' vs ') ?: 'No teams' }}</td>
                    <td style="font-size:12px;">{{ Str::limit($match->tournament->tournament_name??'—',22) }}</td>
                    <td>
                        <div style="font-size:12px;font-weight:500;">{{ $match->match_date->format('M d, Y') }}</div>
                        @if($match->match_time) <div style="font-size:10px;color:#94a3b8;">{{ \Carbon\Carbon::parse($match->match_time)->format('h:i A') }}</div> @endif
                    </td>
                    <td style="font-size:12px;color:#64748b;">{{ $match->venue ?: '—' }}</td>
                    <td style="font-size:12px;color:#64748b;">{{ $match->round_name ?: '—' }}</td>
                    <td>
                        @if($match->status==='live') <span class="badge badge-red">LIVE</span>
                        @elseif($match->status==='completed') <span class="badge badge-green">DONE</span>
                        @else <span class="badge badge-blue">SCHED</span> @endif
                    </td>
                    <td>
                        <a href="{{ route('organizer.matches.edit', $match) }}" class="btn-secondary" style="padding:5px 10px;font-size:11px;">Edit</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;color:#94a3b8;padding:30px;">No matches scheduled yet.</td></tr>
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
                    <a href="{{ $url }}&status={{ request('status') }}&per_page={{ request('per_page',10) }}" style="padding:6px 10px;border-radius:5px;font-size:12px;color:#0f172a;border:0.5px solid rgba(15,23,42,0.1);background:#fff;text-decoration:none;">{{ $page }}</a>
                @endif
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
