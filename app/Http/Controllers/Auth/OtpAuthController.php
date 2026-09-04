<?php

namespace App\Http\Controllers\Auth;

use App\Enums\OtpVerificationResult;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class OtpAuthController extends Controller
{
    private const SESSION_KEY = 'otp_pending_email';

    public function __construct(private readonly OtpService $otpService)
    {
        //
    }

    public function showRegister(): Response
    {
        return Inertia::render('Auth/Register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $user = User::create($request->safe()->except('terms'));

        $this->otpService->generateAndSendFor($user->email);

        $request->session()->put(self::SESSION_KEY, $user->email);

        return to_route('otp.verify');
    }

    public function showLogin(): Response
    {
        return Inertia::render('Auth/Login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $email = $request->validated('email');

        $this->otpService->generateAndSendFor($email);

        $request->session()->put(self::SESSION_KEY, $email);

        return to_route('otp.verify');
    }

    public function showVerify(Request $request): Response|RedirectResponse
    {
        $email = $request->session()->get(self::SESSION_KEY);

        if (! $email) {
            return to_route('login');
        }

        return Inertia::render('Auth/VerifyOtp', ['email' => $email]);
    }

    public function verify(Request $request): RedirectResponse
    {
        $email = $request->session()->get(self::SESSION_KEY);

        if (! $email) {
            return to_route('login');
        }

        $data = $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $result = $this->otpService->verify($email, $data['code']);

        return match ($result) {
            OtpVerificationResult::Verified => $this->completeLogin($request, $email),
            OtpVerificationResult::Expired => throw ValidationException::withMessages([
                'code' => 'This code has expired. Please request a new one.',
            ]),
            OtpVerificationResult::TooManyAttempts => throw ValidationException::withMessages([
                'code' => 'Too many incorrect attempts. Please request a new code.',
            ]),
            OtpVerificationResult::NotFound => throw ValidationException::withMessages([
                'code' => 'No verification code found. Please request a new one.',
            ]),
            OtpVerificationResult::InvalidCode => throw ValidationException::withMessages([
                'code' => 'The code you entered is incorrect.',
            ]),
        };
    }

    public function resend(Request $request): RedirectResponse
    {
        $email = $request->session()->get(self::SESSION_KEY);

        if (! $email) {
            return to_route('login');
        }

        $this->otpService->generateAndSendFor($email);

        return back()->with('status', 'A new code has been sent to your email.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('login');
    }

    private function completeLogin(Request $request, string $email): RedirectResponse
    {
        $user = User::where('email', $email)->firstOrFail();

        if (! $user->email_verified_at) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        $request->session()->forget(self::SESSION_KEY);
        $request->session()->regenerate();

        Auth::login($user);

        return to_route('identity.show');
    }
}
