@extends('layouts.admin')

@section('topbar-action')
@endsection

@section('content')
<div class="page-header">
    <div class="page-title">Dashboard</div>
</div>

<div class="grid-4">
    <div class="stat-card">
        <div class="stat-label">Total Users</div>
        <div class="stat-value">{{ $totalUsers }}</div>
        <div class="stat-sub">All roles combined</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Active Tournaments</div>
        <div class="stat-value">{{ $activeTournaments }}</div>
        <div class="stat-sub">Ongoing right now</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Teams</div>
        <div class="stat-value">{{ $totalTeams }}</div>
        <div class="stat-sub">Across all sports</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Matches Today</div>
        <div class="stat-value">{{ $matchesToday }}</div>
        <div class="stat-sub">Scheduled for today</div>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
            <div style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:700;">Recent Tournaments</div>
            <a href="{{ route('admin.tournaments.index') }}" style="font-size:11px;color:#22c55e;font-weight:600;text-decoration:none;">View all</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Tournament</th>
                        <th>Sport</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentTournaments as $tournament)
                    <tr>
                        <td>
                            <div style="font-weight:600;font-size:12px;">{{ $tournament->tournament_name }}</div>
                            <div style="font-size:10px;color:#94a3b8;">{{ $tournament->organizer->full_name }}</div>
                        </td>
                        <td style="font-size:12px;">{{ $tournament->sport->sport_name }}</td>
                        <td>
                            @if($tournament->status === 'ongoing')
                                <span class="badge badge-green">ONGOING</span>
                            @elseif($tournament->status === 'upcoming')
                                <span class="badge badge-blue">UPCOMING</span>
                            @else
                                <span class="badge badge-gray">COMPLETED</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align:center;color:#94a3b8;font-size:12px;">No tournaments yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
            <div style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:700;">Recent Users</div>
            <a href="{{ route('admin.users.index') }}" style="font-size:11px;color:#22c55e;font-weight:600;text-decoration:none;">View all</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Joined</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentUsers as $user)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div class="avatar">{{ strtoupper(substr($user->first_name, 0, 1)) }}</div>
                                <div>
                                    <div style="font-weight:600;font-size:12px;">{{ $user->full_name }}</div>
                                    <div style="font-size:10px;color:#94a3b8;">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($user->role === 'admin')
                                <span class="badge badge-red">ADMIN</span>
                            @elseif($user->role === 'organizer')
                                <span class="badge badge-blue">ORGANIZER</span>
                            @elseif($user->role === 'coach')
                                <span class="badge badge-amber">COACH</span>
                            @elseif($user->role === 'player')
                                <span class="badge badge-green">PLAYER</span>
                            @else
                                <span class="badge badge-gray">FAN</span>
                            @endif
                        </td>
                        <td style="font-size:11px;color:#94a3b8;">{{ $user->created_at->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align:center;color:#94a3b8;font-size:12px;">No users yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection