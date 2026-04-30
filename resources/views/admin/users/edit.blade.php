@extends('layouts.admin')

@section('content')
<div style="display:flex;align-items:center;justify-content:center;min-height:80vh;">
<div style="width:100%;max-width:560px;">

    <div class="page-header">
        <div class="page-title">Edit User</div>
        <div class="page-subtitle">Update account details for {{ $user->full_name }}.</div>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control"
                           value="{{ old('first_name', $user->first_name) }}" required>
                    @error('first_name') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control"
                           value="{{ old('last_name', $user->last_name) }}" required>
                    @error('last_name') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control"
                       value="{{ old('email', $user->email) }}" required>
                @error('email') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Role</label>
                <select name="role" class="form-control" required id="roleSelect" onchange="toggleSportField()">
                    <option value="admin"     {{ $user->role === 'admin'     ? 'selected' : '' }}>Admin</option>
                    <option value="organizer" {{ $user->role === 'organizer' ? 'selected' : '' }}>Organizer</option>
                    <option value="coach"     {{ $user->role === 'coach'     ? 'selected' : '' }}>Coach</option>
                    <option value="player"    {{ $user->role === 'player'    ? 'selected' : '' }}>Player</option>
                    <option value="fan"       {{ $user->role === 'fan'       ? 'selected' : '' }}>Fan</option>
                </select>
                @error('role') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group" id="sportField" style="display:{{ $user->role === 'player' ? 'block' : 'none' }};">
                <label class="form-label">
                    Sport
                    <span style="color:#a855f7;font-size:11px;font-weight:600;margin-left:4px;">● Player Sport</span>
                </label>
                <select name="sport_id" class="form-control" id="sportSelect">
                    <option value="">— Select sport —</option>
                    @foreach($sports as $sport)
                        @php $currentSportId = old('sport_id', $user->playerProfile?->sport_id); @endphp
                        <option value="{{ $sport->id }}" {{ $currentSportId == $sport->id ? 'selected' : '' }}>
                            {{ $sport->sport_name }}
                        </option>
                    @endforeach
                </select>
                <div style="font-size:11px;color:#64748b;margin-top:4px;">
                    Specifies which sport this player competes in. Required to be added to a team roster.
                </div>
                @error('sport_id') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="is_active" class="form-control">
                    <option value="1" {{ $user->is_active ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ !$user->is_active ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group">
                    <label class="form-label">New Password <span style="color:#94a3b8;font-weight:400;">(leave blank to keep current)</span></label>
                    <input type="password" name="password" class="form-control">
                    @error('password') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
            </div>

            <div style="display:flex;gap:10px;margin-top:8px;">
                <button type="submit" class="btn-primary">Update User</button>
                <a href="{{ route('admin.users.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

</div>
</div>

<script>
function toggleSportField() {
    const role = document.getElementById('roleSelect').value;
    const field = document.getElementById('sportField');
    const select = document.getElementById('sportSelect');
    if (role === 'player') {
        field.style.display = 'block';
    } else {
        field.style.display = 'none';
        select.value = '';
    }
}
</script>
@endsection