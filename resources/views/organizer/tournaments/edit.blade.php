@extends('layouts.organizer')

@section('topbar-action')
    <a href="{{ route('organizer.tournaments.show', $tournament) }}" class="btn-secondary">← Back to Tournament</a>
@endsection

@section('content')
<div style="max-width:580px;margin:0 auto;">
    <div class="page-header">
        <div class="breadcrumb">ORGANIZER <span>› TOURNAMENTS › EDIT</span></div>
        <div class="page-title">Edit Tournament</div>
    </div>
    <div class="card">
        <form method="POST" action="{{ route('organizer.tournaments.update', $tournament) }}">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Tournament Name</label>
                <input type="text" name="tournament_name" class="form-control" value="{{ old('tournament_name',$tournament->tournament_name) }}" required>
                @error('tournament_name') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Sport</label>
                <select name="sport_id" class="form-control" required>
                    @foreach($sports as $sport)
                        <option value="{{ $sport->id }}" {{ old('sport_id',$tournament->sport_id)==$sport->id?'selected':'' }}>{{ $sport->sport_name }}</option>
                    @endforeach
                </select>
                @error('sport_id') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date',$tournament->start_date->format('Y-m-d')) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ old('end_date',$tournament->end_date->format('Y-m-d')) }}" required>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control" required>
                        <option value="upcoming"  {{ old('status',$tournament->status)==='upcoming'?'selected':'' }}>Upcoming</option>
                        <option value="ongoing"   {{ old('status',$tournament->status)==='ongoing'?'selected':'' }}>Ongoing</option>
                        <option value="completed" {{ old('status',$tournament->status)==='completed'?'selected':'' }}>Completed</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Max Teams</label>
                    <input type="number" name="max_teams" class="form-control" value="{{ old('max_teams',$tournament->max_teams) }}" min="2">
                </div>
            </div>
            <div style="display:flex;gap:10px;margin-top:8px;">
                <button type="submit" class="btn-primary">Update Tournament</button>
                <a href="{{ route('organizer.tournaments.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
