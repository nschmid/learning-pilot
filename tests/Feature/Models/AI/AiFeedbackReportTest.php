<?php

namespace Tests\Feature\Models\AI;

use App\Enums\AiFeedbackType;
use App\Models\AiFeedbackReport;
use App\Models\AiGeneratedContent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\ModelTestHelpers;

class AiFeedbackReportTest extends TestCase
{
    use ModelTestHelpers;
    use RefreshDatabase;

    public function test_it_can_create_an_ai_feedback_report(): void
    {
        $report = $this->assertModelCanBeCreated(AiFeedbackReport::class);

        $this->assertNotNull($report->feedback_type);
    }

    public function test_fillable_fields_exist_in_database(): void
    {
        $report = AiFeedbackReport::factory()->create();

        $this->assertFillableFieldsExist($report);
    }

    public function test_uses_uuids(): void
    {
        $report = AiFeedbackReport::factory()->create();

        $this->assertUsesUuids($report);
    }

    public function test_datetime_casts_work(): void
    {
        $report = AiFeedbackReport::factory()->resolved()->create();

        $this->assertDatetimeCast($report, 'resolved_at');
    }

    public function test_enum_casts_work(): void
    {
        $report = AiFeedbackReport::factory()->create([
            'feedback_type' => AiFeedbackType::Inaccurate,
        ]);

        $this->assertEnumCast($report, 'feedback_type', AiFeedbackType::class);
    }

    public function test_user_relationship_works(): void
    {
        $user = User::factory()->create();
        $report = AiFeedbackReport::factory()->forUser($user)->create();

        $this->assertBelongsToRelationship($report, 'user', User::class);
    }

    public function test_ai_generated_content_relationship_works(): void
    {
        $content = AiGeneratedContent::factory()->create();
        $report = AiFeedbackReport::factory()->forContent($content)->create();

        $this->assertInstanceOf(AiGeneratedContent::class, $report->aiGeneratedContent);
        $this->assertEquals($content->id, $report->aiGeneratedContent->id);
    }

    public function test_resolved_by_relationship_works(): void
    {
        $resolver = User::factory()->create();
        $report = AiFeedbackReport::factory()->create([
            'status' => 'resolved',
            'resolved_by' => $resolver->id,
            'resolved_at' => now(),
        ]);

        $this->assertInstanceOf(User::class, $report->resolvedBy);
        $this->assertEquals($resolver->id, $report->resolvedBy->id);
    }

    public function test_pending_scope_works(): void
    {
        AiFeedbackReport::factory()->count(3)->create(['status' => 'pending']);
        AiFeedbackReport::factory()->count(2)->resolved()->create();

        $this->assertCount(3, AiFeedbackReport::pending()->get());
    }

    public function test_reviewed_scope_works(): void
    {
        AiFeedbackReport::factory()->count(3)->reviewed()->create();
        AiFeedbackReport::factory()->count(2)->create(['status' => 'pending']);

        $this->assertCount(3, AiFeedbackReport::reviewed()->get());
    }

    public function test_resolved_scope_works(): void
    {
        AiFeedbackReport::factory()->count(2)->create(['status' => 'pending']);
        AiFeedbackReport::factory()->count(4)->resolved()->create();

        $this->assertCount(4, AiFeedbackReport::resolved()->get());
    }

    public function test_unresolved_scope_works(): void
    {
        AiFeedbackReport::factory()->count(2)->create(['status' => 'pending']);
        AiFeedbackReport::factory()->count(1)->reviewed()->create();
        AiFeedbackReport::factory()->count(3)->resolved()->create();

        $this->assertCount(3, AiFeedbackReport::unresolved()->get());
    }

    public function test_of_type_scope_works(): void
    {
        AiFeedbackReport::factory()->count(3)->type(AiFeedbackType::Inaccurate)->create();
        AiFeedbackReport::factory()->count(2)->type(AiFeedbackType::Unhelpful)->create();

        $this->assertCount(3, AiFeedbackReport::ofType(AiFeedbackType::Inaccurate)->get());
    }

    public function test_mark_as_reviewed_helper_works(): void
    {
        $report = AiFeedbackReport::factory()->create([
            'status' => 'pending',
        ]);

        $report->markAsReviewed('Looking into this');

        $this->assertEquals('reviewed', $report->fresh()->status);
        $this->assertEquals('Looking into this', $report->fresh()->admin_notes);
    }

    public function test_resolve_helper_works(): void
    {
        $resolver = User::factory()->create();
        $report = AiFeedbackReport::factory()->create([
            'status' => 'pending',
            'resolved_at' => null,
        ]);

        $report->resolve($resolver, 'Fixed the issue');

        $this->assertEquals('resolved', $report->fresh()->status);
        $this->assertNotNull($report->fresh()->resolved_at);
        $this->assertEquals($resolver->id, $report->fresh()->resolved_by);
        $this->assertEquals('Fixed the issue', $report->fresh()->admin_notes);
    }

    public function test_resolve_helper_preserves_existing_notes(): void
    {
        $resolver = User::factory()->create();
        $report = AiFeedbackReport::factory()->create([
            'status' => 'reviewed',
            'admin_notes' => 'Existing notes',
        ]);

        $report->resolve($resolver);

        $this->assertEquals('resolved', $report->fresh()->status);
        $this->assertEquals('Existing notes', $report->fresh()->admin_notes);
    }

    public function test_is_pending_helper_works(): void
    {
        $pending = AiFeedbackReport::factory()->create(['status' => 'pending']);
        $resolved = AiFeedbackReport::factory()->resolved()->create();

        $this->assertTrue($pending->isPending());
        $this->assertFalse($resolved->isPending());
    }

    public function test_is_reviewed_helper_works(): void
    {
        $reviewed = AiFeedbackReport::factory()->reviewed()->create();
        $pending = AiFeedbackReport::factory()->create(['status' => 'pending']);

        $this->assertTrue($reviewed->isReviewed());
        $this->assertFalse($pending->isReviewed());
    }

    public function test_is_resolved_helper_works(): void
    {
        $resolved = AiFeedbackReport::factory()->resolved()->create();
        $pending = AiFeedbackReport::factory()->create(['status' => 'pending']);

        $this->assertTrue($resolved->isResolved());
        $this->assertFalse($pending->isResolved());
    }

    public function test_reviewed_factory_state_works(): void
    {
        $report = AiFeedbackReport::factory()->reviewed('Review notes')->create();

        $this->assertEquals('reviewed', $report->status);
        $this->assertEquals('Review notes', $report->admin_notes);
    }

    public function test_resolved_factory_state_works(): void
    {
        $report = AiFeedbackReport::factory()->resolved('Test notes')->create();

        $this->assertEquals('resolved', $report->status);
        $this->assertNotNull($report->resolved_at);
        $this->assertNotNull($report->resolved_by);
        $this->assertEquals('Test notes', $report->admin_notes);
    }

    public function test_type_factory_state_works(): void
    {
        $report = AiFeedbackReport::factory()->type(AiFeedbackType::Inappropriate)->create();

        $this->assertEquals(AiFeedbackType::Inappropriate, $report->feedback_type);
    }

    public function test_for_content_factory_state_works(): void
    {
        $content = AiGeneratedContent::factory()->create();
        $report = AiFeedbackReport::factory()->forContent($content)->create();

        $this->assertEquals($content->id, $report->ai_generated_content_id);
    }

    public function test_without_content_factory_state_works(): void
    {
        $report = AiFeedbackReport::factory()->withoutContent()->create();

        $this->assertNull($report->ai_generated_content_id);
    }

    public function test_with_expected_response_factory_state_works(): void
    {
        $report = AiFeedbackReport::factory()->withExpectedResponse('Expected this output')->create();

        $this->assertEquals('Expected this output', $report->expected_response);
    }
}
