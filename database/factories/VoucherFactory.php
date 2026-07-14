<?php

namespace Database\Factories;

use App\Models\Voucher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Voucher>
 */
class VoucherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('VCH###??')),
            'type' => 'percentage',
            'value' => 10,
            'expires_at' => null,
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the voucher is a fixed-amount discount.
     */
    public function fixed(float $value = 10): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'fixed',
            'value' => $value,
        ]);
    }

    /**
     * Indicate that the voucher is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subDay(),
        ]);
    }

    /**
     * Indicate that the voucher is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
