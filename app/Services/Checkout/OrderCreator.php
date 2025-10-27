<?php

namespace App\Services\Checkout;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Str;

class OrderCreator
{
    public function __construct(protected DatabaseManager $database)
    {
    }

    public function create(Cart $cart, array $billingData, ?array $shippingData = null): Order
    {
        $cart->loadMissing(['items.product', 'coupon']);

        return $this->database->transaction(function () use ($cart, $billingData, $shippingData): Order {
            $totals = $cart->totals();

            $order = Order::query()->create([
                'user_id' => $cart->user_id,
                'subtotal_cents' => $totals->subtotal->amount,
                'discount_cents' => $totals->discount->amount,
                'tax_cents' => $totals->tax->amount,
                'total_cents' => $totals->total->amount,
                'currency' => $cart->currency,
                'status' => OrderStatus::RequiresPayment,
                'coupon_code' => $cart->coupon_code,
                'billing_data' => $billingData,
                'shipping_data' => $shippingData,
            ]);

            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'title_snapshot' => $item->product->title,
                    'unit_price_cents' => $item->unit_price_cents,
                    'quantity' => $item->quantity,
                    'is_digital' => $item->product->is_digital,
                ]);
            }

            $order->payments()->create([
                'provider' => 'WOMPI',
                'provider_reference' => Str::uuid()->toString(),
                'status' => PaymentStatus::Pending,
                'amount_cents' => $totals->total->amount,
            ]);

            $cart->clear();

            return $order->load(['items', 'payments']);
        });
    }
}
