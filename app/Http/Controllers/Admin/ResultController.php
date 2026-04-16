<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MatchTeam;
use App\Models\PlayerMatchStat;
use App\Models\Result;
use App\Models\ZentryMatch;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ResultController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = (int) $request->get('per_page', 15);
        $perPage = in_array($perPage, [5, 10, 15, 25, 50]) ? $perPage : 15;

        $matchTeams = MatchTeam::with(['match.tournament', 'team', 'result'])
            ->whereHas('match', fn($q) => $q->where('status', 'completed'))
            ->when($request->tournament_id, fn($q) => $q->whereHas('match', fn($m) => $m->where('tournament_id', $request->tournament_id)))
            ->paginate($perPage)
            ->withQueryString();

        $tournaments = \App\Models\Tournament::where('is_active', true)->orderBy('tournament_name')->get();

        return view('admin.results.index', compact('matchTeams', 'tournaments'));
    }

    public function edit(MatchTeam $matchTeam): View
    {
        $matchTeam->load(['match.tournament', 'team.playerProfiles.user', 'result', 'playerStats']);
        return view('admin.results.edit', compact('matchTeam'));
    }

    public function update(Request $request, MatchTeam $matchTeam): RedirectResponse
    {
        $request->validate([
            'points_scored'  => ['nullable', 'integer', 'min:0'],
            'rank_position'  => ['nullable', 'integer', 'min:1'],
            'seed_number'    => ['nullable', 'integer', 'min:1'],
            'summary'        => ['nullable', 'string', 'max:1000'],
            'highest_score'  => ['nullable', 'numeric', 'min:0'],
            'player_stats'   => ['nullable', 'array'],
            'player_stats.*.points' => ['nullable', 'integer', 'min:0'],
        ]);

        $matchTeam->update([
            'points_scored' => $request->points_scored,
            'rank_position' => $request->rank_position,
            'seed_number'   => $request->seed_number,
        ]);

        Result::updateOrCreate(
            ['match_team_id' => $matchTeam->id],
            [
                'summary'       => $request->summary,
                'highest_score' => $request->highest_score,
                'total_teams'   => $matchTeam->match->matchTeams()->count(),
                'recorded_at'   => now(),
            ]
        );

        $allowedPlayerIds = $matchTeam->team->playerProfiles()->pluck('id')->all();
        $playerStatsInput = $request->input('player_stats', []);

        foreach ($playerStatsInput as $playerProfileId => $statRow) {
            if (! in_array((int) $playerProfileId, $allowedPlayerIds, true)) {
                continue;
            }

            $points = $statRow['points'] ?? null;

            if ($points === null || $points === '') {
                PlayerMatchStat::where('match_team_id', $matchTeam->id)
                    ->where('player_profile_id', $playerProfileId)
                    ->delete();
                continue;
            }

            PlayerMatchStat::updateOrCreate(
                [
                    'match_team_id' => $matchTeam->id,
                    'player_profile_id' => $playerProfileId,
                ],
                [
                    'points' => (int) $points,
                ]
            );
        }

        return redirect()->route('admin.results.index')->with('success', 'Result recorded successfully.');
    }
}
