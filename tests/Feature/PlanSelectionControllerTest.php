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

    public function test_guests_without_a_confirmed_state_are_redirected_to_the_state_gate(): void
    {
        $response = $this->get('/plans');

        $response->assertRedirect(route('state.show'));
    }

    public function test_guests_with_a_confirmed_state_can_view_the_plans_page(): void
    {
        Plan::factory()->create();

        $response = $this->withSession(['treatment_state' => 'TX'])->get('/plans');

        $response->assertOk();
    }

    public function test_authenticated_user_can_view_the_plans_page(): void
    {
        $user = User::factory()->create();
        Plan::factory()->create();

        $response = $this->actingAs($user)
            ->withSession(['treatment_state' => 'TX'])
            ->get('/plans');

        $response->assertOk();
    }

    public function test_a_guest_selecting_a_plan_creates_a_userless_draft_order_and_is_sent_to_register(): void
    {
        $plan = Plan::factory()->create();

        $response = $this->withSession(['treatment_state' => 'TX'])->post('/plans', [
            'plan_id' => $plan->id,
            'billing_cadence' => 'quarterly',
        ]);

        $response->assertRedirect(route('register'));

        $this->assertDatabaseHas('orders', [
            'user_id' => null,
            'plan_id' => $plan->id,
            'state' => 'TX',
            'status' => OrderStatus::Draft->value,
        ]);
    }

    public function test_an_authenticated_user_selecting_a_plan_creates_a_draft_order_and_redirects_to_assessment(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        $response = $this->actingAs($user)
            ->withSession(['treatment_state' => 'TX'])
            ->post('/plans', [
                'plan_id' => $plan->id,
                'billing_cadence' => 'quarterly',
            ]);

        $response->assertRedirect(route('assessment.show'));

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'state' => 'TX',
            'status' => OrderStatus::Draft->value,
        ]);
    }

    public function test_the_plans_page_preselects_the_plan_intended_from_the_pricing_page(): void
    {
        $plan = Plan::factory()->create();

        $response = $this->withSession([
            'treatment_state' => 'TX',
            'intended_plan_id' => $plan->id,
        ])->get('/plans');

        $response->assertInertia(
            fn ($page) => $page->where('preselectedPlanId', $plan->id)
        );
    }

    public function test_a_user_with_an_existing_draft_order_is_sent_straight_to_assessment(): void
    {
        $user = User::factory()->create();
        Order::factory()->for($user)->create(['status' => OrderStatus::Draft]);

        $response = $this->actingAs($user)->get('/plans');

        $response->assertRedirect(route('assessment.show'));
    }
}
