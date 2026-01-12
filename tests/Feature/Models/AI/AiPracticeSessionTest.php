<?php

namespace Tests\Feature\Models\AI;

use App\Models\AiPracticeQuestion;
use App\Models\AiPracticeSession;
use App\Models\LearningPath;
use App\Models\LearningStep;
use App\Models\Module;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\ModelTestHelpers;

class AiPracticeSessionTest extends TestCase
{
    use ModelTestHelpers;
    use RefreshDatabase;

    public function test_it_can_create_an_ai_practice_session(): void
    {
        $session = $this->assertModelCanBeCreated(AiPracticeSession::class);

        $this->assertNotNull($session->question_count);
    }

    public function test_fillable_fields_exist_in_database(): void
    {
        $session = AiPracticeSession::factory()->create();

        $this->assertFillableFieldsExist($session);
    }

    public function test_uses_uuids(): void
    {
        $session = AiPracticeSession::factory()->create();

        $this->assertUsesUuids($session);
    }

    public function test_datetime_casts_work(): void
    {
        $session = AiPracticeSession::factory()->create([
            'started_at' => now(),
            'completed_at' => now(),
        ]);

        $this->assertDatetimeCast($session, 'started_at');
        $this->assertDatetimeCast($session, 'completed_at');
    }

    public function test_array_casts_work(): void
    {
        $session = AiPracticeSession::factory()->create([
            'focus_areas' => ['math', 'science'],
        ]);

        $this->assertArrayCast($session, 'focus_areas');
    }

    public function test_user_relationship_works(): void
    {
        $user = User::factory()->create();
        $session = AiPracticeSession::factory()->forUser($user)->create();

        $this->assertBelongsToRelationship($session, 'user', User::class);
    }

    public function test_questions_relationship_works(): void
    {
        $session = AiPracticeSession::factory()->create();
        AiPracticeQuestion::factory()->count(5)->forSession($session)->create();

        $this->assertHasManyRelationship($session, 'questions', AiPracticeQuestion::class, 5);
    }

    public function test_learning_path_relationship_works(): void
    {
        $path = LearningPath::factory()->create();
        $session = AiPracticeSession::factory()->forPath($path)->create();

        $this->assertEquals($path->id, $session->learning_path_id);
        $this->assertInstanceOf(LearningPath::class, $session->learningPath);
    }

    public function test_module_relationship_works(): void
    {
        $module = Module::factory()->create();
        $session = AiPracticeSession::factory()->forModule($module)->create();

        $this->assertEquals($module->id, $session->module_id);
        $this->assertInstanceOf(Module::class, $session->module);
    }

    public function test_step_relationship_works(): void
    {
        $step = LearningStep::factory()->create();
        $session = AiPracticeSession::factory()->forStep($step)->create();

        $this->assertEquals($step->id, $session->step_id);
        $this->assertInstanceOf(LearningStep::class, $session->step);
    }

    public function test_active_scope_works(): void
    {
        AiPracticeSession::factory()->count(3)->create(['status' => 'active']);
        AiPracticeSession::factory()->count(2)->completed()->create();

        $this->assertCount(3, AiPracticeSession::active()->get());
    }

    public function test_completed_scope_works(): void
    {
        AiPracticeSession::factory()->count(3)->completed()->create();
        AiPracticeSession::factory()->count(2)->create(['status' => 'active']);

        $this->assertCount(3, AiPracticeSession::completed()->get());
    }

    public function test_abandoned_scope_works(): void
    {
        AiPracticeSession::factory()->count(2)->abandoned()->create();
        AiPracticeSession::factory()->count(3)->create(['status' => 'active']);

        $this->assertCount(2, AiPracticeSession::abandoned()->get());
    }

    public function test_for_path_scope_works(): void
    {
        $path = LearningPath::factory()->create();
        AiPracticeSession::factory()->count(2)->forPath($path)->create();
        AiPracticeSession::factory()->count(3)->create();

        $this->assertCount(2, AiPracticeSession::forPath($path)->get());
    }

    public function test_for_module_scope_works(): void
    {
        $module = Module::factory()->create();
        AiPracticeSession::factory()->count(2)->forModule($module)->create();
        AiPracticeSession::factory()->count(3)->create();

        $this->assertCount(2, AiPracticeSession::forModule($module)->get());
    }

    public function test_for_step_scope_works(): void
    {
        $step = LearningStep::factory()->create();
        AiPracticeSession::factory()->count(2)->forStep($step)->create();
        AiPracticeSession::factory()->count(3)->create();

        $this->assertCount(2, AiPracticeSession::forStep($step)->get());
    }

    public function test_score_percent_helper_works(): void
    {
        $session = AiPracticeSession::factory()->create([
            'questions_answered' => 10,
            'correct_answers' => 7,
        ]);

        $this->assertEquals(70.0, $session->scorePercent());
    }

    public function test_score_percent_returns_zero_when_no_questions_answered(): void
    {
        $session = AiPracticeSession::factory()->create([
            'questions_answered' => 0,
            'correct_answers' => 0,
        ]);

        $this->assertEquals(0, $session->scorePercent());
    }

    public function test_remaining_questions_helper_works(): void
    {
        $session = AiPracticeSession::factory()->create([
            'question_count' => 10,
            'questions_answered' => 3,
        ]);

        $this->assertEquals(7, $session->remainingQuestions());
    }

    public function test_progress_percent_helper_works(): void
    {
        $session = AiPracticeSession::factory()->create([
            'question_count' => 10,
            'questions_answered' => 5,
        ]);

        $this->assertEquals(50.0, $session->progressPercent());
    }

    public function test_progress_percent_returns_zero_when_no_questions(): void
    {
        $session = AiPracticeSession::factory()->create([
            'question_count' => 0,
            'questions_answered' => 0,
        ]);

        $this->assertEquals(0, $session->progressPercent());
    }

    public function test_record_answer_increments_counters(): void
    {
        $session = AiPracticeSession::factory()->create([
            'question_count' => 10,
            'questions_answered' => 0,
            'correct_answers' => 0,
        ]);

        $session->recordAnswer(true);
        $this->assertEquals(1, $session->fresh()->questions_answered);
        $this->assertEquals(1, $session->fresh()->correct_answers);

        $session->recordAnswer(false);
        $this->assertEquals(2, $session->fresh()->questions_answered);
        $this->assertEquals(1, $session->fresh()->correct_answers);
    }

    public function test_record_answer_completes_session_when_all_answered(): void
    {
        $session = AiPracticeSession::factory()->create([
            'question_count' => 2,
            'questions_answered' => 1,
            'correct_answers' => 1,
            'status' => 'active',
        ]);

        $session->recordAnswer(true);

        $this->assertEquals('completed', $session->fresh()->status);
        $this->assertNotNull($session->fresh()->completed_at);
    }

    public function test_complete_helper_works(): void
    {
        $session = AiPracticeSession::factory()->create([
            'status' => 'active',
            'completed_at' => null,
        ]);

        $session->complete();

        $this->assertEquals('completed', $session->fresh()->status);
        $this->assertNotNull($session->fresh()->completed_at);
    }

    public function test_abandon_helper_works(): void
    {
        $session = AiPracticeSession::factory()->create([
            'status' => 'active',
        ]);

        $session->abandon();

        $this->assertEquals('abandoned', $session->fresh()->status);
    }

    public function test_is_active_helper_works(): void
    {
        $activeSession = AiPracticeSession::factory()->create(['status' => 'active']);
        $completedSession = AiPracticeSession::factory()->completed()->create();

        $this->assertTrue($activeSession->isActive());
        $this->assertFalse($completedSession->isActive());
    }

    public function test_is_completed_helper_works(): void
    {
        $activeSession = AiPracticeSession::factory()->create(['status' => 'active']);
        $completedSession = AiPracticeSession::factory()->completed()->create();

        $this->assertFalse($activeSession->isCompleted());
        $this->assertTrue($completedSession->isCompleted());
    }

    public function test_adjust_difficulty_increases_on_high_score(): void
    {
        $session = AiPracticeSession::factory()->create([
            'difficulty' => 'intermediate',
            'questions_answered' => 10,
            'correct_answers' => 9,
        ]);

        $newDifficulty = $session->adjustDifficulty();

        $this->assertEquals('advanced', $newDifficulty);
    }

    public function test_adjust_difficulty_decreases_on_low_score(): void
    {
        $session = AiPracticeSession::factory()->create([
            'difficulty' => 'intermediate',
            'questions_answered' => 10,
            'correct_answers' => 4,
        ]);

        $newDifficulty = $session->adjustDifficulty();

        $this->assertEquals('beginner', $newDifficulty);
    }

    public function test_adjust_difficulty_does_not_exceed_bounds(): void
    {
        $maxSession = AiPracticeSession::factory()->create([
            'difficulty' => 'advanced',
            'questions_answered' => 10,
            'correct_answers' => 10,
        ]);

        $minSession = AiPracticeSession::factory()->create([
            'difficulty' => 'beginner',
            'questions_answered' => 10,
            'correct_answers' => 2,
        ]);

        $this->assertEquals('advanced', $maxSession->adjustDifficulty());
        $this->assertEquals('beginner', $minSession->adjustDifficulty());
    }

    public function test_adjust_difficulty_keeps_adaptive(): void
    {
        $session = AiPracticeSession::factory()->create([
            'difficulty' => 'adaptive',
            'questions_answered' => 10,
            'correct_answers' => 10,
        ]);

        $this->assertEquals('adaptive', $session->adjustDifficulty());
    }

    public function test_completed_factory_state_works(): void
    {
        $session = AiPracticeSession::factory()->completed()->create();

        $this->assertEquals('completed', $session->status);
        $this->assertNotNull($session->completed_at);
        $this->assertGreaterThan(0, $session->questions_answered);
    }

    public function test_difficulty_factory_state_works(): void
    {
        $session = AiPracticeSession::factory()->difficulty('advanced')->create();

        $this->assertEquals('advanced', $session->difficulty);
    }

    public function test_with_progress_factory_state_works(): void
    {
        $session = AiPracticeSession::factory()->withProgress(7, 5)->create();

        $this->assertEquals(7, $session->questions_answered);
        $this->assertEquals(5, $session->correct_answers);
    }

    public function test_with_focus_areas_factory_state_works(): void
    {
        $session = AiPracticeSession::factory()->withFocusAreas(['math', 'physics'])->create();

        $this->assertEquals(['math', 'physics'], $session->focus_areas);
    }
}
