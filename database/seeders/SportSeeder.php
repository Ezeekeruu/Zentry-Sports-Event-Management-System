<?php

namespace Database\Seeders;

use App\Models\Sport;
use Illuminate\Database\Seeder;

class SportSeeder extends Seeder
{
    public function run(): void
    {
        $sports = [
            [
                'sport_name'          => 'Basketball',
                'min_teams_per_match' => 2,
                'max_teams_per_match' => 2,
                'description'         => 'Five-on-five half-court or full-court basketball.',
            ],
            [
                'sport_name'          => 'Football',
                'min_teams_per_match' => 2,
                'max_teams_per_match' => 2,
                'description'         => 'Association football played across two halves.',
            ],
            [
                'sport_name'          => 'Volleyball',
                'min_teams_per_match' => 2,
                'max_teams_per_match' => 2,
                'description'         => 'Six-player indoor volleyball.',
            ],
            [
                'sport_name'          => 'Tennis',
                'min_teams_per_match' => 2,
                'max_teams_per_match' => 2,
                'description'         => 'Singles or doubles court tennis.',
            ],
            [
                'sport_name'          => 'Swimming',
                'min_teams_per_match' => 4,
                'max_teams_per_match' => 8,
                'description'         => 'Competitive lane swimming across multiple distances.',
            ],
            [
                'sport_name'          => 'Badminton',
                'min_teams_per_match' => 2,
                'max_teams_per_match' => 2,
                'description'         => 'Singles or doubles shuttle badminton.',
            ],
            [
                'sport_name'          => 'Table Tennis',
                'min_teams_per_match' => 2,
                'max_teams_per_match' => 2,
                'description'         => 'Singles or doubles table tennis.',
            ],
            [
                'sport_name'          => 'Athletics',
                'min_teams_per_match' => 4,
                'max_teams_per_match' => 16,
                'description'         => 'Track and field athletic events.',
            ],
        ];

        foreach ($sports as $sport) {
            Sport::firstOrCreate(
                ['sport_name' => $sport['sport_name']],
                $sport
            );
        }

        $this->command->info('Sports seeded: ' . Sport::count());
    }
}