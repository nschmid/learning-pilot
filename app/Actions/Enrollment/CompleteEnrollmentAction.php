<?php

namespace App\Actions\Enrollment;

use App\Actions\Certificate\GenerateCertificateAction;
use App\Enums\EnrollmentStatus;
use App\Events\PathCompleted;
use App\Models\Enrollment;

class CompleteEnrollmentAction
{
    public function __construct(
        protected GenerateCertificateAction $generateCertificate
    ) {}

    /**
     * Mark an enrollment as completed.
     */
    public function execute(Enrollment $enrollment): Enrollment
    {
        $enrollment->update([
            'status' => EnrollmentStatus::Completed,
            'completed_at' => now(),
            'progress_percent' => 100,
        ]);

        // Generate certificate
        $this->generateCertificate->execute($enrollment);

        // Dispatch event
        event(new PathCompleted($enrollment));

        activity()
            ->performedOn($enrollment)
            ->causedBy($enrollment->user)
            ->log('completed');

        return $enrollment->fresh();
    }

    /**
     * Check if enrollment should be marked as completed.
     */
    public function checkCompletion(Enrollment $enrollment): bool
    {
        $path = $enrollment->learningPath;
        $totalSteps = $path->modules->sum(fn ($m) => $m->steps->count());

        if ($totalSteps === 0) {
            return false;
        }

        $completedSteps = $enrollment->stepProgress()
            ->where('status', 'completed')
            ->count();

        return $completedSteps >= $totalSteps;
    }

    /**
     * Complete enrollment if all steps are done.
     */
    public function completeIfFinished(Enrollment $enrollment): ?Enrollment
    {
        if ($this->checkCompletion($enrollment) && $enrollment->status !== EnrollmentStatus::Completed) {
            return $this->execute($enrollment);
        }

        return null;
    }
}
