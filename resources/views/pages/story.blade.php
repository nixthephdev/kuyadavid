@php
    $paragraphs = paragraphs($story);
    // Weave photos through the story: one after every other paragraph, the rest in a gallery.
    $inline = [];
    $remaining = $photos;
    foreach ($paragraphs as $i => $p) {
        if ($i % 2 === 1 && $remaining) {
            $inline[$i] = array_shift($remaining);
        }
    }
@endphp
<x-layouts.site :title="__('site.story.title')" :description="\Illuminate\Support\Str::limit($paragraphs[0] ?? '', 155)">
    <article>
        <header class="container-text pt-10 pb-6 md:pt-16">
            <p class="eyebrow">{{ __('site.name') }}</p>
            <h1 class="mt-3 text-4xl font-semibold sm:text-5xl">{{ localized('story_title', __('site.story.title')) }}</h1>
            @if ($isFilipino)
                <p class="mt-4 rounded-xl bg-gold-50 px-4 py-3 text-base text-gold-700">{{ __('site.story.fil_note') }}</p>
            @endif
        </header>

        <div class="container-text prose-story">
            @foreach ($paragraphs as $i => $paragraph)
                <p>{!! nl2br(e($paragraph)) !!}</p>
                @isset($inline[$i])
                    <figure class="my-10 -mx-4 sm:mx-0">
                        <x-photo :src="$inline[$i]['src']" :alt="$inline[$i]['alt'] ?? ''" sizes="(min-width: 672px) 640px, 100vw"
                                 class="w-full sm:rounded-2xl" />
                        @if (! empty($inline[$i]['caption']))
                            <figcaption class="mt-3 px-4 text-base text-muted sm:px-0">{{ $inline[$i]['caption'] }}</figcaption>
                        @endif
                    </figure>
                @endisset
            @endforeach
        </div>

        @if ($remaining)
            <div class="container-page mt-6 grid grid-cols-2 gap-3 md:grid-cols-3">
                @foreach ($remaining as $photo)
                    <x-photo :src="$photo['src']" :alt="$photo['alt'] ?? ''" sizes="(min-width: 768px) 33vw, 50vw"
                             class="aspect-square w-full rounded-2xl object-cover" />
                @endforeach
            </div>
        @endif
    </article>

    @if ($videos)
        <section class="container-page mt-16" aria-labelledby="videos-title">
            <h2 id="videos-title" class="section-title">{{ __('site.story.videos_title') }}</h2>
            <p class="mt-2 text-lg text-muted">{{ __('site.story.videos_intro') }}</p>
            <ul class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3" role="list">
                @foreach ($videos as $video)
                    <li>
                        <figure>
                            <video controls playsinline preload="none" class="aspect-[9/16] w-full rounded-2xl bg-ink object-cover"
                                   @if (! empty($video['poster'])) poster="{{ \App\Support\ImageStore::url($video['poster'], 'sm') }}" @endif>
                                <source src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($video['src']) }}" type="video/mp4">
                            </video>
                            @if (! empty($video['caption']))
                                <figcaption class="mt-3 text-base text-muted">{{ $video['caption'] }}</figcaption>
                            @endif
                        </figure>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    @if ($timeline->isNotEmpty())
        <section class="container-text mt-16" aria-labelledby="timeline-title">
            <h2 id="timeline-title" class="section-title">{{ __('site.story.timeline') }}</h2>
            <ol class="mt-8 border-l-2 border-sage-200 pl-0" role="list">
                @foreach ($timeline as $event)
                    <li class="relative pb-10 pl-8 last:pb-0">
                        <span class="absolute top-1.5 -left-[11px] size-5 rounded-full border-4 border-cream {{ $loop->last ? 'bg-gold-500' : 'bg-sage-700' }}" aria-hidden="true"></span>
                        <p class="text-base font-semibold tracking-wide text-gold-700 uppercase">{{ $event->when_label }}</p>
                        <h3 class="mt-1 text-2xl font-semibold">{{ $event->title }}</h3>
                        @if ($event->body)
                            <p class="mt-2 text-muted">{{ $event->body }}</p>
                        @endif
                    </li>
                @endforeach
            </ol>
        </section>
    @endif

    @if ($proof)
        <section id="proof" class="container-text mt-16" aria-labelledby="proof-title">
            <div class="card">
                <div class="flex items-center gap-3">
                    <x-icon name="shield" class="size-7 text-sage-700" />
                    <h2 id="proof-title" class="text-2xl font-semibold">{{ __('site.story.proof_title') }}</h2>
                </div>
                <p class="mt-2 text-base text-muted">{{ __('site.story.proof_note') }}</p>
                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    @foreach ($proof as $doc)
                        <a href="{{ \App\Support\ImageStore::url($doc) }}" target="_blank" rel="noopener" class="block overflow-hidden rounded-xl border border-line">
                            <x-photo :src="$doc" :alt="__('site.story.proof_alt')" sizes="(min-width: 640px) 320px, 100vw" class="w-full" />
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="container-text mt-16">
        <div class="rounded-3xl bg-sage-50 p-8 text-center">
            <p class="font-serif text-2xl text-sage-900">{{ __('site.help.thanks') }}</p>
            <div class="mt-6 flex flex-col justify-center gap-3 sm:flex-row">
                <a href="{{ route('help') }}" class="btn-primary"><x-icon name="heart" class="size-5" /> {{ __('site.cta.help') }}</a>
                <a href="{{ route('prayers') }}" class="btn-secondary"><x-icon name="candle" class="size-5" /> {{ __('site.cta.leave_prayer') }}</a>
            </div>
            <x-share-buttons :url="route('story')" class="mt-6 justify-center" />
        </div>
    </section>
</x-layouts.site>
