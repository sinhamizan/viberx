<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\ShippingAddress;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ShippingAddress>
 */
class ShippingAddressFactory extends Factory
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
            'full_name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'address_line1' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'postal_code' => fake()->postcode(),
        ];
    }
}
