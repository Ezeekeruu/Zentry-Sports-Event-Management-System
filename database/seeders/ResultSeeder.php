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

            if ($matchTeams->isEmpty()) {
                continue;
            }

            foreach ($matchTeams as $rank => $matchTeam) {
                $matchTeam->update(['rank_position' => $rank + 1]);
            }

            $winner    = $matchTeams->first();
            $highScore = $matchTeams->max('points_scored');

            foreach ($matchTeams as $matchTeam) {
                if (Result::where('match_team_id', $matchTeam->id)->exists()) {
                    continue;
                }

                $isWinner = $matchTeam->id === $winner->id;

                Result::create([
                    'match_team_id' => $matchTeam->id,
                    'summary'       => $isWinner
                        ? "Team won with {$matchTeam->points_scored} points."
                        : "Team scored {$matchTeam->points_scored} points.",
                    'total_teams'   => $matchTeams->count(),
                    'highest_score' => $highScore,
                    'recorded_at'   => now()->modify('-' . rand(1, 30) . ' days'),
                ]);
            }
        }

        $this->command->info('Results seeded: ' . Result::count());
    }
}