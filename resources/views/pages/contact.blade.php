<x-layouts.site :title="__('site.contact.title')" :description="__('site.contact.intro')" livewire>
    <div class="container-page grid gap-10 pt-10 md:pt-16 lg:grid-cols-[1fr_minmax(0,40rem)]">
        <header>
            <h1 class="text-4xl font-semibold sm:text-5xl">{{ __('site.contact.title') }}</h1>
            <p class="mt-4 max-w-xl text-xl text-muted">{{ __('site.contact.intro') }}</p>
            @if ($fb = setting('facebook_url'))
                <a href="{{ $fb }}" target="_blank" rel="noopener" class="btn-secondary mt-8">
                    <x-icon name="facebook" class="size-5" /> {{ __('site.contact.facebook') }}
                </a>
            @endif
        </header>

        <div class="card">
            <livewire:contact-form :topic="$topic" />
        </div>
    </div>
</x-layouts.site>
