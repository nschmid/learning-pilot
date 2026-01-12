<?php

namespace Tests\Feature\Models\Engagement;

use App\Models\LearningPath;
use App\Models\PathReview;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\ModelTestHelpers;

class PathReviewTest extends TestCase
{
    use ModelTestHelpers;
    use RefreshDatabase;

    public function test_it_can_create_a_path_review(): void
    {
        $review = $this->assertModelCanBeCreated(PathReview::class);

        $this->assertNotNull($review->rating);
    }

    public function test_fillable_fields_exist_in_database(): void
    {
        $review = PathReview::factory()->create();

        $this->assertFillableFieldsExist($review);
    }

    public function test_uses_uuids(): void
    {
        $review = PathReview::factory()->create();

        $this->assertUsesUuids($review);
    }

    public function test_boolean_casts_work(): void
    {
        $review = PathReview::factory()->approved()->create();

        $this->assertBooleanCast($review, 'is_approved');
    }

    public function test_user_relationship_works(): void
    {
        $user = User::factory()->create();
        $review = PathReview::factory()->forUser($user)->create();

        $this->assertBelongsToRelationship($review, 'user', User::class);
    }

    public function test_learning_path_relationship_works(): void
    {
        $path = LearningPath::factory()->create();
        $review = PathReview::factory()->forPath($path)->create();

        $this->assertBelongsToRelationship($review, 'learningPath', LearningPath::class);
    }

    public function test_approved_scope_works(): void
    {
        PathReview::factory()->count(3)->approved()->create();
        PathReview::factory()->count(2)->create(['is_approved' => false]);

        $this->assertCount(3, PathReview::approved()->get());
    }

    public function test_pending_scope_works(): void
    {
        PathReview::factory()->count(2)->approved()->create();
        PathReview::factory()->count(3)->create(['is_approved' => false]);

        $this->assertCount(3, PathReview::pending()->get());
    }

    public function test_by_rating_scope_works(): void
    {
        PathReview::factory()->count(2)->rating(5)->create();
        PathReview::factory()->count(3)->rating(4)->create();
        PathReview::factory()->count(1)->rating(3)->create();

        $this->assertCount(2, PathReview::byRating(5)->get());
        $this->assertCount(3, PathReview::byRating(4)->get());
    }

    public function test_rating_factory_state_works(): void
    {
        $fiveStar = PathReview::factory()->rating(5)->create();
        $oneStar = PathReview::factory()->rating(1)->create();

        $this->assertEquals(5, $fiveStar->rating);
        $this->assertEquals(1, $oneStar->rating);
    }
}
