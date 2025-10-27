@php
    use App\Models\Category;
    use App\Support\CacheKeys;
    use Illuminate\Support\Facades\Cache;
    use Illuminate\Support\Str;

    $navCategories = Cache::remember(CacheKeys::categoryTree(), 3600, function () {
        return Category::query()
            ->with('children')
            ->root()
            ->orderBy('name')
            ->get();
    });
@endphp

<nav x-data="{ open: false }" class="sticky top-0 z-50 border-b border-slate-800/80 bg-slate-900/90 backdrop-blur">
    <div class="mx-auto max-w-7xl px-6 sm:px-10 lg:px-12">
        <div class="flex h-20 items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <button @click="open = !open" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-800 text-slate-300 transition hover:border-pink-500 hover:text-white lg:hidden" aria-label="Abrir menú principal">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <a href="{{ route('home') }}" class="flex items-center gap-3 text-white">
                    <x-application-logo class="h-9 w-9 text-pink-500" />
                    <div class="flex flex-col leading-tight">
                        <span class="text-xl font-semibold tracking-tight">{{ config('app.name') }}</span>
                        <span class="text-xs uppercase tracking-[0.3em] text-pink-400/80">Level Up</span>
                    </div>
                </a>

                <div class="hidden lg:flex items-center gap-3">
                    <div x-data="{ flyout: false }" class="relative">
                        <button @mouseenter="flyout = true" @mouseleave="flyout = false" @focus="flyout = true" @blur="flyout = false" class="inline-flex items-center gap-2 rounded-lg border border-slate-700/70 bg-slate-800/60 px-3 py-2 text-sm font-semibold text-slate-200 transition hover:border-pink-500 hover:text-white">
                            Categorías
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.7a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-cloak x-show="flyout" @mouseenter="flyout = true" @mouseleave="flyout = false" x-transition.origin.top class="absolute left-0 top-full mt-3 w-80 rounded-xl border border-slate-800 bg-slate-900/95 p-5 shadow-2xl">
                            <div class="grid gap-4">
                                @foreach ($navCategories as $category)
                                    <div>
                                        <a href="{{ route('categories.show', $category) }}" class="block text-sm font-semibold text-white hover:text-pink-400">
                                            {{ $category->name }}
                                        </a>
                                        @if ($category->children->isNotEmpty())
                                            <div class="mt-1 flex flex-wrap gap-2">
                                                @foreach ($category->children as $child)
                                                    <a href="{{ route('categories.show', $child) }}" class="rounded-full bg-slate-800/60 px-2.5 py-1 text-xs text-slate-400 hover:bg-pink-500/20 hover:text-pink-300">
                                                        {{ $child->name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('products.index') }}" class="text-sm font-medium text-slate-300 transition hover:text-white">Catálogo</a>
                    <a href="{{ route('products.index', ['type' => 'digital']) }}" class="text-sm font-medium text-slate-300 transition hover:text-white">Digitales</a>
                    <a href="{{ route('products.index', ['type' => 'physical']) }}" class="text-sm font-medium text-slate-300 transition hover:text-white">Físicos</a>
                    <a href="{{ route('products.index', ['sort' => 'rating']) }}" class="text-sm font-medium text-slate-300 transition hover:text-white">Top Rating</a>
                </div>
            </div>

            <form action="{{ route('products.index') }}" method="GET" class="relative hidden flex-1 items-center gap-2 rounded-full border border-slate-800 bg-slate-900/80 px-4 py-2 text-sm text-slate-200 focus-within:border-pink-500/70 focus-within:ring-2 focus-within:ring-pink-500/20 md:flex">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-4.35-4.35m0 0A6.5 6.5 0 1010.3 6.3a6.5 6.5 0 006.35 10.35z" />
                </svg>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Buscar título, SKU o palabra clave" class="h-9 w-full bg-transparent text-sm text-white placeholder:text-slate-500 focus:outline-none focus:ring-0" />
                <button type="submit" class="hidden rounded-full bg-gradient-to-r from-pink-500 to-purple-600 px-3 py-1.5 text-xs font-semibold uppercase tracking-wide text-white shadow hover:from-pink-400 hover:to-purple-500 md:inline-flex">
                    Buscar
                </button>
            </form>

            <div class="flex items-center gap-3">
                <a href="{{ route('products.index', ['sort' => 'latest']) }}" class="hidden text-sm font-medium text-slate-300 transition hover:text-white xl:inline-flex">
                    Lanzamientos
                </a>

                <a href="{{ route('products.index', ['type' => 'digital']) }}" class="hidden text-sm font-medium text-slate-300 transition hover:text-white xl:inline-flex">
                    Gift Cards
                </a>

                <x-cart-icon />

                @auth
                    <div x-data="{ openMenu: false }" class="relative">
                        <button @click="openMenu = !openMenu" class="flex items-center gap-2 rounded-full border border-slate-800 bg-slate-900/60 px-2 py-1.5 pl-1 pr-3 text-sm text-white transition hover:border-pink-500 hover:text-pink-200">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-pink-500 to-indigo-600 text-sm font-semibold uppercase">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </span>
                            <span class="hidden sm:inline-flex text-xs font-semibold uppercase tracking-wide text-slate-300">{{ Str::of(auth()->user()->name)->limit(12) }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.7a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-cloak x-show="openMenu" @click.away="openMenu = false" x-transition class="absolute right-0 top-full z-50 mt-2 w-52 rounded-xl border border-slate-800 bg-slate-900/95 p-3 shadow-2xl">
                            <div class="space-y-2 text-sm">
                                <a href="{{ route('dashboard') }}" class="flex items-center justify-between rounded-lg px-3 py-2 text-slate-200 hover:bg-slate-800/80 hover:text-white">
                                    Panel
                                    <span class="text-xs text-slate-500">Dashboard</span>
                                </a>
                                <a href="{{ route('profile.edit') }}" class="block rounded-lg px-3 py-2 text-slate-200 hover:bg-slate-800/80 hover:text-white">
                                    Mi perfil
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-slate-400 transition hover:bg-slate-800/80 hover:text-pink-300">
                                        Cerrar sesión
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M18 12H9m9 0l-3-3m3 3l-3 3" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-full border border-slate-800 px-3 py-2 text-sm font-medium text-slate-200 transition hover:border-pink-500 hover:text-white">
                        Ingresar
                    </a>
                    <a href="{{ route('register') }}" class="hidden items-center gap-2 rounded-full bg-gradient-to-r from-pink-500 to-purple-600 px-3.5 py-2 text-sm font-semibold text-white shadow hover:from-pink-400 hover:to-purple-500 sm:inline-flex">
                        Crear cuenta
                    </a>
                @endauth
            </div>
        </div>

        <div class="md:hidden">
            <form action="{{ route('products.index') }}" method="GET" class="relative mt-3 flex items-center gap-2 rounded-full border border-slate-800 bg-slate-900/80 px-4 py-2 text-sm text-slate-200 focus-within:border-pink-500/70 focus-within:ring-2 focus-within:ring-pink-500/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-4.35-4.35m0 0A6.5 6.5 0 1010.3 6.3a6.5 6.5 0 006.35 10.35z" />
                </svg>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Buscar juegos..." class="h-9 w-full bg-transparent text-sm text-white placeholder:text-slate-500 focus:outline-none focus:ring-0" />
            </form>
        </div>
    </div>

    <div x-cloak x-show="open" x-transition class="border-t border-slate-800 bg-slate-900/95 lg:hidden">
        <div class="space-y-5 px-6 py-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">Navegar</p>
                <div class="mt-3 grid gap-2 text-sm text-slate-200">
                    <a href="{{ route('products.index') }}" class="rounded-lg bg-slate-800/60 px-3 py-2">Catálogo completo</a>
                    <a href="{{ route('products.index', ['type' => 'digital']) }}" class="rounded-lg bg-slate-800/60 px-3 py-2">Digitales</a>
                    <a href="{{ route('products.index', ['type' => 'physical']) }}" class="rounded-lg bg-slate-800/60 px-3 py-2">Físicos</a>
                    <a href="{{ route('products.index', ['sort' => 'rating']) }}" class="rounded-lg bg-slate-800/60 px-3 py-2">Mejor valorados</a>
                </div>
            </div>

            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">Categorías populares</p>
                <div class="mt-3 grid grid-cols-2 gap-2 text-sm">
                    @foreach ($navCategories as $category)
                        <a href="{{ route('categories.show', $category) }}" class="rounded-lg border border-slate-800 bg-slate-900/60 px-3 py-2 text-slate-200 hover:border-pink-500 hover:text-white">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            </div>

            @auth
                <div class="border-t border-slate-800 pt-4">
                    <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">Mi cuenta</p>
                    <div class="mt-3 grid gap-2 text-sm text-slate-200">
                        <a href="{{ route('dashboard') }}" class="rounded-lg bg-slate-800/60 px-3 py-2">Panel de usuario</a>
                        <a href="{{ route('profile.edit') }}" class="rounded-lg bg-slate-800/60 px-3 py-2">Perfil y seguridad</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full rounded-lg bg-slate-800/60 px-3 py-2 text-left text-slate-300">Cerrar sesión</button>
                        </form>
                    </div>
                </div>
            @else
                <div class="border-t border-slate-800 pt-4">
                    <div class="grid gap-2 text-sm text-slate-200">
                        <a href="{{ route('login') }}" class="rounded-lg bg-slate-800/60 px-3 py-2 text-center">Iniciar sesión</a>
                        <a href="{{ route('register') }}" class="rounded-lg bg-gradient-to-r from-pink-500 to-purple-600 px-3 py-2 text-center font-semibold text-white shadow">Crear cuenta</a>
                    </div>
                </div>
            @endauth
        </div>
    </div>
</nav>
