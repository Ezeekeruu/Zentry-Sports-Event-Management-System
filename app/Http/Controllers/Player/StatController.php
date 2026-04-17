<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use App\Models\PlayerMatchStat;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StatController extends Controller
{
    public function index(Request $request): View
    {
        $playerProfile = auth()->user()->playerProfile;
        $selectedTournamentId = $request->get('tournament_id');

        $stats = collect();
        $totals = [];
        $overallStats = [
            'matches_played' => 0,
            'wins' => 0,
            'podiums' => 0,
            'total_points' => 0,
            'avg_points' => 0,
        ];
        $tournamentOptions = collect();
        $tournamentStats = collect();

        if ($playerProfile) {
            $baseQuery = PlayerMatchStat::where('player_profile_id', $playerProfile->id)
                ->whereHas('matchTeam.match', fn ($query) => $query->where('status', 'completed'))
                ->with('matchTeam.match.tournament');

            $allStats = (clone $baseQuery)->get();

            $tournamentOptions = $allStats
                ->map(fn ($item) => $item->matchTeam->match->tournament)
                ->filter()
                ->unique('id')
                ->sortBy('tournament_name')
                ->values();

            if ($selectedTournamentId) {
                $baseQuery->whereHas('matchTeam.match', fn ($query) => $query->where('tournament_id', $selectedTournamentId));
            }

            $stats = $baseQuery->orderByDesc('id')->get();

            foreach ($stats as $item) {
                $line = $item->stat_line ?? [];
                if (empty($line) && $item->points !== null) {
                    $line = ['points' => (int) $item->points];
                }

                foreach ($line as $key => $value) {
                    $totals[$key] = ($totals[$key] ?? 0) + (int) $value;
                }
            }

            $overallStats = [
                'matches_played' => $stats->count(),
                'wins' => $stats->filter(fn($row) => $row->matchTeam?->rank_position === 1)->count(),
                'podiums' => $stats->filter(function ($row) {
                    $rank = $row->matchTeam?->rank_position;
                    return $rank && $rank <= 3;
                })->count(),
                'total_points' => $stats->sum(fn($row) => (int) ($row->points ?? 0)),
                'avg_points' => 0,
            ];
            $overallStats['avg_points'] = $overallStats['matches_played'] > 0
                ? round($overallStats['total_points'] / $overallStats['matches_played'], 2)
                : 0;

            $tournamentStats = $allStats
                ->groupBy(fn($row) => $row->matchTeam->match->tournament_id)
                ->map(function ($rows) {
                    $first = $rows->first();
                    $matchesPlayed = $rows->count();
                    $wins = $rows->filter(fn($item) => $item->matchTeam?->rank_position === 1)->count();
                    $totalPoints = $rows->sum(fn($item) => (int) ($item->points ?? 0));

                    return [
                        'tournament' => $first->matchTeam->match->tournament,
                        'matches_played' => $matchesPlayed,
                        'wins' => $wins,
                        'total_points' => $totalPoints,
                        'win_rate' => $matchesPlayed > 0 ? round(($wins / $matchesPlayed) * 100) : 0,
                    ];
                })
                ->sortByDesc('total_points')
                ->values();
        }

        return view('player.stats.index', [
            'stats' => $stats,
            'totals' => $totals,
            'overallStats' => $overallStats,
            'tournamentOptions' => $tournamentOptions,
            'selectedTournamentId' => $selectedTournamentId,
            'tournamentStats' => $tournamentStats,
        ]);
    }
}
