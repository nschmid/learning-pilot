# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**LearningPilot** is a comprehensive Learning Management System (LMS) / E-Learning Platform with AI-powered adaptive learning features.

**Tech Stack**: Laravel 12 TALL (Tailwind CSS 4, Alpine.js 3, Livewire 3), PHP 8.4, PostgreSQL 16+, Jetstream Teams

**Languages**: Multilingual (DE, EN, FR) - use Laravel localization for all user-facing strings

**Target Users**: Students in enrolled schools/educational institutions (B2B Education)

**Business Model**:
- Free trial: 1 month time-limited
- Subscription: Half-yearly billing per school/organization

**Status**: Pre-implementation planning phase. The Laravel project has not been initialized yet.

### What This Project Does

1. **Schools/Institutions (Teams)** enroll and create isolated learning environments for their students and instructors

2. **Instructors** create structured learning paths with modules and steps containing materials (text, video, audio, PDF), tasks (submissions for review), and assessments (quizzes/exams)

3. **Learners** browse a catalog, enroll in paths, consume content, complete tasks, take assessments, earn points, and receive certificates upon completion

4. **AI features** provide instant explanations for wrong answers, conversational tutoring, adaptive practice questions, progressive hints, module summaries, and auto-generated flashcards

5. **Admins** manage users, teams, view analytics, and control AI quotas

## Commands

```bash
# Initial setup
./setup.sh                    # Or manual setup below
# composer install && npm install && cp .env.example .env && php artisan key:generate
# php artisan jetstream:install livewire --teams

# Development
php artisan serve             # Start server at localhost:8000
npm run dev                   # Watch assets (separate terminal)

# Database
php artisan migrate           # Run migrations
php artisan migrate:fresh --seed  # Reset and seed
php artisan db:seed           # Seed database

# Testing
php artisan test              # Run all tests
php artisan test --filter=LearningPathTest  # Run specific test class
php artisan test tests/Feature/LearningPathTest.php  # Run specific file
php artisan test --filter="it_can_create_path"  # Run single test by name

# Code quality
./vendor/bin/pint             # Format code (PSR-12)
./vendor/bin/phpstan analyse  # Static analysis

# Artisan generators
php artisan make:livewire Instructor/LearningPaths/Create
php artisan make:model LearningPath -m
php artisan make:policy LearningPathPolicy --model=LearningPath
php artisan make:request StoreLearningPathRequest
```

## Architecture

### Required Design Patterns

- **Repository Pattern** - All database queries through `app/Repositories/`
- **Service Pattern** - Business logic in `app/Services/`, not Controllers
- **Action Classes** - Single-purpose operations in `app/Actions/{Domain}/`
- **Form Requests** - All validation in `app/Http/Requests/`
- **Policies** - Authorization in `app/Policies/`
- **Events/Listeners** - Decouple side effects from main logic

### Spatie Packages (Required)

Use Spatie packages where applicable:

| Package | Usage |
|---------|-------|
| `spatie/laravel-permission` | Role & permission management (Admin, Instructor, Learner) |
| `spatie/laravel-medialibrary` | File uploads, media conversions, thumbnails |
| `spatie/laravel-activitylog` | Audit logging for user actions |
| `spatie/laravel-sluggable` | Auto-generate slugs for paths, modules, categories |
| `spatie/laravel-translatable` | Multilingual model fields (DE, EN, FR) |
| `spatie/laravel-settings` | Application settings management |
| `spatie/laravel-data` | DTOs for API responses and service layer |
| `spatie/laravel-query-builder` | API filtering, sorting, includes |
| `spatie/laravel-backup` | Database and file backups |
| `spatie/laravel-cookie-consent` | GDPR cookie consent banner |

```bash
# Install Spatie packages
composer require spatie/laravel-permission spatie/laravel-medialibrary \
  spatie/laravel-activitylog spatie/laravel-sluggable spatie/laravel-translatable \
  spatie/laravel-settings spatie/laravel-data spatie/laravel-query-builder \
  spatie/laravel-backup spatie/laravel-cookie-consent
```

### Additional Required Packages

```bash
# Social login (Google, Microsoft)
composer require laravel/socialite

# Error tracking
composer require sentry/sentry-laravel

# PDF generation (already in spec)
composer require barryvdh/laravel-dompdf

# S3-compatible storage (DigitalOcean Spaces)
composer require league/flysystem-aws-s3-v3
```

### File Storage (DigitalOcean Spaces)

Use **DigitalOcean Spaces** (S3-compatible) for all file storage:

```php
// config/filesystems.php
'disks' => [
    'do_spaces' => [
        'driver' => 's3',
        'key' => env('DO_SPACES_KEY'),
        'secret' => env('DO_SPACES_SECRET'),
        'region' => env('DO_SPACES_REGION', 'fra1'),
        'bucket' => env('DO_SPACES_BUCKET'),
        'endpoint' => env('DO_SPACES_ENDPOINT', 'https://fra1.digitaloceanspaces.com'),
        'url' => env('DO_SPACES_URL'),  // CDN URL
        'visibility' => 'private',
        'throw' => true,
    ],
],
```

**Environment Configuration** (.env):
```
# DigitalOcean Spaces (S3-compatible)
DO_SPACES_KEY=your-spaces-key
DO_SPACES_SECRET=your-spaces-secret
DO_SPACES_REGION=fra1
DO_SPACES_BUCKET=learningpilot-files
DO_SPACES_ENDPOINT=https://fra1.digitaloceanspaces.com
DO_SPACES_URL=https://learningpilot-files.fra1.cdn.digitaloceanspaces.com

FILESYSTEM_DISK=do_spaces
```

**Storage Structure**:
```
learningpilot-files/
├── teams/{team_id}/
│   ├── paths/{path_id}/
│   │   ├── thumbnail.jpg
│   │   └── materials/
│   │       ├── {step_id}/
│   │       │   ├── video.mp4
│   │       │   ├── document.pdf
│   │       │   └── images/
│   ├── submissions/{submission_id}/
│   │   └── uploaded_files/
│   └── certificates/{certificate_id}.pdf
├── avatars/{user_id}.jpg
└── temp/
```

**File Size Limits** (per subscription tier):
| Plan | Max Upload | Storage Limit |
|------|------------|---------------|
| Trial | 50 MB | 1 GB |
| Pro | 200 MB | 50 GB |
| Team Starter | 500 MB | 200 GB |
| Team Business | 1 GB | Unlimited |

### Video Hosting Strategy

**Dual approach**: Self-hosted (S3) + External embeds (YouTube, Loom)

```php
// app/Enums/VideoSourceType.php
enum VideoSourceType: string
{
    case Upload = 'upload';      // Self-hosted on DO Spaces
    case YouTube = 'youtube';    // YouTube embed
    case Loom = 'loom';          // Loom embed
    case Vimeo = 'vimeo';        // Vimeo embed (future)
}
```

**Video Material Schema**:
```php
// In learning_materials table
'video_source_type' => VideoSourceType::class,  // upload, youtube, loom
'video_url' => 'string|nullable',               // External URL or S3 path
'video_id' => 'string|nullable',                // External video ID
'duration_seconds' => 'integer|nullable',
```

**Embed URL Patterns**:
```php
// app/Services/VideoEmbedService.php
public function getEmbedUrl(LearningMaterial $material): string
{
    return match ($material->video_source_type) {
        VideoSourceType::YouTube => "https://www.youtube.com/embed/{$material->video_id}",
        VideoSourceType::Loom => "https://www.loom.com/embed/{$material->video_id}",
        VideoSourceType::Vimeo => "https://player.vimeo.com/video/{$material->video_id}",
        VideoSourceType::Upload => Storage::disk('do_spaces')->temporaryUrl($material->video_url, now()->addHours(4)),
    };
}
```

**Video Player Component**:
```html
<!-- Self-hosted video -->
<video controls class="w-full rounded-brand">
    <source src="{{ $signedUrl }}" type="video/mp4">
</video>

<!-- YouTube/Loom embed -->
<iframe
    src="{{ $embedUrl }}"
    class="w-full aspect-video rounded-brand"
    frameborder="0"
    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
    allowfullscreen>
</iframe>
```

**Upload Processing** (for self-hosted):
```php
// Use queue job for video processing
- Accept: mp4, webm, mov
- Max size: Based on subscription tier
- Generate thumbnail at 10% mark (ffmpeg)
- Extract duration (ffmpeg)
- Store in DO Spaces with private visibility
- Generate signed URLs for playback (4-hour expiry)
```

### Subscription Billing (Required)

Use **Laravel Cashier with Stripe** for subscription management:

```bash
composer require laravel/cashier
php artisan vendor:publish --tag="cashier-migrations"
php artisan migrate
```

**Subscription Model** (school-focused, half-yearly billing with 1-month trial):
```php
// Subscription plans in config/lernpfad.php
'billing' => [
    'provider' => 'stripe',
    'default_currency' => 'eur',
    'supported_currencies' => ['chf', 'eur', 'usd'],
    'trial_days' => 30,  // 1 month free trial
    'plans' => [
        'school_starter' => [
            'name' => 'Schule Starter',
            'interval' => 'every 6 months',
            'prices' => [
                'chf' => ['amount' => 49900, 'stripe_price_id' => env('STRIPE_PRICE_SCHOOL_STARTER_CHF')],  // CHF 499
                'eur' => ['amount' => 49900, 'stripe_price_id' => env('STRIPE_PRICE_SCHOOL_STARTER_EUR')],  // €499
                'usd' => ['amount' => 54900, 'stripe_price_id' => env('STRIPE_PRICE_SCHOOL_STARTER_USD')],  // $549
            ],
            'max_students' => 50,
            'max_instructors' => 5,
            'storage_gb' => 25,
            'features' => [
                '50 students',
                '5 instructors',
                '25 GB storage',
                '100 AI requests/day',
                'Email support',
                'Certificates',
            ],
        ],
        'school_pro' => [
            'name' => 'Schule Professional',
            'interval' => 'every 6 months',
            'prices' => [
                'chf' => ['amount' => 99900, 'stripe_price_id' => env('STRIPE_PRICE_SCHOOL_PRO_CHF')],   // CHF 999
                'eur' => ['amount' => 99900, 'stripe_price_id' => env('STRIPE_PRICE_SCHOOL_PRO_EUR')],   // €999
                'usd' => ['amount' => 109900, 'stripe_price_id' => env('STRIPE_PRICE_SCHOOL_PRO_USD')],  // $1,099
            ],
            'max_students' => 200,
            'max_instructors' => 20,
            'storage_gb' => 100,
            'features' => [
                '200 students',
                '20 instructors',
                '100 GB storage',
                '500 AI requests/day',
                'Priority support',
                'Analytics dashboard',
                'Custom branding',
            ],
        ],
        'school_enterprise' => [
            'name' => 'Schule Enterprise',
            'interval' => 'every 6 months',
            'prices' => [
                'chf' => ['amount' => 199900, 'stripe_price_id' => env('STRIPE_PRICE_SCHOOL_ENTERPRISE_CHF')],  // CHF 1,999
                'eur' => ['amount' => 199900, 'stripe_price_id' => env('STRIPE_PRICE_SCHOOL_ENTERPRISE_EUR')],  // €1,999
                'usd' => ['amount' => 219900, 'stripe_price_id' => env('STRIPE_PRICE_SCHOOL_ENTERPRISE_USD')],  // $2,199
            ],
            'max_students' => null,  // Unlimited
            'max_instructors' => null,
            'storage_gb' => null,  // Unlimited
            'features' => [
                'Unlimited students',
                'Unlimited instructors',
                'Unlimited storage',
                'Unlimited AI requests',
                'Dedicated support',
                'API access',
                'SSO integration',
                'Custom domain',
            ],
        ],
    ],
],

// Currency selection helper
'currency_symbols' => [
    'chf' => 'CHF',
    'eur' => '€',
    'usd' => '$',
],
```

**Trial Period**:
```php
// Team model - apply trial on creation
class Team extends JetstreamTeam
{
    use Billable;

    protected static function booted()
    {
        static::created(function ($team) {
            $team->createAsStripeCustomer([
                'name' => $team->name,
            ]);
            // Trial ends after 30 days
            $team->trial_ends_at = now()->addDays(config('lernpfad.billing.trial_days'));
            $team->save();
        });
    }

    public function onTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function trialDaysRemaining(): int
    {
        return $this->trial_ends_at ? now()->diffInDays($this->trial_ends_at, false) : 0;
    }
}
```

**Billable Model** (Team-based billing for schools):
```php
// app/Models/Team.php - Schools subscribe at team level
use Laravel\Cashier\Billable;

class Team extends JetstreamTeam
{
    use Billable;

    // Check if team can add more students
    public function canAddStudent(): bool
    {
        $plan = $this->currentPlan();
        if (!$plan || !$plan['max_students']) return true;
        return $this->users()->where('role', 'learner')->count() < $plan['max_students'];
    }

    // Check storage usage
    public function storageUsedGb(): float
    {
        return $this->files()->sum('size') / (1024 * 1024 * 1024);
    }
}
```

**Stripe Configuration** (.env):
```
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
CASHIER_CURRENCY=eur
TRIAL_DAYS=30

# School subscription plans - CHF (Switzerland)
STRIPE_PRICE_SCHOOL_STARTER_CHF=price_...
STRIPE_PRICE_SCHOOL_PRO_CHF=price_...
STRIPE_PRICE_SCHOOL_ENTERPRISE_CHF=price_...

# School subscription plans - EUR (Europe)
STRIPE_PRICE_SCHOOL_STARTER_EUR=price_...
STRIPE_PRICE_SCHOOL_PRO_EUR=price_...
STRIPE_PRICE_SCHOOL_ENTERPRISE_EUR=price_...

# School subscription plans - USD (International)
STRIPE_PRICE_SCHOOL_STARTER_USD=price_...
STRIPE_PRICE_SCHOOL_PRO_USD=price_...
STRIPE_PRICE_SCHOOL_ENTERPRISE_USD=price_...
```

**Currency Selection Logic**:
```php
// app/Services/CurrencyService.php
class CurrencyService
{
    // Auto-detect currency based on country/locale
    public function detectCurrency(?string $countryCode = null): string
    {
        $countryCode = $countryCode ?? $this->detectCountry();

        return match ($countryCode) {
            'CH', 'LI' => 'chf',                          // Switzerland, Liechtenstein
            'US', 'CA', 'AU', 'NZ' => 'usd',              // Dollar countries
            default => 'eur',                              // Default to EUR (Europe)
        };
    }

    // Get price for plan in specific currency
    public function getPlanPrice(string $plan, string $currency): array
    {
        $plans = config('lernpfad.billing.plans');
        return $plans[$plan]['prices'][$currency] ?? $plans[$plan]['prices']['eur'];
    }

    // Format price for display
    public function formatPrice(int $amount, string $currency): string
    {
        $symbols = config('lernpfad.billing.currency_symbols');
        $formatted = number_format($amount / 100, 2, '.', "'");

        return match ($currency) {
            'usd' => '$' . $formatted,
            'eur' => '€' . $formatted,
            'chf' => 'CHF ' . $formatted,
            default => $formatted,
        };
    }
}
```

**Pricing Page with Currency Switcher**:
```html
<!-- Currency selector on pricing page -->
<div class="flex justify-center gap-2 mb-8">
    <button wire:click="setCurrency('chf')"
            class="{{ $currency === 'chf' ? 'bg-brand-teal text-white' : 'bg-white' }}
                   px-4 py-2 rounded-brand border">
        🇨🇭 CHF
    </button>
    <button wire:click="setCurrency('eur')"
            class="{{ $currency === 'eur' ? 'bg-brand-teal text-white' : 'bg-white' }}
                   px-4 py-2 rounded-brand border">
        🇪🇺 EUR
    </button>
    <button wire:click="setCurrency('usd')"
            class="{{ $currency === 'usd' ? 'bg-brand-teal text-white' : 'bg-white' }}
                   px-4 py-2 rounded-brand border">
        🇺🇸 USD
    </button>
</div>
```

**Billing Routes**:
```
/billing                → Billing dashboard (current plan, invoices)
/billing/subscribe      → Plan selection
/billing/checkout       → Stripe Checkout
/billing/portal         → Stripe Customer Portal
/pricing                → Public pricing page
```

**Key Livewire Components**:
```
Livewire\Billing\PlanSelector     → Choose subscription plan
Livewire\Billing\CurrentPlan      → Display active subscription
Livewire\Billing\InvoiceHistory   → List past invoices
Livewire\Billing\PaymentMethods   → Manage cards
```

### AI Provider Package (Required)

Use `echolabs/prism` for unified AI provider interface (supports Claude, OpenAI, Gemini, Ollama, etc.):

```bash
composer require echolabs/prism
```

**Why Prism**: Single API to switch between providers, built-in tool/function calling, streaming support, Laravel-native.

```php
// Example usage in AIClientService
use Prism\Prism;
use Prism\Enums\Provider;

$response = Prism::text()
    ->using(Provider::Anthropic, 'claude-sonnet-4-5-20250929')
    ->withSystemPrompt($systemPrompt)
    ->withMessages($messages)
    ->generate();

// Easy provider switching
$response = Prism::text()
    ->using(Provider::OpenAI, 'gpt-4o')
    // ... same interface
```

**Supported providers**: Anthropic (Claude), OpenAI (GPT), Google (Gemini), Mistral, Ollama (local), Groq, xAI

### Livewire Component Organization

Components organized by user role:
```
App\Livewire\Admin\*        → Admin dashboard, user management, AI quota management
App\Livewire\Instructor\*   → Path builder, content management, submission reviews
App\Livewire\Learner\*      → Catalog, learning interface, progress, AI features
App\Livewire\Shared\*       → Reusable components (ProgressBar, Timer, FileUploader, etc.)
```
Views mirror this: `resources/views/livewire/{role}/{feature}/{component}.blade.php`

### Route Structure

```
/admin/*       → Admin routes (role:admin middleware)
/instructor/*  → Instructor routes (role:instructor middleware)
/learn/*       → Learner routes (role:learner middleware)
/learn/ai/*    → AI features (tutor, practice, summaries, flashcards)
```

### Database Schema

**Core entities (23 migrations):**
- Users, Categories, Tags
- LearningPaths → Modules → LearningSteps
- LearningMaterials, Tasks, Assessments → Questions → AnswerOptions
- Enrollments → StepProgress, TaskSubmissions, AssessmentAttempts → QuestionResponses
- Certificates, UserNotes, Bookmarks, PathReviews
- Prerequisites, ModuleDependencies

**AI entities (8 migrations):**
- AIUserQuotas (token/request limits per user)
- AIGeneratedContents (cached AI responses, polymorphic)
- AITutorConversations → AITutorMessages
- AIPracticeSessions → AIPracticeQuestions
- AIUsageLogs (token tracking)
- AIFeedbackReports

**Conventions:**
- UUIDs for primary keys on main entities
- Standard `id` for lookup tables (Category, Tag)
- Soft deletes on: LearningPath, Module, LearningStep, User

### Naming Conventions

| Type | Convention | Example |
|------|------------|---------|
| Model | PascalCase singular | `LearningPath`, `AITutorConversation` |
| Table | snake_case plural | `learning_paths`, `ai_tutor_conversations` |
| Livewire | `{Role}\{Feature}\{Component}` | `Instructor\LearningPaths\Builder` |
| Enum | PascalCase | `UserRole`, `AIContentType` |

## Key Domain Concepts

### Content Hierarchy
```
LearningPath (course)
  └── Module (chapter)
        └── LearningStep (lesson)
              ├── Material (content: text, video, audio, pdf, image, link, interactive)
              ├── Task (submission-based assignment)
              └── Assessment (quiz/exam with questions)
```

### Teams (Organizations)

Laravel Jetstream Teams enabled for multi-tenant organization support:

```
┌─────────────────────────────────────────────────────────────┐
│  Team (Organization)                                        │
│  ├── Owner (Team Admin)                                     │
│  ├── Members with roles:                                    │
│  │   ├── admin      → Manage team, users, settings          │
│  │   ├── instructor → Create paths, review submissions      │
│  │   └── learner    → Enroll, learn, complete paths         │
│  ├── Learning Paths (scoped to team)                        │
│  ├── Enrollments (team members only)                        │
│  └── AI Quotas (per team or per user)                       │
└─────────────────────────────────────────────────────────────┘
```

**Team Features**:
- Users can belong to multiple teams
- Switch between teams via team switcher
- Learning paths are scoped to teams
- Team-level analytics and reporting
- Team-specific AI quota management
- Invite members via email
- Team billing (if applicable)

**Jetstream Installation** (with teams):
```bash
php artisan jetstream:install livewire --teams
```

**Team Roles** (defined in `app/Providers/JetstreamServiceProvider.php`):
```php
Jetstream::role('admin', 'Administrator', [
    'team:manage', 'users:manage', 'paths:manage', 'settings:manage'
])->description('Full team management access');

Jetstream::role('instructor', 'Instructor', [
    'paths:create', 'paths:edit', 'paths:delete', 'submissions:review'
])->description('Create and manage learning content');

Jetstream::role('learner', 'Learner', [
    'paths:view', 'paths:enroll', 'assessments:take', 'tasks:submit'
])->description('Access and complete learning paths');
```

### User Roles (System-wide)
- **Super Admin**: Platform-wide access, manage all teams, system settings
- **Team Admin**: Full team access, user management, analytics, AI quota control
- **Instructor**: Create/manage learning paths, review submissions, view reports
- **Learner**: Browse catalog, enroll, learn, submit tasks, take assessments, earn certificates

### Progress Tracking
- **Enrollment**: Tracks user's overall path progress (status, percent, time spent, points)
- **StepProgress**: Tracks individual step completion (status, time, points)
- **AssessmentAttempt**: Records quiz attempts with score and pass/fail
- **TaskSubmission**: Tracks task submissions and instructor reviews

### AI Features

| Feature | Service | Description |
|---------|---------|-------------|
| Explanations | `AIExplanationService` | Instant explanations for wrong answers |
| Hints | `AIExplanationService` | Progressive hints (levels 1-3) for stuck learners |
| Tutor | `AITutorService` | Conversational Q&A scoped to learning content |
| Practice | `AIPracticeGeneratorService` | Adaptive difficulty practice questions |
| Summaries | `AISummaryService` | AI-generated module recaps |
| Flashcards | `AISummaryService` | Auto-generated study cards |

AI uses Prism package for multi-provider support (Claude, GPT, Gemini, Ollama, etc.) with quota tracking per user.

## Enums Reference

**Core (11):**
`UserRole`, `Difficulty`, `StepType`, `MaterialType`, `TaskType`, `AssessmentType`, `QuestionType`, `EnrollmentStatus`, `StepProgressStatus`, `SubmissionStatus`, `UnlockCondition`

**AI (4):**
`AIContentType`, `AIServiceType`, `AIPracticeDifficulty`, `AIFeedbackType`

See `docs/ENUMS.md` for full definitions with helper methods.

## Configuration

**App config** in `config/lernpfad.php`:
```php
'defaults' => [
    'passing_score' => 70,           // Assessment pass threshold
    'max_assessment_attempts' => 3,  // Retry limit
    'certificate_validity_years' => 2,
],
'materials' => [
    'max_file_size' => 100 * 1024 * 1024,  // 100MB
],
'gamification' => [
    'points' => ['step_completion' => 10, 'assessment_pass' => 50, 'path_completion' => 200],
],
'ai' => [
    'default_provider' => env('PRISM_DEFAULT_PROVIDER', 'anthropic'),
    'providers' => [
        'anthropic' => ['model' => env('PRISM_ANTHROPIC_MODEL', 'claude-sonnet-4-5-20250929')],
        'openai' => ['model' => env('PRISM_OPENAI_MODEL', 'gpt-4o')],
        'gemini' => ['model' => env('PRISM_GEMINI_MODEL', 'gemini-1.5-pro')],
        'ollama' => ['model' => env('PRISM_OLLAMA_MODEL', 'llama3')],
    ],
    'default_monthly_tokens' => env('AI_DEFAULT_MONTHLY_TOKENS', 100000),
    'default_daily_requests' => env('AI_DEFAULT_DAILY_REQUESTS', 100),
    'cache_enabled' => env('AI_CACHE_ENABLED', true),
],
```

**Environment variables** (see `.env.example`):
- `APP_AVAILABLE_LOCALES=de,en,fr`
- `STRIPE_KEY` / `STRIPE_SECRET` / `STRIPE_WEBHOOK_SECRET` - Stripe billing
- `STRIPE_PRICE_*` - Half-yearly subscription plan price IDs
- `PRISM_DEFAULT_PROVIDER` - AI provider: anthropic, openai, gemini, ollama, etc.
- `ANTHROPIC_API_KEY` / `OPENAI_API_KEY` / `GEMINI_API_KEY` - Provider API keys
- `PRISM_*_MODEL` - Model per provider (e.g., `PRISM_ANTHROPIC_MODEL`)
- `AI_DEFAULT_*` - Token/request limits
- `LERNPFAD_*` - App-specific settings

## Services to Implement

**Core services:**
- `LearningPathService` - Path CRUD, publishing, duplication
- `ProgressTrackingService` - Track progress, time, completion
- `AssessmentGradingService` - Grade answers, calculate scores
- `CertificateGeneratorService` - Generate PDFs with DomPDF
- `MediaProcessingService` - Handle file uploads, thumbnails
- `NotificationService` - Email and in-app notifications
- `SubscriptionService` - Stripe billing, plan management, feature gating

**AI services** (in `app/Services/AI/`):
- `AIClientService` - Prism wrapper for multi-provider AI (Claude, GPT, Gemini, etc.)
- `AIContextBuilder` - Build context from user progress
- `AIUsageService` - Quota tracking & rate limiting
- `AIExplanationService` - Wrong answer explanations & hints
- `AITutorService` - Conversational AI tutor
- `AIPracticeGeneratorService` - Generate practice questions
- `AISummaryService` - Module summaries & flashcards

## Implementation Phases

1. **Foundation**: Auth, Jetstream Teams, roles, migrations, models, layouts
2. **Billing & Subscriptions**: Stripe integration, plans, checkout, customer portal
3. **Learning Path Builder**: Instructor CRUD, modules, steps, materials
4. **Learner Interface**: Catalog, enrollment, content viewers, progress
5. **Assessment System**: Quiz builder, question types, grading, results
6. **Tasks & Certificates**: Submissions, reviews, PDF certificates
7. **Analytics & Polish**: Dashboards, reports, search, performance
8. **AI Features**: Tutor, practice, explanations, summaries (after core is complete)

See `TASKS.md` for detailed implementation checklist.

## UI Specification

### Page Structure

**Public Pages** (unauthenticated):
```
/                   → Landing page (hero, features, testimonials, CTA)
/about              → About us (mission, team, story)
/features           → Feature overview (see Features Page Spec below)
/pricing            → School subscription plans (1-month trial, then half-yearly)
/contact            → Contact form
/blog               → Blog/articles listing
/blog/{slug}        → Blog article detail
/legal/privacy      → Privacy policy
/legal/terms        → Terms of service
/legal/imprint      → Imprint (Impressum - required for DE)
/auth/login         → Login
/auth/register      → Registration
/auth/password/*    → Password reset flow
```

**Authenticated Pages** (by role):
```
/dashboard          → Role-based redirect

# Learner
/learn/catalog                    → Browse learning paths
/learn/catalog/{slug}             → Path detail + enroll
/learn/my-paths                   → My enrollments
/learn/path/{id}/module/{id}      → Learning interface
/learn/ai/*                       → AI tutor, practice, etc.
/learn/certificates               → My certificates
/learn/profile                    → Profile settings
/billing                          → Subscription management
/billing/portal                   → Stripe Customer Portal

# Instructor
/instructor/dashboard             → Overview + stats
/instructor/paths                 → My learning paths
/instructor/paths/create          → Create new path
/instructor/paths/{id}/edit       → Path builder
/instructor/submissions           → Pending reviews
/instructor/reports               → Analytics

# Admin
/admin/dashboard                  → System overview
/admin/users                      → User management
/admin/paths                      → All learning paths
/admin/categories                 → Category management
/admin/ai/*                       → AI usage, quotas
/admin/settings                   → System settings
```

### Layout Templates

**1. Public Layout** (`layouts/public.blade.php`):
```
┌─────────────────────────────────────────────────────────┐
│  Logo          Nav: Features | Pricing | About | Blog   │  ← Sticky header
│                                        [Login] [Sign Up]│
├─────────────────────────────────────────────────────────┤
│                                                         │
│                    Page Content                         │
│                                                         │
├─────────────────────────────────────────────────────────┤
│  Footer: Links | Social | Legal | Language Switcher    │
│  © 2025 LearningPilot                                   │
└─────────────────────────────────────────────────────────┘
```

**2. App Layout** (`layouts/app.blade.php`):
```
┌──────────────────────────────────────────────────────────┐
│  Logo    Search...         [Notifications] [User Menu]   │  ← Sticky header
├─────────┬────────────────────────────────────────────────┤
│         │                                                │
│  Side   │              Main Content                      │
│  Nav    │              (Livewire)                        │
│         │                                                │
│  [AI]   │                                                │  ← Floating AI button
└─────────┴────────────────────────────────────────────────┘
```

**3. Learning Layout** (`layouts/learning.blade.php`):
```
┌─────────────────────────────────────────────────────────┐
│  ← Back    Path Title           Progress: ████░░ 60%    │
├──────────────────────────────────────────────────────────┤
│                                                          │
│                   Content Viewer                         │
│                   (Video/Text/PDF)                       │
│                                                          │
├──────────────────────────────────────────────────────────┤
│  [← Prev]    Step 3 of 12    [Mark Complete] [Next →]   │
└──────────────────────────────────────────────────────────┘
```

### Component Library

**Buttons**:
```html
<!-- Primary (Teal) -->
<button class="bg-brand-teal hover:bg-brand-teal/90 text-white font-semibold
               rounded-brand px-6 py-2.5 transition-colors">
    Action
</button>

<!-- Secondary (Outlined) -->
<button class="border-2 border-brand-indigo text-brand-indigo hover:bg-brand-indigo
               hover:text-white font-semibold rounded-brand px-6 py-2.5 transition-colors">
    Secondary
</button>

<!-- Ghost -->
<button class="text-brand-gray-dark hover:text-brand-indigo hover:bg-brand-gray-soft
               rounded-brand px-4 py-2 transition-colors">
    Ghost
</button>

<!-- Danger -->
<button class="bg-red-600 hover:bg-red-700 text-white font-semibold
               rounded-brand px-6 py-2.5 transition-colors">
    Delete
</button>
```

**Cards**:
```html
<!-- Standard Card -->
<div class="bg-white rounded-brand border border-slate-100 shadow-brand-subtle p-6">
    <h3 class="font-semibold text-brand-indigo text-lg">Title</h3>
    <p class="text-brand-gray-dark mt-2">Content</p>
</div>

<!-- Learning Path Card (Catalog) -->
<div class="bg-white rounded-brand border border-slate-100 shadow-brand-subtle
            overflow-hidden hover:shadow-md transition-shadow">
    <img src="thumbnail.jpg" class="w-full h-48 object-cover" />
    <div class="p-4">
        <span class="text-xs font-medium text-brand-teal uppercase">Beginner</span>
        <h3 class="font-semibold text-brand-indigo mt-1">Path Title</h3>
        <p class="text-brand-gray-dark text-sm mt-2 line-clamp-2">Description...</p>
        <div class="flex items-center justify-between mt-4">
            <span class="text-sm text-brand-gray-dark">12 Modules</span>
            <span class="text-sm text-brand-gray-dark">★ 4.8</span>
        </div>
    </div>
</div>
```

**Forms**:
```html
<!-- Input -->
<div>
    <label class="block text-sm font-medium text-brand-indigo mb-1">Email</label>
    <input type="email"
           class="w-full rounded-brand border border-slate-200 px-4 py-2.5
                  focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/20
                  placeholder:text-slate-400"
           placeholder="you@example.com" />
    <p class="text-red-600 text-sm mt-1">Error message</p>
</div>

<!-- Select -->
<select class="w-full rounded-brand border border-slate-200 px-4 py-2.5
               focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/20">
    <option>Select option...</option>
</select>

<!-- Textarea -->
<textarea class="w-full rounded-brand border border-slate-200 px-4 py-2.5 min-h-32
                 focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/20"
          placeholder="Your message..."></textarea>
```

**Modals**:
```html
<div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-brand shadow-xl max-w-lg w-full mx-4 max-h-[90vh] overflow-auto">
        <div class="p-6 border-b border-slate-100">
            <h2 class="font-semibold text-brand-indigo text-xl">Modal Title</h2>
        </div>
        <div class="p-6">
            <!-- Content -->
        </div>
        <div class="p-6 border-t border-slate-100 flex justify-end gap-3">
            <button class="...ghost">Cancel</button>
            <button class="...primary">Confirm</button>
        </div>
    </div>
</div>
```

**Alerts/Notifications**:
```html
<!-- Success -->
<div class="bg-green-50 border border-green-200 text-green-800 rounded-brand p-4 flex gap-3">
    <svg>...</svg>
    <p>Success message</p>
</div>

<!-- Error -->
<div class="bg-red-50 border border-red-200 text-red-800 rounded-brand p-4 flex gap-3">
    <svg>...</svg>
    <p>Error message</p>
</div>

<!-- Info -->
<div class="bg-blue-50 border border-blue-200 text-blue-800 rounded-brand p-4 flex gap-3">
    <svg>...</svg>
    <p>Info message</p>
</div>
```

**Progress Indicators**:
```html
<!-- Progress Bar -->
<div class="w-full bg-slate-100 rounded-full h-2">
    <div class="bg-brand-teal h-2 rounded-full" style="width: 60%"></div>
</div>

<!-- Step Progress -->
<div class="flex items-center gap-2">
    <div class="w-8 h-8 rounded-full bg-brand-teal text-white flex items-center justify-center text-sm font-semibold">✓</div>
    <div class="flex-1 h-1 bg-brand-teal"></div>
    <div class="w-8 h-8 rounded-full bg-brand-teal text-white flex items-center justify-center text-sm font-semibold">2</div>
    <div class="flex-1 h-1 bg-slate-200"></div>
    <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center text-sm font-semibold">3</div>
</div>
```

**Navigation**:
```html
<!-- Sidebar Nav Item -->
<a href="#" class="flex items-center gap-3 px-4 py-2.5 rounded-brand
                   text-brand-gray-dark hover:bg-brand-gray-soft hover:text-brand-indigo
                   transition-colors">
    <svg class="w-5 h-5">...</svg>
    <span>Dashboard</span>
</a>

<!-- Active state -->
<a href="#" class="flex items-center gap-3 px-4 py-2.5 rounded-brand
                   bg-brand-teal/10 text-brand-teal font-medium">
    <svg class="w-5 h-5">...</svg>
    <span>Dashboard</span>
</a>

<!-- Tabs -->
<div class="flex border-b border-slate-200">
    <button class="px-4 py-2.5 border-b-2 border-brand-teal text-brand-teal font-medium">Tab 1</button>
    <button class="px-4 py-2.5 border-b-2 border-transparent text-brand-gray-dark hover:text-brand-indigo">Tab 2</button>
</div>
```

**Badges/Tags**:
```html
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
             bg-brand-teal/10 text-brand-teal">Beginner</span>
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
             bg-blue-100 text-blue-800">In Progress</span>
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
             bg-green-100 text-green-800">Completed</span>
```

### Landing Page Sections

```
1. Hero Section
   ┌─────────────────────────────────────────────────────────┐
   │  [Visual/Illustration]          Headline               │
   │                                  Subheadline            │
   │                                  [Get Started] [Demo]   │
   └─────────────────────────────────────────────────────────┘

2. Trusted By (Logo bar)
   ┌─────────────────────────────────────────────────────────┐
   │  Trusted by: [Logo] [Logo] [Logo] [Logo] [Logo]         │
   └─────────────────────────────────────────────────────────┘

3. Features Grid (3 columns)
   ┌─────────────────────────────────────────────────────────┐
   │  [Icon]          [Icon]          [Icon]                 │
   │  Feature 1       Feature 2       Feature 3              │
   │  Description     Description     Description            │
   └─────────────────────────────────────────────────────────┘

4. How It Works (Steps)
   ┌─────────────────────────────────────────────────────────┐
   │  1. Create      2. Learn       3. Achieve               │
   │  ────●──────────────●──────────────●────                │
   │  Description    Description    Description              │
   └─────────────────────────────────────────────────────────┘

5. AI Feature Highlight
   ┌─────────────────────────────────────────────────────────┐
   │  🤖 Powered by AI                                       │
   │  [Screenshot of AI Tutor]    • Instant explanations     │
   │                              • Adaptive practice         │
   │                              • Personal tutor            │
   └─────────────────────────────────────────────────────────┘

6. Testimonials
   ┌─────────────────────────────────────────────────────────┐
   │  "Quote..."       "Quote..."       "Quote..."           │
   │  - Name, Role     - Name, Role     - Name, Role         │
   └─────────────────────────────────────────────────────────┘

7. CTA Section
   ┌─────────────────────────────────────────────────────────┐
   │           Ready to start learning?                      │
   │           [Create Free Account]                         │
   └─────────────────────────────────────────────────────────┘

8. Footer
   ┌─────────────────────────────────────────────────────────┐
   │  Logo           Product    Company    Legal    Social   │
   │  Tagline        Features   About      Privacy  Twitter  │
   │                 Pricing    Contact    Terms    LinkedIn │
   │                 Blog       Careers    Imprint  GitHub   │
   │  ─────────────────────────────────────────────────────  │
   │  © 2025 LearningPilot  [DE|EN|FR]                       │
   └─────────────────────────────────────────────────────────┘
```

### Features Page (`/features`)

Comprehensive feature overview page with all platform capabilities:

```
┌─────────────────────────────────────────────────────────────┐
│  HERO: "Everything you need to create & deliver learning"   │
│  Subline: "Powerful features for instructors and learners"  │
└─────────────────────────────────────────────────────────────┘

SECTION 1: Learning Path Builder
┌─────────────────────────────────────────────────────────────┐
│  [Screenshot]     • Drag-and-drop course builder            │
│                   • Modular structure (Paths → Modules →    │
│                     Steps)                                   │
│                   • Multi-format content support             │
│                   • Version control & publishing workflow    │
└─────────────────────────────────────────────────────────────┘

SECTION 2: Content Types
┌─────────────────────────────────────────────────────────────┐
│  [Icon] Video     [Icon] PDF       [Icon] Text              │
│  [Icon] Audio     [Icon] Images    [Icon] Interactive       │
│  [Icon] External Links             [Icon] Embedded Content  │
│                                                              │
│  "Support for all major content formats with built-in       │
│   viewers and progress tracking"                             │
└─────────────────────────────────────────────────────────────┘

SECTION 3: Assessment System
┌─────────────────────────────────────────────────────────────┐
│  [Screenshot]     5 Question Types:                          │
│                   ✓ Single Choice    ✓ Multiple Choice       │
│                   ✓ True/False       ✓ Free Text             │
│                   ✓ Matching                                 │
│                                                              │
│                   Features:                                  │
│                   • Auto-grading     • Time limits           │
│                   • Attempt tracking • Instant results       │
│                   • Explanations     • Question banks        │
└─────────────────────────────────────────────────────────────┘

SECTION 4: Task & Submission System
┌─────────────────────────────────────────────────────────────┐
│  [Screenshot]     • File upload submissions                  │
│                   • Text-based submissions                   │
│                   • Instructor review workflow               │
│                   • Feedback & scoring                       │
│                   • Revision requests                        │
└─────────────────────────────────────────────────────────────┘

SECTION 5: Progress Tracking & Gamification
┌─────────────────────────────────────────────────────────────┐
│  [Visual]         • Real-time progress tracking              │
│                   • Time spent analytics                     │
│                   • Points & achievements                    │
│                   • Completion certificates (PDF)            │
│                   • Learning streaks                         │
└─────────────────────────────────────────────────────────────┘

SECTION 6: AI-Powered Learning (Highlight Section)
┌─────────────────────────────────────────────────────────────┐
│  🤖 INTELLIGENT LEARNING ASSISTANT                          │
│  ═══════════════════════════════════════════════════════════│
│                                                              │
│  [AI Tutor Screenshot]                                       │
│                                                              │
│  ┌──────────────┐ ┌──────────────┐ ┌──────────────┐         │
│  │ AI Tutor     │ │ Explanations │ │ Practice Gen │         │
│  │ ──────────── │ │ ──────────── │ │ ──────────── │         │
│  │ Chat with an │ │ Instant help │ │ AI generates │         │
│  │ AI tutor     │ │ when you get │ │ personalized │         │
│  │ about your   │ │ answers      │ │ practice     │         │
│  │ learning     │ │ wrong        │ │ questions    │         │
│  │ content      │ │              │ │              │         │
│  └──────────────┘ └──────────────┘ └──────────────┘         │
│                                                              │
│  ┌──────────────┐ ┌──────────────┐ ┌──────────────┐         │
│  │ Smart Hints  │ │ Summaries    │ │ Flashcards   │         │
│  │ ──────────── │ │ ──────────── │ │ ──────────── │         │
│  │ Progressive  │ │ AI-generated │ │ Auto-created │         │
│  │ hints when   │ │ module       │ │ study cards  │         │
│  │ you're stuck │ │ recaps       │ │ from content │         │
│  └──────────────┘ └──────────────┘ └──────────────┘         │
│                                                              │
│  SUPPORTED AI PROVIDERS                                      │
│  ───────────────────────────────────────────────────────────│
│  Choose your preferred AI provider:                          │
│                                                              │
│  [Claude Logo]    [GPT Logo]      [Gemini Logo]              │
│  Anthropic        OpenAI          Google                     │
│  Claude           GPT-4o          Gemini 1.5 Pro             │
│  ───────────      ───────         ───────────────            │
│  Recommended      Popular         Alternative                │
│                                                              │
│  [Mistral Logo]   [Ollama Logo]   [More...]                  │
│  Mistral AI       Ollama          Groq, xAI                  │
│  Mistral Large    Local/Private   & more                     │
│  ───────────      ───────────────                            │
│  European         Self-hosted                                │
│                                                              │
│  "Flexible AI integration - use cloud providers or run       │
│   locally with Ollama for complete data privacy"             │
└─────────────────────────────────────────────────────────────┘

SECTION 7: Multi-Language Support
┌─────────────────────────────────────────────────────────────┐
│  🌍 Available in:  [DE] Deutsch                              │
│                    [EN] English                              │
│                    [FR] Français                             │
│                                                              │
│  "Full localization for content and interface"               │
└─────────────────────────────────────────────────────────────┘

SECTION 8: Analytics & Reporting
┌─────────────────────────────────────────────────────────────┐
│  [Dashboard Screenshot]                                      │
│                                                              │
│  For Instructors:           For Admins:                      │
│  • Enrollment stats         • User analytics                 │
│  • Completion rates         • System-wide stats              │
│  • Drop-off analysis        • AI usage monitoring            │
│  • Learner progress         • Cost tracking                  │
└─────────────────────────────────────────────────────────────┘

SECTION 9: Security & Compliance
┌─────────────────────────────────────────────────────────────┐
│  ✓ GDPR compliant           ✓ Role-based access              │
│  ✓ Data encryption          ✓ Audit logging                  │
│  ✓ Self-hosting option      ✓ SSO ready                      │
└─────────────────────────────────────────────────────────────┘

SECTION 10: CTA
┌─────────────────────────────────────────────────────────────┐
│           Ready to transform your learning?                  │
│           [Start Free Trial]  [Contact Sales]                │
└─────────────────────────────────────────────────────────────┘
```

**Key Feature Cards Data** (for `features` table or config):
```php
// config/lernpfad.php or database seeder
'features' => [
    'core' => [
        ['icon' => 'academic-cap', 'title' => 'Learning Paths', 'description' => 'Structured courses with modules and steps'],
        ['icon' => 'video-camera', 'title' => 'Multi-format Content', 'description' => 'Video, PDF, audio, text, and more'],
        ['icon' => 'clipboard-check', 'title' => 'Assessments', 'description' => '5 question types with auto-grading'],
        ['icon' => 'document-arrow-up', 'title' => 'Task Submissions', 'description' => 'File uploads with instructor review'],
        ['icon' => 'chart-bar', 'title' => 'Progress Tracking', 'description' => 'Time, completion, and points'],
        ['icon' => 'trophy', 'title' => 'Certificates', 'description' => 'Auto-generated PDF certificates'],
    ],
    'ai' => [
        ['icon' => 'chat-bubble-left-right', 'title' => 'AI Tutor', 'description' => 'Conversational learning assistant'],
        ['icon' => 'light-bulb', 'title' => 'Smart Explanations', 'description' => 'Instant help for wrong answers'],
        ['icon' => 'sparkles', 'title' => 'Practice Generator', 'description' => 'AI-created practice questions'],
        ['icon' => 'question-mark-circle', 'title' => 'Progressive Hints', 'description' => 'Step-by-step assistance'],
        ['icon' => 'document-text', 'title' => 'Summaries', 'description' => 'AI-generated module recaps'],
        ['icon' => 'rectangle-stack', 'title' => 'Flashcards', 'description' => 'Auto-created study cards'],
    ],
    'ai_providers' => [
        ['name' => 'Anthropic Claude', 'logo' => 'claude.svg', 'status' => 'recommended', 'models' => ['claude-sonnet-4-5-20250929', 'claude-haiku']],
        ['name' => 'OpenAI GPT', 'logo' => 'openai.svg', 'status' => 'supported', 'models' => ['gpt-4o', 'gpt-4o-mini']],
        ['name' => 'Google Gemini', 'logo' => 'gemini.svg', 'status' => 'supported', 'models' => ['gemini-1.5-pro', 'gemini-1.5-flash']],
        ['name' => 'Mistral AI', 'logo' => 'mistral.svg', 'status' => 'supported', 'models' => ['mistral-large', 'mistral-medium']],
        ['name' => 'Ollama', 'logo' => 'ollama.svg', 'status' => 'supported', 'models' => ['llama3', 'mixtral'], 'note' => 'Self-hosted'],
        ['name' => 'Groq', 'logo' => 'groq.svg', 'status' => 'supported', 'models' => ['llama-3.1-70b']],
        ['name' => 'xAI', 'logo' => 'xai.svg', 'status' => 'supported', 'models' => ['grok-2']],
    ],
],
```

### Responsive Breakpoints

```css
/* Tailwind defaults */
sm: 640px   /* Mobile landscape */
md: 768px   /* Tablet */
lg: 1024px  /* Desktop */
xl: 1280px  /* Large desktop */
2xl: 1536px /* Extra large */
```

**Mobile adaptations**:
- Sidebar → Bottom navigation or hamburger menu
- Multi-column grids → Single column stack
- Side-by-side layouts → Stacked layouts
- Reduce padding/margins by 50%

### Blade Components to Create

```
resources/views/components/
├── layouts/
│   ├── public.blade.php
│   ├── app.blade.php
│   └── learning.blade.php
├── ui/
│   ├── button.blade.php
│   ├── card.blade.php
│   ├── input.blade.php
│   ├── select.blade.php
│   ├── textarea.blade.php
│   ├── modal.blade.php
│   ├── alert.blade.php
│   ├── badge.blade.php
│   ├── progress-bar.blade.php
│   ├── avatar.blade.php
│   └── dropdown.blade.php
├── navigation/
│   ├── public-header.blade.php
│   ├── app-header.blade.php
│   ├── sidebar.blade.php
│   ├── sidebar-item.blade.php
│   └── footer.blade.php
├── landing/
│   ├── hero.blade.php
│   ├── features.blade.php
│   ├── testimonials.blade.php
│   └── cta.blade.php
└── learning/
    ├── path-card.blade.php
    ├── module-list.blade.php
    ├── step-navigation.blade.php
    └── progress-sidebar.blade.php
```

## Design System

### Brand Colors

| Color | Hex | Usage | Utility |
|-------|-----|-------|---------|
| Indigo | `#1F2A44` | Primary - Trust & Stability | `text-brand-indigo`, `bg-brand-indigo` |
| Teal | `#2EC4B6` | Secondary - Future & AI | `text-brand-teal`, `bg-brand-teal` |
| Soft Gray | `#F4F6F8` | Backgrounds, dividers | `bg-brand-gray-soft` |
| Dark Gray | `#5B6475` | Secondary text | `text-brand-gray-dark` |
| White | `#FFFFFF` | Cards, backgrounds | `bg-brand-white` |

### UI Components

| Element | Specification | Classes |
|---------|---------------|---------|
| Headers | SemiBold, Indigo | `font-semibold text-brand-indigo` |
| Body Text | Regular, Dark Gray | `font-normal text-brand-gray-dark` |
| Action Buttons | Teal bg, white text, rounded | `bg-brand-teal text-white rounded-brand px-6 py-2` |
| UI Cards | Soft gray bg, 10px radius | `bg-brand-gray-soft rounded-brand border border-slate-100` |
| Shadows | Minimalist institutional | `shadow-brand-subtle` |

### Spacing System

Use 8-pt grid: `gap-8`, `p-4`, `m-2`, `space-y-4`

### Tailwind Config

```js
// tailwind.config.js
module.exports = {
  theme: {
    extend: {
      colors: {
        brand: {
          indigo: '#1F2A44',
          teal: '#2EC4B6',
          white: '#FFFFFF',
          gray: {
            soft: '#F4F6F8',
            dark: '#5B6475',
          }
        }
      },
      fontFamily: {
        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
        heading: ['Inter', 'sans-serif'],
      },
      borderRadius: {
        'brand': '10px',
      },
      boxShadow: {
        'brand-subtle': '0 2px 4px rgba(31, 42, 68, 0.05)',
      }
    },
  },
}
```

**Font**: Import Inter from Google Fonts in `resources/views/layouts/app.blade.php`

## CI/CD & Deployment

### GitHub Actions Workflow

Create `.github/workflows/ci.yml`:

```yaml
name: CI

on:
  push:
    branches: [main, develop]
  pull_request:
    branches: [main]

jobs:
  test:
    runs-on: ubuntu-latest

    services:
      postgres:
        image: postgres:16
        env:
          POSTGRES_PASSWORD: password
          POSTGRES_DB: testing
        ports:
          - 5432:5432
        options: >-
          --health-cmd pg_isready
          --health-interval 10s
          --health-timeout 5s
          --health-retries 5

    steps:
      - uses: actions/checkout@v4

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'
          extensions: pdo_pgsql, pgsql, gd, zip
          coverage: xdebug

      - name: Install Composer dependencies
        run: composer install --prefer-dist --no-progress

      - name: Install NPM dependencies
        run: npm ci

      - name: Build assets
        run: npm run build

      - name: Copy .env
        run: cp .env.example .env

      - name: Generate key
        run: php artisan key:generate

      - name: Run Pint (code style)
        run: ./vendor/bin/pint --test

      - name: Run PHPStan (static analysis)
        run: ./vendor/bin/phpstan analyse

      - name: Run tests
        run: php artisan test --coverage-clover coverage.xml
        env:
          DB_CONNECTION: pgsql
          DB_HOST: 127.0.0.1
          DB_PORT: 5432
          DB_DATABASE: testing
          DB_USERNAME: postgres
          DB_PASSWORD: password

      - name: Upload coverage
        uses: codecov/codecov-action@v3
        with:
          file: coverage.xml

  deploy:
    needs: test
    if: github.ref == 'refs/heads/main'
    runs-on: ubuntu-latest

    steps:
      - name: Deploy to Laravel Forge
        uses: jbrooksuk/laravel-forge-action@v1
        with:
          trigger_url: ${{ secrets.FORGE_DEPLOY_WEBHOOK }}
```

### Laravel Forge Setup

**Server Requirements**:
- Ubuntu 22.04+
- PHP 8.4 with extensions: pgsql, gd, zip, redis
- PostgreSQL 16+
- Redis (optional)
- Meilisearch (optional)
- Node.js 20+

**Forge Configuration**:
1. Create server with PHP 8.4, PostgreSQL, Redis
2. Create site with Git repository
3. Set environment variables from `.env.example`
4. Configure deploy script:

```bash
cd /home/forge/learningpilot.com
git pull origin main
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart
```

5. Set up SSL (Let's Encrypt)
6. Configure queue worker (Horizon or Supervisor)
7. Set up scheduled tasks: `php artisan schedule:run`

**GitHub Secrets Required**:
- `FORGE_DEPLOY_WEBHOOK` - Forge deployment trigger URL

### Branch Strategy

| Branch | Purpose | Deploys to |
|--------|---------|------------|
| `main` | Production-ready | Production (Forge) |
| `develop` | Integration | Staging (optional) |
| `feature/*` | New features | - |
| `fix/*` | Bug fixes | - |

## POC Scope (MVP)

### In Scope (Phase 1)
- School registration with 1-month free trial
- User registration, login, email verification
- Team (school) creation and member management (students + instructors)
- **Student onboarding**: Invitation links + bulk CSV import
- **School admin dashboard**: Students, instructors, usage, billing
- Learning path builder (modules, steps, materials)
- Content types: Text, Video (self-hosted S3 + YouTube/Loom embeds), PDF
- Assessments: Single choice, Multiple choice, True/False, Text
- Learner enrollment and progress tracking
- School billing: 1-month trial → Starter/Pro/Enterprise plans (half-yearly)
- File storage on DigitalOcean Spaces
- Certificate generation (PDF)
- Platform admin dashboard with stats
- German language only
- Single AI provider (Claude) for explanations only

### Deferred (Phase 2+)
- AI Tutor, Practice Generator, Summaries, Flashcards
- Multi-provider AI (OpenAI, Gemini, Ollama)
- English and French translations
- Matching question type
- Advanced gamification (badges, streaks, leaderboards)
- Class/group management within schools
- Parent access / progress reports
- LTI integration with other LMS
- Offline mode / PWA

---

## Student Onboarding (B2B Education)

### How Students Join a School

**Option 1: Invitation Link** (Primary)
```
1. School admin generates invite link with role preset
2. Link format: /join/{school_slug}?token={invite_token}&role=student
3. Student clicks link → Registration form (name, email, password)
4. Auto-joins school team with 'learner' role
5. Welcome email sent
```

**Option 2: Bulk CSV Import** (For large schools)
```csv
# students.csv
first_name,last_name,email,class
Max,Mustermann,max@schule.de,10A
Anna,Schmidt,anna@schule.de,10A
...
```

```php
// app/Actions/School/ImportStudentsAction.php
- Parse CSV
- Validate emails (unique, valid format)
- Create User accounts with temporary passwords
- Send invitation emails with password reset link
- Return import summary (created, skipped, errors)
```

**Option 3: Email Domain Restriction** (Optional)
```php
// School can restrict to specific email domains
'allowed_domains' => ['schule-beispiel.de', 'students.schule-beispiel.de']
```

### Student Onboarding UI Flow

```
┌─────────────────────────────────────────────────────────────┐
│  SCHOOL ADMIN: Invite Students                              │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  📧 Invite by Email        📄 Bulk Import (CSV)            │
│  ─────────────────        ──────────────────               │
│  [email@example.com]      [Upload CSV] [Download Template] │
│  Role: [Student ▼]                                         │
│  [Send Invitation]        Preview: 45 students found       │
│                           [Import All]                     │
│                                                             │
│  🔗 Share Invite Link                                       │
│  ─────────────────────                                      │
│  https://app.learningpilot.com/join/schule-xyz?token=abc   │
│  [Copy Link] [Regenerate]                                   │
│                                                             │
│  Pending Invitations (3)                                    │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ anna@example.com    Student    Sent 2 days ago [x]  │   │
│  │ max@example.com     Student    Sent 1 day ago  [x]  │   │
│  └─────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
```

---

## School Admin Dashboard

School admins need a dedicated dashboard (different from platform admin):

### Routes
```
/school/dashboard         → Overview (students, usage, quick actions)
/school/students          → Student list, invite, remove, view progress
/school/instructors       → Instructor management
/school/paths             → Learning paths in this school
/school/analytics         → School-wide progress reports
/school/usage             → Storage used, AI requests, seat count
/school/billing           → Current plan, invoices, upgrade
/school/settings          → School profile, branding, invite settings
```

### School Dashboard Wireframe
```
┌─────────────────────────────────────────────────────────────┐
│  🏫 Schule Beispiel              [Trial: 14 days left]     │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐       │
│  │ 45/50    │ │ 3/5      │ │ 12       │ │ 68%      │       │
│  │ Students │ │ Teachers │ │ Paths    │ │ Avg.Prog │       │
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘       │
│                                                             │
│  Quick Actions                                              │
│  [+ Invite Students] [+ Add Instructor] [+ Create Path]    │
│                                                             │
│  Recent Activity                    Storage Usage           │
│  ─────────────────                  ─────────────           │
│  • Max joined the school            ████████░░ 18/25 GB    │
│  • Anna completed "Math 101"                                │
│  • New path "Biology" created       AI Requests Today       │
│  • 5 students invited               ██████░░░░ 62/100      │
│                                                             │
│  Student Progress Overview          [View Full Report →]   │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ Path            │ Enrolled │ Completed │ Avg Score │   │
│  │ Math 101        │ 42       │ 28 (67%)  │ 82%       │   │
│  │ Biology Basics  │ 38       │ 12 (32%)  │ 75%       │   │
│  └─────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
```

### Key Livewire Components
```
Livewire\School\Dashboard           → School overview
Livewire\School\StudentList         → List, search, filter students
Livewire\School\StudentInvite       → Invite modal (email, CSV, link)
Livewire\School\InstructorList      → Manage instructors
Livewire\School\UsageStats          → Storage, AI, seats
Livewire\School\ProgressReport      → School-wide analytics
Livewire\School\BillingDashboard    → Plan, invoices, upgrade
Livewire\School\Settings            → School configuration
```

---

## Data Privacy for Minors

Since target users are students (potentially minors), add these safeguards:

### Age Verification
```php
// User registration/profile
'date_of_birth' => 'required|date|before:today',

// Check if minor (under 16 in EU)
public function isMinor(): bool
{
    return $this->date_of_birth->age < 16;
}
```

### Parental Consent (if minor)
```php
// migrations: add to users table
$table->boolean('parental_consent_given')->default(false);
$table->timestamp('parental_consent_at')->nullable();
$table->string('parent_email')->nullable();

// Flow for minors:
1. Student registers with DOB showing they're under 16
2. System requires parent email
3. Consent email sent to parent with approval link
4. Account limited until consent received
5. Store consent timestamp for compliance
```

### Data Handling
```php
// config/lernpfad.php
'privacy' => [
    'minor_age_threshold' => 16,  // EU GDPR
    'require_parental_consent' => true,
    'data_retention_days' => 365 * 3,  // 3 years after last activity
    'auto_delete_inactive_students' => true,
],
```
- Discussion forums / Comments
- Mobile app / PWA

---

## Missing Components (Add Before Launch)

### Authentication
```php
// config/lernpfad.php
'auth' => [
    'social_providers' => ['google', 'microsoft'],  // Laravel Socialite
    'require_email_verification' => true,
    'enable_2fa' => true,  // Jetstream built-in
    'password_min_length' => 8,
],
```

### Notifications
```php
// Notification triggers
- welcome_email          → On registration
- email_verified         → On email verification
- team_invitation        → When invited to team
- enrollment_confirmed   → When enrolled in path
- path_completed         → When completing a path
- certificate_issued     → When certificate generated
- submission_reviewed    → When instructor reviews task
- assessment_passed      → When passing an assessment
- subscription_renewed   → Billing reminder/confirmation
```

### Email Templates (Create in `resources/views/emails/`)
```
emails/
├── welcome.blade.php
├── verify-email.blade.php
├── team-invitation.blade.php
├── path-completed.blade.php
├── certificate-issued.blade.php
├── submission-reviewed.blade.php
└── subscription-reminder.blade.php
```

### Error Tracking
```bash
composer require sentry/sentry-laravel
```
```
# .env
SENTRY_LARAVEL_DSN=https://...@sentry.io/...
```

### Health Checks
```php
// routes/web.php
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'database' => DB::connection()->getPdo() ? 'connected' : 'error',
        'cache' => Cache::store()->get('health') !== null || Cache::store()->put('health', true, 10),
        'queue' => Queue::size('default') >= 0,
    ]);
});
```

### GDPR Compliance
```php
// User model methods
public function exportPersonalData(): array  // Data portability
public function anonymize(): void            // Right to erasure
public function getConsentHistory(): Collection

// Required pages
/legal/privacy          → Privacy policy
/legal/terms            → Terms of service
/legal/cookies          → Cookie policy
/profile/data           → Download my data
/profile/delete         → Delete my account
```

### Cookie Consent
```bash
composer require spatie/laravel-cookie-consent
```

### Demo Seeder
```php
// database/seeders/DemoSeeder.php
- Demo team: "Demo Company"
- Demo instructor: demo-instructor@learningpilot.com / demo1234
- Demo learner: demo-learner@learningpilot.com / demo1234
- Sample learning path: "Getting Started with LearningPilot"
  - 3 modules, 9 steps
  - Mix of text, video (YouTube embeds), PDF
  - 2 assessments with sample questions
  - 1 task submission example
```

---

## Documentation Reference

- `docs/MIGRATIONS.md` - All 23 core migration schemas
- `docs/AI_MIGRATIONS.md` - All 8 AI migration schemas
- `docs/ENUMS.md` - All 15 enum definitions with helper methods
- `docs/AI_CONTENT_FEATURE.md` - Complete AI feature specification
- `docs/AI_TASKS.md` - AI implementation checklist
- `TASKS.md` - Core implementation checklist
