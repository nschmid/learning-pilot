# Database Migrations Reference

This file contains all migration schemas for the LearningPilot.
Create migrations with: `php artisan make:migration create_xxx_table`

## Migration Order (Important!)

Run migrations in this order due to foreign key dependencies:

1. users (modify existing)
2. categories
3. tags
4. learning_paths
5. learning_path_tag
6. prerequisites
7. modules
8. module_dependencies
9. learning_steps
10. learning_materials
11. tasks
12. assessments
13. questions
14. answer_options
15. enrollments
16. step_progress
17. task_submissions
18. assessment_attempts
19. question_responses
20. certificates
21. user_notes
22. bookmarks
23. path_reviews

---

## 1. Modify Users Table

```php
// database/migrations/xxxx_add_fields_to_users_table.php
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('role')->default('learner')->after('email');
        $table->string('avatar')->nullable()->after('role');
        $table->boolean('is_active')->default(true)->after('avatar');
        $table->text('bio')->nullable();
        $table->json('preferences')->nullable();
        $table->timestamp('last_login_at')->nullable();
        
        $table->index('role');
        $table->index('is_active');
    });
}
```

## 2. Categories Table

```php
// database/migrations/xxxx_create_categories_table.php
public function up(): void
{
    Schema::create('categories', function (Blueprint $table) {
        $table->id();
        $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
        $table->string('name');
        $table->string('slug')->unique();
        $table->text('description')->nullable();
        $table->string('icon')->nullable();
        $table->integer('position')->default(0);
        $table->boolean('is_active')->default(true);
        $table->timestamps();
        
        $table->index(['parent_id', 'position']);
    });
}
```

## 3. Tags Table

```php
// database/migrations/xxxx_create_tags_table.php
public function up(): void
{
    Schema::create('tags', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('slug')->unique();
        $table->string('color')->nullable();
        $table->timestamps();
    });
}
```

## 4. Learning Paths Table

```php
// database/migrations/xxxx_create_learning_paths_table.php
public function up(): void
{
    Schema::create('learning_paths', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->foreignUuid('creator_id')->constrained('users')->cascadeOnDelete();
        $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
        $table->string('title');
        $table->string('slug')->unique();
        $table->text('description')->nullable();
        $table->text('objectives')->nullable(); // JSON array of learning objectives
        $table->string('difficulty')->default('beginner');
        $table->string('thumbnail')->nullable();
        $table->integer('estimated_hours')->nullable();
        $table->boolean('is_published')->default(false);
        $table->boolean('is_featured')->default(false);
        $table->timestamp('published_at')->nullable();
        $table->integer('version')->default(1);
        $table->json('metadata')->nullable();
        $table->timestamps();
        $table->softDeletes();
        
        $table->index(['is_published', 'is_featured']);
        $table->index('creator_id');
        $table->index('category_id');
        $table->index('difficulty');
    });
}
```

## 5. Learning Path Tag Pivot

```php
// database/migrations/xxxx_create_learning_path_tag_table.php
public function up(): void
{
    Schema::create('learning_path_tag', function (Blueprint $table) {
        $table->foreignUuid('learning_path_id')->constrained()->cascadeOnDelete();
        $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
        $table->primary(['learning_path_id', 'tag_id']);
    });
}
```

## 6. Prerequisites Table

```php
// database/migrations/xxxx_create_prerequisites_table.php
public function up(): void
{
    Schema::create('prerequisites', function (Blueprint $table) {
        $table->foreignUuid('learning_path_id')->constrained()->cascadeOnDelete();
        $table->foreignUuid('required_path_id')->constrained('learning_paths')->cascadeOnDelete();
        $table->primary(['learning_path_id', 'required_path_id']);
    });
}
```

## 7. Modules Table

```php
// database/migrations/xxxx_create_modules_table.php
public function up(): void
{
    Schema::create('modules', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->foreignUuid('learning_path_id')->constrained()->cascadeOnDelete();
        $table->string('title');
        $table->text('description')->nullable();
        $table->integer('position')->default(0);
        $table->string('unlock_condition')->default('sequential'); // sequential, completion_percent, manual
        $table->integer('unlock_value')->nullable(); // For completion_percent
        $table->boolean('is_required')->default(true);
        $table->timestamps();
        $table->softDeletes();
        
        $table->index(['learning_path_id', 'position']);
    });
}
```

## 8. Module Dependencies

```php
// database/migrations/xxxx_create_module_dependencies_table.php
public function up(): void
{
    Schema::create('module_dependencies', function (Blueprint $table) {
        $table->foreignUuid('module_id')->constrained()->cascadeOnDelete();
        $table->foreignUuid('required_module_id')->constrained('modules')->cascadeOnDelete();
        $table->primary(['module_id', 'required_module_id']);
    });
}
```

## 9. Learning Steps Table

```php
// database/migrations/xxxx_create_learning_steps_table.php
public function up(): void
{
    Schema::create('learning_steps', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->foreignUuid('module_id')->constrained()->cascadeOnDelete();
        $table->string('title');
        $table->text('description')->nullable();
        $table->string('step_type'); // material, task, assessment
        $table->integer('position')->default(0);
        $table->integer('points_value')->default(10);
        $table->integer('estimated_minutes')->nullable();
        $table->boolean('is_required')->default(true);
        $table->boolean('is_preview')->default(false); // Can be viewed without enrollment
        $table->timestamps();
        $table->softDeletes();
        
        $table->index(['module_id', 'position']);
        $table->index('step_type');
    });
}
```

## 10. Learning Materials Table

```php
// database/migrations/xxxx_create_learning_materials_table.php
public function up(): void
{
    Schema::create('learning_materials', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->foreignUuid('step_id')->constrained('learning_steps')->cascadeOnDelete();
        $table->string('material_type'); // text, video, audio, pdf, image, link, interactive
        $table->string('title');
        $table->longText('content')->nullable(); // For text content / HTML
        $table->string('file_path')->nullable();
        $table->string('file_name')->nullable();
        $table->string('mime_type')->nullable();
        $table->bigInteger('file_size')->nullable();
        $table->integer('duration_seconds')->nullable(); // For video/audio
        $table->string('external_url')->nullable();
        $table->integer('position')->default(0);
        $table->json('metadata')->nullable();
        $table->timestamps();
        
        $table->index(['step_id', 'position']);
        $table->index('material_type');
    });
}
```

## 11. Tasks Table

```php
// database/migrations/xxxx_create_tasks_table.php
public function up(): void
{
    Schema::create('tasks', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->foreignUuid('step_id')->constrained('learning_steps')->cascadeOnDelete();
        $table->string('task_type'); // submission, project, discussion
        $table->string('title');
        $table->longText('instructions');
        $table->integer('max_points')->default(100);
        $table->integer('due_days')->nullable(); // Days after enrollment
        $table->boolean('allow_late')->default(true);
        $table->boolean('allow_resubmit')->default(true);
        $table->json('rubric')->nullable(); // Grading criteria
        $table->json('allowed_file_types')->nullable();
        $table->integer('max_file_size_mb')->default(10);
        $table->timestamps();
    });
}
```

## 12. Assessments Table

```php
// database/migrations/xxxx_create_assessments_table.php
public function up(): void
{
    Schema::create('assessments', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->foreignUuid('step_id')->constrained('learning_steps')->cascadeOnDelete();
        $table->string('assessment_type'); // quiz, exam, survey
        $table->string('title');
        $table->text('description')->nullable();
        $table->text('instructions')->nullable();
        $table->integer('time_limit_minutes')->nullable();
        $table->integer('passing_score_percent')->default(70);
        $table->integer('max_attempts')->nullable(); // null = unlimited
        $table->boolean('shuffle_questions')->default(false);
        $table->boolean('shuffle_answers')->default(false);
        $table->boolean('show_correct_answers')->default(true);
        $table->boolean('show_score_immediately')->default(true);
        $table->timestamps();
    });
}
```

## 13. Questions Table

```php
// database/migrations/xxxx_create_questions_table.php
public function up(): void
{
    Schema::create('questions', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->foreignUuid('assessment_id')->constrained()->cascadeOnDelete();
        $table->string('question_type'); // single_choice, multiple_choice, true_false, text, matching
        $table->text('question_text');
        $table->string('question_image')->nullable();
        $table->text('explanation')->nullable(); // Shown after answering
        $table->integer('points')->default(1);
        $table->integer('position')->default(0);
        $table->json('metadata')->nullable(); // For matching pairs, etc.
        $table->timestamps();
        
        $table->index(['assessment_id', 'position']);
    });
}
```

## 14. Answer Options Table

```php
// database/migrations/xxxx_create_answer_options_table.php
public function up(): void
{
    Schema::create('answer_options', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->foreignUuid('question_id')->constrained()->cascadeOnDelete();
        $table->text('option_text');
        $table->boolean('is_correct')->default(false);
        $table->integer('position')->default(0);
        $table->text('feedback')->nullable(); // Shown when selected
        $table->timestamps();
        
        $table->index(['question_id', 'position']);
    });
}
```

## 15. Enrollments Table

```php
// database/migrations/xxxx_create_enrollments_table.php
public function up(): void
{
    Schema::create('enrollments', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
        $table->foreignUuid('learning_path_id')->constrained()->cascadeOnDelete();
        $table->string('status')->default('active'); // active, completed, paused, expired
        $table->decimal('progress_percent', 5, 2)->default(0);
        $table->timestamp('started_at')->nullable();
        $table->timestamp('completed_at')->nullable();
        $table->timestamp('last_activity_at')->nullable();
        $table->integer('total_time_spent_seconds')->default(0);
        $table->integer('points_earned')->default(0);
        $table->timestamp('expires_at')->nullable();
        $table->timestamps();
        
        $table->unique(['user_id', 'learning_path_id']);
        $table->index('status');
        $table->index('last_activity_at');
    });
}
```

## 16. Step Progress Table

```php
// database/migrations/xxxx_create_step_progress_table.php
public function up(): void
{
    Schema::create('step_progress', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->foreignUuid('enrollment_id')->constrained()->cascadeOnDelete();
        $table->foreignUuid('step_id')->constrained('learning_steps')->cascadeOnDelete();
        $table->string('status')->default('not_started'); // not_started, in_progress, completed, skipped
        $table->timestamp('started_at')->nullable();
        $table->timestamp('completed_at')->nullable();
        $table->integer('time_spent_seconds')->default(0);
        $table->integer('points_earned')->default(0);
        $table->integer('attempts')->default(0);
        $table->json('data')->nullable(); // Store progress data
        $table->timestamps();
        
        $table->unique(['enrollment_id', 'step_id']);
        $table->index('status');
    });
}
```

## 17. Task Submissions Table

```php
// database/migrations/xxxx_create_task_submissions_table.php
public function up(): void
{
    Schema::create('task_submissions', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->foreignUuid('task_id')->constrained()->cascadeOnDelete();
        $table->foreignUuid('enrollment_id')->constrained()->cascadeOnDelete();
        $table->longText('content')->nullable();
        $table->json('file_paths')->nullable();
        $table->string('status')->default('pending'); // pending, reviewed, revision_requested
        $table->integer('score')->nullable();
        $table->text('feedback')->nullable();
        $table->timestamp('submitted_at');
        $table->timestamp('reviewed_at')->nullable();
        $table->foreignUuid('reviewer_id')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamps();
        
        $table->index(['task_id', 'enrollment_id']);
        $table->index('status');
    });
}
```

## 18. Assessment Attempts Table

```php
// database/migrations/xxxx_create_assessment_attempts_table.php
public function up(): void
{
    Schema::create('assessment_attempts', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->foreignUuid('assessment_id')->constrained()->cascadeOnDelete();
        $table->foreignUuid('enrollment_id')->constrained()->cascadeOnDelete();
        $table->integer('attempt_number')->default(1);
        $table->timestamp('started_at');
        $table->timestamp('completed_at')->nullable();
        $table->decimal('score_percent', 5, 2)->nullable();
        $table->integer('points_earned')->default(0);
        $table->boolean('passed')->default(false);
        $table->integer('time_spent_seconds')->nullable();
        $table->json('answers')->nullable(); // Snapshot of answers
        $table->timestamps();
        
        $table->index(['assessment_id', 'enrollment_id']);
        $table->index('passed');
    });
}
```

## 19. Question Responses Table

```php
// database/migrations/xxxx_create_question_responses_table.php
public function up(): void
{
    Schema::create('question_responses', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->foreignUuid('attempt_id')->constrained('assessment_attempts')->cascadeOnDelete();
        $table->foreignUuid('question_id')->constrained()->cascadeOnDelete();
        $table->text('user_answer')->nullable(); // JSON for multiple choice
        $table->boolean('is_correct')->nullable();
        $table->integer('points_earned')->default(0);
        $table->timestamps();
        
        $table->index(['attempt_id', 'question_id']);
    });
}
```

## 20. Certificates Table

```php
// database/migrations/xxxx_create_certificates_table.php
public function up(): void
{
    Schema::create('certificates', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->foreignUuid('enrollment_id')->constrained()->cascadeOnDelete();
        $table->string('certificate_number')->unique();
        $table->timestamp('issued_at');
        $table->timestamp('expires_at')->nullable();
        $table->string('pdf_path')->nullable();
        $table->json('metadata')->nullable(); // Store completion data snapshot
        $table->timestamps();
        
        $table->index('certificate_number');
    });
}
```

## 21. User Notes Table

```php
// database/migrations/xxxx_create_user_notes_table.php
public function up(): void
{
    Schema::create('user_notes', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
        $table->foreignUuid('step_id')->constrained('learning_steps')->cascadeOnDelete();
        $table->text('content');
        $table->boolean('is_private')->default(true);
        $table->timestamps();
        
        $table->index(['user_id', 'step_id']);
    });
}
```

## 22. Bookmarks Table

```php
// database/migrations/xxxx_create_bookmarks_table.php
public function up(): void
{
    Schema::create('bookmarks', function (Blueprint $table) {
        $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
        $table->foreignUuid('step_id')->constrained('learning_steps')->cascadeOnDelete();
        $table->timestamp('created_at');
        
        $table->primary(['user_id', 'step_id']);
    });
}
```

## 23. Path Reviews Table

```php
// database/migrations/xxxx_create_path_reviews_table.php
public function up(): void
{
    Schema::create('path_reviews', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
        $table->foreignUuid('learning_path_id')->constrained()->cascadeOnDelete();
        $table->tinyInteger('rating'); // 1-5
        $table->text('review_text')->nullable();
        $table->boolean('is_approved')->default(false);
        $table->timestamps();
        
        $table->unique(['user_id', 'learning_path_id']);
        $table->index(['learning_path_id', 'is_approved', 'rating']);
    });
}
```

---

## Create All Migrations Command

```bash
# Run these commands in order
php artisan make:migration add_fields_to_users_table
php artisan make:migration create_categories_table
php artisan make:migration create_tags_table
php artisan make:migration create_learning_paths_table
php artisan make:migration create_learning_path_tag_table
php artisan make:migration create_prerequisites_table
php artisan make:migration create_modules_table
php artisan make:migration create_module_dependencies_table
php artisan make:migration create_learning_steps_table
php artisan make:migration create_learning_materials_table
php artisan make:migration create_tasks_table
php artisan make:migration create_assessments_table
php artisan make:migration create_questions_table
php artisan make:migration create_answer_options_table
php artisan make:migration create_enrollments_table
php artisan make:migration create_step_progress_table
php artisan make:migration create_task_submissions_table
php artisan make:migration create_assessment_attempts_table
php artisan make:migration create_question_responses_table
php artisan make:migration create_certificates_table
php artisan make:migration create_user_notes_table
php artisan make:migration create_bookmarks_table
php artisan make:migration create_path_reviews_table
```
