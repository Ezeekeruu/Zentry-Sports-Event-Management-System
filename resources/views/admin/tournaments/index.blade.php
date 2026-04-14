@extends('layouts.admin')

@section('topbar-action')
    <a href="{{ route('admin.tournaments.create') }}" class="btn-primary">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
        Add Tournament
    </a>
@endsection

@section('content')
<div class="page-title" style="margin-bottom:20px;">Tournaments</div>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;gap:12px;flex-wrap:wrap;">
    <form method="GET" action="{{ route('admin.tournaments.index') }}" style="display:flex;align-items:center;gap:8px;flex:1;max-width:400px;">
        <input type="hidden" name="per_page" value="{{ request('per_page', 15) }}">
        <input type="hidden" name="status"   value="{{ request('status') }}">
        <input type="hidden" name="sport_id" value="{{ request('sport_id') }}">
        <div style="position:relative;flex:1;">
            <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:#94a3b8;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search tournaments..."
                   style="width:100%;padding:8px 12px 8px 32px;border:1px solid rgba(15,23,42,0.12);border-radius:7px;font-size:13px;font-family:'Inter',sans-serif;color:#0f172a;background:#fff;outline:none;">
        </div>
        <button type="submit" class="btn-primary" style="padding:8px 14px;">Search</button>
        @if(request('search') || request('status') || request('sport_id'))
            <a href="{{ route('admin.tournaments.index') }}" class="btn-secondary" style="padding:8px 14px;">Clear</a>
        @endif
    </form>

    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
        <form method="GET" action="{{ route('admin.tournaments.index') }}" style="display:flex;gap:8px;">
            <input type="hidden" name="search"   value="{{ request('search') }}">
            <input type="hidden" name="sport_id" value="{{ request('sport_id') }}">
            <input type="hidden" name="per_page" value="{{ request('per_page', 15) }}">
            <select name="status" onchange="this.form.submit()"
                    style="padding:7px 10px;border:1px solid rgba(15,23,42,0.12);border-radius:7px;font-size:12px;font-family:'Inter',sans-serif;color:#0f172a;background:#fff;outline:none;cursor:pointer;">
                <option value="">All Statuses</option>
                <option value="upcoming"  {{ request('status') === 'upcoming'  ? 'selected' : '' }}>Upcoming</option>
                <option value="ongoing"   {{ request('status') === 'ongoing'   ? 'selected' : '' }}>Ongoing</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </form>

        <form method="GET" action="{{ route('admin.tournaments.index') }}" style="display:flex;gap:8px;">
            <input type="hidden" name="search"  value="{{ request('search') }}">
            <input type="hidden" name="status"  value="{{ request('status') }}">
            <input type="hidden" name="per_page" value="{{ request('per_page', 15) }}">
            <select name="sport_id" onchange="this.form.submit()"
                    style="padding:7px 10px;border:1px solid rgba(15,23,42,0.12);border-radius:7px;font-size:12px;font-family:'Inter',sans-serif;color:#0f172a;background:#fff;outline:none;cursor:pointer;">
                <option value="">All Sports</option>
                @foreach($sports as $sport)
                    <option value="{{ $sport->id }}" {{ request('sport_id') == $sport->id ? 'selected' : '' }}>{{ $sport->sport_name }}</option>
                @endforeach
            </select>
        </form>

        <form method="GET" action="{{ route('admin.tournaments.index') }}" style="display:flex;align-items:center;gap:8px;">
            <input type="hidden" name="search"   value="{{ request('search') }}">
            <input type="hidden" name="status"   value="{{ request('status') }}">
            <input type="hidden" name="sport_id" value="{{ request('sport_id') }}">
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
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Tournament</th>
                    <th>Sport</th>
                    <th>Organizer</th>
                    <th>Dates</th>
                    <th>Teams</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tournaments as $t)
                <tr style="{{ !$t->is_active ? 'opacity:0.55;background:#fafafa;' : '' }}">
                    <td>
                        <div style="font-weight:600;font-size:13px;">{{ $t->tournament_name }}</div>
                        <div style="font-size:10px;color:#94a3b8;">{{ $t->matches_count }} match(es)</div>
                    </td>
                    <td><span class="badge badge-blue">{{ $t->sport->sport_name ?? '—' }}</span></td>
                    <td style="font-size:12px;">{{ $t->organizer->first_name ?? '—' }} {{ $t->organizer->last_name ?? '' }}</td>
                    <td style="font-size:11px;color:#64748b;">
                        {{ $t->start_date->format('M d') }} – {{ $t->end_date->format('M d, Y') }}
                    </td>
                    <td>
                        <span class="badge badge-green">{{ $t->registrations_count }}
                            @if($t->max_teams) / {{ $t->max_teams }} @endif
                        </span>
                    </td>
                    <td>
                        @if($t->status === 'ongoing')
                            <span class="badge badge-green">ONGOING</span>
                        @elseif($t->status === 'upcoming')
                            <span class="badge badge-blue">UPCOMING</span>
                        @else
                            <span class="badge badge-gray">COMPLETED</span>
                        @endif
                        @if(!$t->is_active)
                            <span class="badge badge-gray" style="margin-left:4px;">ARCHIVED</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;align-items:center;">
                            @if($t->is_active)
                                <a href="{{ route('admin.tournaments.show', $t) }}"
                                   class="btn-secondary" style="padding:5px 10px;font-size:11px;">View</a>
                                <a href="{{ route('admin.tournaments.edit', $t) }}"
                                   class="btn-secondary" style="padding:5px 10px;font-size:11px;">Edit</a>
                                <button type="button" class="btn-danger archive-btn"
                                    data-name="{{ $t->tournament_name }}"
                                    data-teams="{{ $t->registrations_count }}"
                                    data-url="{{ route('admin.tournaments.destroy', $t) }}">
                                    Archive
                                </button>
                            @else
                                <form method="POST" action="{{ route('admin.tournaments.restore', $t) }}">
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
                        {{ request('search') ? 'No tournaments found matching "' . request('search') . '"' : 'No tournaments found.' }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($tournaments->hasPages())
    <div style="display:flex;align-items:center;justify-content:space-between;margin-top:16px;padding-top:12px;border-top:0.5px solid rgba(15,23,42,0.06);">
        <div style="font-size:12px;color:#94a3b8;">
            Showing {{ $tournaments->firstItem() }} to {{ $tournaments->lastItem() }} of {{ $tournaments->total() }} results
        </div>
        <div style="display:flex;gap:4px;">
            @if($tournaments->onFirstPage())
                <span style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#d1d5db;border:0.5px solid rgba(15,23,42,0.1);background:#fff;">‹</span>
            @else
                <a href="{{ $tournaments->previousPageUrl() }}&search={{ request('search') }}&status={{ request('status') }}&sport_id={{ request('sport_id') }}&per_page={{ request('per_page', 15) }}" style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#0f172a;border:0.5px solid rgba(15,23,42,0.1);background:#fff;text-decoration:none;">‹</a>
            @endif
            @foreach($tournaments->getUrlRange(1, $tournaments->lastPage()) as $page => $url)
                @if($page == $tournaments->currentPage())
                    <span style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#fff;background:#1a2233;border:0.5px solid #1a2233;">{{ $page }}</span>
                @else
                    <a href="{{ $url }}&search={{ request('search') }}&status={{ request('status') }}&sport_id={{ request('sport_id') }}&per_page={{ request('per_page', 15) }}" style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#0f172a;border:0.5px solid rgba(15,23,42,0.1);background:#fff;text-decoration:none;">{{ $page }}</a>
                @endif
            @endforeach
            @if($tournaments->hasMorePages())
                <a href="{{ $tournaments->nextPageUrl() }}&search={{ request('search') }}&status={{ request('status') }}&sport_id={{ request('sport_id') }}&per_page={{ request('per_page', 15) }}" style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#0f172a;border:0.5px solid rgba(15,23,42,0.1);background:#fff;text-decoration:none;">›</a>
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
        <div style="font-family:'Manrope',sans-serif;font-size:16px;font-weight:800;color:#0f172a;margin-bottom:8px;">Archive Tournament?</div>
        <div id="modal-body" style="font-size:13px;color:#64748b;line-height:1.6;margin-bottom:20px;"></div>

        <div style="display:flex;gap:10px;">
            <form id="archive-form" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger" style="padding:8px 16px;font-size:12px;">Archive Tournament</button>
            </form>
            <button type="button" class="btn-secondary" onclick="document.getElementById('archive-modal').style.display='none';">Cancel</button>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.archive-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var name  = this.dataset.name;
        var teams = this.dataset.teams;
        var url   = this.dataset.url;
        document.getElementById('modal-body').innerHTML =
            'You are about to archive <strong>' + name + '</strong>.<br>' +
            'This tournament has <strong>' + teams + ' registered team(s)</strong>.';
        document.getElementById('archive-form').action = url;
        document.getElementById('archive-modal').style.display = 'flex';
    });
});
</script>
@endsection
