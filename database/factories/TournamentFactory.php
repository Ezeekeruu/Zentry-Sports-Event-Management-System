<?php

namespace Database\Factories;

use App\Models\Sport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TournamentFactory extends Factory
{
    private static array $prefixes = [
        'Summer', 'Winter', 'City', 'National', 'Regional',
        'Pro', 'Elite', 'Open', 'Champions', 'Premier',
    ];

    private static array $suffixes = [
        'Invitational', 'League', 'Cup', 'Classic',
        'Championship', 'Series', 'Grand Prix', 'Open',
    ];

    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-3 months', '+3 months');
        $end   = (clone $start)->modify('+' . fake()->numberBetween(3, 21) . ' days');

        $now = now();
        if ($end < $now) {
            $status = 'completed';
        } elseif ($start <= $now) {
            $status = 'ongoing';
        } else {
            $status = 'upcoming';
        }

        return [
            'tournament_name' => fake()->randomElement(self::$prefixes)
                                 . ' '
                                 . fake()->randomElement(self::$suffixes)
                                 . ' '
                                 . fake()->year(),
            'organizer_id'    => User::where('role', 'organizer')->inRandomOrder()->first()?->id
                                  ?? User::factory()->organizer(),
            'sport_id'        => Sport::inRandomOrder()->first()?->id ?? Sport::factory(),
            'start_date'      => $start->format('Y-m-d'),
            'end_date'        => $end->format('Y-m-d'),
            'status'          => $status,
            'max_teams'       => fake()->randomElement([8, 16, 32]),
        ];
    }

    public function upcoming(): static
    {
        return $this->state(function () {
            $start = fake()->dateTimeBetween('+1 week', '+2 months');
            $end   = (clone $start)->modify('+14 days');
            return [
                'start_date' => $start->format('Y-m-d'),
                'end_date'   => $end->format('Y-m-d'),
                'status'     => 'upcoming',
            ];
        });
    }

    public function ongoing(): static
    {
        return $this->state(function () {
            $start = fake()->dateTimeBetween('-5 days', '-1 day');
            $end   = fake()->dateTimeBetween('+1 day', '+10 days');
            return [
                'start_date' => $start->format('Y-m-d'),
                'end_date'   => $end->format('Y-m-d'),
                'status'     => 'ongoing',
            ];
        });
    }

    public function completed(): static
    {
        return $this->state(function () {
            $start = fake()->dateTimeBetween('-3 months', '-2 months');
            $end   = (clone $start)->modify('+14 days');
            return [
                'start_date' => $start->format('Y-m-d'),
                'end_date'   => $end->format('Y-m-d'),
                'status'     => 'completed',
            ];
        });
    }
}