@props([
    'amount' => 0,
    'currency' => config('store.currency', 'USD'),
    'compact' => false,
])

@php
    $value = (int) $amount;
    $formatted = number_format($value / 100, 2);
@endphp

<span class="inline-flex items-baseline gap-1 {{ $compact ? 'text-sm' : 'text-lg' }} font-semibold text-white">
    <span class="rounded-full bg-pink-500/10 px-2 py-0.5 text-xs uppercase tracking-widest text-pink-300">{{ $currency }}</span>
    <span class="{{ $compact ? 'text-base' : 'text-2xl' }} font-bold text-white drop-shadow-sm">
        {{ $formatted }}
    </span>
</span>
