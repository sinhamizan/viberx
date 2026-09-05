<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class StateGateControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guests_can_view_the_state_gate(): void
    {
        $response = $this->get('/state');

        $response->assertOk();
    }

    public function test_authenticated_users_with_a_draft_order_skip_straight_to_assessment(): void
    {
        $user = User::factory()->create();
        Order::factory()->for($user)->create(['status' => OrderStatus::Draft]);

        $response = $this->actingAs($user)->get('/state');

        $response->assertRedirect(route('assessment.show'));
    }

    public function test_confirming_an_available_state_stores_it_in_session_and_redirects_to_plans(): void
    {
        $response = $this->post('/state', ['state' => 'TX']);

        $response->assertRedirect(route('plans.index'));
        $response->assertSessionHas('treatment_state', 'TX');
    }

    public function test_confirming_an_unavailable_state_fails_validation(): void
    {
        $response = $this->post('/state', ['state' => 'LA']);

        $response->assertInvalid(['state']);
    }

    public function test_confirming_an_unknown_state_code_fails_validation(): void
    {
        $response = $this->post('/state', ['state' => 'ZZ']);

        $response->assertInvalid(['state']);
    }

    public function test_visiting_with_a_plan_query_param_stores_the_intended_plan_id(): void
    {
        $plan = Plan::factory()->create();

        $this->get("/state?plan={$plan->id}");

        $this->assertSame($plan->id, session('intended_plan_id'));
    }

    public function test_an_invalid_plan_query_param_is_ignored(): void
    {
        $this->get('/state?plan=999999');

        $this->assertNull(session('intended_plan_id'));
    }
}
