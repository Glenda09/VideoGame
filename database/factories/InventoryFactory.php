<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Inventory>
 */
class InventoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => \App\Models\Product::factory()->physical(),
            'quantity' => $this->faker->numberBetween(0, 150),
        ];
    }
}
