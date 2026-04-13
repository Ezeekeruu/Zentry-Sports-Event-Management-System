@extends('layouts.public')

@section('content')
<div style="margin-bottom:24px;">
    <div class="page-title">Tournaments</div>
    <div class="page-subtitle">Browse all active and upcoming tournaments.</div>
</div>

<div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;flex-wrap:wrap;">
    <form method="GET" action="{{ route('public.tournaments.index') }}" style="display:flex;gap:8px;flex:1;max-width:380px;">
        <input type="hidden" name="status" value="{{ request('status') }}">
        <div style="position:relative;flex:1;">
            <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:#94a3b8;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search tournaments..."
                   style="width:100%;padding:8px 12px 8px 32px;border:1px solid rgba(15,23,42,0.12);border-radius:7px;font-size:13px;font-family:'Inter',sans-serif;color:#0f172a;background:#fff;outline:none;">
        </div>
        <button type="submit" class="btn-primary" style="padding:8px 14px;">Search</button>
        @if(request('search') || request('status'))
            <a href="{{ route('public.tournaments.index') }}" class="btn-secondary" style="padding:8px 14px;">Clear</a>
        @endif
    </form>

    <form method="GET" action="{{ route('public.tournaments.index') }}" style="display:flex;gap:8px;">
        <input type="hidden" name="search" value="{{ request('search') }}">
        <select name="status" onchange="this.form.submit()"
                style="padding:8px 12px;border:1px solid rgba(15,23,42,0.12);border-radius:7px;font-size:12px;font-family:'Inter',sans-serif;color:#0f172a;background:#fff;outline:none;cursor:pointer;">
            <option value="">All Statuses</option>
            <option value="upcoming"  {{ request('status')==='upcoming'?'selected':'' }}>Upcoming</option>
            <option value="ongoing"   {{ request('status')==='ongoing'?'selected':'' }}>Ongoing</option>
            <option value="completed" {{ request('status')==='completed'?'selected':'' }}>Completed</option>
        </select>
    </form>
</div>

{{-- Tournament cards grid --}}
@if($tournaments->isEmpty())
<div class="card" style="text-align:center;padding:48px;">
    <div style="font-size:32px;margin-bottom:12px;">🏆</div>
    <div style="font-family:'Manrope',sans-serif;font-size:18px;font-weight:700;margin-bottom:6px;">No tournaments found</div>
    <div style="font-size:13px;color:#64748b;">Try adjusting your search or check back later.</div>
</div>
@else
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:16px;margin-bottom:24px;">
    @foreach($tournaments as $t)
    <a href="{{ route('public.tournaments.show', $t) }}"
       style="text-decoration:none;display:block;background:#fff;border-radius:12px;border:0.5px solid rgba(15,23,42,0.08);padding:20px;transition:box-shadow .15s;"
       onmouseover="this.style.boxShadow='0 4px 20px rgba(15,23,42,0.08)'"
       onmouseout="this.style.boxShadow='none'">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:12px;">
            <div style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:700;color:#0f172a;line-height:1.3;flex:1;margin-right:10px;">
                {{ $t->tournament_name }}
            </div>
            @if($t->status==='ongoing')
                <span class="badge badge-green">LIVE</span>
            @elseif($t->status==='upcoming')
                <span class="badge badge-blue">UPCOMING</span>
            @else
                <span class="badge badge-gray">DONE</span>
            @endif
        </div>

        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px;">
            <span class="badge badge-blue">{{ $t->sport->sport_name ?? '—' }}</span>
            <span style="font-size:11px;color:#64748b;">by {{ $t->organizer->first_name ?? '?' }} {{ $t->organizer->last_name ?? '' }}</span>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
            <div>
                <div style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#94a3b8;margin-bottom:2px;">Start</div>
                <div style="font-size:12px;font-weight:600;">{{ $t->start_date->format('M d, Y') }}</div>
            </div>
            <div>
                <div style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#94a3b8;margin-bottom:2px;">End</div>
                <div style="font-size:12px;font-weight:600;">{{ $t->end_date->format('M d, Y') }}</div>
            </div>
            <div>
                <div style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#94a3b8;margin-bottom:2px;">Teams</div>
                <div style="font-size:12px;font-weight:600;">{{ $t->registrations_count }}{{ $t->max_teams ? ' / '.$t->max_teams : '' }}</div>
            </div>
            <div>
                <div style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#94a3b8;margin-bottom:2px;">Matches</div>
                <div style="font-size:12px;font-weight:600;">{{ $t->matches_count }}</div>
            </div>
        </div>
    </a>
    @endforeach
</div>

@if($tournaments->hasPages())
<div style="display:flex;justify-content:center;gap:4px;">
    @if(!$tournaments->onFirstPage())
        <a href="{{ $tournaments->previousPageUrl() }}&search={{ request('search') }}&status={{ request('status') }}" style="padding:7px 12px;border-radius:6px;font-size:12px;font-weight:500;color:#0f172a;border:0.5px solid rgba(15,23,42,0.12);background:#fff;text-decoration:none;">‹ Prev</a>
    @endif
    @foreach($tournaments->getUrlRange(1,$tournaments->lastPage()) as $page => $url)
        @if($page==$tournaments->currentPage())
            <span style="padding:7px 12px;border-radius:6px;font-size:12px;font-weight:500;color:#fff;background:#1a2233;">{{ $page }}</span>
        @else
            <a href="{{ $url }}&search={{ request('search') }}&status={{ request('status') }}" style="padding:7px 12px;border-radius:6px;font-size:12px;font-weight:500;color:#0f172a;border:0.5px solid rgba(15,23,42,0.12);background:#fff;text-decoration:none;">{{ $page }}</a>
        @endif
    @endforeach
    @if($tournaments->hasMorePages())
        <a href="{{ $tournaments->nextPageUrl() }}&search={{ request('search') }}&status={{ request('status') }}" style="padding:7px 12px;border-radius:6px;font-size:12px;font-weight:500;color:#0f172a;border:0.5px solid rgba(15,23,42,0.12);background:#fff;text-decoration:none;">Next ›</a>
    @endif
</div>
@endif
@endif
@endsection
