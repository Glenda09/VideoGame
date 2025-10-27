<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cart>
 */
class CartFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'guest_token' => null,
            'currency' => config('store.currency', 'USD'),
            'vat_rate' => config('store.vat_rate', 0.19),
            'coupon_code' => null,
            'expires_at' => $this->faker->optional(0.1)->dateTimeBetween('+1 day', '+1 week'),
        ];
    }

    public function guest(): static
    {
        return $this->state(fn () => [
            'user_id' => null,
            'guest_token' => \Str::uuid()->toString(),
        ]);
    }
}
