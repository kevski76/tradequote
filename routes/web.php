<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\FencingLandingPageController;

Route::get('/', [WelcomeController::class, 'index'])->name('home');
Route::get('/fencing-quote-app', [FencingLandingPageController::class, 'index'])->name('fencing-landing-page');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
