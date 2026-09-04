<?php

namespace Tests\Feature\Auth;

use App\Models\EmailOtp;
use App\Models\User;
use App\Notifications\SendOtpCode;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class OtpAuthControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_register_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertOk();
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
    }

    public function test_registering_with_valid_data_creates_unverified_user_and_sends_code(): void
    {
        Notification::fake();

        $response = $this->post('/register', [
            'first_name' => 'Alex',
            'last_name' => 'Rivera',
            'email' => 'alex@example.com',
            'phone' => '01700000000',
            'terms' => true,
        ]);

        $response->assertRedirect(route('otp.verify'));
        $this->assertGuest();

        $this->assertDatabaseHas('users', [
            'email' => 'alex@example.com',
            'email_verified_at' => null,
        ]);

        Notification::assertSentOnDemand(
            SendOtpCode::class,
            fn (SendOtpCode $notification, array $channels, object $notifiable) => $notifiable->routes['mail'] === 'alex@example.com'
        );
    }

    public function test_registering_with_an_email_already_taken_fails_validation(): void
    {
        User::factory()->create(['email' => 'alex@example.com']);

        $response = $this->post('/register', [
            'first_name' => 'Alex',
            'last_name' => 'Rivera',
            'email' => 'alex@example.com',
            'terms' => true,
        ]);

        $response->assertInvalid(['email']);
    }

    public function test_registering_without_accepting_terms_fails_validation(): void
    {
        $response = $this->post('/register', [
            'first_name' => 'Alex',
            'last_name' => 'Rivera',
            'email' => 'alex@example.com',
            'terms' => false,
        ]);

        $response->assertInvalid(['terms']);
    }

    public function test_login_with_unknown_email_fails_validation(): void
    {
        $response = $this->post('/login', ['email' => 'nobody@example.com']);

        $response->assertInvalid(['email']);
    }

    public function test_login_with_known_email_sends_code_and_redirects_to_verify(): void
    {
        Notification::fake();

        $user = User::factory()->create(['email' => 'alex@example.com']);

        $response = $this->post('/login', ['email' => $user->email]);

        $response->assertRedirect(route('otp.verify'));

        Notification::assertSentOnDemand(
            SendOtpCode::class,
            fn (SendOtpCode $notification, array $channels, object $notifiable) => $notifiable->routes['mail'] === 'alex@example.com'
        );
    }

    public function test_verifying_correct_code_authenticates_user_and_marks_email_verified(): void
    {
        $user = User::factory()->unverified()->create(['email' => 'alex@example.com']);
        $code = $this->sendCodeAndCapture($user->email);

        $response = $this->withSession(['otp_pending_email' => $user->email])
            ->post('/verify-otp', ['code' => $code]);

        $response->assertRedirect(route('identity.show'));
        $this->assertAuthenticatedAs($user);

        $this->assertNotNull($user->fresh()->email_verified_at);
        $this->assertDatabaseCount('email_otps', 0);
    }

    public function test_verifying_incorrect_code_returns_error_and_keeps_user_a_guest(): void
    {
        $user = User::factory()->unverified()->create(['email' => 'alex@example.com']);
        $this->sendCodeAndCapture($user->email);

        $response = $this->withSession(['otp_pending_email' => $user->email])
            ->post('/verify-otp', ['code' => '000000']);

        $response->assertInvalid(['code']);
        $this->assertGuest();

        $this->assertDatabaseHas('email_otps', ['email' => $user->email, 'attempts' => 1]);
    }

    public function test_verifying_after_five_wrong_attempts_reports_too_many_attempts(): void
    {
        $user = User::factory()->unverified()->create(['email' => 'alex@example.com']);
        $code = $this->sendCodeAndCapture($user->email);
        $wrongCode = $code === '111111' ? '222222' : '111111';

        for ($i = 0; $i < EmailOtp::MAX_ATTEMPTS; $i++) {
            $this->withSession(['otp_pending_email' => $user->email])
                ->post('/verify-otp', ['code' => $wrongCode]);
        }

        $response = $this->withSession(['otp_pending_email' => $user->email])
            ->post('/verify-otp', ['code' => $code]);

        $response->assertInvalid(['code']);
        $this->assertGuest();
    }

    public function test_verifying_an_expired_code_reports_expired(): void
    {
        $user = User::factory()->unverified()->create(['email' => 'alex@example.com']);
        $code = $this->sendCodeAndCapture($user->email);

        $this->travel(11)->minutes();

        $response = $this->withSession(['otp_pending_email' => $user->email])
            ->post('/verify-otp', ['code' => $code]);

        $response->assertInvalid(['code']);
        $this->assertGuest();
    }

    public function test_resending_code_issues_a_new_code_and_invalidates_the_old_one(): void
    {
        $user = User::factory()->unverified()->create(['email' => 'alex@example.com']);
        $firstCode = $this->sendCodeAndCapture($user->email);

        Notification::fake();

        $this->withSession(['otp_pending_email' => $user->email])
            ->post('/resend-otp')
            ->assertRedirect();

        Notification::assertSentOnDemand(
            SendOtpCode::class,
            fn (SendOtpCode $notification, array $channels, object $notifiable) => $notifiable->routes['mail'] === $user->email
        );

        $response = $this->withSession(['otp_pending_email' => $user->email])
            ->post('/verify-otp', ['code' => $firstCode]);

        $response->assertInvalid(['code']);
    }

    private function sendCodeAndCapture(string $email): string
    {
        Notification::fake();

        $code = null;

        $this->post('/login', ['email' => $email]);

        Notification::assertSentOnDemand(
            SendOtpCode::class,
            function (SendOtpCode $notification, array $channels, object $notifiable) use (&$code, $email) {
                if ($notifiable->routes['mail'] !== $email) {
                    return false;
                }

                $code = $notification->code;

                return true;
            }
        );

        Notification::fake();

        return $code;
    }
}
