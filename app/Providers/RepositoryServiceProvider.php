<?php

namespace App\Providers;

use App\Repositories\AssessmentRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\EnrollmentRepository;
use App\Repositories\LearningPathRepository;
use App\Repositories\TaskSubmissionRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * All repository bindings.
     *
     * @var array<class-string>
     */
    protected array $repositories = [
        LearningPathRepository::class,
        EnrollmentRepository::class,
        AssessmentRepository::class,
        UserRepository::class,
        TaskSubmissionRepository::class,
        CategoryRepository::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        foreach ($this->repositories as $repository) {
            $this->app->singleton($repository, fn () => new $repository);
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
