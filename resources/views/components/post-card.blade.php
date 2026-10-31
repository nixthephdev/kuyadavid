@props(['post'])
@php($cover = $post->photos[0] ?? null)
<article {{ $attributes->merge(['class' => 'card relative flex h-full flex-col overflow-hidden p-0']) }}>
    @if ($cover)
        <x-photo :src="$cover" :alt="__('site.updates.photo_alt', ['title' => $post->title])"
                 sizes="(min-width: 768px) 33vw, 100vw" class="aspect-[3/2] w-full object-cover" />
    @endif
    <div class="flex flex-1 flex-col p-6">
        <p class="flex items-center gap-2 text-base text-muted">
            <x-icon name="calendar" class="size-4" />
            <time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->translatedFormat('F j, Y') }}</time>
        </p>
        <h3 class="mt-2 text-2xl font-semibold">
            <a href="{{ route('updates.show', $post) }}" class="no-underline after:absolute after:inset-0 hover:text-sage-800">{{ $post->title }}</a>
        </h3>
        <p class="mt-3 flex-1 text-muted">{{ $post->excerpt() }}</p>
        <span class="mt-4 inline-flex items-center gap-1 font-semibold text-sage-800">{{ __('site.cta.read_more') }} <x-icon name="arrow-right" class="size-4" /></span>
    </div>
</article>
