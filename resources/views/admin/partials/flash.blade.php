@if (session('status'))
    <div class="mb-6 rounded-xl border border-emerald-500/60 bg-emerald-900/40 p-4 text-sm text-emerald-100">
        {{ session('status') }}
    </div>
@endif

@if ($errors->any())
    <div class="mb-6 rounded-xl border border-red-500/60 bg-red-900/40 p-4 text-sm text-red-100">
        <p class="font-semibold text-red-200">Se encontraron algunos problemas:</p>
        <ul class="mt-3 space-y-1 text-sm text-red-100/90">
            @foreach ($errors->all() as $error)
                <li>• {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

