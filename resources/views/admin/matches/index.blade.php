@extends('layouts.admin')

@section('topbar-action')
    <a href="{{ route('admin.matches.create') }}" class="btn-primary">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
        Schedule Match
    </a>
@endsection

@section('content')
<div class="page-title" style="margin-bottom:20px;">Match Schedule</div>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;gap:12px;flex-wrap:wrap;">
    <form method="GET" action="{{ route('admin.matches.index') }}" style="display:flex;align-items:center;gap:8px;flex:1;max-width:400px;">
        <input type="hidden" name="per_page"       value="{{ request('per_page', 15) }}">
        <input type="hidden" name="status"         value="{{ request('status') }}">
        <input type="hidden" name="tournament_id"  value="{{ request('tournament_id') }}">
        <div style="position:relative;flex:1;">
            <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:#94a3b8;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search venue, round, tournament..."
                   style="width:100%;padding:8px 12px 8px 32px;border:1px solid rgba(15,23,42,0.12);border-radius:7px;font-size:13px;font-family:'Inter',sans-serif;color:#0f172a;background:#fff;outline:none;">
        </div>
        <button type="submit" class="btn-primary" style="padding:8px 14px;">Search</button>
        @if(request('search') || request('status') || request('tournament_id'))
            <a href="{{ route('admin.matches.index') }}" class="btn-secondary" style="padding:8px 14px;">Clear</a>
        @endif
    </form>

    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <form method="GET" action="{{ route('admin.matches.index') }}" style="display:flex;gap:8px;">
            <input type="hidden" name="search"  value="{{ request('search') }}">
            <input type="hidden" name="per_page" value="{{ request('per_page', 15) }}">
            <input type="hidden" name="tournament_id" value="{{ request('tournament_id') }}">
            <select name="status" onchange="this.form.submit()"
                    style="padding:7px 10px;border:1px solid rgba(15,23,42,0.12);border-radius:7px;font-size:12px;font-family:'Inter',sans-serif;color:#0f172a;background:#fff;outline:none;cursor:pointer;">
                <option value="">All Statuses</option>
                <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                <option value="live"      {{ request('status') === 'live'      ? 'selected' : '' }}>Live</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </form>

        <form method="GET" action="{{ route('admin.matches.index') }}" style="display:flex;gap:8px;">
            <input type="hidden" name="search"  value="{{ request('search') }}">
            <input type="hidden" name="status"  value="{{ request('status') }}">
            <input type="hidden" name="per_page" value="{{ request('per_page', 15) }}">
            <select name="tournament_id" onchange="this.form.submit()"
                    style="padding:7px 10px;border:1px solid rgba(15,23,42,0.12);border-radius:7px;font-size:12px;font-family:'Inter',sans-serif;color:#0f172a;background:#fff;outline:none;cursor:pointer;">
                <option value="">All Tournaments</option>
                @foreach($tournaments as $tournament)
                    <option value="{{ $tournament->id }}" {{ request('tournament_id') == $tournament->id ? 'selected' : '' }}>
                        {{ Str::limit($tournament->tournament_name, 30) }}
                    </option>
                @endforeach
            </select>
        </form>

        <form method="GET" action="{{ route('admin.matches.index') }}" style="display:flex;align-items:center;gap:8px;">
            <input type="hidden" name="search"        value="{{ request('search') }}">
            <input type="hidden" name="status"        value="{{ request('status') }}">
            <input type="hidden" name="tournament_id" value="{{ request('tournament_id') }}">
            <label style="font-size:12px;color:#64748b;font-weight:500;">Show</label>
            <select name="per_page" onchange="this.form.submit()"
                    style="padding:7px 10px;border:1px solid rgba(15,23,42,0.12);border-radius:7px;font-size:12px;font-family:'Inter',sans-serif;color:#0f172a;background:#fff;outline:none;cursor:pointer;">
                @foreach([5, 10, 15,20, 25, 50] as $option)
                    <option value="{{ $option }}" {{ request('per_page', 15) == $option ? 'selected' : '' }}>{{ $option }}</option>
                @endforeach
            </select>
            <label style="font-size:12px;color:#64748b;font-weight:500;">per page</label>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Match</th>
                    <th>Tournament</th>
                    <th>Date & Time</th>
                    <th>Venue</th>
                    <th>Round</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($matches as $match)
                <tr style"{{ !$match->is_active ? 'opacity:0.55;background:#fafafa;' : '' }}">
                    <td>
                        <div style="font-weight:600;font-size:13px;">
                            {{ $match->matchTeams->map(fn($mt) => $mt->team->team_name ?? '?')->join(' vs ') ?: 'No teams' }}
                        </div>
                        <div style="font-size:10px;color:#94a3b8;">
                            {{ $match->tournament->sport->sport_name ?? '' }}
                        </div>
                    </td>
                    <td style="font-size:12px;">{{ Str::limit($match->tournament->tournament_name ?? '—', 28) }}</td>
                    <td>
                        <div style="font-size:12px;font-weight:500;">{{ $match->match_date->format('M d, Y') }}</div>
                        @if($match->match_time)
                            <div style="font-size:10px;color:#94a3b8;">{{ \Carbon\Carbon::parse($match->match_time)->format('h:i A') }}</div>
                        @endif
                    </td>
                    <td style="font-size:12px;color:#64748b;">{{ $match->venue ?: '—' }}</td>
                    <td style="font-size:12px;color:#64748b;">{{ $match->round_name ?: '—' }}</td>
                    <td>
                        @if($match->status === 'live')
                            <span class="badge badge-red">LIVE</span>
                        @elseif($match->status === 'completed')
                            <span class="badge badge-green">DONE</span>
                        @else
                            <span class="badge badge-blue">SCHED</span>
                        @endif
                        @if(!$match->is_active)
                            <span class="badge badge-gray" style="margin-left:4px;">ARCHIVED</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;align-items:center;">
                            @if($match->is_active)
                                <a href="{{ route('admin.matches.edit', $match) }}"
                                   class="btn-secondary" style="padding:5px 10px;font-size:11px;">Edit</a>
                                <button type="button" class="btn-danger archive-btn"
                                    data-name="{{ $match->matchTeams->map(fn($mt) => $mt->team->team_name ?? '?')->join(' vs ') }}"
                                    data-url="{{ route('admin.matches.destroy', $match) }}">
                                    Archive
                                </button>
                            @else
                                <form method="POST" action="{{ route('admin.matches.restore', $match) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        style="background:#dcfce7;color:#14532d;border:none;border-radius:7px;padding:5px 10px;font-size:11px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif;">
                                        Restore
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;color:#94a3b8;padding:30px;">
                        {{ request('search') ? 'No matches found matching "' . request('search') . '"' : 'No matches scheduled yet.' }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($matches->hasPages())
    <div style="display:flex;align-items:center;justify-content:space-between;margin-top:16px;padding-top:12px;border-top:0.5px solid rgba(15,23,42,0.06);">
        <div style="font-size:12px;color:#94a3b8;">
            Showing {{ $matches->firstItem() }} to {{ $matches->lastItem() }} of {{ $matches->total() }} results
        </div>
        <div style="display:flex;gap:4px;">
            @if($matches->onFirstPage())
                <span style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#d1d5db;border:0.5px solid rgba(15,23,42,0.1);background:#fff;">‹</span>
            @else
                <a href="{{ $matches->previousPageUrl() }}&search={{ request('search') }}&status={{ request('status') }}&tournament_id={{ request('tournament_id') }}&per_page={{ request('per_page', 15) }}" style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#0f172a;border:0.5px solid rgba(15,23,42,0.1);background:#fff;text-decoration:none;">‹</a>
            @endif
            @foreach($matches->getUrlRange(1, $matches->lastPage()) as $page => $url)
                @if($page == $matches->currentPage())
                    <span style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#fff;background:#1a2233;border:0.5px solid #1a2233;">{{ $page }}</span>
                @else
                    <a href="{{ $url }}&search={{ request('search') }}&status={{ request('status') }}&tournament_id={{ request('tournament_id') }}&per_page={{ request('per_page', 15) }}" style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#0f172a;border:0.5px solid rgba(15,23,42,0.1);background:#fff;text-decoration:none;">{{ $page }}</a>
                @endif
            @endforeach
            @if($matches->hasMorePages())
                <a href="{{ $matches->nextPageUrl() }}&search={{ request('search') }}&status={{ request('status') }}&tournament_id={{ request('tournament_id') }}&per_page={{ request('per_page', 15) }}" style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#0f172a;border:0.5px solid rgba(15,23,42,0.1);background:#fff;text-decoration:none;">›</a>
            @else
                <span style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#d1d5db;border:0.5px solid rgba(15,23,42,0.1);background:#fff;">›</span>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- Archive modal --}}
<div id="archive-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:12px;padding:28px;max-width:420px;width:90%;margin:0 auto;">
        <div style="font-family:'Manrope',sans-serif;font-size:16px;font-weight:800;color:#0f172a;margin-bottom:8px;">Archive Match?</div>
        <div id="modal-body" style="font-size:13px;color:#64748b;line-height:1.6;margin-bottom:20px;"></div>
        <div style="display:flex;gap:10px;">
            <form id="archive-form" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger" style="padding:8px 16px;font-size:12px;">Archive Match</button>
            </form>
            <button type="button" class="btn-secondary" onclick="document.getElementById('archive-modal').style.display='none';">Cancel</button>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.archive-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.getElementById('modal-body').innerHTML =
            'You are about to archive the match: <strong>' + this.dataset.name + '</strong>.';
        document.getElementById('archive-form').action = this.dataset.url;
        document.getElementById('archive-modal').style.display = 'flex';
    });
});
</script>
@endsection
