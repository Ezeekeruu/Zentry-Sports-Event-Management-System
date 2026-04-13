<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Models\MatchTeam;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StandingController extends Controller
{
    public function index(Request $request): View
    {
        $tournament = null;
        $standings  = collect();

        $tournaments = Tournament::where('is_active', true)
            ->whereIn('status', ['ongoing', 'completed'])
            ->with('sport')
            ->orderBy('tournament_name')
            ->get();

        if ($request->tournament_id) {
            $tournament = Tournament::with(['sport', 'matches.matchTeams.team'])->findOrFail($request->tournament_id);

            $teamStats = [];
            foreach ($tournament->matches as $match) {
                if ($match->status !== 'completed') continue;
                foreach ($match->matchTeams as $mt) {
                    $tid = $mt->team_id;
                    if (!isset($teamStats[$tid])) {
                        $teamStats[$tid] = ['team' => $mt->team, 'matches_played' => 0, 'wins' => 0, 'losses' => 0, 'total_points' => 0];
                    }
                    $teamStats[$tid]['matches_played']++;
                    $teamStats[$tid]['total_points'] += $mt->points_scored ?? 0;
                    if ($mt->rank_position === 1) $teamStats[$tid]['wins']++;
                    else $teamStats[$tid]['losses']++;
                }
            }
            $standings = collect($teamStats)->sortByDesc('wins')->values();
        }

        return view('public.standings.index', compact('tournaments', 'tournament', 'standings'));
    }
}
