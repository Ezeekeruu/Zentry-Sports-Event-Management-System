<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use App\Models\MatchTeam;
use App\Models\PlayerMatchStat;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class ResultController extends Controller
{
    public function index(): View
    {
        $playerProfile = auth()->user()->playerProfile;
        $team = $playerProfile?->team;

        $matchTeams = $team
            ? MatchTeam::with([
                'match.tournament.sport',
                'result',
                'playerStats' => fn ($query) => $query->where('player_profile_id', $playerProfile->id),
            ])
                ->where('team_id', $team->id)
                ->whereHas('match', fn($q) => $q->where('status', 'completed'))
                ->latest()
                ->paginate(15)
            : new LengthAwarePaginator([], 0, 15);

        $careerTotals = [
            'matches_with_stats' => 0,
            'stat_totals' => [],
            'total_points' => 0,
        ];

        if ($playerProfile) {
            $stats = PlayerMatchStat::where('player_profile_id', $playerProfile->id)
                ->whereHas('matchTeam.match', fn ($query) => $query->where('status', 'completed'))
                ->get();

            $totals = [];
            foreach ($stats as $item) {
                $line = $item->stat_line ?? [];
                if (empty($line) && $item->points !== null) {
                    $line = ['points' => (int) $item->points];
                }

                foreach ($line as $key => $value) {
                    $totals[$key] = ($totals[$key] ?? 0) + (int) $value;
                }
            }

            $careerTotals = [
                'matches_with_stats' => $stats->count(),
                'stat_totals' => $totals,
                'total_points' => $totals['points'] ?? 0,
            ];
        }

        return view('player.results.index', compact('team', 'matchTeams', 'playerProfile', 'careerTotals'));
    }
}
