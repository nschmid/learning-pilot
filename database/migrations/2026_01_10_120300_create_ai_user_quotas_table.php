<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_user_quotas', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->unique()->constrained()->cascadeOnDelete();

            // Monthly limits
            $table->unsignedInteger('monthly_token_limit')->default(100000);
            $table->unsignedInteger('monthly_tokens_used')->default(0);

            // Daily limits
            $table->unsignedInteger('daily_request_limit')->default(100);
            $table->unsignedInteger('daily_requests_used')->default(0);

            // Feature access
            $table->boolean('tutor_enabled')->default(true);
            $table->boolean('practice_gen_enabled')->default(true);
            $table->boolean('advanced_explanations')->default(false);

            // Reset tracking
            $table->date('daily_reset_at')->nullable();
            $table->date('monthly_reset_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_user_quotas');
    }
};
