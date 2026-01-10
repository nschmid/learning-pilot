<?php

use App\Livewire\Admin\Categories\Index as CategoriesIndex;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Paths\Index as PathsIndex;
use App\Livewire\Admin\Paths\Show as PathsShow;
use App\Livewire\Admin\Teams\Index as TeamsIndex;
use App\Livewire\Admin\Teams\Show as TeamsShow;
use App\Livewire\Admin\Users\Create as UsersCreate;
use App\Livewire\Admin\Users\Edit as UsersEdit;
use App\Livewire\Admin\Users\Index as UsersIndex;
use App\Livewire\Admin\Users\Show as UsersShow;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Routes for the admin panel. All routes require authentication and admin role.
|
*/

Route::middleware(['auth:sanctum', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Users Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', UsersIndex::class)->name('index');
        Route::get('/create', UsersCreate::class)->name('create');
        Route::get('/{user}', UsersShow::class)->name('show');
        Route::get('/{user}/edit', UsersEdit::class)->name('edit');
    });

    // Teams (Schools) Management
    Route::prefix('teams')->name('teams.')->group(function () {
        Route::get('/', TeamsIndex::class)->name('index');
        Route::get('/{team}', TeamsShow::class)->name('show');
    });

    // Learning Paths Management
    Route::prefix('paths')->name('paths.')->group(function () {
        Route::get('/', PathsIndex::class)->name('index');
        Route::get('/{path}', PathsShow::class)->name('show');
    });

    // Categories Management
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', CategoriesIndex::class)->name('index');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::view('/', 'admin.reports.index')->name('index');
        Route::view('/users', 'admin.reports.users')->name('users');
        Route::view('/paths', 'admin.reports.paths')->name('paths');
        Route::view('/enrollments', 'admin.reports.enrollments')->name('enrollments');
        Route::view('/ai-usage', 'admin.reports.ai-usage')->name('ai-usage');
    });

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::view('/', 'admin.settings.index')->name('index');
        Route::view('/general', 'admin.settings.general')->name('general');
        Route::view('/billing', 'admin.settings.billing')->name('billing');
        Route::view('/ai', 'admin.settings.ai')->name('ai');
    });

});
