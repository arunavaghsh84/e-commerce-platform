<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $discountType = fake()->randomElement(['percentage', 'fixed']);
        $discountValue = $discountType === 'percentage' ? fake()->numberBetween(5, 50) : fake()->randomFloat(2, 5, 100);

        return [
            'code' => strtoupper(fake()->bothify('??-####')),
            'name' => fake()->words(3, true),
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'valid_from' => now(),
            'valid_until' => now()->addMonths(3),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the coupon is expired.
     */
    public function expired(): static
    {
        return $this->state(fn(array $attributes) => [
            'valid_until' => now()->subDays(1),
        ]);
    }

    /**
     * Indicate that the coupon is not yet active.
     */
    public function notYetActive(): static
    {
        return $this->state(fn(array $attributes) => [
            'valid_from' => now()->addDays(1),
        ]);
    }

    /**
     * Indicate that the coupon is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }
}
