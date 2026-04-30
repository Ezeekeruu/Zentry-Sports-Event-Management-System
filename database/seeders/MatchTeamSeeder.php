<?php

namespace Database\Seeders;

use App\Models\MatchTeam;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\ZentryMatch;
use Illuminate\Database\Seeder;

class MatchTeamSeeder extends Seeder
{
    public function run(): void
    {
        $gravity = Team::where('team_name', 'Gravity Breakers')->first();
        $titans  = Team::where('team_name', 'Starlight Titans')->first();
        $dragons = Team::where('team_name', 'Crimson Dragons')->first();
        $apex    = Team::where('team_name', 'Apex Thunder')->first();
        $waves   = Team::where('team_name', 'Pacific Waves FC')->first();
        $neon    = Team::where('team_name', 'Neon Strikers')->first();
        $solar   = Team::where('team_name', 'Solar Eagles')->first();
        $storm   = Team::where('team_name', 'Storm Riders')->first();

        // ── Summer Hoops (ongoing) ──
        // Gravity plays in it so coach@zentry.com and player@zentry.com see real data
        $hoops = Tournament::where('tournament_name', 'Summer Hoops Invitational')->first();
        if ($hoops) {
            $m = ZentryMatch::where('tournament_id', $hoops->id)->orderBy('match_date')->get();
            // Match 1 completed: Gravity 91 vs Titans 85
            $this->assign($m->get(0), [[$gravity, 91, 1], [$titans,  85, 2]]);
            // Match 2 completed: Dragons 78 vs Apex 70
            $this->assign($m->get(1), [[$dragons, 78, 1], [$apex,    70, 2]]);
            // Match 3 scheduled: Gravity vs Dragons (Semi Finals)
            $this->assign($m->get(2), [[$gravity, null, 1], [$dragons, null, 2]]);
            // Match 4 scheduled: Titans vs Apex
            $this->assign($m->get(3), [[$titans,  null, 1], [$apex,    null, 2]]);
        }

        // ── Pro-Series (completed) — Gravity wins the championship ──
        $proSeries = Tournament::where('tournament_name', 'Pro-Series Division 1')->first();
        if ($proSeries) {
            $m = ZentryMatch::where('tournament_id', $proSeries->id)->orderBy('match_date')->get();
            $this->assign($m->get(0), [[$gravity, 88, 1], [$apex,    72, 2]]);
            $this->assign($m->get(1), [[$titans,  95, 1], [$dragons, 80, 2]]);
            $this->assign($m->get(2), [[$gravity, 76, 1], [$dragons, 65, 2]]);
            $this->assign($m->get(3), [[$titans,  84, 1], [$apex,    70, 2]]);
            // Finals: Gravity beats Titans
            $this->assign($m->get(4), [[$gravity, 102, 1], [$titans, 98, 2]]);
        }

        // ── Champions League (ongoing) ──
        $champions = Tournament::where('tournament_name', 'Champions League 2024')->first();
        if ($champions) {
            $m = ZentryMatch::where('tournament_id', $champions->id)->orderBy('match_date')->get();
            $this->assign($m->get(0), [[$waves, 3, 1], [$neon, 1, 2]]);
            $this->assign($m->get(1), [[$neon,  2, 1], [$waves, 2, 2]]);
            $this->assign($m->get(2), [[$waves, null, 1], [$neon, null, 2]]);
        }

        // ── Volleyball Spring Classic (completed) ──
        $vbClassic = Tournament::where('tournament_name', 'Volleyball Spring Classic')->first();
        if ($vbClassic) {
            $m = ZentryMatch::where('tournament_id', $vbClassic->id)->orderBy('match_date')->get();
            $this->assign($m->get(0), [[$solar, 25, 1], [$storm, 20, 2]]);
            $this->assign($m->get(1), [[$storm, 25, 1], [$solar, 22, 2]]);
            $this->assign($m->get(2), [[$solar, 25, 1], [$storm, 18, 2]]);
        }

        $this->command->info('MatchTeams seeded: ' . MatchTeam::count());
    }

    private function assign(?ZentryMatch $match, array $teams): void
    {
        if (!$match) return;
        foreach ($teams as [$team, $points, $seed]) {
            if (!$team) continue;
            MatchTeam::firstOrCreate(
                ['match_id' => $match->id, 'team_id' => $team->id],
                [
                    'points_scored' => $points,
                    'rank_position' => null,
                    'seed_number'   => $seed,
                ]
            );
        }
    }
}