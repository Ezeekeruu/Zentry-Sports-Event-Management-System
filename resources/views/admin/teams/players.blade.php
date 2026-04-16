@extends('layouts.admin')

@section('topbar-action')
    <a href="{{ route('admin.teams.index') }}" class="btn-secondary">← Back to Teams</a>
@endsection

@section('content')
<div class="page-header">
    <div class="page-title">{{ $team->team_name }}</div>
    <div class="page-subtitle">
        {{ $team->sport->sport_name ?? '—' }} &nbsp;·&nbsp;
        Coach: {{ $team->coach ? $team->coach->first_name . ' ' . $team->coach->last_name : 'None' }}
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 340px;gap:16px;align-items:start;">

    {{-- Current roster --}}
    <div class="card">
        <div style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:700;margin-bottom:14px;">
            Current Roster
            <span style="font-size:11px;font-weight:500;color:#64748b;margin-left:8px;">{{ $team->playerProfiles->count() }} player(s)</span>
        </div>

        @if($team->playerProfiles->isEmpty())
            <div style="text-align:center;color:#94a3b8;padding:30px 0;font-size:13px;">
                No players on this team yet.
            </div>
        @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Player</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($team->playerProfiles as $profile)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div class="avatar">{{ strtoupper(substr($profile->user->first_name ?? '?', 0, 1)) }}</div>
                                <div>
                                    <div style="font-weight:600;font-size:13px;">
                                        {{ $profile->user->first_name ?? '—' }} {{ $profile->user->last_name ?? '' }}
                                    </div>
                                    <span class="badge badge-green" style="margin-top:2px;">PLAYER</span>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:12px;color:#64748b;">{{ $profile->user->email ?? '—' }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.teams.players.remove', [$team, $profile]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger" style="padding:5px 10px;font-size:11px;"
                                        onclick="return confirm('Remove this player from the team?')">
                                    Remove
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- Add player panel --}}
    <div class="card">
        <div style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:700;margin-bottom:14px;">
            Add Player
        </div>

        @if($availablePlayers->isEmpty())
            <div style="font-size:13px;color:#94a3b8;padding:12px 0;">
                No unassigned players available. 
            </div>
        @else
        <form method="POST" action="{{ route('admin.teams.players.add', $team) }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Select Player</label>
                <select name="user_id" class="form-control" required>
                    <option value="">Choose a player...</option>
                    @foreach($availablePlayers as $player)
                        <option value="{{ $player->id }}">
                            {{ $player->first_name }} {{ $player->last_name }}
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn-primary" style="width:100%;justify-content:center;">
                Add to Team
            </button>
        </form>
        @endif

    </div>

</div>
@endsection
