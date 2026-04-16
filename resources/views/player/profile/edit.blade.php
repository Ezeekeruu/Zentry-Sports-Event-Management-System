@extends('layouts.player')

@section('content')
<div style="max-width:980px;margin:0 auto;">
    <div class="page-header">
        <div class="breadcrumb">PLAYER <span>› PROFILE</span></div>
        <div class="page-title">My Profile</div>
        <div class="page-subtitle">Update your personal information and password.</div>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('player.profile.update') }}">
            @csrf @method('PUT')

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control"
                           value="{{ old('first_name', $user->first_name) }}" required>
                    @error('first_name') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control"
                           value="{{ old('last_name', $user->last_name) }}" required>
                    @error('last_name') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control"
                       value="{{ old('email', $user->email) }}" required>
                @error('email') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div style="border-top:0.5px solid rgba(15,23,42,0.08);margin:16px 0;padding-top:16px;">
                <div style="font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#94a3b8;margin-bottom:12px;">Change Password</div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div class="form-group">
                        <label class="form-label">New Password <span style="color:#94a3b8;font-weight:400;">(optional)</span></label>
                        <input type="password" name="password" class="form-control">
                        @error('password') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                </div>
            </div>

            <div style="display:flex;gap:10px;margin-top:4px;">
                <button type="submit" class="btn-primary">Save Changes</button>
            </div>
        </form>
    </div>

    @if($team)
    <div class="card" style="background:#f8faff;">
        <div style="font-size:12px;color:#64748b;">
            These are your <strong>individual player stats</strong> from completed matches where stats were recorded.
        </div>
    </div>

    <div class="grid-3">
        <div class="stat-card">
            <div class="stat-label">Matches Played</div>
            <div class="stat-value">{{ $overallStats['matches_played'] }}</div>
            <div class="stat-sub">Completed games</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Wins</div>
            <div class="stat-value">{{ $overallStats['wins'] }}</div>
            <div class="stat-sub">{{ $overallStats['podiums'] }} podium finishes</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Points</div>
            <div class="stat-value">{{ $overallStats['total_points'] }}</div>
            <div class="stat-sub">Avg {{ $overallStats['avg_points'] }} per game</div>
        </div>
    </div>

    <div class="grid-2">
        <div class="card">
            <div style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:700;margin-bottom:14px;">Per-Game Player Stats</div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Tournament</th>
                            <th>Match</th>
                            <th>Date</th>
                            <th>Points</th>
                            <th>Rank</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($matchStats as $stat)
                            <tr>
                                <td style="font-size:12px;font-weight:600;">{{ Str::limit($stat->matchTeam->match->tournament->tournament_name ?? '—', 24) }}</td>
                                <td style="font-size:11px;color:#64748b;">{{ $stat->matchTeam->match->round_name ?: ('Match #'.$stat->matchTeam->match->id) }}</td>
                                <td style="font-size:11px;color:#64748b;">{{ $stat->matchTeam->match->match_date->format('M d, Y') }}</td>
                                <td style="font-size:12px;font-weight:700;">{{ $stat->points ?? 0 }}</td>
                                <td>
                                    @if($stat->matchTeam->rank_position)
                                        <span class="badge badge-purple">#{{ $stat->matchTeam->rank_position }}</span>
                                    @else
                                        <span style="font-size:12px;color:#94a3b8;">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" style="text-align:center;color:#94a3b8;padding:24px;font-size:12px;">No completed match stats yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:700;margin-bottom:14px;">Tournament Overall</div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Tournament</th>
                            <th>Played</th>
                            <th>Wins</th>
                            <th>Points</th>
                            <th>Win Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tournamentStats as $row)
                            <tr>
                                <td style="font-size:12px;font-weight:600;">{{ Str::limit($row['tournament']->tournament_name ?? '—', 22) }}</td>
                                <td>{{ $row['matches_played'] }}</td>
                                <td>{{ $row['wins'] }}</td>
                                <td>{{ $row['total_points'] }}</td>
                                <td>
                                    <span class="badge badge-blue">{{ $row['win_rate'] }}%</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" style="text-align:center;color:#94a3b8;padding:24px;font-size:12px;">No tournament data yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @else
        <div class="card" style="text-align:center;padding:24px;">
            <div style="font-family:'Manrope',sans-serif;font-size:16px;font-weight:700;margin-bottom:6px;">No Team Assigned</div>
            <div style="font-size:13px;color:#64748b;">Stats will appear once you are linked to a team and have completed matches.</div>
        </div>
    @endif
</div>
@endsection
