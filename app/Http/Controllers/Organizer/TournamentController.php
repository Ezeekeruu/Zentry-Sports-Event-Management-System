<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Models\Sport;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TournamentController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [5, 10, 15, 25]) ? $perPage : 10;

        $tournaments = Tournament::where('organizer_id', auth()->id())
            ->with(['sport'])
            ->withCount('registrations', 'matches')
            ->when($request->search, fn($q) => $q->where('tournament_name', 'like', '%' . $request->search . '%'))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderByDesc('is_active')
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('organizer.tournaments.index', compact('tournaments'));
    }

    public function create(): View
    {
        $sports = Sport::active()->orderBy('sport_name')->get();
        return view('organizer.tournaments.create', compact('sports'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'tournament_name' => ['required', 'string', 'max:255'],
            'sport_id'        => ['required', 'exists:sports,id'],
            'start_date'      => ['required', 'date'],
            'end_date'        => ['required', 'date', 'after_or_equal:start_date'],
            'status'          => ['required', 'in:upcoming,ongoing,completed'],
            'max_teams'       => ['nullable', 'integer', 'min:2'],
        ]);

        Tournament::create([
            'tournament_name' => $request->tournament_name,
            'organizer_id'    => auth()->id(),
            'sport_id'        => $request->sport_id,
            'start_date'      => $request->start_date,
            'end_date'        => $request->end_date,
            'status'          => $request->status,
            'max_teams'       => $request->max_teams,
            'is_active'       => true,
        ]);

        return redirect()->route('organizer.tournaments.index')->with('success', 'Tournament created successfully.');
    }

    public function show(Tournament $tournament): View
    {
        $this->authorizeOrganizer($tournament);

        $tournament->load([
            'sport',
            'registrations.team',
            'matches.matchTeams.team',
            'matches.matchTeams.result',
        ]);

        $standings = $this->buildStandings($tournament);

        return view('organizer.tournaments.show', compact('tournament', 'standings'));
    }

    public function edit(Tournament $tournament): View
    {
        $this->authorizeOrganizer($tournament);
        $sports = Sport::active()->orderBy('sport_name')->get();
        return view('organizer.tournaments.edit', compact('tournament', 'sports'));
    }

    public function update(Request $request, Tournament $tournament): RedirectResponse
    {
        $this->authorizeOrganizer($tournament);

        $request->validate([
            'tournament_name' => ['required', 'string', 'max:255'],
            'sport_id'        => ['required', 'exists:sports,id'],
            'start_date'      => ['required', 'date'],
            'end_date'        => ['required', 'date', 'after_or_equal:start_date'],
            'status'          => ['required', 'in:upcoming,ongoing,completed'],
            'max_teams'       => ['nullable', 'integer', 'min:2'],
        ]);

        $tournament->update($request->only('tournament_name', 'sport_id', 'start_date', 'end_date', 'status', 'max_teams'));

        return redirect()->route('organizer.tournaments.index')->with('success', 'Tournament updated.');
    }

    public function destroy(Tournament $tournament): RedirectResponse
    {
        $this->authorizeOrganizer($tournament);
        $tournament->update(['is_active' => false]);
        return redirect()->route('organizer.tournaments.index')->with('success', 'Tournament archived.');
    }

    private function authorizeOrganizer(Tournament $tournament): void
    {
        if ($tournament->organizer_id !== auth()->id()) {
            abort(403);
        }
    }

    private function buildStandings(Tournament $tournament): \Illuminate\Support\Collection
    {
        $teamStats = [];

        foreach ($tournament->matches as $match) {
            if ($match->status !== 'completed') continue;

            $matchTeams = $match->matchTeams->values();
            $hasScores  = $matchTeams->every(fn($mt) => $mt->points_scored !== null);
            $maxScore   = $hasScores ? $matchTeams->max('points_scored') : null;

            foreach ($matchTeams as $mt) {
                $tid = $mt->team_id;
                if (!isset($teamStats[$tid])) {
                    $teamStats[$tid] = [
                        'team'           => $mt->team,
                        'matches_played' => 0,
                        'wins'           => 0,
                        'draws'          => 0,
                        'losses'         => 0,
                        'total_points'   => 0,
                    ];
                }

                $teamStats[$tid]['matches_played']++;
                $teamStats[$tid]['total_points'] += $mt->points_scored ?? 0;

                if ($hasScores) {
                    $leaders = $matchTeams->filter(fn($m) => (int)$m->points_scored === (int)$maxScore);

                    if ($leaders->count() === 1 && (int)$mt->points_scored === (int)$maxScore)
                        $teamStats[$tid]['wins']++;
                    elseif ($leaders->count() > 1 && (int)$mt->points_scored === (int)$maxScore)
                        $teamStats[$tid]['draws']++;
                    else
                        $teamStats[$tid]['losses']++;
                }
            }
        }

        return collect($teamStats)->sortByDesc('total_points')->sortByDesc('wins')->values();
    }
}