<x-app-layout title="Productos">
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <p class="text-xs uppercase tracking-widest text-slate-200/70">Administración</p>
            <div class="flex items-center justify-between gap-4">
                <h1 class="text-3xl font-semibold text-white">Productos</h1>
                <a href="{{ route('admin.products.create') }}" class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-pink-500 to-purple-600 px-4 py-2 text-sm font-semibold text-white shadow hover:from-pink-400 hover:to-purple-500">
                    Nuevo producto
                </a>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl px-6 py-10 sm:px-10 lg:px-12">
        @include('admin.partials.flash')

        <div class="overflow-hidden rounded-3xl border border-slate-800 bg-slate-900/70">
            <table class="min-w-full divide-y divide-slate-800 text-sm text-slate-200">
                <thead class="bg-slate-900/80 text-xs uppercase tracking-wider text-slate-400">
                    <tr>
                        <th class="px-4 py-3 text-left">Producto</th>
                        <th class="px-4 py-3 text-left">Categoría</th>
                        <th class="px-4 py-3 text-left">Tipo</th>
                        <th class="px-4 py-3 text-left">Estado</th>
                        <th class="px-4 py-3 text-right">Precio</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 bg-slate-950/40">
                    @forelse ($products as $product)
                        <tr>
                            <td class="px-4 py-4">
                                <div class="flex flex-col">
                                    <span class="font-semibold text-white">{{ $product->title }}</span>
                                    <span class="text-xs text-slate-400">SKU: {{ $product->sku }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-slate-300">
                                {{ $product->category?->name ?? 'Sin categoría' }}
                            </td>
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center rounded-full border border-slate-700/70 px-2 py-1 text-xs font-medium text-slate-200">
                                    {{ $product->is_digital ? 'Digital' : 'Físico' }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <span @class([
                                    'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold',
                                    'bg-emerald-500/10 text-emerald-300 border border-emerald-500/30' => $product->active,
                                    'bg-red-500/10 text-red-300 border border-red-500/30' => !$product->active,
                                ])>
                                    {{ $product->active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-right text-slate-200">
                                ${{ number_format($product->price_cents / 100, 2) }} {{ $product->currency }}
                            </td>
                            <td class="px-4 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="rounded-full border border-slate-700/70 px-3 py-1.5 text-xs font-semibold text-slate-200 transition hover:border-pink-500 hover:text-white">
                                        Editar
                                    </a>
                                    <form method="POST" action="{{ route('admin.products.toggle-status', $product) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="rounded-full border border-slate-700/70 px-3 py-1.5 text-xs font-semibold text-slate-200 transition hover:border-pink-500 hover:text-white">
                                            {{ $product->active ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('¿Seguro que deseas eliminar este producto? Esta acción no se puede deshacer.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-full border border-red-600/40 px-3 py-1.5 text-xs font-semibold text-red-300 transition hover:border-red-500 hover:text-red-200">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-slate-400">
                                Aún no hay productos registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $products->links() }}
        </div>
    </div>
</x-app-layout>

