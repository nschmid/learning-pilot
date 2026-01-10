<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@learningpilot.ch',
            'password' => Hash::make('password'),
            'role' => UserRole::Admin,
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        // Create Instructor users
        $instructor1 = User::create([
            'name' => 'Max Mustermann',
            'email' => 'instructor@learningpilot.ch',
            'password' => Hash::make('password'),
            'role' => UserRole::Instructor,
            'email_verified_at' => now(),
            'is_active' => true,
            'bio' => 'Erfahrener Dozent für Webentwicklung und Programmierung.',
        ]);

        $instructor2 = User::create([
            'name' => 'Anna Schmidt',
            'email' => 'anna.schmidt@learningpilot.ch',
            'password' => Hash::make('password'),
            'role' => UserRole::Instructor,
            'email_verified_at' => now(),
            'is_active' => true,
            'bio' => 'Spezialistin für Datenanalyse und Machine Learning.',
        ]);

        // Create Learner users
        $learners = [
            ['name' => 'Lisa Müller', 'email' => 'lisa.mueller@example.com'],
            ['name' => 'Thomas Weber', 'email' => 'thomas.weber@example.com'],
            ['name' => 'Sarah Fischer', 'email' => 'sarah.fischer@example.com'],
            ['name' => 'Michael Braun', 'email' => 'michael.braun@example.com'],
            ['name' => 'Julia Hoffmann', 'email' => 'julia.hoffmann@example.com'],
        ];

        foreach ($learners as $learnerData) {
            User::create([
                'name' => $learnerData['name'],
                'email' => $learnerData['email'],
                'password' => Hash::make('password'),
                'role' => UserRole::Learner,
                'email_verified_at' => now(),
                'is_active' => true,
            ]);
        }

        $this->command->info('Users seeded successfully!');
    }
}
