<?php

namespace Database\Seeders;

use App\Models\Sport;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Database\Seeder;

class TournamentSeeder extends Seeder
{
    public function run(): void
    {
        $basketball = Sport::where('sport_name', 'Basketball')->first();
        $football   = Sport::where('sport_name', 'Football')->first();
        $volleyball = Sport::where('sport_name', 'Volleyball')->first();
        $tennis     = Sport::where('sport_name', 'Tennis')->first();
        $swimming   = Sport::where('sport_name', 'Swimming')->first();

        // Priority: organizer@zentry.com organizes the most visible tournaments
        $org1 = User::where('email', 'organizer@zentry.com')->first();
        $org2 = User::where('email', 'james.organizer@zentry.com')->first();
        $org3 = User::where('email', 'priya.organizer@zentry.com')->first();
        $org4 = User::where('email', 'leo.organizer@zentry.com')->first();

        $tournaments = [
            // organizer@zentry.com owns these two — ongoing + upcoming
            [
                'tournament_name' => 'Summer Hoops Invitational',
                'sport'     => $basketball,
                'organizer' => $org1,        // ← organizer@zentry.com
                'status'    => 'ongoing',
                'max_teams' => 8,
                'start'     => '-10 days',
                'end'       => '+20 days',
            ],
            [
                'tournament_name' => 'City Premier League',
                'sport'     => $football,
                'organizer' => $org1,        // ← organizer@zentry.com
                'status'    => 'upcoming',
                'max_teams' => 8,
                'start'     => '+2 weeks',
                'end'       => '+6 weeks',
            ],

            // Completed tournaments — other organizers
            [
                'tournament_name' => 'Pro-Series Division 1',
                'sport'     => $basketball,
                'organizer' => $org2,
                'status'    => 'completed',
                'max_teams' => 8,
                'start'     => '-3 months',
                'end'       => '-2 months',
            ],
            [
                'tournament_name' => 'Champions League 2024',
                'sport'     => $football,
                'organizer' => $org3,
                'status'    => 'ongoing',
                'max_teams' => 8,
                'start'     => '-2 weeks',
                'end'       => '+3 weeks',
            ],
            [
                'tournament_name' => 'Volleyball Spring Classic',
                'sport'     => $volleyball,
                'organizer' => $org4,
                'status'    => 'completed',
                'max_teams' => 4,
                'start'     => '-2 months',
                'end'       => '-6 weeks',
            ],
            [
                'tournament_name' => 'Ace Masters Open',
                'sport'     => $tennis,
                'organizer' => $org2,
                'status'    => 'completed',
                'max_teams' => 8,
                'start'     => '-3 months',
                'end'       => '-2 months',
            ],
            [
                'tournament_name' => 'National Aquatics Gala',
                'sport'     => $swimming,
                'organizer' => $org3,
                'status'    => 'upcoming',
                'max_teams' => 16,
                'start'     => '+1 month',
                'end'       => '+6 weeks',
            ],
        ];

        foreach ($tournaments as $t) {
            if (!$t['sport'] || !$t['organizer']) continue;
            Tournament::firstOrCreate(
                ['tournament_name' => $t['tournament_name']],
                [
                    'organizer_id' => $t['organizer']->id,
                    'sport_id'     => $t['sport']->id,
                    'start_date'   => now()->modify($t['start'])->format('Y-m-d'),
                    'end_date'     => now()->modify($t['end'])->format('Y-m-d'),
                    'status'       => $t['status'],
                    'max_teams'    => $t['max_teams'],
                ]
            );
        }

        $this->command->info('Tournaments seeded: ' . Tournament::count());
    }
}