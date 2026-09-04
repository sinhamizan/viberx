<?php

use App\Http\Controllers\Auth\OtpAuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [OtpAuthController::class, 'showRegister'])
        ->name('register');

    Route::post('register', [OtpAuthController::class, 'register']);

    Route::get('login', [OtpAuthController::class, 'showLogin'])
        ->name('login');

    Route::post('login', [OtpAuthController::class, 'login'])
        ->middleware('throttle:5,1');

    Route::get('verify-otp', [OtpAuthController::class, 'showVerify'])
        ->name('otp.verify');

    Route::post('verify-otp', [OtpAuthController::class, 'verify'])
        ->middleware('throttle:10,1');

    Route::post('resend-otp', [OtpAuthController::class, 'resend'])
        ->name('otp.resend')
        ->middleware('throttle:3,1');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [OtpAuthController::class, 'destroy'])
        ->name('logout');
});
