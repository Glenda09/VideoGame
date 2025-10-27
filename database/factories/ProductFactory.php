<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
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
        $title = $this->faker->unique()->catchPhrase().' '.$this->faker->randomElement(['Edition', 'Bundle', 'Pack']);
        $price = $this->faker->randomFloat(2, 9.99, 129.99);

        return [
            'sku' => 'GM-'.$this->faker->unique()->bothify('????-#####'),
            'title' => $title,
            'slug' => \Str::slug($title.'-'.$this->faker->unique()->numberBetween(100, 9999)),
            'description' => $this->faker->paragraphs(3, true),
            'price_cents' => (int) round($price * 100),
            'currency' => 'USD',
            'is_digital' => $this->faker->boolean(50),
            'active' => true,
            'cover_image' => 'images/placeholders/game-cover.svg',
            'release_date' => $this->faker->dateTimeBetween('-2 years', '+6 months'),
            'category_id' => \App\Models\Category::factory(),
        ];
    }

    public function digital(): static
    {
        return $this->state(fn () => ['is_digital' => true]);
    }

    public function physical(): static
    {
        return $this->state(fn () => ['is_digital' => false]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['active' => false]);
    }
}
