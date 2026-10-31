@props(['need'])
@php
    $title = strtolower($need->title);
    $icon = match (true) {
        str_contains($title, 'medic') || str_contains($title, 'gamot') => 'pill',
        str_contains($title, 'hospital') || str_contains($title, 'ospital') || str_contains($title, 'bill') => 'hospital',
        str_contains($title, 'treat') || str_contains($title, 'therap') || str_contains($title, 'doctor') => 'stethoscope',
        str_contains($title, 'equip') || str_contains($title, 'suppl') => 'package',
        default => 'heart',
    };
@endphp
<article {{ $attributes->merge(['class' => 'card flex h-full flex-col']) }}>
    <span class="inline-flex size-12 items-center justify-center rounded-full bg-sage-100 text-sage-800">
        <x-icon :name="$icon" class="size-6" />
    </span>
    <h3 class="mt-4 text-2xl font-semibold">{{ $need->title }}</h3>
    @if ($need->description)
        <p class="mt-2 flex-1 text-muted">{{ $need->description }}</p>
    @endif
    @if ($need->amount)
        <p class="mt-4 font-serif text-2xl font-semibold text-sage-800">{{ money($need->amount) }}</p>
    @endif
</article>
