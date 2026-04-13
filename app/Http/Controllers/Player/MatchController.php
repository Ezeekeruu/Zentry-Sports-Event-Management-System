<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use App\Models\ZentryMatch;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class MatchController extends Controller
{
    public function index(Request $request): View
    {
        $team = auth()->user()->playerProfile?->team;

        $matches = $team
            ? ZentryMatch::whereHas('matchTeams', fn($q) => $q->where('team_id', $team->id))
                ->with(['tournament', 'matchTeams.team'])
                ->when($request->status, fn($q) => $q->where('status', $request->status))
                ->orderByDesc('match_date')
                ->paginate(15)
                ->withQueryString()
            : new LengthAwarePaginator([], 0, 15);

        return view('player.matches.index', compact('team', 'matches'));
    }
}
