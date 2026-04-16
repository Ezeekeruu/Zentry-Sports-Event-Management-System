<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
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
            'logo'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
            'founded_at' => ['nullable', 'date'],
        ]);

        $logoPath = $team->logo_url;
        if ($request->hasFile('logo')) {
            if ($team->logo_url && ! filter_var($team->logo_url, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($team->logo_url);
            }

            $logoPath = $request->file('logo')->store('team-logos', 'public');
        }

        $team->update([
            'team_name'  => $request->team_name,
            'logo_url'   => $logoPath,
            'founded_at' => $request->founded_at,
        ]);

        return redirect()->route('coach.team.show')->with('success', 'Team updated successfully.');
    }
}
