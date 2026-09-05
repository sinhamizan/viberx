<?php

namespace Tests\Feature;

use App\Enums\AssessmentStatus;
use App\Enums\FunnelStep;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ShippingAddressControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function shippingPayload(): array
    {
        return [
            'full_name' => 'Alex Rivera',
            'email' => 'alex@example.com',
            'phone' => '01700000000',
            'address_line1' => '123 Main St',
            'city' => 'Los Angeles',
            'state' => 'CA',
            'postal_code' => '90026',
        ];
    }

    public function test_users_without_a_draft_order_are_redirected_to_plans(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/shipping');

        $response->assertRedirect(route('plans.index'));
    }

    public function test_users_with_an_unsubmitted_assessment_are_redirected_to_assessment(): void
    {
        $user = User::factory()->create();
        Order::factory()->for($user)->create(['status' => OrderStatus::Draft]);

        $response = $this->actingAs($user)->get('/shipping');

        $response->assertRedirect(route('assessment.show'));
    }

    public function test_the_page_can_be_rendered_once_assessment_is_submitted(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['status' => OrderStatus::Draft]);
        $order->assessment()->create(['status' => AssessmentStatus::Submitted]);

        $response = $this->actingAs($user)->get('/shipping');

        $response->assertOk();
    }

    public function test_submitting_a_valid_address_saves_it_and_advances_the_order(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['status' => OrderStatus::Draft]);
        $order->assessment()->create(['status' => AssessmentStatus::Submitted]);

        $response = $this->actingAs($user)->post('/shipping', $this->shippingPayload());

        $response->assertRedirect(route('payment.show'));

        $this->assertDatabaseHas('shipping_addresses', [
            'order_id' => $order->id,
            'full_name' => 'Alex Rivera',
            'city' => 'Los Angeles',
        ]);
        $this->assertSame(FunnelStep::Payment, $order->fresh()->current_step);
    }

    public function test_submitting_an_incomplete_address_fails_validation(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['status' => OrderStatus::Draft]);
        $order->assessment()->create(['status' => AssessmentStatus::Submitted]);

        $response = $this->actingAs($user)->post('/shipping', []);

        $response->assertInvalid(['full_name', 'email', 'phone', 'address_line1', 'city', 'state', 'postal_code']);
    }
}
