<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
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
            'product_id' => Product::factory(),
            'customer_name' => fake()->name(),
            'customer_phone' => '01'.fake()->numerify('########'),
            'customer_address' => fake()->address(),
            'quantity' => fake()->numberBetween(1, 3),
            'status' => 'new',
        ];
    }
}
