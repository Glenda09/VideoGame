@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>
            {{ trim(($title ? $title.' | ' : '').config('app.name', 'GameStore')) }}
        </title>
        <meta name="description" content="GameStore es tu tienda integral de videojuegos físicos y digitales con lanzamientos, ofertas y compras seguras a través de Wompi.">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('head')
    </head>
    <body class="min-h-full font-sans antialiased bg-slate-950 text-slate-100">
        <div class="relative flex min-h-screen flex-col">
            @include('layouts.navigation')

            @isset($header)
                <header class="bg-gradient-to-r from-indigo-700/80 via-purple-700/80 to-pink-600/80 backdrop-blur border-b border-slate-800/60">
                    <div class="mx-auto flex max-w-7xl flex-col gap-4 px-6 py-8 sm:px-10 lg:px-12">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main class="flex-1 bg-slate-950">
                {{ $slot }}
            </main>

            <footer class="border-t border-slate-800 bg-slate-900/80 backdrop-blur">
                <div class="mx-auto max-w-7xl px-6 py-10 sm:px-10 lg:px-12">
                    <div class="grid gap-8 md:grid-cols-4">
                        <div>
                            <p class="text-lg font-semibold text-white">{{ config('app.name') }}</p>
                            <p class="mt-3 text-sm text-slate-400">
                                Videojuegos físicos y digitales, preventas, ediciones coleccionista y contenido descargable listo para ti 24/7.
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-widest text-slate-300">Explora</p>
                            <ul class="mt-3 space-y-2 text-sm text-slate-400">
                                <li><a href="{{ route('products.index') }}" class="hover:text-white hover:underline">Catálogo completo</a></li>
                                <li><a href="{{ route('categories.show', ['category' => 'action']) }}" class="hover:text-white hover:underline">Acción</a></li>
                                <li><a href="{{ route('categories.show', ['category' => 'role-playing']) }}" class="hover:text-white hover:underline">RPG</a></li>
                                <li><a href="{{ route('platforms.show', ['platform' => 'playstation-5']) }}" class="hover:text-white hover:underline">Juegos PS5</a></li>
                            </ul>
                        </div>
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-widest text-slate-300">Soporte</p>
                            <ul class="mt-3 space-y-2 text-sm text-slate-400">
                                <li>Escríbenos: <a href="mailto:{{ config('store.support_email') }}" class="hover:text-white hover:underline">{{ config('store.support_email') }}</a></li>
                                <li>Horarios: L-V 9:00 - 18:00</li>
                                <li>Pagos seguros con Wompi</li>
                                <li>FAQ y devoluciones</li>
                            </ul>
                        </div>
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-widest text-slate-300">Suscríbete</p>
                            <p class="mt-3 text-sm text-slate-400">
                                Recibe lanzamientos, códigos exclusivos y promociones antes que nadie.
                            </p>
                            <form class="mt-4 flex rounded-lg bg-slate-800/60 p-1 shadow-inner shadow-slate-900">
                                <input type="email" placeholder="tu@correo.com" class="flex-1 rounded-l-md bg-transparent px-3 py-2 text-sm text-white placeholder:text-slate-500 focus:outline-none focus:ring-0">
                                <button type="button" class="rounded-md bg-gradient-to-r from-pink-500 to-purple-600 px-4 py-2 text-sm font-semibold text-white hover:from-pink-400 hover:to-purple-500 transition">
                                    Notificarme
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="mt-10 flex flex-col gap-4 border-t border-slate-800 pt-6 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between">
                        <p>&copy; {{ now()->year }} {{ config('app.name') }}. Todos los derechos reservados.</p>
                        <div class="flex gap-4">
                            <a href="#!" class="hover:text-white">Términos</a>
                            <a href="#!" class="hover:text-white">Privacidad</a>
                            <a href="#!" class="hover:text-white">Estado del sistema</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>

        @stack('modals')
        @stack('scripts')
    </body>
</html>
