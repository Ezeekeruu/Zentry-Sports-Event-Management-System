<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use App\Models\PlayerMatchStat;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        $user = auth()->user();
        $profile = $user->playerProfile;
        $team = $profile?->team;

        $matchStats = $profile
            ? PlayerMatchStat::with(['matchTeam.match.tournament'])
                ->where('player_profile_id', $profile->id)
                ->whereHas('matchTeam.match', fn($q) => $q->where('status', 'completed'))
                ->orderByDesc('id')
                ->get()
            : collect();

        $overallStats = [
            'matches_played' => $matchStats->count(),
            'wins' => $matchStats->filter(fn($row) => $row->matchTeam?->rank_position === 1)->count(),
            'podiums' => $matchStats->filter(function ($row) {
                $rank = $row->matchTeam?->rank_position;
                return $rank && $rank <= 3;
            })->count(),
            'total_points' => $matchStats->sum(fn($row) => (int) ($row->points ?? 0)),
        ];
        $overallStats['avg_points'] = $overallStats['matches_played'] > 0
            ? round($overallStats['total_points'] / $overallStats['matches_played'], 2)
            : 0;

        $tournamentStats = $matchStats
            ->groupBy(fn($row) => $row->matchTeam->match->tournament_id)
            ->map(function ($rows) {
                $first = $rows->first();
                $matchesPlayed = $rows->count();
                $wins = $rows->filter(fn($item) => $item->matchTeam?->rank_position === 1)->count();
                $totalPoints = $rows->sum(fn($item) => (int) ($item->points ?? 0));

                return [
                    'tournament' => $first->matchTeam->match->tournament,
                    'matches_played' => $matchesPlayed,
                    'wins' => $wins,
                    'total_points' => $totalPoints,
                    'win_rate' => $matchesPlayed > 0 ? round(($wins / $matchesPlayed) * 100) : 0,
                ];
            })
            ->sortByDesc('total_points')
            ->values();

        return view('player.profile.edit', compact('user', 'team', 'matchStats', 'overallStats', 'tournamentStats'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'unique:users,email,' . $user->id],
            'password'   => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('player.profile.edit')->with('success', 'Profile updated successfully.');
    }
}
