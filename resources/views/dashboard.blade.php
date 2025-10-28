
@php
    $user = auth()->user();
    $ordersCount = $user->orders()->count();
    $wishlistCount = $user->wishlist()->count();
    $reviewsCount = $user->reviews()->count();
@endphp

<x-app-layout title="Panel de jugador">
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <p class="text-xs uppercase tracking-widest text-slate-400">Bienvenido de nuevo</p>
            <h1 class="text-3xl font-semibold text-white">Hola, {{ $user->name }}</h1>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl px-6 py-10 sm:px-10 lg:px-12">
        <div @class([
            'grid gap-6',
            'md:grid-cols-4' => $user->isSuperAdmin(),
            'md:grid-cols-3' => !$user->isSuperAdmin(),
        ])>
            @if ($user->isSuperAdmin())
                <a href="{{ route('admin.dashboard') }}" class="rounded-3xl border border-pink-600/60 bg-gradient-to-br from-pink-500/20 via-purple-600/10 to-slate-900/70 p-6 shadow-lg shadow-pink-500/20 transition hover:border-pink-500 hover:shadow-pink-500/40">
                    <p class="text-xs uppercase tracking-widest text-pink-300">Administración</p>
                    <p class="mt-2 text-3xl font-semibold text-white">Panel super admin</p>
                    <p class="mt-1 text-sm text-pink-100/80">Gestiona juegos, categorías, plataformas y más.</p>
                </a>
            @endif
            <div class="rounded-3xl border border-slate-800 bg-slate-900/70 p-6 shadow-lg shadow-slate-950/20">
                <p class="text-xs uppercase tracking-widest text-slate-400">Pedidos</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ $ordersCount }}</p>
                <p class="mt-1 text-sm text-slate-400">Historial de compras y estados del checkout.</p>
            </div>
            <div class="rounded-3xl border border-slate-800 bg-slate-900/70 p-6 shadow-lg shadow-slate-950/20">
                <p class="text-xs uppercase tracking-widest text-slate-400">Wishlist</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ $wishlistCount }}</p>
                <p class="mt-1 text-sm text-slate-400">Juegos guardados para comprar más tarde.</p>
            </div>
            <div class="rounded-3xl border border-slate-800 bg-slate-900/70 p-6 shadow-lg shadow-slate-950/20">
                <p class="text-xs uppercase tracking-widest text-slate-400">Reseñas</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ $reviewsCount }}</p>
                <p class="mt-1 text-sm text-slate-400">Tu opinión ayuda a otros jugadores.</p>
            </div>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-2">
            <div class="rounded-3xl border border-slate-800 bg-slate-900/70 p-6 shadow-lg shadow-slate-950/20">
                <h2 class="text-lg font-semibold text-white">Próximos pasos</h2>
                <ul class="mt-4 space-y-3 text-sm text-slate-300">
                    <li class="flex items-start gap-2">
                        <span class="mt-1 h-1.5 w-1.5 rounded-full bg-pink-400"></span>
                        Explora las novedades digitales y recibe claves al instante tras la confirmación de pago.
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="mt-1 h-1.5 w-1.5 rounded-full bg-pink-400"></span>
                        Revisa el estado de tus pedidos y descarga facturas desde el resumen.
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="mt-1 h-1.5 w-1.5 rounded-full bg-pink-400"></span>
                        Gestiona tus reseñas pendientes y comparte tus impresiones con la comunidad.
                    </li>
                </ul>
            </div>
            <div class="rounded-3xl border border-slate-800 bg-slate-900/70 p-6 shadow-lg shadow-slate-950/20">
                <h2 class="text-lg font-semibold text-white">Accesos rápidos</h2>
                <div class="mt-4 grid gap-3 text-sm text-slate-200">
                    <a href="{{ route('products.index') }}" class="flex items-center justify-between rounded-2xl border border-slate-800 bg-slate-900/60 px-4 py-3 transition hover:border-pink-500 hover:text-white">
                        Ir al catálogo
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                    <a href="{{ route('profile.edit') }}" class="flex items-center justify-between rounded-2xl border border-slate-800 bg-slate-900/60 px-4 py-3 transition hover:border-pink-500 hover:text-white">
                        Actualizar perfil
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a1.875 1.875 0 01-.884.497l-3.365.84a.375.375 0 01-.456-.456l.84-3.365a1.875 1.875 0 01.497-.884L16.863 4.487z" />
                        </svg>
                    </a>
                    <a href="{{ url('/cart') }}" class="flex items-center justify-between rounded-2xl border border-slate-800 bg-slate-900/60 px-4 py-3 transition hover:border-pink-500 hover:text-white">
                        Ver carrito
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 3h1.386a1.5 1.5 0 011.447 1.118L5.91 6.75m0 0l1.3 5.201A2.25 2.25 0 009.4 13.5h7.47a2.25 2.25 0 002.194-1.744l1.252-5.006A.75.75 0 0019.59 6H5.91m0 0L5.2 3.75M9.75 20.25a1.125 1.125 0 11-2.25 0 1.125 1.125 0 012.25 0zm9 0a1.125 1.125 0 11-2.25 0 1.125 1.125 0 012.25 0z" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
