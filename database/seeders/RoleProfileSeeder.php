<?php

namespace Database\Seeders;

use App\Models\CoachProfile;
use App\Models\OrganizerProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoleProfileSeeder extends Seeder
{
    public function run(): void
    {
        $organizers = User::where('role', 'organizer')->get();

        $orgNames = [
            'Metro Sports Federation',
            'Elite FA',
            'Pro Circuit Events',
            'Vanguard Sports',
            'Aquatics Federation',
        ];

        foreach ($organizers as $i => $user) {
            OrganizerProfile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'organization_name' => $orgNames[$i % count($orgNames)],
                    'contact_number'    => fake()->phoneNumber(),
                ]
            );
        }

        $coaches = User::where('role', 'coach')->get();

        $specializations = [
            'Offensive Strategy',
            'Defensive Drills',
            'Fitness & Conditioning',
            'Youth Development',
            'Technical Skills',
            'Team Psychology',
        ];

        foreach ($coaches as $i => $user) {
            CoachProfile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'specialization'   => $specializations[$i % count($specializations)],
                    'years_experience' => rand(1, 20),
                ]
            );
        }

        $this->command->info('Organizer profiles: ' . OrganizerProfile::count());
        $this->command->info('Coach profiles: ' . CoachProfile::count());
    }
}