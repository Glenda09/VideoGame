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
use App\Support\Money;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    /**
     * Mostrar la página de checkout con el carrito y totales.
     */
    public function index(CartManager $cartManager): RedirectResponse|View
    {
        $cart = $cartManager->current()->load(['items.product', 'coupon']);

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('status', 'Tu carrito está vacío. Agrega productos antes de continuar al checkout.');
        }

        $totals = $cartManager->totals();

        return view('storefront.checkout', [
            'cart' => $cart,
            'totals' => $totals,
        ]);
    }

    /**
     * Procesar el checkout: validar, crear orden y redirigir a Wompi (sandbox).
     */
    public function store(
        Request $request,
        CartManager $cartManager,
        OrderCreator $orderCreator,
        WompiClient $wompi
    ): RedirectResponse {
        // Cargar carrito actual
        $cart = $cartManager->current()->load(['items.product', 'coupon']);
        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'Tu carrito está vacío.']);
        }

        // Validaciones del formulario (ajusta campos según tu formulario)
        $validated = $request->validate([
            'billing.name' => ['required', 'string', 'max:255'],
            'billing.doc' => ['required', 'string', 'max:50'],
            'billing.email' => ['required', 'email', 'max:255'],
            'billing.phone' => ['required', 'string', 'max:30'],
            'shipping.address' => ['nullable', 'string', 'max:255'],
            'shipping.city' => ['nullable', 'string', 'max:120'],
            'shipping.country' => ['nullable', 'string', 'max:120'],
            'shipping.zip' => ['nullable', 'string', 'max:30'],
        ]);

        // Determinar si hay items físicos
        $hasPhysical = $cart->items->contains(fn ($item) => ! ($item->product->is_digital ?? false));

        if ($hasPhysical && blank(data_get($validated, 'shipping.address'))) {
            return back()->withErrors(['shipping.address' => 'Necesitamos una dirección de envío para los productos físicos.'])->withInput();
        }

        // Crear la orden localmente (ajusta la firma si tu OrderCreator es diferente)
        $order = $orderCreator->create(
            $cart,
            data_get($validated, 'billing', []),
            $hasPhysical ? data_get($validated, 'shipping', []) : null
        );

        // -----------------------------
        // NORMALIZAR Y OBTENER UN Money
        // -----------------------------
        $totalMoney = null;

        // 1) Si el order tiene accessor total y es Money
        if (isset($order) && ($order->total instanceof Money)) {
            $totalMoney = $order->total;
        }

        // 2) Intentar desde $cartManager->totals()
        if (is_null($totalMoney)) {
            try {
                $totals = $cartManager->totals();
            } catch (\Throwable $e) {
                $totals = null;
            }

            if (is_array($totals) && isset($totals['total']) && $totals['total'] instanceof Money) {
                $totalMoney = $totals['total'];
            } elseif (is_object($totals) && isset($totals->total) && $totals->total instanceof Money) {
                $totalMoney = $totals->total;
            }
        }

        // 3) Si aún no tenemos Money, revisar si total viene como número (cents o float)
        if (is_null($totalMoney)) {
            // Posibles ubicaciones: $order->total_cents, $order->total_cents ?? $order->total_amount etc.
            if (isset($order->total_cents)) {
                $amountCents = (int) $order->total_cents;
                $currency = $order->currency ?? 'USD';
                $totalMoney = new Money($amountCents, $currency);
            } elseif (isset($order->total) && is_numeric($order->total)) {
                // si por alguna razón total es numérico (por ejemplo 118.43)
                $amountCents = (int) round(floatval($order->total) * 100);
                $currency = $order->currency ?? 'USD';
                $totalMoney = new Money($amountCents, $currency);
            }
        }

        // 4) Si sigue sin Money válido, abortamos
        if (is_null($totalMoney) || !($totalMoney instanceof Money)) {
            Log::warning('No se pudo obtener Money para el total del pedido', [
                'order_id' => $order->id ?? null,
                'cart_id' => $cart->id ?? null,
            ]);

            return redirect()->route('cart.index')->withErrors(['cart' => 'No fue posible calcular el total del pedido. Por favor revisa tu carrito.']);
        }

        // ---------------
        // OBTENER CENTAVOS
        // ---------------
        // En tu implementación Money->amount ya viene en centavos, así que lo usamos directamente.
        $amountInCents = (int) ($totalMoney->amount ?? 0);
        $currency = $totalMoney->currency ?? ($order->currency ?? 'USD');

        if ($amountInCents <= 0) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'El monto a cobrar debe ser mayor que 0.']);
        }

        // Preparar payload para Wompi (ajusta campos según la firma real de tu cliente)
        $reference = 'ORDER-' . ($order->number ?? $order->id ?? uniqid());
        $payload = [
            'amount_in_cents' => $amountInCents,
            'currency' => $currency,
            'reference' => $reference,
            'customer_email' => data_get($validated, 'billing.email') ?? ($request->user()->email ?? 'test@example.com'),
            'redirect_url' => env('WOMPI_REDIRECT_URL', url('/checkout/success')),
            // otros campos que tu WompiClient necesite...
        ];

        // Llamada a Wompi (capturamos errores)
        try {
            $response = $wompi->createTransaction($payload);
        } catch (RuntimeException $e) {
            Log::error('Wompi createTransaction failed', ['exception' => $e, 'order_id' => $order->id ?? null]);
            return redirect()
                ->route('cart.index')
                ->withErrors(['cart' => 'No pudimos iniciar el pago en Wompi: ' . $e->getMessage()]);
        }

        // Guardar respuesta cruda en la orden (si tus columnas existen; si no, el try/catch evita fallos)
        try {
            // Si tienes columnas wompi_response/wompi_reference/status en la tabla orders, usa update:
            $order->wompi_response = $response;
            $order->wompi_reference = $reference;
            $order->status = 'pending_payment';
            $order->save();
        } catch (\Throwable $ex) {
            Log::warning('No se pudo guardar la respuesta de Wompi en la orden', ['ex' => $ex]);
        }

        // Extraer URL de redirección desde la respuesta (ajusta según la estructura real)
        $redirectUrl = data_get($response, 'data.authorization_url')
            ?? data_get($response, 'data.attributes.redirect_url')
            ?? data_get($response, 'data.redirect_url')
            ?? data_get($response, 'redirect_url');

        if (!empty($redirectUrl)) {
            return redirect()->away($redirectUrl);
        }

        // Fallback: redirigir a success con orden creada
        return redirect()
            ->route('checkout.success', ['order' => $order->number ?? $order->id])
            ->with('status', 'Orden creada. Completa el pago para finalizar.');
    }

    /**
     * Página de éxito (Wompi redirige aquí si configuras `WOMPI_REDIRECT_URL`)
     */
    public function success(Request $request): View
    {
        $orderNumber = $request->query('order') ?? $request->input('order');
        $order = Order::query()
            ->where('number', $orderNumber)
            ->orWhere('id', $orderNumber)
            ->first();

        return view('storefront.checkout-success', [
            'order' => $order,
        ]);
    }

    /**
     * Página de fallo / cancelación
     */
    public function failure(Request $request): View
    {
        $orderNumber = $request->query('order') ?? $request->input('order');
        $order = Order::query()
            ->where('number', $orderNumber)
            ->orWhere('id', $orderNumber)
            ->first();

        return view('storefront.checkout-failure', [
            'order' => $order,
        ]);
    }
}
