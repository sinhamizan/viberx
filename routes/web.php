<?php

use App\Http\Controllers\IdentityVerificationController;
use App\Http\Controllers\ProfileController;
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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/identity-verification', [IdentityVerificationController::class, 'show'])
        ->name('identity.show');
    Route::post('/identity-verification', [IdentityVerificationController::class, 'store'])
        ->name('identity.store');
    Route::post('/identity-verification/confirm', [IdentityVerificationController::class, 'confirm'])
        ->name('identity.confirm');
    Route::post('/identity-verification/skip', [IdentityVerificationController::class, 'skip'])
        ->name('identity.skip');
});

require __DIR__.'/auth.php';
