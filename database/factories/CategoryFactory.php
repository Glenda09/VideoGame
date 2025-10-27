<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);

        return [
            'name' => ucwords($name),
            'slug' => \Str::slug($name.'-'.$this->faker->unique()->numberBetween(1, 9999)),
            'parent_id' => null,
        ];
    }

    public function child(?\App\Models\Category $parent = null): static
    {
        return $this->state(function () use ($parent): array {
            return [
                'parent_id' => $parent?->id ?? \App\Models\Category::factory(),
            ];
        });
    }
}
