@extends('layouts.coach')

@section('content')

@if(!$team)
<div class="card" style="text-align:center;padding:48px;">
    <div style="font-size:32px;margin-bottom:12px;">🏀</div>
    <div style="font-family:'Manrope',sans-serif;font-size:18px;font-weight:700;margin-bottom:6px;">No Team Assigned</div>
    <div style="font-size:13px;color:#64748b;">You have not been assigned as a coach for any team yet. Contact an admin.</div>
</div>
@else

<div class="page-header">
    <div class="page-title">Match Schedule</div>
    <div class="page-subtitle">{{ $team->team_name }} matches.</div>
</div>

<div style="display:flex;gap:8px;margin-bottom:14px;">
    <form method="GET" action="{{ route('coach.matches.index') }}" style="display:flex;gap:8px;">
        <input type="hidden" name="month" value="{{ $calendarMonth->format('Y-m') }}">
        <select name="status" onchange="this.form.submit()"
                style="padding:7px 10px;border:1px solid rgba(15,23,42,0.12);border-radius:7px;font-size:12px;font-family:'Inter',sans-serif;color:#0f172a;background:#fff;outline:none;cursor:pointer;">
            <option value="">All Statuses</option>
            <option value="scheduled" {{ request('status')==='scheduled'?'selected':'' }}>Scheduled</option>
            <option value="live"      {{ request('status')==='live'?'selected':'' }}>Live</option>
            <option value="completed" {{ request('status')==='completed'?'selected':'' }}>Completed</option>
        </select>
    </form>
    @if(request('status'))
        <a href="{{ route('coach.matches.index') }}" class="btn-secondary" style="padding:7px 12px;font-size:12px;">Clear</a>
    @endif
</div>

<div class="card" style="margin-bottom:14px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;gap:8px;flex-wrap:wrap;">
        <div style="font-family:'Manrope',sans-serif;font-size:16px;font-weight:800;">Calendar</div>
        <div style="display:flex;align-items:center;gap:6px;">
            <a href="{{ route('coach.matches.index', ['status' => request('status'), 'month' => $calendarMonth->copy()->subMonth()->format('Y-m')]) }}"
               class="btn-secondary" style="padding:6px 10px;font-size:11px;">← Prev</a>
            <span style="font-size:12px;font-weight:700;color:#334155;min-width:130px;text-align:center;">
                {{ $calendarMonth->format('F Y') }}
            </span>
            <a href="{{ route('coach.matches.index', ['status' => request('status'), 'month' => $calendarMonth->copy()->addMonth()->format('Y-m')]) }}"
               class="btn-secondary" style="padding:6px 10px;font-size:11px;">Next →</a>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(7,minmax(0,1fr));gap:6px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">
        <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
    </div>
    @foreach($calendarGrid as $week)
        <div style="display:grid;grid-template-columns:repeat(7,minmax(0,1fr));gap:6px;margin-top:6px;">
            @foreach($week as $day)
                <div style="border:0.5px solid rgba(15,23,42,0.1);border-radius:8px;min-height:82px;padding:6px;background:{{ $day['isCurrentMonth'] ? '#fff' : '#f8fafc' }};">
                    <div style="font-size:11px;font-weight:700;color:{{ $day['isCurrentMonth'] ? '#0f172a' : '#94a3b8' }};">{{ $day['date']->day }}</div>
                    @if($day['matches']->isNotEmpty())
                        <div style="margin-top:4px;font-size:10px;font-weight:600;color:#0369a1;">{{ $day['matches']->count() }} match(es)</div>
                        @foreach($day['matches']->take(2) as $calendarMatch)
                            @php($opponents = $calendarMatch->matchTeams->filter(fn($mt)=>$mt->team_id!==$team->id)->map(fn($mt)=>$mt->team->team_name??'?')->values())
                            <div style="font-size:10px;color:#64748b;margin-top:2px;">
                                vs {{ $opponents->isNotEmpty() ? $opponents->join(' & ') : 'TBD' }}
                            </div>
                        @endforeach
                        @if($day['matches']->count() > 2)
                            <div style="font-size:10px;color:#94a3b8;">+{{ $day['matches']->count() - 2 }} more</div>
                        @endif
                    @endif
                </div>
            @endforeach
        </div>
    @endforeach
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Opponent(s)</th><th>Tournament</th><th>Date & Time</th><th>Venue</th><th>Round</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse($matches as $match)
                <tr>
                    <td>
                        @php($opponents = $match->matchTeams->filter(fn($mt)=>$mt->team_id!==$team->id)->map(fn($mt)=>$mt->team->team_name??'?')->values())
                        <div style="font-weight:600;font-size:13px;">
                            vs {{ $opponents->isNotEmpty() ? $opponents->join(' & ') : 'TBD' }}
                        </div>
                    </td>
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
                        <a href="{{ route('coach.matches.show', $match) }}"
                           style="font-size:11px;color:#eab308;font-weight:600;text-decoration:none;">Details →</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;color:#94a3b8;padding:30px;">No matches found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($matches instanceof \Illuminate\Pagination\LengthAwarePaginator && $matches->hasPages())
    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:16px;padding-top:12px;border-top:0.5px solid rgba(15,23,42,0.06);">
        <div style="font-size:12px;color:#94a3b8;">Showing {{ $matches->firstItem() }}–{{ $matches->lastItem() }} of {{ $matches->total() }}</div>
        <div style="display:flex;gap:4px;">
            @foreach($matches->getUrlRange(1,$matches->lastPage()) as $page => $url)
                @if($page==$matches->currentPage())
                    <span style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#fff;background:#1a2233;">{{ $page }}</span>
                @else
                    <a href="{{ $url }}&status={{ request('status') }}&month={{ $calendarMonth->format('Y-m') }}" style="padding:6px 10px;border-radius:5px;font-size:12px;color:#0f172a;border:0.5px solid rgba(15,23,42,0.1);background:#fff;text-decoration:none;">{{ $page }}</a>
                @endif
            @endforeach
        </div>
    </div>
    @endif
</div>

@endif
@endsection