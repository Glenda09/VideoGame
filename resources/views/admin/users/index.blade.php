<x-app-layout title="Usuarios">
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <p class="text-xs uppercase tracking-widest text-slate-200/70">Administración</p>
            <h1 class="text-3xl font-semibold text-white">Usuarios del sistema</h1>
            <p class="text-sm text-slate-300">Gestiona qué cuentas tienen privilegios de super administrador.</p>
        </div>
    </x-slot>

    <div class="mx-auto max-w-5xl px-6 py-10 sm:px-10 lg:px-12">
        @include('admin.partials.flash')

        <form method="GET" action="{{ route('admin.users.index') }}" class="mb-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <label class="flex-1">
                    <span class="sr-only">Buscar usuarios</span>
                    <input
                        type="search"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Buscar por nombre o correo..."
                        class="w-full rounded-full border border-slate-700 bg-slate-950/60 px-5 py-3 text-sm text-white placeholder:text-slate-500 focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                    >
                </label>
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-full border border-slate-700 px-5 py-3 text-sm font-semibold text-slate-200 transition hover:border-pink-500 hover:text-white">
                    Filtrar
                </button>
            </div>
        </form>

        <div class="overflow-hidden rounded-3xl border border-slate-800 bg-slate-900/70">
            <table class="min-w-full divide-y divide-slate-800 text-sm text-slate-200">
                <thead class="bg-slate-900/80 text-xs uppercase tracking-wider text-slate-400">
                    <tr>
                        <th class="px-4 py-3 text-left">Usuario</th>
                        <th class="px-4 py-3 text-left">Correo</th>
                        <th class="px-4 py-3 text-left">Rol</th>
                        <th class="px-4 py-3 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 bg-slate-950/40">
                    @forelse ($users as $user)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-white">{{ $user->name }}</td>
                            <td class="px-4 py-3 text-slate-300">{{ $user->email }}</td>
                            <td class="px-4 py-3">
                                <span @class([
                                    'inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-medium uppercase tracking-wide',
                                    'border-pink-500/40 bg-pink-500/10 text-pink-200' => $user->isSuperAdmin(),
                                    'border-slate-700/70 bg-slate-800/60 text-slate-300' => !$user->isSuperAdmin(),
                                ])>
                                    {{ $user->role instanceof \App\Enums\Role ? $user->role->label() : ($user->role === 'super_admin' ? 'Super administrador' : 'Cliente') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="inline-flex items-center gap-3">
                                    @csrf
                                    @method('PUT')
                                    <select name="role" class="rounded-full border border-slate-700 bg-slate-950/60 px-3 py-2 text-xs text-white focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30">
                                        <option value="customer" @selected($user->role === 'customer')>Cliente</option>
                                        <option value="super_admin" @selected($user->role === 'super_admin')>Super admin</option>
                                    </select>
                                    <button type="submit" class="rounded-full bg-gradient-to-r from-pink-500 to-purple-600 px-4 py-2 text-xs font-semibold text-white shadow hover:from-pink-400 hover:to-purple-500">
                                        Actualizar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-slate-400">
                                No se encontraron usuarios para la búsqueda indicada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $users->links() }}
        </div>
    </div>
</x-app-layout>

