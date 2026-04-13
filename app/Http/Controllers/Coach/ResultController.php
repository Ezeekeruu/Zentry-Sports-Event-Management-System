<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\MatchTeam;
use Illuminate\View\View;

class ResultController extends Controller
{
    public function index(): View
    {
        $team = Team::where('coach_id', auth()->id())->firstOrFail();

        $matchTeams = MatchTeam::with(['match.tournament', 'result'])
            ->where('team_id', $team->id)
            ->whereHas('match', fn($q) => $q->where('status', 'completed'))
            ->latest()
            ->paginate(15);

        return view('coach.results.index', compact('team', 'matchTeams'));
    }
}
