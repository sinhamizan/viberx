<?php

namespace App\Http\Controllers;

use App\Enums\AssessmentStatus;
use App\Enums\BillingCadence;
use App\Enums\FunnelStep;
use App\Enums\OrderStatus;
use App\Http\Requests\StorePaymentMethodRequest;
use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    public function __construct(private readonly PaymentService $payments)
    {
        //
    }

    public function show(Request $request): Response|RedirectResponse
    {
        $order = $this->currentOrder($request);

        if (! $order) {
            return to_route('plans.index');
        }

        if ($order->assessment?->status !== AssessmentStatus::Submitted) {
            return to_route('assessment.show');
        }

        if (! $order->shippingAddress) {
            return to_route('shipping.show');
        }

        $setupIntent = $this->payments->createSetupIntent($request->user());

        return Inertia::render('Payment/Method', [
            'stripeKey' => config('cashier.key'),
            'clientSecret' => $setupIntent->client_secret,
            'estimatedTotalCents' => $order->billing_cadence === BillingCadence::Quarterly
                ? $order->plan->quarterly_price_cents
                : $order->plan->monthly_price_cents,
        ]);
    }

    public function store(StorePaymentMethodRequest $request): RedirectResponse
    {
        $order = $this->currentOrder($request);

        if (! $order) {
            return to_route('plans.index');
        }

        $paymentMethod = $this->payments->attachPaymentMethod(
            $request->user(),
            $request->validated('payment_method_id'),
        );

        $order->update([
            'stripe_setup_intent_id' => $request->validated('setup_intent_id'),
            'stripe_payment_method_id' => $paymentMethod->id,
            'card_brand' => $paymentMethod->card->brand,
            'card_last_four' => $paymentMethod->card->last4,
            'card_exp_month' => $paymentMethod->card->exp_month,
            'card_exp_year' => $paymentMethod->card->exp_year,
            'current_step' => FunnelStep::Review,
        ]);

        return to_route('review.show');
    }

    private function currentOrder(Request $request): ?Order
    {
        return $request->user()->orders()->where('status', OrderStatus::Draft)->latest()->first();
    }
}
