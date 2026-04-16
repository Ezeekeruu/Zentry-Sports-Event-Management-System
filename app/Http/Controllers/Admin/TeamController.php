<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\Sport;
use App\Models\User;
use App\Models\PlayerProfile;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = (int) $request->get('per_page', 15);
        $perPage = in_array($perPage, [5, 10, 15, 25, 50]) ? $perPage : 15;

        $teams = Team::with(['sport', 'coach'])
            ->withCount('playerProfiles')
            ->when($request->search, function ($q) use ($request) {
                $q->where('team_name', 'like', '%' . $request->search . '%');
            })
            ->when($request->sport_id, function ($q) use ($request) {
                $q->where('sport_id', $request->sport_id);
            })
            ->orderByDesc('is_active')
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        $sports = Sport::active()->orderBy('sport_name')->get();

        return view('admin.teams.index', compact('teams', 'sports'));
    }

    public function create(): View
    {
        $sports = Sport::active()->orderBy('sport_name')->get();
        $coaches = User::where('role', 'coach')->where('is_active', true)->orderBy('first_name')->get();

        return view('admin.teams.create', compact('sports', 'coaches'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'team_name'  => ['required', 'string', 'max:255', 'unique:teams,team_name'],
            'sport_id'   => ['required', 'exists:sports,id'],
            'coach_id'   => ['required', 'exists:users,id'],
            'logo'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
            'founded_at' => ['nullable', 'date'],
        ]);

        // Ensure coach doesn't already lead another team
        if (Team::where('coach_id', $request->coach_id)->where('is_active', true)->exists()) {
            return back()->withInput()->withErrors(['coach_id' => 'This coach already leads another active team.']);
        }

        $logoPath = $request->hasFile('logo')
            ? $request->file('logo')->store('team-logos', 'public')
            : null;

        Team::create([
            'team_name'  => $request->team_name,
            'sport_id'   => $request->sport_id,
            'coach_id'   => $request->coach_id,
            'logo_url'   => $logoPath,
            'founded_at' => $request->founded_at,
            'is_active'  => true,
        ]);

        return redirect()->route('admin.teams.index')->with('success', 'Team created successfully.');
    }

    public function edit(Team $team): View
    {
        $sports  = Sport::active()->orderBy('sport_name')->get();
        $coaches = User::where('role', 'coach')->where('is_active', true)->orderBy('first_name')->get();

        return view('admin.teams.edit', compact('team', 'sports', 'coaches'));
    }

    public function update(Request $request, Team $team): RedirectResponse
    {
        $request->validate([
            'team_name'  => ['required', 'string', 'max:255', 'unique:teams,team_name,' . $team->id],
            'sport_id'   => ['required', 'exists:sports,id'],
            'coach_id'   => ['required', 'exists:users,id'],
            'logo'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
            'founded_at' => ['nullable', 'date'],
        ]);

        // Check coach conflict (excluding this team)
        if (Team::where('coach_id', $request->coach_id)->where('id', '!=', $team->id)->where('is_active', true)->exists()) {
            return back()->withInput()->withErrors(['coach_id' => 'This coach already leads another active team.']);
        }

        $logoPath = $team->logo_url;
        if ($request->hasFile('logo')) {
            if ($team->logo_url && ! filter_var($team->logo_url, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($team->logo_url);
            }

            $logoPath = $request->file('logo')->store('team-logos', 'public');
        }

        $team->update([
            'team_name'  => $request->team_name,
            'sport_id'   => $request->sport_id,
            'coach_id'   => $request->coach_id,
            'logo_url'   => $logoPath,
            'founded_at' => $request->founded_at,
            'is_active'  => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.teams.index')->with('success', 'Team updated successfully.');
    }

    public function destroy(Team $team): RedirectResponse
    {
        $team->update(['is_active' => false]);
        return redirect()->route('admin.teams.index')->with('success', "\"{$team->team_name}\" has been archived.");
    }

    public function restore(Team $team): RedirectResponse
    {
        $team->update(['is_active' => true]);
        return redirect()->route('admin.teams.index')->with('success', "\"{$team->team_name}\" has been restored.");
    }

    public function players(Team $team): View
    {
        $team->load(['sport', 'coach', 'playerProfiles.user']);
        $availablePlayers = User::where('role', 'player')
            ->where('is_active', true)
            ->whereDoesntHave('playerProfile', fn($q) => $q->whereNotNull('team_id'))
            ->orderBy('first_name')
            ->get();

        return view('admin.teams.players', compact('team', 'availablePlayers'));
    }

    public function addPlayer(Request $request, Team $team): RedirectResponse
    {
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

    public function removePlayer(Team $team, PlayerProfile $player): RedirectResponse
    {
        $player->update(['team_id' => null]);
        return back()->with('success', 'Player removed from team.');
    }
}
