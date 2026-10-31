<div>
    @if ($editing === null)
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h1 class="text-3xl font-semibold">Updates</h1>
            <button type="button" wire:click="create" class="btn-primary"><x-icon name="plus" class="size-5" /> Write a new update</button>
        </div>

        @if ($posts->isEmpty())
            <p class="card mt-6 text-lg text-muted">No updates yet. Press "Write a new update" to share news with everyone.</p>
        @else
            <ul class="mt-6 space-y-3" role="list">
                @foreach ($posts as $post)
                    <li wire:key="post-{{ $post->id }}" class="card flex flex-wrap items-center gap-4 p-4">
                        @if ($post->photos)
                            <img src="{{ \App\Support\ImageStore::url($post->photos[0], 'sm') }}" alt="" class="size-16 rounded-xl object-cover">
                        @endif
                        <div class="min-w-0 flex-1">
                            <p class="text-lg font-semibold">{{ $post->title }}</p>
                            <p class="text-base text-muted">
                                {{ $post->published_at->format('F j, Y') }}
                                @if ($post->published_at->isFuture()) · <span class="font-semibold text-gold-700">Scheduled</span> @endif
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" wire:click="edit({{ $post->id }})" class="btn-quiet min-h-11 px-4 py-2 text-base"><x-icon name="pencil" class="size-5" /> Edit</button>
                            <button type="button" wire:click="delete({{ $post->id }})" wire:confirm="Delete this update? This cannot be undone."
                                    class="btn-quiet min-h-11 px-4 py-2 text-base text-[#9a3412]"><x-icon name="trash" class="size-5" /> Delete</button>
                        </div>
                    </li>
                @endforeach
            </ul>
            <div class="mt-6">{{ $posts->links() }}</div>
        @endif
    @else
        <h1 class="text-3xl font-semibold">{{ $editing ? 'Edit update' : 'New update' }}</h1>

        <form wire:submit="save" class="card mt-6 space-y-6">
            <div>
                <label for="title" class="field-label">Title</label>
                <input id="title" type="text" wire:model="title" maxlength="200" class="field" placeholder="For example: A good week at the hospital">
                @error('title') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="published_at" class="field-label">Date</label>
                <input id="published_at" type="date" wire:model="published_at" class="field max-w-60">
                @error('published_at') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="body" class="field-label">What would you like to share?</label>
                <textarea id="body" wire:model="body" rows="12" class="field" placeholder="Leave an empty line between paragraphs."></textarea>
                <p class="mt-1.5 text-base text-muted">Leave an empty line to start a new paragraph. Please do not include addresses, ID numbers or hospital record numbers.</p>
                @error('body') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <p class="field-label">Photos</p>
                @if ($photos || $uploads)
                    <ul class="mb-4 grid grid-cols-3 gap-3" role="list">
                        @foreach ($photos as $i => $base)
                            <li wire:key="saved-{{ $base }}" class="relative">
                                <img src="{{ \App\Support\ImageStore::url($base, 'sm') }}" alt="" class="aspect-square w-full rounded-xl object-cover">
                                <button type="button" wire:click="removePhoto({{ $i }})" class="absolute top-1 right-1 inline-flex size-10 items-center justify-center rounded-full bg-white/95 text-[#9a3412] shadow">
                                    <x-icon name="trash" class="size-5" /><span class="sr-only">Remove photo</span>
                                </button>
                            </li>
                        @endforeach
                        @foreach ($uploads as $i => $upload)
                            <li wire:key="upload-{{ $i }}" class="relative">
                                @if (method_exists($upload, 'isPreviewable') && $upload->isPreviewable())
                                    <img src="{{ $upload->temporaryUrl() }}" alt="" class="aspect-square w-full rounded-xl object-cover">
                                @else
                                    <div class="flex aspect-square items-center justify-center rounded-xl bg-cream text-muted"><x-icon name="image" class="size-8" /></div>
                                @endif
                                <button type="button" wire:click="removeUpload({{ $i }})" class="absolute top-1 right-1 inline-flex size-10 items-center justify-center rounded-full bg-white/95 text-[#9a3412] shadow">
                                    <x-icon name="trash" class="size-5" /><span class="sr-only">Remove photo</span>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                @endif
                <x-admin.upload model="picked" multiple label="Add photos" />
                @error('picked.*') <p class="field-error">{{ $message }}</p> @enderror
                @error('uploads') <p class="field-error">{{ $message }}</p> @enderror
                @error('uploads.*') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                <button type="submit" class="btn-primary" wire:loading.attr="disabled" wire:target="save,uploads">
                    <x-icon name="check" class="size-5" />
                    <span wire:loading.remove wire:target="save">{{ $editing ? 'Save changes' : 'Publish update' }}</span>
                    <span wire:loading wire:target="save">Saving…</span>
                </button>
                <button type="button" wire:click="cancel" class="btn-quiet">Cancel</button>
            </div>
        </form>
    @endif
</div>
