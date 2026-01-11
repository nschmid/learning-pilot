<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_user_quotas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->unique()->constrained()->cascadeOnDelete();

            // Monthly limits
            $table->unsignedInteger('monthly_token_limit')->default(100000);
            $table->unsignedInteger('tokens_used_this_month')->default(0);

            // Daily limits
            $table->unsignedInteger('daily_request_limit')->default(100);
            $table->unsignedInteger('requests_today')->default(0);

            // Feature access
            $table->boolean('feature_explanations_enabled')->default(true);
            $table->boolean('feature_tutor_enabled')->default(true);
            $table->boolean('feature_practice_enabled')->default(true);
            $table->boolean('feature_summaries_enabled')->default(true);

            // Reset tracking
            $table->timestamp('last_request_at')->nullable();
            $table->timestamp('month_reset_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_user_quotas');
    }
};
