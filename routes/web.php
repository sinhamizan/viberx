<?php

use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\IdentityVerificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PlanSelectionController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ShippingAddressController;
use App\Http\Controllers\StateGateController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
  return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/pricing', [PricingController::class, 'index'])->name('pricing.index');

Route::get('/state', [StateGateController::class, 'show'])->name('state.show');
Route::post('/state', [StateGateController::class, 'store'])->name('state.store');

Route::get('/plans', [PlanSelectionController::class, 'index'])->name('plans.index');
Route::post('/plans', [PlanSelectionController::class, 'store'])->name('plans.store');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/identity-verification', [IdentityVerificationController::class, 'show'])->name('identity.show');
    Route::post('/identity-verification', [IdentityVerificationController::class, 'store'])->name('identity.store');
    Route::post('/identity-verification/confirm', [IdentityVerificationController::class, 'confirm'])->name('identity.confirm');
    Route::post('/identity-verification/skip', [IdentityVerificationController::class, 'skip'])->name('identity.skip');

    Route::get('/assessment', [AssessmentController::class, 'show'])->name('assessment.show');
    Route::post('/assessment', [AssessmentController::class, 'store'])->name('assessment.store');

    Route::get('/shipping', [ShippingAddressController::class, 'show'])->name('shipping.show');
    Route::post('/shipping', [ShippingAddressController::class, 'store'])->name('shipping.store');

    Route::get('/payment', [PaymentController::class, 'show'])->name('payment.show');
    Route::post('/payment', [PaymentController::class, 'store'])->name('payment.store');

    Route::get('/review', [ReviewController::class, 'show'])->name('review.show');
    Route::post('/review', [ReviewController::class, 'store'])->name('review.store');
});

require __DIR__.'/auth.php';
