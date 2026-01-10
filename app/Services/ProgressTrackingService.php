<?php

namespace App\Services;

use App\Enums\EnrollmentStatus;
use App\Enums\StepProgressStatus;
use App\Events\StepCompleted;
use App\Events\PathCompleted;
use App\Models\Enrollment;
use App\Models\LearningStep;
use App\Models\StepProgress;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ProgressTrackingService
{
    /**
     * Start tracking a step for a user.
     */
    public function startStep(Enrollment $enrollment, LearningStep $step): StepProgress
    {
        return DB::transaction(function () use ($enrollment, $step) {
            $progress = StepProgress::firstOrCreate(
                [
                    'enrollment_id' => $enrollment->id,
                    'step_id' => $step->id,
                ],
                [
                    'status' => StepProgressStatus::InProgress,
                    'started_at' => now(),
                    'time_spent_seconds' => 0,
                ]
            );

            if ($progress->status === StepProgressStatus::NotStarted) {
                $progress->update([
                    'status' => StepProgressStatus::InProgress,
                    'started_at' => now(),
                ]);
            }

            // Update enrollment last activity
            $enrollment->update(['last_activity_at' => now()]);

            return $progress;
        });
    }

    /**
     * Complete a step for a user.
     */
    public function completeStep(Enrollment $enrollment, LearningStep $step): StepProgress
    {
        return DB::transaction(function () use ($enrollment, $step) {
            $progress = StepProgress::updateOrCreate(
                [
                    'enrollment_id' => $enrollment->id,
                    'step_id' => $step->id,
                ],
                [
                    'status' => StepProgressStatus::Completed,
                    'completed_at' => now(),
                    'points_earned' => $step->points_value ?? 0,
                ]
            );

            // Recalculate enrollment progress
            $this->recalculateEnrollmentProgress($enrollment);

            // Check if path is completed
            $this->checkPathCompletion($enrollment);

            // Dispatch event
            event(new StepCompleted($enrollment, $step));

            return $progress;
        });
    }

    /**
     * Skip a step for a user.
     */
    public function skipStep(Enrollment $enrollment, LearningStep $step): StepProgress
    {
        return DB::transaction(function () use ($enrollment, $step) {
            $progress = StepProgress::updateOrCreate(
                [
                    'enrollment_id' => $enrollment->id,
                    'step_id' => $step->id,
                ],
                [
                    'status' => StepProgressStatus::Skipped,
                    'completed_at' => now(),
                    'points_earned' => 0,
                ]
            );

            $this->recalculateEnrollmentProgress($enrollment);

            return $progress;
        });
    }

    /**
     * Add time spent on a step.
     */
    public function addTimeSpent(Enrollment $enrollment, LearningStep $step, int $seconds): void
    {
        DB::transaction(function () use ($enrollment, $step, $seconds) {
            $progress = StepProgress::where('enrollment_id', $enrollment->id)
                ->where('step_id', $step->id)
                ->first();

            if ($progress) {
                $progress->increment('time_spent_seconds', $seconds);
            }

            // Update enrollment total time
            $enrollment->increment('total_time_spent_seconds', $seconds);
            $enrollment->update(['last_activity_at' => now()]);
        });
    }

    /**
     * Recalculate enrollment progress percentage.
     */
    public function recalculateEnrollmentProgress(Enrollment $enrollment): void
    {
        $totalSteps = $enrollment->learningPath->steps()->count();

        if ($totalSteps === 0) {
            $enrollment->update(['progress_percent' => 0]);

            return;
        }

        $completedSteps = $enrollment->stepProgress()
            ->whereIn('status', [StepProgressStatus::Completed, StepProgressStatus::Skipped])
            ->count();

        $progressPercent = round(($completedSteps / $totalSteps) * 100, 2);

        $totalPoints = $enrollment->stepProgress()->sum('points_earned');

        $enrollment->update([
            'progress_percent' => $progressPercent,
            'points_earned' => $totalPoints,
            'last_activity_at' => now(),
        ]);
    }

    /**
     * Check if the learning path is completed.
     */
    public function checkPathCompletion(Enrollment $enrollment): bool
    {
        $path = $enrollment->learningPath;

        // Get required steps count
        $requiredSteps = $path->steps()->where('is_required', true)->count();

        if ($requiredSteps === 0) {
            // If no required steps, use all steps
            $requiredSteps = $path->steps()->count();
        }

        // Get completed required steps
        $completedRequired = $enrollment->stepProgress()
            ->whereHas('step', fn ($q) => $q->where('is_required', true))
            ->where('status', StepProgressStatus::Completed)
            ->count();

        // If no required steps defined, count all completed
        if ($completedRequired === 0) {
            $completedRequired = $enrollment->stepProgress()
                ->where('status', StepProgressStatus::Completed)
                ->count();
        }

        $isComplete = $completedRequired >= $requiredSteps && $requiredSteps > 0;

        if ($isComplete && $enrollment->status !== EnrollmentStatus::Completed) {
            $enrollment->update([
                'status' => EnrollmentStatus::Completed,
                'completed_at' => now(),
                'progress_percent' => 100,
            ]);

            event(new PathCompleted($enrollment));

            return true;
        }

        return false;
    }

    /**
     * Get progress summary for an enrollment.
     */
    public function getProgressSummary(Enrollment $enrollment): array
    {
        $path = $enrollment->learningPath;
        $modules = $path->modules()->with('steps')->ordered()->get();

        $summary = [
            'total_steps' => 0,
            'completed_steps' => 0,
            'in_progress_steps' => 0,
            'total_points' => 0,
            'earned_points' => 0,
            'total_time_seconds' => 0,
            'modules' => [],
        ];

        $completedStepIds = $enrollment->stepProgress()
            ->where('status', StepProgressStatus::Completed)
            ->pluck('step_id')
            ->toArray();

        $inProgressStepIds = $enrollment->stepProgress()
            ->where('status', StepProgressStatus::InProgress)
            ->pluck('step_id')
            ->toArray();

        foreach ($modules as $module) {
            $moduleData = [
                'id' => $module->id,
                'title' => $module->title,
                'total_steps' => $module->steps->count(),
                'completed_steps' => 0,
                'steps' => [],
            ];

            foreach ($module->steps as $step) {
                $summary['total_steps']++;
                $summary['total_points'] += $step->points_value ?? 0;

                $isCompleted = in_array($step->id, $completedStepIds);
                $isInProgress = in_array($step->id, $inProgressStepIds);

                if ($isCompleted) {
                    $summary['completed_steps']++;
                    $moduleData['completed_steps']++;
                } elseif ($isInProgress) {
                    $summary['in_progress_steps']++;
                }

                $moduleData['steps'][] = [
                    'id' => $step->id,
                    'title' => $step->title,
                    'type' => $step->step_type->value,
                    'is_completed' => $isCompleted,
                    'is_in_progress' => $isInProgress,
                    'points' => $step->points_value ?? 0,
                ];
            }

            $summary['modules'][] = $moduleData;
        }

        $summary['earned_points'] = $enrollment->points_earned;
        $summary['total_time_seconds'] = $enrollment->total_time_spent_seconds;
        $summary['progress_percent'] = $enrollment->progress_percent;

        return $summary;
    }

    /**
     * Get the next uncompleted step for an enrollment.
     */
    public function getNextStep(Enrollment $enrollment): ?LearningStep
    {
        $completedStepIds = $enrollment->stepProgress()
            ->where('status', StepProgressStatus::Completed)
            ->pluck('step_id')
            ->toArray();

        $allSteps = $enrollment->learningPath
            ->modules()
            ->ordered()
            ->with(['steps' => fn ($q) => $q->ordered()])
            ->get()
            ->flatMap->steps;

        foreach ($allSteps as $step) {
            if (! in_array($step->id, $completedStepIds)) {
                return $step;
            }
        }

        return $allSteps->first();
    }

    /**
     * Get current step number for an enrollment.
     */
    public function getCurrentStepNumber(Enrollment $enrollment): int
    {
        $nextStep = $this->getNextStep($enrollment);

        if (! $nextStep) {
            return 1;
        }

        $allSteps = $enrollment->learningPath
            ->modules()
            ->ordered()
            ->with(['steps' => fn ($q) => $q->ordered()])
            ->get()
            ->flatMap->steps;

        foreach ($allSteps as $index => $step) {
            if ($step->id === $nextStep->id) {
                return $index + 1;
            }
        }

        return 1;
    }
}
