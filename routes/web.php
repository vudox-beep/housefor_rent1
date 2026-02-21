<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DealerController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LeadController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Payments\LencoPaymentController;

Route::post('/leads', [LeadController::class, 'store'])->name('leads.store');
Route::post('/reports', [ReportController::class, 'store'])->middleware('auth')->name('reports.store');

Route::get('/', [ListingController::class, 'home'])->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::resource('listings', ListingController::class)->only(['index', 'show']);
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('listings', ListingController::class)->except(['index', 'show']);
});

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/settings/free-trial', [AdminController::class, 'updateFreeTrial'])->name('settings.free-trial');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/dealers', [AdminController::class, 'dealers'])->name('dealers');
    Route::get('/transactions', [AdminController::class, 'transactions'])->name('transactions');
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    Route::post('/users/{id}/suspend', [AdminController::class, 'suspendUser'])->name('users.suspend');
    Route::post('/users/{id}/activate', [AdminController::class, 'activateUser'])->name('users.activate');
    Route::post('/listings/{listing}/status', [AdminController::class, 'updateListingStatus'])->name('listings.status');
});

// Dealer Routes
Route::prefix('dealer')->name('dealer.')->middleware(['auth', 'verified', 'role:dealer'])->group(function () {
    Route::get('/dashboard', [DealerController::class, 'dashboard'])->name('dashboard');
    Route::get('/leads', [DealerController::class, 'leads'])->name('leads');
    Route::get('/listings', [DealerController::class, 'myListings'])->name('my-listings');
    Route::post('/listings/{listing}/status', [DealerController::class, 'updateListingStatus'])->name('listings.status');
    Route::get('/agents', [DealerController::class, 'agents'])->name('agents');
    Route::post('/agents', [DealerController::class, 'storeAgent'])->name('agents.store');
    Route::delete('/agents/{agent}', [DealerController::class, 'destroyAgent'])->name('agents.destroy');
    Route::get('/profile', [DealerController::class, 'profile'])->name('profile');
    Route::put('/profile', [DealerController::class, 'updateProfile'])->name('profile.update');
    Route::put('/password', [DealerController::class, 'updatePassword'])->name('password.update');
    Route::get('/subscription', [DealerController::class, 'subscription'])->name('subscription');
    Route::post('/subscription', [DealerController::class, 'processSubscription'])->name('subscription.process');
});

require __DIR__.'/auth.php';

// Socialite / Google OAuth
Route::get('auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);

// Lenco payment routes
Route::get('/payments/lenco/form', [LencoPaymentController::class, 'showPaymentForm'])->name('payments.lenco.form');
Route::post('/payments/lenco/submit', [LencoPaymentController::class, 'submitPayment'])->name('payments.lenco.submit');
Route::post('/payments/lenco/initiate', [LencoPaymentController::class, 'initiateMobileMoneyPayment'])->name('payments.lenco.initiate')->middleware('auth');
Route::post('/payments/lenco/debug/mobile-money', [LencoPaymentController::class, 'debugMobileMoneyCollection'])->name('payments.lenco.debug.mobile-money')->middleware('auth');
Route::post('/payments/lenco/verify', [LencoPaymentController::class, 'verify'])->name('payments.lenco.verify')->middleware('auth');
Route::post('/payments/lenco/validate-account', [LencoPaymentController::class, 'validateMobileMoneyAccount'])->name('payments.lenco.validate-account')->middleware('auth');
Route::post('/payments/lenco/webhook', [LencoPaymentController::class, 'webhook'])->name('payments.lenco.webhook');
Route::get('/payments/lenco/return', [LencoPaymentController::class, 'return'])->name('payments.lenco.return');
