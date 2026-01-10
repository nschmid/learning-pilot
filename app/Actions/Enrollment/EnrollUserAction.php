<?php

namespace App\Actions\Enrollment;

use App\Enums\EnrollmentStatus;
use App\Events\EnrollmentCreated;
use App\Models\Enrollment;
use App\Models\LearningPath;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class EnrollUserAction
{
    /**
     * Enroll a user in a learning path.
     */
    public function execute(User $user, LearningPath $path): Enrollment
    {
        $this->validate($user, $path);

        $enrollment = Enrollment::create([
            'user_id' => $user->id,
            'learning_path_id' => $path->id,
            'status' => EnrollmentStatus::Active,
            'progress_percent' => 0,
            'started_at' => now(),
            'last_activity_at' => now(),
        ]);

        event(new EnrollmentCreated($enrollment));

        activity()
            ->performedOn($enrollment)
            ->causedBy($user)
            ->withProperties(['path_id' => $path->id])
            ->log('enrolled');

        return $enrollment;
    }

    /**
     * Validate enrollment eligibility.
     */
    protected function validate(User $user, LearningPath $path): void
    {
        $errors = [];

        // Check if already enrolled
        $existingEnrollment = Enrollment::where('user_id', $user->id)
            ->where('learning_path_id', $path->id)
            ->first();

        if ($existingEnrollment) {
            $errors['enrollment'] = __('Du bist bereits in diesem Lernpfad eingeschrieben.');
        }

        // Check if path is published
        if (! $path->is_published) {
            $errors['path'] = __('Dieser Lernpfad ist nicht veröffentlicht.');
        }

        // Check prerequisites
        foreach ($path->prerequisites as $prerequisite) {
            $completedPrereq = Enrollment::where('user_id', $user->id)
                ->where('learning_path_id', $prerequisite->id)
                ->where('status', EnrollmentStatus::Completed)
                ->exists();

            if (! $completedPrereq) {
                $errors['prerequisites'] = __('Bitte schliesse zuerst den erforderlichen Lernpfad ab: :path', [
                    'path' => $prerequisite->title,
                ]);
                break;
            }
        }

        if (! empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }
}
