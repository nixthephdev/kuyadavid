<div>
    <h1 class="text-3xl font-semibold">Goal and amount raised</h1>
    <p class="mt-1 text-lg text-muted">Update the amount raised whenever you check your accounts. The "last updated" date changes by itself.</p>

    <form wire:submit="save" class="card mt-6 space-y-6">
        <div>
            <label for="goal" class="field-label">Fundraising goal</label>
            <input id="goal" type="text" inputmode="decimal" wire:model="goal" class="field" placeholder="For example 500000">
            <p class="mt-1.5 text-base text-muted">Leave this empty to hide the progress bar.</p>
            @error('goal') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="raised" class="field-label">Amount raised so far</label>
            <input id="raised" type="text" inputmode="decimal" wire:model="raised" class="field" placeholder="For example 125000">
            @error('raised') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="currency" class="field-label">Money sign</label>
            <input id="currency" type="text" wire:model="currency" maxlength="5" class="field max-w-28">
            @error('currency') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <button type="submit" class="btn-primary w-full sm:w-auto" wire:loading.attr="disabled">
            <x-icon name="check" class="size-5" /> Save
        </button>
    </form>

    <h2 class="mt-10 font-sans text-xl font-semibold">How it looks on the website</h2>
    @if ($fund->hasGoal())
        <x-progress :fund="$fund" class="mt-3" />
    @else
        <p class="card mt-3 text-muted">No goal is set, so the progress bar is hidden.</p>
    @endif
</div>
