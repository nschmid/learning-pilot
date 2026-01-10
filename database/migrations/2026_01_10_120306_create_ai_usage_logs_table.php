<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();

            // Request details
            $table->string('service_type'); // explanation, hint, summary, practice_gen, feedback, tutor_chat, recommendation
            $table->string('model', 100);

            // Token usage
            $table->unsignedInteger('tokens_input');
            $table->unsignedInteger('tokens_output');
            $table->unsignedInteger('tokens_total');

            // Cost tracking
            $table->decimal('cost_credits', 10, 4)->nullable();

            // Performance
            $table->unsignedInteger('latency_ms')->nullable();
            $table->boolean('cache_hit')->default(false);

            // Context
            $table->string('context_type', 100)->nullable();
            $table->uuid('context_id')->nullable();

            // Timestamp
            $table->timestamp('created_at')->useCurrent();

            // Indexes
            $table->index('service_type');
            $table->index('created_at');
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_usage_logs');
    }
};
