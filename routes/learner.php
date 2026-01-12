<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Learner Routes
|--------------------------------------------------------------------------
|
| Routes for learners. All routes require authentication.
|
*/

use App\Livewire\Learner\Assessment\Result as AssessmentResult;
use App\Livewire\Learner\Assessment\Start as AssessmentStart;
use App\Livewire\Learner\Assessment\Take as AssessmentTake;
use App\Livewire\Learner\Bookmarks\Index as BookmarksIndex;
use App\Livewire\Learner\Catalog\Browse as CatalogBrowse;
use App\Livewire\Learner\Dashboard;
use App\Livewire\Learner\Learn\StepViewer;
use App\Livewire\Learner\Notes\Index as NotesIndex;
use App\Livewire\Learner\Path\Reviews as PathReviews;
use App\Livewire\Learner\Path\Show as PathShow;
use App\Livewire\Learner\Task\Show as TaskShow;
use App\Livewire\Learner\Task\Submission as TaskSubmission;
use App\Livewire\Learner\Certificates\Index as CertificatesIndex;
use App\Livewire\Learner\Certificates\Show as CertificatesShow;
use App\Livewire\Learner\Settings\Index as SettingsIndex;
use App\Livewire\Learner\Ai\TutorChat;
use App\Livewire\Learner\Ai\PracticeSession;
use App\Livewire\Learner\Ai\SummaryPanel;
use App\Livewire\Learner\Ai\FlashcardViewer;

Route::middleware(['auth:sanctum', 'verified'])->prefix('learn')->name('learner.')->group(function () {

    // Dashboard / My Learning
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Catalog
    Route::prefix('catalog')->name('catalog')->group(function () {
        Route::get('/', CatalogBrowse::class);
        Route::get('/category/{category:slug}', CatalogBrowse::class)->name('.category');
        Route::get('/search', CatalogBrowse::class)->name('.search');
    });

    // Learning Path Details
    Route::prefix('path')->name('path.')->group(function () {
        Route::get('/{path:slug}', PathShow::class)->name('show');
        Route::get('/{path:slug}/overview', PathShow::class)->name('overview');
        Route::get('/{path:slug}/reviews', PathReviews::class)->name('reviews');
    });

    // Active Learning (Step Viewer)
    Route::prefix('learn')->name('learn.')->middleware('enrolled')->group(function () {
        Route::get('/{path:slug}', StepViewer::class)->name('index');
        Route::get('/{path:slug}/step/{step}', StepViewer::class)->name('step');
    });

    // Assessments
    Route::prefix('assessment')->name('assessment.')->group(function () {
        Route::get('/{assessment}/start', AssessmentStart::class)->name('start');
        Route::get('/{assessment}/take', AssessmentTake::class)->name('take');
        Route::get('/{assessment}/result/{attempt}', AssessmentResult::class)->name('result');
    });

    // Tasks
    Route::prefix('task')->name('task.')->group(function () {
        Route::get('/{task}', TaskShow::class)->name('show');
        Route::get('/{task}/submission/{submission}', TaskSubmission::class)->name('submission');
    });

    // AI Features
    Route::prefix('ai')->name('ai.')->group(function () {
        Route::get('/tutor', TutorChat::class)->name('tutor');
        Route::get('/tutor/{step}', TutorChat::class)->name('tutor.step');
        Route::get('/tutor/conversation/{conversation}', TutorChat::class)->name('tutor.conversation');
        Route::get('/practice/{module}', PracticeSession::class)->name('practice');
        Route::get('/summary/{module}', SummaryPanel::class)->name('summary');
        Route::get('/flashcards/{module}', FlashcardViewer::class)->name('flashcards');
    });

    // Bookmarks
    Route::get('/bookmarks', BookmarksIndex::class)->name('bookmarks');

    // Notes
    Route::get('/notes', NotesIndex::class)->name('notes');

    // Certificates
    Route::prefix('certificates')->name('certificates')->group(function () {
        Route::get('/', CertificatesIndex::class);
        Route::get('/{certificate}', CertificatesShow::class)->name('.show');
    });

    // Settings
    Route::get('/settings', SettingsIndex::class)->name('settings');

});

// Public Certificate Verification
Route::get('/verify/{certificate:certificate_number}', function ($certificate) {
    return view('certificates.verify', ['certificate' => $certificate]);
})->name('certificate.verify');
