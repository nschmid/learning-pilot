<?php

namespace App\Actions\LearningPath;

use App\Models\LearningPath;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class PublishLearningPathAction
{
    /**
     * Publish a learning path.
     */
    public function execute(LearningPath $path, User $publisher): LearningPath
    {
        $this->validate($path);

        $path->update([
            'is_published' => true,
            'published_at' => now(),
        ]);

        activity()
            ->performedOn($path)
            ->causedBy($publisher)
            ->log('published');

        return $path->fresh();
    }

    /**
     * Unpublish a learning path.
     */
    public function unpublish(LearningPath $path, User $user): LearningPath
    {
        $path->update([
            'is_published' => false,
        ]);

        activity()
            ->performedOn($path)
            ->causedBy($user)
            ->log('unpublished');

        return $path->fresh();
    }

    /**
     * Validate that the path can be published.
     */
    protected function validate(LearningPath $path): void
    {
        $errors = [];

        // Must have at least one module
        if ($path->modules()->count() === 0) {
            $errors['modules'] = __('Ein Lernpfad benötigt mindestens ein Modul.');
        }

        // Must have at least one step per module
        foreach ($path->modules as $module) {
            if ($module->steps()->count() === 0) {
                $errors['steps'] = __('Jedes Modul benötigt mindestens einen Schritt.');
                break;
            }
        }

        // Must have a description
        if (empty($path->description)) {
            $errors['description'] = __('Eine Beschreibung ist erforderlich.');
        }

        // Must have a category
        if (empty($path->category_id)) {
            $errors['category'] = __('Eine Kategorie ist erforderlich.');
        }

        if (! empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }
}
