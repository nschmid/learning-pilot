<?php

namespace App\Actions\LearningPath;

use App\Models\LearningPath;
use App\Models\User;

class DuplicateLearningPathAction
{
    /**
     * Duplicate a learning path with all its content.
     */
    public function execute(LearningPath $original, User $newCreator): LearningPath
    {
        $newPath = $original->replicate([
            'id',
            'slug',
            'created_at',
            'updated_at',
            'published_at',
        ]);

        $newPath->creator_id = $newCreator->id;
        $newPath->team_id = $newCreator->currentTeam?->id;
        $newPath->title = $original->title . ' (Kopie)';
        $newPath->slug = null; // Let the model generate a new slug
        $newPath->is_published = false;
        $newPath->published_at = null;
        $newPath->version = 1;
        $newPath->save();

        // Copy tags
        $newPath->tags()->attach($original->tags->pluck('id'));

        // Copy modules with steps and content
        foreach ($original->modules()->ordered()->get() as $module) {
            $this->duplicateModule($module, $newPath);
        }

        activity()
            ->performedOn($newPath)
            ->causedBy($newCreator)
            ->withProperties(['original_id' => $original->id])
            ->log('duplicated');

        return $newPath;
    }

    /**
     * Duplicate a module.
     */
    protected function duplicateModule($module, LearningPath $newPath): void
    {
        $newModule = $module->replicate(['id', 'created_at', 'updated_at']);
        $newModule->learning_path_id = $newPath->id;
        $newModule->save();

        foreach ($module->steps()->ordered()->get() as $step) {
            $this->duplicateStep($step, $newModule);
        }
    }

    /**
     * Duplicate a step with its content.
     */
    protected function duplicateStep($step, $newModule): void
    {
        $newStep = $step->replicate(['id', 'created_at', 'updated_at']);
        $newStep->module_id = $newModule->id;
        $newStep->save();

        // Duplicate materials
        foreach ($step->materials()->ordered()->get() as $material) {
            $newMaterial = $material->replicate(['id', 'created_at', 'updated_at']);
            $newMaterial->step_id = $newStep->id;
            $newMaterial->save();
        }

        // Duplicate tasks
        foreach ($step->tasks as $task) {
            $newTask = $task->replicate(['id', 'created_at', 'updated_at']);
            $newTask->step_id = $newStep->id;
            $newTask->save();
        }

        // Duplicate assessments with questions
        foreach ($step->assessments as $assessment) {
            $this->duplicateAssessment($assessment, $newStep);
        }
    }

    /**
     * Duplicate an assessment with questions and options.
     */
    protected function duplicateAssessment($assessment, $newStep): void
    {
        $newAssessment = $assessment->replicate(['id', 'created_at', 'updated_at']);
        $newAssessment->step_id = $newStep->id;
        $newAssessment->save();

        foreach ($assessment->questions()->ordered()->get() as $question) {
            $newQuestion = $question->replicate(['id', 'created_at', 'updated_at']);
            $newQuestion->assessment_id = $newAssessment->id;
            $newQuestion->save();

            foreach ($question->options()->ordered()->get() as $option) {
                $newOption = $option->replicate(['id', 'created_at', 'updated_at']);
                $newOption->question_id = $newQuestion->id;
                $newOption->save();
            }
        }
    }
}
