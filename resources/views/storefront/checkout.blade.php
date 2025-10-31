<x-app-layout title="Checkout">
    <section class="mx-auto max-w-7xl px-6 py-10 sm:px-10 lg:px-12">
        <div class="mb-8">
            <p class="text-xs uppercase tracking-widest text-slate-400">Paso final</p>
            <h1 class="text-3xl font-semibold text-white">Checkout seguro</h1>
            <p class="mt-2 text-sm text-slate-400">
                Completa tu información y te redireccionaremos a Wompi para el pago.
            </p>
        </div>

        {{-- Si el carrito está vacío mostramos un mensaje amable --}}
        @if($cart->items->isEmpty())
            <div class="mx-auto max-w-3xl px-6 py-16 text-center sm:px-10 lg:px-12">
                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-8">
                    <h2 class="text-xl font-semibold text-white">Tu carrito está vacío</h2>
                    <p class="mt-2 text-sm text-slate-400">Agrega productos al carrito antes de proceder al pago.</p>
                    <div class="mt-4">
                        <a href="{{ route('products.index') }}"
                           class="inline-block rounded-full bg-pink-500 px-4 py-2 text-sm font-semibold text-white">
                            Ver productos
                        </a>
                        <a href="{{ route('cart.index') }}"
                           class="ml-2 inline-block rounded-full border border-slate-700 px-4 py-2 text-sm text-slate-200">
                            Ir al carrito
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="grid gap-8 lg:grid-cols-[1.1fr_0.9fr]">
                {{-- FORMULARIO DE FACTURACIÓN --}}
                <div class="space-y-6">
                    <form method="POST" action="{{ route('checkout.store') }}"
                          class="space-y-6 rounded-3xl border border-slate-800 bg-slate-900/70 p-6 shadow-lg shadow-slate-950/20">
                        @csrf

                        {{-- DATOS DE FACTURACIÓN --}}
                        <div class="space-y-3">
                            <h2 class="text-lg font-semibold text-white">Datos de facturación</h2>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="text-xs uppercase tracking-widest text-slate-400">Nombre completo</label>
                                    <input type="text" name="billing[name]" value="{{ old('billing.name') }}"
                                           class="mt-1 w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-white
                                           focus:border-pink-500 focus:ring-pink-500/30" required>
                                </div>

                                <div>
                                    <label class="text-xs uppercase tracking-widest text-slate-400">Documento</label>
                                    <input type="text" name="billing[doc]" value="{{ old('billing.doc') }}"
                                           class="mt-1 w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-white
                                           focus:border-pink-500 focus:ring-pink-500/30" required>
                                </div>

                                <div>
                                    <label class="text-xs uppercase tracking-widest text-slate-400">Correo electrónico</label>
                                    <input type="email" name="billing[email]"
                                           value="{{ old('billing.email', auth()->user()->email ?? '') }}"
                                           class="mt-1 w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-white
                                           focus:border-pink-500 focus:ring-pink-500/30" required>
                                </div>

                                <div>
                                    <label class="text-xs uppercase tracking-widest text-slate-400">Teléfono</label>
                                    <input type="text" name="billing[phone]" value="{{ old('billing.phone') }}"
                                           class="mt-1 w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-white
                                           focus:border-pink-500 focus:ring-pink-500/30" required>
                                </div>
                            </div>
                        </div>

                        {{-- DETERMINAR SI HAY ITEMS FÍSICOS --}}
                        @php
                            $hasPhysical = $cart->items->contains(fn($item) => !($item->product->is_digital ?? false));
                        @endphp

                        <div class="space-y-3">
                            <h2 class="text-lg font-semibold text-white">
                                {{ $hasPhysical ? 'Dirección de envío' : 'Confirmación' }}
                            </h2>
                            <p class="text-xs text-slate-500">
                                {{ $hasPhysical
                                    ? 'Necesitamos tu dirección para entregar los productos físicos.'
                                    : 'Los productos digitales se enviarán al correo registrado.' }}
                            </p>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <label class="text-xs uppercase tracking-widest text-slate-400">Dirección</label>
                                    <input type="text" name="shipping[address]" value="{{ old('shipping.address') }}"
                                           class="mt-1 w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-white
                                           focus:border-pink-500 focus:ring-pink-500/30" {{ $hasPhysical ? 'required' : '' }}>
                                </div>

                                <div>
                                    <label class="text-xs uppercase tracking-widest text-slate-400">Ciudad</label>
                                    <input type="text" name="shipping[city]" value="{{ old('shipping.city') }}"
                                           class="mt-1 w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-white
                                           focus:border-pink-500 focus:ring-pink-500/30">
                                </div>

                                <div>
                                    <label class="text-xs uppercase tracking-widest text-slate-400">País</label>
                                    <input type="text" name="shipping[country]"
                                           value="{{ old('shipping.country', 'El Salvador') }}"
                                           class="mt-1 w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-white
                                           focus:border-pink-500 focus:ring-pink-500/30">
                                </div>

                                <div>
                                    <label class="text-xs uppercase tracking-widest text-slate-400">Código postal</label>
                                    <input type="text" name="shipping[zip]" value="{{ old('shipping.zip') }}"
                                           class="mt-1 w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-white
                                           focus:border-pink-500 focus:ring-pink-500/30">
                                </div>
                            </div>
                        </div>

                        {{-- BOTÓN PRINCIPAL --}}
                    <!-- Botón de pago con Wompi (Widget integrado en el formulario) -->
<div class="mt-6 flex justify-center">
    <div class="wompi_button_widget"
        data-url-pago="https://pagos.wompi.sv/IntentoPago/Redirect?id=e7a8177e-0690-497e-8300-d82fa984e061&esWidget=1"
        data-render="widget">
    </div>
</div>

                    </form>
                </div>

                {{-- RESUMEN DEL PEDIDO --}}
                <div class="space-y-6">
                    <div class="rounded-3xl border border-slate-800 bg-slate-900/70 p-6 shadow-lg shadow-slate-950/20">
                        <h3 class="text-lg font-semibold text-white">Resumen del pedido</h3>
                        <ul class="mt-4 space-y-3 text-sm text-slate-300">
                            @foreach($cart->items as $item)
                                <li class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-semibold text-white">{{ $item->product->title }}</p>
                                        <p class="text-xs text-slate-500">Cantidad: {{ $item->quantity }}</p>
                                    </div>
                                    <span class="text-sm text-white">
                                        {{ number_format((($item->lineTotal()?->amount ?? 0) / 100), 2) }}
                                        {{ $item->currency ?? $cart->currency ?? 'USD' }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>

                        <dl class="mt-4 space-y-2 border-t border-slate-800 pt-4 text-sm text-slate-300">
                            <div class="flex items-center justify-between">
                                <dt>Subtotal</dt>
                                <dd>{{ number_format((($cart->subtotal->amount ?? 0) / 100), 2) }} {{ $cart->currency ?? 'USD' }}</dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt>Descuento</dt>
                                <dd>-{{ number_format((($cart->discount->amount ?? 0) / 100), 2) }} {{ $cart->currency ?? 'USD' }}</dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt>IVA</dt>
                                <dd>{{ number_format((($cart->tax->amount ?? 0) / 100), 2) }} {{ $cart->currency ?? 'USD' }}</dd>
                            </div>
                            <div class="flex items-center justify-between text-lg font-semibold text-white">
                                <dt>Total</dt>
                                <dd>{{ number_format((($cart->total->amount ?? 0) / 100), 2) }} {{ $cart->currency ?? 'USD' }}</dd>
                            </div>
                        </dl>

                        <p class="mt-4 text-xs text-slate-500">
                            Tras completar el pago recibirás un correo con la confirmación y las claves digitales.
                        </p>
                    </div>
                </div>
            </div>

            {{-- BOTÓN DE WOMPI --}}
            <div class="mt-10 text-center">
                <h3 class="text-white font-semibold text-lg mb-3">O paga directamente con Wompi:</h3>
                <div class="wompi_button_widget"
                     data-url-pago="https://pagos.wompi.sv/IntentoPago/Redirect?id=e7a8177e-0690-497e-8300-d82fa984e061&esWidget=1"
                     data-render="widget">
                </div>
            </div>
        @endif
    </section>

    {{-- Script de Wompi (debe ir dentro del layout o aquí al final) --}}
    @push('scripts')
        <script src="https://pagos.wompi.sv/js/wompi.pagos.js"></script>
    @endpush
</x-app-layout>
