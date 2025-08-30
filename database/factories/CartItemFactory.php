<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartItemFactory extends Factory
{
    public function definition(): array
    {
        $product = Product::factory()->create();
        $quantity = fake()->numberBetween(1, 5);

        return [
            'session_id' => fake()->uuid(),
            'product_id' => $product->id,
            'quantity' => $quantity,
            'unit_price' => $product->price,
            'total_price' => $product->price * $quantity,
        ];
    }

    /**
     * Indicate that the cart item belongs to an authenticated user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn(array $attributes) => [
            'session_id' => null,
        ]);
    }

    /**
     * Indicate that the cart item is for a guest user (session-based).
     */
    public function forSession(string $sessionId): static
    {
        return $this->state(fn(array $attributes) => [
            'session_id' => $sessionId,
        ]);
    }

    /**
     * Indicate that the cart item has specific options.
     */
    public function withOptions(array $options): static
    {
        return $this->state(fn(array $attributes) => [
            'options' => $options,
        ]);
    }
}
