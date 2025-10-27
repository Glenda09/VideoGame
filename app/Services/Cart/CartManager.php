<?php

namespace App\Services\Cart;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use App\Support\CartTotals;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use RuntimeException;

class CartManager
{
    public function __construct(
        protected Request $request,
        protected DatabaseManager $database
    ) {
    }

    public function current(): Cart
    {
        if (Auth::check()) {
            return $this->forUser(Auth::user());
        }

        return $this->forGuest();
    }

    public function add(Product $product, int $quantity = 1): CartItem
    {
        $cart = $this->current();

        if (!$product->hasStock($quantity) && !$product->is_digital) {
            throw new RuntimeException('No hay inventario suficiente para este producto.');
        }

        return $cart->addProduct($product, $quantity)->load('product');
    }

    public function update(CartItem $item, int $quantity): void
    {
        $cart = $this->current();

        if ($item->cart_id !== $cart->id) {
            throw new RuntimeException('El artículo no pertenece a este carrito.');
        }

        if ($quantity < 1) {
            $cart->removeProduct($item->product);

            return;
        }

        if (!$item->product->hasStock($quantity) && !$item->product->is_digital) {
            throw new RuntimeException('No hay inventario suficiente para este producto.');
        }

        $cart->updateProductQuantity($item->product, $quantity);
    }

    public function remove(CartItem $item): void
    {
        $cart = $this->current();

        if ($item->cart_id !== $cart->id) {
            return;
        }

        $cart->removeProduct($item->product);
    }

    public function applyCoupon(string $code): Coupon
    {
        $cart = $this->current();

        $coupon = Coupon::query()
            ->code($code)
            ->valid()
            ->firstOrFail();

        $cart->applyCoupon($coupon);

        return $coupon;
    }

    public function removeCoupon(): void
    {
        $this->current()->removeCoupon();
    }

    public function totals(): CartTotals
    {
        $cart = $this->current()->load(['items.product', 'coupon']);

        return $cart->totals();
    }

    public function clear(): void
    {
        $this->current()->clear();
    }

    protected function forGuest(): Cart
    {
        $token = $this->request->session()->get('cart_token');

        if (!$token) {
            $token = (string) Str::uuid();
            $this->request->session()->put('cart_token', $token);
        }

        $cart = Cart::query()
            ->with('items.product')
            ->forGuest($token)
            ->first();

        if (!$cart) {
            $cart = Cart::create([
                'guest_token' => $token,
                'currency' => config('store.currency', 'USD'),
                'vat_rate' => config('store.vat_rate', 0.19),
            ]);
        }

        return $cart->loadMissing(['items.product', 'coupon']);
    }

    protected function forUser(User $user): Cart
    {
        $user->loadMissing('activeCart.items.product');
        $cart = $user->activeCart ?? $user->carts()->create([
            'currency' => config('store.currency', 'USD'),
            'vat_rate' => config('store.vat_rate', 0.19),
        ]);

        if ($guestCart = $this->guestCartIfAny()) {
            $this->mergeCarts($guestCart, $cart);
            $this->forgetGuestToken();
        }

        return $cart->load(['items.product', 'coupon']);
    }

    protected function guestCartIfAny(): ?Cart
    {
        $token = $this->request->session()->get('cart_token');

        if (!$token) {
            return null;
        }

        return Cart::query()->forGuest($token)->with('items.product')->first();
    }

    protected function mergeCarts(Cart $source, Cart $destination): void
    {
        $this->database->transaction(function () use ($source, $destination): void {
            foreach ($source->items as $item) {
                $existing = $destination->items->firstWhere('product_id', $item->product_id);

                if ($existing) {
                    $destination->updateProductQuantity($existing->product, $existing->quantity + $item->quantity);
                } else {
                    $destination->items()->create([
                        'product_id' => $item->product_id,
                        'unit_price_cents' => $item->unit_price_cents,
                        'quantity' => $item->quantity,
                    ]);
                }
            }

            if ($source->coupon_code && !$destination->coupon_code) {
                $destination->update(['coupon_code' => $source->coupon_code]);
            }

            $source->items()->delete();
            $source->delete();
        });
    }

    protected function forgetGuestToken(): void
    {
        $this->request->session()->forget('cart_token');
    }
}
