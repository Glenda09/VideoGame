@props([
    'rating' => 0,
    'count' => null,
    'size' => 'sm',
])

@php
    $full = (int) floor($rating);
    $half = $rating - $full >= 0.5;
    $empty = 5 - $full - ($half ? 1 : 0);
    $sizes = [
        'xs' => 'h-3 w-3',
        'sm' => 'h-4 w-4',
        'md' => 'h-5 w-5',
    ];
    $iconSize = $sizes[$size] ?? $sizes['sm'];
    $gradientId = 'half-star-'.uniqid();
@endphp

<div class="inline-flex items-center gap-1 text-[11px] font-medium text-pink-300">
    <div class="flex items-center gap-1">
        @for ($i = 0; $i < $full; $i++)
            <svg xmlns="http://www.w3.org/2000/svg" class="{{ $iconSize }} text-pink-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path d="M9.049.927a1 1 0 011.902 0l1.175 3.617a1 1 0 00.95.69h3.8a1 1 0 01.589 1.806l-3.073 2.233a1 1 0 00-.364 1.118l1.175 3.617a1 1 0 01-1.538 1.118L10 12.347l-3.865 2.782a1 1 0 01-1.538-1.118l1.175-3.617a1 1 0 00-.364-1.118L2.335 7.04a1 1 0 01.589-1.806h3.8a1 1 0 00.95-.69L9.05.927z" />
            </svg>
        @endfor

        @if ($half)
            <svg xmlns="http://www.w3.org/2000/svg" class="{{ $iconSize }} text-pink-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <defs>
                    <linearGradient id="{{ $gradientId }}">
                        <stop offset="50%" stop-color="currentColor" />
                        <stop offset="50%" stop-color="transparent" />
                    </linearGradient>
                </defs>
                <path fill="url(#{{ $gradientId }})" d="M9.049.927a1 1 0 011.902 0l1.175 3.617a1 1 0 00.95.69h3.8a1 1 0 01.589 1.806l-3.073 2.233a1 1 0 00-.364 1.118l1.175 3.617a1 1 0 01-1.538 1.118L10 12.347l-3.865 2.782a1 1 0 01-1.538-1.118l1.175-3.617a1 1 0 00-.364-1.118L2.335 7.04a1 1 0 01.589-1.806h3.8a1 1 0 00.95-.69L9.05.927z" />
            </svg>
        @endif

        @for ($i = 0; $i < $empty; $i++)
            <svg xmlns="http://www.w3.org/2000/svg" class="{{ $iconSize }} text-slate-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path d="M9.049.927a1 1 0 011.902 0l1.175 3.617a1 1 0 00.95.69h3.8a1 1 0 01.589 1.806l-3.073 2.233a1 1 0 00-.364 1.118l1.175 3.617a1 1 0 01-1.538 1.118L10 12.347l-3.865 2.782a1 1 0 01-1.538-1.118l1.175-3.617a1 1 0 00-.364-1.118L2.335 7.04a1 1 0 01.589-1.806h3.8a1 1 0 00.95-.69L9.05.927z" />
            </svg>
        @endfor
    </div>

    <span class="hidden sm:inline-flex text-[11px] uppercase tracking-wide text-slate-400">
        {{ number_format($rating, 1) }}@if($count) <span class="ml-1 text-slate-600">({{ $count }})</span>@endif
    </span>
</div>
