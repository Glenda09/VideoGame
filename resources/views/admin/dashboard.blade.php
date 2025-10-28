<x-app-layout title="Panel de administración">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex flex-col gap-1">
                <p class="text-xs uppercase tracking-widest text-slate-200/70">Administración</p>
                <h1 class="text-3xl font-semibold text-white">Panel de control</h1>
            </div>
            <div class="hidden gap-3 sm:flex">
                <a href="{{ route('admin.products.create') }}" class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-pink-500 to-purple-600 px-4 py-2 text-sm font-semibold text-white shadow hover:from-pink-400 hover:to-purple-500">
                    Nuevo producto
                </a>
                <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center gap-2 rounded-full border border-slate-200/20 bg-slate-900/40 px-4 py-2 text-sm font-semibold text-slate-100 transition hover:border-pink-500 hover:text-white">
                    Nueva categoría
                </a>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl px-6 py-10 sm:px-10 lg:px-12">
        @include('admin.partials.flash')

        <div class="grid gap-6 md:grid-cols-4">
            <div class="rounded-3xl border border-slate-800/70 bg-slate-900/70 p-6">
                <p class="text-xs uppercase tracking-widest text-slate-400">Productos</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ number_format($stats['products']) }}</p>
                <p class="mt-1 text-sm text-slate-400">Total de juegos en catálogo.</p>
            </div>
            <div class="rounded-3xl border border-slate-800/70 bg-slate-900/70 p-6">
                <p class="text-xs uppercase tracking-widest text-slate-400">Categorías</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ number_format($stats['categories']) }}</p>
                <p class="mt-1 text-sm text-slate-400">Árbol de géneros organizado.</p>
            </div>
            <div class="rounded-3xl border border-slate-800/70 bg-slate-900/70 p-6">
                <p class="text-xs uppercase tracking-widest text-slate-400">Pedidos</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ number_format($stats['orders']) }}</p>
                <p class="mt-1 text-sm text-slate-400">Historial de compras confirmadas.</p>
            </div>
            <div class="rounded-3xl border border-slate-800/70 bg-slate-900/70 p-6">
                <p class="text-xs uppercase tracking-widest text-slate-400">Clientes</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ number_format($stats['customers']) }}</p>
                <p class="mt-1 text-sm text-slate-400">Usuarios registrados sin permisos administrativos.</p>
            </div>
        </div>

        <div class="mt-10 grid gap-6 lg:grid-cols-[2fr,1fr]">
            <div class="rounded-3xl border border-slate-800 bg-slate-900/70 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-white">Últimos productos</h2>
                        <p class="text-sm text-slate-400">Registros añadidos recientemente al catálogo.</p>
                    </div>
                    <a href="{{ route('admin.products.index') }}" class="text-sm font-medium text-pink-400 hover:text-pink-300">
                        Ver todos
                    </a>
                </div>

                <div class="mt-5 overflow-hidden rounded-2xl border border-slate-800">
                    <table class="min-w-full divide-y divide-slate-800 text-sm text-slate-200">
                        <thead class="bg-slate-900/80 text-xs uppercase tracking-wider text-slate-400">
                            <tr>
                                <th class="px-4 py-3 text-left">Título</th>
                                <th class="px-4 py-3 text-left">Categoría</th>
                                <th class="px-4 py-3 text-left">Estado</th>
                                <th class="px-4 py-3 text-right">Precio</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800 bg-slate-950/40">
                            @forelse ($latestProducts as $product)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-col">
                                            <span class="font-semibold text-white">{{ $product->title }}</span>
                                            <span class="text-xs text-slate-400">SKU: {{ $product->sku }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-slate-300">
                                        {{ $product->category?->name ?? 'Sin categoría' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span @class([
                                            'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium',
                                            'bg-emerald-500/10 text-emerald-300 border border-emerald-500/30' => $product->active,
                                            'bg-red-500/10 text-red-300 border border-red-500/30' => !$product->active,
                                        ])>
                                            {{ $product->active ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right text-slate-200">
                                        ${{ number_format($product->price_cents / 100, 2) }} {{ $product->currency }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-slate-400">
                                        No hay productos recientes para mostrar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-800 bg-slate-900/70 p-6">
                <h2 class="text-lg font-semibold text-white">Accesos rápidos</h2>
                <div class="mt-4 grid gap-3 text-sm">
                    <a href="{{ route('admin.products.index') }}" class="flex items-center justify-between rounded-2xl border border-slate-800 bg-slate-950/60 px-4 py-3 transition hover:border-pink-500 hover:text-white">
                        Gestionar productos
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                    <a href="{{ route('admin.categories.index') }}" class="flex items-center justify-between rounded-2xl border border-slate-800 bg-slate-950/60 px-4 py-3 transition hover:border-pink-500 hover:text-white">
                        Gestionar categorías
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                    <a href="{{ route('admin.platforms.index') }}" class="flex items-center justify-between rounded-2xl border border-slate-800 bg-slate-950/60 px-4 py-3 transition hover:border-pink-500 hover:text-white">
                        Gestionar plataformas
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center justify-between rounded-2xl border border-slate-800 bg-slate-950/60 px-4 py-3 transition hover:border-pink-500 hover:text-white">
                        Administrar usuarios
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
