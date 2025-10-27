<x-app-layout title="Pago no completado">
    <section class="mx-auto max-w-3xl px-6 py-16 text-center sm:px-10 lg:px-12">
        <div class="rounded-3xl border border-red-500/40 bg-red-500/10 p-10 shadow-lg shadow-red-500/20">
            <h1 class="text-3xl font-semibold text-white">Pago pendiente</h1>
            <p class="mt-3 text-sm text-red-200">La transacción para la orden {{ ->number ?? '' }} no se completó. Puedes intentarlo nuevamente desde tu carrito o elegir otro método de pago.</p>
            <div class="mt-6 flex flex-wrap justify-center gap-3">
                <a href="{{ route('cart.index') }}" class="inline-flex items-center gap-2 rounded-full border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:border-pink-500 hover:text-white">Volver al carrito</a>
                <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-pink-500 to-purple-600 px-4 py-2 text-sm font-semibold text-white hover:from-pink-400 hover:to-purple-500">Explorar catálogo</a>
            </div>
        </div>
    </section>
</x-app-layout>
