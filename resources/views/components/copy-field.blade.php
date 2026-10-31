@props(['label', 'value', 'button' => null])
<div {{ $attributes->merge(['class' => 'flex flex-wrap items-center justify-between gap-3 rounded-xl bg-cream px-4 py-3']) }} x-data="copy(@js($value))">
    <div class="min-w-0">
        <p class="text-sm font-semibold text-muted">{{ $label }}</p>
        <p class="font-mono text-xl font-semibold break-all text-ink">{{ $value }}</p>
    </div>
    <button type="button" class="btn-quiet min-h-11 px-4 py-2 text-base" @click="copy()">
        <x-icon name="copy" class="size-5" x-show="!copied" />
        <x-icon name="check" class="size-5 text-sage-700" x-show="copied" x-cloak />
        <span x-show="!copied">{{ $button ?? __('site.help.copy') }}</span>
        <span x-show="copied" x-cloak role="status">{{ __('site.help.copied') }}</span>
    </button>
</div>
