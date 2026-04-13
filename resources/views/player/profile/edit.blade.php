@extends('layouts.player')

@section('content')
<div style="max-width:520px;margin:0 auto;">
    <div class="page-header">
        <div class="breadcrumb">PLAYER <span>› PROFILE</span></div>
        <div class="page-title">My Profile</div>
        <div class="page-subtitle">Update your personal information and password.</div>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('player.profile.update') }}">
            @csrf @method('PUT')

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

            <div style="border-top:0.5px solid rgba(15,23,42,0.08);margin:16px 0;padding-top:16px;">
                <div style="font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#94a3b8;margin-bottom:12px;">Change Password</div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div class="form-group">
                        <label class="form-label">New Password <span style="color:#94a3b8;font-weight:400;">(optional)</span></label>
                        <input type="password" name="password" class="form-control">
                        @error('password') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                </div>
            </div>

            <div style="display:flex;gap:10px;margin-top:4px;">
                <button type="submit" class="btn-primary">Save Changes</button>
            </div>
        </form>
    </div>

    <div class="card" style="background:#f8faff;">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;margin-bottom:10px;">Account Info</div>
        <div style="display:flex;flex-direction:column;gap:8px;">
            <div style="display:flex;justify-content:space-between;">
                <span style="font-size:12px;color:#64748b;">Role</span>
                <span class="badge badge-purple">PLAYER</span>
            </div>
            <div style="display:flex;justify-content:space-between;">
                <span style="font-size:12px;color:#64748b;">Member since</span>
                <span style="font-size:12px;font-weight:500;">{{ $user->created_at->format('M d, Y') }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;">
                <span style="font-size:12px;color:#64748b;">Status</span>
                @if($user->is_active) <span class="badge badge-green">ACTIVE</span>
                @else <span class="badge badge-gray">INACTIVE</span> @endif
            </div>
        </div>
    </div>
</div>
@endsection
