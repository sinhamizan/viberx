<?php

namespace App\Services;

use App\Enums\OtpVerificationResult;
use App\Models\EmailOtp;
use App\Notifications\SendOtpCode;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

class OtpService
{
    private const CODE_LIFETIME_MINUTES = 10;

    /**
     * Generate a fresh 6-digit code for the given email and send it.
     */
    public function generateAndSendFor(string $email): void
    {
        EmailOtp::where('email', $email)->delete();

        $code = (string) random_int(100000, 999999);

        EmailOtp::create([
            'email' => $email,
            'hashed_code' => Hash::make($code),
            'expires_at' => now()->addMinutes(self::CODE_LIFETIME_MINUTES),
        ]);

        Notification::route('mail', $email)->notify(new SendOtpCode($code));
    }

    /**
     * Verify a submitted code against the latest OTP stored for the email.
     */
    public function verify(string $email, string $code): OtpVerificationResult
    {
        $otp = EmailOtp::where('email', $email)->latest()->first();

        if (! $otp) {
            return OtpVerificationResult::NotFound;
        }

        if ($otp->isExpired()) {
            $otp->delete();

            return OtpVerificationResult::Expired;
        }

        if ($otp->hasExceededAttempts()) {
            $otp->delete();

            return OtpVerificationResult::TooManyAttempts;
        }

        if (! Hash::check($code, $otp->hashed_code)) {
            $otp->increment('attempts');

            return OtpVerificationResult::InvalidCode;
        }

        $otp->delete();

        return OtpVerificationResult::Verified;
    }
}
