<?php

namespace Tests\Feature;

use App\Enums\AssessmentStatus;
use App\Enums\FunnelStep;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\ShippingAddress;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Laravel\Cashier\PaymentMethod;
use Stripe\PaymentMethod as StripePaymentMethod;
use Stripe\SetupIntent;
use Tests\TestCase;

class PaymentControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function draftOrderReadyForPayment(User $user): Order
    {
        $order = Order::factory()->for($user)->create(['status' => OrderStatus::Draft]);
        $order->assessment()->create(['status' => AssessmentStatus::Submitted]);
        ShippingAddress::factory()->for($order)->create();

        return $order;
    }

    public function test_users_without_a_draft_order_are_redirected_to_plans(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/payment');

        $response->assertRedirect(route('plans.index'));
    }

    public function test_users_with_an_unsubmitted_assessment_are_redirected_to_assessment(): void
    {
        $user = User::factory()->create();
        Order::factory()->for($user)->create(['status' => OrderStatus::Draft]);

        $response = $this->actingAs($user)->get('/payment');

        $response->assertRedirect(route('assessment.show'));
    }

    public function test_users_without_a_shipping_address_are_redirected_to_shipping(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['status' => OrderStatus::Draft]);
        $order->assessment()->create(['status' => AssessmentStatus::Submitted]);

        $response = $this->actingAs($user)->get('/payment');

        $response->assertRedirect(route('shipping.show'));
    }

    public function test_the_page_can_be_rendered_once_shipping_is_set(): void
    {
        $user = User::factory()->create();
        $this->draftOrderReadyForPayment($user);

        $setupIntent = SetupIntent::constructFrom([
            'id' => 'seti_test123',
            'client_secret' => 'seti_test123_secret_abc',
        ]);

        $this->mock(PaymentService::class, function ($mock) use ($setupIntent) {
            $mock->shouldReceive('createSetupIntent')->once()->andReturn($setupIntent);
        });

        $response = $this->actingAs($user)->get('/payment');

        $response->assertOk();
    }

    public function test_submitting_a_payment_method_saves_it_and_advances_the_order(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['stripe_id' => 'cus_test123'])->save();
        $order = $this->draftOrderReadyForPayment($user);

        $stripePaymentMethod = StripePaymentMethod::constructFrom([
            'id' => 'pm_test123',
            'customer' => 'cus_test123',
            'card' => ['brand' => 'visa', 'last4' => '4242', 'exp_month' => 8, 'exp_year' => 2028],
        ]);
        $cashierPaymentMethod = new PaymentMethod($user, $stripePaymentMethod);

        $this->mock(PaymentService::class, function ($mock) use ($cashierPaymentMethod) {
            $mock->shouldReceive('attachPaymentMethod')
                ->once()
                ->with(\Mockery::type(User::class), 'pm_test123')
                ->andReturn($cashierPaymentMethod);
        });

        $response = $this->actingAs($user)->post('/payment', [
            'setup_intent_id' => 'seti_test123',
            'payment_method_id' => 'pm_test123',
        ]);

        $response->assertRedirect(route('review.show'));

        $order->refresh();
        $this->assertSame('seti_test123', $order->stripe_setup_intent_id);
        $this->assertSame('pm_test123', $order->stripe_payment_method_id);
        $this->assertSame('visa', $order->card_brand);
        $this->assertSame('4242', $order->card_last_four);
        $this->assertSame(8, $order->card_exp_month);
        $this->assertSame(2028, $order->card_exp_year);
        $this->assertSame(FunnelStep::Review, $order->current_step);
    }

    public function test_submitting_without_a_payment_method_id_fails_validation(): void
    {
        $user = User::factory()->create();
        $this->draftOrderReadyForPayment($user);

        $response = $this->actingAs($user)->post('/payment', []);

        $response->assertInvalid(['setup_intent_id', 'payment_method_id']);
    }
}
