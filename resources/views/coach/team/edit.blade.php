@extends('layouts.coach')

@section('topbar-action')
    <a href="{{ route('coach.team.show') }}" class="btn-secondary">← Back to Team</a>
@endsection

@section('content')
<div style="max-width:520px;margin:0 auto;">
    <div class="page-header">
        <div class="breadcrumb">COACH <span>› MY TEAM › EDIT</span></div>
        <div class="page-title">Edit Team</div>
        <div class="page-subtitle">Update basic team information.</div>
    </div>
    <div class="card">
        <form method="POST" action="{{ route('coach.team.update') }}">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Team Name</label>
                <input type="text" name="team_name" class="form-control"
                       value="{{ old('team_name',$team->team_name) }}" required>
                @error('team_name') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Founded Date <span style="color:#94a3b8;font-weight:400;">(optional)</span></label>
                <input type="date" name="founded_at" class="form-control"
                       value="{{ old('founded_at',$team->founded_at?->format('Y-m-d')) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Logo URL <span style="color:#94a3b8;font-weight:400;">(optional)</span></label>
                <input type="url" name="logo_url" class="form-control"
                       value="{{ old('logo_url',$team->logo_url) }}" placeholder="https://...">
            </div>
            <div style="display:flex;gap:10px;margin-top:8px;">
                <button type="submit" class="btn-primary">Save Changes</button>
                <a href="{{ route('coach.team.show') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
