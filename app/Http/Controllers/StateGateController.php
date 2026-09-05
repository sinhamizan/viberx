<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Requests\StoreStateRequest;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StateGateController extends Controller
{
    public function show(Request $request): Response|RedirectResponse
    {
        $user = $request->user();

        if ($user && $user->orders()->where('status', OrderStatus::Draft)->exists()) {
            return to_route('assessment.show');
        }

        if ($request->has('plan') && Plan::whereKey($request->integer('plan'))->exists()) {
            $request->session()->put('intended_plan_id', $request->integer('plan'));
        }

        $resumeRoute = $request->session()->has('pending_order_id')
            ? route('register')
            : null;

        return Inertia::render('StateGate/Show', [
            'states' => collect(config('states'))
                ->map(fn (array $state, string $code) => [
                    'code' => $code,
                    'name' => $state['name'],
                    'available' => $state['available'],
                ])
                ->values(),
            'resumeRoute' => $resumeRoute,
        ]);
    }

    public function store(StoreStateRequest $request): RedirectResponse
    {
        $request->session()->put('treatment_state', $request->validated('state'));

        return to_route('plans.index');
    }
}
