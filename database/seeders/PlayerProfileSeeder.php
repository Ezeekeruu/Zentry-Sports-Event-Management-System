<?php

namespace Database\Seeders;

use App\Models\PlayerProfile;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class PlayerProfileSeeder extends Seeder
{
    public function run(): void
    {
        $players = User::where('role', 'player')->get();
        $teams   = Team::all();

        if ($teams->isEmpty()) {
            $this->command->warn('No teams found — skipping player profiles.');
            return;
        }

        $positions = [
            'Point Guard', 'Shooting Guard', 'Small Forward',
            'Power Forward', 'Center', 'Midfielder', 'Striker',
            'Goalkeeper', 'Defender', 'Winger',
        ];

        $jerseyCounter = [];

        foreach ($players as $i => $player) {
            if (PlayerProfile::where('user_id', $player->id)->exists()) {
                continue;
            }

            $team   = $teams[$i % $teams->count()];
            $teamId = $team->id;

            $jerseyCounter[$teamId] = ($jerseyCounter[$teamId] ?? 0) + 1;

            PlayerProfile::create([
                'user_id'       => $player->id,
                'team_id'       => $team->id,
                'position'      => $positions[$i % count($positions)],
                'jersey_number' => str_pad($jerseyCounter[$teamId], 2, '0', STR_PAD_LEFT),
            ]);
        }

        $this->command->info('Player profiles seeded: ' . PlayerProfile::count());
    }
}