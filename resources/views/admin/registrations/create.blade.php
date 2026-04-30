@extends('layouts.admin')

@section('topbar-action')
    <a href="{{ route('admin.registrations.index') }}" class="btn-secondary">← Back to Registrations</a>
@endsection

@section('content')
<div style="max-width:560px;margin:0 auto;">

    <div class="page-header">
        <div class="page-title">Register a Team</div>
        <div class="page-subtitle">Enroll a team into a tournament. Registration is approved immediately.</div>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('admin.registrations.store') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Team</label>
                <select name="team_id" class="form-control" required>
                    <option value="">Select team</option>
                    @foreach($teams as $team)
                        <option value="{{ $team->id }}" {{ old('team_id') == $team->id ? 'selected' : '' }}>
                            {{ $team->team_name }} ({{ $team->sport->sport_name ?? '?' }})
                        </option>
                    @endforeach
                </select>
                @error('team_id')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Tournament</label>
                <select name="tournament_id" class="form-control" required>
                    <option value="">Select tournament</option>
                    @foreach($tournaments as $t)
                        <option value="{{ $t->id }}" {{ old('tournament_id') == $t->id ? 'selected' : '' }}>
                            {{ $t->tournament_name }} ({{ $t->sport->sport_name ?? '?' }})
                        </option>
                    @endforeach
                </select>
                @error('tournament_id')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Registration Date</label>
                <input type="date" name="registration_date" class="form-control"
                       value="{{ old('registration_date', today()->format('Y-m-d')) }}" required>
                @error('registration_date')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Notes <span style="color:#94a3b8;font-weight:400;">(optional)</span></label>
                <input type="text" name="notes" class="form-control"
                       value="{{ old('notes') }}" placeholder="Any additional notes...">
                @error('notes')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div style="display:flex;gap:10px;margin-top:8px;">
                <button type="submit" class="btn-primary">Register Team</button>
                <a href="{{ route('admin.registrations.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

</div>
@endsection