<x-app-layout title="Categorías">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex flex-col gap-1">
                <p class="text-xs uppercase tracking-widest text-slate-200/70">Administración</p>
                <h1 class="text-3xl font-semibold text-white">Categorías</h1>
            </div>
            <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-pink-500 to-purple-600 px-4 py-2 text-sm font-semibold text-white shadow hover:from-pink-400 hover:to-purple-500">
                Nueva categoría
            </a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-6xl px-6 py-10 sm:px-10 lg:px-12">
        @include('admin.partials.flash')

        <div class="overflow-hidden rounded-3xl border border-slate-800 bg-slate-900/70">
            <table class="min-w-full divide-y divide-slate-800 text-sm text-slate-200">
                <thead class="bg-slate-900/80 text-xs uppercase tracking-wider text-slate-400">
                    <tr>
                        <th class="px-4 py-3 text-left">Nombre</th>
                        <th class="px-4 py-3 text-left">Slug</th>
                        <th class="px-4 py-3 text-left">Padre</th>
                        <th class="px-4 py-3 text-right">Productos</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 bg-slate-950/40">
                    @forelse ($categories as $category)
                        <tr>
                            <td class="px-4 py-3">
                                <span class="font-semibold text-white">{{ $category->name }}</span>
                            </td>
                            <td class="px-4 py-3 text-slate-300">
                                {{ $category->slug }}
                            </td>
                            <td class="px-4 py-3 text-slate-300">
                                {{ $category->parent?->name ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-right text-slate-200">
                                {{ $category->products_count }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="rounded-full border border-slate-700/70 px-3 py-1.5 text-xs font-semibold text-slate-200 transition hover:border-pink-500 hover:text-white">
                                        Editar
                                    </a>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('¿Seguro que deseas eliminar esta categoría?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-full border border-red-600/40 px-3 py-1.5 text-xs font-semibold text-red-300 transition hover:border-red-500 hover:text-red-100">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-slate-400">
                                Aún no hay categorías registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $categories->links() }}
        </div>
    </div>
</x-app-layout>

