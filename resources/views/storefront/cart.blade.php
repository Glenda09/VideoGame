<x-app-layout title="Carrito de compras">
    <section class="mx-auto max-w-7xl px-6 py-10 sm:px-10 lg:px-12">
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs uppercase tracking-widest text-slate-400">Tu selección</p>
                <h1 class="text-3xl font-semibold text-white">Carrito de compras</h1>
                <p class="mt-2 text-sm text-slate-400">Revisa tus productos antes de continuar al checkout seguro con Wompi.</p>
            </div>
            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 rounded-full border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:border-pink-500 hover:text-white">
                Seguir explorando
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>

        @if (session('status'))
            <div class="mb-6 rounded-2xl border border-pink-500/40 bg-pink-500/10 px-4 py-3 text-sm text-pink-200">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->has('cart'))
            <div class="mb-6 rounded-2xl border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                {{ $errors->first('cart') }}
            </div>
        @endif

        <div class="grid gap-8 lg:grid-cols-[1.15fr_0.85fr]">
            <div class="space-y-4">
                @forelse ($cart->items as $item)
                    <div class="rounded-3xl border border-slate-800 bg-slate-900/70 p-5 shadow-lg shadow-slate-950/20">
                        <div class="flex flex-col gap-5 md:flex-row md:items-center md:gap-6">
                            <div class="h-28 w-28 flex-shrink-0 overflow-hidden rounded-2xl border border-slate-800 bg-slate-800">
                                <img src="{{ $item->product->coverImageUrl() }}" alt="Portada {{ $item->product->title }}" class="h-full w-full object-cover" />
                            </div>
                            <div class="flex-1 space-y-2 text-sm text-slate-300">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <h2 class="text-lg font-semibold text-white">
                                        <a href="{{ route('products.show', $item->product) }}" class="hover:text-pink-200">{{ $item->product->title }}</a>
                                    </h2>
                                    <x-price-tag :amount="$item->unit_price_cents" :currency="$cart->currency" compact />
                                </div>
                                @if ($item->product->platforms->isNotEmpty())
                                    <div class="flex flex-wrap gap-2 text-xs text-slate-400">
                                        @foreach ($item->product->platforms as $platform)
                                            <span class="rounded-full bg-slate-800/60 px-2 py-1 uppercase tracking-wide">{{ $platform->name }}</span>
                                        @endforeach
                                    </div>
                                @endif
                                <p class="text-xs uppercase tracking-widest text-slate-500">SKU {{ $item->product->sku }}</p>
                            </div>
                        </div>
                        <div class="mt-4 flex flex-wrap items-center justify-between gap-4 border-t border-slate-800 pt-4 text-sm">
                            <form method="POST" action="{{ route('cart.items.update', $item) }}" class="flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <label for="quantity-{{ $item->id }}" class="text-xs uppercase tracking-widest text-slate-500">Cantidad</label>
                                <input type="number" min="1" max="10" id="quantity-{{ $item->id }}" name="quantity" value="{{ $item->quantity }}" class="h-9 w-16 rounded-xl border border-slate-700 bg-slate-950 text-center text-white focus:border-pink-500 focus:ring-pink-500/30" />
                                <button type="submit" class="inline-flex items-center gap-2 rounded-full border border-slate-700 px-3 py-1.5 text-xs font-semibold uppercase tracking-widest text-slate-300 transition hover:border-pink-500 hover:text-white">
                                    Actualizar
                                </button>
                            </form>

                            <form method="POST" action="{{ route('cart.items.destroy', $item) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-1 rounded-full border border-red-500/40 bg-red-500/10 px-3 py-1.5 text-xs font-semibold uppercase tracking-widest text-red-200 transition hover:border-red-500 hover:text-red-100">
                                    Eliminar
                                </button>
                            </form>

                            <div class="text-right text-sm text-slate-300">
                                <span class="text-xs uppercase tracking-widest text-slate-500">Subtotal</span>
                                <div class="text-lg font-semibold text-white">
                                    {{ number_format(($item->lineTotal()->amount)/100, 2) }} {{ $cart->currency }}
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-3xl border border-slate-800 bg-slate-900/70 p-8 text-center text-slate-300">
                        <p class="text-lg font-semibold text-white">Tu carrito está vacío.</p>
                        <p class="mt-2 text-sm text-slate-400">Agrega algunos juegos desde el catálogo para comenzar.</p>
                        <a href="{{ route('products.index') }}" class="mt-4 inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-pink-500 to-purple-600 px-4 py-2 text-sm font-semibold text-white hover:from-pink-400 hover:to-purple-500">
                            Ver catálogo
                        </a>
                    </div>
                @endforelse
            </div>

            <div class="space-y-6">
                <div class="rounded-3xl border border-slate-800 bg-slate-900/70 p-6 shadow-lg shadow-slate-950/20">
                    <h2 class="text-lg font-semibold text-white">Resumen</h2>
                    <dl class="mt-4 space-y-3 text-sm text-slate-300">
                        <div class="flex items-center justify-between">
                            <dt>Subtotal</dt>
                            <dd>{{ number_format($totals->subtotal->amount / 100, 2) }} {{ $cart->currency }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt>Descuento</dt>
                            <dd>-{{ number_format($totals->discount->amount / 100, 2) }} {{ $cart->currency }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt>IVA ({{ number_format($cart->vat_rate * 100, 0) }}%)</dt>
                            <dd>{{ number_format($totals->tax->amount / 100, 2) }} {{ $cart->currency }}</dd>
                        </div>
                    </dl>
                    <div class="mt-4 flex items-center justify-between border-t border-slate-800 pt-4 text-lg font-semibold text-white">
                        <span>Total</span>
                        <span>{{ number_format($totals->total->amount / 100, 2) }} {{ $cart->currency }}</span>
                    </div>
                    <a href="{{ route('checkout.index') }}" class="mt-6 inline-flex w-full items-center justify-center gap-2 rounded-full bg-gradient-to-r from-pink-500 to-purple-600 px-5 py-3 text-sm font-semibold text-white shadow hover:from-pink-400 hover:to-purple-500">
                        Ir al checkout seguro
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                    <p class="mt-3 text-xs text-slate-500">Pagos procesados con Wompi. Recibirás confirmación por correo y claves digitales al instante.</p>
                </div>

                <div class="rounded-3xl border border-slate-800 bg-slate-900/70 p-6 shadow-lg shadow-slate-950/20">
                    <h3 class="text-sm font-semibold uppercase tracking-widest text-slate-400">Cupón</h3>
                    @if ($errors->has('coupon'))
                        <div class="mt-3 rounded-xl border border-red-500/40 bg-red-500/10 px-3 py-2 text-xs text-red-200">
                            {{ $errors->first('coupon') }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('cart.coupon.apply') }}" class="mt-3 flex gap-2">
                        @csrf
                        <input type="text" name="code" value="{{ $cart->coupon_code }}" placeholder="GAMER10" class="flex-1 rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-white placeholder:text-slate-500 focus:border-pink-500 focus:ring-pink-500/30" />
                        <button type="submit" class="inline-flex items-center gap-2 rounded-full border border-slate-700 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-slate-200 transition hover:border-pink-500 hover:text-white">
                            Aplicar
                        </button>
                    </form>
                    @if ($cart->coupon_code)
                        <form method="POST" action="{{ route('cart.coupon.destroy') }}" class="mt-3">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1 rounded-full border border-slate-700 px-3 py-1.5 text-xs font-semibold uppercase tracking-widest text-slate-400 transition hover:border-red-500 hover:text-red-200">
                                Quitar cupón
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
