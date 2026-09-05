<?php

namespace Database\Factories;

use App\Enums\BillingCadence;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'plan_id' => Plan::factory(),
            'billing_cadence' => fake()->randomElement(BillingCadence::cases()),
            'status' => OrderStatus::Draft,
        ];
    }
}
