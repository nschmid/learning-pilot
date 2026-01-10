<?php

namespace App\Providers;

use App\Events\AssessmentCompleted;
use App\Events\CertificateIssued;
use App\Events\EnrollmentCreated;
use App\Events\PathCompleted;
use App\Events\StepCompleted;
use App\Events\SubmissionGraded;
use App\Events\TaskSubmitted;
use App\Listeners\AwardPointsOnStepCompletion;
use App\Listeners\HandlePathCompletion;
use App\Listeners\NotifyInstructorOfSubmission;
use App\Listeners\NotifyLearnerOfGrade;
use App\Listeners\ProcessAssessmentCompletion;
use App\Listeners\SendCertificateNotification;
use App\Listeners\SendEnrollmentConfirmation;
use App\Listeners\UpdateProgressOnStepCompletion;
use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\Category;
use App\Models\Certificate;
use App\Models\LearningStep;
use App\Models\Module;
use App\Models\Question;
use App\Models\StepProgress;
use App\Models\Task;
use App\Models\User;
use App\Policies\AssessmentAttemptPolicy;
use App\Policies\AssessmentPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\CertificatePolicy;
use App\Policies\LearningStepPolicy;
use App\Policies\ModulePolicy;
use App\Policies\QuestionPolicy;
use App\Policies\StepProgressPolicy;
use App\Policies\TaskPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        $this->registerEvents();
    }

    /**
     * Register policies.
     */
    protected function registerPolicies(): void
    {
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Module::class, ModulePolicy::class);
        Gate::policy(LearningStep::class, LearningStepPolicy::class);
        Gate::policy(Assessment::class, AssessmentPolicy::class);
        Gate::policy(Question::class, QuestionPolicy::class);
        Gate::policy(AssessmentAttempt::class, AssessmentAttemptPolicy::class);
        Gate::policy(StepProgress::class, StepProgressPolicy::class);
        Gate::policy(Task::class, TaskPolicy::class);
        Gate::policy(Certificate::class, CertificatePolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);
    }

    /**
     * Register event listeners.
     */
    protected function registerEvents(): void
    {
        // Enrollment events
        Event::listen(
            EnrollmentCreated::class,
            SendEnrollmentConfirmation::class
        );

        // Step completion events
        Event::listen(
            StepCompleted::class,
            [UpdateProgressOnStepCompletion::class, AwardPointsOnStepCompletion::class]
        );

        // Path completion events
        Event::listen(
            PathCompleted::class,
            HandlePathCompletion::class
        );

        // Assessment events
        Event::listen(
            AssessmentCompleted::class,
            ProcessAssessmentCompletion::class
        );

        // Task submission events
        Event::listen(
            TaskSubmitted::class,
            NotifyInstructorOfSubmission::class
        );

        // Submission grading events
        Event::listen(
            SubmissionGraded::class,
            NotifyLearnerOfGrade::class
        );

        // Certificate events
        Event::listen(
            CertificateIssued::class,
            SendCertificateNotification::class
        );
    }
}
