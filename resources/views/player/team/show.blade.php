@extends('layouts.player')

@section('content')
<div class="page-header">
    <div class="page-title">{{ $team?->team_name ?? 'No Team' }}</div>
    @if($team)<div class="page-subtitle">{{ $team->sport->sport_name ?? '' }} &nbsp;·&nbsp; Coach: {{ $team->coach ? $team->coach->first_name.' '.$team->coach->last_name : 'None' }}</div>@endif
</div>

@if(!$team)
<div class="card" style="text-align:center;padding:40px;">
    <div style="font-size:13px;color:#64748b;">You haven't been assigned to a team yet.</div>
</div>
@else

<div class="grid-2">
    <div class="card">
        <div style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:700;margin-bottom:14px;">Team Info</div>
        <div style="display:flex;flex-direction:column;gap:10px;">
            <div style="display:flex;justify-content:space-between;border-bottom:0.5px solid rgba(15,23,42,0.06);padding-bottom:9px;">
                <span style="font-size:12px;color:#64748b;">Sport</span>
                <span class="badge badge-blue">{{ $team->sport->sport_name ?? '—' }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;border-bottom:0.5px solid rgba(15,23,42,0.06);padding-bottom:9px;">
                <span style="font-size:12px;color:#64748b;">Coach</span>
                <span style="font-size:13px;font-weight:600;">{{ $team->coach ? $team->coach->first_name.' '.$team->coach->last_name : '—' }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;border-bottom:0.5px solid rgba(15,23,42,0.06);padding-bottom:9px;">
                <span style="font-size:12px;color:#64748b;">Founded</span>
                <span style="font-size:13px;font-weight:600;">{{ $team->founded_at?->format('M d, Y') ?? '—' }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;">
                <span style="font-size:12px;color:#64748b;">Players</span>
                <span style="font-size:13px;font-weight:600;">{{ $team->playerProfiles->count() }}</span>
            </div>
        </div>
    </div>

    <div class="card">
        <div style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:700;margin-bottom:14px;">Full Roster</div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Player</th><th></th></tr></thead>
                <tbody>
                    @foreach($team->playerProfiles as $p)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div class="avatar">{{ strtoupper(substr($p->user->first_name??'?',0,1)) }}</div>
                                <div>
                                    <div style="font-size:13px;font-weight:600;">{{ $p->user->first_name??'—' }} {{ $p->user->last_name??'' }}</div>
                                    <div style="font-size:10px;color:#94a3b8;">{{ $p->user->email??'' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($p->user_id === auth()->id())
                                <span class="badge badge-purple">YOU</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection
