<?php

namespace App\Http\Controllers;

use App\Enums\FunnelStep;
use App\Enums\OrderStatus;
use App\Http\Requests\StorePlanSelectionRequest;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PlanSelectionController extends Controller
{
    public function index(Request $request): Response|RedirectResponse
    {
        $hasDraftOrder = $request->user()->orders()
            ->where('status', OrderStatus::Draft)
            ->exists();

        if ($hasDraftOrder) {
            return to_route('assessment.show');
        }

        return Inertia::render('Plans/Select', [
            'plans' => Plan::orderBy('sort_order')->get(),
        ]);
    }

    public function store(StorePlanSelectionRequest $request): RedirectResponse
    {
        $request->user()->orders()->create([
            'plan_id' => $request->validated('plan_id'),
            'billing_cadence' => $request->validated('billing_cadence'),
            'status' => OrderStatus::Draft,
            'current_step' => FunnelStep::Assessment,
        ]);

        return to_route('assessment.show');
    }
}
