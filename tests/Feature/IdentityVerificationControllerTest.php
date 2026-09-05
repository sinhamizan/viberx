<?php

namespace Tests\Feature;

use App\Enums\IdentityVerificationStatus;
use App\Models\IdentityVerification;
use App\Models\User;
use App\Services\DocumentOcrService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class IdentityVerificationControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get('/identity-verification');

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_the_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/identity-verification');

        $response->assertOk();
    }

    public function test_submitting_valid_documents_stores_an_in_progress_verification(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/identity-verification', [
            'legal_first_name' => 'Alex',
            'legal_last_name' => 'Rivera',
            'document_type' => 'drivers_license',
            'document_number' => '2345-0987-2026',
            'document_expiry_date' => now()->addYear()->toDateString(),
            'front_photo' => UploadedFile::fake()->image('front.jpg'),
            'back_photo' => UploadedFile::fake()->image('back.jpg'),
        ]);

        $response->assertRedirect(route('identity.show'));

        $this->assertDatabaseHas('identity_verifications', [
            'user_id' => $user->id,
            'legal_first_name' => 'Alex',
            'legal_last_name' => 'Rivera',
            'document_type' => 'drivers_license',
            'status' => IdentityVerificationStatus::InProgress->value,
        ]);

        $verification = IdentityVerification::first();
        Storage::disk('local')->assertExists($verification->front_photo_path);
        Storage::disk('local')->assertExists($verification->back_photo_path);
    }

    public function test_submitting_without_required_fields_fails_validation(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/identity-verification', []);

        $response->assertInvalid([
            'legal_first_name',
            'legal_last_name',
            'document_type',
            'document_number',
            'document_expiry_date',
            'front_photo',
            'back_photo',
        ]);
    }

    public function test_confirming_a_readable_document_marks_it_verified(): void
    {
        $this->mock(DocumentOcrService::class, function ($mock) {
            $mock->shouldReceive('looksLikeReadableDocument')->once()->andReturn(true);
        });

        $user = User::factory()->create();
        $verification = IdentityVerification::factory()->for($user)->create();

        $response = $this->actingAs($user)->post('/identity-verification/confirm');

        $response->assertRedirect(route('identity.show'));

        $this->assertSame(
            IdentityVerificationStatus::Verified,
            $verification->fresh()->status,
        );
        $this->assertNotNull($verification->fresh()->verified_at);
    }

    public function test_confirming_an_unreadable_document_marks_it_rejected(): void
    {
        $this->mock(DocumentOcrService::class, function ($mock) {
            $mock->shouldReceive('looksLikeReadableDocument')->once()->andReturn(false);
        });

        $user = User::factory()->create();
        $verification = IdentityVerification::factory()->for($user)->create();

        $response = $this->actingAs($user)->post('/identity-verification/confirm');

        $response->assertRedirect(route('identity.show'));

        $verification->refresh();
        $this->assertSame(IdentityVerificationStatus::Rejected, $verification->status);
        $this->assertNotNull($verification->rejection_reason);
        $this->assertNull($verification->verified_at);
    }

    public function test_skipping_marks_verification_as_skipped_and_redirects_to_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/identity-verification/skip');

        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('identity_verifications', [
            'user_id' => $user->id,
            'status' => IdentityVerificationStatus::Skipped->value,
            'document_type' => null,
        ]);
    }
}
