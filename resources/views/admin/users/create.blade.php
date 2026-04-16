@extends('layouts.admin')

@section('search-placeholder', 'Search users by name or email...')

@section('topbar-action')
    <a href="{{ route('admin.users.index') }}" class="btn-secondary">
        ← Back to Users
    </a>
@endsection

@section('content')
<div style="max-width:560px;margin:0 auto;">
    <div class="page-header">
        <div class="page-title">Add User</div>
        <div class="page-subtitle">Create a new user account and assign a role.</div>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control"
                           value="{{ old('first_name') }}" required autofocus>
                    @error('first_name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control"
                           value="{{ old('last_name') }}" required>
                    @error('last_name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control"
                       value="{{ old('email') }}" required>
                @error('email')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Role</label>
                <select name="role" class="form-control" required>
                    <option value="">Select role</option>
                    <option value="admin"     {{ old('role') === 'admin'     ? 'selected' : '' }}>Admin</option>
                    <option value="organizer" {{ old('role') === 'organizer' ? 'selected' : '' }}>Organizer</option>
                    <option value="coach"     {{ old('role') === 'coach'     ? 'selected' : '' }}>Coach</option>
                    <option value="player"    {{ old('role') === 'player'    ? 'selected' : '' }}>Player</option>
                    <option value="fan"       {{ old('role') === 'fan'       ? 'selected' : '' }}>Fan</option>
                </select>
                @error('role')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                    @error('password')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
            </div>

            <div style="display:flex;gap:10px;margin-top:8px;">
                <button type="submit" class="btn-primary">Create User</button>
                <a href="{{ route('admin.users.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection