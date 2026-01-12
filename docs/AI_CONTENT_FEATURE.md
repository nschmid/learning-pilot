# 🤖 AI Content Feature Specification

## LearningPilot - AI-Powered Adaptive Learning

**Version:** 1.0  
**Status:** Feature Design  
**Integration:** Extension to existing LearningPilot specification

---

## 1. Gap Analysis

### Current Specification Status

The existing LearningPilot specification **does NOT include** any AI-generated content features. The current architecture is based on:

| Aspect | Current State | Gap |
|--------|---------------|-----|
| Content | Static (instructor-created) | No dynamic generation |
| Assessments | Fixed question pools | No adaptive questions |
| Feedback | Manual instructor feedback | No instant AI explanations |
| Support | None | No AI tutoring |
| Personalization | Basic progress tracking | No learning path adaptation |
| Summaries | None | No AI-generated study guides |

### Why AI Content is Essential

1. **Scalability**: Instructors can't create personalized content for thousands of learners
2. **Immediate Feedback**: Learners need instant explanations, not delayed responses
3. **Adaptive Learning**: Each learner has different knowledge gaps
4. **Engagement**: Personalized content increases completion rates by 30-50%
5. **Cost Efficiency**: AI-generated practice materials reduce instructor workload

---

## 2. Feature Overview

### AI Content Capabilities

```
┌─────────────────────────────────────────────────────────────────────────┐
│                        AI CONTENT FEATURES                               │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  ┌──────────────────┐  ┌──────────────────┐  ┌──────────────────────┐  │
│  │ AI Explanations  │  │ Practice Gen     │  │ Adaptive Hints       │  │
│  │ ──────────────── │  │ ──────────────── │  │ ──────────────────── │  │
│  │ • Wrong answer   │  │ • Similar Qs     │  │ • Progressive hints  │  │
│  │   explanations   │  │ • Practice sets  │  │ • Task assistance    │  │
│  │ • Concept        │  │ • Difficulty     │  │ • Stuck detection    │  │
│  │   breakdowns     │  │   adjustment     │  │                      │  │
│  └──────────────────┘  └──────────────────┘  └──────────────────────┘  │
│                                                                          │
│  ┌──────────────────┐  ┌──────────────────┐  ┌──────────────────────┐  │
│  │ AI Tutor Chat    │  │ Content Summary  │  │ Learning Analytics   │  │
│  │ ──────────────── │  │ ──────────────── │  │ ──────────────────── │  │
│  │ • Q&A about      │  │ • Module recaps  │  │ • Weakness detection │  │
│  │   materials      │  │ • Study guides   │  │ • Recommendations    │  │
│  │ • Clarifications │  │ • Key concepts   │  │ • Path suggestions   │  │
│  │ • Socratic mode  │  │ • Flashcards     │  │                      │  │
│  └──────────────────┘  └──────────────────┘  └──────────────────────┘  │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘
```

### Integration Points with Existing System

```
Existing System                    AI Layer
─────────────────────────────────────────────────────────────────
StepProgress      ───────────►   AIContextBuilder
    │                                  │
    ▼                                  ▼
QuestionResponse  ───────────►   AIExplanationService
    │                                  │
    ▼                                  ▼
TaskSubmission    ───────────►   AIFeedbackService
    │                                  │
    ▼                                  ▼
LearningMaterial  ───────────►   AISummaryService
    │                                  │
    ▼                                  ▼
Enrollment        ───────────►   AIRecommendationService
```

---

## 3. Database Schema Extensions

### New Tables

```sql
-- ============================================
-- AI Content Generation & Caching
-- ============================================

CREATE TABLE ai_generated_contents (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    
    -- Polymorphic relationship to source
    contentable_type VARCHAR(255) NOT NULL,  -- 'question_response', 'step_progress', etc.
    contentable_id UUID NOT NULL,
    
    -- AI content metadata
    content_type ENUM(
        'explanation',          -- Wrong answer explanations
        'hint',                 -- Progressive hints
        'summary',              -- Module/step summaries
        'practice_question',    -- Generated practice questions
        'feedback',             -- Task feedback
        'recommendation',       -- Learning path recommendations
        'flashcard',            -- Generated flashcards
        'concept_breakdown'     -- Concept explanations
    ) NOT NULL,
    
    -- The generated content
    content TEXT NOT NULL,
    content_metadata JSON,  -- {model, tokens_used, generation_params}
    
    -- Context used for generation
    context_snapshot JSON NOT NULL,  -- Snapshot of user progress/context
    
    -- Quality & feedback
    rating TINYINT UNSIGNED,  -- 1-5 user rating
    was_helpful BOOLEAN,
    user_feedback TEXT,
    
    -- Caching & versioning
    cache_key VARCHAR(255) UNIQUE,
    expires_at TIMESTAMP,
    version INT DEFAULT 1,
    
    -- Audit
    user_id UUID NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP,
    
    INDEX idx_contentable (contentable_type, contentable_id),
    INDEX idx_content_type (content_type),
    INDEX idx_cache_key (cache_key),
    INDEX idx_user_id (user_id),
    INDEX idx_expires_at (expires_at),
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================
-- AI Tutor Conversations
-- ============================================

CREATE TABLE ai_tutor_conversations (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID NOT NULL,
    
    -- Context binding (optional - can be global or scoped)
    learning_path_id UUID,
    module_id UUID,
    step_id UUID,
    
    -- Conversation metadata
    title VARCHAR(255),  -- Auto-generated or user-defined
    status ENUM('active', 'archived', 'resolved') DEFAULT 'active',
    
    -- AI context
    system_context JSON,  -- Cached context for conversation
    total_messages INT DEFAULT 0,
    total_tokens_used INT DEFAULT 0,
    
    -- Timestamps
    last_message_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP,
    
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_learning_path (learning_path_id),
    INDEX idx_last_message (last_message_at),
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (learning_path_id) REFERENCES learning_paths(id) ON DELETE SET NULL,
    FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE SET NULL,
    FOREIGN KEY (step_id) REFERENCES learning_steps(id) ON DELETE SET NULL
);

CREATE TABLE ai_tutor_messages (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    conversation_id UUID NOT NULL,
    
    -- Message content
    role ENUM('user', 'assistant', 'system') NOT NULL,
    content TEXT NOT NULL,
    
    -- AI metadata (for assistant messages)
    model VARCHAR(100),
    tokens_input INT,
    tokens_output INT,
    latency_ms INT,
    
    -- Optional attachments/references
    references JSON,  -- [{type: 'material', id: '...', title: '...'}]
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_conversation (conversation_id),
    INDEX idx_created_at (created_at),
    
    FOREIGN KEY (conversation_id) REFERENCES ai_tutor_conversations(id) ON DELETE CASCADE
);

-- ============================================
-- Practice Question Generation
-- ============================================

CREATE TABLE ai_practice_sessions (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID NOT NULL,
    
    -- Scope
    learning_path_id UUID,
    module_id UUID,
    step_id UUID,
    
    -- Session config
    difficulty ENUM('beginner', 'intermediate', 'advanced', 'adaptive') DEFAULT 'adaptive',
    question_count INT DEFAULT 10,
    focus_areas JSON,  -- Topics/concepts to focus on
    
    -- Progress
    questions_generated INT DEFAULT 0,
    questions_answered INT DEFAULT 0,
    correct_answers INT DEFAULT 0,
    
    -- Status
    status ENUM('active', 'completed', 'abandoned') DEFAULT 'active',
    started_at TIMESTAMP,
    completed_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (learning_path_id) REFERENCES learning_paths(id) ON DELETE SET NULL,
    FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE SET NULL,
    FOREIGN KEY (step_id) REFERENCES learning_steps(id) ON DELETE SET NULL
);

CREATE TABLE ai_practice_questions (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    session_id UUID NOT NULL,
    
    -- Generated question
    question_type ENUM('single_choice', 'multiple_choice', 'true_false', 'fill_blank', 'short_answer') NOT NULL,
    question_text TEXT NOT NULL,
    options JSON,  -- For choice questions: [{text, is_correct, explanation}]
    correct_answer TEXT,  -- For non-choice questions
    explanation TEXT NOT NULL,
    
    -- Difficulty & topics
    difficulty ENUM('beginner', 'intermediate', 'advanced', 'expert') NOT NULL,
    topics JSON,  -- ['topic1', 'topic2']
    source_material_ids JSON,  -- IDs of materials used for generation
    
    -- User response
    user_answer TEXT,
    is_correct BOOLEAN,
    answered_at TIMESTAMP,
    time_spent_seconds INT,
    
    -- AI feedback on user's answer
    ai_feedback TEXT,
    
    -- Position
    position INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_session (session_id),
    INDEX idx_is_correct (is_correct),
    
    FOREIGN KEY (session_id) REFERENCES ai_practice_sessions(id) ON DELETE CASCADE
);

-- ============================================
-- AI Usage Tracking & Limits
-- ============================================

CREATE TABLE ai_usage_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id UUID NOT NULL,
    
    -- Request details
    service_type ENUM(
        'explanation', 'hint', 'summary', 'practice_gen', 
        'feedback', 'tutor_chat', 'recommendation'
    ) NOT NULL,
    model VARCHAR(100) NOT NULL,
    
    -- Token usage
    tokens_input INT NOT NULL,
    tokens_output INT NOT NULL,
    tokens_total INT GENERATED ALWAYS AS (tokens_input + tokens_output) STORED,
    
    -- Cost tracking (in credits/cents)
    cost_credits DECIMAL(10, 4),
    
    -- Performance
    latency_ms INT,
    cache_hit BOOLEAN DEFAULT FALSE,
    
    -- Context
    context_type VARCHAR(100),  -- 'question_response', 'step', 'module', etc.
    context_id UUID,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_user_id (user_id),
    INDEX idx_service_type (service_type),
    INDEX idx_created_at (created_at),
    INDEX idx_user_date (user_id, created_at),
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE ai_user_quotas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id UUID NOT NULL UNIQUE,
    
    -- Monthly limits
    monthly_token_limit INT DEFAULT 100000,
    monthly_tokens_used INT DEFAULT 0,
    
    -- Daily limits (for rate limiting)
    daily_request_limit INT DEFAULT 100,
    daily_requests_used INT DEFAULT 0,
    
    -- Feature access
    tutor_enabled BOOLEAN DEFAULT TRUE,
    practice_gen_enabled BOOLEAN DEFAULT TRUE,
    advanced_explanations BOOLEAN DEFAULT FALSE,  -- Premium feature
    
    -- Reset tracking
    daily_reset_at DATE DEFAULT CURRENT_DATE,
    monthly_reset_at DATE DEFAULT (DATE_FORMAT(CURRENT_DATE, '%Y-%m-01')),
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================
-- AI Content Feedback & Learning
-- ============================================

CREATE TABLE ai_feedback_reports (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID NOT NULL,
    ai_generated_content_id UUID,
    
    -- Feedback type
    feedback_type ENUM(
        'inaccurate',      -- Factually wrong
        'unhelpful',       -- Didn't help understanding
        'too_complex',     -- Too difficult to understand
        'too_simple',      -- Too basic
        'off_topic',       -- Didn't address the question
        'inappropriate',   -- Content policy violation
        'other'
    ) NOT NULL,
    
    -- Details
    description TEXT,
    expected_response TEXT,
    
    -- Status
    status ENUM('pending', 'reviewed', 'resolved') DEFAULT 'pending',
    admin_notes TEXT,
    resolved_at TIMESTAMP,
    resolved_by UUID,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_status (status),
    INDEX idx_feedback_type (feedback_type),
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (ai_generated_content_id) REFERENCES ai_generated_contents(id) ON DELETE SET NULL,
    FOREIGN KEY (resolved_by) REFERENCES users(id) ON DELETE SET NULL
);
```

### Entity Relationship Updates

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                            AI CONTENT ERD EXTENSION                              │
├─────────────────────────────────────────────────────────────────────────────────┤
│                                                                                  │
│   ┌─────────────┐         ┌──────────────────────┐         ┌─────────────────┐ │
│   │    User     │────────►│  AITutorConversation │◄────────│  LearningPath   │ │
│   │             │         │  • title             │         │                 │ │
│   │             │         │  • status            │         │                 │ │
│   │             │         │  • system_context    │         │                 │ │
│   └──────┬──────┘         └──────────┬───────────┘         └─────────────────┘ │
│          │                           │                                          │
│          │    ┌──────────────────────┘                                          │
│          │    │                                                                  │
│          ▼    ▼                                                                  │
│   ┌──────────────────┐    ┌──────────────────────┐    ┌─────────────────────┐  │
│   │ AIUserQuota      │    │  AITutorMessage      │    │ AIPracticeSession   │  │
│   │ • monthly_limit  │    │  • role              │    │ • difficulty        │  │
│   │ • tokens_used    │    │  • content           │    │ • question_count    │  │
│   │ • daily_limit    │    │  • tokens_used       │    │ • status            │  │
│   └──────────────────┘    └──────────────────────┘    └──────────┬──────────┘  │
│                                                                   │             │
│                                                                   ▼             │
│   ┌──────────────────┐    ┌──────────────────────┐    ┌─────────────────────┐  │
│   │ QuestionResponse │───►│ AIGeneratedContent   │    │ AIPracticeQuestion  │  │
│   │                  │    │ (polymorphic)        │    │ • question_text     │  │
│   │                  │    │ • content_type       │    │ • options           │  │
│   │                  │    │ • content            │    │ • explanation       │  │
│   │                  │    │ • context_snapshot   │    │ • user_answer       │  │
│   └──────────────────┘    └──────────────────────┘    └─────────────────────┘  │
│                                      ▲                                          │
│                                      │                                          │
│   ┌──────────────────┐    ┌──────────┴───────────┐    ┌─────────────────────┐  │
│   │ StepProgress     │───►│ AIFeedbackReport     │    │ AIUsageLog          │  │
│   │                  │    │ • feedback_type      │    │ • service_type      │  │
│   │                  │    │ • description        │    │ • tokens_used       │  │
│   │                  │    │ • status             │    │ • cost_credits      │  │
│   └──────────────────┘    └──────────────────────┘    └─────────────────────┘  │
│                                                                                  │
└─────────────────────────────────────────────────────────────────────────────────┘
```

---

## 4. New Enums

### app/Enums/AIContentType.php

```php
<?php

namespace App\Enums;

enum AIContentType: string
{
    case Explanation = 'explanation';
    case Hint = 'hint';
    case Summary = 'summary';
    case PracticeQuestion = 'practice_question';
    case Feedback = 'feedback';
    case Recommendation = 'recommendation';
    case Flashcard = 'flashcard';
    case ConceptBreakdown = 'concept_breakdown';

    public function label(): string
    {
        return match ($this) {
            self::Explanation => 'Erklärung',
            self::Hint => 'Hinweis',
            self::Summary => 'Zusammenfassung',
            self::PracticeQuestion => 'Übungsfrage',
            self::Feedback => 'Feedback',
            self::Recommendation => 'Empfehlung',
            self::Flashcard => 'Lernkarte',
            self::ConceptBreakdown => 'Konzepterklärung',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Explanation => 'light-bulb',
            self::Hint => 'question-mark-circle',
            self::Summary => 'document-text',
            self::PracticeQuestion => 'academic-cap',
            self::Feedback => 'chat-bubble-left-right',
            self::Recommendation => 'sparkles',
            self::Flashcard => 'rectangle-stack',
            self::ConceptBreakdown => 'puzzle-piece',
        };
    }

    public function cacheDuration(): int // in minutes
    {
        return match ($this) {
            self::Explanation => 60 * 24 * 7, // 7 days
            self::Hint => 60 * 24,            // 1 day
            self::Summary => 60 * 24 * 30,    // 30 days
            self::PracticeQuestion => 0,       // No cache
            self::Feedback => 60 * 24,        // 1 day
            self::Recommendation => 60,        // 1 hour
            self::Flashcard => 60 * 24 * 7,   // 7 days
            self::ConceptBreakdown => 60 * 24 * 7, // 7 days
        };
    }
}
```

### app/Enums/AIServiceType.php

```php
<?php

namespace App\Enums;

enum AIServiceType: string
{
    case Explanation = 'explanation';
    case Hint = 'hint';
    case Summary = 'summary';
    case PracticeGeneration = 'practice_gen';
    case Feedback = 'feedback';
    case TutorChat = 'tutor_chat';
    case Recommendation = 'recommendation';

    public function label(): string
    {
        return match ($this) {
            self::Explanation => 'KI-Erklärung',
            self::Hint => 'KI-Hinweis',
            self::Summary => 'KI-Zusammenfassung',
            self::PracticeGeneration => 'Übungsgenerierung',
            self::Feedback => 'KI-Feedback',
            self::TutorChat => 'KI-Tutor',
            self::Recommendation => 'KI-Empfehlung',
        };
    }

    public function defaultModel(): string
    {
        return match ($this) {
            self::TutorChat => 'claude-sonnet-4-5-20250929',
            self::PracticeGeneration => 'claude-sonnet-4-5-20250929',
            default => 'claude-haiku-4-5-20251001',
        };
    }

    public function maxTokens(): int
    {
        return match ($this) {
            self::Explanation => 1000,
            self::Hint => 300,
            self::Summary => 2000,
            self::PracticeGeneration => 3000,
            self::Feedback => 800,
            self::TutorChat => 1500,
            self::Recommendation => 500,
        };
    }

    public function rateLimit(): array // [requests, period_in_minutes]
    {
        return match ($this) {
            self::TutorChat => [30, 60],
            self::PracticeGeneration => [10, 60],
            default => [50, 60],
        };
    }
}
```

### app/Enums/AIPracticeDifficulty.php

```php
<?php

namespace App\Enums;

enum AIPracticeDifficulty: string
{
    case Beginner = 'beginner';
    case Intermediate = 'intermediate';
    case Advanced = 'advanced';
    case Adaptive = 'adaptive';

    public function label(): string
    {
        return match ($this) {
            self::Beginner => 'Anfänger',
            self::Intermediate => 'Fortgeschritten',
            self::Advanced => 'Experte',
            self::Adaptive => 'Adaptiv',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Beginner => 'Grundlegende Konzepte und einfache Anwendungen',
            self::Intermediate => 'Vertiefte Konzepte und komplexere Szenarien',
            self::Advanced => 'Anspruchsvolle Problemstellungen und Transferaufgaben',
            self::Adaptive => 'Passt sich automatisch an dein Niveau an',
        };
    }
}
```

### app/Enums/AIFeedbackType.php

```php
<?php

namespace App\Enums;

enum AIFeedbackType: string
{
    case Inaccurate = 'inaccurate';
    case Unhelpful = 'unhelpful';
    case TooComplex = 'too_complex';
    case TooSimple = 'too_simple';
    case OffTopic = 'off_topic';
    case Inappropriate = 'inappropriate';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Inaccurate => 'Inhaltlich falsch',
            self::Unhelpful => 'Nicht hilfreich',
            self::TooComplex => 'Zu kompliziert',
            self::TooSimple => 'Zu einfach',
            self::OffTopic => 'Thema verfehlt',
            self::Inappropriate => 'Unangemessen',
            self::Other => 'Sonstiges',
        };
    }
}
```

---

## 5. New Models

### app/Models/AIGeneratedContent.php

```php
<?php

namespace App\Models;

use App\Enums\AIContentType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AIGeneratedContent extends Model
{
    use HasUuids;

    protected $fillable = [
        'contentable_type',
        'contentable_id',
        'content_type',
        'content',
        'content_metadata',
        'context_snapshot',
        'rating',
        'was_helpful',
        'user_feedback',
        'cache_key',
        'expires_at',
        'version',
        'user_id',
    ];

    protected $casts = [
        'content_type' => AIContentType::class,
        'content_metadata' => 'array',
        'context_snapshot' => 'array',
        'was_helpful' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function contentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function scopeValid($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    public function scopeByType($query, AIContentType $type)
    {
        return $query->where('content_type', $type);
    }
}
```

### app/Models/AITutorConversation.php

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AITutorConversation extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'learning_path_id',
        'module_id',
        'step_id',
        'title',
        'status',
        'system_context',
        'total_messages',
        'total_tokens_used',
        'last_message_at',
    ];

    protected $casts = [
        'system_context' => 'array',
        'last_message_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function learningPath(): BelongsTo
    {
        return $this->belongsTo(LearningPath::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(LearningStep::class, 'step_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(AITutorMessage::class, 'conversation_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getContextScope(): string
    {
        if ($this->step_id) return 'step';
        if ($this->module_id) return 'module';
        if ($this->learning_path_id) return 'path';
        return 'global';
    }
}
```

### app/Models/AITutorMessage.php

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AITutorMessage extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'conversation_id',
        'role',
        'content',
        'model',
        'tokens_input',
        'tokens_output',
        'latency_ms',
        'references',
        'created_at',
    ];

    protected $casts = [
        'references' => 'array',
        'created_at' => 'datetime',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(AITutorConversation::class, 'conversation_id');
    }

    public function isAssistant(): bool
    {
        return $this->role === 'assistant';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    public function getTotalTokens(): int
    {
        return ($this->tokens_input ?? 0) + ($this->tokens_output ?? 0);
    }
}
```

### app/Models/AIPracticeSession.php

```php
<?php

namespace App\Models;

use App\Enums\AIPracticeDifficulty;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AIPracticeSession extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'learning_path_id',
        'module_id',
        'step_id',
        'difficulty',
        'question_count',
        'focus_areas',
        'questions_generated',
        'questions_answered',
        'correct_answers',
        'status',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'difficulty' => AIPracticeDifficulty::class,
        'focus_areas' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function learningPath(): BelongsTo
    {
        return $this->belongsTo(LearningPath::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(LearningStep::class, 'step_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(AIPracticeQuestion::class, 'session_id')
                    ->orderBy('position');
    }

    public function getAccuracyPercent(): float
    {
        if ($this->questions_answered === 0) return 0;
        return round(($this->correct_answers / $this->questions_answered) * 100, 1);
    }

    public function isComplete(): bool
    {
        return $this->status === 'completed';
    }
}
```

### app/Models/AIPracticeQuestion.php

```php
<?php

namespace App\Models;

use App\Enums\Difficulty;
use App\Enums\QuestionType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIPracticeQuestion extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'session_id',
        'question_type',
        'question_text',
        'options',
        'correct_answer',
        'explanation',
        'difficulty',
        'topics',
        'source_material_ids',
        'user_answer',
        'is_correct',
        'answered_at',
        'time_spent_seconds',
        'ai_feedback',
        'position',
        'created_at',
    ];

    protected $casts = [
        'question_type' => QuestionType::class,
        'difficulty' => Difficulty::class,
        'options' => 'array',
        'topics' => 'array',
        'source_material_ids' => 'array',
        'is_correct' => 'boolean',
        'answered_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(AIPracticeSession::class, 'session_id');
    }

    public function isAnswered(): bool
    {
        return $this->answered_at !== null;
    }
}
```

### app/Models/AIUserQuota.php

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIUserQuota extends Model
{
    protected $fillable = [
        'user_id',
        'monthly_token_limit',
        'monthly_tokens_used',
        'daily_request_limit',
        'daily_requests_used',
        'tutor_enabled',
        'practice_gen_enabled',
        'advanced_explanations',
        'daily_reset_at',
        'monthly_reset_at',
    ];

    protected $casts = [
        'tutor_enabled' => 'boolean',
        'practice_gen_enabled' => 'boolean',
        'advanced_explanations' => 'boolean',
        'daily_reset_at' => 'date',
        'monthly_reset_at' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function checkAndResetDaily(): void
    {
        if ($this->daily_reset_at->lt(today())) {
            $this->update([
                'daily_requests_used' => 0,
                'daily_reset_at' => today(),
            ]);
        }
    }

    public function checkAndResetMonthly(): void
    {
        $monthStart = today()->startOfMonth();
        if ($this->monthly_reset_at->lt($monthStart)) {
            $this->update([
                'monthly_tokens_used' => 0,
                'monthly_reset_at' => $monthStart,
            ]);
        }
    }

    public function canMakeRequest(): bool
    {
        $this->checkAndResetDaily();
        return $this->daily_requests_used < $this->daily_request_limit;
    }

    public function hasTokensAvailable(int $estimatedTokens = 1000): bool
    {
        $this->checkAndResetMonthly();
        return ($this->monthly_tokens_used + $estimatedTokens) <= $this->monthly_token_limit;
    }

    public function incrementUsage(int $tokens): void
    {
        $this->increment('daily_requests_used');
        $this->increment('monthly_tokens_used', $tokens);
    }

    public function getRemainingTokens(): int
    {
        return max(0, $this->monthly_token_limit - $this->monthly_tokens_used);
    }

    public function getUsagePercent(): float
    {
        return round(($this->monthly_tokens_used / $this->monthly_token_limit) * 100, 1);
    }
}
```

---

## 6. Service Layer

### app/Services/AI/AIClientService.php

```php
<?php

namespace App\Services\AI;

use App\Enums\AIServiceType;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIClientService
{
    protected string $baseUrl = 'https://api.anthropic.com/v1';
    
    public function __construct(
        protected string $apiKey,
    ) {}

    public function createMessage(
        AIServiceType $serviceType,
        string $systemPrompt,
        array $messages,
        ?string $model = null,
        ?int $maxTokens = null,
    ): array {
        $model = $model ?? $serviceType->defaultModel();
        $maxTokens = $maxTokens ?? $serviceType->maxTokens();

        $startTime = microtime(true);

        try {
            $response = $this->client()->post('/messages', [
                'model' => $model,
                'max_tokens' => $maxTokens,
                'system' => $systemPrompt,
                'messages' => $messages,
            ]);

            $latencyMs = (int) ((microtime(true) - $startTime) * 1000);

            if (!$response->successful()) {
                Log::error('AI API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new \Exception('AI API request failed: ' . $response->body());
            }

            $data = $response->json();

            return [
                'content' => $data['content'][0]['text'] ?? '',
                'model' => $model,
                'tokens_input' => $data['usage']['input_tokens'] ?? 0,
                'tokens_output' => $data['usage']['output_tokens'] ?? 0,
                'latency_ms' => $latencyMs,
                'stop_reason' => $data['stop_reason'] ?? null,
            ];

        } catch (\Exception $e) {
            Log::error('AI Client Exception', [
                'message' => $e->getMessage(),
                'service_type' => $serviceType->value,
            ]);
            throw $e;
        }
    }

    protected function client(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])
            ->timeout(60);
    }
}
```

### app/Services/AI/AIContextBuilder.php

```php
<?php

namespace App\Services\AI;

use App\Models\Enrollment;
use App\Models\LearningPath;
use App\Models\LearningStep;
use App\Models\Module;
use App\Models\QuestionResponse;
use App\Models\StepProgress;
use App\Models\User;
use Illuminate\Support\Collection;

class AIContextBuilder
{
    /**
     * Build context for question explanation
     */
    public function buildQuestionContext(QuestionResponse $response): array
    {
        $question = $response->question;
        $assessment = $question->assessment;
        $step = $assessment->step;
        $module = $step->module;
        $path = $module->learningPath;

        return [
            'question' => [
                'text' => $question->question_text,
                'type' => $question->question_type->value,
                'explanation' => $question->explanation,
                'points' => $question->points,
            ],
            'user_answer' => $response->answer_data,
            'is_correct' => $response->is_correct,
            'correct_answer' => $this->getCorrectAnswer($question),
            'assessment' => [
                'title' => $assessment->title,
                'type' => $assessment->assessment_type->value,
            ],
            'learning_context' => [
                'step_title' => $step->title,
                'module_title' => $module->title,
                'path_title' => $path->title,
                'difficulty' => $path->difficulty->value,
            ],
        ];
    }

    /**
     * Build context for step hints
     */
    public function buildStepContext(StepProgress $progress): array
    {
        $step = $progress->learningStep;
        $module = $step->module;
        $path = $module->learningPath;
        $enrollment = $progress->enrollment;

        // Get previous steps' materials for context
        $previousMaterials = $this->getPreviousMaterials($step);

        return [
            'step' => [
                'title' => $step->title,
                'description' => $step->description,
                'type' => $step->step_type->value,
                'estimated_minutes' => $step->estimated_minutes,
            ],
            'materials' => $step->materials->map(fn($m) => [
                'type' => $m->material_type->value,
                'title' => $m->title,
                'content_preview' => $this->truncateContent($m->content, 500),
            ])->toArray(),
            'progress' => [
                'status' => $progress->status->value,
                'time_spent_minutes' => round($progress->time_spent_seconds / 60),
                'attempts' => $progress->attempt_count ?? 0,
            ],
            'learning_context' => [
                'module_title' => $module->title,
                'path_title' => $path->title,
                'path_difficulty' => $path->difficulty->value,
                'overall_progress_percent' => $enrollment->progress_percentage,
            ],
            'previous_context' => $previousMaterials,
        ];
    }

    /**
     * Build context for AI tutor conversation
     */
    public function buildTutorContext(User $user, ?LearningPath $path = null, ?Module $module = null, ?LearningStep $step = null): array
    {
        $context = [
            'user' => [
                'name' => $user->name,
                'role' => $user->role->value,
            ],
        ];

        if ($path) {
            $enrollment = $user->enrollments()
                ->where('learning_path_id', $path->id)
                ->first();

            $context['learning_path'] = [
                'title' => $path->title,
                'description' => $this->truncateContent($path->description, 300),
                'difficulty' => $path->difficulty->value,
                'category' => $path->category?->name,
            ];

            if ($enrollment) {
                $context['enrollment'] = [
                    'progress_percent' => $enrollment->progress_percentage,
                    'status' => $enrollment->status->value,
                    'started_at' => $enrollment->enrolled_at->toDateString(),
                ];

                // Get weak areas
                $context['weak_areas'] = $this->identifyWeakAreas($enrollment);
            }
        }

        if ($module) {
            $context['module'] = [
                'title' => $module->title,
                'description' => $this->truncateContent($module->description, 200),
                'position' => $module->position,
            ];
        }

        if ($step) {
            $context['step'] = [
                'title' => $step->title,
                'description' => $step->description,
                'type' => $step->step_type->value,
            ];

            // Include step materials summary
            $context['available_materials'] = $step->materials->map(fn($m) => [
                'title' => $m->title,
                'type' => $m->material_type->value,
            ])->toArray();
        }

        return $context;
    }

    /**
     * Build context for practice question generation
     */
    public function buildPracticeContext(User $user, ?LearningPath $path, ?Module $module, ?LearningStep $step): array
    {
        $context = [
            'scope' => $step ? 'step' : ($module ? 'module' : ($path ? 'path' : 'general')),
        ];

        // Gather all relevant materials
        $materials = collect();

        if ($step) {
            $materials = $step->materials;
            $context['focus'] = [
                'step_title' => $step->title,
                'step_description' => $step->description,
            ];
        } elseif ($module) {
            $materials = $module->steps->flatMap(fn($s) => $s->materials);
            $context['focus'] = [
                'module_title' => $module->title,
                'module_description' => $module->description,
            ];
        } elseif ($path) {
            $materials = $path->modules->flatMap(fn($m) => $m->steps->flatMap(fn($s) => $s->materials));
            $context['focus'] = [
                'path_title' => $path->title,
                'path_description' => $path->description,
            ];
        }

        // Summarize materials for context
        $context['materials'] = $materials->take(20)->map(fn($m) => [
            'id' => $m->id,
            'title' => $m->title,
            'type' => $m->material_type->value,
            'content_summary' => $this->truncateContent($m->content, 300),
        ])->toArray();

        // Get user's performance data
        if ($path) {
            $enrollment = $user->enrollments()->where('learning_path_id', $path->id)->first();
            if ($enrollment) {
                $context['performance'] = [
                    'weak_areas' => $this->identifyWeakAreas($enrollment),
                    'overall_accuracy' => $this->calculateOverallAccuracy($enrollment),
                ];
            }
        }

        return $context;
    }

    /**
     * Build module summary context
     */
    public function buildSummaryContext(Module $module): array
    {
        return [
            'module' => [
                'title' => $module->title,
                'description' => $module->description,
            ],
            'steps' => $module->steps->map(fn($step) => [
                'title' => $step->title,
                'type' => $step->step_type->value,
                'materials' => $step->materials->map(fn($m) => [
                    'title' => $m->title,
                    'type' => $m->material_type->value,
                    'content' => $m->content,
                ])->toArray(),
            ])->toArray(),
            'path' => [
                'title' => $module->learningPath->title,
                'difficulty' => $module->learningPath->difficulty->value,
            ],
        ];
    }

    protected function getCorrectAnswer($question): mixed
    {
        if ($question->question_type->hasOptions()) {
            return $question->answerOptions
                ->where('is_correct', true)
                ->pluck('option_text')
                ->toArray();
        }

        return $question->correct_answer ?? null;
    }

    protected function getPreviousMaterials(LearningStep $step): array
    {
        $module = $step->module;
        $previousSteps = $module->steps()
            ->where('position', '<', $step->position)
            ->with('materials')
            ->get();

        return $previousSteps->flatMap(fn($s) => $s->materials)
            ->take(5)
            ->map(fn($m) => [
                'title' => $m->title,
                'type' => $m->material_type->value,
                'summary' => $this->truncateContent($m->content, 200),
            ])
            ->toArray();
    }

    protected function identifyWeakAreas(Enrollment $enrollment): array
    {
        // Analyze assessment attempts to find weak areas
        $attempts = $enrollment->assessmentAttempts()
            ->with('responses.question')
            ->get();

        $incorrectByTopic = [];

        foreach ($attempts as $attempt) {
            foreach ($attempt->responses->where('is_correct', false) as $response) {
                $topics = $response->question->topics ?? ['general'];
                foreach ($topics as $topic) {
                    $incorrectByTopic[$topic] = ($incorrectByTopic[$topic] ?? 0) + 1;
                }
            }
        }

        arsort($incorrectByTopic);

        return array_slice(array_keys($incorrectByTopic), 0, 5);
    }

    protected function calculateOverallAccuracy(Enrollment $enrollment): float
    {
        $attempts = $enrollment->assessmentAttempts;
        if ($attempts->isEmpty()) return 0;

        $totalCorrect = $attempts->sum('correct_answers');
        $totalQuestions = $attempts->sum('total_questions');

        return $totalQuestions > 0 ? round(($totalCorrect / $totalQuestions) * 100, 1) : 0;
    }

    protected function truncateContent(?string $content, int $maxLength): string
    {
        if (!$content) return '';

        // Strip HTML tags
        $content = strip_tags($content);

        if (strlen($content) <= $maxLength) return $content;

        return substr($content, 0, $maxLength) . '...';
    }
}
```

### app/Services/AI/AIExplanationService.php

```php
<?php

namespace App\Services\AI;

use App\Enums\AIContentType;
use App\Enums\AIServiceType;
use App\Models\AIGeneratedContent;
use App\Models\QuestionResponse;
use App\Models\StepProgress;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class AIExplanationService
{
    public function __construct(
        protected AIClientService $client,
        protected AIContextBuilder $contextBuilder,
        protected AIUsageService $usageService,
    ) {}

    /**
     * Generate explanation for wrong answer
     */
    public function explainWrongAnswer(QuestionResponse $response, User $user): AIGeneratedContent
    {
        // Check cache first
        $cacheKey = "explanation:question:{$response->question_id}:answer:" . md5(json_encode($response->answer_data));
        
        $cached = AIGeneratedContent::where('cache_key', $cacheKey)
            ->valid()
            ->first();

        if ($cached) {
            return $cached;
        }

        // Check quota
        $this->usageService->checkQuota($user, AIServiceType::Explanation);

        $context = $this->contextBuilder->buildQuestionContext($response);

        $systemPrompt = $this->buildExplanationPrompt();
        
        $messages = [
            [
                'role' => 'user',
                'content' => json_encode($context, JSON_UNESCAPED_UNICODE),
            ],
        ];

        $result = $this->client->createMessage(
            AIServiceType::Explanation,
            $systemPrompt,
            $messages,
        );

        // Log usage
        $this->usageService->logUsage($user, AIServiceType::Explanation, $result, $response);

        // Cache and store
        return AIGeneratedContent::create([
            'contentable_type' => QuestionResponse::class,
            'contentable_id' => $response->id,
            'content_type' => AIContentType::Explanation,
            'content' => $result['content'],
            'content_metadata' => [
                'model' => $result['model'],
                'tokens_input' => $result['tokens_input'],
                'tokens_output' => $result['tokens_output'],
                'latency_ms' => $result['latency_ms'],
            ],
            'context_snapshot' => $context,
            'cache_key' => $cacheKey,
            'expires_at' => now()->addMinutes(AIContentType::Explanation->cacheDuration()),
            'user_id' => $user->id,
        ]);
    }

    /**
     * Generate progressive hint for step
     */
    public function generateHint(StepProgress $progress, User $user, int $hintLevel = 1): AIGeneratedContent
    {
        $this->usageService->checkQuota($user, AIServiceType::Hint);

        $context = $this->contextBuilder->buildStepContext($progress);
        $context['hint_level'] = $hintLevel;
        $context['max_hint_level'] = 3;

        $systemPrompt = $this->buildHintPrompt($hintLevel);

        $messages = [
            [
                'role' => 'user',
                'content' => json_encode($context, JSON_UNESCAPED_UNICODE),
            ],
        ];

        $result = $this->client->createMessage(
            AIServiceType::Hint,
            $systemPrompt,
            $messages,
        );

        $this->usageService->logUsage($user, AIServiceType::Hint, $result, $progress);

        return AIGeneratedContent::create([
            'contentable_type' => StepProgress::class,
            'contentable_id' => $progress->id,
            'content_type' => AIContentType::Hint,
            'content' => $result['content'],
            'content_metadata' => [
                'model' => $result['model'],
                'hint_level' => $hintLevel,
                'tokens_input' => $result['tokens_input'],
                'tokens_output' => $result['tokens_output'],
            ],
            'context_snapshot' => $context,
            'user_id' => $user->id,
        ]);
    }

    protected function buildExplanationPrompt(): string
    {
        return <<<PROMPT
Du bist ein freundlicher und geduldiger Lernassistent für die LearningPilot. 
Deine Aufgabe ist es, Lernenden zu erklären, warum ihre Antwort falsch war und 
ihnen zu helfen, das Konzept besser zu verstehen.

ANWEISUNGEN:
1. Erkläre zunächst kurz und verständlich, warum die gegebene Antwort falsch ist
2. Zeige dann, was die richtige Antwort ist und warum
3. Gib zusätzlichen Kontext oder Merkhilfen, die beim Verständnis helfen
4. Verwende einfache Sprache, passend zum Schwierigkeitsgrad des Lernpfads
5. Sei ermutigend - Fehler sind Lernchancen!

FORMAT:
- Verwende Markdown für bessere Lesbarkeit
- Halte die Erklärung kompakt (max. 200-300 Wörter)
- Nutze Aufzählungen oder nummerierte Listen wo sinnvoll
- Füge ggf. ein kurzes Beispiel hinzu

EINGABE:
Du erhältst JSON mit Informationen zur Frage, der falschen Antwort des Nutzers,
der richtigen Antwort und dem Lernkontext.

Antworte auf Deutsch.
PROMPT;
    }

    protected function buildHintPrompt(int $hintLevel): string
    {
        $intensity = match ($hintLevel) {
            1 => 'sehr vage und allgemein - nur eine Richtung geben',
            2 => 'etwas konkreter - auf relevante Konzepte hinweisen',
            3 => 'detailliert - fast die Lösung verraten, aber nicht ganz',
            default => 'sehr vage und allgemein',
        };

        return <<<PROMPT
Du bist ein Lernassistent, der Hinweise für Lernende generiert.

HINWEIS-STUFE: {$hintLevel} von 3
INTENSITÄT: {$intensity}

REGELN:
- Gib niemals die direkte Lösung
- Der Hinweis soll zum Nachdenken anregen
- Passe die Detailtiefe an die Hinweis-Stufe an
- Berücksichtige, wie lange der Lernende schon an diesem Schritt arbeitet
- Verweise ggf. auf relevantes Material aus vorherigen Schritten

FORMAT:
- Kurz und prägnant (max. 100 Wörter)
- Ein klarer Hinweis, keine Aufzählung
- Ermutigender Ton

Antworte auf Deutsch.
PROMPT;
    }
}
```

### app/Services/AI/AITutorService.php

```php
<?php

namespace App\Services\AI;

use App\Enums\AIServiceType;
use App\Models\AITutorConversation;
use App\Models\AITutorMessage;
use App\Models\LearningPath;
use App\Models\LearningStep;
use App\Models\Module;
use App\Models\User;

class AITutorService
{
    public function __construct(
        protected AIClientService $client,
        protected AIContextBuilder $contextBuilder,
        protected AIUsageService $usageService,
    ) {}

    /**
     * Start a new tutor conversation
     */
    public function startConversation(
        User $user,
        ?LearningPath $path = null,
        ?Module $module = null,
        ?LearningStep $step = null,
    ): AITutorConversation {
        $context = $this->contextBuilder->buildTutorContext($user, $path, $module, $step);

        return AITutorConversation::create([
            'user_id' => $user->id,
            'learning_path_id' => $path?->id,
            'module_id' => $module?->id,
            'step_id' => $step?->id,
            'title' => $this->generateTitle($path, $module, $step),
            'system_context' => $context,
            'status' => 'active',
        ]);
    }

    /**
     * Send a message and get AI response
     */
    public function sendMessage(AITutorConversation $conversation, string $userMessage, User $user): AITutorMessage
    {
        // Check quota
        $this->usageService->checkQuota($user, AIServiceType::TutorChat);

        // Store user message
        $userMsg = AITutorMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => $userMessage,
            'created_at' => now(),
        ]);

        // Build messages array for API
        $messages = $this->buildMessagesArray($conversation);

        // Get AI response
        $systemPrompt = $this->buildTutorSystemPrompt($conversation);
        
        $result = $this->client->createMessage(
            AIServiceType::TutorChat,
            $systemPrompt,
            $messages,
        );

        // Store AI response
        $aiMsg = AITutorMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'content' => $result['content'],
            'model' => $result['model'],
            'tokens_input' => $result['tokens_input'],
            'tokens_output' => $result['tokens_output'],
            'latency_ms' => $result['latency_ms'],
            'created_at' => now(),
        ]);

        // Update conversation stats
        $totalTokens = $result['tokens_input'] + $result['tokens_output'];
        $conversation->increment('total_messages', 2);
        $conversation->increment('total_tokens_used', $totalTokens);
        $conversation->update(['last_message_at' => now()]);

        // Log usage
        $this->usageService->logUsage($user, AIServiceType::TutorChat, $result);

        return $aiMsg;
    }

    /**
     * Get conversation with messages
     */
    public function getConversation(string $conversationId): ?AITutorConversation
    {
        return AITutorConversation::with(['messages' => function ($q) {
            $q->orderBy('created_at', 'asc');
        }])->find($conversationId);
    }

    /**
     * Archive a conversation
     */
    public function archiveConversation(AITutorConversation $conversation): void
    {
        $conversation->update(['status' => 'archived']);
    }

    protected function buildMessagesArray(AITutorConversation $conversation): array
    {
        return $conversation->messages()
            ->whereIn('role', ['user', 'assistant'])
            ->orderBy('created_at')
            ->get()
            ->map(fn($m) => [
                'role' => $m->role,
                'content' => $m->content,
            ])
            ->toArray();
    }

    protected function buildTutorSystemPrompt(AITutorConversation $conversation): string
    {
        $context = json_encode($conversation->system_context, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $scope = $conversation->getContextScope();

        return <<<PROMPT
Du bist ein intelligenter und freundlicher KI-Tutor für die LearningPilot, eine E-Learning-Plattform.

DEINE ROLLE:
- Hilf Lernenden, Konzepte zu verstehen
- Beantworte Fragen zu Lernmaterialien
- Erkläre komplexe Themen auf verständliche Weise
- Nutze den sokratischen Dialog - stelle Gegenfragen, um zum Nachdenken anzuregen
- Gib niemals direkte Lösungen für Assessments oder Aufgaben

KONTEXT DES GESPRÄCHS:
Scope: {$scope}
{$context}

VERHALTENSREGELN:
1. Sei freundlich, geduldig und ermutigend
2. Passe deine Erklärungen an das Niveau des Lernpfads an
3. Verweise auf relevante Lernmaterialien, wenn sinnvoll
4. Bei Fragen zu Assessments: Erkläre Konzepte, aber gib nicht die Antworten
5. Wenn du etwas nicht weißt, sag es ehrlich
6. Halte Antworten kompakt, aber vollständig (max. 300 Wörter)
7. Nutze Markdown für bessere Formatierung
8. Wenn der Lernende frustriert scheint, sei besonders einfühlsam

SPRACHE: Deutsch

Beginne jede Antwort direkt mit dem Inhalt, ohne Präambel wie "Natürlich!" oder "Gerne!".
PROMPT;
    }

    protected function generateTitle(?LearningPath $path, ?Module $module, ?LearningStep $step): string
    {
        if ($step) {
            return "Hilfe: {$step->title}";
        }
        if ($module) {
            return "Fragen zu: {$module->title}";
        }
        if ($path) {
            return "Tutor: {$path->title}";
        }
        return "Allgemeine Fragen";
    }
}
```

### app/Services/AI/AIPracticeGeneratorService.php

```php
<?php

namespace App\Services\AI;

use App\Enums\AIPracticeDifficulty;
use App\Enums\AIServiceType;
use App\Enums\Difficulty;
use App\Enums\QuestionType;
use App\Models\AIPracticeQuestion;
use App\Models\AIPracticeSession;
use App\Models\LearningPath;
use App\Models\LearningStep;
use App\Models\Module;
use App\Models\User;

class AIPracticeGeneratorService
{
    public function __construct(
        protected AIClientService $client,
        protected AIContextBuilder $contextBuilder,
        protected AIUsageService $usageService,
    ) {}

    /**
     * Start a new practice session
     */
    public function startSession(
        User $user,
        int $questionCount = 10,
        AIPracticeDifficulty $difficulty = AIPracticeDifficulty::Adaptive,
        ?LearningPath $path = null,
        ?Module $module = null,
        ?LearningStep $step = null,
        array $focusAreas = [],
    ): AIPracticeSession {
        $session = AIPracticeSession::create([
            'user_id' => $user->id,
            'learning_path_id' => $path?->id,
            'module_id' => $module?->id,
            'step_id' => $step?->id,
            'difficulty' => $difficulty,
            'question_count' => $questionCount,
            'focus_areas' => $focusAreas,
            'status' => 'active',
            'started_at' => now(),
        ]);

        // Generate first batch of questions
        $this->generateQuestions($session, $user, min(5, $questionCount));

        return $session;
    }

    /**
     * Generate practice questions for session
     */
    public function generateQuestions(AIPracticeSession $session, User $user, int $count = 5): void
    {
        $this->usageService->checkQuota($user, AIServiceType::PracticeGeneration);

        $context = $this->contextBuilder->buildPracticeContext(
            $user,
            $session->learningPath,
            $session->module,
            $session->step,
        );

        // Determine difficulty for this batch
        $difficulty = $this->determineDifficulty($session);
        $context['target_difficulty'] = $difficulty->value;
        $context['questions_to_generate'] = $count;
        $context['existing_questions'] = $session->questions_generated;

        $systemPrompt = $this->buildGeneratorPrompt();

        $messages = [
            [
                'role' => 'user',
                'content' => json_encode($context, JSON_UNESCAPED_UNICODE),
            ],
        ];

        $result = $this->client->createMessage(
            AIServiceType::PracticeGeneration,
            $systemPrompt,
            $messages,
        );

        // Parse and store questions
        $questions = $this->parseGeneratedQuestions($result['content']);
        
        $position = $session->questions_generated;
        foreach ($questions as $questionData) {
            AIPracticeQuestion::create([
                'session_id' => $session->id,
                'question_type' => QuestionType::from($questionData['type']),
                'question_text' => $questionData['question'],
                'options' => $questionData['options'] ?? null,
                'correct_answer' => $questionData['correct_answer'] ?? null,
                'explanation' => $questionData['explanation'],
                'difficulty' => Difficulty::from($questionData['difficulty']),
                'topics' => $questionData['topics'] ?? [],
                'source_material_ids' => $questionData['source_ids'] ?? [],
                'position' => ++$position,
                'created_at' => now(),
            ]);
        }

        $session->update(['questions_generated' => $position]);

        $this->usageService->logUsage($user, AIServiceType::PracticeGeneration, $result);
    }

    /**
     * Submit answer to practice question
     */
    public function submitAnswer(AIPracticeQuestion $question, mixed $answer, User $user): array
    {
        $isCorrect = $this->evaluateAnswer($question, $answer);
        
        $question->update([
            'user_answer' => is_array($answer) ? json_encode($answer) : $answer,
            'is_correct' => $isCorrect,
            'answered_at' => now(),
        ]);

        // Update session stats
        $session = $question->session;
        $session->increment('questions_answered');
        if ($isCorrect) {
            $session->increment('correct_answers');
        }

        // Generate AI feedback for wrong answers
        $feedback = null;
        if (!$isCorrect) {
            $feedback = $this->generateAnswerFeedback($question, $answer, $user);
            $question->update(['ai_feedback' => $feedback]);
        }

        // Check if more questions needed
        if ($session->questions_answered >= $session->questions_generated && 
            $session->questions_generated < $session->question_count) {
            $remaining = $session->question_count - $session->questions_generated;
            $this->generateQuestions($session, $user, min(5, $remaining));
        }

        // Check completion
        if ($session->questions_answered >= $session->question_count) {
            $session->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }

        return [
            'is_correct' => $isCorrect,
            'explanation' => $question->explanation,
            'feedback' => $feedback,
            'correct_answer' => $this->getCorrectAnswerDisplay($question),
        ];
    }

    protected function determineDifficulty(AIPracticeSession $session): Difficulty
    {
        if ($session->difficulty !== AIPracticeDifficulty::Adaptive) {
            return match ($session->difficulty) {
                AIPracticeDifficulty::Beginner => Difficulty::Beginner,
                AIPracticeDifficulty::Intermediate => Difficulty::Intermediate,
                AIPracticeDifficulty::Advanced => Difficulty::Advanced,
            };
        }

        // Adaptive: adjust based on performance
        $accuracy = $session->getAccuracyPercent();

        if ($session->questions_answered < 3) {
            return Difficulty::Intermediate; // Start medium
        }

        if ($accuracy >= 80) {
            return Difficulty::Advanced;
        } elseif ($accuracy >= 50) {
            return Difficulty::Intermediate;
        } else {
            return Difficulty::Beginner;
        }
    }

    protected function evaluateAnswer(AIPracticeQuestion $question, mixed $answer): bool
    {
        return match ($question->question_type) {
            QuestionType::SingleChoice, QuestionType::TrueFalse => $this->evaluateChoiceAnswer($question, $answer),
            QuestionType::MultipleChoice => $this->evaluateMultipleChoiceAnswer($question, $answer),
            default => false, // Text answers need manual review
        };
    }

    protected function evaluateChoiceAnswer(AIPracticeQuestion $question, mixed $answer): bool
    {
        foreach ($question->options as $option) {
            if ($option['is_correct'] && $option['text'] === $answer) {
                return true;
            }
        }
        return false;
    }

    protected function evaluateMultipleChoiceAnswer(AIPracticeQuestion $question, mixed $answer): bool
    {
        $correctOptions = collect($question->options)
            ->where('is_correct', true)
            ->pluck('text')
            ->sort()
            ->values()
            ->toArray();

        $userAnswer = collect($answer)->sort()->values()->toArray();

        return $correctOptions === $userAnswer;
    }

    protected function generateAnswerFeedback(AIPracticeQuestion $question, mixed $answer, User $user): string
    {
        // Simple feedback without API call to save quota
        $feedback = "Leider nicht ganz richtig. ";
        $feedback .= "Die richtige Antwort war: " . $this->getCorrectAnswerDisplay($question) . ". ";
        $feedback .= $question->explanation;

        return $feedback;
    }

    protected function getCorrectAnswerDisplay(AIPracticeQuestion $question): string
    {
        if ($question->question_type->hasOptions()) {
            return collect($question->options)
                ->where('is_correct', true)
                ->pluck('text')
                ->implode(', ');
        }
        return $question->correct_answer ?? '';
    }

    protected function parseGeneratedQuestions(string $content): array
    {
        // Extract JSON from response
        preg_match('/\[[\s\S]*\]/', $content, $matches);
        
        if (empty($matches)) {
            throw new \Exception('Failed to parse generated questions');
        }

        return json_decode($matches[0], true) ?? [];
    }

    protected function buildGeneratorPrompt(): string
    {
        return <<<PROMPT
Du bist ein Experte für die Erstellung von Übungsfragen für E-Learning.

AUFGABE:
Generiere Übungsfragen basierend auf dem bereitgestellten Lernmaterial.

ANFORDERUNGEN:
1. Erstelle Fragen auf dem angegebenen Schwierigkeitsgrad
2. Variiere die Fragetypen (single_choice, multiple_choice, true_false)
3. Jede Frage muss eine detaillierte Erklärung haben
4. Bei Multiple Choice: genau 4 Optionen, 1 oder mehr richtig
5. Decke verschiedene Aspekte des Materials ab
6. Vermeide triviale oder missverständliche Fragen

OUTPUT FORMAT (JSON Array):
```json
[
  {
    "type": "single_choice",
    "question": "Fragetext hier",
    "options": [
      {"text": "Option A", "is_correct": false},
      {"text": "Option B", "is_correct": true},
      {"text": "Option C", "is_correct": false},
      {"text": "Option D", "is_correct": false}
    ],
    "explanation": "Erklärung warum B richtig ist...",
    "difficulty": "intermediate",
    "topics": ["topic1", "topic2"],
    "source_ids": ["material-id-1"]
  }
]
```

Für true_false Fragen:
```json
{
  "type": "true_false",
  "question": "Aussage zum Bewerten",
  "options": [
    {"text": "Wahr", "is_correct": true},
    {"text": "Falsch", "is_correct": false}
  ],
  "explanation": "...",
  "difficulty": "beginner",
  "topics": ["topic"]
}
```

WICHTIG: Antworte NUR mit dem JSON Array, kein zusätzlicher Text.
PROMPT;
    }
}
```

### app/Services/AI/AISummaryService.php

```php
<?php

namespace App\Services\AI;

use App\Enums\AIContentType;
use App\Enums\AIServiceType;
use App\Models\AIGeneratedContent;
use App\Models\LearningStep;
use App\Models\Module;
use App\Models\User;

class AISummaryService
{
    public function __construct(
        protected AIClientService $client,
        protected AIContextBuilder $contextBuilder,
        protected AIUsageService $usageService,
    ) {}

    /**
     * Generate module summary
     */
    public function generateModuleSummary(Module $module, User $user): AIGeneratedContent
    {
        $cacheKey = "summary:module:{$module->id}:v{$module->updated_at->timestamp}";

        $cached = AIGeneratedContent::where('cache_key', $cacheKey)
            ->valid()
            ->first();

        if ($cached) {
            return $cached;
        }

        $this->usageService->checkQuota($user, AIServiceType::Summary);

        $context = $this->contextBuilder->buildSummaryContext($module);

        $systemPrompt = $this->buildSummaryPrompt('module');

        $messages = [
            [
                'role' => 'user',
                'content' => json_encode($context, JSON_UNESCAPED_UNICODE),
            ],
        ];

        $result = $this->client->createMessage(
            AIServiceType::Summary,
            $systemPrompt,
            $messages,
        );

        $this->usageService->logUsage($user, AIServiceType::Summary, $result, $module);

        return AIGeneratedContent::create([
            'contentable_type' => Module::class,
            'contentable_id' => $module->id,
            'content_type' => AIContentType::Summary,
            'content' => $result['content'],
            'content_metadata' => [
                'model' => $result['model'],
                'tokens_input' => $result['tokens_input'],
                'tokens_output' => $result['tokens_output'],
            ],
            'context_snapshot' => $context,
            'cache_key' => $cacheKey,
            'expires_at' => now()->addMinutes(AIContentType::Summary->cacheDuration()),
            'user_id' => $user->id,
        ]);
    }

    /**
     * Generate flashcards for a step or module
     */
    public function generateFlashcards(Module|LearningStep $content, User $user, int $count = 10): AIGeneratedContent
    {
        $this->usageService->checkQuota($user, AIServiceType::Summary);

        $context = $content instanceof Module 
            ? $this->contextBuilder->buildSummaryContext($content)
            : ['step' => ['title' => $content->title, 'materials' => $content->materials->toArray()]];

        $context['flashcard_count'] = $count;

        $systemPrompt = $this->buildFlashcardPrompt();

        $messages = [
            [
                'role' => 'user',
                'content' => json_encode($context, JSON_UNESCAPED_UNICODE),
            ],
        ];

        $result = $this->client->createMessage(
            AIServiceType::Summary,
            $systemPrompt,
            $messages,
        );

        $this->usageService->logUsage($user, AIServiceType::Summary, $result, $content);

        return AIGeneratedContent::create([
            'contentable_type' => get_class($content),
            'contentable_id' => $content->id,
            'content_type' => AIContentType::Flashcard,
            'content' => $result['content'],
            'content_metadata' => [
                'model' => $result['model'],
                'count' => $count,
            ],
            'context_snapshot' => $context,
            'user_id' => $user->id,
        ]);
    }

    protected function buildSummaryPrompt(string $scope): string
    {
        return <<<PROMPT
Du bist ein Experte für die Erstellung von Lernzusammenfassungen.

AUFGABE:
Erstelle eine prägnante Zusammenfassung des {$scope}-Inhalts.

STRUKTUR:
1. **Überblick** (2-3 Sätze): Worum geht es?
2. **Kernkonzepte**: Die wichtigsten Punkte als Aufzählung
3. **Wichtige Begriffe**: Definitionen von Schlüsselbegriffen
4. **Zusammenhänge**: Wie hängen die Konzepte zusammen?
5. **Praxistipps**: Wie kann man das Gelernte anwenden?

ANFORDERUNGEN:
- Maximal 500 Wörter
- Klare, verständliche Sprache
- Markdown-Formatierung
- Fokus auf das Wesentliche

Antworte auf Deutsch.
PROMPT;
    }

    protected function buildFlashcardPrompt(): string
    {
        return <<<PROMPT
Du bist ein Experte für die Erstellung von Lernkarten (Flashcards).

AUFGABE:
Erstelle Lernkarten basierend auf dem Lernmaterial.

FORMAT (JSON Array):
```json
[
  {
    "front": "Frage oder Begriff",
    "back": "Antwort oder Definition",
    "hint": "Optionaler Hinweis"
  }
]
```

REGELN:
1. Vorderseite: Kurze, präzise Frage oder Begriff
2. Rückseite: Klare, vollständige Antwort
3. Mischung aus Definitionen, Konzepten und Anwendungen
4. Aufsteigender Schwierigkeitsgrad
5. Hinweise nur bei komplexeren Karten

WICHTIG: Antworte NUR mit dem JSON Array.
PROMPT;
    }
}
```

### app/Services/AI/AIUsageService.php

```php
<?php

namespace App\Services\AI;

use App\Enums\AIServiceType;
use App\Exceptions\AIQuotaExceededException;
use App\Models\AIUsageLog;
use App\Models\AIUserQuota;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AIUsageService
{
    /**
     * Check if user has quota available
     */
    public function checkQuota(User $user, AIServiceType $serviceType): void
    {
        $quota = $this->getOrCreateQuota($user);
        
        // Check feature access
        if (!$this->isFeatureEnabled($quota, $serviceType)) {
            throw new AIQuotaExceededException("Diese KI-Funktion ist für deinen Account nicht verfügbar.");
        }

        // Check daily request limit
        if (!$quota->canMakeRequest()) {
            throw new AIQuotaExceededException("Du hast dein tägliches Limit für KI-Anfragen erreicht. Versuch es morgen wieder.");
        }

        // Check monthly token limit
        $estimatedTokens = $serviceType->maxTokens() * 2; // Input + Output estimate
        if (!$quota->hasTokensAvailable($estimatedTokens)) {
            throw new AIQuotaExceededException("Du hast dein monatliches Token-Limit erreicht. Dein Kontingent wird am Monatsanfang zurückgesetzt.");
        }
    }

    /**
     * Log AI usage
     */
    public function logUsage(User $user, AIServiceType $serviceType, array $result, ?Model $context = null): void
    {
        $totalTokens = ($result['tokens_input'] ?? 0) + ($result['tokens_output'] ?? 0);

        // Create usage log
        AIUsageLog::create([
            'user_id' => $user->id,
            'service_type' => $serviceType,
            'model' => $result['model'],
            'tokens_input' => $result['tokens_input'] ?? 0,
            'tokens_output' => $result['tokens_output'] ?? 0,
            'cost_credits' => $this->calculateCost($result['model'], $result['tokens_input'] ?? 0, $result['tokens_output'] ?? 0),
            'latency_ms' => $result['latency_ms'] ?? null,
            'cache_hit' => false,
            'context_type' => $context ? get_class($context) : null,
            'context_id' => $context?->id,
        ]);

        // Update quota
        $quota = $this->getOrCreateQuota($user);
        $quota->incrementUsage($totalTokens);
    }

    /**
     * Get usage statistics for user
     */
    public function getUsageStats(User $user): array
    {
        $quota = $this->getOrCreateQuota($user);
        
        $todayUsage = AIUsageLog::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->selectRaw('service_type, COUNT(*) as count, SUM(tokens_total) as tokens')
            ->groupBy('service_type')
            ->get()
            ->keyBy('service_type');

        $monthlyUsage = AIUsageLog::where('user_id', $user->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->selectRaw('service_type, COUNT(*) as count, SUM(tokens_total) as tokens')
            ->groupBy('service_type')
            ->get()
            ->keyBy('service_type');

        return [
            'quota' => [
                'daily_requests_remaining' => max(0, $quota->daily_request_limit - $quota->daily_requests_used),
                'monthly_tokens_remaining' => $quota->getRemainingTokens(),
                'usage_percent' => $quota->getUsagePercent(),
            ],
            'today' => $todayUsage,
            'this_month' => $monthlyUsage,
            'features' => [
                'tutor' => $quota->tutor_enabled,
                'practice' => $quota->practice_gen_enabled,
                'advanced' => $quota->advanced_explanations,
            ],
        ];
    }

    protected function getOrCreateQuota(User $user): AIUserQuota
    {
        return AIUserQuota::firstOrCreate(
            ['user_id' => $user->id],
            [
                'monthly_token_limit' => config('lernpfad.ai.default_monthly_tokens', 100000),
                'daily_request_limit' => config('lernpfad.ai.default_daily_requests', 100),
            ]
        );
    }

    protected function isFeatureEnabled(AIUserQuota $quota, AIServiceType $serviceType): bool
    {
        return match ($serviceType) {
            AIServiceType::TutorChat => $quota->tutor_enabled,
            AIServiceType::PracticeGeneration => $quota->practice_gen_enabled,
            default => true,
        };
    }

    protected function calculateCost(string $model, int $inputTokens, int $outputTokens): float
    {
        // Cost in credits (1 credit = $0.001)
        $rates = [
            'claude-opus-4-5-20251101' => ['input' => 15, 'output' => 75],
            'claude-sonnet-4-5-20250929' => ['input' => 3, 'output' => 15],
            'claude-haiku-4-5-20251001' => ['input' => 0.25, 'output' => 1.25],
        ];

        $rate = $rates[$model] ?? $rates['claude-haiku-4-5-20251001'];

        return round(
            ($inputTokens / 1000000 * $rate['input']) + ($outputTokens / 1000000 * $rate['output']),
            4
        );
    }
}
```

---

## 7. Livewire Components

### app/Livewire/Learner/AI/TutorChat.php

```php
<?php

namespace App\Livewire\Learner\Ai;

use App\Models\AITutorConversation;
use App\Services\AI\AITutorService;
use Livewire\Component;

class TutorChat extends Component
{
    public ?string $conversationId = null;
    public string $message = '';
    public bool $isLoading = false;
    public ?string $error = null;

    // Context (optional)
    public ?string $pathId = null;
    public ?string $moduleId = null;
    public ?string $stepId = null;

    protected AITutorService $tutorService;

    public function boot(AITutorService $tutorService)
    {
        $this->tutorService = $tutorService;
    }

    public function mount(?string $conversationId = null)
    {
        $this->conversationId = $conversationId;
    }

    public function sendMessage()
    {
        if (empty(trim($this->message))) return;

        $this->isLoading = true;
        $this->error = null;

        try {
            $conversation = $this->getOrCreateConversation();
            
            $this->tutorService->sendMessage(
                $conversation,
                $this->message,
                auth()->user()
            );

            $this->message = '';
            $this->dispatch('message-sent');

        } catch (\App\Exceptions\AIQuotaExceededException $e) {
            $this->error = $e->getMessage();
        } catch (\Exception $e) {
            $this->error = 'Ein Fehler ist aufgetreten. Bitte versuche es erneut.';
            report($e);
        } finally {
            $this->isLoading = false;
        }
    }

    public function getOrCreateConversation(): AITutorConversation
    {
        if ($this->conversationId) {
            return AITutorConversation::findOrFail($this->conversationId);
        }

        $path = $this->pathId ? \App\Models\LearningPath::find($this->pathId) : null;
        $module = $this->moduleId ? \App\Models\Module::find($this->moduleId) : null;
        $step = $this->stepId ? \App\Models\LearningStep::find($this->stepId) : null;

        $conversation = $this->tutorService->startConversation(
            auth()->user(),
            $path,
            $module,
            $step
        );

        $this->conversationId = $conversation->id;

        return $conversation;
    }

    public function render()
    {
        $conversation = $this->conversationId 
            ? $this->tutorService->getConversation($this->conversationId)
            : null;

        return view('livewire.learner.ai.tutor-chat', [
            'conversation' => $conversation,
        ]);
    }
}
```

### app/Livewire/Learner/AI/PracticeSession.php

```php
<?php

namespace App\Livewire\Learner\Ai;

use App\Enums\AIPracticeDifficulty;
use App\Models\AIPracticeSession;
use App\Services\AI\AIPracticeGeneratorService;
use Livewire\Component;

class PracticeSession extends Component
{
    public ?string $sessionId = null;
    public ?string $pathId = null;
    public ?string $moduleId = null;
    public ?string $stepId = null;
    
    public int $questionCount = 10;
    public string $difficulty = 'adaptive';
    
    public ?int $currentQuestionIndex = null;
    public mixed $selectedAnswer = null;
    public ?array $result = null;
    public bool $isLoading = false;

    protected AIPracticeGeneratorService $practiceService;

    public function boot(AIPracticeGeneratorService $practiceService)
    {
        $this->practiceService = $practiceService;
    }

    public function startSession()
    {
        $this->isLoading = true;

        try {
            $path = $this->pathId ? \App\Models\LearningPath::find($this->pathId) : null;
            $module = $this->moduleId ? \App\Models\Module::find($this->moduleId) : null;
            $step = $this->stepId ? \App\Models\LearningStep::find($this->stepId) : null;

            $session = $this->practiceService->startSession(
                auth()->user(),
                $this->questionCount,
                AIPracticeDifficulty::from($this->difficulty),
                $path,
                $module,
                $step
            );

            $this->sessionId = $session->id;
            $this->currentQuestionIndex = 0;

        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    public function submitAnswer()
    {
        if ($this->selectedAnswer === null) return;

        $this->isLoading = true;

        try {
            $session = AIPracticeSession::findOrFail($this->sessionId);
            $question = $session->questions()->where('position', $this->currentQuestionIndex + 1)->first();

            $this->result = $this->practiceService->submitAnswer(
                $question,
                $this->selectedAnswer,
                auth()->user()
            );

        } finally {
            $this->isLoading = false;
        }
    }

    public function nextQuestion()
    {
        $this->currentQuestionIndex++;
        $this->selectedAnswer = null;
        $this->result = null;
    }

    public function render()
    {
        $session = $this->sessionId ? AIPracticeSession::with('questions')->find($this->sessionId) : null;
        $currentQuestion = $session?->questions[$this->currentQuestionIndex] ?? null;

        return view('livewire.learner.ai.practice-session', [
            'session' => $session,
            'currentQuestion' => $currentQuestion,
        ]);
    }
}
```

### app/Livewire/Learner/AI/ExplanationModal.php

```php
<?php

namespace App\Livewire\Learner\Ai;

use App\Models\QuestionResponse;
use App\Services\AI\AIExplanationService;
use Livewire\Component;

class ExplanationModal extends Component
{
    public bool $show = false;
    public ?string $responseId = null;
    public ?string $explanation = null;
    public bool $isLoading = false;
    public ?string $error = null;

    protected $listeners = ['showExplanation'];

    public function showExplanation(string $responseId)
    {
        $this->responseId = $responseId;
        $this->show = true;
        $this->loadExplanation();
    }

    public function loadExplanation()
    {
        $this->isLoading = true;
        $this->error = null;

        try {
            $response = QuestionResponse::findOrFail($this->responseId);
            
            $explanationService = app(AIExplanationService::class);
            $content = $explanationService->explainWrongAnswer($response, auth()->user());
            
            $this->explanation = $content->content;

        } catch (\App\Exceptions\AIQuotaExceededException $e) {
            $this->error = $e->getMessage();
        } catch (\Exception $e) {
            $this->error = 'Erklärung konnte nicht geladen werden.';
            report($e);
        } finally {
            $this->isLoading = false;
        }
    }

    public function submitFeedback(bool $wasHelpful)
    {
        if ($this->responseId) {
            \App\Models\AIGeneratedContent::where('contentable_type', QuestionResponse::class)
                ->where('contentable_id', $this->responseId)
                ->latest()
                ->first()
                ?->update(['was_helpful' => $wasHelpful]);
        }

        $this->dispatch('notify', ['message' => 'Danke für dein Feedback!']);
    }

    public function close()
    {
        $this->show = false;
        $this->explanation = null;
        $this->error = null;
    }

    public function render()
    {
        return view('livewire.learner.ai.explanation-modal');
    }
}
```

---

## 8. Configuration

### config/lernpfad.php (AI Section Addition)

```php
<?php

return [
    // ... existing config ...

    /*
    |--------------------------------------------------------------------------
    | AI Configuration
    |--------------------------------------------------------------------------
    */
    'ai' => [
        // API Configuration
        'provider' => env('AI_PROVIDER', 'anthropic'),
        'api_key' => env('ANTHROPIC_API_KEY'),
        
        // Models
        'models' => [
            'default' => env('AI_MODEL_DEFAULT', 'claude-haiku-4-5-20251001'),
            'tutor' => env('AI_MODEL_TUTOR', 'claude-sonnet-4-5-20250929'),
            'practice' => env('AI_MODEL_PRACTICE', 'claude-sonnet-4-5-20250929'),
        ],

        // Quota defaults
        'default_monthly_tokens' => env('AI_DEFAULT_MONTHLY_TOKENS', 100000),
        'default_daily_requests' => env('AI_DEFAULT_DAILY_REQUESTS', 100),

        // Feature flags
        'features' => [
            'tutor' => env('AI_FEATURE_TUTOR', true),
            'practice' => env('AI_FEATURE_PRACTICE', true),
            'explanations' => env('AI_FEATURE_EXPLANATIONS', true),
            'summaries' => env('AI_FEATURE_SUMMARIES', true),
            'hints' => env('AI_FEATURE_HINTS', true),
        ],

        // Rate limiting
        'rate_limits' => [
            'tutor' => [30, 60],        // 30 requests per 60 minutes
            'practice' => [10, 60],     // 10 sessions per 60 minutes
            'explanation' => [50, 60],  // 50 explanations per 60 minutes
        ],

        // Cache settings
        'cache' => [
            'enabled' => env('AI_CACHE_ENABLED', true),
            'ttl' => [
                'explanation' => 60 * 24 * 7,   // 7 days
                'summary' => 60 * 24 * 30,      // 30 days
                'hint' => 60 * 24,              // 1 day
            ],
        ],

        // Safety settings
        'safety' => [
            'max_message_length' => 4000,
            'max_conversation_messages' => 50,
            'content_filter' => true,
        ],
    ],
];
```

### .env additions

```env
# AI Configuration
AI_PROVIDER=anthropic
ANTHROPIC_API_KEY=your-api-key-here

# Model selection
AI_MODEL_DEFAULT=claude-haiku-4-5-20251001
AI_MODEL_TUTOR=claude-sonnet-4-5-20250929
AI_MODEL_PRACTICE=claude-sonnet-4-5-20250929

# Quota defaults
AI_DEFAULT_MONTHLY_TOKENS=100000
AI_DEFAULT_DAILY_REQUESTS=100

# Feature toggles
AI_FEATURE_TUTOR=true
AI_FEATURE_PRACTICE=true
AI_FEATURE_EXPLANATIONS=true
AI_FEATURE_SUMMARIES=true
AI_FEATURE_HINTS=true

# Cache
AI_CACHE_ENABLED=true
```

---

## 9. Implementation Tasks

### Phase AI-1: Foundation (Week 10)

```
[ ] Create migrations for AI tables
    [ ] ai_generated_contents
    [ ] ai_tutor_conversations
    [ ] ai_tutor_messages
    [ ] ai_practice_sessions
    [ ] ai_practice_questions
    [ ] ai_usage_logs
    [ ] ai_user_quotas
    [ ] ai_feedback_reports

[ ] Create Enums
    [ ] AIContentType
    [ ] AIServiceType
    [ ] AIPracticeDifficulty
    [ ] AIFeedbackType

[ ] Create Models with relationships
    [ ] AIGeneratedContent
    [ ] AITutorConversation
    [ ] AITutorMessage
    [ ] AIPracticeSession
    [ ] AIPracticeQuestion
    [ ] AIUsageLog
    [ ] AIUserQuota
    [ ] AIFeedbackReport

[ ] Update existing models
    [ ] User: hasOne(AIUserQuota), hasMany(AITutorConversation)
    [ ] QuestionResponse: morphMany(AIGeneratedContent)
    [ ] StepProgress: morphMany(AIGeneratedContent)
    [ ] Module: morphMany(AIGeneratedContent)
```

### Phase AI-2: Core Services (Week 10-11)

```
[ ] Create Service Layer
    [ ] AIClientService (Anthropic API client)
    [ ] AIContextBuilder (context aggregation)
    [ ] AIUsageService (quota & logging)
    [ ] AIExplanationService
    [ ] AITutorService
    [ ] AIPracticeGeneratorService
    [ ] AISummaryService

[ ] Create Exception classes
    [ ] AIQuotaExceededException
    [ ] AIServiceException

[ ] Register services in AppServiceProvider
```

### Phase AI-3: Livewire Components (Week 11)

```
[ ] Create Learner AI Components
    [ ] TutorChat (with conversation history)
    [ ] PracticeSession (question flow)
    [ ] ExplanationModal (wrong answer help)
    [ ] HintButton (progressive hints)
    [ ] SummaryPanel (module summaries)
    [ ] FlashcardViewer (study mode)

[ ] Create views
    [ ] livewire/learner/ai/tutor-chat.blade.php
    [ ] livewire/learner/ai/practice-session.blade.php
    [ ] livewire/learner/ai/explanation-modal.blade.php
    [ ] livewire/learner/ai/hint-button.blade.php
    [ ] livewire/learner/ai/summary-panel.blade.php
    [ ] livewire/learner/ai/flashcard-viewer.blade.php
```

### Phase AI-4: Integration (Week 12)

```
[ ] Integrate with existing components
    [ ] Add "Get AI Help" to StepViewer
    [ ] Add "Explain" button to assessment results
    [ ] Add "Practice More" after assessments
    [ ] Add "Summary" button to module completion

[ ] Admin features
    [ ] AI usage dashboard
    [ ] User quota management
    [ ] Feedback review interface

[ ] Routes
    [ ] /learn/ai/tutor - AI Tutor interface
    [ ] /learn/ai/practice - Practice session
    [ ] /learn/ai/summary/{module} - Module summary
```

### Phase AI-5: Testing & Polish (Week 12)

```
[ ] Unit Tests
    [ ] AIContextBuilder tests
    [ ] AIUsageService tests
    [ ] AI response parsing tests

[ ] Feature Tests
    [ ] Tutor conversation flow
    [ ] Practice session flow
    [ ] Quota enforcement
    [ ] Caching behavior

[ ] Performance
    [ ] Implement response streaming (optional)
    [ ] Optimize context building
    [ ] Cache warming for popular content
```

---

## 10. Security Considerations

### Input Sanitization

```php
// In AITutorService
protected function sanitizeUserMessage(string $message): string
{
    // Remove potential prompt injection attempts
    $message = strip_tags($message);
    
    // Limit length
    $message = substr($message, 0, config('lernpfad.ai.safety.max_message_length'));
    
    // Remove suspicious patterns
    $patterns = [
        '/ignore previous instructions/i',
        '/you are now/i',
        '/act as/i',
        '/pretend to be/i',
    ];
    
    foreach ($patterns as $pattern) {
        $message = preg_replace($pattern, '[filtered]', $message);
    }
    
    return $message;
}
```

### Rate Limiting Middleware

```php
// app/Http/Middleware/AIRateLimiter.php
public function handle($request, Closure $next, string $serviceType)
{
    $user = $request->user();
    $key = "ai_rate:{$user->id}:{$serviceType}";
    
    $limits = config("lernpfad.ai.rate_limits.{$serviceType}", [50, 60]);
    
    if (RateLimiter::tooManyAttempts($key, $limits[0])) {
        throw new AIQuotaExceededException(
            "Zu viele Anfragen. Bitte warte " . 
            RateLimiter::availableIn($key) . " Sekunden."
        );
    }
    
    RateLimiter::hit($key, $limits[1] * 60);
    
    return $next($request);
}
```

---

## 11. User Interface Mockups

### AI Tutor Chat

```
┌─────────────────────────────────────────────────────────────┐
│  🤖 KI-Tutor: Einführung in PHP                            │
│  ─────────────────────────────────────────────────────────  │
│                                                             │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ 👤 Was ist der Unterschied zwischen == und ===?     │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ 🤖 Gute Frage! Der Unterschied liegt in der        │   │
│  │    Typprüfung:                                      │   │
│  │                                                     │   │
│  │    • `==` vergleicht nur Werte (lose)              │   │
│  │    • `===` vergleicht Werte UND Typen (strikt)     │   │
│  │                                                     │   │
│  │    Beispiel:                                        │   │
│  │    `"5" == 5`  → true (String wird zu Int)         │   │
│  │    `"5" === 5` → false (verschiedene Typen)        │   │
│  │                                                     │   │
│  │    Kannst du dir vorstellen, wann der strikte      │   │
│  │    Vergleich wichtig sein könnte?                  │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ Schreibe deine Frage...                    [Senden] │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
│  Verbleibende Nachrichten heute: 28/30                     │
└─────────────────────────────────────────────────────────────┘
```

### Practice Session

```
┌─────────────────────────────────────────────────────────────┐
│  🎯 KI-Übungsmodus                                         │
│  ─────────────────────────────────────────────────────────  │
│  Modul: PHP Grundlagen  │  Schwierigkeit: Adaptiv          │
│  ─────────────────────────────────────────────────────────  │
│                                                             │
│  Frage 3 von 10                    [████████░░] 80%        │
│                                                             │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ Was gibt der folgende Code aus?                     │   │
│  │                                                     │   │
│  │ ```php                                              │   │
│  │ $x = "10";                                          │   │
│  │ echo $x + 5;                                        │   │
│  │ ```                                                 │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
│  ○ "105"                                                   │
│  ● 15                                                      │
│  ○ Fehler                                                  │
│  ○ "10" + 5                                                │
│                                                             │
│                                    [Antwort prüfen →]      │
│                                                             │
│  Bisherige Erfolgsquote: 75%                               │
└─────────────────────────────────────────────────────────────┘
```

---

## 12. Summary

### What This Feature Adds

| Capability | Description | User Benefit |
|------------|-------------|--------------|
| AI Explanations | Instant explanations for wrong answers | Learn from mistakes immediately |
| AI Tutor | Conversational help scoped to content | Get help without waiting |
| Practice Generator | Unlimited practice questions | Reinforce learning |
| Progressive Hints | Graduated help for stuck learners | Don't give up on hard content |
| Summaries | AI-generated module recaps | Quick review before tests |
| Flashcards | Auto-generated study cards | Efficient memorization |
| Adaptive Difficulty | Questions adjust to performance | Optimal challenge level |

### Integration Points

1. **Assessment Results** → "Explain this answer" button
2. **Step Viewer** → "Ask AI Tutor" floating button
3. **Module Completion** → "Generate Summary" option
4. **My Progress** → "Practice Weak Areas" recommendation
5. **Admin Dashboard** → AI usage analytics

### Estimated Development Time

| Phase | Duration | Effort |
|-------|----------|--------|
| AI-1: Foundation | 3 days | Database, Models, Config |
| AI-2: Core Services | 4 days | API Client, Services |
| AI-3: Components | 4 days | Livewire, Views |
| AI-4: Integration | 2 days | Connect to existing UI |
| AI-5: Testing | 2 days | Tests, Polish |
| **Total** | **~3 weeks** | **Full feature** |

---

This AI Content Feature specification is designed to integrate seamlessly with the existing LearningPilot architecture while providing powerful adaptive learning capabilities.
