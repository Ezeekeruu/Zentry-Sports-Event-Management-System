@extends('layouts.organizer')

@section('content')
<div class="page-title" style="margin-bottom:20px;">Results & Standings</div>

<div class="card">
    <form method="GET" action="{{ route('organizer.results.index') }}" style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:12px;">
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
            <label style="font-size:12px;color:#64748b;font-weight:500;">Tournament:</label>
            <select name="tournament_id" class="form-control" style="max-width:260px;">
                <option value="">All tournaments</option>
                @foreach($tournaments as $t)
                    <option value="{{ $t->id }}" {{ (string) $selectedTournamentId === (string) $t->id ? 'selected' : '' }}>
                        {{ $t->tournament_name }}
                    </option>
                @endforeach
            </select>
            <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Search tournament..." style="max-width:220px;">
            <button type="submit" class="btn-primary" style="padding:8px 14px;">Apply</button>
            @if($selectedTournamentId || $search)
                <a href="{{ route('organizer.results.index') }}" class="btn-secondary" style="padding:8px 14px;">Clear</a>
            @endif
        </div>
    </form>

    <div style="font-family:'Manrope',sans-serif;font-size:16px;font-weight:800;margin-bottom:10px;">Tournament Standings</div>
    @if($standingsByTournament->isEmpty())
        <div style="text-align:center;color:#94a3b8;padding:24px 0;">
            No tournaments with completed matches yet.
        </div>
    @else
        <div style="display:flex;flex-direction:column;gap:10px;">
            @foreach($standingsByTournament as $idx => $group)
                <div style="border:0.5px solid rgba(15,23,42,0.08);border-radius:10px;overflow:hidden;">
                    <button type="button"
                            class="standing-toggle"
                            data-target="standing-panel-{{ $idx }}"
                            style="width:100%;display:flex;align-items:center;justify-content:space-between;gap:10px;padding:12px 14px;background:#f8faff;border:none;cursor:pointer;">
                        <div style="text-align:left;">
                            <div style="font-size:14px;font-weight:800;color:#0f172a;">{{ $group['tournament']->tournament_name }}</div>
                            <div style="font-size:11px;color:#64748b;">
                                {{ $group['tournament']->sport->sport_name ?? '—' }}
                                &nbsp;·&nbsp;
                                {{ ucfirst($group['tournament']->status) }}
                            </div>
                        </div>
                        <span style="font-size:11px;color:#64748b;">Click to {{ $idx === 0 ? 'collapse' : 'expand' }}</span>
                    </button>

                    <div id="standing-panel-{{ $idx }}" style="{{ $idx === 0 ? '' : 'display:none;' }}padding:10px 12px;background:#fff;">
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th style="width:44px;">#</th>
                                        <th>Team</th>
                                        <th>W</th>
                                        <th>L</th>
                                        <th>D</th>
                                        <th>WIN%</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($group['standings'] as $rank => $row)
                                        @php($winPct = $row['played'] > 0 ? number_format($row['wins'] / $row['played'], 3) : '0.000')
                                        <tr>
                                            <td style="font-weight:700;color:#334155;">{{ $rank + 1 }}</td>
                                            <td style="font-weight:600;">{{ $row['team']->team_name ?? '—' }}</td>
                                            <td style="font-weight:700;color:#16a34a;">{{ $row['wins'] }}</td>
                                            <td style="font-weight:700;color:#64748b;">{{ $row['losses'] }}</td>
                                            <td style="font-weight:700;color:#0ea5e9;">{{ $row['draws'] }}</td>
                                            <td style="font-weight:700;">{{ $winPct }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<script>
document.querySelectorAll('.standing-toggle').forEach(function(button) {
    button.addEventListener('click', function() {
        const panel = document.getElementById(this.dataset.target);
        if (!panel) return;
        panel.style.display = panel.style.display === 'none' ? '' : 'none';
    });
});
</script>
@endsection
