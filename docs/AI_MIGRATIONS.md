# AI Feature Migrations

## Migration Order

Run these migrations after the core LearningPilot migrations.

```bash
php artisan make:migration create_ai_user_quotas_table
php artisan make:migration create_ai_generated_contents_table
php artisan make:migration create_ai_tutor_conversations_table
php artisan make:migration create_ai_tutor_messages_table
php artisan make:migration create_ai_practice_sessions_table
php artisan make:migration create_ai_practice_questions_table
php artisan make:migration create_ai_usage_logs_table
php artisan make:migration create_ai_feedback_reports_table
```

---

## 1. AI User Quotas

```php
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
            $table->date('daily_reset_at')->default(DB::raw('CURRENT_DATE'));
            $table->date('monthly_reset_at')->default(DB::raw('DATE_FORMAT(CURRENT_DATE, "%Y-%m-01")'));
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_user_quotas');
    }
};
```

---

## 2. AI Generated Contents

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_generated_contents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Polymorphic relationship
            $table->string('contentable_type');
            $table->uuid('contentable_id');
            
            // Content type
            $table->enum('content_type', [
                'explanation',
                'hint',
                'summary',
                'practice_question',
                'feedback',
                'recommendation',
                'flashcard',
                'concept_breakdown',
            ]);
            
            // Generated content
            $table->text('content');
            $table->json('content_metadata')->nullable();
            $table->json('context_snapshot');
            
            // User feedback
            $table->unsignedTinyInteger('rating')->nullable();
            $table->boolean('was_helpful')->nullable();
            $table->text('user_feedback')->nullable();
            
            // Caching
            $table->string('cache_key')->unique()->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->unsignedInteger('version')->default(1);
            
            // Audit
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            
            // Indexes
            $table->index(['contentable_type', 'contentable_id']);
            $table->index('content_type');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_generated_contents');
    }
};
```

---

## 3. AI Tutor Conversations

```php
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
            $table->enum('status', ['active', 'archived', 'resolved'])->default('active');
            
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
```

---

## 4. AI Tutor Messages

```php
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
            $table->enum('role', ['user', 'assistant', 'system']);
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
```

---

## 5. AI Practice Sessions

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_practice_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            
            // Scope
            $table->foreignUuid('learning_path_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('module_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('step_id')->nullable()->constrained('learning_steps')->nullOnDelete();
            
            // Session config
            $table->enum('difficulty', ['beginner', 'intermediate', 'advanced', 'adaptive'])->default('adaptive');
            $table->unsignedSmallInteger('question_count')->default(10);
            $table->json('focus_areas')->nullable();
            
            // Progress
            $table->unsignedSmallInteger('questions_generated')->default(0);
            $table->unsignedSmallInteger('questions_answered')->default(0);
            $table->unsignedSmallInteger('correct_answers')->default(0);
            
            // Status
            $table->enum('status', ['active', 'completed', 'abandoned'])->default('active');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_practice_sessions');
    }
};
```

---

## 6. AI Practice Questions

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_practice_questions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('session_id')->constrained('ai_practice_sessions')->cascadeOnDelete();
            
            // Question content
            $table->enum('question_type', ['single_choice', 'multiple_choice', 'true_false', 'fill_blank', 'short_answer']);
            $table->text('question_text');
            $table->json('options')->nullable();
            $table->text('correct_answer')->nullable();
            $table->text('explanation');
            
            // Metadata
            $table->enum('difficulty', ['beginner', 'intermediate', 'advanced', 'expert']);
            $table->json('topics')->nullable();
            $table->json('source_material_ids')->nullable();
            
            // User response
            $table->text('user_answer')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->timestamp('answered_at')->nullable();
            $table->unsignedInteger('time_spent_seconds')->nullable();
            
            // AI feedback
            $table->text('ai_feedback')->nullable();
            
            // Position
            $table->unsignedSmallInteger('position');
            $table->timestamp('created_at')->useCurrent();
            
            // Indexes
            $table->index('is_correct');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_practice_questions');
    }
};
```

---

## 7. AI Usage Logs

```php
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
            $table->enum('service_type', [
                'explanation',
                'hint',
                'summary',
                'practice_gen',
                'feedback',
                'tutor_chat',
                'recommendation',
            ]);
            $table->string('model', 100);
            
            // Token usage
            $table->unsignedInteger('tokens_input');
            $table->unsignedInteger('tokens_output');
            $table->unsignedInteger('tokens_total')->storedAs('tokens_input + tokens_output');
            
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
```

---

## 8. AI Feedback Reports

```php
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
            $table->enum('feedback_type', [
                'inaccurate',
                'unhelpful',
                'too_complex',
                'too_simple',
                'off_topic',
                'inappropriate',
                'other',
            ]);
            
            // Details
            $table->text('description')->nullable();
            $table->text('expected_response')->nullable();
            
            // Status
            $table->enum('status', ['pending', 'reviewed', 'resolved'])->default('pending');
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
```

---

## Quick Creation Commands

```bash
# Generate all AI migrations at once
php artisan make:migration create_ai_user_quotas_table
php artisan make:migration create_ai_generated_contents_table
php artisan make:migration create_ai_tutor_conversations_table
php artisan make:migration create_ai_tutor_messages_table
php artisan make:migration create_ai_practice_sessions_table
php artisan make:migration create_ai_practice_questions_table
php artisan make:migration create_ai_usage_logs_table
php artisan make:migration create_ai_feedback_reports_table

# Then copy the schema code from above into each migration file

# Run migrations
php artisan migrate
```
