@php
    $categoryOptions = [];
    foreach ($categories as $category) {
        $categoryOptions[] = ['slug' => $category->slug, 'label' => $category->name];
        foreach ($category->children as $child) {
            $categoryOptions[] = ['slug' => $child->slug, 'label' => '— '.$child->name];
        }
    }

    $activeFilters = collect($filters)->filter(fn ($value, $key) => filled($value) && !in_array($key, ['sort']));
@endphp

<x-app-layout :title="$pageTitle">
    <section class="mx-auto max-w-7xl px-6 py-10 sm:px-10 lg:px-12">
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm uppercase tracking-widest text-slate-400">Catálogo</p>
                <h1 class="text-3xl font-semibold text-white">{{ $pageTitle }}</h1>
                <p class="mt-2 text-sm text-slate-400">
                    Encontramos <span class="text-pink-300 font-semibold">{{ $products->total() }}</span> resultados disponibles.
                    Filtra por plataforma, tipo de producto o rango de precio para afinar tu búsqueda.
                </p>
            </div>
            <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2 rounded-full border border-slate-800 bg-slate-900/70 px-3 py-2 text-sm text-slate-200">
                @foreach ($filters as $name => $value)
                    @if (!in_array($name, ['sort']) && filled($value))
                        <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                    @endif
                @endforeach
                <label for="sort" class="text-xs uppercase tracking-widest text-slate-500">Ordenar</label>
                <select id="sort" name="sort" onchange="this.form.submit()" class="rounded-full border-none bg-transparent text-sm text-white focus:border-pink-500 focus:ring-0">
                    <option value="latest" @selected(($filters['sort'] ?? 'latest') === 'latest')>Más recientes</option>
                    <option value="price_asc" @selected(($filters['sort'] ?? '') === 'price_asc')>Precio: menor a mayor</option>
                    <option value="price_desc" @selected(($filters['sort'] ?? '') === 'price_desc')>Precio: mayor a menor</option>
                    <option value="rating" @selected(($filters['sort'] ?? '') === 'rating')>Mejor puntuación</option>
                    <option value="oldest" @selected(($filters['sort'] ?? '') === 'oldest')>Más antiguos</option>
                </select>
            </form>
        </div>

        <div class="grid gap-10 lg:grid-cols-[280px_1fr]">
            <aside class="space-y-6">
                <form action="{{ route('products.index') }}" method="GET" class="space-y-6 rounded-3xl border border-slate-800 bg-slate-900/70 p-6 shadow-xl shadow-slate-950/20">
                    <div class="space-y-2">
                        <label for="search" class="text-xs font-semibold uppercase tracking-widest text-slate-400">Buscar</label>
                        <input type="text" id="search" name="search" value="{{ $filters['search'] }}" placeholder="Nombre o palabra clave" class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-white placeholder:text-slate-500 focus:border-pink-500 focus:ring-pink-500/30" />
                    </div>

                    <div class="space-y-2">
                        <label for="category" class="text-xs font-semibold uppercase tracking-widest text-slate-400">Categoría</label>
                        <select id="category" name="category" class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-white focus:border-pink-500 focus:ring-pink-500/30">
                            <option value="">Todas</option>
                            @foreach ($categoryOptions as $option)
                                <option value="{{ $option['slug'] }}" @selected($filters['category'] === $option['slug'])>{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label for="platform" class="text-xs font-semibold uppercase tracking-widest text-slate-400">Plataforma</label>
                        <select id="platform" name="platform" class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-white focus:border-pink-500 focus:ring-pink-500/30">
                            <option value="">Todas</option>
                            @foreach ($platforms as $platform)
                                <option value="{{ $platform->slug }}" @selected($filters['platform'] === $platform->slug)>{{ $platform->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">Tipo</p>
                        <div class="flex flex-col gap-2 text-sm text-slate-300">
                            <label class="inline-flex items-center gap-2">
                                <input type="radio" name="type" value="" class="rounded-full border-slate-600 bg-slate-900 text-pink-500 focus:ring-pink-500/30" @checked(!$filters['type'])>
                                <span>Todos</span>
                            </label>
                            <label class="inline-flex items-center gap-2">
                                <input type="radio" name="type" value="digital" class="rounded-full border-slate-600 bg-slate-900 text-pink-500 focus:ring-pink-500/30" @checked($filters['type'] === 'digital')>
                                <span>Digital</span>
                            </label>
                            <label class="inline-flex items-center gap-2">
                                <input type="radio" name="type" value="physical" class="rounded-full border-slate-600 bg-slate-900 text-pink-500 focus:ring-pink-500/30" @checked($filters['type'] === 'physical')>
                                <span>Físico</span>
                            </label>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">Precio</p>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label for="price_min" class="block text-[11px] uppercase tracking-widest text-slate-500">Mín.</label>
                                <input type="number" step="0.01" min="0" id="price_min" name="price_min" value="{{ $filters['price_min'] }}" class="mt-1 w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-white placeholder:text-slate-500 focus:border-pink-500 focus:ring-pink-500/30" placeholder="0.00" />
                            </div>
                            <div>
                                <label for="price_max" class="block text-[11px] uppercase tracking-widest text-slate-500">Máx.</label>
                                <input type="number" step="0.01" min="0" id="price_max" name="price_max" value="{{ $filters['price_max'] }}" class="mt-1 w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-white placeholder:text-slate-500 focus:border-pink-500 focus:ring-pink-500/30" placeholder="199.99" />
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="sort" value="{{ $filters['sort'] }}">

                    <div class="flex flex-col gap-2">
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-pink-500 to-purple-600 px-4 py-3 text-sm font-semibold text-white shadow hover:from-pink-400 hover:to-purple-500">
                            Aplicar filtros
                        </button>
                        <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-700 px-4 py-3 text-sm font-semibold text-slate-300 transition hover:border-pink-500 hover:text-white">
                            Limpiar filtros
                        </a>
                    </div>
                </form>

                @if ($activeFilters->isNotEmpty())
                    <div class="rounded-3xl border border-slate-800 bg-slate-900/70 p-5 text-sm text-slate-300">
                        <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">Filtros activos</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach ($activeFilters as $key => $value)
                                @php
                                    $query = collect($filters)->merge(['page' => null])->put($key, null)->filter();
                                @endphp
                                <a href="{{ route('products.index', $query->toArray()) }}" class="inline-flex items-center gap-2 rounded-full border border-pink-500/40 bg-pink-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-pink-200 hover:border-pink-400 hover:bg-pink-500/20">
                                    <span>{{ ucfirst(str_replace('_', ' ', $key)) }}: {{ $value }}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </aside>

            <div class="space-y-8">
                @if ($products->isEmpty())
                    <div class="rounded-3xl border border-slate-800 bg-slate-900/70 p-10 text-center text-slate-300">
                        <p class="text-lg font-semibold text-white">No hay resultados para tu búsqueda.</p>
                        <p class="mt-2 text-sm text-slate-400">
                            Ajusta los filtros o explora todas las categorías del catálogo.
                        </p>
                        <a href="{{ route('products.index') }}" class="mt-4 inline-flex items-center gap-2 rounded-full border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:border-pink-500 hover:text-white">
                            Reiniciar búsqueda
                        </a>
                    </div>
                @else
                    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($products as $product)
                            <x-product-card :product="$product" />
                        @endforeach
                    </div>
                    <div class="pt-4">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-app-layout>
