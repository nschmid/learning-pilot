<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            TeamSeeder::class,
            CategorySeeder::class,
            TagSeeder::class,
            LearningPathSeeder::class,
            AssessmentSeeder::class,
            TaskSeeder::class,
            EnrollmentSeeder::class,
        ]);

        $this->command->info('All seeders completed successfully!');
    }
}
