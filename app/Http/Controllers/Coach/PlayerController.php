<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\User;
use App\Models\PlayerProfile;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PlayerController extends Controller
{
    public function index(): View
    {
        $team = Team::where('coach_id', auth()->id())
            ->with(['playerProfiles.user'])
            ->firstOrFail();

        $availablePlayers = User::where('role', 'player')
            ->where('is_active', true)
            ->whereDoesntHave('playerProfile', fn($q) => $q->whereNotNull('team_id'))
            ->orderBy('first_name')
            ->get();

        return view('coach.players.index', compact('team', 'availablePlayers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $team = Team::where('coach_id', auth()->id())->firstOrFail();

        $request->validate(['user_id' => ['required', 'exists:users,id']]);

        $user = User::findOrFail($request->user_id);

        if ($user->playerProfile && $user->playerProfile->team_id) {
            return back()->with('error', 'This player already belongs to a team.');
        }

        PlayerProfile::updateOrCreate(
            ['user_id' => $user->id],
            ['team_id' => $team->id]
        );

        return back()->with('success', "{$user->first_name} {$user->last_name} added to the team.");
    }

    public function destroy(PlayerProfile $player): RedirectResponse
    {
        $team = Team::where('coach_id', auth()->id())->firstOrFail();

        if ($player->team_id !== $team->id) {
            abort(403);
        }

        $player->update(['team_id' => null]);

        return back()->with('success', 'Player removed from team.');
    }
}
