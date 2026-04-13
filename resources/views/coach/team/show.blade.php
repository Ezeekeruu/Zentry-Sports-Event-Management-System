@extends('layouts.coach')

@section('topbar-action')
    @if($team)
        <a href="{{ route('coach.team.edit') }}" class="btn-primary">Edit Team</a>
    @endif
@endsection

@section('content')
<div class="page-header">
    <div class="breadcrumb">COACH <span>› MY TEAM</span></div>
    <div class="page-title">{{ $team?->team_name ?? 'No Team Assigned' }}</div>
    @if($team) <div class="page-subtitle">{{ $team->sport->sport_name ?? '' }} &nbsp;·&nbsp; Founded {{ $team->founded_at?->format('Y') ?? 'N/A' }}</div> @endif
</div>

@if(!$team)
<div class="card" style="text-align:center;padding:40px;">
    <div style="font-size:13px;color:#64748b;">You have not been assigned to a team yet. Contact an administrator.</div>
</div>
@else

<div class="grid-2">
    <div class="card">
        <div style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:700;margin-bottom:14px;">Team Info</div>
        <div style="display:flex;flex-direction:column;gap:12px;">
            <div style="display:flex;justify-content:space-between;border-bottom:0.5px solid rgba(15,23,42,0.06);padding-bottom:10px;">
                <span style="font-size:12px;color:#64748b;font-weight:500;">Sport</span>
                <span class="badge badge-blue">{{ $team->sport->sport_name ?? '—' }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;border-bottom:0.5px solid rgba(15,23,42,0.06);padding-bottom:10px;">
                <span style="font-size:12px;color:#64748b;font-weight:500;">Founded</span>
                <span style="font-size:13px;font-weight:600;">{{ $team->founded_at?->format('M d, Y') ?? '—' }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;border-bottom:0.5px solid rgba(15,23,42,0.06);padding-bottom:10px;">
                <span style="font-size:12px;color:#64748b;font-weight:500;">Status</span>
                @if($team->is_active) <span class="badge badge-green">ACTIVE</span>
                @else <span class="badge badge-gray">ARCHIVED</span> @endif
            </div>
            <div style="display:flex;justify-content:space-between;">
                <span style="font-size:12px;color:#64748b;font-weight:500;">Players</span>
                <span style="font-size:13px;font-weight:600;">{{ $team->playerProfiles->count() }} on roster</span>
            </div>
        </div>
    </div>

    <div class="card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
            <div style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:700;">Tournaments</div>
        </div>
        @if($team->tournaments->isEmpty())
            <div style="text-align:center;color:#94a3b8;padding:20px;font-size:13px;">Not registered in any tournament yet.</div>
        @else
        <div class="table-wrap">
            <table>
                <thead><tr><th>Tournament</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach($team->tournaments as $t)
                    <tr>
                        <td style="font-weight:600;font-size:12px;">{{ $t->tournament_name }}</td>
                        <td>
                            @if($t->status==='ongoing') <span class="badge badge-green">ONGOING</span>
                            @elseif($t->status==='upcoming') <span class="badge badge-blue">UPCOMING</span>
                            @else <span class="badge badge-gray">COMPLETED</span> @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endif
@endsection
