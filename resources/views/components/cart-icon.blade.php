@props(['count' => 0])

<a href="{{ url('/cart') }}" class="relative inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-800 bg-slate-900/60 text-slate-200 transition hover:border-pink-500 hover:text-white" aria-label="Abrir carrito">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 3h1.386a1.5 1.5 0 011.447 1.118L5.91 6.75m0 0l1.3 5.201A2.25 2.25 0 009.4 13.5h7.47a2.25 2.25 0 002.194-1.744l1.252-5.006A.75.75 0 0019.59 6H5.91m0 0L5.2 3.75M9.75 20.25a1.125 1.125 0 11-2.25 0 1.125 1.125 0 012.25 0zm9 0a1.125 1.125 0 11-2.25 0 1.125 1.125 0 012.25 0z" />
    </svg>

    @if ($count > 0)
        <span class="absolute -top-1.5 -right-1.5 inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-pink-500 px-1 text-[10px] font-semibold text-white">
            {{ $count }}
        </span>
    @endif
</a>
