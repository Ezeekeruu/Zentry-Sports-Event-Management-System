<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TournamentController extends Controller
{
    public function index(Request $request): View
    {
        $tournaments = Tournament::with(['sport', 'organizer'])
            ->where('is_active', true)
            ->withCount('registrations', 'matches')
            ->when($request->search, fn($q) => $q->where('tournament_name', 'like', '%' . $request->search . '%'))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('public.tournaments.index', compact('tournaments'));
    }

    public function show(Tournament $tournament): View
    {
        $tournament->load(['sport', 'organizer', 'registrations.team', 'matches.matchTeams.team']);
        return view('public.tournaments.show', compact('tournament'));
    }
}
