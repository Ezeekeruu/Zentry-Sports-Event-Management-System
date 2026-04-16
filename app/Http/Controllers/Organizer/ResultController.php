<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\MatchTeam;
use App\Models\PlayerMatchStat;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ResultController extends Controller
{
    public function index(Request $request): View
    {
        $matchTeams = MatchTeam::with(['match.tournament', 'team', 'result'])
            ->whereHas('match.tournament', fn($q) => $q->where('organizer_id', auth()->id()))
            ->whereHas('match', fn($q) => $q->where('status', 'completed'))
            ->paginate(15)
            ->withQueryString();

        return view('organizer.results.index', compact('matchTeams'));
    }

    public function edit(MatchTeam $matchTeam): View
    {
        if ($matchTeam->match->tournament->organizer_id !== auth()->id()) abort(403);
        $matchTeam->load(['match.tournament', 'team.playerProfiles.user', 'result', 'playerStats']);
        return view('organizer.results.edit', compact('matchTeam'));
    }

    public function update(Request $request, MatchTeam $matchTeam): RedirectResponse
    {
        if ($matchTeam->match->tournament->organizer_id !== auth()->id()) abort(403);

        $request->validate([
            'points_scored' => ['nullable', 'integer', 'min:0'],
            'rank_position' => ['nullable', 'integer', 'min:1'],
            'summary'       => ['nullable', 'string', 'max:1000'],
            'highest_score' => ['nullable', 'numeric', 'min:0'],
            'player_stats'  => ['nullable', 'array'],
            'player_stats.*.points' => ['nullable', 'integer', 'min:0'],
        ]);

        $matchTeam->update([
            'points_scored' => $request->points_scored,
            'rank_position' => $request->rank_position,
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

        return redirect()->route('organizer.results.index')->with('success', 'Result recorded.');
    }
}
