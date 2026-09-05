<?php

namespace Database\Factories;

use App\Enums\AssessmentStatus;
use App\Models\Assessment;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Assessment>
 */
class AssessmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'status' => AssessmentStatus::Pending,
        ];
    }
}
