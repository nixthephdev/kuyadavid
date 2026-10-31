<x-layouts.site :title="__('site.transparency.title')" :description="__('site.transparency.intro')">
    <header class="container-page pt-10 pb-8 md:pt-16">
        <h1 class="text-4xl font-semibold sm:text-5xl">{{ __('site.transparency.title') }}</h1>
        <p class="mt-4 max-w-2xl text-xl text-muted">{{ __('site.transparency.intro') }}</p>
    </header>

    <div class="container-page">
        <dl class="grid gap-4 sm:grid-cols-2 lg:max-w-3xl">
            <div class="card">
                <dt class="eyebrow">{{ __('site.transparency.total_raised') }}</dt>
                <dd class="mt-2 font-serif text-4xl font-semibold text-sage-900">{{ money($fund->raised) }}</dd>
                @if ($fund->updatedAt)
                    <dd class="mt-1 text-base text-muted">{{ __('site.home.last_updated', ['date' => $fund->updatedAt->translatedFormat('F j, Y')]) }}</dd>
                @endif
            </div>
            <div class="card">
                <dt class="eyebrow">{{ __('site.transparency.total_spent') }}</dt>
                <dd class="mt-2 font-serif text-4xl font-semibold text-sage-900">{{ money($totalSpent) }}</dd>
            </div>
        </dl>

        @if ($expenses->isEmpty())
            <p class="card mt-8 text-lg text-muted">{{ __('site.transparency.empty') }}</p>
        @else
            <ul class="mt-8 space-y-4 lg:max-w-3xl" role="list">
                @foreach ($expenses as $expense)
                    <li class="card flex flex-wrap items-start gap-x-6 gap-y-3">
                        <div class="min-w-0 flex-1">
                            <p class="text-base text-muted">
                                <span class="sr-only">{{ __('site.transparency.date') }}:</span>
                                <time datetime="{{ $expense->spent_on->toDateString() }}">{{ $expense->spent_on->translatedFormat('F j, Y') }}</time>
                            </p>
                            <p class="mt-1 text-xl font-semibold"><span class="sr-only">{{ __('site.transparency.item') }}:</span> {{ $expense->item }}</p>
                            @if ($expense->note)
                                <p class="mt-1 text-muted">{{ $expense->note }}</p>
                            @endif
                            @if ($expense->receipt)
                                <a href="{{ \App\Support\ImageStore::url($expense->receipt) }}" target="_blank" rel="noopener" class="link mt-3 inline-flex items-center gap-2 text-base">
                                    <x-icon name="receipt" class="size-5" /> {{ __('site.transparency.view_receipt') }}
                                </a>
                            @endif
                        </div>
                        <p class="font-serif text-2xl font-semibold text-sage-900"><span class="sr-only">{{ __('site.transparency.amount') }}:</span> {{ money($expense->amount) }}</p>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</x-layouts.site>
