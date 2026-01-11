<?php

use App\Livewire\Instructor\Analytics\Index as AnalyticsIndex;
use App\Livewire\Instructor\Analytics\Paths as AnalyticsPaths;
use App\Livewire\Instructor\Analytics\Students as AnalyticsStudents;
use App\Livewire\Instructor\Assessments\AssessmentBuilder;
use App\Livewire\Instructor\Assessments\Results as AssessmentsResults;
use App\Livewire\Instructor\Dashboard;
use App\Livewire\Instructor\LearningPaths\Create as PathsCreate;
use App\Livewire\Instructor\LearningPaths\Edit as PathsEdit;
use App\Livewire\Instructor\LearningPaths\Index as PathsIndex;
use App\Livewire\Instructor\LearningPaths\Show as PathsShow;
use App\Livewire\Instructor\Materials\MaterialUploader;
use App\Livewire\Instructor\Modules\ModuleManager;
use App\Livewire\Instructor\Paths\Preview as PathsPreview;
use App\Livewire\Instructor\Paths\Settings as PathsSettings;
use App\Livewire\Instructor\Steps\Edit as StepsEdit;
use App\Livewire\Instructor\Students\Index as StudentsIndex;
use App\Livewire\Instructor\Students\Show as StudentsShow;
use App\Livewire\Instructor\Submissions\Index as SubmissionsIndex;
use App\Livewire\Instructor\Submissions\Review as SubmissionsReview;
use App\Livewire\Instructor\Submissions\Show as SubmissionsShow;
use App\Livewire\Instructor\Tasks\Edit as TasksEdit;
use App\Livewire\Instructor\Tasks\Show as TasksShow;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Instructor Routes
|--------------------------------------------------------------------------
|
| Routes for instructors. All routes require authentication and instructor role.
|
*/

Route::middleware(['auth:sanctum', 'verified', 'role:instructor,admin'])->prefix('instructor')->name('instructor.')->group(function () {

    // Dashboard
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Learning Paths
    Route::prefix('paths')->name('paths.')->group(function () {
        Route::get('/', PathsIndex::class)->name('index');
        Route::get('/create', PathsCreate::class)->name('create');
        Route::get('/{path}', PathsShow::class)->name('show');
        Route::get('/{path}/edit', PathsEdit::class)->name('edit');
        Route::get('/{path}/modules', ModuleManager::class)->name('modules');
        Route::get('/{path}/settings', PathsSettings::class)->name('settings');
        Route::get('/{path}/preview', PathsPreview::class)->name('preview');
    });

    // Step Editor
    Route::prefix('steps')->name('steps.')->group(function () {
        Route::get('/{step}/edit', StepsEdit::class)->name('edit');
        Route::get('/{step}/materials', MaterialUploader::class)->name('materials');
    });

    // Assessment Builder
    Route::prefix('assessments')->name('assessments.')->group(function () {
        Route::get('/{assessment}', AssessmentBuilder::class)->name('show');
        Route::get('/{assessment}/builder', AssessmentBuilder::class)->name('builder');
        Route::get('/{assessment}/results', AssessmentsResults::class)->name('results');
    });

    // Task Management
    Route::prefix('tasks')->name('tasks.')->group(function () {
        Route::get('/{task}', TasksShow::class)->name('show');
        Route::get('/{task}/edit', TasksEdit::class)->name('edit');
    });

    // Submissions Review
    Route::prefix('submissions')->name('submissions.')->group(function () {
        Route::get('/', SubmissionsIndex::class)->name('index');
        Route::get('/{submission}', SubmissionsShow::class)->name('show');
        Route::get('/{submission}/review', SubmissionsReview::class)->name('review');
    });

    // Students
    Route::prefix('students')->name('students.')->group(function () {
        Route::get('/', StudentsIndex::class)->name('index');
        Route::get('/{enrollment}', StudentsShow::class)->name('show');
    });

    // Analytics
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/', AnalyticsIndex::class)->name('index');
        Route::get('/paths', AnalyticsPaths::class)->name('paths');
        Route::get('/students', AnalyticsStudents::class)->name('students');
    });

});
