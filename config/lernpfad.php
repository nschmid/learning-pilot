<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Settings
    |--------------------------------------------------------------------------
    |
    | These are the default settings for assessments, certificates, and other
    | features of the learning platform.
    |
    */

    'defaults' => [
        'passing_score' => 70,
        'max_assessment_attempts' => 3,
        'certificate_validity_years' => 2,
        'default_points_per_step' => 10,
        'default_time_limit_minutes' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Material Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for file uploads and allowed media types.
    |
    */

    'materials' => [
        'max_file_size' => 100 * 1024 * 1024, // 100MB
        'allowed_extensions' => [
            'video' => ['mp4', 'webm', 'mov'],
            'audio' => ['mp3', 'wav', 'ogg', 'm4a'],
            'document' => ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx'],
            'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'],
        ],
        'thumbnail' => [
            'width' => 400,
            'height' => 225,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Video Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for video hosting and embeds.
    |
    */

    'video' => [
        'allowed_sources' => ['upload', 'youtube', 'vimeo', 'loom'],
        'max_upload_size' => 500 * 1024 * 1024, // 500MB
        'supported_formats' => ['mp4', 'webm', 'mov'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Gamification Settings
    |--------------------------------------------------------------------------
    |
    | Points and rewards configuration for learner engagement.
    |
    */

    'gamification' => [
        'enabled' => true,
        'points' => [
            'step_completion' => 10,
            'assessment_pass' => 50,
            'assessment_perfect' => 100,
            'path_completion' => 200,
            'first_login_of_day' => 5,
            'streak_bonus' => 25,
        ],
        'streak' => [
            'enabled' => true,
            'days_for_bonus' => 7,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for AI-powered features using Prism PHP.
    | Supports multiple providers: anthropic, openai, mistral, groq, gemini,
    | deepseek, ollama, openrouter.
    |
    */

    'ai' => [
        'enabled' => env('AI_ENABLED', true),

        // Active provider: anthropic, openai, mistral, groq, gemini, deepseek, ollama, openrouter
        'provider' => env('AI_PROVIDER', 'anthropic'),

        // Model overrides per service type (optional - provider defaults are used if not set)
        'models' => [
            'default' => env('AI_MODEL_DEFAULT'),
            'tutor' => env('AI_MODEL_TUTOR'),
            'practice' => env('AI_MODEL_PRACTICE'),
            'summary' => env('AI_MODEL_SUMMARY'),
        ],

        // Provider-specific default models (used when models.* is not set)
        'provider_models' => [
            'anthropic' => [
                'default' => 'claude-haiku-4-5-20251001',
                'tutor' => 'claude-sonnet-4-5-20250929',
                'practice' => 'claude-sonnet-4-5-20250929',
                'summary' => 'claude-haiku-4-5-20251001',
            ],
            'openai' => [
                'default' => 'gpt-4o-mini',
                'tutor' => 'gpt-4o',
                'practice' => 'gpt-4o',
                'summary' => 'gpt-4o-mini',
            ],
            'mistral' => [
                'default' => 'mistral-small-latest',
                'tutor' => 'mistral-large-latest',
                'practice' => 'mistral-large-latest',
                'summary' => 'mistral-small-latest',
            ],
            'groq' => [
                'default' => 'llama-3.3-70b-versatile',
                'tutor' => 'llama-3.3-70b-versatile',
                'practice' => 'llama-3.3-70b-versatile',
                'summary' => 'llama-3.3-70b-versatile',
            ],
            'gemini' => [
                'default' => 'gemini-1.5-flash',
                'tutor' => 'gemini-1.5-pro',
                'practice' => 'gemini-1.5-pro',
                'summary' => 'gemini-1.5-flash',
            ],
            'deepseek' => [
                'default' => 'deepseek-chat',
                'tutor' => 'deepseek-chat',
                'practice' => 'deepseek-chat',
                'summary' => 'deepseek-chat',
            ],
            'ollama' => [
                'default' => 'llama3.2',
                'tutor' => 'llama3.2',
                'practice' => 'llama3.2',
                'summary' => 'llama3.2',
            ],
        ],

        'quotas' => [
            'default_monthly_tokens' => 100000,
            'default_daily_requests' => 100,
        ],

        'cache' => [
            'enabled' => true,
            'ttl_hours' => 24,
        ],

        'features' => [
            'explanations' => true,
            'tutor' => true,
            'practice_generator' => true,
            'hints' => true,
            'summaries' => true,
            'flashcards' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Billing & Subscription
    |--------------------------------------------------------------------------
    |
    | Multi-currency and subscription settings.
    |
    */

    'billing' => [
        'default_currency' => 'chf',
        'supported_currencies' => ['chf', 'eur', 'usd'],
        'trial_days' => 14,
    ],

    /*
    |--------------------------------------------------------------------------
    | Subscription Plans
    |--------------------------------------------------------------------------
    |
    | Define pricing plans with Stripe price IDs and feature limits.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Per-Student Pricing Model
    |--------------------------------------------------------------------------
    |
    | Pricing is based on number of active students. Schools pay per student
    | per month, with yearly billing offering ~17% discount.
    |
    */

    'pricing_model' => 'per_student', // 'per_student' or 'flat'

    'plans' => [
        'starter' => [
            'name' => 'Starter',
            'description' => 'Ideal für kleine Schulen und Nachhilfe-Anbieter',
            'pricing_type' => 'per_student',
            'per_student_price' => [
                'chf' => ['monthly' => 24, 'yearly' => 240],  // CHF 24/student/month
                'eur' => ['monthly' => 22, 'yearly' => 220],
                'usd' => ['monthly' => 26, 'yearly' => 260],
            ],
            'min_students' => 10,
            'stripe_price_ids' => [
                'chf_monthly' => env('STRIPE_PRICE_STARTER_CHF_MONTHLY'),
                'chf_yearly' => env('STRIPE_PRICE_STARTER_CHF_YEARLY'),
                'eur_monthly' => env('STRIPE_PRICE_STARTER_EUR_MONTHLY'),
                'eur_yearly' => env('STRIPE_PRICE_STARTER_EUR_YEARLY'),
                'usd_monthly' => env('STRIPE_PRICE_STARTER_USD_MONTHLY'),
                'usd_yearly' => env('STRIPE_PRICE_STARTER_USD_YEARLY'),
            ],
            'limits' => [
                'instructors_per_50_students' => 3,
                'learning_paths' => -1, // unlimited
                'storage_gb_per_10_students' => 5,
                'ai_requests_per_student_monthly' => 0, // No AI
            ],
            'features' => [
                'ai_tutor' => false,
                'ai_practice' => false,
                'ai_explanations' => false,
                'advanced_analytics' => false,
                'custom_branding' => false,
                'api_access' => false,
                'sso' => false,
                'priority_support' => false,
            ],
        ],
        'professional' => [
            'name' => 'Professional',
            'description' => 'Mit KI-Tutor, Übungen und erweiterten Funktionen',
            'pricing_type' => 'per_student',
            'per_student_price' => [
                'chf' => ['monthly' => 32, 'yearly' => 320],  // CHF 32/student/month
                'eur' => ['monthly' => 30, 'yearly' => 300],
                'usd' => ['monthly' => 34, 'yearly' => 340],
            ],
            'min_students' => 10,
            'highlighted' => true,
            'stripe_price_ids' => [
                'chf_monthly' => env('STRIPE_PRICE_PRO_CHF_MONTHLY'),
                'chf_yearly' => env('STRIPE_PRICE_PRO_CHF_YEARLY'),
                'eur_monthly' => env('STRIPE_PRICE_PRO_EUR_MONTHLY'),
                'eur_yearly' => env('STRIPE_PRICE_PRO_EUR_YEARLY'),
                'usd_monthly' => env('STRIPE_PRICE_PRO_USD_MONTHLY'),
                'usd_yearly' => env('STRIPE_PRICE_PRO_USD_YEARLY'),
            ],
            'limits' => [
                'instructors_per_50_students' => 5,
                'learning_paths' => -1, // unlimited
                'storage_gb_per_10_students' => 10,
                'ai_requests_per_student_monthly' => 50,
            ],
            'features' => [
                'ai_tutor' => true,
                'ai_practice' => true,
                'ai_explanations' => true,
                'advanced_analytics' => true,
                'custom_branding' => false,
                'api_access' => false,
                'sso' => false,
                'priority_support' => true,
            ],
        ],
        'enterprise' => [
            'name' => 'Enterprise',
            'description' => 'Massgeschneiderte Lösung für grosse Institutionen',
            'pricing_type' => 'contact_sales',
            'per_student_price' => [
                'chf' => ['monthly' => null, 'yearly' => null], // CHF 5-7/student negotiated
                'eur' => ['monthly' => null, 'yearly' => null],
                'usd' => ['monthly' => null, 'yearly' => null],
            ],
            'min_students' => 500,
            'contact_sales' => true,
            'limits' => [
                'instructors_per_50_students' => -1, // unlimited
                'learning_paths' => -1,
                'storage_gb_per_10_students' => -1, // unlimited
                'ai_requests_per_student_monthly' => -1, // unlimited
            ],
            'features' => [
                'ai_tutor' => true,
                'ai_practice' => true,
                'ai_explanations' => true,
                'advanced_analytics' => true,
                'custom_branding' => true,
                'api_access' => true,
                'sso' => true,
                'priority_support' => true,
                'dedicated_support' => true,
                'custom_integrations' => true,
                'sla' => true,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Localization
    |--------------------------------------------------------------------------
    |
    | Supported languages and default locale.
    |
    */

    'localization' => [
        'default_locale' => 'de',
        'supported_locales' => ['de', 'en', 'fr'],
        'fallback_locale' => 'de',
    ],

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    |
    | Email and in-app notification settings.
    |
    */

    'notifications' => [
        'channels' => ['mail', 'database'],

        'events' => [
            'enrollment_confirmed' => true,
            'step_completed' => false,
            'path_completed' => true,
            'certificate_issued' => true,
            'submission_graded' => true,
            'new_content_available' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Progress Tracking
    |--------------------------------------------------------------------------
    |
    | Settings for tracking learner progress and time spent.
    |
    */

    'progress' => [
        'track_time_spent' => true,
        'auto_complete_after_video' => true,
        'require_all_materials' => true,
        'allow_skip_steps' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Certificates
    |--------------------------------------------------------------------------
    |
    | Certificate generation and verification settings.
    |
    */

    'certificates' => [
        'enabled' => true,
        'require_all_assessments_passed' => true,
        'min_progress_percent' => 100,
        'include_qr_code' => true,
        'verification_url' => env('APP_URL') . '/verify',
    ],

];
