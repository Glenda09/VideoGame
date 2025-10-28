<x-app-layout title="Editar plataforma">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex flex-col gap-1">
                <p class="text-xs uppercase tracking-widest text-slate-200/70">Administración</p>
                <h1 class="text-3xl font-semibold text-white">Editar plataforma</h1>
                <p class="text-sm text-slate-300">{{ $platform->name }}</p>
            </div>
            <a href="{{ route('admin.platforms.index') }}" class="inline-flex items-center gap-2 rounded-full border border-slate-700/70 bg-slate-900/60 px-4 py-2 text-sm font-semibold text-slate-100 transition hover:border-pink-500 hover:text-white">
                Volver al listado
            </a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-3xl px-6 py-10 sm:px-10 lg:px-12">
        @include('admin.partials.flash')

        <form method="POST" action="{{ route('admin.platforms.update', $platform) }}" class="space-y-8">
            @csrf
            @method('PUT')

            @include('admin.platforms._form')

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.platforms.index') }}" class="rounded-full border border-slate-700/70 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:border-pink-500 hover:text-white">
                    Cancelar
                </a>
                <button type="submit" class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-pink-500 to-purple-600 px-5 py-2 text-sm font-semibold text-white shadow hover:from-pink-400 hover:to-purple-500">
                    Guardar cambios
                </button>
            </div>
        </form>

        <form method="POST" action="{{ route('admin.platforms.destroy', $platform) }}" onsubmit="return confirm('¿Seguro que deseas eliminar esta plataforma?');" class="mt-6 flex justify-end">
            @csrf
            @method('DELETE')
            <button type="submit" class="rounded-full border border-red-600/40 px-4 py-2 text-sm font-semibold text-red-300 transition hover:border-red-500 hover:text-red-100">
                Eliminar plataforma
            </button>
        </form>
    </div>
</x-app-layout>

