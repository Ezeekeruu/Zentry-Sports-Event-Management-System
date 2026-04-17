@extends('layouts.organizer')

@section('topbar-action')
    <a href="{{ route('organizer.matches.index') }}" class="btn-secondary">← Back to Matches</a>
@endsection

@section('content')
<div style="max-width:920px;margin:0 auto;">
    <div class="page-header">
        <div class="page-title">Update Winner & Player Points</div>
        <div class="page-subtitle">
            {{ $match->tournament->tournament_name ?? '' }} · {{ $match->round_name ?: ('Match #'.$match->id) }}
        </div>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('organizer.matches.winner.update', $match) }}">
            @csrf
            @method('PUT')

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                <div class="form-group">
                    <label class="form-label">Match Date</label>
                    <input class="form-control" value="{{ $match->match_date->format('M d, Y') }}" disabled>
                </div>
                <div class="form-group">
                    <label class="form-label">Venue</label>
                    <input class="form-control" value="{{ $match->venue ?: '—' }}" disabled>
                </div>
                <div class="form-group">
                    <label class="form-label">Winner Team</label>
                    <select name="winner_team_id" class="form-control" required>
                        <option value="">Select winner team</option>
                        @foreach($match->matchTeams as $mt)
                            <option value="{{ $mt->team_id }}" {{ (string) old('winner_team_id', $currentWinnerTeamId) === (string) $mt->team_id ? 'selected' : '' }}>
                                {{ $mt->team->team_name ?? '—' }}
                            </option>
                        @endforeach
                    </select>
                    @error('winner_team_id')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            @foreach($match->matchTeams as $mt)
                <div style="border-top:0.5px solid rgba(15,23,42,0.08);margin-top:14px;padding-top:14px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:10px;">
                        <div style="font-family:'Manrope',sans-serif;font-size:16px;font-weight:800;">
                            {{ $mt->team->team_name ?? '—' }}
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <label class="form-label" style="margin:0;">Team Points</label>
                            <input type="number"
                                   name="team_points[{{ $mt->team_id }}]"
                                   class="form-control"
                                   style="width:120px;"
                                   min="0"
                                   value="{{ old('team_points.'.$mt->team_id, $mt->points_scored) }}"
                                   placeholder="Score">
                        </div>
                    </div>

                    @if($mt->team->playerProfiles->isEmpty())
                        <div style="font-size:12px;color:#94a3b8;">No players found for this team.</div>
                    @else
                        <div style="display:grid;grid-template-columns:1fr 120px;gap:8px;align-items:center;">
                            <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;">Player</div>
                            <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;">Points</div>

                            @foreach($mt->team->playerProfiles as $profile)
                                @php($existing = $existingStats[$mt->id . '-' . $profile->id] ?? null)
                                <div style="font-size:12px;font-weight:600;">
                                    {{ $profile->user->first_name ?? '—' }} {{ $profile->user->last_name ?? '' }}
                                </div>
                                <div>
                                    <input type="number"
                                           name="player_points[{{ $mt->team_id }}][{{ $profile->id }}]"
                                           class="form-control"
                                           min="0"
                                           value="{{ old('player_points.'.$mt->team_id.'.'.$profile->id, $existing?->points) }}"
                                           placeholder="0">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach

            <div style="display:flex;gap:10px;margin-top:16px;">
                <button type="submit" class="btn-primary">Save Winner & Points</button>
                <a href="{{ route('organizer.matches.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
