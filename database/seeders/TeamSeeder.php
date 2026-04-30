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
        $basketball = Sport::where('sport_name', 'Basketball')->first();
        $football   = Sport::where('sport_name', 'Football')->first();
        $volleyball = Sport::where('sport_name', 'Volleyball')->first();
        $tennis     = Sport::where('sport_name', 'Tennis')->first();
        $swimming   = Sport::where('sport_name', 'Swimming')->first();
        $badminton  = Sport::where('sport_name', 'Badminton')->first();

        $teams = [
            // coach@zentry.com gets Gravity Breakers — his own team
            ['team_name' => 'Gravity Breakers',    'sport' => $basketball, 'coach_email' => 'coach@zentry.com',             'founded' => '2021-05-11'],

            // Other basketball teams
            ['team_name' => 'Starlight Titans',    'sport' => $basketball, 'coach_email' => 'coach.basketball1@zentry.com', 'founded' => '2018-03-15'],
            ['team_name' => 'Crimson Dragons',     'sport' => $basketball, 'coach_email' => 'coach.basketball2@zentry.com', 'founded' => '2019-07-01'],
            ['team_name' => 'Apex Thunder',        'sport' => $basketball, 'coach_email' => 'coach.basketball3@zentry.com', 'founded' => '2020-01-20'],

            // Football
            ['team_name' => 'Pacific Waves FC',    'sport' => $football,   'coach_email' => 'coach.football1@zentry.com',   'founded' => '2017-09-10'],
            ['team_name' => 'Neon Strikers',       'sport' => $football,   'coach_email' => 'coach.football2@zentry.com',   'founded' => '2016-05-22'],

            // Volleyball
            ['team_name' => 'Solar Eagles',        'sport' => $volleyball, 'coach_email' => 'coach.volleyball1@zentry.com', 'founded' => '2019-02-14'],
            ['team_name' => 'Storm Riders',        'sport' => $volleyball, 'coach_email' => 'coach.volleyball2@zentry.com', 'founded' => '2021-08-30'],

            // Others
            ['team_name' => 'Iron Hawks Academy',  'sport' => $tennis,     'coach_email' => 'coach.tennis1@zentry.com',     'founded' => '2015-11-05'],
            ['team_name' => 'Arctic Blazers',      'sport' => $swimming,   'coach_email' => 'coach.swimming1@zentry.com',   'founded' => '2020-06-18'],
            ['team_name' => 'Shadow Wolves',       'sport' => $badminton,  'coach_email' => 'coach.badminton1@zentry.com',  'founded' => '2022-01-09'],
        ];

        foreach ($teams as $t) {
            if (!$t['sport']) continue;
            $coach = User::where('email', $t['coach_email'])->first();
            if (!$coach) continue;

            Team::firstOrCreate(
                ['team_name' => $t['team_name']],
                [
                    'sport_id'   => $t['sport']->id,
                    'coach_id'   => $coach->id,
                    'founded_at' => $t['founded'],
                ]
            );
        }

        $this->command->info('Teams seeded: ' . Team::count());
    }
}