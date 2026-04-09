<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SportFactory extends Factory
{
    private static array $sports = [
        ['name' => 'Basketball',   'min' => 2, 'max' => 2],
        ['name' => 'Football',     'min' => 2, 'max' => 2],
        ['name' => 'Volleyball',   'min' => 2, 'max' => 2],
        ['name' => 'Tennis',       'min' => 2, 'max' => 2],
        ['name' => 'Swimming',     'min' => 4, 'max' => 8],
        ['name' => 'Badminton',    'min' => 2, 'max' => 2],
        ['name' => 'Table Tennis', 'min' => 2, 'max' => 2],
        ['name' => 'Athletics',    'min' => 4, 'max' => 16],
    ];

    private static int $index = 0;

    public function definition(): array
    {
        $sport = self::$sports[self::$index % count(self::$sports)];
        self::$index++;

        return [
            'sport_name'          => $sport['name'],
            'min_teams_per_match' => $sport['min'],
            'max_teams_per_match' => $sport['max'],
            'description'         => fake()->sentence(8),
        ];
    }
}