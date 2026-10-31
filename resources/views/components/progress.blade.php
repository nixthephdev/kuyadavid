@props(['fund'])
@if ($fund->hasGoal())
    <section {{ $attributes->merge(['class' => 'card']) }} aria-labelledby="progress-title">
        <h2 id="progress-title" class="eyebrow font-sans">{{ __('site.home.progress_title') }}</h2>
        <p class="mt-3 flex flex-wrap items-baseline gap-x-2">
            <span class="font-serif text-4xl font-semibold text-sage-900 sm:text-5xl">{{ money($fund->raised) }}</span>
            <span class="text-lg text-muted">{{ __('site.home.raised') }} {{ __('site.home.of_goal', ['goal' => money($fund->goal)]) }}</span>
        </p>
        <div class="mt-5 h-5 overflow-hidden rounded-full bg-sage-100"
             role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $fund->percent() }}"
             aria-label="{{ __('site.home.percent', ['percent' => $fund->percent()]) }}">
            {{-- A small sliver is always visible so the bar never looks empty. --}}
            <div class="h-full rounded-full bg-gradient-to-r from-sage-500 to-sage-700" style="width: {{ max(2, $fund->percent()) }}%"></div>
        </div>
        <div class="mt-3 flex flex-wrap justify-between gap-2 text-base text-muted">
            <span class="font-semibold text-sage-900">{{ __('site.home.percent', ['percent' => $fund->percent()]) }}</span>
            @if ($fund->updatedAt)
                <span>{{ __('site.home.last_updated', ['date' => $fund->updatedAt->translatedFormat('F j, Y')]) }}</span>
            @endif
        </div>
    </section>
@endif
