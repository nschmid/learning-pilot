<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_tutor_conversations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();

            // Optional context binding
            $table->foreignUuid('learning_path_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('module_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('step_id')->nullable()->constrained('learning_steps')->nullOnDelete();

            // Metadata
            $table->string('title')->nullable();
            $table->string('status')->default('active'); // active, archived, resolved

            // AI context
            $table->json('system_context')->nullable();
            $table->unsignedInteger('total_messages')->default(0);
            $table->unsignedInteger('total_tokens_used')->default(0);

            // Timestamps
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index('last_message_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_tutor_conversations');
    }
};
