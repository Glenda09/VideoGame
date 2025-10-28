<div class="space-y-6">
    <div>
        <label class="text-sm font-semibold text-slate-200">Nombre</label>
        <input type="text" name="name" value="{{ old('name', $platform->name) }}" required class="mt-2 w-full rounded-2xl border border-slate-700/70 bg-slate-950/60 px-4 py-3 text-sm text-white focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30">
    </div>
    <div>
        <label class="text-sm font-semibold text-slate-200">Slug (opcional)</label>
        <input type="text" name="slug" value="{{ old('slug', $platform->slug) }}" class="mt-2 w-full rounded-2xl border border-slate-700/70 bg-slate-950/60 px-4 py-3 text-sm text-white focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30">
        <p class="mt-2 text-xs text-slate-400">Si lo dejas vacío se generará automáticamente a partir del nombre.</p>
    </div>
</div>

