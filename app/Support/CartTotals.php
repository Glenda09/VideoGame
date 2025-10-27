<?php

namespace App\Support;

use App\Models\Coupon;
use App\Models\CartItem;
use Illuminate\Support\Collection;

class CartTotals
{
    public function __construct(
        public readonly Money $subtotal,
        public readonly Money $discount,
        public readonly Money $tax,
        public readonly Money $total,
    ) {
    }

    /**
     * @param  Collection<int, CartItem>  $items
     */
    public static function fromItems(Collection $items, ?Coupon $coupon, float $vatRate, string $currency): self
    {
        $subtotalAmount = (int) $items->reduce(
            fn (int $carry, CartItem $item) => $carry + $item->lineTotal()->amount,
            0,
        );

        $subtotal = new Money($subtotalAmount, $currency);

        $discount = $coupon
            ? $coupon->applyTo($subtotal)
            : new Money(0, $currency);

        $taxable = max($subtotal->amount - $discount->amount, 0);

        $normalizedRate = $vatRate >= 1 ? $vatRate / 100 : $vatRate;
        $tax = new Money((int) round($taxable * $normalizedRate), $currency);

        $totalAmount = max($subtotal->amount - $discount->amount + $tax->amount, 0);
        $total = new Money($totalAmount, $currency);

        return new self($subtotal, $discount, $tax, $total);
    }

    public function toArray(): array
    {
        return [
            'subtotal_cents' => $this->subtotal->amount,
            'discount_cents' => $this->discount->amount,
            'tax_cents' => $this->tax->amount,
            'total_cents' => $this->total->amount,
        ];
    }
}
