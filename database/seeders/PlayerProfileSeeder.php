<?php

namespace Database\Seeders;

use App\Models\PlayerProfile;
use App\Models\Sport;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class PlayerProfileSeeder extends Seeder
{
    public function run(): void
    {
        $basketball = Sport::where('sport_name', 'Basketball')->first();
        $football   = Sport::where('sport_name', 'Football')->first();
        $volleyball = Sport::where('sport_name', 'Volleyball')->first();
        $tennis     = Sport::where('sport_name', 'Tennis')->first();

        $gravityTeam = Team::where('team_name', 'Gravity Breakers')->first(); // coach@zentry.com's team
        $titanTeam   = Team::where('team_name', 'Starlight Titans')->first();
        $dragonTeam  = Team::where('team_name', 'Crimson Dragons')->first();
        $apexTeam    = Team::where('team_name', 'Apex Thunder')->first();
        $wavesTeam   = Team::where('team_name', 'Pacific Waves FC')->first();
        $neonTeam    = Team::where('team_name', 'Neon Strikers')->first();
        $solarTeam   = Team::where('team_name', 'Solar Eagles')->first();
        $stormTeam   = Team::where('team_name', 'Storm Riders')->first();

        $bbPositions = ['Point Guard', 'Shooting Guard', 'Small Forward', 'Power Forward', 'Center'];
        $fbPositions = ['Goalkeeper', 'Defender', 'Midfielder', 'Striker', 'Winger'];
        $vbPositions = ['Setter', 'Libero', 'Outside Hitter', 'Middle Blocker', 'Opposite'];
        $tnPositions = ['Singles Player', 'Doubles Player'];

        // ── Priority: player@zentry.com → Gravity Breakers (coach@zentry.com's team) ──
        $fixedPlayer = User::where('email', 'player@zentry.com')->first();
        if ($fixedPlayer && $gravityTeam && $basketball) {
            PlayerProfile::updateOrCreate(
                ['user_id' => $fixedPlayer->id],
                [
                    'sport_id'      => $basketball->id,
                    'team_id'       => $gravityTeam->id,
                    'position'      => 'Point Guard',
                    'jersey_number' => '00',
                ]
            );
        }

        // ── All other players, excluding player@zentry.com ──
        $allPlayers = User::where('role', 'player')
            ->where('email', '!=', 'player@zentry.com')
            ->orderBy('id')
            ->get();

        // Split into groups: basketball gets 4 teams × 5 players = 20
        // football 2 teams × 9 = 18, volleyball 2 teams × 6 = 12, rest tennis/unassigned
        $bbPlayers = $allPlayers->slice(0, 20)->values();
        $fbPlayers = $allPlayers->slice(20, 18)->values();
        $vbPlayers = $allPlayers->slice(38, 12)->values();
        $remaining = $allPlayers->slice(50)->values();

        // Basketball — spread across Gravity, Titans, Dragons, Apex equally
        $bbTeams = [$gravityTeam, $titanTeam, $dragonTeam, $apexTeam];
        foreach ($bbPlayers as $i => $player) {
            $team = $bbTeams[$i % 4];
            if (!$team || !$basketball) continue;
            PlayerProfile::firstOrCreate(
                ['user_id' => $player->id],
                [
                    'sport_id'      => $basketball->id,
                    'team_id'       => $team->id,
                    'position'      => $bbPositions[$i % count($bbPositions)],
                    'jersey_number' => str_pad(($i % 15) + 1, 2, '0', STR_PAD_LEFT),
                ]
            );
        }

        // Football — Waves and Neon
        foreach ($fbPlayers as $i => $player) {
            $team = $i % 2 === 0 ? $wavesTeam : $neonTeam;
            if (!$team || !$football) continue;
            PlayerProfile::firstOrCreate(
                ['user_id' => $player->id],
                [
                    'sport_id'      => $football->id,
                    'team_id'       => $team->id,
                    'position'      => $fbPositions[$i % count($fbPositions)],
                    'jersey_number' => str_pad(($i % 15) + 1, 2, '0', STR_PAD_LEFT),
                ]
            );
        }

        // Volleyball — Solar and Storm
        foreach ($vbPlayers as $i => $player) {
            $team = $i % 2 === 0 ? $solarTeam : $stormTeam;
            if (!$team || !$volleyball) continue;
            PlayerProfile::firstOrCreate(
                ['user_id' => $player->id],
                [
                    'sport_id'      => $volleyball->id,
                    'team_id'       => $team->id,
                    'position'      => $vbPositions[$i % count($vbPositions)],
                    'jersey_number' => str_pad(($i % 10) + 1, 2, '0', STR_PAD_LEFT),
                ]
            );
        }

        // Remaining — sport assigned but no team yet
        foreach ($remaining as $i => $player) {
            PlayerProfile::firstOrCreate(
                ['user_id' => $player->id],
                [
                    'sport_id'      => $i % 2 === 0 ? $tennis?->id : $basketball?->id,
                    'team_id'       => null,
                    'position'      => $tnPositions[$i % count($tnPositions)],
                    'jersey_number' => null,
                ]
            );
        }

        $this->command->info('Player profiles seeded: ' . PlayerProfile::count());
    }
}