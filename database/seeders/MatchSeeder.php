<?php

namespace Database\Seeders;

use App\Models\Registration;
use App\Models\Tournament;
use App\Models\ZentryMatch;
use Illuminate\Database\Seeder;

class MatchSeeder extends Seeder
{
    public function run(): void
    {
        $venues = [
            'Central Arena',          'Skyline Stadium',
            'Olympic Grounds',        'National Sports Complex',
            'City Sports Hall',       'Apex Dome',
            'Riverside Courts',       'Metro Coliseum',
        ];

        $rounds = [
            'Group Phase', 'Round Robin', 'Quarter Finals',
            'Semi Finals', 'Finals',
        ];

        $tournaments = Tournament::all();

        foreach ($tournaments as $tournament) {
            if ($tournament->status === 'upcoming') {
                continue;
            }

            $approvedTeams = Registration::where('tournament_id', $tournament->id)
                                          ->where('status', 'approved')
                                          ->pluck('team_id')
                                          ->toArray();

            if (count($approvedTeams) < 2) {
                continue;
            }

            $matchCount = min(rand(3, 5), (int) floor(count($approvedTeams) / 2));

            for ($i = 0; $i < $matchCount; $i++) {
                $status = match ($tournament->status) {
                    'completed' => 'completed',
                    'ongoing'   => fake()->randomElement(['completed', 'scheduled', 'live']),
                    default     => 'scheduled',
                };

                ZentryMatch::create([
                    'tournament_id' => $tournament->id,
                    'match_date'    => fake()->dateTimeBetween(
                        $tournament->start_date,
                        $tournament->end_date
                    )->format('Y-m-d'),
                    'match_time'    => fake()->randomElement([
                        '10:00', '13:00', '16:00', '18:30', '20:00',
                    ]),
                    'venue'         => fake()->randomElement($venues),
                    'status'        => $status,
                    'round_name'    => $rounds[$i % count($rounds)],
                ]);
            }
        }

        $this->command->info('Matches seeded: ' . ZentryMatch::count());
    }
}