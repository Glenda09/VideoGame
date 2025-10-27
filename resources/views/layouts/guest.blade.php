@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ trim(($title ? $title.' | ' : '').config('app.name', 'GameStore')) }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-full bg-slate-950 font-sans text-slate-100">
        <div class="relative flex min-h-screen items-center justify-center px-6 py-12 sm:px-10">
            <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top,_rgba(236,72,153,0.25),_transparent_55%),radial-gradient(circle_at_bottom,_rgba(59,130,246,0.2),_transparent_45%)]"></div>
            <div class="absolute inset-0 -z-10 bg-slate-950/70 backdrop-blur"></div>

            <div class="w-full max-w-md space-y-6">
                <div class="flex flex-col items-center gap-3 text-center">
                    <a href="{{ route('home') }}" class="inline-flex h-16 w-16 items-center justify-center rounded-2xl border border-pink-500/40 bg-slate-900/70 text-pink-300 shadow-lg shadow-pink-500/20">
                        <x-application-logo class="h-10 w-10 text-pink-400" />
                    </a>
                    <h1 class="text-2xl font-semibold text-white">{{ config('app.name') }}</h1>
                    <p class="max-w-sm text-sm text-slate-400">
                        Ingresa para sincronizar tu carrito, wishlist y seguimiento de pedidos con confirmaciones seguras vía Wompi.
                    </p>
                </div>

                <div class="overflow-hidden rounded-3xl border border-slate-800 bg-slate-900/80 p-6 shadow-2xl shadow-slate-950/40">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
