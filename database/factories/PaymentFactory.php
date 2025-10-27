<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement(\App\Enums\PaymentStatus::cases());

        return [
            'order_id' => \App\Models\Order::factory(),
            'provider' => 'WOMPI',
            'provider_reference' => 'PAY-'.$this->faker->unique()->hexadecimal(10),
            'status' => $status,
            'amount_cents' => (int) round($this->faker->randomFloat(2, 29.99, 399.99) * 100),
            'raw_payload' => ['sample' => true],
            'received_at' => $status->isTerminal() ? now() : null,
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (\App\Models\Payment $payment): void {
            if ($payment->order) {
                $payment->amount_cents = $payment->order->total_cents;
            }
        })->afterCreating(function (\App\Models\Payment $payment): void {
            if ($payment->order) {
                $payment->update(['amount_cents' => $payment->order->total_cents]);
            }
        });
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => \App\Enums\PaymentStatus::Pending, 'received_at' => null]);
    }

    public function approved(): static
    {
        return $this->state(fn () => ['status' => \App\Enums\PaymentStatus::Approved, 'received_at' => now()]);
    }
}
