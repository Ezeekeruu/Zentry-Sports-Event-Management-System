<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\ZentryMatch;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MatchController extends Controller
{
    public function index(Request $request): View
    {
        $team = Team::where('coach_id', auth()->id())->firstOrFail();

        $matches = ZentryMatch::whereHas('matchTeams', fn($q) => $q->where('team_id', $team->id))
            ->with(['tournament', 'matchTeams.team', 'matchTeams.result'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderByDesc('match_date')
            ->paginate(15)
            ->withQueryString();

        return view('coach.matches.index', compact('team', 'matches'));
    }

    public function show(ZentryMatch $match): View
    {
        $team = Team::where('coach_id', auth()->id())->firstOrFail();
        $match->load(['tournament', 'matchTeams.team', 'matchTeams.result']);
        return view('coach.matches.show', compact('match', 'team'));
    }
}
