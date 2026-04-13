<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use App\Models\ZentryMatch;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user    = auth()->user();
        $profile = $user->playerProfile?->load(['team.sport', 'team.coach']);
        $team    = $profile?->team;

        $upcomingMatches = $team
            ? ZentryMatch::whereHas('matchTeams', fn($q) => $q->where('team_id', $team->id))
                ->where('status', 'scheduled')
                ->with('tournament')
                ->orderBy('match_date')
                ->take(5)
                ->get()
            : collect();

        return view('player.dashboard', compact('user', 'profile', 'team', 'upcomingMatches'));
    }
}
