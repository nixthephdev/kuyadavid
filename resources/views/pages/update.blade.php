@php($photos = $post->photos ?? [])
<x-layouts.site :title="$post->title" :description="$post->excerpt(155)" type="article"
                :image="$photos ? \App\Support\ImageStore::url($photos[0]) : null">
    <article class="container-text pt-10 md:pt-16">
        <a href="{{ route('updates.index') }}" class="inline-flex items-center gap-2 text-base font-semibold text-sage-800 no-underline hover:underline">
            <x-icon name="arrow-left" class="size-4" /> {{ __('site.updates.back') }}
        </a>
        <header class="mt-6">
            <p class="flex items-center gap-2 text-base text-muted">
                <x-icon name="calendar" class="size-4" />
                <time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->translatedFormat('F j, Y') }}</time>
            </p>
            <h1 class="mt-2 text-4xl font-semibold sm:text-5xl">{{ $post->title }}</h1>
        </header>

        @if ($photos)
            <figure class="my-8 -mx-4 sm:mx-0">
                <x-photo :src="$photos[0]" :alt="__('site.updates.photo_alt', ['title' => $post->title])" eager
                         sizes="(min-width: 672px) 640px, 100vw" class="w-full sm:rounded-2xl" />
            </figure>
        @endif

        <div class="prose-story mt-8">
            @foreach (paragraphs($post->body) as $paragraph)
                <p>{!! nl2br(e($paragraph)) !!}</p>
            @endforeach
        </div>

        @if (count($photos) > 1)
            <div class="mt-8 grid grid-cols-2 gap-3">
                @foreach (array_slice($photos, 1) as $photo)
                    <a href="{{ \App\Support\ImageStore::url($photo) }}" target="_blank" rel="noopener">
                        <x-photo :src="$photo" :alt="__('site.updates.photo_alt', ['title' => $post->title])"
                                 sizes="(min-width: 672px) 320px, 50vw" class="aspect-square w-full rounded-2xl object-cover" />
                    </a>
                @endforeach
            </div>
        @endif

        <div class="mt-12 rounded-3xl bg-sage-50 p-6 sm:p-8">
            <p class="font-serif text-2xl text-sage-900">{{ __('site.help.thanks') }}</p>
            <div class="mt-5 flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('help') }}" class="btn-primary"><x-icon name="heart" class="size-5" /> {{ __('site.cta.help') }}</a>
            </div>
            <x-share-buttons :url="route('updates.show', $post)" :text="$post->title" class="mt-5" />
        </div>
    </article>
</x-layouts.site>
