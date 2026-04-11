@extends('layouts.admin')

@section('topbar-action')
    <a href="{{ route('admin.users.create') }}" class="btn-primary">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
        Add User
    </a>
@endsection

@section('content')
<div class="page-header">
    <div class="page-title">User Management</div>
</div>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;gap:12px;">
    <form method="GET" action="{{ route('admin.users.index') }}" style="display:flex;align-items:center;gap:8px;flex:1;max-width:360px;">
        <input type="hidden" name="per_page" value="{{ request('per_page', 15) }}">
        <div style="position:relative;flex:1;">
            <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:#94a3b8;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search users..."
                   style="width:100%;padding:8px 12px 8px 32px;border:1px solid rgba(15,23,42,0.12);border-radius:7px;font-size:13px;font-family:'Inter',sans-serif;color:#0f172a;background:#fff;outline:none;">
        </div>
        <button type="submit" class="btn-primary" style="padding:8px 14px;">Search</button>
        @if(request('search'))
            <a href="{{ route('admin.users.index') }}" class="btn-secondary" style="padding:8px 14px;">Clear</a>
        @endif
    </form>

    <form method="GET" action="{{ route('admin.users.index') }}" style="display:flex;align-items:center;gap:8px;">
        <input type="hidden" name="search" value="{{ request('search') }}">
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

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr style="{{ !$user->is_active ? 'opacity:0.55;background:#fafafa;' : '' }}">
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div class="avatar">{{ strtoupper(substr($user->first_name, 0, 1)) }}</div>
                            <div>
                                <div style="font-weight:600;font-size:13px;">{{ $user->full_name }}</div>
                                <div style="font-size:10px;color:#94a3b8;">#EO-{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:12px;color:#64748b;">{{ $user->email }}</td>
                    <td>
                        @if($user->role === 'admin')
                            <span class="badge badge-red">ADMIN</span>
                        @elseif($user->role === 'organizer')
                            <span class="badge badge-blue">ORGANIZER</span>
                        @elseif($user->role === 'coach')
                            <span class="badge badge-amber">COACH</span>
                        @elseif($user->role === 'player')
                            <span class="badge badge-green">PLAYER</span>
                        @else
                            <span class="badge badge-gray">FAN</span>
                        @endif
                    </td>
                    <td>
                        @if($user->is_active)
                            <span class="badge badge-green">ACTIVE</span>
                        @else
                            <span class="badge badge-gray">ARCHIVED</span>
                        @endif
                    </td>
                    <td style="font-size:11px;color:#94a3b8;">{{ $user->created_at->format('M d, Y') }}</td>
                    <td>
                        <div style="display:flex;gap:6px;align-items:center;">
                            @if($user->is_active)
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="btn-secondary" style="padding:5px 10px;font-size:11px;">Edit</a>
                                @if($user->id !== auth()->id())
                                    <button type="button" class="btn-danger"
                                        onclick="confirmArchive(
                                            '{{ addslashes($user->full_name) }}',
                                            '{{ route('admin.users.destroy', $user) }}'
                                        )">
                                        Archive
                                    </button>
                                @endif
                            @else
                                <form method="POST" action="{{ route('admin.users.restore', $user) }}">
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
                    <td colspan="6" style="text-align:center;color:#94a3b8;padding:30px;">
                        {{ request('search') ? 'No users found matching "' . request('search') . '"' : 'No users found.' }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <div style="display:flex;align-items:center;justify-content:space-between;margin-top:16px;padding-top:12px;border-top:0.5px solid rgba(15,23,42,0.06);">
        <div style="font-size:12px;color:#94a3b8;">
            Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} results
        </div>
        <div style="display:flex;gap:4px;">
            @if($users->onFirstPage())
                <span style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#d1d5db;border:0.5px solid rgba(15,23,42,0.1);background:#fff;">‹</span>
            @else
                <a href="{{ $users->previousPageUrl() }}&search={{ request('search') }}&per_page={{ request('per_page', 15) }}" style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#0f172a;border:0.5px solid rgba(15,23,42,0.1);background:#fff;text-decoration:none;">‹</a>
            @endif
            @foreach($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                @if($page == $users->currentPage())
                    <span style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#fff;background:#1a2233;border:0.5px solid #1a2233;">{{ $page }}</span>
                @else
                    <a href="{{ $url }}&search={{ request('search') }}&per_page={{ request('per_page', 15) }}" style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#0f172a;border:0.5px solid rgba(15,23,42,0.1);background:#fff;text-decoration:none;">{{ $page }}</a>
                @endif
            @endforeach
            @if($users->hasMorePages())
                <a href="{{ $users->nextPageUrl() }}&search={{ request('search') }}&per_page={{ request('per_page', 15) }}" style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#0f172a;border:0.5px solid rgba(15,23,42,0.1);background:#fff;text-decoration:none;">›</a>
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
            Archive User?
        </div>
        <div id="modal-body" style="font-size:13px;color:#64748b;line-height:1.6;margin-bottom:20px;"></div>
        <div style="background:#fef3c7;border-left:3px solid #f59e0b;padding:10px 12px;border-radius:6px;font-size:12px;color:#78350f;margin-bottom:20px;">
            This user will be deactivated and unable to log in. All their data is preserved. You can restore them anytime.
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
function confirmArchive(name, url) {
    document.getElementById('modal-body').innerHTML =
        `You are about to archive <strong>${name}</strong>.<br><br>` +
        `This user will be deactivated and unable to log in. All their data remains intact.`;
    document.getElementById('archive-form').action = url;
    document.getElementById('archive-modal').style.display = 'flex';
}
function closeModal() {
    document.getElementById('archive-modal').style.display = 'none';
}
</script>
@endsection