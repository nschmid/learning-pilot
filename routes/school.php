<?php

use App\Livewire\School\Analytics;
use App\Livewire\School\Dashboard;
use App\Livewire\School\StudentImport;
use App\Livewire\School\StudentList;
use App\Livewire\School\UsageDashboard;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| School Admin Routes
|--------------------------------------------------------------------------
|
| Routes for school/team administrators to manage their organization.
|
*/

Route::middleware(['auth', 'verified', 'team.admin'])->prefix('school')->name('school.')->group(function () {
    // Dashboard
    Route::get('/', Dashboard::class)->name('dashboard');

    // Students & Instructors
    Route::get('/students', StudentList::class)->name('students');
    Route::get('/students/import', StudentImport::class)->name('students.import');

    // Analytics
    Route::get('/analytics', Analytics::class)->name('analytics');

    // Usage
    Route::get('/usage', UsageDashboard::class)->name('usage');
});
