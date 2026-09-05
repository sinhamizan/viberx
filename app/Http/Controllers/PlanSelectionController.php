<?php

namespace App\Http\Controllers;

use App\Enums\FunnelStep;
use App\Enums\OrderStatus;
use App\Http\Requests\StorePlanSelectionRequest;
use App\Models\Order;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PlanSelectionController extends Controller
{
    public function index(Request $request): Response|RedirectResponse
    {
        $user = $request->user();

        if ($user && $user->orders()->where('status', OrderStatus::Draft)->exists()) {
            return to_route('assessment.show');
        }

        if (! $request->session()->has('treatment_state')) {
            return to_route('state.show');
        }

        return Inertia::render('Plans/Select', [
            'plans' => Plan::orderBy('sort_order')->get(),
            'preselectedPlanId' => $request->session()->get('intended_plan_id'),
        ]);
    }

    public function store(StorePlanSelectionRequest $request): RedirectResponse
    {
        $attributes = [
            'plan_id' => $request->validated('plan_id'),
            'billing_cadence' => $request->validated('billing_cadence'),
            'state' => $request->session()->get('treatment_state'),
            'status' => OrderStatus::Draft,
            'current_step' => FunnelStep::Assessment,
        ];

        $user = $request->user();

        if ($user) {
            $user->orders()->create($attributes);

            return to_route('assessment.show');
        }

        $order = Order::create($attributes);
        $request->session()->put('pending_order_id', $order->id);

        return to_route('register');
    }
}
