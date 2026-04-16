@extends('layouts.admin')

@section('topbar-action')
    <a href="{{ route('admin.matches.index') }}" class="btn-secondary">← Back to Matches</a>
@endsection

@section('content')
<div style="max-width:620px;margin:0 auto;">
    <div class="page-header">
        <div class="breadcrumb">ADMIN <span>› MATCH SCHEDULE › CREATE</span></div>
        <div class="page-title">Schedule Match</div>
        <div class="page-subtitle">Create a new match and assign participating teams.</div>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('admin.matches.store') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Tournament</label>
                <select name="tournament_id" class="form-control" required id="tournament-select">
                    <option value="">Select tournament</option>
                    @foreach($tournaments as $tournament)
                        <option value="{{ $tournament->id }}"
                                data-sport="{{ $tournament->sport_id }}"
                                {{ old('tournament_id') == $tournament->id ? 'selected' : '' }}>
                            {{ $tournament->tournament_name }} — {{ $tournament->sport->sport_name ?? '' }}
                        </option>
                    @endforeach
                </select>
                @error('tournament_id')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group">
                    <label class="form-label">Match Date</label>
                    <input type="date" name="match_date" class="form-control"
                           value="{{ old('match_date') }}" required>
                    @error('match_date')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Match Time <span style="color:#94a3b8;font-weight:400;">(optional)</span></label>
                    <input type="time" name="match_time" class="form-control"
                           value="{{ old('match_time') }}">
                    @error('match_time')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group">
                    <label class="form-label">Venue <span style="color:#94a3b8;font-weight:400;"></span></label>
                    <input type="text" name="venue" class="form-control"
                           value="{{ old('venue') }}" placeholder="e.g. Main Court A" required>
                    @error('venue')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Round / Stage <span style="color:#94a3b8;font-weight:400;"></span></label>
                    <input type="text" name="round_name" class="form-control"
                           value="{{ old('round_name') }}" placeholder="e.g. Quarter Finals" required>
                    @error('round_name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-control" required>
                    <option value="scheduled" {{ old('status', 'scheduled') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="live"      {{ old('status') === 'live'      ? 'selected' : '' }}>Live</option>
                    <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
                @error('status')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Participating Teams</label>
                <div style="border:1px solid rgba(15,23,42,0.12);border-radius:7px;padding:10px;max-height:200px;overflow-y:auto;">
                    @foreach($teams as $team)
                    <label style="display:flex;align-items:center;gap:8px;padding:5px 4px;cursor:pointer;font-size:13px;">
                        <input type="checkbox" name="team_ids[]" value="{{ $team->id }}"
                               {{ in_array($team->id, old('team_ids', [])) ? 'checked' : '' }}
                               style="width:14px;height:14px;accent-color:#22c55e;">
                        <span style="font-weight:500;">{{ $team->team_name }}</span>
                        <span class="badge badge-blue" style="margin-left:auto;">{{ $team->sport->sport_name ?? '' }}</span>
                    </label>
                    @endforeach
                </div>
                @error('team_ids')
                    <div class="form-error">{{ $message }}</div>
                @enderror
                <div style="font-size:11px;color:#94a3b8;margin-top:4px;">Select at least 2 teams. Teams should match the tournament's sport.</div>
            </div>

            <div style="display:flex;gap:10px;margin-top:8px;">
                <button type="submit" class="btn-primary">Schedule Match</button>
                <a href="{{ route('admin.matches.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
