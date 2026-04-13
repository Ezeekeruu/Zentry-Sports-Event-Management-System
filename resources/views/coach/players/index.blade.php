@extends('layouts.coach')

@section('content')
<div class="page-header">
    <div class="breadcrumb">COACH <span>› PLAYERS</span></div>
    <div class="page-title">{{ $team->team_name }} — Roster</div>
    <div class="page-subtitle">{{ $team->playerProfiles->count() }} player(s) on the team.</div>
</div>

<div style="display:grid;grid-template-columns:1fr 300px;gap:16px;align-items:start;">
    <div class="card">
        <div style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:700;margin-bottom:14px;">Current Roster</div>
        @if($team->playerProfiles->isEmpty())
            <div style="text-align:center;color:#94a3b8;padding:30px;font-size:13px;">No players yet. Add players using the panel on the right.</div>
        @else
        <div class="table-wrap">
            <table>
                <thead><tr><th>Player</th><th>Email</th><th>Action</th></tr></thead>
                <tbody>
                    @foreach($team->playerProfiles as $profile)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div class="avatar">{{ strtoupper(substr($profile->user->first_name??'?',0,1)) }}</div>
                                <div style="font-weight:600;font-size:13px;">{{ $profile->user->first_name??'—' }} {{ $profile->user->last_name??'' }}</div>
                            </div>
                        </td>
                        <td style="font-size:12px;color:#64748b;">{{ $profile->user->email??'—' }}</td>
                        <td>
                            <form method="POST" action="{{ route('coach.team.players.destroy', $profile) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-danger" style="padding:5px 10px;font-size:11px;"
                                        onclick="return confirm('Remove this player?')">Remove</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <div class="card">
        <div style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:700;margin-bottom:14px;">Add Player</div>
        @if($availablePlayers->isEmpty())
            <div style="font-size:13px;color:#94a3b8;">No unassigned players available.</div>
        @else
        <form method="POST" action="{{ route('coach.team.players.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Select Player</label>
                <select name="user_id" class="form-control" required>
                    <option value="">Choose a player...</option>
                    @foreach($availablePlayers as $player)
                        <option value="{{ $player->id }}">{{ $player->first_name }} {{ $player->last_name }}</option>
                    @endforeach
                </select>
                @error('user_id') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <button type="submit" class="btn-primary" style="width:100%;justify-content:center;">Add to Roster</button>
        </form>
        @endif
    </div>
</div>
@endsection
