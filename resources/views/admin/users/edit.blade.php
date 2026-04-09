@extends('layouts.admin')

@section('content')
<div class="page-header">
    <div class="breadcrumb">ADMIN <span>› USER MANAGEMENT › EDIT</span></div>
    <div class="page-title">Edit User</div>
    <div class="page-subtitle">Update account details for {{ $user->full_name }}.</div>
</div>

<div class="card" style="max-width:600px;">
    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label class="form-label">First Name</label>
            <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $user->first_name) }}" required>
            @error('first_name') <div class="form-error">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label class="form-label">Last Name</label>
            <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $user->last_name) }}" required>
            @error('last_name') <div class="form-error">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
            @error('email') <div class="form-error">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label class="form-label">Role</label>
            <select name="role" class="form-control" required>
                <option value="admin"     {{ $user->role === 'admin'     ? 'selected' : '' }}>Admin</option>
                <option value="organizer" {{ $user->role === 'organizer' ? 'selected' : '' }}>Organizer</option>
                <option value="coach"     {{ $user->role === 'coach'     ? 'selected' : '' }}>Coach</option>
                <option value="player"    {{ $user->role === 'player'    ? 'selected' : '' }}>Player</option>
                <option value="fan"       {{ $user->role === 'fan'       ? 'selected' : '' }}>Fan</option>
            </select>
            @error('role') <div class="form-error">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label class="form-label">Status</label>
            <select name="is_active" class="form-control">
                <option value="1" {{ $user->is_active ? 'selected' : '' }}>Active</option>
                <option value="0" {{ !$user->is_active ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">New Password <span style="color:#94a3b8;font-weight:400;">(leave blank to keep current)</span></label>
            <input type="password" name="password" class="form-control">
            @error('password') <div class="form-error">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label class="form-label">Confirm New Password</label>
            <input type="password" name="password_confirmation" class="form-control">
        </div>
        <div style="display:flex;gap:10px;margin-top:20px;">
            <button type="submit" class="btn-primary">Update User</button>
            <a href="{{ route('admin.users.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection