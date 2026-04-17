<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ZentryMatch;
use App\Models\Tournament;
use App\Models\Team;
use App\Models\MatchTeam;
use App\Models\PlayerMatchStat;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
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
        $tournaments = Tournament::with(['sport', 'registrations' => function ($query) {
                $query->where('status', 'approved');
            }])
            ->where('is_active', true)
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

        $this->assertTeamsAreAllowedForTournament((int) $request->tournament_id, $request->team_ids ?? []);

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
        $tournaments = Tournament::with([
                'sport',
                'registrations' => function ($query) {
                    $query->where('status', 'approved');
                },
            ])
            ->where('is_active', true)
            ->orderBy('tournament_name')
            ->get();
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

        $this->assertTeamsAreAllowedForTournament((int) $request->tournament_id, $request->team_ids ?? []);

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

    public function winnerEdit(ZentryMatch $match): View
    {
        $match->load([
            'tournament.sport',
            'matchTeams.team.playerProfiles.user',
            'matchTeams.playerStats',
        ]);

        $currentWinnerTeamId = $match->matchTeams->firstWhere('rank_position', 1)?->team_id;
        $existingStats = PlayerMatchStat::whereIn('match_team_id', $match->matchTeams->pluck('id'))
            ->get()
            ->keyBy(fn ($row) => $row->match_team_id . '-' . $row->player_profile_id);

        return view('admin.matches.winner', compact('match', 'currentWinnerTeamId', 'existingStats'));
    }

    public function winnerUpdate(Request $request, ZentryMatch $match): RedirectResponse
    {
        $match->load(['matchTeams.team.playerProfiles']);

        $request->validate([
            'winner_team_id' => ['required', 'integer', 'exists:teams,id'],
            'team_points' => ['nullable', 'array'],
            'team_points.*' => ['nullable', 'integer', 'min:0'],
            'player_points' => ['nullable', 'array'],
            'player_points.*' => ['nullable', 'array'],
            'player_points.*.*' => ['nullable', 'integer', 'min:0'],
        ]);

        $validTeamIds = $match->matchTeams->pluck('team_id')->map(fn ($id) => (int) $id)->all();
        $winnerTeamId = (int) $request->input('winner_team_id');
        if (! in_array($winnerTeamId, $validTeamIds, true)) {
            throw ValidationException::withMessages([
                'winner_team_id' => 'Winner must be one of the participating teams.',
            ]);
        }

        $teamPoints = $request->input('team_points', []);
        $playerPoints = $request->input('player_points', []);

        foreach ($match->matchTeams as $matchTeam) {
            $teamId = (int) $matchTeam->team_id;
            $matchTeam->update([
                'rank_position' => $teamId === $winnerTeamId ? 1 : 2,
                'points_scored' => array_key_exists($teamId, $teamPoints) && $teamPoints[$teamId] !== ''
                    ? (int) $teamPoints[$teamId]
                    : $matchTeam->points_scored,
            ]);

            $allowedPlayerIds = $matchTeam->team->playerProfiles->pluck('id')->map(fn ($id) => (int) $id)->all();
            $rows = $playerPoints[$teamId] ?? [];

            foreach ($rows as $playerProfileId => $value) {
                $playerProfileId = (int) $playerProfileId;
                if (! in_array($playerProfileId, $allowedPlayerIds, true)) {
                    continue;
                }

                if ($value === '' || $value === null) {
                    PlayerMatchStat::where('match_team_id', $matchTeam->id)
                        ->where('player_profile_id', $playerProfileId)
                        ->delete();
                    continue;
                }

                $points = (int) $value;
                PlayerMatchStat::updateOrCreate(
                    [
                        'match_team_id' => $matchTeam->id,
                        'player_profile_id' => $playerProfileId,
                    ],
                    [
                        'points' => $points,
                        'stat_line' => ['points' => $points],
                    ]
                );
            }
        }

        if ($match->status !== 'completed') {
            $match->update(['status' => 'completed']);
        }

        return redirect()->route('admin.matches.index')
            ->with('success', 'Winner and player points updated. Team standings and player stats were refreshed.');
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

    private function assertTeamsAreAllowedForTournament(int $tournamentId, array $teamIds): void
    {
        $tournament = Tournament::findOrFail($tournamentId);

        $approvedTeamIds = Registration::where('tournament_id', $tournamentId)
            ->where('status', 'approved')
            ->pluck('team_id')
            ->all();

        if (count($approvedTeamIds) < 2) {
            throw ValidationException::withMessages([
                'team_ids' => 'Tournament must have at least two approved teams before scheduling a match.',
            ]);
        }

        foreach ($teamIds as $teamId) {
            if (! in_array((int) $teamId, $approvedTeamIds, true)) {
                throw ValidationException::withMessages([
                    'team_ids' => 'One or more selected teams are not approved participants of this tournament.',
                ]);
            }
        }

        $invalidSportCount = Team::whereIn('id', $teamIds)
            ->where('sport_id', '!=', $tournament->sport_id)
            ->count();
        if ($invalidSportCount > 0) {
            throw ValidationException::withMessages([
                'team_ids' => 'Selected teams must match the tournament sport.',
            ]);
        }
    }
}
