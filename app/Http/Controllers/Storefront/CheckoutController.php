<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Cart\CartManager;
use App\Services\Checkout\OrderCreator;
use App\Services\Wompi\WompiClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class CheckoutController extends Controller
{
    public function index(CartManager ): RedirectResponse|View
    {
         = ->current()->load(['items.product', 'coupon']);

        if (->items->isEmpty()) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'Tu carrito está vacío.']);
        }

         = ->totals();

        return view('storefront.checkout', [
            'cart' => ,
            'totals' => ,
        ]);
    }

    public function store(
        Request ,
        CartManager ,
        OrderCreator ,
        WompiClient 
    ): RedirectResponse {
         = ->current()->load(['items.product', 'coupon']);

        if (->items->isEmpty()) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'Tu carrito está vacío.']);
        }

         = ->validate([
            'billing.name' => ['required', 'string', 'max:255'],
            'billing.doc' => ['required', 'string', 'max:50'],
            'billing.email' => ['required', 'email', 'max:255'],
            'billing.phone' => ['required', 'string', 'max:30'],
            'shipping.address' => ['nullable', 'string', 'max:255'],
            'shipping.city' => ['nullable', 'string', 'max:120'],
            'shipping.country' => ['nullable', 'string', 'max:120'],
            'shipping.zip' => ['nullable', 'string', 'max:30'],
        ]);

         = ->items->contains(fn () => !->product->is_digital);

        if ( && blank(data_get(, 'shipping.address'))) {
            return back()->withErrors(['shipping.address' => 'Necesitamos una dirección de envío para los productos físicos.'])->withInput();
        }

         = ->create(
            ,
            ['billing'],
             ? ['shipping'] : null,
        );

        try {
             = ->createTransaction();
        } catch (RuntimeException ) {
            return redirect()
                ->route('cart.index')
                ->withErrors(['cart' => 'No pudimos iniciar el pago en Wompi: '.->getMessage()]);
        }

         = ->payments()->latest()->first();
        if ( && isset(['data']['id'])) {
            ->update(['provider_reference' => ['data']['id']]);
        }

        if (!empty(['data']['redirect_url'] ?? ['redirect_url'] ?? null)) {
             = ['data']['redirect_url'] ?? ['redirect_url'];

            return redirect()->away();
        }

        return redirect()
            ->route('checkout.success', ['order' => ->number])
            ->with('status', 'Orden creada. Completa el pago para finalizar.');
    }

    public function success(Request ): View
    {
         = Order::query()->where('number', ->string('order'))->first();

        return view('storefront.checkout-success', [
            'order' => ,
        ]);
    }

    public function failure(Request ): View
    {
         = Order::query()->where('number', ->string('order'))->first();

        return view('storefront.checkout-failure', [
            'order' => ,
        ]);
    }
}
