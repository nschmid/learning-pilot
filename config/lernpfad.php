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

        'plans' => [
            'basic' => [
                'max_students' => 50,
                'max_instructors' => 5,
                'max_storage_gb' => 10,
                'ai_enabled' => false,
            ],
            'professional' => [
                'max_students' => 200,
                'max_instructors' => 20,
                'max_storage_gb' => 50,
                'ai_enabled' => true,
            ],
            'enterprise' => [
                'max_students' => null, // unlimited
                'max_instructors' => null,
                'max_storage_gb' => 500,
                'ai_enabled' => true,
            ],
        ],

        'trial_days' => 14,
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
