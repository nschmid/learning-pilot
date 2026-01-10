<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_feedback_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('ai_generated_content_id')->nullable()->constrained()->nullOnDelete();

            // Feedback type
            $table->string('feedback_type'); // inaccurate, unhelpful, too_complex, too_simple, off_topic, inappropriate, other

            // Details
            $table->text('description')->nullable();
            $table->text('expected_response')->nullable();

            // Status
            $table->string('status')->default('pending'); // pending, reviewed, resolved
            $table->text('admin_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->foreignUuid('resolved_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index('feedback_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_feedback_reports');
    }
};
