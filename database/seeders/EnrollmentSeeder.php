<?php

namespace Database\Seeders;

use App\Enums\EnrollmentStatus;
use App\Enums\StepProgressStatus;
use App\Models\Enrollment;
use App\Models\LearningPath;
use App\Models\StepProgress;
use App\Models\User;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = LearningPath::where('slug', 'laravel-grundlagen')->first();
        $learners = User::where('role', 'learner')->get();

        if (! $path) {
            $this->command->warn('Learning path not found. Run LearningPathSeeder first.');

            return;
        }

        foreach ($learners as $index => $learner) {
            // Create enrollment
            $enrollment = Enrollment::create([
                'user_id' => $learner->id,
                'learning_path_id' => $path->id,
                'status' => $index < 3 ? EnrollmentStatus::Active : EnrollmentStatus::Completed,
                'progress_percent' => $index < 3 ? rand(10, 80) : 100,
                'started_at' => now()->subDays(rand(5, 30)),
                'completed_at' => $index >= 3 ? now()->subDays(rand(1, 5)) : null,
                'last_activity_at' => now()->subHours(rand(1, 48)),
                'total_time_spent_seconds' => rand(3600, 36000),
                'points_earned' => rand(50, 200),
            ]);

            // Create step progress for first module
            $steps = $path->modules()->first()->steps ?? collect();

            foreach ($steps as $stepIndex => $step) {
                if ($stepIndex < $index + 1) {
                    StepProgress::create([
                        'enrollment_id' => $enrollment->id,
                        'step_id' => $step->id,
                        'status' => $stepIndex < $index ? StepProgressStatus::Completed : StepProgressStatus::InProgress,
                        'started_at' => now()->subDays(rand(1, 10)),
                        'completed_at' => $stepIndex < $index ? now()->subDays(rand(1, 5)) : null,
                        'time_spent_seconds' => rand(300, 1800),
                        'points_earned' => $stepIndex < $index ? $step->points_value : 0,
                    ]);
                }
            }
        }

        $this->command->info('Enrollments seeded successfully!');
    }
}
