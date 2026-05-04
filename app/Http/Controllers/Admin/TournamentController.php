<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Models\Sport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TournamentController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = (int) $request->get('per_page', 15);
        $perPage = in_array($perPage, [5, 10, 15, 25, 50]) ? $perPage : 15;

        $tournaments = Tournament::with(['sport', 'organizer'])
            ->withCount('registrations', 'matches')
            ->when($request->search, function ($q) use ($request) {
                $q->where('tournament_name', 'like', '%' . $request->search . '%');
            })
            ->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->sport_id, function ($q) use ($request) {
                $q->where('sport_id', $request->sport_id);
            })
            ->orderByDesc('is_active')
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        $sports = Sport::active()->orderBy('sport_name')->get();

        return view('admin.tournaments.index', compact('tournaments', 'sports'));
    }

    public function create(): View
    {
        $sports     = Sport::active()->orderBy('sport_name')->get();
        $organizers = User::where('role', 'organizer')->where('is_active', true)->orderBy('first_name')->get();

        return view('admin.tournaments.create', compact('sports', 'organizers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'tournament_name' => ['required', 'string', 'max:255'],
            'organizer_id'    => ['required', 'exists:users,id'],
            'sport_id'        => ['required', 'exists:sports,id'],
            'start_date'      => ['required', 'date'],
            'end_date'        => ['required', 'date', 'after_or_equal:start_date'],
            'status'          => ['required', 'in:upcoming,ongoing,completed'],
            'max_teams'       => ['nullable', 'integer', 'min:2'],
        ]);

        Tournament::create([
            'tournament_name' => $request->tournament_name,
            'organizer_id'    => $request->organizer_id,
            'sport_id'        => $request->sport_id,
            'start_date'      => $request->start_date,
            'end_date'        => $request->end_date,
            'status'          => $request->status,
            'max_teams'       => $request->max_teams,
            'is_active'       => true,
        ]);

        return redirect()->route('admin.tournaments.index')->with('success', 'Tournament created successfully.');
    }

    public function show(Tournament $tournament): View
    {
        $tournament->load([
            'sport',
            'organizer',
            'registrations.team',
            'matches.matchTeams.team',
            'matches.matchTeams.result',
        ]);

        $standings = $this->buildStandings($tournament);

        return view('admin.tournaments.show', compact('tournament', 'standings'));
    }

    public function edit(Tournament $tournament): View
    {
        $sports     = Sport::active()->orderBy('sport_name')->get();
        $organizers = User::where('role', 'organizer')->where('is_active', true)->orderBy('first_name')->get();

        return view('admin.tournaments.edit', compact('tournament', 'sports', 'organizers'));
    }

    public function update(Request $request, Tournament $tournament): RedirectResponse
    {
        $request->validate([
            'tournament_name' => ['required', 'string', 'max:255'],
            'organizer_id'    => ['required', 'exists:users,id'],
            'sport_id'        => ['required', 'exists:sports,id'],
            'start_date'      => ['required', 'date'],
            'end_date'        => ['required', 'date', 'after_or_equal:start_date'],
            'status'          => ['required', 'in:upcoming,ongoing,completed'],
            'max_teams'       => ['nullable', 'integer', 'min:2'],
        ]);

        $tournament->update([
            'tournament_name' => $request->tournament_name,
            'organizer_id'    => $request->organizer_id,
            'sport_id'        => $request->sport_id,
            'start_date'      => $request->start_date,
            'end_date'        => $request->end_date,
            'status'          => $request->status,
            'max_teams'       => $request->max_teams,
            'is_active'       => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.tournaments.index')->with('success', 'Tournament updated successfully.');
    }

    public function destroy(Tournament $tournament): RedirectResponse
    {
        $tournament->update(['is_active' => false]);
        return redirect()->route('admin.tournaments.index')->with('success', "\"{$tournament->tournament_name}\" has been archived.");
    }

    public function restore(Tournament $tournament): RedirectResponse
    {
        $tournament->update(['is_active' => true]);
        return redirect()->route('admin.tournaments.index')->with('success', "\"{$tournament->tournament_name}\" has been restored.");
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