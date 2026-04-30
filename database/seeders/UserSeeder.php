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

            // Fixed dev accounts
            User::firstOrCreate(['email' => 'admin@zentry.com'], [
                'first_name' => 'System', 'last_name' => 'Admin',
                'password' => Hash::make('password'), 'role' => 'admin',
                'is_active' => true, 'email_verified_at' => now(),
            ]);
            User::firstOrCreate(['email' => 'organizer@zentry.com'], [
                'first_name' => 'Marcus', 'last_name' => 'Sterling',
                'password' => Hash::make('password'), 'role' => 'organizer',
                'is_active' => true, 'email_verified_at' => now(),
            ]);
            User::firstOrCreate(['email' => 'coach@zentry.com'], [
                'first_name' => 'Elena', 'last_name' => 'Rodriguez',
                'password' => Hash::make('password'), 'role' => 'coach',
                'is_active' => true, 'email_verified_at' => now(),
            ]);
            User::firstOrCreate(['email' => 'player@zentry.com'], [
                'first_name' => 'David', 'last_name' => 'Chen',
                'password' => Hash::make('password'), 'role' => 'player',
                'is_active' => true, 'email_verified_at' => now(),
            ]);
            User::firstOrCreate(['email' => 'fan@zentry.com'], [
                'first_name' => 'Sarah', 'last_name' => 'Jenkins',
                'password' => Hash::make('password'), 'role' => 'fan',
                'is_active' => true, 'email_verified_at' => now(),
            ]);

            // Extra named organizers
            $organizers = [
                ['first_name' => 'James',   'last_name' => 'Ortega',    'email' => 'james.organizer@zentry.com'],
                ['first_name' => 'Priya',   'last_name' => 'Kapoor',    'email' => 'priya.organizer@zentry.com'],
                ['first_name' => 'Leo',     'last_name' => 'Nakamura',  'email' => 'leo.organizer@zentry.com'],
                ['first_name' => 'Carmen',  'last_name' => 'Delgado',   'email' => 'carmen.organizer@zentry.com'],
            ];
            foreach ($organizers as $o) {
                User::firstOrCreate(['email' => $o['email']], array_merge($o, [
                    'password' => Hash::make('password'), 'role' => 'organizer',
                    'is_active' => true, 'email_verified_at' => now(),
                ]));
            }

            // Named coaches per sport (Basketball, Football, Volleyball, Tennis, Swimming, Badminton, Table Tennis, Athletics)
            $coaches = [
                ['first_name' => 'Rick',    'last_name' => 'Torres',    'email' => 'coach.basketball1@zentry.com'],
                ['first_name' => 'Mia',     'last_name' => 'Fujimoto',  'email' => 'coach.basketball2@zentry.com'],
                ['first_name' => 'Andre',   'last_name' => 'Mensah',    'email' => 'coach.basketball3@zentry.com'],
                ['first_name' => 'Sofia',   'last_name' => 'Brennan',   'email' => 'coach.football1@zentry.com'],
                ['first_name' => 'Kwame',   'last_name' => 'Asante',    'email' => 'coach.football2@zentry.com'],
                ['first_name' => 'Hana',    'last_name' => 'Park',      'email' => 'coach.volleyball1@zentry.com'],
                ['first_name' => 'Carlos',  'last_name' => 'Vega',      'email' => 'coach.volleyball2@zentry.com'],
                ['first_name' => 'Nina',    'last_name' => 'Reeves',    'email' => 'coach.tennis1@zentry.com'],
                ['first_name' => 'Tom',     'last_name' => 'Lindqvist', 'email' => 'coach.swimming1@zentry.com'],
                ['first_name' => 'Grace',   'last_name' => 'Okafor',    'email' => 'coach.badminton1@zentry.com'],
                ['first_name' => 'Yusuf',   'last_name' => 'Saleh',     'email' => 'coach.tabletennis1@zentry.com'],
                ['first_name' => 'Dana',    'last_name' => 'Holloway',  'email' => 'coach.athletics1@zentry.com'],
            ];
            foreach ($coaches as $c) {
                User::firstOrCreate(['email' => $c['email']], array_merge($c, [
                    'password' => Hash::make('password'), 'role' => 'coach',
                    'is_active' => true, 'email_verified_at' => now(),
                ]));
            }

            // Named players — Basketball (18)
            $basketballPlayers = [
                ['first_name' => 'Jordan',  'last_name' => 'Miles'],
                ['first_name' => 'Tyrese',  'last_name' => 'Blake'],
                ['first_name' => 'Marcus',  'last_name' => 'Webb'],
                ['first_name' => 'DeShawn', 'last_name' => 'Grant'],
                ['first_name' => 'Elijah',  'last_name' => 'Carter'],
                ['first_name' => 'Isaiah',  'last_name' => 'Brooks'],
                ['first_name' => 'Malik',   'last_name' => 'Odom'],
                ['first_name' => 'Dante',   'last_name' => 'Rivera'],
                ['first_name' => 'Jaylen',  'last_name' => 'Cross'],
                ['first_name' => 'Amir',    'last_name' => 'Hassan'],
                ['first_name' => 'Trey',    'last_name' => 'Coleman'],
                ['first_name' => 'Zion',    'last_name' => 'Pearce'],
                ['first_name' => 'Kofi',    'last_name' => 'Ampah'],
                ['first_name' => 'Luca',    'last_name' => 'Ferraro'],
                ['first_name' => 'Emeka',   'last_name' => 'Nwosu'],
                ['first_name' => 'Devon',   'last_name' => 'Shaw'],
                ['first_name' => 'Chris',   'last_name' => 'Ng'],
                ['first_name' => 'Soren',   'last_name' => 'Lindberg'],
            ];

            // Football players (18)
            $footballPlayers = [
                ['first_name' => 'Fabio',   'last_name' => 'Monteiro'],
                ['first_name' => 'Leandro', 'last_name' => 'Costa'],
                ['first_name' => 'Osei',    'last_name' => 'Darko'],
                ['first_name' => 'Hamid',   'last_name' => 'Rahimi'],
                ['first_name' => 'Rui',     'last_name' => 'Tavares'],
                ['first_name' => 'Nikos',   'last_name' => 'Papadopoulos'],
                ['first_name' => 'Andres',  'last_name' => 'Herrera'],
                ['first_name' => 'Viktor',  'last_name' => 'Sorokin'],
                ['first_name' => 'Kwesi',   'last_name' => 'Boateng'],
                ['first_name' => 'Emre',    'last_name' => 'Yildiz'],
                ['first_name' => 'Gabriel', 'last_name' => 'Alves'],
                ['first_name' => 'Tomás',   'last_name' => 'Novak'],
                ['first_name' => 'Lukas',   'last_name' => 'Becker'],
                ['first_name' => 'Seun',    'last_name' => 'Adeyemi'],
                ['first_name' => 'Diego',   'last_name' => 'Fuentes'],
                ['first_name' => 'Pawel',   'last_name' => 'Wozniak'],
                ['first_name' => 'Matteo',  'last_name' => 'Romano'],
                ['first_name' => 'Bilal',   'last_name' => 'Mansour'],
            ];

            // Volleyball players (12)
            $volleyballPlayers = [
                ['first_name' => 'Yuki',    'last_name' => 'Tanaka'],
                ['first_name' => 'Marta',   'last_name' => 'Oliveira'],
                ['first_name' => 'Ifeoma',  'last_name' => 'Eze'],
                ['first_name' => 'Katya',   'last_name' => 'Morozova'],
                ['first_name' => 'Selin',   'last_name' => 'Yilmaz'],
                ['first_name' => 'Amara',   'last_name' => 'Diallo'],
                ['first_name' => 'Hira',    'last_name' => 'Baig'],
                ['first_name' => 'Elena',   'last_name' => 'Petrov'],
                ['first_name' => 'Naomi',   'last_name' => 'Osei'],
                ['first_name' => 'Ling',    'last_name' => 'Wei'],
                ['first_name' => 'Fatima',  'last_name' => 'Al-Rashid'],
                ['first_name' => 'Bianca',  'last_name' => 'Santos'],
            ];

            // Tennis / other sport players (12)
            $otherPlayers = [
                ['first_name' => 'Alex',    'last_name' => 'Mercer'],
                ['first_name' => 'Sam',     'last_name' => 'Weston'],
                ['first_name' => 'Robin',   'last_name' => 'Johansson'],
                ['first_name' => 'Casey',   'last_name' => 'Flynn'],
                ['first_name' => 'Morgan',  'last_name' => 'Price'],
                ['first_name' => 'Jamie',   'last_name' => 'Sullivan'],
                ['first_name' => 'Taylor',  'last_name' => 'Huang'],
                ['first_name' => 'Quinn',   'last_name' => 'Nakamura'],
                ['first_name' => 'Riley',   'last_name' => 'Patel'],
                ['first_name' => 'Drew',    'last_name' => 'Vasquez'],
                ['first_name' => 'Avery',   'last_name' => 'Kim'],
                ['first_name' => 'Reese',   'last_name' => 'Molina'],
            ];

            $allPlayers = array_merge($basketballPlayers, $footballPlayers, $volleyballPlayers, $otherPlayers);
            foreach ($allPlayers as $i => $p) {
                $email = strtolower($p['first_name'] . '.' . $p['last_name'] . $i . '@zentry.com');
                User::firstOrCreate(['email' => $email], array_merge($p, [
                    'password' => Hash::make('password'), 'role' => 'player',
                    'is_active' => true, 'email_verified_at' => now(),
                ]));
            }

            // Fans
            $fans = [
                ['first_name' => 'Ben',     'last_name' => 'Walker',   'email' => 'ben.fan@zentry.com'],
                ['first_name' => 'Lily',    'last_name' => 'Chang',    'email' => 'lily.fan@zentry.com'],
                ['first_name' => 'Omar',    'last_name' => 'Farouq',   'email' => 'omar.fan@zentry.com'],
                ['first_name' => 'Chloe',   'last_name' => 'Dubois',   'email' => 'chloe.fan@zentry.com'],
                ['first_name' => 'Nathan',  'last_name' => 'Marsh',    'email' => 'nathan.fan@zentry.com'],
            ];
            foreach ($fans as $f) {
                User::firstOrCreate(['email' => $f['email']], array_merge($f, [
                    'password' => Hash::make('password'), 'role' => 'fan',
                    'is_active' => true, 'email_verified_at' => now(),
                ]));
            }
        });

        $this->command->info('Users seeded: ' . User::count());
    }
}