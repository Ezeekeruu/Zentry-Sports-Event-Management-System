@extends('layouts.admin')

@section('topbar-action')
    <a href="{{ route('admin.tournaments.index') }}" class="btn-secondary">← Back to Tournaments</a>
@endsection

@section('content')
<div style="max-width:600px;margin:0 auto;">
    <div class="page-header">
        <div class="breadcrumb">ADMIN <span>› TOURNAMENTS › EDIT</span></div>
        <div class="page-title">Edit Tournament</div>
        <div class="page-subtitle">Update details for {{ $tournament->tournament_name }}.</div>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('admin.tournaments.update', $tournament) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Tournament Name</label>
                <input type="text" name="tournament_name" class="form-control"
                       value="{{ old('tournament_name', $tournament->tournament_name) }}" required>
                @error('tournament_name')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group">
                    <label class="form-label">Sport</label>
                    <select name="sport_id" class="form-control" required>
                        <option value="">Select sport</option>
                        @foreach($sports as $sport)
                            <option value="{{ $sport->id }}" {{ old('sport_id', $tournament->sport_id) == $sport->id ? 'selected' : '' }}>
                                {{ $sport->sport_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('sport_id')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Organizer</label>
                    <select name="organizer_id" class="form-control" required>
                        <option value="">Select organizer</option>
                        @foreach($organizers as $organizer)
                            <option value="{{ $organizer->id }}" {{ old('organizer_id', $tournament->organizer_id) == $organizer->id ? 'selected' : '' }}>
                                {{ $organizer->first_name }} {{ $organizer->last_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('organizer_id')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control"
                           value="{{ old('start_date', $tournament->start_date->format('Y-m-d')) }}" required>
                    @error('start_date')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control"
                           value="{{ old('end_date', $tournament->end_date->format('Y-m-d')) }}" required>
                    @error('end_date')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control" required>
                        <option value="upcoming"  {{ old('status', $tournament->status) === 'upcoming'  ? 'selected' : '' }}>Upcoming</option>
                        <option value="ongoing"   {{ old('status', $tournament->status) === 'ongoing'   ? 'selected' : '' }}>Ongoing</option>
                        <option value="completed" {{ old('status', $tournament->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                    @error('status')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Max Teams</label>
                    <input type="number" name="max_teams" class="form-control"
                           value="{{ old('max_teams', $tournament->max_teams) }}" min="2" required>
                    @error('max_teams')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Active Status</label>
                    <select name="is_active" class="form-control">
                        <option value="1" {{ $tournament->is_active ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !$tournament->is_active ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
            </div>

            <div style="display:flex;gap:10px;margin-top:8px;">
                <button type="submit" class="btn-primary">Update Tournament</button>
                <a href="{{ route('admin.tournaments.show', $tournament) }}" class="btn-secondary">View</a>
                <a href="{{ route('admin.tournaments.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
