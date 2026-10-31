<x-layouts.site>
    {{-- Hero --}}
    <section class="container-page grid items-center gap-8 pt-6 pb-12 md:grid-cols-2 md:gap-12 md:pt-12 lg:pb-20">
        <div class="md:order-2">
            @if ($hero = setting('hero_photo'))
                <div class="relative">
                    <div class="absolute -inset-3 -z-10 rotate-2 rounded-[2rem] bg-gold-100" aria-hidden="true"></div>
                    <x-photo :src="$hero" :alt="setting('hero_alt', 'David and his wife')" eager
                             sizes="(min-width: 768px) 50vw, 100vw"
                             class="aspect-[4/3] w-full rounded-[1.75rem] object-cover shadow-lg md:aspect-[4/5]" />
                </div>
            @else
                <div class="flex aspect-[4/3] items-center justify-center rounded-[1.75rem] bg-gradient-to-br from-sage-100 via-cream to-gold-100 md:aspect-[4/5]" aria-hidden="true">
                    <x-icon name="sprout" class="size-28 text-sage-500/60" />
                </div>
            @endif
        </div>

        <div class="md:order-1">
            <p class="eyebrow">{{ localized('hero_eyebrow', __('site.name')) }}</p>
            <h1 class="mt-3 text-4xl font-semibold sm:text-5xl lg:text-6xl">{{ localized('hero_headline') }}</h1>
            <p class="mt-5 max-w-xl text-xl text-muted sm:text-2xl">{{ localized('hero_subline') }}</p>
            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('help') }}" class="btn-primary text-xl"><x-icon name="heart" class="size-6" /> {{ __('site.cta.help') }}</a>
                <a href="{{ route('story') }}" class="btn-secondary text-xl"><x-icon name="book" class="size-6" /> {{ __('site.cta.story') }}</a>
            </div>
        </div>
    </section>

    {{-- Progress (hidden when no goal is set) --}}
    @if ($fund->hasGoal())
        <div class="container-page">
            <x-progress :fund="$fund" class="mx-auto max-w-3xl" />
        </div>
    @endif

    {{-- Needs --}}
    @if ($needs->isNotEmpty())
        <section class="container-page mt-20" aria-labelledby="needs-title">
            <div class="max-w-2xl">
                <h2 id="needs-title" class="section-title">{{ __('site.home.needs_title') }}</h2>
                <p class="mt-3 text-xl text-muted">{{ __('site.home.needs_intro') }}</p>
            </div>
            <ul class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3" role="list">
                @foreach ($needs as $need)
                    <li><x-need-card :need="$need" /></li>
                @endforeach
            </ul>
            <div class="mt-8">
                <a href="{{ route('help') }}" class="btn-primary"><x-icon name="heart" class="size-5" /> {{ __('site.cta.help') }}</a>
            </div>
        </section>
    @endif

    {{-- Trust --}}
    <section class="container-page mt-20">
        <div class="flex flex-col gap-5 rounded-3xl bg-sage-900 p-8 text-white sm:flex-row sm:items-center sm:p-10">
            <x-icon name="shield" class="size-12 text-gold-300" />
            <div class="flex-1">
                <h2 class="text-2xl font-semibold text-white">{{ __('site.home.trust_title') }}</h2>
                <p class="mt-2 text-lg text-sage-100">{{ __('site.home.trust_body') }}</p>
            </div>
            <a href="{{ route('transparency') }}" class="btn shrink-0 bg-white text-sage-900 hover:bg-gold-50">{{ __('site.cta.see_transparency') }}</a>
        </div>
    </section>

    {{-- Latest updates --}}
    @if ($posts->isNotEmpty())
        <section class="container-page mt-20" aria-labelledby="updates-title">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <h2 id="updates-title" class="section-title">{{ __('site.home.updates_title') }}</h2>
                <a href="{{ route('updates.index') }}" class="link">{{ __('site.cta.all_updates') }}</a>
            </div>
            <div class="mt-8 grid gap-6 md:grid-cols-3">
                @foreach ($posts as $post)
                    <x-post-card :post="$post" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- Prayer wall preview --}}
    <section class="container-page mt-20" aria-labelledby="prayers-title">
        <div class="rounded-3xl bg-gold-50 p-6 sm:p-10">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h2 id="prayers-title" class="section-title">{{ __('site.home.prayers_title') }}</h2>
                    <p class="mt-2 text-lg text-muted">
                        {{ $prayerCount ? __('site.home.prayers_intro', ['count' => number_format($prayerCount)]) : __('site.home.prayers_empty') }}
                    </p>
                </div>
                <a href="{{ route('prayers') }}" class="btn-primary"><x-icon name="candle" class="size-5" /> {{ __('site.cta.leave_prayer') }}</a>
            </div>
            @if ($prayers->isNotEmpty())
                <div class="mt-8 grid gap-4 md:grid-cols-3">
                    @foreach ($prayers as $prayer)
                        <x-prayer-card :prayer="$prayer" />
                    @endforeach
                </div>
            @endif
        </div>
    </section>
</x-layouts.site>
