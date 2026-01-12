<?php

namespace Tests\Feature\Models\AI;

use App\Models\AiUserQuota;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\ModelTestHelpers;

class AiUserQuotaTest extends TestCase
{
    use ModelTestHelpers;
    use RefreshDatabase;

    public function test_it_can_create_an_ai_user_quota(): void
    {
        $quota = $this->assertModelCanBeCreated(AiUserQuota::class);

        $this->assertNotNull($quota->monthly_token_limit);
    }

    public function test_fillable_fields_exist_in_database(): void
    {
        $quota = AiUserQuota::factory()->create();

        $this->assertFillableFieldsExist($quota);
    }

    public function test_uses_uuids(): void
    {
        $quota = AiUserQuota::factory()->create();

        $this->assertUsesUuids($quota);
    }

    public function test_datetime_casts_work(): void
    {
        $quota = AiUserQuota::factory()->create([
            'last_request_at' => now(),
            'month_reset_at' => now(),
        ]);

        $this->assertDatetimeCast($quota, 'last_request_at');
        $this->assertDatetimeCast($quota, 'month_reset_at');
    }

    public function test_boolean_casts_work(): void
    {
        $quota = AiUserQuota::factory()->create([
            'feature_explanations_enabled' => true,
            'feature_tutor_enabled' => false,
        ]);

        $this->assertBooleanCast($quota, 'feature_explanations_enabled');
        $this->assertBooleanCast($quota, 'feature_tutor_enabled');
        $this->assertBooleanCast($quota, 'feature_practice_enabled');
        $this->assertBooleanCast($quota, 'feature_summaries_enabled');
    }

    public function test_user_relationship_works(): void
    {
        $user = User::factory()->create();
        $quota = AiUserQuota::factory()->forUser($user)->create();

        $this->assertBelongsToRelationship($quota, 'user', User::class);
    }

    public function test_has_tokens_remaining_helper_works(): void
    {
        $hasTokens = AiUserQuota::factory()->create([
            'monthly_token_limit' => 100000,
            'tokens_used_this_month' => 50000,
        ]);
        $noTokens = AiUserQuota::factory()->exhaustedTokens()->create();

        $this->assertTrue($hasTokens->hasTokensRemaining());
        $this->assertFalse($noTokens->hasTokensRemaining());
    }

    public function test_has_requests_remaining_helper_works(): void
    {
        $hasRequests = AiUserQuota::factory()->create([
            'daily_request_limit' => 100,
            'requests_today' => 50,
        ]);
        $noRequests = AiUserQuota::factory()->exhaustedRequests()->create();

        $this->assertTrue($hasRequests->hasRequestsRemaining());
        $this->assertFalse($noRequests->hasRequestsRemaining());
    }

    public function test_can_make_request_helper_works(): void
    {
        $canRequest = AiUserQuota::factory()->create();
        $cannotRequest = AiUserQuota::factory()->exhaustedTokens()->create();

        $this->assertTrue($canRequest->canMakeRequest());
        $this->assertFalse($cannotRequest->canMakeRequest());
    }

    public function test_remaining_tokens_helper_works(): void
    {
        $quota = AiUserQuota::factory()->create([
            'monthly_token_limit' => 100000,
            'tokens_used_this_month' => 25000,
        ]);

        $this->assertEquals(75000, $quota->remainingTokens());
    }

    public function test_token_usage_percent_helper_works(): void
    {
        $quota = AiUserQuota::factory()->create([
            'monthly_token_limit' => 100000,
            'tokens_used_this_month' => 25000,
        ]);

        $this->assertEquals(25.0, $quota->tokenUsagePercent());
    }

    public function test_increment_usage_updates_counters(): void
    {
        $quota = AiUserQuota::factory()->create([
            'tokens_used_this_month' => 0,
            'requests_today' => 0,
        ]);

        $quota->incrementUsage(500);

        $this->assertEquals(500, $quota->fresh()->tokens_used_this_month);
        $this->assertEquals(1, $quota->fresh()->requests_today);
        $this->assertNotNull($quota->fresh()->last_request_at);
    }
}
