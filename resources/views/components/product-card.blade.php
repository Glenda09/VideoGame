@props(['product'])

@php
    use Illuminate\Support\Str;

    $rating = $product->rating_avg ?? 0;
    $reviewCount = $product->approved_reviews_count ?? null;
@endphp

<a href="{{ route('products.show', $product) }}" class="group block h-full rounded-3xl border border-slate-800 bg-slate-900/70 p-4 shadow-lg shadow-slate-950/20 transition hover:border-pink-500/70 hover:shadow-pink-500/10">
    <div class="relative overflow-hidden rounded-2xl bg-slate-800/80">
        <img src="{{ $product->coverImageUrl() }}" alt="Portada de {{ $product->title }}" class="h-48 w-full rounded-2xl object-cover transition duration-300 group-hover:scale-105" loading="lazy" />
        <div class="absolute inset-x-0 bottom-0 flex items-center justify-between px-3 pb-3 text-[11px] text-slate-200">
            <span class="rounded-full bg-slate-900/85 px-2 py-1 uppercase tracking-widest {{ $product->is_digital ? 'text-pink-300' : 'text-indigo-300' }}">
                {{ $product->is_digital ? 'Digital' : 'Físico' }}
            </span>
            @if ($product->release_date)
                <span class="rounded-full bg-slate-900/75 px-2 py-1 text-[10px] uppercase tracking-wide text-slate-400">
                    {{ optional($product->release_date)->format('M Y') }}
                </span>
            @endif
        </div>
    </div>

    <div class="mt-4 flex flex-col gap-3">
        <div>
            <h3 class="text-lg font-semibold text-white transition group-hover:text-pink-200">
                {{ $product->title }}
            </h3>
            @if ($product->category)
                <p class="mt-1 text-xs uppercase tracking-widest text-slate-500">
                    {{ $product->category->name }}
                </p>
            @endif
        </div>

        @if ($product->platforms->isNotEmpty())
            <div class="flex flex-wrap gap-2">
                @foreach ($product->platforms->take(3) as $platform)
                    <span class="rounded-full bg-slate-800/60 px-2 py-1 text-[10px] uppercase tracking-wide text-slate-300">
                        {{ $platform->name }}
                    </span>
                @endforeach
                @if ($product->platforms->count() > 3)
                    <span class="rounded-full bg-slate-800/60 px-2 py-1 text-[10px] uppercase tracking-wide text-slate-500">
                        +{{ $product->platforms->count() - 3 }}
                    </span>
                @endif
            </div>
        @endif

        <p class="line-clamp-2 text-sm text-slate-400">
            {{ Str::limit(strip_tags($product->description), 110) }}
        </p>

        <div class="flex items-center justify-between">
            <x-price-tag :amount="$product->price_cents" :currency="$product->currency" />
            <x-rating-stars :rating="$rating" :count="$reviewCount" size="sm" />
        </div>

        <div class="flex items-center justify-between text-xs text-slate-500">
            <span class="font-semibold uppercase tracking-wide">SKU {{ $product->sku }}</span>
            <span class="inline-flex items-center gap-1 text-pink-300 group-hover:text-pink-200">
                Ver detalle
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7" />
                </svg>
            </span>
        </div>
    </div>
</a>
