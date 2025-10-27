<x-app-layout title="Checkout">
    <section class="mx-auto max-w-7xl px-6 py-10 sm:px-10 lg:px-12">
        <div class="mb-8">
            <p class="text-xs uppercase tracking-widest text-slate-400">Paso final</p>
            <h1 class="text-3xl font-semibold text-white">Checkout seguro</h1>
            <p class="mt-2 text-sm text-slate-400">Completa tu información y te redireccionaremos a Wompi para el pago.</p>
        </div>

        <div class="grid gap-8 lg:grid-cols-[1.1fr_0.9fr]">
            <div class="space-y-6">
                <form method="POST" action="{{ route('checkout.store') }}" class="space-y-6 rounded-3xl border border-slate-800 bg-slate-900/70 p-6 shadow-lg shadow-slate-950/20">
                    @csrf

                    <div class="space-y-3">
                        <h2 class="text-lg font-semibold text-white">Datos de facturación</h2>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="text-xs uppercase tracking-widest text-slate-400">Nombre completo</label>
                                <input type="text" name="billing[name]" value="{{ old('billing.name') }}" class="mt-1 w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-white focus:border-pink-500 focus:ring-pink-500/30" required>
                                @error('billing.name')<p class="mt-1 text-xs text-red-300">{{  }}</p>@enderror
                            </div>
                            <div>
                                <label class="text-xs uppercase tracking-widest text-slate-400">Documento</label>
                                <input type="text" name="billing[doc]" value="{{ old('billing.doc') }}" class="mt-1 w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-white focus:border-pink-500 focus:ring-pink-500/30" required>
                                @error('billing.doc')<p class="mt-1 text-xs text-red-300">{{  }}</p>@enderror
                            </div>
                            <div>
                                <label class="text-xs uppercase tracking-widest text-slate-400">Correo electrónico</label>
                                <input type="email" name="billing[email]" value="{{ old('billing.email', auth()->user()->email ?? '') }}" class="mt-1 w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-white focus:border-pink-500 focus:ring-pink-500/30" required>
                                @error('billing.email')<p class="mt-1 text-xs text-red-300">{{  }}</p>@enderror
                            </div>
                            <div>
                                <label class="text-xs uppercase tracking-widest text-slate-400">Teléfono</label>
                                <input type="text" name="billing[phone]" value="{{ old('billing.phone') }}" class="mt-1 w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-white focus:border-pink-500 focus:ring-pink-500/30" required>
                                @error('billing.phone')<p class="mt-1 text-xs text-red-300">{{  }}</p>@enderror
                            </div>
                        </div>
                    </div>

                    @php  = ->items->contains(fn () => !->product->is_digital); @endphp

                    <div class="space-y-3">
                        <h2 class="text-lg font-semibold text-white">{{  ? 'Dirección de envío' : 'Confirmación' }}</h2>
                        <p class="text-xs text-slate-500">{{  ? 'Necesitamos tu dirección para entregar los productos físicos.' : 'Los productos digitales se enviarán al correo registrado.' }}</p>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label class="text-xs uppercase tracking-widest text-slate-400">Dirección</label>
                                <input type="text" name="shipping[address]" value="{{ old('shipping.address') }}" class="mt-1 w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-white focus:border-pink-500 focus:ring-pink-500/30" {{  ? 'required' : '' }}>
                                @error('shipping.address')<p class="mt-1 text-xs text-red-300">{{  }}</p>@enderror
                            </div>
                            <div>
                                <label class="text-xs uppercase tracking-widest text-slate-400">Ciudad</label>
                                <input type="text" name="shipping[city]" value="{{ old('shipping.city') }}" class="mt-1 w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-white focus:border-pink-500 focus:ring-pink-500/30">
                            </div>
                            <div>
                                <label class="text-xs uppercase tracking-widest text-slate-400">País</label>
                                <input type="text" name="shipping[country]" value="{{ old('shipping.country', 'Colombia') }}" class="mt-1 w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-white focus:border-pink-500 focus:ring-pink-500/30">
                            </div>
                            <div>
                                <label class="text-xs uppercase tracking-widest text-slate-400">Código postal</label>
                                <input type="text" name="shipping[zip]" value="{{ old('shipping.zip') }}" class="mt-1 w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-white focus:border-pink-500 focus:ring-pink-500/30">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-pink-500 to-purple-600 px-5 py-3 text-sm font-semibold text-white shadow hover:from-pink-400 hover:to-purple-500">
                        Proceder al pago con Wompi
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </form>
            </div>

            <div class="space-y-6">
                <div class="rounded-3xl border border-slate-800 bg-slate-900/70 p-6 shadow-lg shadow-slate-950/20">
                    <h3 class="text-lg font-semibold text-white">Resumen del pedido</h3>
                    <ul class="mt-4 space-y-3 text-sm text-slate-300">
                        @foreach (->items as )
                            <li class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-white">{{ ->product->title }}</p>
                                    <p class="text-xs text-slate-500">Cantidad: {{ ->quantity }}</p>
                                </div>
                                <span class="text-sm text-white">{{ number_format(->lineTotal()->amount / 100, 2) }} {{ ->currency }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <dl class="mt-4 space-y-2 border-t border-slate-800 pt-4 text-sm text-slate-300">
                        <div class="flex items-center justify-between"><dt>Subtotal</dt><dd>{{ number_format(->subtotal->amount / 100, 2) }} {{ ->currency }}</dd></div>
                        <div class="flex items-center justify-between"><dt>Descuento</dt><dd>-{{ number_format(->discount->amount / 100, 2) }} {{ ->currency }}</dd></div>
                        <div class="flex items-center justify-between"><dt>IVA</dt><dd>{{ number_format(->tax->amount / 100, 2) }} {{ ->currency }}</dd></div>
                        <div class="flex items-center justify-between text-lg font-semibold text-white"><dt>Total</dt><dd>{{ number_format(->total->amount / 100, 2) }} {{ ->currency }}</dd></div>
                    </dl>
                    <p class="mt-4 text-xs text-slate-500">Tras completar el pago recibirás un correo con la confirmación y las claves digitales.</p>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
