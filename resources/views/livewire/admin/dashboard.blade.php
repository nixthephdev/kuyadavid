@php
    $tiles = [
        ['admin.fundraising', 'flag', 'Goal and amount raised', $fund->hasGoal() ? money($fund->raised).' of '.money($fund->goal) : 'No goal set yet', null],
        ['admin.posts', 'pencil', 'Write an update', 'Share news with photos', null],
        ['admin.prayers', 'candle', 'Prayer messages', $pendingPrayers ? "{$pendingPrayers} waiting for you" : 'All checked', $pendingPrayers],
        ['admin.messages', 'mail', 'Contact messages', $unreadMessages ? "{$unreadMessages} new" : 'No new messages', $unreadMessages],
        ['admin.expenses', 'receipt', 'Where the money went', 'Add what donations paid for', null],
        ['admin.needs', 'list', 'What David needs', 'Edit the needs cards', null],
        ['admin.donations', 'wallet', 'Donation details', 'GCash, banks, GoFundMe', null],
        ['admin.shop', 'package', 'Buy from the family', 'Sweets, mangoes and more', null],
        ['admin.story', 'book', 'Story, photos and headline', 'Edit the main text', null],
    ];
@endphp
<div>
    <h1 class="text-3xl font-semibold">Hello, {{ auth()->user()->name }}</h1>
    <p class="mt-1 text-lg text-muted">What would you like to do today?</p>

    <ul class="mt-6 grid gap-3 sm:grid-cols-2" role="list">
        @foreach ($tiles as [$route, $icon, $label, $hint, $badge])
            <li>
                <a href="{{ route($route) }}" class="card flex min-h-24 items-center gap-4 p-5 no-underline transition-colors hover:border-sage-500 hover:bg-sage-50">
                    <span class="inline-flex size-14 shrink-0 items-center justify-center rounded-2xl bg-sage-100 text-sage-800">
                        <x-icon :name="$icon" class="size-7" />
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block text-xl font-semibold text-ink">{{ $label }}</span>
                        <span class="block text-base text-muted">{{ $hint }}</span>
                    </span>
                    @if ($badge)
                        <span class="inline-flex min-w-9 items-center justify-center rounded-full bg-gold-700 px-2.5 py-1 text-base font-bold text-white">{{ $badge }}</span>
                    @endif
                </a>
            </li>
        @endforeach
    </ul>
</div>
