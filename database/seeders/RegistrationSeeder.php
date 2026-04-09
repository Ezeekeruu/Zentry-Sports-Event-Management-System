<?php

namespace Database\Seeders;

use App\Models\Registration;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Database\Seeder;

class RegistrationSeeder extends Seeder
{
    public function run(): void
    {
        $tournaments = Tournament::with('sport')->get();
        $teams       = Team::with('sport')->get();

        foreach ($tournaments as $tournament) {
            $eligible = $teams->where('sport_id', $tournament->sport_id);

            if ($eligible->isEmpty()) {
                continue;
            }

            $count  = min($eligible->count(), rand(4, 8), $tournament->max_teams);
            $sample = $eligible->random(min($count, $eligible->count()));

            foreach ($sample as $team) {
                if (Registration::where('team_id', $team->id)
                                 ->where('tournament_id', $tournament->id)
                                 ->exists()) {
                    continue;
                }

                $status = match ($tournament->status) {
                    'completed' => 'approved',
                    'ongoing'   => fake()->randomElement(['approved', 'approved', 'pending']),
                    default     => fake()->randomElement(['pending', 'approved']),
                };

                Registration::create([
                    'team_id'           => $team->id,
                    'tournament_id'     => $tournament->id,
                    'registration_date' => now()->modify('-' . rand(10, 60) . ' days')
                                          ->format('Y-m-d'),
                    'status'            => $status,
                    'notes'             => null,
                ]);
            }
        }

        $this->command->info('Registrations seeded: ' . Registration::count());
    }
}