<div>
    <h1 class="text-3xl font-semibold">Prayer messages</h1>
    <p class="mt-1 text-lg text-muted">New messages stay hidden until you approve them.</p>

    <div class="mt-6 grid grid-cols-2 gap-2 rounded-full border border-line bg-paper p-1" role="tablist">
        <button type="button" role="tab" wire:click="show('pending')" aria-selected="{{ $tab === 'pending' ? 'true' : 'false' }}"
                @class(['min-h-12 rounded-full text-lg font-semibold', 'bg-sage-700 text-white' => $tab === 'pending', 'text-muted' => $tab !== 'pending'])>
            Waiting ({{ $pendingCount }})
        </button>
        <button type="button" role="tab" wire:click="show('approved')" aria-selected="{{ $tab === 'approved' ? 'true' : 'false' }}"
                @class(['min-h-12 rounded-full text-lg font-semibold', 'bg-sage-700 text-white' => $tab === 'approved', 'text-muted' => $tab !== 'approved'])>
            On the wall
        </button>
    </div>

    @if ($prayers->isEmpty())
        <p class="card mt-6 text-lg text-muted">{{ $tab === 'pending' ? 'No new messages. You are all caught up.' : 'Nothing approved yet.' }}</p>
    @else
        <ul class="mt-6 space-y-4" role="list">
            @foreach ($prayers as $prayer)
                <li wire:key="prayer-{{ $prayer->id }}" class="card">
                    <p class="text-lg whitespace-pre-line">{{ $prayer->message }}</p>
                    <p class="mt-3 text-base text-muted">
                        <span class="font-semibold text-ink">{{ $prayer->name ?: 'No name' }}</span> · {{ $prayer->country }} · {{ $prayer->created_at->format('M j, Y g:i A') }}
                    </p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @if ($tab === 'pending')
                            <button type="button" wire:click="approve({{ $prayer->id }})" class="btn-primary flex-1 sm:flex-none"><x-icon name="check" class="size-5" /> Approve</button>
                        @else
                            <button type="button" wire:click="hide({{ $prayer->id }})" class="btn-quiet flex-1 sm:flex-none"><x-icon name="eye" class="size-5" /> Hide</button>
                        @endif
                        <button type="button" wire:click="delete({{ $prayer->id }})" wire:confirm="Delete this message? This cannot be undone."
                                class="btn-quiet flex-1 text-[#9a3412] sm:flex-none"><x-icon name="trash" class="size-5" /> Delete</button>
                    </div>
                </li>
            @endforeach
        </ul>
        <div class="mt-6">{{ $prayers->links() }}</div>
    @endif
</div>
