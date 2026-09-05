<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PlanSelectionControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get('/plans');

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_the_plans_page(): void
    {
        $user = User::factory()->create();
        Plan::factory()->create();

        $response = $this->actingAs($user)->get('/plans');

        $response->assertOk();
    }

    public function test_selecting_a_plan_creates_a_draft_order_and_redirects_to_assessment(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        $response = $this->actingAs($user)->post('/plans', [
            'plan_id' => $plan->id,
            'billing_cadence' => 'quarterly',
        ]);

        $response->assertRedirect(route('assessment.show'));

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'billing_cadence' => 'quarterly',
            'status' => OrderStatus::Draft->value,
        ]);
    }

    public function test_a_user_with_an_existing_draft_order_is_sent_straight_to_assessment(): void
    {
        $user = User::factory()->create();
        Order::factory()->for($user)->create(['status' => OrderStatus::Draft]);

        $response = $this->actingAs($user)->get('/plans');

        $response->assertRedirect(route('assessment.show'));
    }
}
