@extends('layouts.admin')

@section('topbar-action')
    <a href="{{ route('admin.results.index') }}" class="btn-secondary">← Back to Results</a>
@endsection

@section('content')
<div style="max-width:560px;margin:0 auto;">
    <div class="page-header">
        <div class="breadcrumb">ADMIN <span>› RESULTS › RECORD</span></div>
        <div class="page-title">Record Result</div>
        <div class="page-subtitle">
            {{ $matchTeam->team->team_name ?? '—' }} —
            {{ $matchTeam->match->tournament->tournament_name ?? '' }}
        </div>
    </div>

    {{-- Context card --}}
    <div class="card" style="background:#f8faff;border:0.5px solid rgba(15,23,42,0.06);">
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
            <div>
                <div style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#94a3b8;margin-bottom:3px;">Team</div>
                <div style="font-size:13px;font-weight:600;">{{ $matchTeam->team->team_name ?? '—' }}</div>
            </div>
            <div>
                <div style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#94a3b8;margin-bottom:3px;">Match</div>
                <div style="font-size:13px;font-weight:600;">{{ $matchTeam->match->round_name ?: ('Match #' . $matchTeam->match->id) }}</div>
                <div style="font-size:10px;color:#94a3b8;">{{ $matchTeam->match->match_date->format('M d, Y') }}</div>
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
        <form method="POST" action="{{ route('admin.results.update', $matchTeam) }}">
            @csrf
            @method('PUT')

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group">
                    <label class="form-label">Points Scored</label>
                    <input type="number" name="points_scored" class="form-control"
                           value="{{ old('points_scored', $matchTeam->points_scored) }}"
                           min="0" placeholder="e.g. 85">
                    @error('points_scored')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Rank Position</label>
                    <input type="number" name="rank_position" class="form-control"
                           value="{{ old('rank_position', $matchTeam->rank_position) }}"
                           min="1" placeholder="1 = Winner">
                    @error('rank_position')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group">
                    <label class="form-label">Seed Number <span style="color:#94a3b8;font-weight:400;">(optional)</span></label>
                    <input type="number" name="seed_number" class="form-control"
                           value="{{ old('seed_number', $matchTeam->seed_number) }}" min="1">
                    @error('seed_number')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Highest Score <span style="color:#94a3b8;font-weight:400;">(optional)</span></label>
                    <input type="number" name="highest_score" class="form-control"
                           value="{{ old('highest_score', $matchTeam->result?->highest_score) }}"
                           step="0.01" min="0">
                    @error('highest_score')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Summary / Notes <span style="color:#94a3b8;font-weight:400;">(optional)</span></label>
                <textarea name="summary" class="form-control" rows="3"
                          placeholder="Any notable highlights, injuries, or context...">{{ old('summary', $matchTeam->result?->summary) }}</textarea>
                @error('summary')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div style="display:flex;gap:10px;margin-top:8px;">
                <button type="submit" class="btn-primary">
                    {{ $matchTeam->result ? 'Update Result' : 'Record Result' }}
                </button>
                <a href="{{ route('admin.results.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
