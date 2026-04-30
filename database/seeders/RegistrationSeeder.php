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
        $gravity = Team::where('team_name', 'Gravity Breakers')->first(); // coach@zentry.com
        $titans  = Team::where('team_name', 'Starlight Titans')->first();
        $dragons = Team::where('team_name', 'Crimson Dragons')->first();
        $apex    = Team::where('team_name', 'Apex Thunder')->first();
        $waves   = Team::where('team_name', 'Pacific Waves FC')->first();
        $neon    = Team::where('team_name', 'Neon Strikers')->first();
        $solar   = Team::where('team_name', 'Solar Eagles')->first();
        $storm   = Team::where('team_name', 'Storm Riders')->first();

        $hoops     = Tournament::where('tournament_name', 'Summer Hoops Invitational')->first();
        $proSeries = Tournament::where('tournament_name', 'Pro-Series Division 1')->first();
        $champions = Tournament::where('tournament_name', 'Champions League 2024')->first();
        $cityPL    = Tournament::where('tournament_name', 'City Premier League')->first();
        $vbClassic = Tournament::where('tournament_name', 'Volleyball Spring Classic')->first();

        $registrations = [
            // Summer Hoops (ongoing) — all 4 basketball teams including Gravity (coach@zentry.com)
            [$gravity, $hoops,     'approved', '-20 days'],
            [$titans,  $hoops,     'approved', '-19 days'],
            [$dragons, $hoops,     'approved', '-18 days'],
            [$apex,    $hoops,     'approved', '-15 days'],

            // Pro-Series (completed) — all 4 basketball teams
            [$gravity, $proSeries, 'approved', '-95 days'],
            [$titans,  $proSeries, 'approved', '-94 days'],
            [$dragons, $proSeries, 'approved', '-93 days'],
            [$apex,    $proSeries, 'approved', '-90 days'],

            // Champions League (ongoing) — football
            [$waves,   $champions, 'approved', '-20 days'],
            [$neon,    $champions, 'approved', '-19 days'],

            // City Premier League (upcoming) — pending registrations
            [$waves,   $cityPL,    'pending',  '-3 days'],
            [$neon,    $cityPL,    'pending',  '-2 days'],

            // Volleyball Spring Classic (completed)
            [$solar,   $vbClassic, 'approved', '-70 days'],
            [$storm,   $vbClassic, 'approved', '-68 days'],
        ];

        foreach ($registrations as [$team, $tournament, $status, $daysAgo]) {
            if (!$team || !$tournament) continue;
            Registration::firstOrCreate(
                ['team_id' => $team->id, 'tournament_id' => $tournament->id],
                [
                    'registration_date' => now()->modify($daysAgo)->format('Y-m-d'),
                    'status'            => $status,
                    'notes'             => null,
                ]
            );
        }

        $this->command->info('Registrations seeded: ' . Registration::count());
    }
}