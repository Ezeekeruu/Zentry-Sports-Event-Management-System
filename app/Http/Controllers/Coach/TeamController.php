<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function show(): View
    {
        $team = Team::where('coach_id', auth()->id())
            ->with(['sport', 'playerProfiles.user', 'tournaments'])
            ->first();

        return view('coach.team.show', compact('team'));
    }

    public function edit(): View
    {
        $team = Team::where('coach_id', auth()->id())->firstOrFail();
        return view('coach.team.edit', compact('team'));
    }

    public function update(Request $request): RedirectResponse
    {
        $team = Team::where('coach_id', auth()->id())->firstOrFail();

        $request->validate([
            'team_name'  => ['required', 'string', 'max:255', 'unique:teams,team_name,' . $team->id],
            'logo_url'   => ['nullable', 'url', 'max:500'],
            'founded_at' => ['nullable', 'date'],
        ]);

        $team->update($request->only('team_name', 'logo_url', 'founded_at'));

        return redirect()->route('coach.team.show')->with('success', 'Team updated successfully.');
    }
}
