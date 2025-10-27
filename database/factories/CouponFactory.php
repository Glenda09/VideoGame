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
        $type = $this->faker->randomElement([\App\Enums\CouponType::Fixed, \App\Enums\CouponType::Percent]);

        return [
            'code' => strtoupper($this->faker->unique()->bothify('SAVE##??')),
            'type' => $type,
            'value' => $type === \App\Enums\CouponType::Fixed
                ? $this->faker->numberBetween(5, 25)
                : $this->faker->numberBetween(5, 30),
            'starts_at' => now()->subDays($this->faker->numberBetween(0, 10)),
            'ends_at' => $this->faker->optional(0.5)->dateTimeBetween('+5 days', '+2 months'),
            'usage_limit' => $this->faker->optional(0.3)->numberBetween(50, 500),
            'used_count' => 0,
            'active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['active' => false]);
    }
}
