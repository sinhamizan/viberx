<?php

namespace App\Http\Controllers;

use App\Enums\AssessmentStatus;
use App\Enums\OrderStatus;
use App\Models\Assessment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReviewController extends Controller
{
    public function show(Request $request): Response|RedirectResponse
    {
        $order = $request->user()->orders()->latest()->first();

        if (! $order) {
            return to_route('plans.index');
        }

        if ($order->status !== OrderStatus::Draft) {
            return Inertia::render('Review/Show', [
                'submitted' => true,
                'status' => $order->status->value,
            ]);
        }

        if ($order->assessment?->status !== AssessmentStatus::Submitted) {
            return to_route('assessment.show');
        }

        if (! $order->shippingAddress) {
            return to_route('shipping.show');
        }

        if (! $order->stripe_payment_method_id) {
            return to_route('payment.show');
        }

        $user = $request->user();
        $shipping = $order->shippingAddress;

        return Inertia::render('Review/Show', [
            'submitted' => false,
            'plan' => [
                'name' => $order->plan->name,
                'tagline' => $order->plan->tagline,
                'billingCadence' => $order->billing_cadence->value,
            ],
            'account' => [
                'fullName' => trim("{$user->first_name} {$user->last_name}"),
                'email' => $user->email,
                'phone' => $user->phone,
            ],
            'shipping' => [
                'fullName' => $shipping->full_name,
                'addressLine1' => $shipping->address_line1,
                'city' => $shipping->city,
                'state' => $shipping->state,
                'postalCode' => $shipping->postal_code,
            ],
            'payment' => [
                'cardBrand' => $order->card_brand,
                'cardLastFour' => $order->card_last_four,
                'cardExpMonth' => $order->card_exp_month,
                'cardExpYear' => $order->card_exp_year,
            ],
            'assessmentSummary' => $this->assessmentSummary($order->assessment),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'consent' => ['accepted'],
        ]);

        $order = $request->user()->orders()->where('status', OrderStatus::Draft)->latest()->firstOrFail();

        $order->update([
            'status' => OrderStatus::UnderReview,
            'submitted_at' => now(),
        ]);

        return to_route('review.show');
    }

    /**
     * @return array{reportedDisclosures: string, consultationForm: string}
     */
    private function assessmentSummary(?Assessment $assessment): array
    {
        $conditions = $assessment?->health_conditions['conditions'] ?? [];
        $hasConditions = ! empty($conditions) && $conditions !== ['none'];

        return [
            'reportedDisclosures' => $hasConditions
                ? 'Reported: '.implode(', ', $conditions)
                : 'No active counter-indications reported',
            'consultationForm' => 'Completed & certified for secure medical review',
        ];
    }
}
