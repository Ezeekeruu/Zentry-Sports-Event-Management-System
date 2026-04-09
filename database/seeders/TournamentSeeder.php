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
        $organizers = User::where('role', 'organizer')->get();
        $sports     = Sport::all();

        $tournaments = [
            ['name' => 'Summer Hoops Invitational', 'sport' => 'Basketball', 'status' => 'ongoing',   'max' => 32, 'start' => '-5 days',  'end' => '+10 days'],
            ['name' => 'City Premier League',        'sport' => 'Football',   'status' => 'upcoming',  'max' => 16, 'start' => '+2 weeks', 'end' => '+6 weeks'],
            ['name' => 'National Aquatics Gala',     'sport' => 'Swimming',   'status' => 'upcoming',  'max' => 32, 'start' => '+1 month', 'end' => '+6 weeks'],
            ['name' => 'Ace Masters Open',           'sport' => 'Tennis',     'status' => 'completed', 'max' => 64, 'start' => '-2 months','end' => '-7 weeks'],
            ['name' => 'Volleyball Spring Classic',  'sport' => 'Volleyball', 'status' => 'upcoming',  'max' => 16, 'start' => '+3 weeks', 'end' => '+5 weeks'],
            ['name' => 'Champions League 2024',      'sport' => 'Football',   'status' => 'ongoing',   'max' => 32, 'start' => '-2 weeks', 'end' => '+2 weeks'],
            ['name' => 'Pro-Series Division 1',      'sport' => 'Basketball', 'status' => 'completed', 'max' => 16, 'start' => '-3 months','end' => '-2 months'],
            ['name' => 'Regional Athletics Meet',    'sport' => 'Athletics',  'status' => 'upcoming',  'max' => 64, 'start' => '+5 weeks', 'end' => '+7 weeks'],
        ];

        foreach ($tournaments as $i => $t) {
            $sport     = $sports->firstWhere('sport_name', $t['sport']) ?? $sports->first();
            $organizer = $organizers[$i % $organizers->count()];

            Tournament::firstOrCreate(
                ['tournament_name' => $t['name']],
                [
                    'organizer_id' => $organizer->id,
                    'sport_id'     => $sport->id,
                    'start_date'   => now()->modify($t['start'])->format('Y-m-d'),
                    'end_date'     => now()->modify($t['end'])->format('Y-m-d'),
                    'status'       => $t['status'],
                    'max_teams'    => $t['max'],
                ]
            );
        }

        $this->command->info('Tournaments seeded: ' . Tournament::count());
    }
}