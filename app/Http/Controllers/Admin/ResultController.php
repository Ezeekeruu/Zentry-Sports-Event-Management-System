<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MatchTeam;
use App\Models\PlayerMatchStat;
use App\Models\Result;
use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ResultController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->get('search', ''));
        $selectedTournamentId = $request->get('tournament_id');
        $tournaments = Tournament::where('is_active', true)->orderBy('tournament_name')->get();

        $standingsByTournament = Tournament::with([
                'sport',
                'matches' => fn ($query) => $query->where('status', 'completed')->with('matchTeams.team'),
            ])
            ->where('is_active', true)
            ->whereIn('status', ['ongoing', 'completed'])
            ->whereHas('matches', fn ($query) => $query->where('status', 'completed'))
            ->when($selectedTournamentId, fn ($query) => $query->where('id', $selectedTournamentId))
            ->when($search !== '', fn ($query) => $query->where('tournament_name', 'like', '%' . $search . '%'))
            ->orderBy('tournament_name')
            ->get()
            ->map(function (Tournament $tournament) {
                $standings = $this->buildStandings($tournament);
                return [
                    'tournament' => $tournament,
                    'standings' => $standings,
                ];
            })
            ->filter(fn ($group) => $group['standings']->isNotEmpty())
            ->values();

        return view('admin.results.index', compact('tournaments', 'standingsByTournament', 'search', 'selectedTournamentId'));
    }

    public function edit(MatchTeam $matchTeam): View
    {
        $matchTeam->load(['match.tournament', 'team.playerProfiles.user', 'result', 'playerStats']);
        $sportName = $matchTeam->match->tournament->sport->sport_name ?? null;
        $statFields = $this->statFieldsForSport($sportName);
        return view('admin.results.edit', compact('matchTeam', 'statFields'));
    }

    public function update(Request $request, MatchTeam $matchTeam): RedirectResponse
    {
        $sportName = $matchTeam->match->tournament->sport->sport_name ?? null;
        $statFields = $this->statFieldsForSport($sportName);

        $rules = [
            'points_scored'  => ['nullable', 'integer', 'min:0'],
            'rank_position'  => ['nullable', 'integer', 'min:1'],
            'seed_number'    => ['nullable', 'integer', 'min:1'],
            'summary'        => ['nullable', 'string', 'max:1000'],
            'highest_score'  => ['nullable', 'numeric', 'min:0'],
            'player_stats'   => ['nullable', 'array'],
        ];

        foreach (array_keys($statFields) as $statKey) {
            $rules["player_stats.*.$statKey"] = ['nullable', 'integer', 'min:0'];
        }
        $request->validate($rules);

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

            $statLine = [];
            foreach (array_keys($statFields) as $statKey) {
                $value = $statRow[$statKey] ?? null;
                if ($value === '' || $value === null) {
                    continue;
                }
                $statLine[$statKey] = (int) $value;
            }

            if (empty($statLine)) {
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
                    'points' => $statLine['points'] ?? null,
                    'stat_line' => $statLine,
                ]
            );
        }

        return redirect()->route('admin.results.index')->with('success', 'Result recorded successfully.');
    }

    private function statFieldsForSport(?string $sportName): array
    {
        $normalized = strtolower((string) $sportName);

        if (str_contains($normalized, 'basketball')) {
            return [
                'points' => 'Points',
                'rebounds' => 'Rebounds',
                'assists' => 'Assists',
                'steals' => 'Steals',
                'blocks' => 'Blocks',
            ];
        }

        if (str_contains($normalized, 'volleyball')) {
            return [
                'points' => 'Points',
                'kills' => 'Kills',
                'blocks' => 'Blocks',
                'aces' => 'Aces',
                'digs' => 'Digs',
            ];
        }

        if (str_contains($normalized, 'football') || str_contains($normalized, 'soccer')) {
            return [
                'goals' => 'Goals',
                'assists' => 'Assists',
                'shots_on_target' => 'Shots On Target',
                'tackles' => 'Tackles',
                'saves' => 'Saves',
            ];
        }

        return [
            'points' => 'Points',
        ];
    }

    private function buildStandings(Tournament $tournament)
    {
        $teamStats = [];

        foreach ($tournament->matches as $match) {
            foreach ($match->matchTeams as $mt) {
                $teamId = $mt->team_id;

                if (! isset($teamStats[$teamId])) {
                    $teamStats[$teamId] = [
                        'team' => $mt->team,
                        'played' => 0,
                        'wins' => 0,
                        'losses' => 0,
                        'draws' => 0,
                    ];
                }

                $teamStats[$teamId]['played']++;
            }

            $ranked = $match->matchTeams->filter(fn ($mt) => $mt->rank_position !== null)->values();
            if ($ranked->isNotEmpty()) {
                $bestRank = $ranked->min('rank_position');
                $winners = $ranked->filter(fn ($mt) => (int) $mt->rank_position === (int) $bestRank)->values();

                if ($winners->count() === 1) {
                    $winnerTeamId = $winners->first()->team_id;
                    foreach ($match->matchTeams as $mt) {
                        if ((int) $mt->team_id === (int) $winnerTeamId) {
                            $teamStats[$mt->team_id]['wins']++;
                        } else {
                            $teamStats[$mt->team_id]['losses']++;
                        }
                    }
                    continue;
                }

                foreach ($winners as $draw) {
                    $teamStats[$draw->team_id]['draws']++;
                }
                foreach ($match->matchTeams as $mt) {
                    if (! $winners->contains(fn ($w) => (int) $w->team_id === (int) $mt->team_id)) {
                        $teamStats[$mt->team_id]['losses']++;
                    }
                }
                continue;
            }

            $scored = $match->matchTeams->filter(fn ($mt) => $mt->points_scored !== null)->values();
            if ($scored->count() === $match->matchTeams->count()) {
                $topScore = $scored->max('points_scored');
                $winners = $scored->filter(fn ($mt) => (int) $mt->points_scored === (int) $topScore)->values();
                if ($winners->count() === 1) {
                    $winnerTeamId = $winners->first()->team_id;
                    foreach ($match->matchTeams as $mt) {
                        if ((int) $mt->team_id === (int) $winnerTeamId) {
                            $teamStats[$mt->team_id]['wins']++;
                        } else {
                            $teamStats[$mt->team_id]['losses']++;
                        }
                    }
                } else {
                    foreach ($winners as $draw) {
                        $teamStats[$draw->team_id]['draws']++;
                    }
                    foreach ($match->matchTeams as $mt) {
                        if (! $winners->contains(fn ($w) => (int) $w->team_id === (int) $mt->team_id)) {
                            $teamStats[$mt->team_id]['losses']++;
                        }
                    }
                }
            }
        }

        return collect($teamStats)
            ->sortByDesc('draws')
            ->sortByDesc('wins')
            ->values();
    }
}
