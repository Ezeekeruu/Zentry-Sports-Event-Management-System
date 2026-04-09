<?php

namespace Database\Seeders;

use App\Models\MatchTeam;
use App\Models\Registration;
use App\Models\ZentryMatch;
use Illuminate\Database\Seeder;

class MatchTeamSeeder extends Seeder
{
    public function run(): void
    {
        $matches = ZentryMatch::all();

        foreach ($matches as $match) {
            $teamIds = Registration::where('tournament_id', $match->tournament_id)
                                    ->where('status', 'approved')
                                    ->pluck('team_id')
                                    ->toArray();

            if (count($teamIds) < 2) {
                continue;
            }

            shuffle($teamIds);
            $selected = array_slice($teamIds, 0, 2);

            foreach ($selected as $seed => $teamId) {
                if (MatchTeam::where('match_id', $match->id)
                              ->where('team_id', $teamId)
                              ->exists()) {
                    continue;
                }

                MatchTeam::create([
                    'match_id'      => $match->id,
                    'team_id'       => $teamId,
                    'points_scored' => $match->status === 'completed' ? rand(50, 120) : null,
                    'rank_position' => null,
                    'seed_number'   => $seed + 1,
                ]);
            }
        }

        $this->command->info('MatchTeams seeded: ' . MatchTeam::count());
    }
}