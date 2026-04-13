<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use App\Models\MatchTeam;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class ResultController extends Controller
{
    public function index(): View
    {
        $team = auth()->user()->playerProfile?->team;

        $matchTeams = $team
            ? MatchTeam::with(['match.tournament', 'result'])
                ->where('team_id', $team->id)
                ->whereHas('match', fn($q) => $q->where('status', 'completed'))
                ->latest()
                ->paginate(15)
            : new LengthAwarePaginator([], 0, 15);

        return view('player.results.index', compact('team', 'matchTeams'));
    }
}
