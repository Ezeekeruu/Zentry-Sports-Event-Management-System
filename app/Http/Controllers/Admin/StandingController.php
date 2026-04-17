<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StandingController extends Controller
{
    public function index(Request $request): View
    {
        $tournament = null;
        $standings  = collect();
        $bracketRounds = collect();
        $allTournamentStandings = collect();

        $tournamentId = $request->tournament_id;
        $tournaments  = Tournament::where('is_active', true)
            ->whereIn('status', ['ongoing', 'completed'])
            ->with('sport')
            ->orderBy('tournament_name')
            ->get();

        if ($tournamentId) {
            $tournament = Tournament::with(['sport', 'matches.matchTeams.team', 'matches.matchTeams.result'])->findOrFail($tournamentId);
            [$standings, $bracketRounds] = $this->buildStandingsAndBracket($tournament);
        } else {
            $allTournamentStandings = Tournament::with(['sport', 'matches.matchTeams.team', 'matches.matchTeams.result'])
                ->where('is_active', true)
                ->whereIn('status', ['ongoing', 'completed'])
                ->orderBy('tournament_name')
                ->get()
                ->map(function (Tournament $item) {
                    [$table] = $this->buildStandingsAndBracket($item);
                    return [
                        'tournament' => $item,
                        'standings' => $table,
                    ];
                });
        }

        return view('admin.standings.index', compact('tournaments', 'tournament', 'standings', 'bracketRounds', 'allTournamentStandings'));
    }

    private function buildStandingsAndBracket(Tournament $tournament): array
    {
        $teamStats = [];
        $matches = $tournament->matches
            ->sortBy([
                ['match_date', 'asc'],
                ['match_time', 'asc'],
                ['id', 'asc'],
            ])
            ->values();

        foreach ($matches as $match) {
            if ($match->status !== 'completed') {
                continue;
            }

            foreach ($match->matchTeams as $mt) {
                $tid = $mt->team_id;
                if (! isset($teamStats[$tid])) {
                    $teamStats[$tid] = [
                        'team' => $mt->team,
                        'matches_played' => 0,
                        'wins' => 0,
                        'draws' => 0,
                        'losses' => 0,
                        'total_points' => 0,
                    ];
                }
                $teamStats[$tid]['matches_played']++;
                $teamStats[$tid]['total_points'] += $mt->points_scored ?? 0;
            }

            [$winnerIds, $drawIds] = $this->resolveMatchOutcome($match);

            foreach ($match->matchTeams as $mt) {
                $tid = $mt->team_id;
                if (in_array($tid, $winnerIds, true)) {
                    $teamStats[$tid]['wins']++;
                } elseif (in_array($tid, $drawIds, true)) {
                    $teamStats[$tid]['draws']++;
                } else {
                    $teamStats[$tid]['losses']++;
                }
            }
        }

        $standings = collect($teamStats)
            ->sortByDesc('total_points')
            ->sortByDesc('draws')
            ->sortByDesc('wins')
            ->values();

        $bracketRounds = $matches
            ->groupBy(fn ($match) => $match->round_name ?: 'Round ' . $match->id)
            ->map(function ($roundMatches) {
                return $roundMatches->map(function ($match) {
                    [$winnerIds, $drawIds] = $this->resolveMatchOutcome($match);
                    $winners = $match->matchTeams
                        ->filter(fn ($mt) => in_array($mt->team_id, $winnerIds, true))
                        ->map(fn ($mt) => $mt->team->team_name ?? 'TBD')
                        ->values();
                    $drawn = $match->matchTeams
                        ->filter(fn ($mt) => in_array($mt->team_id, $drawIds, true))
                        ->map(fn ($mt) => $mt->team->team_name ?? 'TBD')
                        ->values();

                    return [
                        'match' => $match,
                        'teams' => $match->matchTeams->map(fn ($mt) => $mt->team->team_name ?? 'TBD')->values(),
                        'winners' => $winners,
                        'drawn' => $drawn,
                    ];
                });
            });

        return [$standings, $bracketRounds];
    }

    private function resolveMatchOutcome($match): array
    {
        $matchTeams = $match->matchTeams->values();
        if ($matchTeams->isEmpty()) {
            return [[], []];
        }

        $hasScores = $matchTeams->every(fn ($mt) => $mt->points_scored !== null);
        if ($hasScores) {
            $maxScore = $matchTeams->max('points_scored');
            $leaders = $matchTeams->filter(fn ($mt) => (int) $mt->points_scored === (int) $maxScore)->values();
            if ($leaders->count() === 1) {
                return [[$leaders->first()->team_id], []];
            }

            return [[], $leaders->pluck('team_id')->all()];
        }

        $withRank = $matchTeams->filter(fn ($mt) => $mt->rank_position !== null)->values();
        if ($withRank->isEmpty()) {
            return [[], []];
        }

        $bestRank = $withRank->min('rank_position');
        $leaders = $withRank->filter(fn ($mt) => (int) $mt->rank_position === (int) $bestRank)->values();
        if ($leaders->count() === 1) {
            return [[$leaders->first()->team_id], []];
        }

        return [[], $leaders->pluck('team_id')->all()];
    }
}
