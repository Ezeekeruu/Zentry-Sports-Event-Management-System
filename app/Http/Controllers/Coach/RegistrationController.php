<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function index(Request $request): View
    {
        $team = Team::where('coach_id', $request->user()->id)
            ->with('sport')
            ->first();

        $availableTournaments = collect();
        $registrations = collect();

        if ($team) {
            $availableTournaments = Tournament::with(['sport', 'organizer'])
                ->whereIn('status', ['upcoming', 'ongoing'])
                ->where('sport_id', $team->sport_id)
                ->orderBy('start_date')
                ->get()
                ->filter(function (Tournament $tournament) {
                    $approvedCount = Registration::where('tournament_id', $tournament->id)
                        ->where('status', 'approved')
                        ->count();

                    return $approvedCount < $tournament->max_teams;
                })
                ->values();

            $registrations = Registration::with(['tournament.sport', 'tournament.organizer'])
                ->where('team_id', $team->id)
                ->latest()
                ->get();
        }

        return view('coach.registrations.index', compact('team', 'availableTournaments', 'registrations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $team = Team::where('coach_id', $request->user()->id)->first();

        if (! $team) {
            return back()->with('error', 'No team assigned to your account.');
        }

        $validated = $request->validate([
            'tournament_id' => ['required', 'integer', 'exists:tournaments,id'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $tournament = Tournament::findOrFail($validated['tournament_id']);

        if (! in_array($tournament->status, ['upcoming', 'ongoing'], true)) {
            return back()->with('error', 'You can only register to upcoming or ongoing tournaments.');
        }

        if ((int) $tournament->sport_id !== (int) $team->sport_id) {
            return back()->with('error', 'Your team sport does not match this tournament.');
        }

        $alreadyRegistered = Registration::where('team_id', $team->id)
            ->where('tournament_id', $tournament->id)
            ->exists();

        if ($alreadyRegistered) {
            return back()->with('error', 'Your team is already registered in this tournament.');
        }

        $approvedCount = Registration::where('tournament_id', $tournament->id)
            ->where('status', 'approved')
            ->count();

        if ($approvedCount >= $tournament->max_teams) {
            return back()->with('error', 'This tournament is already full.');
        }

        Registration::create([
            'team_id' => $team->id,
            'tournament_id' => $tournament->id,
            'registration_date' => today(),
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('coach.registrations.index')
            ->with('success', 'Registration request submitted. Waiting for organizer approval.');
    }
}
