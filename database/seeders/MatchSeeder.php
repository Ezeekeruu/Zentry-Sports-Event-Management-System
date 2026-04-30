<?php

namespace Database\Seeders;

use App\Models\Tournament;
use App\Models\ZentryMatch;
use Illuminate\Database\Seeder;

class MatchSeeder extends Seeder
{
    public function run(): void
    {
        $venues = [
            'Central Arena', 'Skyline Stadium', 'Olympic Grounds',
            'National Sports Complex', 'City Sports Hall', 'Apex Dome',
        ];

        // Summer Hoops (ongoing) — 3 matches: 2 completed, 1 scheduled
        $hoops = Tournament::where('tournament_name', 'Summer Hoops Invitational')->first();
        if ($hoops) {
            $hoopsMatches = [
                ['date' => '-8 days',  'status' => 'completed', 'round' => 'Group Phase',   'time' => '18:00'],
                ['date' => '-4 days',  'status' => 'completed', 'round' => 'Quarter Finals', 'time' => '16:00'],
                ['date' => '+5 days',  'status' => 'scheduled', 'round' => 'Semi Finals',    'time' => '19:00'],
                ['date' => '+12 days', 'status' => 'scheduled', 'round' => 'Finals',         'time' => '20:00'],
            ];
            foreach ($hoopsMatches as $m) {
                ZentryMatch::firstOrCreate(
                    ['tournament_id' => $hoops->id, 'round_name' => $m['round']],
                    [
                        'match_date' => now()->modify($m['date'])->format('Y-m-d'),
                        'match_time' => $m['time'],
                        'venue'      => $venues[array_rand($venues)],
                        'status'     => $m['status'],
                    ]
                );
            }
        }

        // Pro-Series (completed) — 5 matches all completed
        $proSeries = Tournament::where('tournament_name', 'Pro-Series Division 1')->first();
        if ($proSeries) {
            $rounds = ['Group Phase', 'Group Phase', 'Quarter Finals', 'Semi Finals', 'Finals'];
            foreach ($rounds as $i => $round) {
                ZentryMatch::firstOrCreate(
                    ['tournament_id' => $proSeries->id, 'round_name' => $round . ($i < 2 ? ' ' . ($i + 1) : '')],
                    [
                        'match_date' => now()->modify('-' . (90 - ($i * 10)) . ' days')->format('Y-m-d'),
                        'match_time' => '17:00',
                        'venue'      => $venues[$i % count($venues)],
                        'status'     => 'completed',
                        'round_name' => $round,
                    ]
                );
            }
        }

        // Champions League (ongoing) — 2 completed, 1 live
        $champions = Tournament::where('tournament_name', 'Champions League 2024')->first();
        if ($champions) {
            $clMatches = [
                ['date' => '-10 days', 'status' => 'completed', 'round' => 'Group Phase',   'time' => '20:00'],
                ['date' => '-5 days',  'status' => 'completed', 'round' => 'Quarter Finals', 'time' => '20:00'],
                ['date' => '+7 days',  'status' => 'scheduled', 'round' => 'Semi Finals',    'time' => '21:00'],
            ];
            foreach ($clMatches as $m) {
                ZentryMatch::firstOrCreate(
                    ['tournament_id' => $champions->id, 'round_name' => $m['round']],
                    [
                        'match_date' => now()->modify($m['date'])->format('Y-m-d'),
                        'match_time' => $m['time'],
                        'venue'      => $venues[array_rand($venues)],
                        'status'     => $m['status'],
                    ]
                );
            }
        }

        // Volleyball Spring Classic (completed) — 3 matches
        $vbClassic = Tournament::where('tournament_name', 'Volleyball Spring Classic')->first();
        if ($vbClassic) {
            $vbMatches = [
                ['date' => '-65 days', 'round' => 'Group Phase'],
                ['date' => '-58 days', 'round' => 'Semi Finals'],
                ['date' => '-52 days', 'round' => 'Finals'],
            ];
            foreach ($vbMatches as $m) {
                ZentryMatch::firstOrCreate(
                    ['tournament_id' => $vbClassic->id, 'round_name' => $m['round']],
                    [
                        'match_date' => now()->modify($m['date'])->format('Y-m-d'),
                        'match_time' => '15:00',
                        'venue'      => 'City Sports Hall',
                        'status'     => 'completed',
                    ]
                );
            }
        }

        $this->command->info('Matches seeded: ' . ZentryMatch::count());
    }
}