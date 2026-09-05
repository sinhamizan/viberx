<?php

namespace Tests\Feature;

use App\Enums\AssessmentStatus;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\ShippingAddress;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ReviewControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function draftOrderReadyForReview(User $user): Order
    {
        $order = Order::factory()->for($user)->create(['status' => OrderStatus::Draft]);
        $order->assessment()->create([
            'status' => AssessmentStatus::Submitted,
            'health_conditions' => ['conditions' => ['none']],
        ]);
        ShippingAddress::factory()->for($order)->create();
        $order->update([
            'stripe_payment_method_id' => 'pm_test123',
            'card_brand' => 'visa',
            'card_last_four' => '4242',
            'card_exp_month' => 8,
            'card_exp_year' => 2028,
        ]);

        return $order->fresh();
    }

    public function test_users_without_any_order_are_redirected_to_plans(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/review');

        $response->assertRedirect(route('plans.index'));
    }

    public function test_users_with_an_unsubmitted_assessment_are_redirected_to_assessment(): void
    {
        $user = User::factory()->create();
        Order::factory()->for($user)->create(['status' => OrderStatus::Draft]);

        $response = $this->actingAs($user)->get('/review');

        $response->assertRedirect(route('assessment.show'));
    }

    public function test_users_without_a_shipping_address_are_redirected_to_shipping(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['status' => OrderStatus::Draft]);
        $order->assessment()->create(['status' => AssessmentStatus::Submitted]);

        $response = $this->actingAs($user)->get('/review');

        $response->assertRedirect(route('shipping.show'));
    }

    public function test_users_without_a_payment_method_are_redirected_to_payment(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['status' => OrderStatus::Draft]);
        $order->assessment()->create(['status' => AssessmentStatus::Submitted]);
        ShippingAddress::factory()->for($order)->create();

        $response = $this->actingAs($user)->get('/review');

        $response->assertRedirect(route('payment.show'));
    }

    public function test_the_summary_page_can_be_rendered_once_everything_is_complete(): void
    {
        $user = User::factory()->create();
        $this->draftOrderReadyForReview($user);

        $response = $this->actingAs($user)->get('/review');

        $response->assertOk();
    }

    public function test_submitting_without_consent_fails_validation(): void
    {
        $user = User::factory()->create();
        $this->draftOrderReadyForReview($user);

        $response = $this->actingAs($user)->post('/review', []);

        $response->assertInvalid(['consent']);
    }

    public function test_submitting_with_consent_marks_the_order_under_review(): void
    {
        $user = User::factory()->create();
        $order = $this->draftOrderReadyForReview($user);

        $response = $this->actingAs($user)->post('/review', ['consent' => true]);

        $response->assertRedirect(route('review.show'));

        $order->refresh();
        $this->assertSame(OrderStatus::UnderReview, $order->status);
        $this->assertNotNull($order->submitted_at);
    }

    public function test_visiting_review_after_submission_shows_the_confirmation(): void
    {
        $user = User::factory()->create();
        $this->draftOrderReadyForReview($user);
        $this->actingAs($user)->post('/review', ['consent' => true]);

        $response = $this->actingAs($user)->get('/review');

        $response->assertOk();
    }
}
