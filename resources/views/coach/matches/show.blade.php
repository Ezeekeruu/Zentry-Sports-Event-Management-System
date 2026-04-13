@extends('layouts.coach')

@section('topbar-action')
    <a href="{{ route('coach.matches.index') }}" class="btn-secondary">← Back to Matches</a>
@endsection

@section('content')
<div class="page-header">
    <div class="breadcrumb">COACH <span>› MATCHES › DETAIL</span></div>
    <div class="page-title">{{ $match->round_name ?: 'Match Detail' }}</div>
    <div class="page-subtitle">{{ $match->tournament->tournament_name ?? '' }} — {{ $match->match_date->format('M d, Y') }}</div>
</div>

<div class="grid-2">
    <div class="card">
        <div style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:700;margin-bottom:14px;">Match Info</div>
        <div style="display:flex;flex-direction:column;gap:10px;">
            <div style="display:flex;justify-content:space-between;border-bottom:0.5px solid rgba(15,23,42,0.06);padding-bottom:9px;">
                <span style="font-size:12px;color:#64748b;">Status</span>
                @if($match->status==='live') <span class="badge badge-red">LIVE</span>
                @elseif($match->status==='completed') <span class="badge badge-green">DONE</span>
                @else <span class="badge badge-blue">SCHEDULED</span> @endif
            </div>
            <div style="display:flex;justify-content:space-between;border-bottom:0.5px solid rgba(15,23,42,0.06);padding-bottom:9px;">
                <span style="font-size:12px;color:#64748b;">Date</span>
                <span style="font-size:13px;font-weight:600;">{{ $match->match_date->format('M d, Y') }}</span>
            </div>
            @if($match->match_time)
            <div style="display:flex;justify-content:space-between;border-bottom:0.5px solid rgba(15,23,42,0.06);padding-bottom:9px;">
                <span style="font-size:12px;color:#64748b;">Time</span>
                <span style="font-size:13px;font-weight:600;">{{ \Carbon\Carbon::parse($match->match_time)->format('h:i A') }}</span>
            </div>
            @endif
            <div style="display:flex;justify-content:space-between;border-bottom:0.5px solid rgba(15,23,42,0.06);padding-bottom:9px;">
                <span style="font-size:12px;color:#64748b;">Venue</span>
                <span style="font-size:13px;font-weight:600;">{{ $match->venue ?: '—' }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;">
                <span style="font-size:12px;color:#64748b;">Tournament</span>
                <span style="font-size:13px;font-weight:600;">{{ Str::limit($match->tournament->tournament_name??'—',24) }}</span>
            </div>
        </div>
    </div>

    <div class="card">
        <div style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:700;margin-bottom:14px;">Teams & Results</div>
        @foreach($match->matchTeams as $mt)
        <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:0.5px solid rgba(15,23,42,0.06);">
            <div style="display:flex;align-items:center;gap:8px;">
                @if($mt->team_id === $team->id)
                    <div class="avatar" style="background:#312e00;color:#eab308;">{{ strtoupper(substr($mt->team->team_name??'?',0,1)) }}</div>
                @else
                    <div class="avatar">{{ strtoupper(substr($mt->team->team_name??'?',0,1)) }}</div>
                @endif
                <div>
                    <div style="font-size:13px;font-weight:600;">{{ $mt->team->team_name ?? '—' }}</div>
                    @if($mt->team_id === $team->id) <div style="font-size:10px;color:#eab308;">Your Team</div> @endif
                </div>
            </div>
            <div style="text-align:right;">
                @if($mt->points_scored !== null)
                    <div style="font-family:'Manrope',sans-serif;font-size:22px;font-weight:800;">{{ $mt->points_scored }}</div>
                @endif
                @if($mt->rank_position === 1) <span class="badge badge-amber">🥇 Winner</span>
                @elseif($mt->rank_position) <span class="badge badge-gray">#{{ $mt->rank_position }}</span> @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
