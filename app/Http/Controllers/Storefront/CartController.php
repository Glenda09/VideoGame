<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\Cart\CartManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class CartController extends Controller
{
    public function index(CartManager $cartManager): View
    {
        $cart = $cartManager->current()->load(['items.product.platforms', 'coupon']);
        $totals = $cartManager->totals();

        return view('storefront.cart', [
            'cart' => $cart,
            'totals' => $totals,
        ]);
    }

    public function store(Request $request, CartManager $cartManager): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:10'],
        ]);

        $product = Product::query()->active()->findOrFail($data['product_id']);

        try {
            $cartManager->add($product, $data['quantity'] ?? 1);
        } catch (RuntimeException $exception) {
            return back()->withErrors(['cart' => $exception->getMessage()]);
        }

        return redirect()
            ->route('cart.index')
            ->with('status', 'Producto agregado al carrito.');
    }

    public function update(Request $request, CartItem $cartItem, CartManager $cartManager): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:10'],
        ]);

        try {
            $cartManager->update($cartItem, $data['quantity']);
        } catch (RuntimeException $exception) {
            return back()->withErrors(['cart' => $exception->getMessage()]);
        }

        return redirect()
            ->route('cart.index')
            ->with('status', 'Carrito actualizado.');
    }

    public function destroy(CartItem $cartItem, CartManager $cartManager): RedirectResponse
    {
        $cartManager->remove($cartItem);

        return redirect()
            ->route('cart.index')
            ->with('status', 'Producto eliminado del carrito.');
    }

    public function applyCoupon(Request $request, CartManager $cartManager): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50'],
        ]);

        try {
            $cartManager->applyCoupon($data['code']);
        } catch (RuntimeException $exception) {
            return back()->withErrors(['coupon' => $exception->getMessage()]);
        } catch (\Throwable $exception) {
            return back()->withErrors(['coupon' => 'El cupón no es válido o expiró.']);
        }

        return redirect()
            ->route('cart.index')
            ->with('status', 'Cupón aplicado correctamente.');
    }

    public function removeCoupon(CartManager $cartManager): RedirectResponse
    {
        $cartManager->removeCoupon();

        return redirect()
            ->route('cart.index')
            ->with('status', 'Cupón eliminado.');
    }
}
