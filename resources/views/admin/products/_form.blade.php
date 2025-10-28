@php
    $isDigital = old('is_digital', $product->is_digital ?? true);
    $active = old('active', $product->active ?? true);
    $price = old('price', isset($product->price_cents) ? number_format($product->price_cents / 100, 2, '.', '') : null);
    $selectedPlatforms = old('platforms', $product->platforms?->pluck('id')->all() ?? []);
    $inventoryQuantity = old('inventory_quantity', $product->inventory?->quantity ?? null);
@endphp

<div
    x-data="{ digital: {{ $isDigital ? 'true' : 'false' }} }"
    class="space-y-6"
>
    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <label class="text-sm font-semibold text-slate-200">Título</label>
            <input type="text" name="title" value="{{ old('title', $product->title) }}" required class="mt-2 w-full rounded-2xl border border-slate-700/70 bg-slate-950/60 px-4 py-3 text-sm text-white focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30">
        </div>
        <div>
            <label class="text-sm font-semibold text-slate-200">SKU</label>
            <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" required class="mt-2 w-full rounded-2xl border border-slate-700/70 bg-slate-950/60 px-4 py-3 text-sm text-white focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30">
        </div>
    </div>

    <div>
        <label class="text-sm font-semibold text-slate-200">Descripción</label>
        <textarea name="description" rows="4" class="mt-2 w-full rounded-2xl border border-slate-700/70 bg-slate-950/60 px-4 py-3 text-sm text-white focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30">{{ old('description', $product->description) }}</textarea>
    </div>

    <div class="grid gap-6 md:grid-cols-3">
        <div>
            <label class="text-sm font-semibold text-slate-200">Precio ({{ old('currency', $product->currency ?? 'USD') }})</label>
            <input type="number" name="price" min="0" step="0.01" value="{{ $price }}" required class="mt-2 w-full rounded-2xl border border-slate-700/70 bg-slate-950/60 px-4 py-3 text-sm text-white focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30">
        </div>
        <div>
            <label class="text-sm font-semibold text-slate-200">Moneda</label>
            <input type="text" name="currency" value="{{ old('currency', $product->currency ?? 'USD') }}" maxlength="3" required class="mt-2 w-full uppercase rounded-2xl border border-slate-700/70 bg-slate-950/60 px-4 py-3 text-sm text-white tracking-[0.3em] focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30">
        </div>
        <div>
            <label class="text-sm font-semibold text-slate-200">Fecha de lanzamiento</label>
            <input type="date" name="release_date" value="{{ old('release_date', optional($product->release_date)->format('Y-m-d')) }}" class="mt-2 w-full rounded-2xl border border-slate-700/70 bg-slate-950/60 px-4 py-3 text-sm text-white focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30">
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <label class="text-sm font-semibold text-slate-200">Categoría</label>
            <select name="category_id" class="mt-2 w-full rounded-2xl border border-slate-700/70 bg-slate-950/60 px-4 py-3 text-sm text-white focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30">
                <option value="">Sin categoría</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm font-semibold text-slate-200">Portada (URL o ruta)</label>
            <input type="text" name="cover_image" value="{{ old('cover_image', $product->cover_image) }}" class="mt-2 w-full rounded-2xl border border-slate-700/70 bg-slate-950/60 px-4 py-3 text-sm text-white focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30">
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <label class="text-sm font-semibold text-slate-200">Plataformas</label>
            <div class="mt-2 grid grid-cols-2 gap-2 rounded-2xl border border-slate-700/70 bg-slate-950/60 p-4 text-sm text-white md:grid-cols-3">
                @forelse ($platforms as $platform)
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="platforms[]" value="{{ $platform->id }}" @checked(in_array($platform->id, $selectedPlatforms, true)) class="h-4 w-4 rounded border-slate-600 bg-slate-800 text-pink-500 focus:ring-pink-500/40">
                        <span>{{ $platform->name }}</span>
                    </label>
                @empty
                    <p class="text-xs text-slate-400">
                        No hay plataformas registradas todavía.
                    </p>
                @endforelse
            </div>
        </div>
        <div class="space-y-4">
            <label class="text-sm font-semibold text-slate-200">Configuración</label>
            <div class="flex items-center gap-3 rounded-2xl border border-slate-700/70 bg-slate-950/60 p-4 text-sm text-white">
                <input
                    type="checkbox"
                    name="is_digital"
                    value="1"
                    x-model="digital"
                    @checked($isDigital)
                    class="h-5 w-5 rounded border-slate-600 bg-slate-800 text-pink-500 focus:ring-pink-500/40"
                >
                <span>Producto digital (entrega inmediata)</span>
            </div>
            <div class="flex items-center gap-3 rounded-2xl border border-slate-700/70 bg-slate-950/60 p-4 text-sm text-white">
                <input
                    type="checkbox"
                    name="active"
                    value="1"
                    @checked($active)
                    class="h-5 w-5 rounded border-slate-600 bg-slate-800 text-pink-500 focus:ring-pink-500/40"
                >
                <span>Producto activo en el catálogo</span>
            </div>
        </div>
    </div>

    <div x-show="!digital" x-cloak>
        <label class="text-sm font-semibold text-slate-200">Inventario disponible</label>
        <input type="number" name="inventory_quantity" min="0" value="{{ $inventoryQuantity }}" class="mt-2 w-full rounded-2xl border border-slate-700/70 bg-slate-950/60 px-4 py-3 text-sm text-white focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30">
        <p class="mt-2 text-xs text-slate-400">
            Este campo solo aplica para productos físicos. Para digitales el stock es ilimitado.
        </p>
    </div>
</div>

