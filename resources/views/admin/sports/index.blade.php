@extends('layouts.admin')

@section('topbar-action')
    <a href="{{ route('admin.sports.create') }}" class="btn-primary">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
        Add Sport
    </a>
@endsection

@section('content')
<div class="page-title" style="margin-bottom:20px;">Sports</div>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;gap:12px;">
    <form method="GET" action="{{ route('admin.sports.index') }}" style="display:flex;align-items:center;gap:8px;flex:1;max-width:360px;">
        <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
        <div style="position:relative;flex:1;">
            <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:#94a3b8;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search sports..."
                   style="width:100%;padding:8px 12px 8px 32px;border:1px solid rgba(15,23,42,0.12);border-radius:7px;font-size:13px;font-family:'Inter',sans-serif;color:#0f172a;background:#fff;outline:none;">
        </div>
        <button type="submit" class="btn-primary" style="padding:8px 14px;">Search</button>
        @if(request('search'))
            <a href="{{ route('admin.sports.index') }}" class="btn-secondary" style="padding:8px 14px;">Clear</a>
        @endif
    </form>

    <form method="GET" action="{{ route('admin.sports.index') }}" style="display:flex;align-items:center;gap:8px;">
        <input type="hidden" name="search" value="{{ request('search') }}">
        <label style="font-size:12px;color:#64748b;font-weight:500;">Show</label>
        <select name="per_page" onchange="this.form.submit()"
                style="padding:7px 10px;border:1px solid rgba(15,23,42,0.12);border-radius:7px;font-size:12px;font-family:'Inter',sans-serif;color:#0f172a;background:#fff;outline:none;cursor:pointer;">
            @foreach([5, 10, 15, 25] as $option)
                <option value="{{ $option }}" {{ request('per_page', 10) == $option ? 'selected' : '' }}>{{ $option }}</option>
            @endforeach
        </select>
        <label style="font-size:12px;color:#64748b;font-weight:500;">per page</label>
    </form>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Sport</th>
                    <th>Min Teams</th>
                    <th>Max Teams</th>
                    <th>Teams</th>
                    <th>Tournaments</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sports as $sport)
                <tr style="{{ !$sport->is_active ? 'opacity:0.55;background:#fafafa;' : '' }}">
                    <td>
                        <div style="font-weight:600;font-size:13px;">{{ $sport->sport_name }}</div>
                        @if($sport->description)
                            <div style="font-size:10px;color:#94a3b8;">{{ Str::limit($sport->description, 50) }}</div>
                        @endif
                    </td>
                    <td style="font-size:13px;">{{ $sport->min_teams_per_match }}</td>
                    <td style="font-size:13px;">{{ $sport->max_teams_per_match }}</td>
                    <td><span class="badge badge-blue">{{ $sport->teams_count }} teams</span></td>
                    <td><span class="badge badge-green">{{ $sport->tournaments_count }} tournaments</span></td>
                    <td>
                        @if($sport->is_active)
                            <span class="badge badge-green">ACTIVE</span>
                        @else
                            <span class="badge badge-gray">ARCHIVED</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;align-items:center;">
                            @if($sport->is_active)
                                <a href="{{ route('admin.sports.edit', $sport) }}"
                                   class="btn-secondary" style="padding:5px 10px;font-size:11px;">Edit</a>
                                <button type="button" class="btn-danger archive-btn"
                                    data-name="{{ $sport->sport_name }}"
                                    data-teams="{{ $sport->teams_count }}"
                                    data-tournaments="{{ $sport->tournaments_count }}"
                                    data-url="{{ route('admin.sports.destroy', $sport) }}">
                                    Archive
                                </button>
                            @else
                                <form method="POST" action="{{ route('admin.sports.restore', $sport) }}">
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
                        {{ request('search') ? 'No sports found matching "' . request('search') . '"' : 'No sports found.' }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($sports->hasPages())
    <div style="display:flex;align-items:center;justify-content:space-between;margin-top:16px;padding-top:12px;border-top:0.5px solid rgba(15,23,42,0.06);">
        <div style="font-size:12px;color:#94a3b8;">
            Showing {{ $sports->firstItem() }} to {{ $sports->lastItem() }} of {{ $sports->total() }} results
        </div>
        <div style="display:flex;gap:4px;">
            @if($sports->onFirstPage())
                <span style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#d1d5db;border:0.5px solid rgba(15,23,42,0.1);background:#fff;">‹</span>
            @else
                <a href="{{ $sports->previousPageUrl() }}&search={{ request('search') }}&per_page={{ request('per_page', 10) }}" style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#0f172a;border:0.5px solid rgba(15,23,42,0.1);background:#fff;text-decoration:none;">‹</a>
            @endif
            @foreach($sports->getUrlRange(1, $sports->lastPage()) as $page => $url)
                @if($page == $sports->currentPage())
                    <span style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#fff;background:#1a2233;border:0.5px solid #1a2233;">{{ $page }}</span>
                @else
                    <a href="{{ $url }}&search={{ request('search') }}&per_page={{ request('per_page', 10) }}" style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#0f172a;border:0.5px solid rgba(15,23,42,0.1);background:#fff;text-decoration:none;">{{ $page }}</a>
                @endif
            @endforeach
            @if($sports->hasMorePages())
                <a href="{{ $sports->nextPageUrl() }}&search={{ request('search') }}&per_page={{ request('per_page', 10) }}" style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#0f172a;border:0.5px solid rgba(15,23,42,0.1);background:#fff;text-decoration:none;">›</a>
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
            Archive Sport?
        </div>
        <div id="modal-body" style="font-size:13px;color:#64748b;line-height:1.6;margin-bottom:20px;"></div>
        <div style="background:#fef3c7;border-left:3px solid #f59e0b;padding:10px 12px;border-radius:6px;font-size:12px;color:#78350f;margin-bottom:20px;">
            This sport will be hidden from active use but all data is preserved. You can restore it anytime.
        </div>
        <div style="display:flex;gap:10px;">
            <form id="archive-form" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit"
                    style="background:#fee2e2;color:#991b1b;border:none;border-radius:7px;padding:9px 18px;font-size:13px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif;">
                    Yes, Archive
                </button>
            </form>
            <button type="button" onclick="closeModal()" class="btn-secondary">Cancel</button>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.archive-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var name        = this.dataset.name;
        var teams       = this.dataset.teams;
        var tournaments = this.dataset.tournaments;
        var url         = this.dataset.url;

        document.getElementById('modal-body').innerHTML =
            'You are about to archive <strong>' + name + '</strong>.<br><br>' +
            'This sport currently has:<br>' +
            '&nbsp;&nbsp;• <strong>' + teams + '</strong> team(s) attached<br>' +
            '&nbsp;&nbsp;• <strong>' + tournaments + '</strong> tournament(s) attached<br><br>' +
            'These will remain in the database but the sport will be marked as archived.';

        document.getElementById('archive-form').action = url;
        document.getElementById('archive-modal').style.display = 'flex';
    });
});

function closeModal() {
    document.getElementById('archive-modal').style.display = 'none';
}
</script>
@endsection

{empty-line}