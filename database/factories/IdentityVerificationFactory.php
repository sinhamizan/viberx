<?php

namespace Database\Factories;

use App\Enums\DocumentType;
use App\Enums\IdentityVerificationStatus;
use App\Models\IdentityVerification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IdentityVerification>
 */
class IdentityVerificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'legal_first_name' => fake()->firstName(),
            'legal_last_name' => fake()->lastName(),
            'document_type' => fake()->randomElement(DocumentType::cases()),
            'document_number' => fake()->bothify('??########'),
            'document_expiry_date' => fake()->dateTimeBetween('+1 year', '+5 years'),
            'front_photo_path' => 'identity-documents/placeholder-front.jpg',
            'back_photo_path' => 'identity-documents/placeholder-back.jpg',
            'status' => IdentityVerificationStatus::InProgress,
        ];
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => IdentityVerificationStatus::Verified,
            'verified_at' => now(),
        ]);
    }

    public function skipped(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => IdentityVerificationStatus::Skipped,
        ]);
    }
}
