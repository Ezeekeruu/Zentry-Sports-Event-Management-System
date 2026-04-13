<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\ZentryMatch;
use App\Models\Tournament;
use App\Models\Team;
use App\Models\MatchTeam;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MatchController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [5, 10, 15, 25]) ? $perPage : 10;

        $matches = ZentryMatch::with(['tournament', 'matchTeams.team'])
            ->whereHas('tournament', fn($q) => $q->where('organizer_id', auth()->id()))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderByDesc('match_date')
            ->paginate($perPage)
            ->withQueryString();

        return view('organizer.matches.index', compact('matches'));
    }

    public function create(): View
    {
        $tournaments = Tournament::where('organizer_id', auth()->id())
            ->whereIn('status', ['upcoming', 'ongoing'])
            ->with('sport')->orderBy('tournament_name')->get();
        $teams = Team::where('is_active', true)->orderBy('team_name')->get();
        return view('organizer.matches.create', compact('tournaments', 'teams'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'tournament_id' => ['required', 'exists:tournaments,id'],
            'match_date'    => ['required', 'date'],
            'match_time'    => ['nullable', 'date_format:H:i'],
            'venue'         => ['nullable', 'string', 'max:255'],
            'status'        => ['required', 'in:scheduled,live,completed'],
            'round_name'    => ['nullable', 'string', 'max:100'],
            'team_ids'      => ['required', 'array', 'min:2'],
            'team_ids.*'    => ['exists:teams,id'],
        ]);

        $tournament = Tournament::findOrFail($request->tournament_id);
        if ($tournament->organizer_id !== auth()->id()) abort(403);

        $match = ZentryMatch::create([
            'tournament_id' => $request->tournament_id,
            'match_date'    => $request->match_date,
            'match_time'    => $request->match_time,
            'venue'         => $request->venue,
            'status'        => $request->status,
            'round_name'    => $request->round_name,
            'is_active'     => true,
        ]);

        foreach ($request->team_ids as $teamId) {
            MatchTeam::create(['match_id' => $match->id, 'team_id' => $teamId]);
        }

        return redirect()->route('organizer.matches.index')->with('success', 'Match created successfully.');
    }

    public function edit(ZentryMatch $match): View
    {
        if ($match->tournament->organizer_id !== auth()->id()) abort(403);
        $match->load(['tournament', 'matchTeams.team']);
        $tournaments = Tournament::where('organizer_id', auth()->id())->with('sport')->get();
        $teams = Team::where('is_active', true)->orderBy('team_name')->get();
        $selectedTeamIds = $match->matchTeams->pluck('team_id')->toArray();
        return view('organizer.matches.edit', compact('match', 'tournaments', 'teams', 'selectedTeamIds'));
    }

    public function update(Request $request, ZentryMatch $match): RedirectResponse
    {
        if ($match->tournament->organizer_id !== auth()->id()) abort(403);

        $request->validate([
            'match_date'  => ['required', 'date'],
            'match_time'  => ['nullable', 'date_format:H:i'],
            'venue'       => ['nullable', 'string', 'max:255'],
            'status'      => ['required', 'in:scheduled,live,completed'],
            'round_name'  => ['nullable', 'string', 'max:100'],
            'team_ids'    => ['required', 'array', 'min:2'],
            'team_ids.*'  => ['exists:teams,id'],
        ]);

        $match->update($request->only('match_date', 'match_time', 'venue', 'status', 'round_name'));

        $match->matchTeams()->whereNotIn('team_id', $request->team_ids)->delete();
        foreach ($request->team_ids as $teamId) {
            MatchTeam::firstOrCreate(['match_id' => $match->id, 'team_id' => $teamId]);
        }

        return redirect()->route('organizer.matches.index')->with('success', 'Match updated.');
    }

    public function destroy(ZentryMatch $match): RedirectResponse
    {
        if ($match->tournament->organizer_id !== auth()->id()) abort(403);
        $match->update(['is_active' => false]);
        return redirect()->route('organizer.matches.index')->with('success', 'Match archived.');
    }
}
