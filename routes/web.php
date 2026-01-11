<?php

use App\Enums\UserRole;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes (landing, pricing, features, etc.)
require __DIR__ . '/public.php';

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Dashboard redirect based on role
    Route::get('/dashboard', function () {
        $user = auth()->user();

        return match ($user->role) {
            UserRole::Admin => redirect()->route('admin.dashboard'),
            UserRole::Instructor => redirect()->route('instructor.dashboard'),
            default => redirect()->route('learner.dashboard'),
        };
    })->name('dashboard');
});

// Include role-specific routes
require __DIR__ . '/admin.php';
require __DIR__ . '/instructor.php';
require __DIR__ . '/learner.php';

// Billing routes (Stripe subscriptions)
require __DIR__ . '/billing.php';

// School admin routes
require __DIR__ . '/school.php';
