<?php

namespace App\Http\Controllers;

use App\Enums\AssessmentStatus;
use App\Enums\FunnelStep;
use App\Enums\OrderStatus;
use App\Http\Requests\StoreShippingAddressRequest;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ShippingAddressController extends Controller
{
    public function show(Request $request): Response|RedirectResponse
    {
        $order = $this->currentOrder($request);

        if (! $order) {
            return to_route('plans.index');
        }

        if ($order->assessment?->status !== AssessmentStatus::Submitted) {
            return to_route('assessment.show');
        }

        $user = $request->user();
        $shipping = $order->shippingAddress;

        return Inertia::render('Shipping/Address', [
            'address' => [
                'full_name' => $shipping?->full_name ?? trim("{$user->first_name} {$user->last_name}"),
                'email' => $shipping?->email ?? $user->email,
                'phone' => $shipping?->phone ?? $user->phone ?? '',
                'address_line1' => $shipping?->address_line1 ?? '',
                'city' => $shipping?->city ?? '',
                'state' => $shipping?->state ?? '',
                'postal_code' => $shipping?->postal_code ?? '',
            ],
        ]);
    }

    public function store(StoreShippingAddressRequest $request): RedirectResponse
    {
        $order = $this->currentOrder($request);

        if (! $order) {
            return to_route('plans.index');
        }

        $order->shippingAddress()->updateOrCreate([], $request->validated());
        $order->update(['current_step' => FunnelStep::Payment]);

        return to_route('payment.show');
    }

    private function currentOrder(Request $request): ?Order
    {
        return $request->user()->orders()->where('status', OrderStatus::Draft)->latest()->first();
    }
}
