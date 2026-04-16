@extends('layouts.organizer')

@section('topbar-action')
    <a href="{{ route('organizer.matches.index') }}" class="btn-secondary">← Back</a>
@endsection

@section('content')
<div style="max-width:600px;margin:0 auto;">
    <div class="page-header">
        <div class="breadcrumb">ORGANIZER <span>› MATCHES › EDIT</span></div>
        <div class="page-title">Edit Match</div>
    </div>
    <div class="card">
        <form method="POST" action="{{ route('organizer.matches.update', $match) }}">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Tournament</label>
                <select name="tournament_id" class="form-control" required>
                    @foreach($tournaments as $t)
                        <option value="{{ $t->id }}" {{ old('tournament_id',$match->tournament_id)==$t->id?'selected':'' }}>{{ $t->tournament_name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group">
                    <label class="form-label">Date</label>
                    <input type="date" name="match_date" class="form-control" value="{{ old('match_date',$match->match_date->format('Y-m-d')) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Time</label>
                    <input type="time" name="match_time" class="form-control" value="{{ old('match_time',$match->match_time) }}">
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group">
                    <label class="form-label">Venue</label>
                    <input type="text" name="venue" class="form-control" value="{{ old('venue',$match->venue) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Round</label>
                    <input type="text" name="round_name" class="form-control" value="{{ old('round_name',$match->round_name) }}" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-control" required>
                    <option value="scheduled" {{ old('status',$match->status)==='scheduled'?'selected':'' }}>Scheduled</option>
                    <option value="live"      {{ old('status',$match->status)==='live'?'selected':'' }}>Live</option>
                    <option value="completed" {{ old('status',$match->status)==='completed'?'selected':'' }}>Completed</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Teams</label>
                <div style="border:1px solid rgba(15,23,42,0.12);border-radius:7px;padding:10px;max-height:200px;overflow-y:auto;">
                    @foreach($teams as $team)
                    <label style="display:flex;align-items:center;gap:8px;padding:5px 4px;cursor:pointer;font-size:13px;">
                        <input type="checkbox" name="team_ids[]" value="{{ $team->id }}"
                               {{ in_array($team->id, old('team_ids',$selectedTeamIds)) ? 'checked' : '' }}
                               style="width:14px;height:14px;accent-color:#3b82f6;">
                        <span style="font-weight:500;">{{ $team->team_name }}</span>
                        <span class="badge badge-blue" style="margin-left:auto;">{{ $team->sport->sport_name??'' }}</span>
                    </label>
                    @endforeach
                </div>
                @error('team_ids') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <div style="display:flex;gap:10px;margin-top:8px;">
                <button type="submit" class="btn-primary">Update Match</button>
                <a href="{{ route('organizer.matches.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
