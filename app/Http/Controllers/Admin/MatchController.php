<?php

namespace App\Http\Controllers\Admin;

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
        $perPage = (int) $request->get('per_page', 15);
        $perPage = in_array($perPage, [5, 10, 15, 25, 50]) ? $perPage : 15;

        $matches = ZentryMatch::with(['tournament.sport', 'matchTeams.team'])
            ->when($request->search, function ($q) use ($request) {
                $q->where('venue', 'like', '%' . $request->search . '%')
                  ->orWhere('round_name', 'like', '%' . $request->search . '%')
                  ->orWhereHas('tournament', fn($t) => $t->where('tournament_name', 'like', '%' . $request->search . '%'));
            })
            ->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->tournament_id, function ($q) use ($request) {
                $q->where('tournament_id', $request->tournament_id);
            })
            ->orderByDesc('is_active')
            ->orderByDesc('match_date')
            ->paginate($perPage)
            ->withQueryString();

        $tournaments = Tournament::where('is_active', true)->orderBy('tournament_name')->get();

        return view('admin.matches.index', compact('matches', 'tournaments'));
    }

    public function create(): View
    {
        $tournaments = Tournament::with('sport')->where('is_active', true)
            ->whereIn('status', ['upcoming', 'ongoing'])
            ->orderBy('tournament_name')->get();
        $teams = Team::where('is_active', true)->with('sport')->orderBy('team_name')->get();

        return view('admin.matches.create', compact('tournaments', 'teams'));
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

        return redirect()->route('admin.matches.index')->with('success', 'Match created successfully.');
    }

    public function edit(ZentryMatch $match): View
    {
        $match->load(['tournament', 'matchTeams.team']);
        $tournaments = Tournament::with('sport')->where('is_active', true)->orderBy('tournament_name')->get();
        $teams       = Team::where('is_active', true)->with('sport')->orderBy('team_name')->get();
        $selectedTeamIds = $match->matchTeams->pluck('team_id')->toArray();

        return view('admin.matches.edit', compact('match', 'tournaments', 'teams', 'selectedTeamIds'));
    }

    public function update(Request $request, ZentryMatch $match): RedirectResponse
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

        $match->update([
            'tournament_id' => $request->tournament_id,
            'match_date'    => $request->match_date,
            'match_time'    => $request->match_time,
            'venue'         => $request->venue,
            'status'        => $request->status,
            'round_name'    => $request->round_name,
            'is_active'     => $request->boolean('is_active'),
        ]);

        // Sync teams
        $match->matchTeams()->whereNotIn('team_id', $request->team_ids)->delete();
        foreach ($request->team_ids as $teamId) {
            MatchTeam::firstOrCreate(['match_id' => $match->id, 'team_id' => $teamId]);
        }

        return redirect()->route('admin.matches.index')->with('success', 'Match updated successfully.');
    }

    public function destroy(ZentryMatch $match): RedirectResponse
    {
        $match->update(['is_active' => false]);
        return redirect()->route('admin.matches.index')->with('success', 'Match has been archived.');
    }

    public function restore(ZentryMatch $match): RedirectResponse
    {
        $match->update(['is_active' => true]);
        return redirect()->route('admin.matches.index')->with('success', 'Match has been restored.');
    }
}
