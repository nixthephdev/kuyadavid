<div>
    @if ($editing === null)
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h1 class="text-3xl font-semibold">What David needs</h1>
            <button type="button" wire:click="create" class="btn-primary"><x-icon name="plus" class="size-5" /> Add a card</button>
        </div>
        <p class="mt-2 text-lg text-muted">These cards show on the home page. Three to five cards work best. Use the arrows to change the order.</p>

        @if ($needs->isEmpty())
            <p class="card mt-6 text-lg text-muted">No cards yet.</p>
        @else
            <ul class="mt-6 space-y-3" role="list">
                @foreach ($needs as $need)
                    <li wire:key="need-{{ $need->id }}" class="card flex flex-wrap items-center gap-3 p-4">
                        <div class="flex flex-col gap-1">
                            <button type="button" wire:click="move({{ $need->id }}, 'up')" @disabled($loop->first) class="inline-flex size-10 items-center justify-center rounded-full border border-line disabled:opacity-30">
                                <x-icon name="arrow-left" class="size-5 rotate-90" /><span class="sr-only">Move up</span>
                            </button>
                            <button type="button" wire:click="move({{ $need->id }}, 'down')" @disabled($loop->last) class="inline-flex size-10 items-center justify-center rounded-full border border-line disabled:opacity-30">
                                <x-icon name="arrow-left" class="size-5 -rotate-90" /><span class="sr-only">Move down</span>
                            </button>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-lg font-semibold">{{ $need->title }}</p>
                            <p class="text-base text-muted">{{ $need->amount ? money($need->amount) : 'No amount shown' }}</p>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" wire:click="edit({{ $need->id }})" class="btn-quiet min-h-11 px-4 py-2 text-base"><x-icon name="pencil" class="size-5" /> Edit</button>
                            <button type="button" wire:click="delete({{ $need->id }})" wire:confirm="Delete this card?"
                                    class="btn-quiet min-h-11 px-4 py-2 text-base text-[#9a3412]"><x-icon name="trash" class="size-5" /><span class="sr-only">Delete</span></button>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    @else
        <h1 class="text-3xl font-semibold">{{ $editing ? 'Edit card' : 'New card' }}</h1>
        <form wire:submit="save" class="card mt-6 space-y-6">
            <div>
                <label for="title" class="field-label">Title</label>
                <input id="title" type="text" wire:model="title" maxlength="120" class="field" placeholder="For example: Monthly medicine">
                @error('title') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="description" class="field-label">Short description (optional)</label>
                <textarea id="description" wire:model="description" rows="3" maxlength="400" class="field"></textarea>
                @error('description') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="amount" class="field-label">Amount (optional)</label>
                <input id="amount" type="text" inputmode="decimal" wire:model="amount" class="field" placeholder="Leave empty if not sure">
                @error('amount') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <div class="flex flex-col gap-3 sm:flex-row">
                <button type="submit" class="btn-primary"><x-icon name="check" class="size-5" /> Save</button>
                <button type="button" wire:click="cancel" class="btn-quiet">Cancel</button>
            </div>
        </form>
    @endif
</div>
