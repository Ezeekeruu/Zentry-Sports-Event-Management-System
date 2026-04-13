<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\ZentryMatch;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $coach = auth()->user();
        $team  = Team::where('coach_id', $coach->id)->with(['sport', 'playerProfiles.user'])->first();

        $upcomingMatches = $team
            ? ZentryMatch::whereHas('matchTeams', fn($q) => $q->where('team_id', $team->id))
                ->where('status', 'scheduled')
                ->with('tournament')
                ->orderBy('match_date')
                ->take(5)
                ->get()
            : collect();

        return view('coach.dashboard', compact('coach', 'team', 'upcomingMatches'));
    }
}
