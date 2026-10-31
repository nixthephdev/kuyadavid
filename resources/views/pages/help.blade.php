@php
    $gcashNumber = setting('gcash_number');
    $gofundme = setting('gofundme_url');
    $paypal = setting('paypal_email');
    $paypalLink = setting('paypal_link');
    $banks = collect(['bank', 'bank2'])
        ->map(fn ($key) => [
            'key' => $key,
            'name' => setting("{$key}_name"),
            'account_name' => setting("{$key}_account_name"),
            'number' => setting("{$key}_account_number"),
            'qr' => setting("{$key}_qr"),
            'extra' => $key === 'bank' ? setting('bank_extra') : null,
        ])
        ->filter(fn ($bank) => $bank['number'] || $bank['qr']);
    $hasAnyWay = $gcashNumber || setting('gcash_qr') || $banks->isNotEmpty() || $gofundme || $paypal || $paypalLink;
    $messageUrl = setting('facebook_url') ?: route('contact').'?topic=supplies';
    $shareMessage = localized('share_message');
@endphp
<x-layouts.site :title="__('site.help.title')" :description="__('site.help.intro')" livewire>
    <header class="container-page pt-10 pb-8 md:pt-16">
        <h1 class="text-4xl font-semibold sm:text-5xl">{{ __('site.help.title') }}</h1>
        <p class="mt-4 max-w-2xl text-xl text-muted">{{ __('site.help.intro') }}</p>
        <p class="mt-6 flex max-w-2xl items-start gap-3 rounded-2xl border border-sage-200 bg-sage-50 p-4 text-lg font-semibold text-sage-900">
            <x-icon name="shield" class="mt-0.5 size-6 text-sage-700" /> {{ __('site.help.direct') }}
        </p>
    </header>

    @if ($fund->hasGoal())
        <div class="container-page mb-10"><x-progress :fund="$fund" class="max-w-3xl" /></div>
    @endif

    <section class="container-page grid gap-6 md:grid-cols-2 lg:grid-cols-3" aria-label="{{ __('site.help.title') }}">
        {{-- GCash --}}
        @if ($gcashNumber || setting('gcash_qr'))
            <article id="gcash" class="card flex flex-col gap-4">
                <h2 class="flex items-center gap-3 text-2xl font-semibold"><x-icon name="wallet" class="size-7 text-sage-700" /> {{ __('site.help.gcash') }}</h2>
                <p class="text-muted">{{ __('site.help.gcash_hint') }}</p>
                @if ($qr = setting('gcash_qr'))
                    <x-photo :src="$qr" :alt="__('site.help.qr_alt')" sizes="320px" class="mx-auto max-h-80 w-auto max-w-64 rounded-xl border border-line bg-white object-contain" />
                @endif
                @if ($name = setting('gcash_name'))
                    <div class="rounded-xl bg-cream px-4 py-3">
                        <p class="text-sm font-semibold text-muted">{{ __('site.help.name') }}</p>
                        <p class="text-xl font-semibold">{{ $name }}</p>
                    </div>
                @endif
                @if ($gcashNumber)
                    <x-copy-field :label="__('site.help.number')" :value="$gcashNumber" :button="__('site.help.copy_number')" />
                @endif
            </article>
        @endif

        {{-- Banks --}}
        @foreach ($banks as $bank)
            <article id="{{ $bank['key'] }}" class="card flex flex-col gap-4">
                <h2 class="flex items-center gap-3 text-2xl font-semibold"><x-icon name="bank" class="size-7 text-sage-700" /> {{ $bank['name'] ?: __('site.help.bank') }}</h2>
                <p class="text-muted">{{ $bank['number'] ? __('site.help.bank_hint') : __('site.help.qr_only') }}</p>
                @if ($bank['qr'])
                    <x-photo :src="$bank['qr']" :alt="__('site.help.bank_qr_alt', ['bank' => $bank['name'] ?: __('site.help.bank')])" sizes="320px" class="mx-auto max-h-80 w-auto max-w-64 rounded-xl border border-line bg-white object-contain" />
                @endif
                @if ($bank['account_name'])
                    <div class="rounded-xl bg-cream px-4 py-3">
                        <p class="text-sm font-semibold text-muted">{{ __('site.help.name') }}</p>
                        <p class="text-xl font-semibold">{{ $bank['account_name'] }}</p>
                    </div>
                @endif
                @if ($bank['number'])
                    <x-copy-field :label="__('site.help.account_number')" :value="$bank['number']" />
                @endif
                @if ($bank['extra'])
                    <div class="rounded-xl bg-cream px-4 py-3">
                        <p class="text-sm font-semibold text-muted">{{ __('site.help.details') }}</p>
                        <p class="whitespace-pre-line">{{ $bank['extra'] }}</p>
                    </div>
                @endif
            </article>
        @endforeach

        {{-- From abroad: GoFundMe and/or PayPal --}}
        @if ($gofundme || $paypal || $paypalLink)
            <article id="abroad" class="card flex flex-col gap-4 border-gold-300 bg-gold-50">
                <h2 class="flex items-center gap-3 text-2xl font-semibold"><x-icon name="globe" class="size-7 text-sage-700" /> {{ __('site.help.abroad') }}</h2>
                @if ($gofundme)
                    <p class="text-muted">{{ __('site.help.gofundme_hint') }}</p>
                    @if ($organizer = setting('gofundme_organizer'))
                        <p class="text-base text-muted">{{ __('site.help.gofundme_by', ['name' => $organizer]) }}</p>
                    @endif
                    <a href="{{ $gofundme }}" target="_blank" rel="noopener" class="btn-primary">
                        {{ __('site.help.open_gofundme') }} <x-icon name="external" class="size-5" />
                    </a>
                @endif
                @if ($paypal || $paypalLink)
                    <p class="mt-2 font-semibold">{{ __('site.help.paypal') }} · <span class="font-normal text-muted">{{ __('site.help.paypal_hint') }}</span></p>
                    @if ($paypal)
                        <x-copy-field :label="__('site.help.email')" :value="$paypal" class="bg-paper" />
                    @endif
                    @if ($paypalLink)
                        <a href="{{ $paypalLink }}" target="_blank" rel="noopener" class="btn-secondary">{{ __('site.help.open_paypal') }} <x-icon name="external" class="size-5" /></a>
                    @endif
                @endif
            </article>
        @endif

        @unless ($hasAnyWay)
            <p class="card text-lg text-muted">{{ __('site.help.not_set') }}</p>
        @endunless
    </section>

    @if ($needs->isNotEmpty())
        <section class="container-page mt-16" aria-labelledby="needs-title">
            <h2 id="needs-title" class="section-title">{{ __('site.home.needs_title') }}</h2>
            <ul class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3" role="list">
                @foreach ($needs as $need)
                    <li><x-need-card :need="$need" /></li>
                @endforeach
            </ul>
        </section>
    @endif

    @if ($shop->isNotEmpty())
        <section id="shop" class="container-page mt-20" aria-labelledby="shop-title">
            <h2 id="shop-title" class="section-title">{{ __('site.help.shop_title') }}</h2>
            <p class="mt-3 max-w-2xl text-xl text-muted">{{ __('site.help.shop_intro') }}</p>
            <ul class="mt-8 grid gap-6 md:grid-cols-3" role="list">
                @foreach ($shop as $item)
                    <li class="card flex flex-col overflow-hidden p-0">
                        @if ($item->photo)
                            <a href="{{ \App\Support\ImageStore::url($item->photo) }}" target="_blank" rel="noopener" class="block bg-cream">
                                <x-photo :src="$item->photo" :alt="__('site.help.shop_photo_alt', ['title' => $item->title])"
                                         sizes="(min-width: 768px) 33vw, 100vw" class="aspect-[4/5] w-full object-cover object-top" />
                            </a>
                        @endif
                        <div class="flex flex-1 flex-col p-6">
                            <h3 class="text-2xl font-semibold">{{ $item->title }}</h3>
                            @if ($item->description)
                                <p class="mt-2 flex-1 text-muted">{{ $item->description }}</p>
                            @endif
                            <a href="{{ $messageUrl }}" @if (setting('facebook_url')) target="_blank" rel="noopener" @endif class="btn-secondary mt-5">
                                <x-icon name="message" class="size-5" /> {{ __('site.help.shop_order') }}
                            </a>
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    <section class="container-page mt-20" aria-labelledby="other-title">
        <h2 id="other-title" class="section-title">{{ __('site.help.other_title') }}</h2>
        <div class="mt-8 grid gap-6 md:grid-cols-2">
            <article class="card md:col-span-2">
                <h3 class="flex items-center gap-3 text-2xl font-semibold"><x-icon name="share" class="size-7 text-sage-700" /> {{ __('site.help.share_title') }}</h3>
                <p class="mt-3 text-muted">{{ __('site.help.share_body') }}</p>
                <x-share-buttons class="mt-5" :text="$shareMessage ?: __('site.help.share_text')" />

                @if ($shareMessage)
                    <div class="mt-6 rounded-2xl bg-cream p-5" x-data="copy(@js($shareMessage."\n\n".route('home')))">
                        <p class="font-semibold">{{ __('site.help.kit_title') }}</p>
                        <p class="mt-1 text-base text-muted">{{ __('site.help.kit_intro') }}</p>
                        <blockquote class="mt-4 border-l-4 border-gold-300 pl-4 text-lg">{{ $shareMessage }}<br><span class="text-sage-800">{{ route('home') }}</span></blockquote>
                        <button type="button" class="btn-quiet mt-4 min-h-11 px-4 py-2 text-base" @click="copy()">
                            <x-icon name="copy" class="size-5" x-show="!copied" />
                            <x-icon name="check" class="size-5 text-sage-700" x-show="copied" x-cloak />
                            <span x-show="!copied">{{ __('site.help.copy_message') }}</span>
                            <span x-show="copied" x-cloak role="status">{{ __('site.help.copied') }}</span>
                        </button>
                    </div>
                @endif
            </article>

            <article class="card">
                <h3 class="flex items-center gap-3 text-2xl font-semibold"><x-icon name="candle" class="size-7 text-gold-500" /> {{ __('site.help.pray_title') }}</h3>
                <p class="mt-3 text-muted">{{ __('site.help.pray_body') }}</p>
                <a href="{{ route('prayers') }}" class="btn-secondary mt-5">{{ __('site.cta.leave_prayer') }}</a>
            </article>

            <article class="card">
                <h3 class="flex items-center gap-3 text-2xl font-semibold"><x-icon name="package" class="size-7 text-sage-700" /> {{ __('site.help.supplies_title') }}</h3>
                <p class="mt-3 text-muted">{{ __('site.help.supplies_body') }}</p>
                <a href="{{ route('contact') }}?topic=supplies" class="btn-secondary mt-5">{{ __('site.help.supplies_button') }}</a>
            </article>

            <article id="connect" class="card md:col-span-2">
                <h3 class="flex items-center gap-3 text-2xl font-semibold"><x-icon name="stethoscope" class="size-7 text-sage-700" /> {{ __('site.help.doctor_title') }}</h3>
                <p class="mt-3 mb-6 max-w-2xl text-muted">{{ __('site.help.doctor_body') }}</p>
                <livewire:contact-form topic="medical" />
            </article>
        </div>
    </section>
</x-layouts.site>
