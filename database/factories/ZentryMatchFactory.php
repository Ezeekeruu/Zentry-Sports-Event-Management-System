<?php

namespace Database\Factories;

use App\Models\Tournament;
use Illuminate\Database\Eloquent\Factories\Factory;

class ZentryMatchFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tournament_id' => Tournament::inRandomOrder()->first()?->id
                               ?? Tournament::factory(),
            'match_date'    => fake()->dateTimeBetween('-1 month', '+1 month')
                               ->format('Y-m-d'),
            'match_time'    => fake()->time('H:i:s'),
            'venue'         => fake()->randomElement([
                'Central Arena', 'Skyline Stadium', 'Olympic Grounds',
                'National Sports Complex', 'City Sports Hall', 'Apex Dome',
            ]),
            'status'        => fake()->randomElement([
                'scheduled', 'live', 'completed', 'delayed',
            ]),
            'round_name'    => fake()->randomElement([
                'Group Phase', 'Round of 16', 'Quarter Finals',
                'Semi Finals', 'Finals', 'Round Robin',
            ]),
        ];
    }
}