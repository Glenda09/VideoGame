<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Platform>
 */
class PlatformFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement([
            'PlayStation 5',
            'Xbox Series X',
            'Nintendo Switch',
            'PC',
            'Steam Deck',
            'PlayStation 4',
            'Xbox One',
        ]);

        return [
            'name' => $name,
            'slug' => \Str::slug($name),
        ];
    }
}
