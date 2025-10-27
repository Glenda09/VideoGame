<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CartItem>
 */
class CartItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cart_id' => \App\Models\Cart::factory(),
            'product_id' => \App\Models\Product::factory(),
            'unit_price_cents' => $this->faker->numberBetween(1999, 12999),
            'quantity' => $this->faker->numberBetween(1, 3),
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (\App\Models\CartItem $item): void {
            if ($item->product) {
                $item->unit_price_cents = $item->product->price_cents;
            }
        })->afterCreating(function (\App\Models\CartItem $item): void {
            if ($item->product && $item->unit_price_cents !== $item->product->price_cents) {
                $item->update(['unit_price_cents' => $item->product->price_cents]);
            }
        });
    }
}
