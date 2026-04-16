@extends('layouts.admin')

@section('topbar-action')
    <a href="{{ route('admin.teams.index') }}" class="btn-secondary">← Back to Teams</a>
@endsection

@section('content')
<div style="max-width:560px;margin:0 auto;">
    <div class="page-header">
        <div class="page-title">Edit Team</div>
        <div class="page-subtitle">Update details for {{ $team->team_name }}.</div>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('admin.teams.update', $team) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Team Name</label>
                <input type="text" name="team_name" class="form-control"
                       value="{{ old('team_name', $team->team_name) }}" required>
                @error('team_name')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group">
                    <label class="form-label">Sport</label>
                    <select name="sport_id" class="form-control" required>
                        <option value="">Select sport</option>
                        @foreach($sports as $sport)
                            <option value="{{ $sport->id }}" {{ old('sport_id', $team->sport_id) == $sport->id ? 'selected' : '' }}>
                                {{ $sport->sport_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('sport_id')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Coach</label>
                    <select name="coach_id" class="form-control" required>
                        <option value="">Select coach</option>
                        @foreach($coaches as $coach)
                            <option value="{{ $coach->id }}" {{ old('coach_id', $team->coach_id) == $coach->id ? 'selected' : '' }}>
                                {{ $coach->first_name }} {{ $coach->last_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('coach_id')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Founded Date <span style="color:#94a3b8;font-weight:400;">(optional)</span></label>
                <input type="date" name="founded_at" class="form-control"
                       value="{{ old('founded_at', $team->founded_at?->format('Y-m-d')) }}">
                @error('founded_at')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Team Logo <span style="color:#94a3b8;font-weight:400;">(optional)</span></label>
                @if($team->logo_src)
                    <div style="margin-bottom:10px;">
                        <img src="{{ $team->logo_src }}" alt="{{ $team->team_name }} logo" style="width:56px;height:56px;object-fit:cover;border-radius:10px;border:1px solid rgba(15,23,42,0.08);background:#fff;">
                    </div>
                @endif
                <input type="file" name="logo" class="form-control" accept=".jpg,.jpeg,.png,.webp,.gif,image/*">
                <div style="font-size:11px;color:#94a3b8;margin-top:6px;">Upload a new PNG, JPG, WEBP, or GIF file up to 2MB to replace the current logo.</div>
                @error('logo')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="is_active" class="form-control">
                    <option value="1" {{ $team->is_active ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ !$team->is_active ? 'selected' : '' }}>Archived</option>
                </select>
            </div>

            <div style="display:flex;gap:10px;margin-top:8px;">
                <button type="submit" class="btn-primary">Update Team</button>
                <a href="{{ route('admin.teams.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
