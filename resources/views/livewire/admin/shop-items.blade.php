<div>
    @if ($editing === null)
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h1 class="text-3xl font-semibold">Buy from the family</h1>
            <button type="button" wire:click="create" class="btn-primary"><x-icon name="plus" class="size-5" /> Add something</button>
        </div>
        <p class="mt-2 text-lg text-muted">Things you sell to help pay for David's care, like sweets or dried mangoes. They show on the "How to Help" page.</p>

        @if ($items->isEmpty())
            <p class="card mt-6 text-lg text-muted">Nothing here yet.</p>
        @else
            <ul class="mt-6 space-y-3" role="list">
                @foreach ($items as $item)
                    <li wire:key="shop-{{ $item->id }}" class="card flex flex-wrap items-center gap-3 p-4">
                        <div class="flex flex-col gap-1">
                            <button type="button" wire:click="move({{ $item->id }}, 'up')" @disabled($loop->first) class="inline-flex size-10 items-center justify-center rounded-full border border-line disabled:opacity-30">
                                <x-icon name="arrow-left" class="size-5 rotate-90" /><span class="sr-only">Move up</span>
                            </button>
                            <button type="button" wire:click="move({{ $item->id }}, 'down')" @disabled($loop->last) class="inline-flex size-10 items-center justify-center rounded-full border border-line disabled:opacity-30">
                                <x-icon name="arrow-left" class="size-5 -rotate-90" /><span class="sr-only">Move down</span>
                            </button>
                        </div>
                        @if ($item->photo)
                            <img src="{{ \App\Support\ImageStore::url($item->photo, 'sm') }}" alt="" class="size-16 rounded-xl object-cover">
                        @endif
                        <p class="min-w-0 flex-1 text-lg font-semibold">{{ $item->title }}</p>
                        <div class="flex gap-2">
                            <button type="button" wire:click="edit({{ $item->id }})" class="btn-quiet min-h-11 px-4 py-2 text-base"><x-icon name="pencil" class="size-5" /> Edit</button>
                            <button type="button" wire:click="delete({{ $item->id }})" wire:confirm="Remove this?"
                                    class="btn-quiet min-h-11 px-4 py-2 text-base text-[#9a3412]"><x-icon name="trash" class="size-5" /><span class="sr-only">Delete</span></button>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    @else
        <h1 class="text-3xl font-semibold">{{ $editing ? 'Edit' : 'Add something to sell' }}</h1>
        <form wire:submit="save" class="card mt-6 space-y-6">
            <div>
                <label for="title" class="field-label">Name</label>
                <input id="title" type="text" wire:model="title" maxlength="120" class="field" placeholder="For example: Sweet Treats for a Cause">
                @error('title') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="description" class="field-label">Short description (what, prices, how to order)</label>
                <textarea id="description" wire:model="description" rows="5" maxlength="600" class="field"></textarea>
                <p class="mt-1.5 text-base text-muted">Please do not write your home address. Ask people to message you instead.</p>
                @error('description') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <p class="field-label">Photo or price list (optional)</p>
                @if ($upload && method_exists($upload, 'isPreviewable') && $upload->isPreviewable())
                    <img src="{{ $upload->temporaryUrl() }}" alt="" class="mb-3 max-h-60 rounded-xl border border-line">
                @elseif ($photo)
                    <img src="{{ \App\Support\ImageStore::url($photo, 'sm') }}" alt="" class="mb-3 max-h-60 rounded-xl border border-line">
                @endif
                <x-admin.upload model="upload" :label="$photo ? 'Choose a different photo' : 'Choose a photo'" />
                @error('upload') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <div class="flex flex-col gap-3 sm:flex-row">
                <button type="submit" class="btn-primary" wire:loading.attr="disabled" wire:target="save,upload"><x-icon name="check" class="size-5" /> Save</button>
                <button type="button" wire:click="cancel" class="btn-quiet">Cancel</button>
            </div>
        </form>
    @endif
</div>
