<x-app-layout title="Inicio">
    <section class="relative overflow-hidden">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top,_rgba(236,72,153,0.18),_transparent_55%),radial-gradient(circle_at_bottom,_rgba(59,130,246,0.15),_transparent_50%)]"></div>
        <div class="mx-auto max-w-7xl px-6 py-16 sm:py-20 lg:px-12">
            <div class="grid gap-12 lg:grid-cols-[1.15fr_0.85fr] lg:items-center">
                <div class="space-y-6">
                    <span class="inline-flex items-center rounded-full bg-pink-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-pink-300">
                        Nueva temporada
                    </span>
                    <h1 class="font-display text-4xl font-bold leading-tight text-white sm:text-5xl">
                        Descubre los videojuegos que están rompiendo el ranking esta semana.
                    </h1>
                    <p class="max-w-xl text-base text-slate-200">
                        GameStore reúne preventas, ediciones especiales y clásicos remasterizados para todas las plataformas. Compra con total seguridad usando Wompi y recibe claves digitales o juegos físicos listos para la aventura.
                    </p>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-pink-500 to-purple-600 px-5 py-3 text-sm font-semibold text-white shadow hover:from-pink-400 hover:to-purple-500">
                            Explorar catálogo
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3" />
                            </svg>
                        </a>
                        <a href="{{ route('products.index', ['sort' => 'rating']) }}" class="inline-flex items-center gap-2 rounded-full border border-slate-700/80 px-5 py-3 text-sm font-semibold text-slate-200 transition hover:border-pink-500 hover:text-white">
                            Ver top rating
                        </a>
                    </div>
                    <dl class="grid max-w-lg grid-cols-3 gap-3 text-center text-xs uppercase tracking-widest text-slate-400">
                        <div class="rounded-xl border border-slate-800/70 bg-slate-900/40 px-3 py-3">
                            <dt class="text-pink-300">+{{ $featured->count() }}</dt>
                            <dd>Nuevos lanzamientos</dd>
                        </div>
                        <div class="rounded-xl border border-slate-800/70 bg-slate-900/40 px-3 py-3">
                            <dt class="text-pink-300">{{ $digitalHighlights->count() }}</dt>
                            <dd>Digitales destacados</dd>
                        </div>
                        <div class="rounded-xl border border-slate-800/70 bg-slate-900/40 px-3 py-3">
                            <dt class="text-pink-300">Garantía</dt>
                            <dd>Pagos Wompi</dd>
                        </div>
                    </dl>
                </div>

                <div class="space-y-4">
                    <div class="rounded-3xl border border-slate-800/60 bg-slate-900/70 p-6 shadow-lg shadow-slate-950/30">
                        <p class="text-sm font-semibold uppercase tracking-widest text-slate-400">Explora por categoría</p>
                        <div class="mt-5 grid grid-cols-2 gap-3">
                            @foreach ($categories->take(6) as $category)
                                <a href="{{ route('categories.show', $category) }}" class="group flex flex-col gap-1 rounded-2xl border border-slate-800/70 bg-slate-900/60 px-4 py-3 text-left transition hover:border-pink-500/60 hover:bg-pink-500/10">
                                    <span class="text-sm font-semibold text-white group-hover:text-pink-200">{{ $category->name }}</span>
                                    <span class="text-[11px] uppercase tracking-widest text-slate-500">{{ $category->children->pluck('name')->take(2)->implode(' · ') ?: 'Destacados' }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <div class="rounded-3xl border border-slate-800/60 bg-gradient-to-r from-slate-900/80 via-slate-900/70 to-slate-900/50 p-6 shadow-lg shadow-slate-950/30">
                        <p class="text-sm font-semibold uppercase tracking-widest text-slate-400">¿Por qué GameStore?</p>
                        <ul class="mt-4 space-y-3 text-sm text-slate-300">
                            <li class="flex items-start gap-2">
                                <span class="mt-1 h-1.5 w-1.5 rounded-full bg-pink-400"></span>
                                Entregamos claves digitales al instante para tus plataformas favoritas.
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="mt-1 h-1.5 w-1.5 rounded-full bg-pink-400"></span>
                                Seguimiento de pedidos físicos, inventario actualizado y soporte inmediato.
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="mt-1 h-1.5 w-1.5 rounded-full bg-pink-400"></span>
                                Cupones dinámicos, recompensas por reseñas y wishlist sincronizada.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-6 py-12 sm:px-10 lg:px-12">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h2 class="text-2xl font-semibold text-white">Nuevos lanzamientos</h2>
            <a href="{{ route('products.index', ['sort' => 'latest']) }}" class="inline-flex items-center gap-1 text-sm font-medium text-pink-300 hover:text-pink-200">
                Ver catálogo completo
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
        <div class="mt-6 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($featured as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-6 py-12 sm:px-10 lg:px-12">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h2 class="text-2xl font-semibold text-white">Top calificados por la comunidad</h2>
            <a href="{{ route('products.index', ['sort' => 'rating']) }}" class="inline-flex items-center gap-1 text-sm font-medium text-pink-300 hover:text-pink-200">
                Ver más reseñas
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
        <div class="mt-6 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($topRated as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-6 py-12 sm:px-10 lg:px-12">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h2 class="text-2xl font-semibold text-white">Claves digitales listas para descargar</h2>
            <a href="{{ route('products.index', ['type' => 'digital']) }}" class="inline-flex items-center gap-1 text-sm font-medium text-pink-300 hover:text-pink-200">
                Explorar juegos digitales
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
        <div class="mt-6 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($digitalHighlights as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </section>
</x-app-layout>
