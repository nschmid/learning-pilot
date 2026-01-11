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
    |
    */

    'ai' => [
        'enabled' => env('AI_ENABLED', true),
        'provider' => env('AI_PROVIDER', 'anthropic'),
        'api_key' => env('ANTHROPIC_API_KEY'),

        'models' => [
            'default' => env('AI_MODEL_DEFAULT', 'claude-haiku-4-5-20251001'),
            'tutor' => env('AI_MODEL_TUTOR', 'claude-sonnet-4-5-20250929'),
            'practice' => env('AI_MODEL_PRACTICE', 'claude-sonnet-4-5-20250929'),
            'summary' => env('AI_MODEL_SUMMARY', 'claude-haiku-4-5-20251001'),
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

    'plans' => [
        'starter' => [
            'name' => 'Starter',
            'description' => 'Ideal für kleine Schulen und Nachhilfe',
            'stripe_prices' => [
                'chf' => env('STRIPE_PRICE_STARTER_CHF', 'price_starter_chf'),
                'eur' => env('STRIPE_PRICE_STARTER_EUR', 'price_starter_eur'),
                'usd' => env('STRIPE_PRICE_STARTER_USD', 'price_starter_usd'),
            ],
            'prices' => [
                'chf' => 49,
                'eur' => 45,
                'usd' => 49,
            ],
            'billing_period' => 'monthly',
            'limits' => [
                'students' => 50,
                'instructors' => 3,
                'storage_gb' => 5,
                'ai_daily_requests' => 0,
            ],
            'features' => [
                'ai_tutor' => false,
                'ai_practice' => false,
                'ai_explanations' => false,
                'advanced_analytics' => false,
                'custom_branding' => false,
                'api_access' => false,
                'priority_support' => false,
            ],
        ],
        'professional' => [
            'name' => 'Professional',
            'description' => 'Für wachsende Bildungseinrichtungen',
            'stripe_prices' => [
                'chf' => env('STRIPE_PRICE_PRO_CHF', 'price_pro_chf'),
                'eur' => env('STRIPE_PRICE_PRO_EUR', 'price_pro_eur'),
                'usd' => env('STRIPE_PRICE_PRO_USD', 'price_pro_usd'),
            ],
            'prices' => [
                'chf' => 149,
                'eur' => 139,
                'usd' => 149,
            ],
            'billing_period' => 'monthly',
            'limits' => [
                'students' => 200,
                'instructors' => 10,
                'storage_gb' => 25,
                'ai_daily_requests' => 500,
            ],
            'features' => [
                'ai_tutor' => true,
                'ai_practice' => true,
                'ai_explanations' => true,
                'advanced_analytics' => true,
                'custom_branding' => false,
                'api_access' => false,
                'priority_support' => false,
            ],
        ],
        'enterprise' => [
            'name' => 'Enterprise',
            'description' => 'Für grosse Institutionen mit individuellen Anforderungen',
            'stripe_prices' => [
                'chf' => env('STRIPE_PRICE_ENTERPRISE_CHF', 'price_enterprise_chf'),
                'eur' => env('STRIPE_PRICE_ENTERPRISE_EUR', 'price_enterprise_eur'),
                'usd' => env('STRIPE_PRICE_ENTERPRISE_USD', 'price_enterprise_usd'),
            ],
            'prices' => [
                'chf' => 399,
                'eur' => 369,
                'usd' => 399,
            ],
            'billing_period' => 'monthly',
            'limits' => [
                'students' => -1, // unlimited
                'instructors' => -1,
                'storage_gb' => 100,
                'ai_daily_requests' => -1, // unlimited
            ],
            'features' => [
                'ai_tutor' => true,
                'ai_practice' => true,
                'ai_explanations' => true,
                'advanced_analytics' => true,
                'custom_branding' => true,
                'api_access' => true,
                'priority_support' => true,
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
