@extends('layouts.admin')

@section('topbar-action')
    <a href="{{ route('admin.teams.create') }}" class="btn-primary">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
        Add Team
    </a>
@endsection

@section('content')
<div class="page-title" style="margin-bottom:20px;">Teams</div>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;gap:12px;">
    <form method="GET" action="{{ route('admin.teams.index') }}" style="display:flex;align-items:center;gap:8px;flex:1;max-width:480px;">
        <input type="hidden" name="per_page" value="{{ request('per_page', 15) }}">
        <input type="hidden" name="sport_id" value="{{ request('sport_id') }}">
        <div style="position:relative;flex:1;">
            <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:#94a3b8;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search teams..."
                   style="width:100%;padding:8px 12px 8px 32px;border:1px solid rgba(15,23,42,0.12);border-radius:7px;font-size:13px;font-family:'Inter',sans-serif;color:#0f172a;background:#fff;outline:none;">
        </div>
        <button type="submit" class="btn-primary" style="padding:8px 14px;">Search</button>
        @if(request('search') || request('sport_id'))
            <a href="{{ route('admin.teams.index') }}" class="btn-secondary" style="padding:8px 14px;">Clear</a>
        @endif
    </form>

    <div style="display:flex;align-items:center;gap:8px;">
        <form method="GET" action="{{ route('admin.teams.index') }}" style="display:flex;align-items:center;gap:8px;">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <input type="hidden" name="per_page" value="{{ request('per_page', 15) }}">
            <select name="sport_id" onchange="this.form.submit()"
                    style="padding:7px 10px;border:1px solid rgba(15,23,42,0.12);border-radius:7px;font-size:12px;font-family:'Inter',sans-serif;color:#0f172a;background:#fff;outline:none;cursor:pointer;">
                <option value="">All Sports</option>
                @foreach($sports as $sport)
                    <option value="{{ $sport->id }}" {{ request('sport_id') == $sport->id ? 'selected' : '' }}>{{ $sport->sport_name }}</option>
                @endforeach
            </select>
        </form>

        <form method="GET" action="{{ route('admin.teams.index') }}" style="display:flex;align-items:center;gap:8px;">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <input type="hidden" name="sport_id" value="{{ request('sport_id') }}">
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
                    <th>Team</th>
                    <th>Sport</th>
                    <th>Coach</th>
                    <th>Players</th>
                    <th>Founded</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($teams as $team)
                <tr style="{{ !$team->is_active ? 'opacity:0.55;background:#fafafa;' : '' }}">
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div class="avatar">{{ strtoupper(substr($team->team_name, 0, 1)) }}</div>
                            <div>
                                <div style="font-weight:600;font-size:13px;">{{ $team->team_name }}</div>
                                @if($team->logo_url)
                                    <div style="font-size:10px;color:#94a3b8;">Has logo</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-blue">{{ $team->sport->sport_name ?? '—' }}</span>
                    </td>
                    <td>
                        @if($team->coach)
                            <div style="font-size:13px;font-weight:500;">{{ $team->coach->first_name }} {{ $team->coach->last_name }}</div>
                        @else
                            <span style="color:#94a3b8;font-size:12px;">No coach</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-green">{{ $team->player_profiles_count }} players</span>
                    </td>
                    <td style="font-size:12px;color:#64748b;">
                        {{ $team->founded_at ? $team->founded_at->format('M d, Y') : '—' }}
                    </td>
                    <td>
                        @if($team->is_active)
                            <span class="badge badge-green">ACTIVE</span>
                        @else
                            <span class="badge badge-gray">ARCHIVED</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;align-items:center;">
                            @if($team->is_active)
                                <a href="{{ route('admin.teams.players', $team) }}"
                                   class="btn-secondary" style="padding:5px 10px;font-size:11px;">Players</a>
                                <a href="{{ route('admin.teams.edit', $team) }}"
                                   class="btn-secondary" style="padding:5px 10px;font-size:11px;">Edit</a>
                                <button type="button" class="btn-danger archive-btn"
                                    data-name="{{ $team->team_name }}"
                                    data-players="{{ $team->player_profiles_count }}"
                                    data-url="{{ route('admin.teams.destroy', $team) }}">
                                    Archive
                                </button>
                            @else
                                <form method="POST" action="{{ route('admin.teams.restore', $team) }}">
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
                        {{ request('search') ? 'No teams found matching "' . request('search') . '"' : 'No teams found.' }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($teams->hasPages())
    <div style="display:flex;align-items:center;justify-content:space-between;margin-top:16px;padding-top:12px;border-top:0.5px solid rgba(15,23,42,0.06);">
        <div style="font-size:12px;color:#94a3b8;">
            Showing {{ $teams->firstItem() }} to {{ $teams->lastItem() }} of {{ $teams->total() }} results
        </div>
        <div style="display:flex;gap:4px;">
            @if($teams->onFirstPage())
                <span style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#d1d5db;border:0.5px solid rgba(15,23,42,0.1);background:#fff;">‹</span>
            @else
                <a href="{{ $teams->previousPageUrl() }}&search={{ request('search') }}&sport_id={{ request('sport_id') }}&per_page={{ request('per_page', 15) }}" style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#0f172a;border:0.5px solid rgba(15,23,42,0.1);background:#fff;text-decoration:none;">‹</a>
            @endif
            @foreach($teams->getUrlRange(1, $teams->lastPage()) as $page => $url)
                @if($page == $teams->currentPage())
                    <span style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#fff;background:#1a2233;border:0.5px solid #1a2233;">{{ $page }}</span>
                @else
                    <a href="{{ $url }}&search={{ request('search') }}&sport_id={{ request('sport_id') }}&per_page={{ request('per_page', 15) }}" style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#0f172a;border:0.5px solid rgba(15,23,42,0.1);background:#fff;text-decoration:none;">{{ $page }}</a>
                @endif
            @endforeach
            @if($teams->hasMorePages())
                <a href="{{ $teams->nextPageUrl() }}&search={{ request('search') }}&sport_id={{ request('sport_id') }}&per_page={{ request('per_page', 15) }}" style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#0f172a;border:0.5px solid rgba(15,23,42,0.1);background:#fff;text-decoration:none;">›</a>
            @else
                <span style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#d1d5db;border:0.5px solid rgba(15,23,42,0.1);background:#fff;">›</span>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- Archive confirmation modal --}}
<div id="archive-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:12px;padding:28px;max-width:420px;width:90%;margin:0 auto;">
        <div style="font-family:'Manrope',sans-serif;font-size:16px;font-weight:800;color:#0f172a;margin-bottom:8px;">
            Archive Team?
        </div>
        <div id="modal-body" style="font-size:13px;color:#64748b;line-height:1.6;margin-bottom:20px;"></div>

        <div style="display:flex;gap:10px;">
            <form id="archive-form" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger" style="padding:8px 16px;font-size:12px;">Archive Team</button>
            </form>
            <button type="button" class="btn-secondary" onclick="document.getElementById('archive-modal').style.display='none';">Cancel</button>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.archive-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var name    = this.dataset.name;
        var players = this.dataset.players;
        var url     = this.dataset.url;
        document.getElementById('modal-body').innerHTML =
            'You are about to archive <strong>' + name + '</strong>.<br>' +
            'This team has <strong>' + players + ' player(s)</strong> currently assigned.';
        document.getElementById('archive-form').action = url;
        document.getElementById('archive-modal').style.display = 'flex';
    });
});
</script>
@endsection
