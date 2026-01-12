<?php

namespace Tests\Feature\Models\Engagement;

use App\Models\LearningStep;
use App\Models\User;
use App\Models\UserNote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\ModelTestHelpers;

class UserNoteTest extends TestCase
{
    use ModelTestHelpers;
    use RefreshDatabase;

    public function test_it_can_create_a_user_note(): void
    {
        $note = $this->assertModelCanBeCreated(UserNote::class);

        $this->assertNotNull($note->content);
    }

    public function test_fillable_fields_exist_in_database(): void
    {
        $note = UserNote::factory()->create();

        $this->assertFillableFieldsExist($note);
    }

    public function test_uses_uuids(): void
    {
        $note = UserNote::factory()->create();

        $this->assertUsesUuids($note);
    }

    public function test_boolean_casts_work(): void
    {
        $note = UserNote::factory()->private()->create();

        $this->assertBooleanCast($note, 'is_private');
    }

    public function test_user_relationship_works(): void
    {
        $user = User::factory()->create();
        $note = UserNote::factory()->forUser($user)->create();

        $this->assertBelongsToRelationship($note, 'user', User::class);
    }

    public function test_step_relationship_works(): void
    {
        $step = LearningStep::factory()->create();
        $note = UserNote::factory()->forStep($step)->create();

        $this->assertBelongsToRelationship($note, 'step', LearningStep::class);
    }

    public function test_private_scope_works(): void
    {
        UserNote::factory()->count(3)->private()->create();
        UserNote::factory()->count(2)->public()->create();

        $this->assertCount(3, UserNote::private()->get());
    }

    public function test_public_scope_works(): void
    {
        UserNote::factory()->count(2)->private()->create();
        UserNote::factory()->count(3)->public()->create();

        $this->assertCount(3, UserNote::public()->get());
    }
}
