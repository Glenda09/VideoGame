@php
    $parentId = old('parent_id', $category->parent_id);
@endphp

<div class="space-y-6">
    <div>
        <label class="text-sm font-semibold text-slate-200">Nombre</label>
        <input type="text" name="name" value="{{ old('name', $category->name) }}" required class="mt-2 w-full rounded-2xl border border-slate-700/70 bg-slate-950/60 px-4 py-3 text-sm text-white focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30">
    </div>

    <div>
        <label class="text-sm font-semibold text-slate-200">Categoría padre</label>
        <select name="parent_id" class="mt-2 w-full rounded-2xl border border-slate-700/70 bg-slate-950/60 px-4 py-3 text-sm text-white focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30">
            <option value="">Sin padre (categoría raíz)</option>
            @foreach ($parents as $parent)
                <option value="{{ $parent->id }}" @selected($parentId == $parent->id)>
                    {{ $parent->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>

