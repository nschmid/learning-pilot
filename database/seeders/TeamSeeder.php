<?php

namespace Database\Seeders;

use App\Enums\SchoolType;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@learningpilot.ch')->first();
        $instructor1 = User::where('email', 'instructor@learningpilot.ch')->first();
        $instructor2 = User::where('email', 'anna.schmidt@learningpilot.ch')->first();

        // Create main platform team (admin)
        $platformTeam = Team::create([
            'name' => 'LearningPilot',
            'slug' => 'learningpilot',
            'user_id' => $admin->id,
            'personal_team' => false,
            'school_type' => SchoolType::University,
            'currency' => 'chf',
            'locale' => 'de',
            'max_students' => null,
            'max_instructors' => null,
        ]);

        $admin->current_team_id = $platformTeam->id;
        $admin->save();

        // Create demo school 1
        $demoSchool1 = Team::create([
            'name' => 'Berufsschule Zürich',
            'slug' => 'berufsschule-zuerich',
            'user_id' => $instructor1->id,
            'personal_team' => false,
            'school_type' => SchoolType::Vocational,
            'address' => 'Technikumstrasse 21',
            'city' => 'Zürich',
            'postal_code' => '8400',
            'country' => 'CH',
            'currency' => 'chf',
            'locale' => 'de',
            'max_students' => 200,
            'max_instructors' => 20,
            'trial_ends_at' => now()->addDays(14),
        ]);

        $instructor1->current_team_id = $demoSchool1->id;
        $instructor1->save();

        // Create demo school 2
        $demoSchool2 = Team::create([
            'name' => 'Gymnasium Basel',
            'slug' => 'gymnasium-basel',
            'user_id' => $instructor2->id,
            'personal_team' => false,
            'school_type' => SchoolType::Secondary,
            'address' => 'Münsterplatz 8',
            'city' => 'Basel',
            'postal_code' => '4051',
            'country' => 'CH',
            'currency' => 'chf',
            'locale' => 'de',
            'max_students' => 500,
            'max_instructors' => 50,
        ]);

        $instructor2->current_team_id = $demoSchool2->id;
        $instructor2->save();

        // Add instructors to platform team
        $platformTeam->users()->attach($instructor1->id, ['role' => 'instructor']);
        $platformTeam->users()->attach($instructor2->id, ['role' => 'instructor']);

        // Add learners to teams
        $learners = User::where('role', 'learner')->get();
        foreach ($learners as $index => $learner) {
            $team = $index % 2 === 0 ? $demoSchool1 : $demoSchool2;
            $team->users()->attach($learner->id, ['role' => 'learner']);
            $learner->current_team_id = $team->id;
            $learner->save();
        }

        $this->command->info('Teams seeded successfully!');
    }
}
