<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_tutor_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('conversation_id')->constrained('ai_tutor_conversations')->cascadeOnDelete();

            // Message content
            $table->string('role'); // user, assistant, system
            $table->text('content');

            // AI metadata (for assistant messages)
            $table->string('model', 100)->nullable();
            $table->unsignedInteger('tokens_input')->nullable();
            $table->unsignedInteger('tokens_output')->nullable();
            $table->unsignedInteger('latency_ms')->nullable();

            // References
            $table->json('references')->nullable();

            // Timestamp
            $table->timestamp('created_at')->useCurrent();

            // Indexes
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_tutor_messages');
    }
};
