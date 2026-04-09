<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::withoutEvents(function () {

            User::firstOrCreate(['email' => 'admin@zentry.com'], [
                'first_name'        => 'System',
                'last_name'         => 'Admin',
                'password'          => Hash::make('password'),
                'role'              => 'admin',
                'is_active'         => true,
                'email_verified_at' => now(),
            ]);

            User::firstOrCreate(['email' => 'organizer@zentry.com'], [
                'first_name'        => 'Marcus',
                'last_name'         => 'Sterling',
                'password'          => Hash::make('password'),
                'role'              => 'organizer',
                'is_active'         => true,
                'email_verified_at' => now(),
            ]);

            User::firstOrCreate(['email' => 'coach@zentry.com'], [
                'first_name'        => 'Elena',
                'last_name'         => 'Rodriguez',
                'password'          => Hash::make('password'),
                'role'              => 'coach',
                'is_active'         => true,
                'email_verified_at' => now(),
            ]);

            User::firstOrCreate(['email' => 'player@zentry.com'], [
                'first_name'        => 'David',
                'last_name'         => 'Chen',
                'password'          => Hash::make('password'),
                'role'              => 'player',
                'is_active'         => true,
                'email_verified_at' => now(),
            ]);

            User::firstOrCreate(['email' => 'fan@zentry.com'], [
                'first_name'        => 'Sarah',
                'last_name'         => 'Jenkins',
                'password'          => Hash::make('password'),
                'role'              => 'fan',
                'is_active'         => true,
                'email_verified_at' => now(),
            ]);

            User::factory()->organizer()->count(4)->create();
            User::factory()->coach()->count(10)->create();
            User::factory()->player()->count(50)->create();
            User::factory()->fan()->count(20)->create();
        });

        $this->command->info('Users seeded: ' . User::count());
        $this->command->line('All passwords: password');
    }
}