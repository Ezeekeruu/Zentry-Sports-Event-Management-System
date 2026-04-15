@extends('layouts.admin')

@section('topbar-action')
    <a href="{{ route('admin.tournaments.index') }}" class="btn-secondary">← Back to Tournaments</a>
@endsection

@section('content')
<div style="display:flex;align-items:center;justify-content:center;min-height:80vh;">
<div style="width:100%;max-width:660px;">

    <div class="page-header">
        <div class="breadcrumb">ADMIN <span>› TOURNAMENTS › CREATE</span></div>
        <div class="page-title">Add Tournament</div>
        <div class="page-subtitle">Create a new tournament and assign an organizer.</div>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('admin.tournaments.store') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Tournament Name</label>
                <input type="text" name="tournament_name" class="form-control"
                       value="{{ old('tournament_name') }}" required autofocus
                       placeholder="e.g. City Basketball Championship 2025">
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
                            <option value="{{ $sport->id }}" {{ old('sport_id') == $sport->id ? 'selected' : '' }}>
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
                            <option value="{{ $organizer->id }}" {{ old('organizer_id') == $organizer->id ? 'selected' : '' }}>
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
                           value="{{ old('start_date') }}" required
                           min="{{ date('Y-m-d') }}">
                    @error('start_date')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control"
                           value="{{ old('end_date') }}" required
                           min="{{ date('Y-m-d') }}">
                    @error('end_date')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control" required>
                        <option value="upcoming"  {{ old('status', 'upcoming') === 'upcoming'  ? 'selected' : '' }}>Upcoming</option>
                        <option value="ongoing"   {{ old('status') === 'ongoing'   ? 'selected' : '' }}>Ongoing</option>
                        <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                    @error('status')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                     <label class="form-label">Max Teams <span style="color:#94a3b8;font-weight:400;">(optional)</span></label>
                     <input type="number" name="max_teams" class="form-control"
                            value="{{ old('max_teams') }}" min="2"
                            placeholder="Leave blank for unlimited">
                    @error('max_teams')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div style="display:flex;gap:10px;margin-top:8px;">
                <button type="submit" class="btn-primary">Create Tournament</button>
                <a href="{{ route('admin.tournaments.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

</div>
</div>
@endsection