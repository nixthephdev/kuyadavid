<x-layouts.site :title="__('site.prayers.title')" :description="__('site.prayers.intro')" livewire>
    <header class="container-page pt-10 pb-8 md:pt-16">
        <h1 class="text-4xl font-semibold sm:text-5xl">{{ __('site.prayers.title') }}</h1>
        <p class="mt-4 max-w-2xl text-xl text-muted">{{ __('site.prayers.intro') }}</p>
    </header>

    <div class="container-page">
        <livewire:prayer-wall />
    </div>
</x-layouts.site>
