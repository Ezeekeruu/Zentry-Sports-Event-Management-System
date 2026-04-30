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
            'organizer@zentry.com'       => 'Metro Sports Federation',
            'james.organizer@zentry.com' => 'Elite FA',
            'priya.organizer@zentry.com' => 'Pro Circuit Events',
            'leo.organizer@zentry.com'   => 'Vanguard Sports',
            'carmen.organizer@zentry.com'=> 'Aquatics Federation',
        ];

        foreach ($organizers as $i => $user) {
            OrganizerProfile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'organization_name' => $orgNames[$user->email] ?? 'Sports Organization ' . ($i + 1),
                    'contact_number'    => fake()->phoneNumber(),
                ]
            );
        }

        $coaches = User::where('role', 'coach')->get();

        $coachSpecs = [
            'coach@zentry.com'              => 'Offensive Strategy',
            'coach.basketball1@zentry.com'  => 'Defensive Drills',
            'coach.basketball2@zentry.com'  => 'Fitness & Conditioning',
            'coach.basketball3@zentry.com'  => 'Youth Development',
            'coach.football1@zentry.com'    => 'Technical Skills',
            'coach.football2@zentry.com'    => 'Team Psychology',
            'coach.volleyball1@zentry.com'  => 'Offensive Strategy',
            'coach.volleyball2@zentry.com'  => 'Defensive Drills',
            'coach.tennis1@zentry.com'      => 'Technical Skills',
            'coach.swimming1@zentry.com'    => 'Fitness & Conditioning',
            'coach.badminton1@zentry.com'   => 'Technical Skills',
            'coach.tabletennis1@zentry.com' => 'Offensive Strategy',
            'coach.athletics1@zentry.com'   => 'Fitness & Conditioning',
        ];

        $coachExp = [
            'coach@zentry.com'              => 8,
            'coach.basketball1@zentry.com'  => 12,
            'coach.basketball2@zentry.com'  => 6,
            'coach.basketball3@zentry.com'  => 9,
            'coach.football1@zentry.com'    => 15,
            'coach.football2@zentry.com'    => 7,
            'coach.volleyball1@zentry.com'  => 5,
            'coach.volleyball2@zentry.com'  => 10,
            'coach.tennis1@zentry.com'      => 11,
            'coach.swimming1@zentry.com'    => 4,
            'coach.badminton1@zentry.com'   => 6,
            'coach.tabletennis1@zentry.com' => 3,
            'coach.athletics1@zentry.com'   => 13,
        ];

        foreach ($coaches as $i => $user) {
            CoachProfile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'specialization'   => $coachSpecs[$user->email] ?? 'General Coaching',
                    'years_experience' => $coachExp[$user->email]   ?? rand(1, 15),
                ]
            );
        }

        $this->command->info('Organizer profiles: ' . OrganizerProfile::count());
        $this->command->info('Coach profiles: '     . CoachProfile::count());
    }
}