<?php

namespace App\Http\Controllers\Organizer;

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
            ->with(['sport', 'registrations' => function ($query) {
                $query->where('status', 'approved');
            }])
            ->orderBy('tournament_name')
            ->get();
        $teams = Team::where('is_active', true)->with('sport')->orderBy('team_name')->get();
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
        $this->assertTeamsAreAllowedForTournament($tournament, $request->team_ids ?? []);

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
        $tournaments = Tournament::where('organizer_id', auth()->id())
            ->with([
                'sport',
                'registrations' => function ($query) {
                    $query->where('status', 'approved');
                },
            ])
            ->get();
        $teams = Team::where('is_active', true)->with('sport')->orderBy('team_name')->get();
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

        $this->assertTeamsAreAllowedForTournament($match->tournament, $request->team_ids ?? []);

        $match->update($request->only('match_date', 'match_time', 'venue', 'status', 'round_name'));

        $match->matchTeams()->whereNotIn('team_id', $request->team_ids)->delete();
        foreach ($request->team_ids as $teamId) {
            MatchTeam::firstOrCreate(['match_id' => $match->id, 'team_id' => $teamId]);
        }

        return redirect()->route('organizer.matches.index')->with('success', 'Match updated.');
    }

    public function winnerEdit(ZentryMatch $match): View
    {
        if ($match->tournament->organizer_id !== auth()->id()) abort(403);

        $match->load([
            'tournament.sport',
            'matchTeams.team.playerProfiles.user',
            'matchTeams.playerStats',
        ]);

        $currentWinnerTeamId = $match->matchTeams->firstWhere('rank_position', 1)?->team_id;
        $existingStats = PlayerMatchStat::whereIn('match_team_id', $match->matchTeams->pluck('id'))
            ->get()
            ->keyBy(fn ($row) => $row->match_team_id . '-' . $row->player_profile_id);

        return view('organizer.matches.winner', compact('match', 'currentWinnerTeamId', 'existingStats'));
    }

    public function winnerUpdate(Request $request, ZentryMatch $match): RedirectResponse
    {
        if ($match->tournament->organizer_id !== auth()->id()) abort(403);
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

        return redirect()->route('organizer.matches.index')
            ->with('success', 'Winner and player points updated. Team standings and player stats were refreshed.');
    }

    public function destroy(ZentryMatch $match): RedirectResponse
    {
        if ($match->tournament->organizer_id !== auth()->id()) abort(403);
        $match->update(['is_active' => false]);
        return redirect()->route('organizer.matches.index')->with('success', 'Match archived.');
    }

    private function assertTeamsAreAllowedForTournament(Tournament $tournament, array $teamIds): void
    {
        $approvedTeamIds = Registration::where('tournament_id', $tournament->id)
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
