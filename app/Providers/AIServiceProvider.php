<?php

namespace App\Providers;

use App\Services\AI\AIClientService;
use App\Services\AI\AIContextBuilder;
use App\Services\AI\AIExplanationService;
use App\Services\AI\AIPracticeGeneratorService;
use App\Services\AI\AISummaryService;
use App\Services\AI\AITutorService;
use App\Services\AI\AIUsageService;
use Illuminate\Support\ServiceProvider;

class AIServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AIClientService::class, function ($app) {
            return new AIClientService(
                config('lernpfad.ai.api_key', env('ANTHROPIC_API_KEY', ''))
            );
        });

        $this->app->singleton(AIContextBuilder::class);
        $this->app->singleton(AIUsageService::class);

        $this->app->singleton(AIExplanationService::class, function ($app) {
            return new AIExplanationService(
                $app->make(AIClientService::class),
                $app->make(AIContextBuilder::class),
                $app->make(AIUsageService::class)
            );
        });

        $this->app->singleton(AITutorService::class, function ($app) {
            return new AITutorService(
                $app->make(AIClientService::class),
                $app->make(AIContextBuilder::class),
                $app->make(AIUsageService::class)
            );
        });

        $this->app->singleton(AIPracticeGeneratorService::class, function ($app) {
            return new AIPracticeGeneratorService(
                $app->make(AIClientService::class),
                $app->make(AIContextBuilder::class),
                $app->make(AIUsageService::class)
            );
        });

        $this->app->singleton(AISummaryService::class, function ($app) {
            return new AISummaryService(
                $app->make(AIClientService::class),
                $app->make(AIContextBuilder::class),
                $app->make(AIUsageService::class)
            );
        });
    }

    public function boot(): void
    {
        //
    }
}
