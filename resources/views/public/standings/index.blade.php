@extends('layouts.public')

@section('content')
<div style="margin-bottom:24px;">
    <div class="page-title">Standings</div>
    <div class="page-subtitle">Select a tournament to view team rankings.</div>
</div>

<form method="GET" action="{{ route('public.standings.index') }}" style="display:flex;align-items:center;gap:10px;margin-bottom:24px;flex-wrap:wrap;">
    <select name="tournament_id"
            style="padding:9px 14px;border:1px solid rgba(15,23,42,0.12);border-radius:7px;font-size:13px;font-family:'Inter',sans-serif;color:#0f172a;background:#fff;outline:none;min-width:280px;cursor:pointer;">
        <option value="">Select a tournament...</option>
        @foreach($tournaments as $t)
            <option value="{{ $t->id }}" {{ request('tournament_id')==$t->id?'selected':'' }}>
                {{ $t->tournament_name }} ({{ ucfirst($t->status) }})
            </option>
        @endforeach
    </select>
    <button type="submit" class="btn-primary">View Standings</button>
    @if(!request('tournament_id') && $tournaments->isEmpty())
        {{-- no tournaments --}}
    @endif
</form>

@if($tournament)
<div class="card" style="background:linear-gradient(135deg,#1a2233 0%,#243048 100%);border:none;margin-bottom:20px;padding:20px 24px;">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <div style="font-family:'Manrope',sans-serif;font-size:20px;font-weight:800;color:#fff;">{{ $tournament->tournament_name }}</div>
            <div style="font-size:12px;color:rgba(255,255,255,0.5);margin-top:3px;">
                {{ $tournament->sport->sport_name ?? '' }} &nbsp;·&nbsp;
                {{ $tournament->start_date->format('M d') }} – {{ $tournament->end_date->format('M d, Y') }}
            </div>
        </div>
        @if($tournament->status==='ongoing')
            <span style="background:rgba(34,197,94,0.2);color:#22c55e;font-size:10px;font-weight:700;letter-spacing:.08em;padding:5px 12px;border-radius:20px;">ONGOING</span>
        @else
            <span style="background:rgba(255,255,255,0.1);color:rgba(255,255,255,0.5);font-size:10px;font-weight:700;letter-spacing:.08em;padding:5px 12px;border-radius:20px;">COMPLETED</span>
        @endif
    </div>
</div>

@if($standings->isEmpty())
<div class="card" style="text-align:center;padding:48px;">
    <div style="font-size:32px;margin-bottom:12px;">📊</div>
    <div style="font-family:'Manrope',sans-serif;font-size:18px;font-weight:700;margin-bottom:6px;">No results yet</div>
    <div style="font-size:13px;color:#64748b;">Standings will appear once matches are completed and results are recorded.</div>
</div>
@else
<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:48px;">Rank</th>
                    <th>Team</th>
                    <th>Played</th>
                    <th>Wins</th>
                    <th>Losses</th>
                    <th>Points</th>
                    <th>Win Rate</th>
                </tr>
            </thead>
            <tbody>
                @foreach($standings as $i => $row)
                @php $winPct = $row['matches_played'] > 0 ? round(($row['wins']/$row['matches_played'])*100) : 0; @endphp
                <tr style="{{ $i===0 ? 'background:#f0fdf4;' : '' }}">
                    <td>
                        @if($i===0) <span style="font-size:20px;">🥇</span>
                        @elseif($i===1) <span style="font-size:20px;">🥈</span>
                        @elseif($i===2) <span style="font-size:20px;">🥉</span>
                        @else <span style="font-family:'Manrope',sans-serif;font-size:14px;font-weight:700;color:#94a3b8;">{{ $i+1 }}</span>
                        @endif
                    </td>
                    <td>
                        <div style="font-weight:700;font-size:14px;">{{ $row['team']->team_name ?? '—' }}</div>
                    </td>
                    <td style="font-size:13px;color:#64748b;">{{ $row['matches_played'] }}</td>
                    <td><span style="font-family:'Manrope',sans-serif;font-size:16px;font-weight:800;color:#16a34a;">{{ $row['wins'] }}</span></td>
                    <td><span style="font-family:'Manrope',sans-serif;font-size:16px;font-weight:800;color:#94a3b8;">{{ $row['losses'] }}</span></td>
                    <td><span style="font-family:'Manrope',sans-serif;font-size:16px;font-weight:800;color:#0f172a;">{{ $row['total_points'] }}</span></td>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="width:80px;height:6px;background:#f1f5f9;border-radius:3px;overflow:hidden;">
                                <div style="width:{{ $winPct }}%;height:100%;background:#22c55e;border-radius:3px;"></div>
                            </div>
                            <span style="font-size:12px;font-weight:600;color:#64748b;">{{ $winPct }}%</span>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@elseif(request('tournament_id'))
<div class="card" style="text-align:center;padding:40px;color:#94a3b8;">Tournament not found.</div>

@elseif($tournaments->isEmpty())
<div class="card" style="text-align:center;padding:48px;">
    <div style="font-size:32px;margin-bottom:12px;">🏟️</div>
    <div style="font-family:'Manrope',sans-serif;font-size:18px;font-weight:700;margin-bottom:6px;">No tournaments available</div>
    <div style="font-size:13px;color:#64748b;">Standings appear for ongoing and completed tournaments.</div>
</div>
@endif
@endsection
