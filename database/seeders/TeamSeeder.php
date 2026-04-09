<?php

namespace Database\Seeders;

use App\Models\Sport;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        $coaches = User::where('role', 'coach')->get();
        $sports  = Sport::all();

        $teamNames = [
            'Starlight Titans',  'Apex Thunder',      'Iron Hawks Academy',
            'Pacific Waves',     'Velocity Prime',    'Neon Strikers',
            'Gravity Breakers',  'Shadow Wolves',     'Solar Eagles',
            'Crimson Dragons',   'Storm Riders',      'Arctic Blazers',
        ];

        foreach ($coaches as $i => $coach) {
            if (Team::where('coach_id', $coach->id)->exists()) {
                continue;
            }

            $sport = $sports[$i % $sports->count()];

            Team::create([
                'team_name'  => $teamNames[$i % count($teamNames)],
                'sport_id'   => $sport->id,
                'coach_id'   => $coach->id,
                'founded_at' => fake()->dateTimeBetween('-8 years', '-1 year')
                                ->format('Y-m-d'),
            ]);
        }

        $this->command->info('Teams seeded: ' . Team::count());
    }
}