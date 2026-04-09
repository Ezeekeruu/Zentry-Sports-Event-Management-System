<?php

namespace App\Http\Requests;

use App\Models\Registration;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
{
    /**
     * Only organizers and admins can register teams
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user && in_array($user->role, ['admin', 'organizer'], true);
    }

    /**
     * Basic field validation
     */
    public function rules(): array
    {
        return [
            'team_id'           => ['required', 'integer', 'exists:teams,id'],
            'tournament_id'     => ['required', 'integer', 'exists:tournaments,id'],
            'registration_date' => ['required', 'date'],
            'notes'             => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Business rules enforcement
     * Runs after basic rules pass
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $teamId       = $this->input('team_id');
            $tournamentId = $this->input('tournament_id');

            $team       = Team::with('sport')->find($teamId);
            $tournament = Tournament::with('sport')->find($tournamentId);

            if (! $team || ! $tournament) {
                return;
            }

            // BR 11.3 — sport must match
            if ($team->sport_id !== $tournament->sport_id) {
                $validator->errors()->add(
                    'team_id',
                    "Sport mismatch: \"{$team->sport->sport_name}\" teams cannot join "
                    . "a \"{$tournament->sport->sport_name}\" tournament."
                );
            }

            // BR 5.5 — no duplicate registration
            $alreadyRegistered = Registration::where('team_id', $teamId)
                ->where('tournament_id', $tournamentId)
                ->exists();

            if ($alreadyRegistered) {
                $validator->errors()->add(
                    'team_id',
                    "\"{$team->team_name}\" is already registered in this tournament."
                );
            }

            // BR 4.4 — cannot register into completed tournament
            if ($tournament->status === 'completed') {
                $validator->errors()->add(
                    'tournament_id',
                    'Cannot register teams into a completed tournament.'
                );
            }

            // BR 6.3 — tournament max teams cap
            $currentCount = Registration::where('tournament_id', $tournamentId)
                ->where('status', 'approved')
                ->count();

            if ($currentCount >= $tournament->max_teams) {
                $validator->errors()->add(
                    'tournament_id',
                    "This tournament is full ({$tournament->max_teams} teams maximum)."
                );
            }

            // BR 4.2 — organizer can only manage their own tournaments
            $user = $this->user();
            if ($user->role === 'organizer' && $tournament->organizer_id !== $user->id) {
                $validator->errors()->add(
                    'tournament_id',
                    'You can only manage registrations for your own tournaments.'
                );
            }
        });
    }

    /**
     * Human readable attribute names for error messages
     */
    public function attributes(): array
    {
        return [
            'team_id'       => 'team',
            'tournament_id' => 'tournament',
        ];
    }
}