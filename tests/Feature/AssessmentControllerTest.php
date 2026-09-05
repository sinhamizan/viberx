<?php

namespace Tests\Feature;

use App\Enums\AssessmentStatus;
use App\Enums\OrderStatus;
use App\Models\IdentityVerification;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AssessmentControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function completeAnswers(): array
    {
        return [
            'personal_info' => [
                'date_of_birth' => '1990-01-01',
                'sex' => 'female',
                'height_in' => 65,
                'weight_lb' => 140,
                'address_line1' => '123 Main St',
                'city' => 'Los Angeles',
                'state' => 'CA',
                'postal_code' => '90026',
            ],
            'medical_history' => ['notes' => 'None'],
            'medications' => ['notes' => 'None'],
            'allergies' => ['notes' => 'None'],
            'prior_treatments' => ['notes' => 'None'],
            'health_conditions' => ['conditions' => ['none']],
            'goals' => ['notes' => 'Better sleep'],
        ];
    }

    public function test_users_without_a_draft_order_are_redirected_to_plans(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/assessment');

        $response->assertRedirect(route('plans.index'));
    }

    public function test_the_page_can_be_rendered(): void
    {
        $user = User::factory()->create();
        Order::factory()->for($user)->create(['status' => OrderStatus::Draft]);

        $response = $this->actingAs($user)->get('/assessment');

        $response->assertOk();
    }

    public function test_submitting_incomplete_answers_fails_validation(): void
    {
        $user = User::factory()->create();
        Order::factory()->for($user)->create(['status' => OrderStatus::Draft]);

        $response = $this->actingAs($user)->post('/assessment', []);

        $response->assertInvalid([
            'personal_info.date_of_birth',
            'medical_history.notes',
            'health_conditions.conditions',
            'goals.notes',
        ]);
    }

    public function test_submitting_complete_answers_redirects_to_identity_when_not_yet_verified(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['status' => OrderStatus::Draft]);

        $response = $this->actingAs($user)->post('/assessment', $this->completeAnswers());

        $response->assertRedirect(route('identity.show'));

        $assessment = $order->fresh()->assessment;
        $this->assertSame(AssessmentStatus::Submitted, $assessment->status);
        $this->assertNotNull($assessment->submitted_at);
        $this->assertSame('Los Angeles', $assessment->personal_info['city']);
    }

    public function test_submitting_complete_answers_redirects_to_dashboard_when_already_verified(): void
    {
        $user = User::factory()->create();
        IdentityVerification::factory()->for($user)->verified()->create();
        Order::factory()->for($user)->create(['status' => OrderStatus::Draft]);

        $response = $this->actingAs($user)->post('/assessment', $this->completeAnswers());

        $response->assertRedirect(route('dashboard'));
    }
}
