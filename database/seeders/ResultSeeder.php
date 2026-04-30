<?php

namespace Database\Seeders;

use App\Models\MatchTeam;
use App\Models\Result;
use App\Models\ZentryMatch;
use Illuminate\Database\Seeder;

class ResultSeeder extends Seeder
{
    public function run(): void
    {
        $completedMatches = ZentryMatch::where('status', 'completed')->get();

        foreach ($completedMatches as $match) {
            $matchTeams = MatchTeam::where('match_id', $match->id)
                ->orderByDesc('points_scored')
                ->get();

            if ($matchTeams->isEmpty()) continue;

            $highScore = $matchTeams->max('points_scored');

            foreach ($matchTeams as $rank => $mt) {
                $mt->update(['rank_position' => $rank + 1]);

                if (Result::where('match_team_id', $mt->id)->exists()) continue;

                $isWinner = $rank === 0;

                Result::create([
                    'match_team_id' => $mt->id,
                    'summary'       => $isWinner
                        ? "Won with a score of {$mt->points_scored}."
                        : "Finished with a score of {$mt->points_scored}.",
                    'total_teams'   => $matchTeams->count(),
                    'highest_score' => $highScore,
                    'recorded_at'   => now()->modify('-' . rand(1, 5) . ' days'),
                ]);
            }
        }

        $this->command->info('Results seeded: ' . Result::count());
    }
}