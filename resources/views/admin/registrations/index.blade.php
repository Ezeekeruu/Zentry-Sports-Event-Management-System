@extends('layouts.admin')

@section('content')
<div class="page-title" style="margin-bottom:20px;">Registrations</div>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;gap:12px;flex-wrap:wrap;">
    <form method="GET" action="{{ route('admin.registrations.index') }}" style="display:flex;align-items:center;gap:8px;flex:1;max-width:460px;">
        <input type="hidden" name="per_page"      value="{{ request('per_page', 20) }}">
        <input type="hidden" name="tournament_id" value="{{ request('tournament_id') }}">
        <div style="position:relative;flex:1;">
            <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:#94a3b8;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search team or tournament..."
                   style="width:100%;padding:8px 12px 8px 32px;border:1px solid rgba(15,23,42,0.12);border-radius:7px;font-size:13px;font-family:'Inter',sans-serif;color:#0f172a;background:#fff;outline:none;">
        </div>
        <button type="submit" class="btn-primary" style="padding:8px 14px;">Search</button>
        @if(request('search') || request('status') || request('tournament_id'))
            <a href="{{ route('admin.registrations.index') }}" class="btn-secondary" style="padding:8px 14px;">Clear</a>
        @endif
    </form>

    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <form method="GET" action="{{ route('admin.registrations.index') }}" style="display:flex;gap:8px;">
            <input type="hidden" name="search"  value="{{ request('search') }}">
            <input type="hidden" name="per_page" value="{{ request('per_page', 20) }}">
            <input type="hidden" name="tournament_id" value="{{ request('tournament_id') }}">
            <select name="status" onchange="this.form.submit()"
                    style="padding:7px 10px;border:1px solid rgba(15,23,42,0.12);border-radius:7px;font-size:12px;font-family:'Inter',sans-serif;color:#0f172a;background:#fff;outline:none;cursor:pointer;">
                <option value="">All Statuses</option>
                <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </form>

        <form method="GET" action="{{ route('admin.registrations.index') }}" style="display:flex;align-items:center;gap:8px;">
            <input type="hidden" name="search"  value="{{ request('search') }}">
            <input type="hidden" name="status"  value="{{ request('status') }}">
            <input type="hidden" name="tournament_id" value="{{ request('tournament_id') }}">
            <label style="font-size:12px;color:#64748b;font-weight:500;">Show</label>
            <select name="per_page" onchange="this.form.submit()"
                    style="padding:7px 10px;border:1px solid rgba(15,23,42,0.12);border-radius:7px;font-size:12px;font-family:'Inter',sans-serif;color:#0f172a;background:#fff;outline:none;cursor:pointer;">
                @foreach([10, 20, 30, 50] as $option)
                    <option value="{{ $option }}" {{ request('per_page', 20) == $option ? 'selected' : '' }}>{{ $option }}</option>
                @endforeach
            </select>
            <label style="font-size:12px;color:#64748b;font-weight:500;">per page</label>
        </form>
    </div>
</div>

{{-- Quick-register panel --}}
<details style="margin-bottom:16px;">
    <summary style="cursor:pointer;font-size:12px;font-weight:700;color:#22c55e;letter-spacing:.04em;user-select:none;list-style:none;display:flex;align-items:center;gap:6px;">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
        Register a Team
    </summary>
    <div class="card" style="margin-top:10px;">
        <form method="POST" action="{{ route('admin.registrations.store') }}">
            @csrf
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr auto;gap:12px;align-items:end;">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Team</label>
                    <select name="team_id" class="form-control" required>
                        <option value="">Select team</option>
                        @foreach(\App\Models\Team::active()->with('sport')->orderBy('team_name')->get() as $team)
                            <option value="{{ $team->id }}" {{ old('team_id') == $team->id ? 'selected' : '' }}>
                                {{ $team->team_name }} ({{ $team->sport->sport_name ?? '?' }})
                            </option>
                        @endforeach
                    </select>
                    @error('team_id') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Tournament</label>
                    <select name="tournament_id" class="form-control" required>
                        <option value="">Select tournament</option>
                        @foreach(\App\Models\Tournament::whereIn('status',['upcoming','ongoing'])->with('sport')->orderBy('tournament_name')->get() as $t)
                            <option value="{{ $t->id }}" {{ old('tournament_id') == $t->id ? 'selected' : '' }}>
                                {{ $t->tournament_name }} ({{ $t->sport->sport_name ?? '?' }})
                            </option>
                        @endforeach
                    </select>
                    @error('tournament_id') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Registration Date</label>
                    <input type="date" name="registration_date" class="form-control"
                           value="{{ old('registration_date', today()->format('Y-m-d')) }}" required>
                    @error('registration_date') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div>
                    <button type="submit" class="btn-primary" style="padding:9px 16px;">Register</button>
                </div>
            </div>
            <div class="form-group" style="margin-top:10px;margin-bottom:0;">
                <label class="form-label">Notes <span style="color:#94a3b8;font-weight:400;">(optional)</span></label>
                <input type="text" name="notes" class="form-control"
                       value="{{ old('notes') }}" placeholder="Any additional notes...">
            </div>
        </form>
    </div>
</details>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Team</th>
                    <th>Sport</th>
                    <th>Tournament</th>
                    <th>Organizer</th>
                    <th>Reg. Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($registrations as $reg)
                <tr>
                    <td>
                        <div style="font-weight:600;font-size:13px;">{{ $reg->team->team_name ?? '—' }}</div>
                    </td>
                    <td>
                        <span class="badge badge-blue">{{ $reg->team->sport->sport_name ?? '—' }}</span>
                    </td>
                    <td style="font-size:12px;font-weight:500;">{{ Str::limit($reg->tournament->tournament_name ?? '—', 28) }}</td>
                    <td style="font-size:12px;color:#64748b;">
                        {{ $reg->tournament->organizer->first_name ?? '—' }}
                        {{ $reg->tournament->organizer->last_name ?? '' }}
                    </td>
                    <td style="font-size:11px;color:#64748b;">
                        {{ $reg->registration_date?->format('M d, Y') ?? '—' }}
                    </td>
                    <td>
                        @if($reg->status === 'approved')
                            <span class="badge badge-green">APPROVED</span>
                        @elseif($reg->status === 'pending')
                            <span class="badge badge-amber">PENDING</span>
                        @else
                            <span class="badge badge-red">REJECTED</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;align-items:center;">
                            @if($reg->status === 'pending')
                                <form method="POST" action="{{ route('admin.registrations.approve', $reg) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        style="background:#dcfce7;color:#14532d;border:none;border-radius:7px;padding:5px 10px;font-size:11px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif;">
                                        Approve
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.registrations.reject', $reg) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn-danger" style="padding:5px 10px;font-size:11px;">
                                        Reject
                                    </button>
                                </form>
                            @elseif($reg->status === 'approved')
                                <form method="POST" action="{{ route('admin.registrations.reject', $reg) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn-danger" style="padding:5px 10px;font-size:11px;">
                                        Revoke
                                    </button>
                                </form>
                            @elseif($reg->status === 'rejected')
                                <form method="POST" action="{{ route('admin.registrations.approve', $reg) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        style="background:#dcfce7;color:#14532d;border:none;border-radius:7px;padding:5px 10px;font-size:11px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif;">
                                        Re-approve
                                    </button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('admin.registrations.destroy', $reg) }}"
                                  onsubmit="return confirm('Permanently delete this registration?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    style="background:transparent;color:#94a3b8;border:none;font-size:11px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif;padding:5px 6px;">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;color:#94a3b8;padding:30px;">
                        No registrations found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($registrations->hasPages())
    <div style="display:flex;align-items:center;justify-content:space-between;margin-top:16px;padding-top:12px;border-top:0.5px solid rgba(15,23,42,0.06);">
        <div style="font-size:12px;color:#94a3b8;">
            Showing {{ $registrations->firstItem() }} to {{ $registrations->lastItem() }} of {{ $registrations->total() }} results
        </div>
        <div style="display:flex;gap:4px;">
            @if($registrations->onFirstPage())
                <span style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#d1d5db;border:0.5px solid rgba(15,23,42,0.1);background:#fff;">‹</span>
            @else
                <a href="{{ $registrations->previousPageUrl() }}&search={{ request('search') }}&status={{ request('status') }}&tournament_id={{ request('tournament_id') }}&per_page={{ request('per_page', 20) }}" style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#0f172a;border:0.5px solid rgba(15,23,42,0.1);background:#fff;text-decoration:none;">‹</a>
            @endif
            @foreach($registrations->getUrlRange(1, $registrations->lastPage()) as $page => $url)
                @if($page == $registrations->currentPage())
                    <span style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#fff;background:#1a2233;border:0.5px solid #1a2233;">{{ $page }}</span>
                @else
                    <a href="{{ $url }}&search={{ request('search') }}&status={{ request('status') }}&tournament_id={{ request('tournament_id') }}&per_page={{ request('per_page', 20) }}" style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#0f172a;border:0.5px solid rgba(15,23,42,0.1);background:#fff;text-decoration:none;">{{ $page }}</a>
                @endif
            @endforeach
            @if($registrations->hasMorePages())
                <a href="{{ $registrations->nextPageUrl() }}&search={{ request('search') }}&status={{ request('status') }}&tournament_id={{ request('tournament_id') }}&per_page={{ request('per_page', 20) }}" style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#0f172a;border:0.5px solid rgba(15,23,42,0.1);background:#fff;text-decoration:none;">›</a>
            @else
                <span style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#d1d5db;border:0.5px solid rgba(15,23,42,0.1);background:#fff;">›</span>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
