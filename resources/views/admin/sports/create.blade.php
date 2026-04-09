@extends('layouts.admin')

@section('topbar-action')
    <a href="{{ route('admin.sports.index') }}" class="btn-secondary">← Back to Sports</a>
@endsection

@section('content')
<div style="max-width:560px;margin:0 auto;">
    <div class="page-header">
        <div class="page-title">Add Sport</div>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('admin.sports.store') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Sport Name</label>
                <input type="text" name="sport_name" class="form-control"
                       value="{{ old('sport_name') }}" required autofocus
                       placeholder="e.g. Basketball">
                @error('sport_name')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group">
                    <label class="form-label">Min Teams Per Match</label>
                    <input type="number" name="min_teams_per_match" class="form-control"
                           value="{{ old('min_teams_per_match', 2) }}" min="2" required>
                    @error('min_teams_per_match')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Max Teams Per Match</label>
                    <input type="number" name="max_teams_per_match" class="form-control"
                           value="{{ old('max_teams_per_match', 2) }}" min="2" required>
                    @error('max_teams_per_match')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Description <span style="color:#94a3b8;font-weight:400;">(optional)</span></label>
                <textarea name="description" class="form-control" rows="3"
                          placeholder="Brief description of the sport...">{{ old('description') }}</textarea>
                @error('description')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div style="display:flex;gap:10px;margin-top:8px;">
                <button type="submit" class="btn-primary">Create Sport</button>
                <a href="{{ route('admin.sports.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection