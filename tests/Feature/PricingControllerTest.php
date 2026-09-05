<?php

namespace Tests\Feature;

use App\Models\Plan;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PricingControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_the_pricing_page_can_be_rendered_by_guests(): void
    {
        Plan::factory()->count(3)->create();

        $response = $this->get('/pricing');

        $response->assertOk();
    }
}
