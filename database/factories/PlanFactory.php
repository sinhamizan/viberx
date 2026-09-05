<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Plan>
 */
class PlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $monthlyPrice = fake()->randomElement([9900, 13900, 18900]);

        return [
            'name' => fake()->unique()->words(2, true),
            'slug' => fake()->unique()->slug(2),
            'tagline' => fake()->sentence(4),
            'doses_per_month' => fake()->randomElement([8, 12, 18]),
            'monthly_price_cents' => $monthlyPrice,
            'quarterly_price_cents' => ($monthlyPrice - 2000) * 3,
            'is_recommended' => false,
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
