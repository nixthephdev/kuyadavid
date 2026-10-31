<x-layouts.site :title="__('site.updates.title')" :description="__('site.updates.intro')">
    <header class="container-page pt-10 pb-8 md:pt-16">
        <h1 class="text-4xl font-semibold sm:text-5xl">{{ __('site.updates.title') }}</h1>
        <p class="mt-4 max-w-2xl text-xl text-muted">{{ __('site.updates.intro') }}</p>
    </header>

    <div class="container-page">
        @if ($posts->isEmpty())
            <p class="card text-lg text-muted">{{ __('site.updates.empty') }}</p>
        @else
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($posts as $post)
                    <x-post-card :post="$post" />
                @endforeach
            </div>
            <div class="mt-10">{{ $posts->links() }}</div>
        @endif
    </div>
</x-layouts.site>
