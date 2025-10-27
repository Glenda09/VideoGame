<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $currency = 'USD';
        $subtotal = (int) round($this->faker->randomFloat(2, 29.99, 399.99) * 100);
        $discount = $this->faker->boolean(40)
            ? (int) round($subtotal * $this->faker->randomFloat(2, 0.05, 0.2))
            : 0;

        $taxable = max($subtotal - $discount, 0);
        $tax = (int) round($taxable * 0.19);
        $total = max($subtotal - $discount + $tax, 0);

        $status = $this->faker->randomElement(\App\Enums\OrderStatus::cases());

        return [
            'number' => null,
            'user_id' => \App\Models\User::factory(),
            'subtotal_cents' => $subtotal,
            'discount_cents' => $discount,
            'tax_cents' => $tax,
            'total_cents' => $total,
            'currency' => $currency,
            'status' => $status,
            'coupon_code' => null,
            'billing_data' => [
                'name' => $this->faker->name(),
                'doc' => $this->faker->bothify('#########'),
                'email' => $this->faker->safeEmail(),
                'phone' => $this->faker->e164PhoneNumber(),
            ],
            'shipping_data' => $this->faker->boolean(60) ? [
                'address' => $this->faker->streetAddress(),
                'city' => $this->faker->city(),
                'country' => $this->faker->country(),
                'zip' => $this->faker->postcode(),
            ] : null,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn () => ['status' => \App\Enums\OrderStatus::Paid]);
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => \App\Enums\OrderStatus::Pending]);
    }
}
