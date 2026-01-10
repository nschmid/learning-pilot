<?php

use App\Livewire\Instructor\Assessments\AssessmentBuilder;
use App\Livewire\Instructor\Dashboard;
use App\Livewire\Instructor\LearningPaths\Create as PathsCreate;
use App\Livewire\Instructor\LearningPaths\Edit as PathsEdit;
use App\Livewire\Instructor\LearningPaths\Index as PathsIndex;
use App\Livewire\Instructor\LearningPaths\Show as PathsShow;
use App\Livewire\Instructor\Materials\MaterialUploader;
use App\Livewire\Instructor\Modules\ModuleManager;
use App\Livewire\Instructor\Steps\Edit as StepsEdit;
use App\Livewire\Instructor\Submissions\Index as SubmissionsIndex;
use App\Livewire\Instructor\Submissions\Review as SubmissionsReview;
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
        Route::view('/{path}/settings', 'instructor.paths.settings')->name('settings');
        Route::view('/{path}/preview', 'instructor.paths.preview')->name('preview');
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
        Route::view('/{assessment}/results', 'instructor.assessments.results')->name('results');
    });

    // Task Management
    Route::prefix('tasks')->name('tasks.')->group(function () {
        Route::view('/{task}', 'instructor.tasks.show')->name('show');
        Route::view('/{task}/edit', 'instructor.tasks.edit')->name('edit');
    });

    // Submissions Review
    Route::prefix('submissions')->name('submissions.')->group(function () {
        Route::get('/', SubmissionsIndex::class)->name('index');
        Route::view('/{submission}', 'instructor.submissions.show')->name('show');
        Route::get('/{submission}/review', SubmissionsReview::class)->name('review');
    });

    // Students
    Route::prefix('students')->name('students.')->group(function () {
        Route::view('/', 'instructor.students.index')->name('index');
        Route::view('/{enrollment}', 'instructor.students.show')->name('show');
    });

    // Analytics
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::view('/', 'instructor.analytics.index')->name('index');
        Route::view('/paths', 'instructor.analytics.paths')->name('paths');
        Route::view('/students', 'instructor.analytics.students')->name('students');
    });

});
