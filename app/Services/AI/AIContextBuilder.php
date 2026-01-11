<?php

namespace App\Services\AI;

use App\Models\Enrollment;
use App\Models\LearningPath;
use App\Models\LearningStep;
use App\Models\Module;
use App\Models\Question;
use App\Models\QuestionResponse;
use App\Models\StepProgress;
use App\Models\User;
use Illuminate\Support\Collection;

class AIContextBuilder
{
    /**
     * Build context for question explanation
     */
    public function buildQuestionContext(QuestionResponse $response): array
    {
        $question = $response->question;
        $assessment = $question->assessment;
        $step = $assessment->learningStep;
        $module = $step->module;
        $path = $module->learningPath;

        return [
            'question' => [
                'text' => $question->question_text,
                'type' => $question->question_type->value,
                'explanation' => $question->explanation,
                'points' => $question->points,
            ],
            'user_answer' => $response->user_answer,
            'is_correct' => $response->is_correct,
            'correct_answer' => $this->getCorrectAnswer($question),
            'assessment' => [
                'title' => $assessment->title,
                'type' => $assessment->assessment_type->value,
            ],
            'learning_context' => [
                'step_title' => $step->title,
                'module_title' => $module->title,
                'path_title' => $path->title,
                'difficulty' => $path->difficulty->value,
            ],
        ];
    }

    /**
     * Build context for step hints
     */
    public function buildStepContext(StepProgress $progress): array
    {
        $step = $progress->learningStep;
        $module = $step->module;
        $path = $module->learningPath;
        $enrollment = $progress->enrollment;

        $previousMaterials = $this->getPreviousMaterials($step);

        return [
            'step' => [
                'title' => $step->title,
                'description' => $step->description,
                'type' => $step->step_type->value,
                'estimated_minutes' => $step->estimated_minutes,
            ],
            'materials' => $step->materials->map(fn ($m) => [
                'type' => $m->material_type->value,
                'title' => $m->title,
                'content_preview' => $this->truncateContent($m->content, 500),
            ])->toArray(),
            'progress' => [
                'status' => $progress->status->value,
                'time_spent_minutes' => round($progress->time_spent_seconds / 60),
            ],
            'learning_context' => [
                'module_title' => $module->title,
                'path_title' => $path->title,
                'path_difficulty' => $path->difficulty->value,
                'overall_progress_percent' => $enrollment->progress_percent,
            ],
            'previous_context' => $previousMaterials,
        ];
    }

    /**
     * Build context for AI tutor conversation
     */
    public function buildTutorContext(
        User $user,
        ?LearningPath $path = null,
        ?Module $module = null,
        ?LearningStep $step = null
    ): array {
        $context = [
            'user' => [
                'name' => $user->name,
                'role' => $user->role->value,
            ],
        ];

        if ($path) {
            $enrollment = $user->enrollments()
                ->where('learning_path_id', $path->id)
                ->first();

            $context['learning_path'] = [
                'title' => $path->title,
                'description' => $this->truncateContent($path->description, 300),
                'difficulty' => $path->difficulty->value,
                'category' => $path->category?->name,
            ];

            if ($enrollment) {
                $context['enrollment'] = [
                    'progress_percent' => $enrollment->progress_percent,
                    'status' => $enrollment->status->value,
                    'points_earned' => $enrollment->points_earned,
                ];
            }
        }

        if ($module) {
            $context['module'] = [
                'title' => $module->title,
                'description' => $this->truncateContent($module->description, 200),
                'position' => $module->position,
            ];
        }

        if ($step) {
            $context['step'] = [
                'title' => $step->title,
                'description' => $step->description,
                'type' => $step->step_type->value,
            ];

            $context['materials'] = $step->materials->map(fn ($m) => [
                'type' => $m->material_type->value,
                'title' => $m->title,
                'content_preview' => $this->truncateContent($m->content, 300),
            ])->toArray();
        }

        return $context;
    }

    /**
     * Build context for module summary
     */
    public function buildModuleContext(Module $module, User $user): array
    {
        $path = $module->learningPath;
        $enrollment = $user->enrollments()
            ->where('learning_path_id', $path->id)
            ->first();

        return [
            'module' => [
                'title' => $module->title,
                'description' => $module->description,
                'position' => $module->position,
                'total_steps' => $module->steps->count(),
            ],
            'learning_path' => [
                'title' => $path->title,
                'difficulty' => $path->difficulty->value,
            ],
            'steps' => $module->steps->map(fn ($step) => [
                'title' => $step->title,
                'type' => $step->step_type->value,
                'materials' => $step->materials->map(fn ($m) => [
                    'type' => $m->material_type->value,
                    'title' => $m->title,
                    'content' => $this->truncateContent($m->content, 500),
                ])->toArray(),
            ])->toArray(),
            'user_progress' => $enrollment ? [
                'progress_percent' => $enrollment->progress_percent,
                'completed_steps' => $enrollment->stepProgress()
                    ->whereHas('learningStep', fn ($q) => $q->where('module_id', $module->id))
                    ->where('status', 'completed')
                    ->count(),
            ] : null,
        ];
    }

    /**
     * Build context for summary generation (alias for buildModuleContext).
     */
    public function buildSummaryContext(Module $module, ?User $user = null): array
    {
        $context = [
            'module' => [
                'title' => $module->title,
                'description' => $module->description,
                'position' => $module->position,
                'total_steps' => $module->steps->count(),
            ],
            'learning_path' => [
                'title' => $module->learningPath->title,
                'difficulty' => $module->learningPath->difficulty->value,
            ],
            'content' => $module->steps->map(fn ($step) => [
                'title' => $step->title,
                'type' => $step->step_type->value,
                'materials' => $step->materials->map(fn ($m) => [
                    'type' => $m->material_type->value,
                    'title' => $m->title,
                    'content' => $this->truncateContent($m->content, 1000),
                ])->toArray(),
            ])->toArray(),
        ];

        if ($user) {
            $enrollment = $user->enrollments()
                ->where('learning_path_id', $module->learning_path_id)
                ->first();

            if ($enrollment) {
                $context['user_progress'] = [
                    'overall_accuracy' => $this->calculateOverallAccuracy($enrollment),
                    'completed_steps' => $enrollment->stepProgress()
                        ->whereHas('learningStep', fn ($q) => $q->where('module_id', $module->id))
                        ->where('status', 'completed')
                        ->count(),
                ];
            }
        }

        return $context;
    }

    /**
     * Build context for practice question generation
     */
    public function buildPracticeContext(
        User $user,
        LearningPath $path,
        ?Module $module = null,
        ?string $difficulty = null
    ): array {
        $enrollment = $user->enrollments()
            ->where('learning_path_id', $path->id)
            ->first();

        $context = [
            'learning_path' => [
                'title' => $path->title,
                'description' => $this->truncateContent($path->description, 300),
                'difficulty' => $path->difficulty->value,
            ],
            'target_difficulty' => $difficulty ?? $path->difficulty->value,
        ];

        if ($module) {
            $context['module'] = [
                'title' => $module->title,
                'description' => $module->description,
            ];
            $context['content'] = $module->steps->flatMap(fn ($step) => $step->materials->map(fn ($m) => [
                'title' => $m->title,
                'content' => $this->truncateContent($m->content, 800),
            ]))->toArray();
        }

        if ($enrollment) {
            $weakAreas = $this->identifyWeakAreas($enrollment);
            $context['user_performance'] = [
                'overall_progress' => $enrollment->progress_percent,
                'weak_areas' => $weakAreas,
            ];
        }

        return $context;
    }

    protected function getCorrectAnswer(Question $question): mixed
    {
        $correctOptions = $question->answerOptions
            ->where('is_correct', true)
            ->pluck('option_text')
            ->toArray();

        return count($correctOptions) === 1
            ? $correctOptions[0]
            : $correctOptions;
    }

    protected function getPreviousMaterials(LearningStep $step): array
    {
        $module = $step->module;
        $previousSteps = $module->steps
            ->where('position', '<', $step->position)
            ->take(3);

        return $previousSteps->flatMap(fn ($s) => $s->materials->map(fn ($m) => [
            'step_title' => $s->title,
            'material_title' => $m->title,
            'content_preview' => $this->truncateContent($m->content, 200),
        ]))->toArray();
    }

    /**
     * Calculate overall accuracy for an enrollment.
     */
    protected function calculateOverallAccuracy(Enrollment $enrollment): float
    {
        $responses = QuestionResponse::query()
            ->whereHas('attempt', fn ($q) => $q->where('enrollment_id', $enrollment->id))
            ->get();

        if ($responses->isEmpty()) {
            return 0.0;
        }

        $correct = $responses->where('is_correct', true)->count();

        return round(($correct / $responses->count()) * 100, 1);
    }

    protected function identifyWeakAreas(Enrollment $enrollment): array
    {
        $incorrectResponses = QuestionResponse::query()
            ->whereHas('attempt', fn ($q) => $q->where('enrollment_id', $enrollment->id))
            ->where('is_correct', false)
            ->with('question.assessment.learningStep.module')
            ->get();

        $weakModules = $incorrectResponses
            ->groupBy(fn ($r) => $r->question->assessment->learningStep->module->id)
            ->map(fn ($responses, $moduleId) => [
                'module_id' => $moduleId,
                'module_title' => $responses->first()->question->assessment->learningStep->module->title,
                'incorrect_count' => $responses->count(),
            ])
            ->sortByDesc('incorrect_count')
            ->take(3)
            ->values()
            ->toArray();

        return $weakModules;
    }

    protected function truncateContent(?string $content, int $maxLength): string
    {
        if (! $content) {
            return '';
        }

        $stripped = strip_tags($content);

        if (strlen($stripped) <= $maxLength) {
            return $stripped;
        }

        return substr($stripped, 0, $maxLength).'...';
    }
}
