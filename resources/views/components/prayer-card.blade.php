@props(['prayer'])
<figure {{ $attributes->merge(['class' => 'card h-full']) }}>
    <x-icon name="candle" class="size-6 text-gold-500" />
    <blockquote class="mt-3 text-lg leading-relaxed text-ink">{{ $prayer->message }}</blockquote>
    <figcaption class="mt-4 text-base text-muted">
        <span class="font-semibold text-ink">{{ $prayer->displayName() }}</span>
        · {{ __('site.prayers.from', ['country' => $prayer->country]) }}
    </figcaption>
</figure>
