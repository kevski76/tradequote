<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FeedbackSubmissionController;
use App\Http\Controllers\QuotePdfController;
use App\Http\Controllers\QuotePublicController;
use App\Http\Controllers\ReviewPublicController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\FencingLandingPageController;

Route::get('/', [WelcomeController::class, 'index'])->name('home');
Route::get('/fencing-quote-app', [FencingLandingPageController::class, 'index'])->name('fencing-landing-page');
Route::view('/pricing', 'pricing-page')->name('pricing');

Route::get('/quote/{uuid}', [QuotePublicController::class, 'show'])->name('quote.public');
Route::get('/review/{uuid}', [ReviewPublicController::class, 'show'])->name('review.public');
Route::get('/review/{uuid}/feedback', [ReviewPublicController::class, 'feedbackForm'])->name('review.feedback');
Route::post('/review/{uuid}/feedback', [ReviewPublicController::class, 'submitFeedback'])->name('review.feedback.submit');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('quotes/create', 'quotes.create')->name('quotes.create');
    Route::get('quotes/{quote}/edit', fn (\App\Models\Quotes $quote) => view('quotes.edit', ['quote' => $quote]))->name('quotes.edit');
    Route::get('quotes/{quote}/pdf', [QuotePdfController::class, 'show'])->name('quotes.pdf');
    Route::get('feedback-submissions', [FeedbackSubmissionController::class, 'index'])->name('feedback.index');
});

require __DIR__.'/settings.php';
