@extends('layouts.organizer')

@section('topbar-action')
    <a href="{{ route('organizer.tournaments.create') }}" class="btn-primary">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
        New Tournament
    </a>
@endsection

@section('content')
<div class="page-title" style="margin-bottom:20px;">My Tournaments</div>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;gap:12px;flex-wrap:wrap;">
    <form method="GET" action="{{ route('organizer.tournaments.index') }}" style="display:flex;gap:8px;flex:1;max-width:400px;">
        <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
        <input type="hidden" name="status"   value="{{ request('status') }}">
        <div style="position:relative;flex:1;">
            <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:#94a3b8;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search tournaments..."
                   style="width:100%;padding:8px 12px 8px 32px;border:1px solid rgba(15,23,42,0.12);border-radius:7px;font-size:13px;font-family:'Inter',sans-serif;color:#0f172a;background:#fff;outline:none;">
        </div>
        <button type="submit" class="btn-primary" style="padding:8px 14px;">Search</button>
        @if(request('search') || request('status'))
            <a href="{{ route('organizer.tournaments.index') }}" class="btn-secondary" style="padding:8px 14px;">Clear</a>
        @endif
    </form>

    <div style="display:flex;gap:8px;">
        <form method="GET" action="{{ route('organizer.tournaments.index') }}" style="display:flex;gap:8px;">
            <input type="hidden" name="search"   value="{{ request('search') }}">
            <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
            <select name="status" onchange="this.form.submit()"
                    style="padding:7px 10px;border:1px solid rgba(15,23,42,0.12);border-radius:7px;font-size:12px;font-family:'Inter',sans-serif;color:#0f172a;background:#fff;outline:none;cursor:pointer;">
                <option value="">All Statuses</option>
                <option value="upcoming"  {{ request('status') === 'upcoming'  ? 'selected' : '' }}>Upcoming</option>
                <option value="ongoing"   {{ request('status') === 'ongoing'   ? 'selected' : '' }}>Ongoing</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </form>
        <form method="GET" action="{{ route('organizer.tournaments.index') }}" style="display:flex;align-items:center;gap:8px;">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <input type="hidden" name="status" value="{{ request('status') }}">
            <select name="per_page" onchange="this.form.submit()"
                    style="padding:7px 10px;border:1px solid rgba(15,23,42,0.12);border-radius:7px;font-size:12px;font-family:'Inter',sans-serif;color:#0f172a;background:#fff;outline:none;cursor:pointer;">
                @foreach([5,10,15,25] as $o)
                    <option value="{{ $o }}" {{ request('per_page',10)==$o?'selected':'' }}>{{ $o }}</option>
                @endforeach
            </select>
            <label style="font-size:12px;color:#64748b;">per page</label>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Tournament</th><th>Sport</th><th>Dates</th><th>Teams</th><th>Matches</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($tournaments as $t)
                <tr style="{{ !$t->is_active ? 'opacity:0.55;background:#fafafa;' : '' }}">
                    <td style="font-weight:600;font-size:13px;">{{ $t->tournament_name }}</td>
                    <td><span class="badge badge-blue">{{ $t->sport->sport_name ?? '—' }}</span></td>
                    <td style="font-size:11px;color:#64748b;">{{ $t->start_date->format('M d') }} – {{ $t->end_date->format('M d, Y') }}</td>
                    <td><span class="badge badge-green">{{ $t->registrations_count }}</span></td>
                    <td style="font-size:12px;color:#64748b;">{{ $t->matches_count }}</td>
                    <td>
                        @if($t->status==='ongoing') <span class="badge badge-green">ONGOING</span>
                        @elseif($t->status==='upcoming') <span class="badge badge-blue">UPCOMING</span>
                        @else <span class="badge badge-gray">COMPLETED</span> @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="{{ route('organizer.tournaments.show', $t) }}" class="btn-secondary" style="padding:5px 10px;font-size:11px;">View</a>
                            @if($t->is_active)
                                <a href="{{ route('organizer.tournaments.edit', $t) }}" class="btn-secondary" style="padding:5px 10px;font-size:11px;">Edit</a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;color:#94a3b8;padding:30px;">No tournaments found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($tournaments->hasPages())
    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:16px;padding-top:12px;border-top:0.5px solid rgba(15,23,42,0.06);">
        <div style="font-size:12px;color:#94a3b8;">Showing {{ $tournaments->firstItem() }}–{{ $tournaments->lastItem() }} of {{ $tournaments->total() }}</div>
        <div style="display:flex;gap:4px;">
            @foreach($tournaments->getUrlRange(1,$tournaments->lastPage()) as $page => $url)
                @if($page==$tournaments->currentPage())
                    <span style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#fff;background:#1a2233;">{{ $page }}</span>
                @else
                    <a href="{{ $url }}&search={{ request('search') }}&status={{ request('status') }}&per_page={{ request('per_page',10) }}" style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#0f172a;border:0.5px solid rgba(15,23,42,0.1);background:#fff;text-decoration:none;">{{ $page }}</a>
                @endif
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
