<?php

namespace Tests\Feature\Models\Assessment;

use App\Enums\EnrollmentStatus;
use App\Models\AssessmentAttempt;
use App\Models\Certificate;
use App\Models\Enrollment;
use App\Models\LearningPath;
use App\Models\StepProgress;
use App\Models\TaskSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\ModelTestHelpers;

class EnrollmentTest extends TestCase
{
    use ModelTestHelpers;
    use RefreshDatabase;

    public function test_it_can_create_an_enrollment(): void
    {
        $enrollment = $this->assertModelCanBeCreated(Enrollment::class);

        $this->assertNotNull($enrollment->user_id);
        $this->assertNotNull($enrollment->learning_path_id);
    }

    public function test_fillable_fields_exist_in_database(): void
    {
        $enrollment = Enrollment::factory()->create();

        $this->assertFillableFieldsExist($enrollment);
    }

    public function test_uses_uuids(): void
    {
        $enrollment = Enrollment::factory()->create();

        $this->assertUsesUuids($enrollment);
    }

    public function test_enum_casts_work(): void
    {
        $enrollment = Enrollment::factory()->create(['status' => EnrollmentStatus::Active]);

        $this->assertEnumCast($enrollment, 'status', EnrollmentStatus::class);
    }

    public function test_datetime_casts_work(): void
    {
        $enrollment = Enrollment::factory()->completed()->create();

        $this->assertDatetimeCast($enrollment, 'started_at');
        $this->assertDatetimeCast($enrollment, 'completed_at');
        $this->assertDatetimeCast($enrollment, 'last_activity_at');
    }

    public function test_user_relationship_works(): void
    {
        $user = User::factory()->create();
        $enrollment = Enrollment::factory()->forUser($user)->create();

        $this->assertBelongsToRelationship($enrollment, 'user', User::class);
    }

    public function test_learning_path_relationship_works(): void
    {
        $path = LearningPath::factory()->create();
        $enrollment = Enrollment::factory()->forPath($path)->create();

        $this->assertBelongsToRelationship($enrollment, 'learningPath', LearningPath::class);
    }

    public function test_step_progress_relationship_works(): void
    {
        $enrollment = Enrollment::factory()->create();
        StepProgress::factory()->count(3)->forEnrollment($enrollment)->create();

        $this->assertHasManyRelationship($enrollment, 'stepProgress', StepProgress::class);
    }

    public function test_task_submissions_relationship_works(): void
    {
        $enrollment = Enrollment::factory()->create();
        TaskSubmission::factory()->count(2)->forEnrollment($enrollment)->create();

        $this->assertHasManyRelationship($enrollment, 'taskSubmissions', TaskSubmission::class);
    }

    public function test_assessment_attempts_relationship_works(): void
    {
        $enrollment = Enrollment::factory()->create();
        AssessmentAttempt::factory()->count(2)->forEnrollment($enrollment)->create();

        $this->assertHasManyRelationship($enrollment, 'assessmentAttempts', AssessmentAttempt::class);
    }

    public function test_certificate_relationship_works(): void
    {
        $enrollment = Enrollment::factory()->completed()->create();
        Certificate::factory()->forEnrollment($enrollment)->create();

        $related = $enrollment->certificate;
        $this->assertInstanceOf(Certificate::class, $related);
    }

    public function test_active_scope_works(): void
    {
        Enrollment::factory()->count(3)->create(['status' => EnrollmentStatus::Active]);
        Enrollment::factory()->count(2)->completed()->create();

        $this->assertCount(3, Enrollment::active()->get());
    }

    public function test_completed_scope_works(): void
    {
        Enrollment::factory()->count(2)->create(['status' => EnrollmentStatus::Active]);
        Enrollment::factory()->count(3)->completed()->create();

        $this->assertCount(3, Enrollment::completed()->get());
    }

    public function test_is_active_helper_works(): void
    {
        $active = Enrollment::factory()->create(['status' => EnrollmentStatus::Active]);
        $completed = Enrollment::factory()->completed()->create();

        $this->assertTrue($active->isActive());
        $this->assertFalse($completed->isActive());
    }

    public function test_is_completed_helper_works(): void
    {
        $active = Enrollment::factory()->create(['status' => EnrollmentStatus::Active]);
        $completed = Enrollment::factory()->completed()->create();

        $this->assertFalse($active->isCompleted());
        $this->assertTrue($completed->isCompleted());
    }

    public function test_get_formatted_time_spent_works(): void
    {
        $enrollment = Enrollment::factory()->create([
            'total_time_spent_seconds' => 3720, // 1 hour 2 minutes
        ]);

        $this->assertEquals('1h 2m', $enrollment->getFormattedTimeSpent());
    }
}
