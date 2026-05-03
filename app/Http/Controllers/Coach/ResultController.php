<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\MatchTeam;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class ResultController extends Controller
{
    public function index(Request $request): View
    {
        $team = Team::where('coach_id', auth()->id())->first();

        if (!$team) {
            $matchTeams  = new LengthAwarePaginator([], 0, 15);
            $tournaments = collect();
            return view('coach.results.index', compact('team', 'matchTeams', 'tournaments'));
        }

        $tournaments = Tournament::whereHas('matches.matchTeams', fn($q) => $q->where('team_id', $team->id))
            ->orderBy('tournament_name')
            ->get();

        $matchTeams = MatchTeam::with(['match.tournament', 'match.matchTeams.team', 'result'])
            ->where('team_id', $team->id)
            ->whereHas('match', fn($q) => $q->where('status', 'completed'))
            ->when($request->tournament_id, fn($q) => $q->whereHas('match', fn($q2) => $q2->where('tournament_id', $request->tournament_id)))
            ->when($request->outcome, function ($q) use ($request) {
                if ($request->outcome === 'win')  $q->where('rank_position', 1);
                if ($request->outcome === 'loss') $q->where('rank_position', '>', 1);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('coach.results.index', compact('team', 'matchTeams', 'tournaments'));
    }
}