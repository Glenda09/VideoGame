<x-app-layout title="Pago exitoso">
    <section class="mx-auto max-w-3xl px-6 py-16 text-center sm:px-10 lg:px-12">
        <div class="rounded-3xl border border-emerald-500/40 bg-emerald-500/10 p-10 shadow-lg shadow-emerald-500/20">
            <h1 class="text-3xl font-semibold text-white">¡Pago recibido!</h1>
            <p class="mt-3 text-sm text-emerald-200">Tu orden {{ ->number ?? '' }} fue procesada. En minutos recibirás un correo con los detalles y claves digitales.</p>
            <div class="mt-6 flex flex-wrap justify-center gap-3">
                <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 rounded-full border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:border-pink-500 hover:text-white">Seguir comprando</a>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-pink-500 to-purple-600 px-4 py-2 text-sm font-semibold text-white hover:from-pink-400 hover:to-purple-500">Ver mis pedidos</a>
            </div>
        </div>
    </section>
</x-app-layout>
