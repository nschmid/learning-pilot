<?php

use App\Livewire\Public\Contact;
use App\Livewire\Public\Features;
use App\Livewire\Public\Landing;
use App\Livewire\Public\Pricing;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
|
| These routes are for the public-facing marketing pages.
|
*/

Route::get('/', Landing::class)->name('landing');
Route::get('/features', Features::class)->name('features');
Route::get('/pricing', Pricing::class)->name('pricing');
Route::get('/contact', Contact::class)->name('contact');

// Legal pages
Route::view('/legal/privacy', 'pages.legal.privacy')->name('legal.privacy');
Route::view('/legal/terms', 'pages.legal.terms')->name('legal.terms');
Route::view('/legal/imprint', 'pages.legal.imprint')->name('legal.imprint');
