<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuotePdfController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\FencingLandingPageController;

Route::get('/', [WelcomeController::class, 'index'])->name('home');
Route::get('/fencing-quote-app', [FencingLandingPageController::class, 'index'])->name('fencing-landing-page');
Route::view('/pricing', 'pricing-page')->name('pricing');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('quotes/create', 'quotes.create')->name('quotes.create');
    Route::get('quotes/{quote}/pdf', [QuotePdfController::class, 'show'])->name('quotes.pdf');
});

require __DIR__.'/settings.php';
