@extends('layouts.organizer')

@section('topbar-action')
    <a href="{{ route('organizer.results.index') }}" class="btn-secondary">← Back to Results</a>
@endsection

@section('content')
<div style="max-width:520px;margin:0 auto;">
    <div class="page-header">
        <div class="page-title">Record Result</div>
        <div class="page-subtitle">{{ $matchTeam->team->team_name ?? '—' }} — {{ $matchTeam->match->tournament->tournament_name ?? '' }}</div>
    </div>

    <div class="card" style="background:#f8faff;border:0.5px solid rgba(15,23,42,0.06);">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div>
                <div style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#94a3b8;margin-bottom:3px;">Match</div>
                <div style="font-size:13px;font-weight:600;">{{ $matchTeam->match->round_name ?: ('Match #'.$matchTeam->match->id) }}</div>
                <div style="font-size:11px;color:#94a3b8;">{{ $matchTeam->match->match_date->format('M d, Y') }}</div>
            </div>
            <div>
                <div style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#94a3b8;margin-bottom:3px;">Opponents</div>
                @foreach($matchTeam->match->matchTeams as $mt)
                    @if($mt->team_id !== $matchTeam->team_id)
                        <div style="font-size:12px;font-weight:500;">{{ $mt->team->team_name ?? '?' }}</div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('organizer.results.update', $matchTeam) }}">
            @csrf @method('PUT')
            @php($existingPlayerStats = $matchTeam->playerStats->keyBy('player_profile_id'))
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group">
                    <label class="form-label">Points Scored</label>
                    <input type="number" name="points_scored" class="form-control"
                           value="{{ old('points_scored',$matchTeam->points_scored) }}" min="0">
                    @error('points_scored') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Rank Position (1 = winner)</label>
                    <input type="number" name="rank_position" class="form-control"
                           value="{{ old('rank_position',$matchTeam->rank_position) }}" min="1">
                    @error('rank_position') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Highest Score <span style="color:#94a3b8;font-weight:400;">(optional)</span></label>
                <input type="number" name="highest_score" class="form-control"
                       value="{{ old('highest_score',$matchTeam->result?->highest_score) }}" step="0.01" min="0">
            </div>
            <div class="form-group">
                <label class="form-label">Summary <span style="color:#94a3b8;font-weight:400;">(optional)</span></label>
                <textarea name="summary" class="form-control" rows="3"
                          placeholder="Notable highlights, final score details...">{{ old('summary',$matchTeam->result?->summary) }}</textarea>
            </div>

            <div style="border-top:0.5px solid rgba(15,23,42,0.08);margin:16px 0;padding-top:16px;">
                <div style="font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#94a3b8;margin-bottom:10px;">
                    Player Stats (Individual)
                </div>
                @if($matchTeam->team->playerProfiles->isEmpty())
                    <div style="font-size:12px;color:#94a3b8;">No players found for this team.</div>
                @else
                    <div style="display:grid;grid-template-columns:1.2fr repeat({{ count($statFields) }}, minmax(80px, 1fr));gap:8px;align-items:center;">
                        <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;">Player</div>
                        @foreach($statFields as $label)
                            <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;">{{ $label }}</div>
                        @endforeach
                        @foreach($matchTeam->team->playerProfiles as $profile)
                            <div style="font-size:12px;font-weight:600;align-self:center;">
                                {{ $profile->user->first_name ?? '—' }} {{ $profile->user->last_name ?? '' }}
                                @if($profile->position)
                                    <span style="font-size:10px;color:#94a3b8;font-weight:500;">({{ $profile->position }})</span>
                                @endif
                            </div>
                            @foreach($statFields as $key => $label)
                                <div>
                                    <input type="number"
                                           name="player_stats[{{ $profile->id }}][{{ $key }}]"
                                           class="form-control"
                                           min="0"
                                           placeholder="{{ $label }}"
                                           value="{{ old('player_stats.'.$profile->id.'.'.$key, $existingPlayerStats[$profile->id]->stat_line[$key] ?? $existingPlayerStats[$profile->id]->points ?? null) }}">
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                    <div style="font-size:11px;color:#94a3b8;margin-top:8px;">Leave blank to clear a player's stat for this match.</div>
                @endif
            </div>
            <div style="display:flex;gap:10px;margin-top:8px;">
                <button type="submit" class="btn-primary">{{ $matchTeam->result ? 'Update Result' : 'Record Result' }}</button>
                <a href="{{ route('organizer.results.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
