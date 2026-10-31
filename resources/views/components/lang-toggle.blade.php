<div {{ $attributes->merge(['class' => 'items-center rounded-full border border-line bg-paper p-1 text-sm font-bold']) }} role="group" aria-label="{{ __('site.language') }}">
    @foreach (['en' => 'EN', 'fil' => 'FIL'] as $code => $label)
        <a href="{{ route('locale', $code) }}" hreflang="{{ $code }}" lang="{{ $code }}"
           @class([
               'inline-flex min-h-9 min-w-11 items-center justify-center rounded-full px-3 no-underline',
               'bg-sage-700 text-white' => app()->getLocale() === $code,
               'text-muted hover:text-ink' => app()->getLocale() !== $code,
           ])
           @if (app()->getLocale() === $code) aria-current="true" @endif
           rel="nofollow">{{ $label }}</a>
    @endforeach
</div>
