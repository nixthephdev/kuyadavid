@use('App\Support\ImageStore')
@props([
    'title' => null,
    'description' => null,
    'image' => null,
    'type' => 'website',
    // Pages with a Livewire form load Livewire; the rest get a lighter Alpine-only bundle.
    'livewire' => false,
])
@php
    $siteName = setting('site_name', __('site.name'));
    $pageTitle = $title ? $title.' · '.$siteName : setting('meta_title', $siteName);
    $metaDescription = $description ?: localized('meta_description', __('site.meta.description'));
    $ogImage = $image ?: (ImageStore::url(setting('hero_photo')) ?: asset('images/og-default.jpg'));
    $locale = app()->getLocale();

    $nav = [
        'home' => __('site.nav.home'),
        'story' => __('site.nav.story'),
        'help' => __('site.nav.help'),
        'updates.index' => __('site.nav.updates'),
        'transparency' => __('site.nav.transparency'),
        'prayers' => __('site.nav.prayers'),
        'contact' => __('site.nav.contact'),
    ];

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'WebPage',
        'name' => $pageTitle,
        'description' => $metaDescription,
        'url' => url()->current(),
        'inLanguage' => $locale === 'fil' ? 'fil-PH' : 'en',
        'image' => $ogImage,
        'isPartOf' => ['@type' => 'WebSite', 'name' => $siteName, 'url' => route('home')],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta name="theme-color" content="#fbf8f3">

    <meta property="og:type" content="{{ $type }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:locale" content="{{ $locale === 'fil' ? 'fil_PH' : 'en_US' }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">

    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="32x32">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>

    {{ $head ?? '' }}

    @vite(['resources/css/app.css', $livewire ? 'resources/js/app.js' : 'resources/js/site.js'])
    @if ($livewire)
        @livewireStyles
    @endif
</head>
<body class="flex min-h-screen flex-col">
    <a href="#main" class="sr-only z-50 rounded-lg bg-ink px-4 py-2 text-white focus:not-sr-only focus:fixed focus:top-3 focus:left-3">{{ __('site.skip') }}</a>

    <header x-data="{ open: false }" class="sticky top-0 z-40 border-b border-line/80 bg-cream/95 backdrop-blur" @keydown.escape.window="open = false">
        <div class="container-page flex h-16 items-center justify-between gap-4 lg:h-20">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 no-underline">
                <x-icon name="sprout" class="size-8 text-sage-700" />
                <span class="font-serif text-xl font-semibold whitespace-nowrap text-ink lg:text-2xl">{{ $siteName }}</span>
            </a>

            <nav class="hidden items-center gap-0.5 xl:flex" aria-label="Main">
                @foreach ($nav as $route => $label)
                    <a href="{{ route($route) }}"
                       @class([
                           'rounded-full px-2.5 py-2 text-base font-semibold whitespace-nowrap no-underline transition-colors',
                           'bg-sage-100 text-sage-900' => request()->routeIs($route === 'updates.index' ? 'updates.*' : $route),
                           'text-muted hover:text-ink' => ! request()->routeIs($route === 'updates.index' ? 'updates.*' : $route),
                       ])
                       @if (request()->routeIs($route === 'updates.index' ? 'updates.*' : $route)) aria-current="page" @endif>{{ $label }}</a>
                @endforeach
            </nav>

            <div class="flex items-center gap-2">
                <x-lang-toggle class="flex" />
                <a href="{{ route('help') }}" class="btn-primary hidden min-h-11 px-5 py-2 text-base whitespace-nowrap sm:inline-flex">
                    <x-icon name="heart" class="size-5" /> {{ __('site.cta.help') }}
                </a>
                <button type="button" class="inline-flex size-12 items-center justify-center rounded-full border border-line bg-paper xl:hidden"
                        @click="open = !open" :aria-expanded="open.toString()" aria-controls="mobile-menu">
                    <span class="sr-only" x-text="open ? @js(__('site.close')) : @js(__('site.menu'))">{{ __('site.menu') }}</span>
                    <x-icon name="menu" class="size-6" x-show="!open" />
                    <x-icon name="close" class="size-6" x-show="open" x-cloak />
                </button>
            </div>
        </div>

        <nav id="mobile-menu" x-show="open" x-cloak x-transition.opacity class="border-t border-line bg-cream xl:hidden" aria-label="Mobile">
            <div class="container-page flex flex-col gap-1 py-4">
                @foreach ($nav as $route => $label)
                    <a href="{{ route($route) }}" class="rounded-xl px-4 py-3 text-xl font-semibold text-ink no-underline hover:bg-sage-50">{{ $label }}</a>
                @endforeach
                <div class="mt-3 px-4">
                    <a href="{{ route('help') }}" class="btn-primary w-full"><x-icon name="heart" class="size-5" /> {{ __('site.cta.help') }}</a>
                </div>
            </div>
        </nav>
    </header>

    <main id="main" class="flex-1">
        {{ $slot }}
    </main>

    <footer class="mt-20 border-t border-line bg-sage-50">
        <div class="container-page grid gap-10 py-12 md:grid-cols-[1.4fr_1fr]">
            <div>
                <p class="font-serif text-2xl text-sage-900 italic">{{ __('site.footer.verse') }}</p>
                <p class="mt-1 text-base font-semibold text-muted">{{ __('site.footer.verse_ref') }}</p>
                <p class="mt-8 max-w-xl text-base text-muted">{{ __('site.footer.volunteer') }}</p>
            </div>
            <div class="flex flex-col gap-2 text-base md:items-end">
                @foreach ($nav as $route => $label)
                    <a href="{{ route($route) }}" class="font-semibold text-sage-900 no-underline hover:underline">{{ $label }}</a>
                @endforeach
                @if ($fb = setting('facebook_url'))
                    <a href="{{ $fb }}" class="mt-2 inline-flex items-center gap-2 font-semibold text-sage-900 no-underline hover:underline" rel="noopener" target="_blank">
                        <x-icon name="facebook" class="size-5" /> Facebook
                    </a>
                @endif
                <a href="{{ route('admin.dashboard') }}" class="mt-4 text-sm text-muted no-underline hover:underline" rel="nofollow">{{ __('site.footer.admin') }}</a>
            </div>
        </div>
    </footer>

    @if ($livewire)
        @livewireScriptConfig
    @endif
</body>
</html>
