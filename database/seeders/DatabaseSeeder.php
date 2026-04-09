<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SportSeeder::class,
            UserSeeder::class,
            RoleProfileSeeder::class,
            TeamSeeder::class,
            PlayerProfileSeeder::class,
            TournamentSeeder::class,
            RegistrationSeeder::class,
            MatchSeeder::class,
            MatchTeamSeeder::class,
            ResultSeeder::class,
        ]);
    }
}