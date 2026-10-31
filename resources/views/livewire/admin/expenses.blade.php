<div>
    @if ($editing === null)
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h1 class="text-3xl font-semibold">Where the money went</h1>
            <button type="button" wire:click="create" class="btn-primary"><x-icon name="plus" class="size-5" /> Add an entry</button>
        </div>
        <p class="mt-2 text-lg text-muted">Each entry shows on the Transparency page. A receipt photo builds trust, but cover any personal details first.</p>

        <p class="card mt-6 flex items-center justify-between gap-4 p-5">
            <span class="text-lg font-semibold">Total spent</span>
            <span class="font-serif text-3xl font-semibold text-sage-900">{{ money($total) }}</span>
        </p>

        @if ($expenses->isEmpty())
            <p class="card mt-4 text-lg text-muted">No entries yet.</p>
        @else
            <ul class="mt-4 space-y-3" role="list">
                @foreach ($expenses as $expense)
                    <li wire:key="expense-{{ $expense->id }}" class="card flex flex-wrap items-center gap-4 p-4">
                        <div class="min-w-0 flex-1">
                            <p class="text-lg font-semibold">{{ $expense->item }}</p>
                            <p class="text-base text-muted">{{ $expense->spent_on->format('F j, Y') }} · {{ money($expense->amount) }}
                                @if ($expense->receipt) · <x-icon name="receipt" class="inline size-4" /> receipt @endif</p>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" wire:click="edit({{ $expense->id }})" class="btn-quiet min-h-11 px-4 py-2 text-base"><x-icon name="pencil" class="size-5" /> Edit</button>
                            <button type="button" wire:click="delete({{ $expense->id }})" wire:confirm="Delete this entry? This cannot be undone."
                                    class="btn-quiet min-h-11 px-4 py-2 text-base text-[#9a3412]"><x-icon name="trash" class="size-5" /> Delete</button>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    @else
        <h1 class="text-3xl font-semibold">{{ $editing ? 'Edit entry' : 'New entry' }}</h1>

        <form wire:submit="save" class="card mt-6 space-y-6">
            <div>
                <label for="item" class="field-label">What was it for?</label>
                <input id="item" type="text" wire:model="item" maxlength="200" class="field" placeholder="For example: Medicine for one month">
                @error('item') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="amount" class="field-label">Amount</label>
                    <input id="amount" type="text" inputmode="decimal" wire:model="amount" class="field" placeholder="For example 2500">
                    @error('amount') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="spent_on" class="field-label">Date</label>
                    <input id="spent_on" type="date" wire:model="spent_on" class="field">
                    @error('spent_on') <p class="field-error">{{ $message }}</p> @enderror
                </div>
            </div>
            <div>
                <label for="note" class="field-label">Short note (optional)</label>
                <input id="note" type="text" wire:model="note" maxlength="255" class="field" placeholder="For example: Paid at the hospital pharmacy">
                @error('note') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <p class="field-label">Receipt photo (optional)</p>
                @if ($upload && method_exists($upload, 'isPreviewable') && $upload->isPreviewable())
                    <img src="{{ $upload->temporaryUrl() }}" alt="" class="mb-3 max-h-60 rounded-xl border border-line">
                @elseif ($receipt && ! $removeReceipt)
                    <img src="{{ \App\Support\ImageStore::url($receipt, 'sm') }}" alt="" class="mb-3 max-h-60 rounded-xl border border-line">
                    <button type="button" wire:click="$set('removeReceipt', true)" class="btn-quiet mb-3 min-h-11 px-4 py-2 text-base text-[#9a3412]"><x-icon name="trash" class="size-5" /> Remove receipt</button>
                @endif
                <x-admin.upload model="upload" :label="$receipt ? 'Choose a different photo' : 'Choose a photo'" />
                <p class="mt-1 text-base font-semibold text-gold-700">Please cover names, addresses and ID or record numbers before taking the photo.</p>
                @error('upload') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                <button type="submit" class="btn-primary" wire:loading.attr="disabled" wire:target="save,upload"><x-icon name="check" class="size-5" /> Save</button>
                <button type="button" wire:click="cancel" class="btn-quiet">Cancel</button>
            </div>
        </form>
    @endif
</div>
