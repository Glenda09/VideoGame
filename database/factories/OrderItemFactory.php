<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => \App\Models\Order::factory(),
            'product_id' => \App\Models\Product::factory(),
            'title_snapshot' => $this->faker->catchPhrase(),
            'unit_price_cents' => $this->faker->numberBetween(1999, 12999),
            'quantity' => $this->faker->numberBetween(1, 3),
            'is_digital' => false,
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (\App\Models\OrderItem $item): void {
            if ($item->product) {
                $item->unit_price_cents = $item->product->price_cents;
                $item->title_snapshot = $item->product->title;
                $item->is_digital = $item->product->is_digital;
            }
        })->afterCreating(function (\App\Models\OrderItem $item): void {
            if ($item->product) {
                $item->update([
                    'unit_price_cents' => $item->product->price_cents,
                    'title_snapshot' => $item->product->title,
                    'is_digital' => $item->product->is_digital,
                ]);
            }
        });
    }
}
