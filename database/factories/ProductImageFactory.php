<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\ProductImage>
 */
class ProductImageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => \App\Models\Product::factory(),
            'path' => 'images/placeholders/gallery-'.$this->faker->numberBetween(1, 6).'.svg',
            'sort_order' => $this->faker->numberBetween(1, 5),
        ];
    }
}
