@props(['url' => route('home'), 'text' => __('site.help.share_text')])
@php
    $encodedUrl = urlencode($url);
    $encodedText = urlencode($text);
    $links = [
        'facebook' => ['Facebook', "https://www.facebook.com/sharer/sharer.php?u={$encodedUrl}"],
        // The web share dialog needs a Facebook app ID; without one, open the Messenger app (phones).
        'messenger' => ['Messenger', config('services.facebook.app_id')
            ? 'https://www.facebook.com/dialog/send?link='.$encodedUrl.'&app_id='.config('services.facebook.app_id').'&redirect_uri='.$encodedUrl
            : "fb-messenger://share/?link={$encodedUrl}"],
        'whatsapp' => ['WhatsApp', "https://wa.me/?text={$encodedText}%20{$encodedUrl}"],
        'x' => ['X', "https://x.com/intent/post?text={$encodedText}&url={$encodedUrl}"],
    ];
@endphp
<div {{ $attributes->merge(['class' => 'flex flex-wrap gap-2']) }}>
    @foreach ($links as $icon => [$label, $href])
        <a href="{{ $href }}" target="_blank" rel="noopener" class="btn-quiet min-h-11 px-4 py-2 text-base">
            <x-icon :name="$icon" class="size-5" /> {{ $label }}
        </a>
    @endforeach
    <button type="button" x-data="copy(@js($url))" @click="copy()" class="btn-quiet min-h-11 px-4 py-2 text-base">
        <x-icon name="link" class="size-5" x-show="!copied" />
        <x-icon name="check" class="size-5 text-sage-700" x-show="copied" x-cloak />
        <span x-show="!copied">{{ __('site.help.copy_link') }}</span>
        <span x-show="copied" x-cloak role="status">{{ __('site.help.copied') }}</span>
    </button>
</div>
