<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Inertia\Inertia;
use Inertia\Response;

class PricingController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Pricing/Index', [
            'plans' => Plan::orderBy('sort_order')->get(),
        ]);
    }
}
