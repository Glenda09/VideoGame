<x-app-layout :title="$product->title">
    <section class="mx-auto max-w-7xl px-6 py-10 sm:px-10 lg:px-12">
        <div class="grid gap-10 lg:grid-cols-[1.1fr_0.9fr]">
            <div>
                <div class="overflow-hidden rounded-3xl border border-slate-800 bg-slate-900/70">
                    <img src="{{ $product->coverImageUrl() }}" alt="Portada de {{ $product->title }}" class="h-96 w-full object-cover" />
                </div>
                @if ($product->images->isNotEmpty())
                    <div class="mt-4 grid grid-cols-4 gap-3">
                        @foreach ($product->images as $image)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($image->path) }}" alt="Imagen adicional de {{ $product->title }}" class="h-24 w-full rounded-2xl border border-slate-800 object-cover" loading="lazy" />
                        @endforeach
                    </div>
                @endif

                <div class="mt-10 rounded-3xl border border-slate-800 bg-slate-900/70 p-6 text-sm text-slate-300">
                    <h2 class="text-lg font-semibold text-white">Descripción</h2>
                    <div class="prose prose-invert mt-4 max-w-none text-slate-300">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-3xl border border-slate-800 bg-slate-900/70 p-6 shadow-xl shadow-slate-950/20">
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="rounded-full bg-pink-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-widest {{ $product->is_digital ? 'text-pink-300' : 'text-indigo-300' }}">
                            {{ $product->is_digital ? 'Digital' : 'Físico' }}
                        </span>
                        @if ($product->release_date)
                            <span class="text-xs uppercase tracking-widest text-slate-400">
                                Lanzamiento: {{ $product->release_date->format('d/m/Y') }}
                            </span>
                        @endif
                    </div>

                    <h1 class="mt-4 text-3xl font-semibold text-white">{{ $product->title }}</h1>
                    @if ($product->category)
                        <p class="mt-1 text-sm uppercase tracking-widest text-slate-400">{{ $product->category->name }}</p>
                    @endif

                    <div class="mt-4 flex flex-wrap items-center gap-4">
                        <x-price-tag :amount="$product->price_cents" :currency="$product->currency" :compact="false" />
                        <x-rating-stars :rating="$averageRating" :count="$reviewCount" size="md" />
                    </div>

                    <div class="mt-6 space-y-3 text-sm text-slate-300">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-pink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6l4 2" />
                            </svg>
                            <span>SKU: <span class="font-semibold text-white">{{ $product->sku }}</span></span>
                        </div>
                        @if ($product->platforms->isNotEmpty())
                            <div class="flex items-start gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-4 w-4 text-pink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($product->platforms as $platform)
                                        <span class="rounded-full bg-slate-800/60 px-3 py-1 text-xs uppercase tracking-wide text-slate-300">{{ $platform->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-pink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                            </svg>
                            <span>Disponibilidad: <span class="font-semibold text-white">{{ $product->hasStock() ? 'En stock' : 'Agotado' }}</span></span>
                        </div>
                    </div>

                    <div class="mt-6 space-y-3">
                        <form method="POST" action="{{ route('cart.items.store') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            @if ($product->is_digital)
                                <input type="hidden" name="quantity" value="1">
                            @else
                                <label class="flex items-center gap-3 rounded-full border border-slate-800 bg-slate-950/60 px-4 py-2 text-sm text-slate-200">
                                    <span class="text-xs uppercase tracking-widest text-slate-400">Cantidad</span>
                                    <input type="number" name="quantity" value="1" min="1" max="10" class="h-9 w-20 rounded-full border border-slate-700 bg-slate-900 px-3 text-sm text-white focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30">
                                </label>
                            @endif
                            <button
                                type="submit"
                                class="inline-flex flex-1 items-center justify-center gap-2 rounded-full bg-gradient-to-r from-pink-500 to-purple-600 px-5 py-3 text-sm font-semibold text-white shadow transition hover:from-pink-400 hover:to-purple-500 disabled:cursor-not-allowed disabled:opacity-60"
                                @disabled(!$product->is_digital && !$product->hasStock())
                            >
                                Anadir al carrito
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        </form>
                        <button type="button" class="inline-flex w-full items-center justify-center gap-2 rounded-full border border-slate-700 px-5 py-3 text-sm font-semibold text-slate-200 transition hover:border-pink-500 hover:text-white">
                            Agregar a wishlist
                        </button>
                    </div>
                </div>

                @if ($product->approvedReviews->isNotEmpty())
                    <div class="rounded-3xl border border-slate-800 bg-slate-900/70 p-6 shadow-lg shadow-slate-950/20">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-white">Reseñas</h2>
                            <span class="text-xs uppercase tracking-widest text-slate-500">{{ $reviewCount }} comentarios</span>
                        </div>
                        <div class="mt-4 space-y-4">
                            @foreach ($product->approvedReviews->take(4) as $review)
                                <div class="rounded-2xl border border-slate-800/70 bg-slate-900/60 p-4">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-semibold text-white">{{ $review->user?->name ?? 'Usuario' }}</span>
                                        <x-rating-stars :rating="$review->rating" size="xs" />
                                    </div>
                                    @if ($review->comment)
                                        <p class="mt-2 text-sm text-slate-300">{{ $review->comment }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <p class="mt-4 text-xs text-slate-500">¿Compraste este juego? Recibirás un correo para calificar tu experiencia una vez completado el pedido.</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    @if ($relatedProducts->isNotEmpty())
        <section class="mx-auto max-w-7xl px-6 pb-12 sm:px-10 lg:px-12">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-semibold text-white">También podría interesarte</h2>
                <a href="{{ route('products.index', ['category' => $product->category?->slug]) }}" class="inline-flex items-center gap-1 text-sm font-medium text-pink-300 hover:text-pink-200">
                    Ver más {{ $product->category?->name }}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
            <div class="mt-6 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                @foreach ($relatedProducts as $related)
                    <x-product-card :product="$related" />
                @endforeach
            </div>
        </section>
    @endif
</x-app-layout>



