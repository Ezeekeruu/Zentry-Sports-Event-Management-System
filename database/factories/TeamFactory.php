<?php

namespace Database\Factories;

use App\Models\Sport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamFactory extends Factory
{
    private static array $adjectives = [
        'Apex', 'Iron', 'Nova', 'Storm', 'Blaze',
        'Neon', 'Gravity', 'Velocity', 'Shadow', 'Solar',
        'Crimson', 'Arctic', 'Thunder', 'Phantom', 'Steel',
    ];

    private static array $nouns = [
        'Tigers',   'Hawks',   'Wolves',  'Dragons', 'Titans',
        'Strikers', 'Riders',  'Breakers','Raiders', 'Kings',
        'Warriors', 'Blazers', 'Sharks',  'Eagles',  'Lions',
    ];

    public function definition(): array
    {
        return [
            'team_name'  => fake()->unique()->randomElement(self::$adjectives)
                            . ' '
                            . fake()->randomElement(self::$nouns),
            'sport_id'   => Sport::inRandomOrder()->first()?->id ?? Sport::factory(),
            'coach_id'   => User::where('role', 'coach')->inRandomOrder()->first()?->id
                            ?? User::factory()->coach(),
            'logo_url'   => null,
            'founded_at' => fake()->dateTimeBetween('-10 years', '-1 year')->format('Y-m-d'),
        ];
    }
}