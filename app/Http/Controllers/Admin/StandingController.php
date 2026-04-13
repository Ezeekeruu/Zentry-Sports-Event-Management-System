<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StandingController extends Controller
{
    public function index(Request $request): View
    {
        $tournament = null;
        $standings  = collect();

        $tournamentId = $request->tournament_id;
        $tournaments  = Tournament::where('is_active', true)
            ->whereIn('status', ['ongoing', 'completed'])
            ->with('sport')
            ->orderBy('tournament_name')
            ->get();

        if ($tournamentId) {
            $tournament = Tournament::with(['sport', 'matches.matchTeams.team', 'matches.matchTeams.result'])->findOrFail($tournamentId);

            // Aggregate standings from match_teams
            $teamStats = [];
            foreach ($tournament->matches as $match) {
                if ($match->status !== 'completed') continue;
                foreach ($match->matchTeams as $mt) {
                    $tid = $mt->team_id;
                    if (!isset($teamStats[$tid])) {
                        $teamStats[$tid] = [
                            'team'           => $mt->team,
                            'matches_played' => 0,
                            'wins'           => 0,
                            'losses'         => 0,
                            'total_points'   => 0,
                        ];
                    }
                    $teamStats[$tid]['matches_played']++;
                    $teamStats[$tid]['total_points'] += $mt->points_scored ?? 0;
                    if ($mt->rank_position === 1) {
                        $teamStats[$tid]['wins']++;
                    } else {
                        $teamStats[$tid]['losses']++;
                    }
                }
            }

            $standings = collect($teamStats)
                ->sortByDesc('wins')
                ->values();
        }

        return view('admin.standings.index', compact('tournaments', 'tournament', 'standings'));
    }
}
