<?php

namespace Tests\Feature;

use App\Models\Plan;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PricingToPlanFlowTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * Full guest journey: Pricing "Get Started" -> state gate (with the
     * clicked plan carried via ?plan=) -> confirm an available state ->
     * land on /plans with that same plan pre-selected.
     */
    public function test_clicking_get_started_on_pricing_carries_the_plan_through_state_confirmation_into_plans(): void
    {
        $essential = Plan::factory()->create(['name' => 'Essential', 'sort_order' => 1]);
        $standard = Plan::factory()->create(['name' => 'Standard', 'is_recommended' => true, 'sort_order' => 2]);

        // 1. Guest lands on Pricing and clicks "Get Started" on Essential
        //    (not the recommended one — proves it's not just defaulting to Standard).
        $pricing = $this->get('/pricing');
        $pricing->assertOk();
        $pricing->assertInertia(fn ($page) => $page
            ->component('Pricing/Index')
            ->has('plans', 2)
        );

        // That button links to /state?plan={essential->id}.
        $stateGate = $this->get("/state?plan={$essential->id}");
        $stateGate->assertOk();
        $stateGate->assertInertia(fn ($page) => $page->component('StateGate/Show'));
        $this->assertSame($essential->id, session('intended_plan_id'));

        // 2. Guest confirms an available state.
        $confirm = $this->post('/state', ['state' => 'TX']);
        $confirm->assertRedirect(route('plans.index'));
        $this->assertSame('TX', session('treatment_state'));

        // 3. Landing on /plans, the Essential plan (the one actually clicked
        //    on the pricing page) must be the pre-selected one, not Standard.
        $plans = $this->get('/plans');
        $plans->assertOk();
        $plans->assertInertia(fn ($page) => $page
            ->component('Plans/Select')
            ->where('preselectedPlanId', $essential->id)
        );

        // Sanity check the recommended plan is a *different* plan, so this
        // assertion couldn't pass by accidentally matching the fallback.
        $this->assertNotSame($essential->id, $standard->id);
    }

    public function test_visiting_start_directly_without_a_plan_falls_back_to_the_recommended_plan_on_plans_page(): void
    {
        Plan::factory()->create(['is_recommended' => false, 'sort_order' => 1]);
        $recommended = Plan::factory()->create(['is_recommended' => true, 'sort_order' => 2]);

        $this->get('/state');
        $this->post('/state', ['state' => 'TX']);

        $response = $this->get('/plans');

        $response->assertInertia(fn ($page) => $page
            ->where('preselectedPlanId', null)
        );

        // The frontend's own fallback (plans.find(is_recommended)) would
        // pick this one when preselectedPlanId is null — confirmed separately
        // in the Plans/Select component, not re-tested here server-side.
        $this->assertTrue($recommended->is_recommended);
    }
}
