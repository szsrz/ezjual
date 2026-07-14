<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(3, true),
            'tagline' => fake()->sentence(),
            'description' => fake()->paragraphs(3, true),
            'benefits' => [fake()->sentence(), fake()->sentence(), fake()->sentence()],
            'testimonials' => [
                ['name' => fake()->name(), 'content' => fake()->sentence()],
            ],
            'price' => fake()->randomFloat(2, 10, 500),
            'status' => 'draft',
        ];
    }

    /**
     * Indicate that the product is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
        ]);
    }
}
