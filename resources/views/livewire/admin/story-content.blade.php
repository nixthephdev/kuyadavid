@use('App\Support\ImageStore')
<div class="space-y-10">
    <div>
        <h1 class="text-3xl font-semibold">Story, photos and headline</h1>
        <p class="mt-1 text-lg text-muted">Change the main words and photos on the website. Each part has its own Save button.</p>
    </div>

    {{-- Main photo --}}
    <section class="card space-y-4" aria-labelledby="hero-photo">
        <h2 id="hero-photo" class="text-2xl font-semibold">Main photo (top of the home page)</h2>
        @if ($hero)
            <img src="{{ ImageStore::url($hero, 'sm') }}" alt="" class="max-h-72 rounded-xl">
        @endif
        <x-admin.upload model="heroUpload" :label="$hero ? 'Choose a different photo' : 'Choose a photo'" />
        @error('heroUpload') <p class="field-error">{{ $message }}</p> @enderror
    </section>

    {{-- Words --}}
    <form wire:submit="saveText" class="card space-y-5" aria-labelledby="words">
        <h2 id="words" class="text-2xl font-semibold">Words</h2>
        <div>
            <label for="hero_headline" class="field-label">Headline</label>
            <input id="hero_headline" type="text" wire:model="text.hero_headline" maxlength="140" class="field">
            @error('text.hero_headline') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="hero_subline" class="field-label">One line under the headline</label>
            <textarea id="hero_subline" wire:model="text.hero_subline" rows="2" maxlength="240" class="field"></textarea>
            @error('text.hero_subline') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="hero_alt" class="field-label">Describe the main photo (for blind visitors)</label>
            <input id="hero_alt" type="text" wire:model="text.hero_alt" maxlength="200" class="field" placeholder="For example: David and his wife smiling at home">
            @error('text.hero_alt') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="story_en" class="field-label">His story (English)</label>
            <textarea id="story_en" wire:model="text.story_en" rows="16" class="field"></textarea>
            <p class="mt-1.5 text-base text-muted">Leave an empty line between paragraphs.</p>
            @error('text.story_en') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="share_message" class="field-label">Ready-to-post message (for people who share the page)</label>
            <textarea id="share_message" wire:model="text.share_message" rows="4" maxlength="1000" class="field"></textarea>
            @error('text.share_message') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <details class="rounded-xl border border-line p-4" @if ($text['story_fil'] || $text['hero_headline_fil']) open @endif>
            <summary class="cursor-pointer text-lg font-semibold">Filipino version</summary>
            <div class="mt-4 space-y-5">
                <div>
                    <label for="hero_headline_fil" class="field-label">Headline (Filipino)</label>
                    <input id="hero_headline_fil" type="text" wire:model="text.hero_headline_fil" maxlength="140" class="field">
                </div>
                <div>
                    <label for="hero_subline_fil" class="field-label">Line under the headline (Filipino)</label>
                    <textarea id="hero_subline_fil" wire:model="text.hero_subline_fil" rows="2" maxlength="240" class="field"></textarea>
                </div>
                <div>
                    <label for="story_fil" class="field-label">Short story (Filipino)</label>
                    <textarea id="story_fil" wire:model="text.story_fil" rows="10" class="field"></textarea>
                </div>
                <div>
                    <label for="share_message_fil" class="field-label">Ready-to-post message (Filipino)</label>
                    <textarea id="share_message_fil" wire:model="text.share_message_fil" rows="4" maxlength="1000" class="field"></textarea>
                </div>
            </div>
        </details>

        <button type="submit" class="btn-primary w-full sm:w-auto"><x-icon name="check" class="size-5" /> Save words</button>
    </form>

    {{-- Story photos --}}
    <section class="card space-y-4" aria-labelledby="story-photos">
        <h2 id="story-photos" class="text-2xl font-semibold">Photos in the story</h2>
        @if ($photos)
            <ul class="space-y-4" role="list">
                @foreach ($photos as $i => $photo)
                    <li wire:key="sp-{{ $photo['src'] }}" class="flex flex-col gap-3 rounded-xl border border-line p-3 sm:flex-row">
                        <img src="{{ ImageStore::url($photo['src'], 'sm') }}" alt="" class="aspect-square w-full rounded-lg object-cover sm:w-32">
                        <div class="flex-1 space-y-2">
                            <label class="block text-base font-semibold" for="alt-{{ $i }}">Describe the photo</label>
                            <input id="alt-{{ $i }}" type="text" wire:model="photos.{{ $i }}.alt" maxlength="200" class="field" placeholder="For example: David in the hospital with his wife">
                            <label class="block text-base font-semibold" for="cap-{{ $i }}">Caption under the photo (optional)</label>
                            <input id="cap-{{ $i }}" type="text" wire:model="photos.{{ $i }}.caption" maxlength="200" class="field">
                            <button type="button" wire:click="removeStoryPhoto({{ $i }})" wire:confirm="Remove this photo?" class="btn-quiet min-h-11 px-4 py-2 text-base text-[#9a3412]"><x-icon name="trash" class="size-5" /> Remove</button>
                        </div>
                    </li>
                @endforeach
            </ul>
            <button type="button" wire:click="savePhotos" class="btn-primary"><x-icon name="check" class="size-5" /> Save descriptions</button>
        @endif
        <x-admin.upload model="photoUploads" multiple label="Add photos" />
        @error('photoUploads.*') <p class="field-error">{{ $message }}</p> @enderror
    </section>

    {{-- Timeline --}}
    <section class="card space-y-4" aria-labelledby="timeline">
        <h2 id="timeline" class="text-2xl font-semibold">Timeline</h2>
        <p class="text-muted">From diagnosis, through treatment, to today.</p>
        <ul class="space-y-2" role="list">
            @foreach ($timeline as $row)
                <li wire:key="tl-{{ $row->id }}" class="flex items-center gap-3 rounded-xl border border-line p-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-base font-semibold text-gold-700">{{ $row->when_label }}</p>
                        <p class="text-lg font-semibold">{{ $row->title }}</p>
                    </div>
                    <button type="button" wire:click="editEvent({{ $row->id }})" class="btn-quiet min-h-11 px-4 py-2 text-base"><x-icon name="pencil" class="size-5" /><span class="sr-only">Edit</span></button>
                    <button type="button" wire:click="deleteEvent({{ $row->id }})" wire:confirm="Remove this from the timeline?" class="btn-quiet min-h-11 px-4 py-2 text-base text-[#9a3412]"><x-icon name="trash" class="size-5" /><span class="sr-only">Delete</span></button>
                </li>
            @endforeach
        </ul>

        @if ($eventId !== null)
            <form wire:submit="saveEvent" class="space-y-4 rounded-xl bg-cream p-4">
                <div>
                    <label for="ev-when" class="field-label">When</label>
                    <input id="ev-when" type="text" wire:model="event.when_label" maxlength="60" class="field" placeholder="For example: March 2024">
                    @error('event.when_label') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="ev-title" class="field-label">What happened</label>
                    <input id="ev-title" type="text" wire:model="event.title" maxlength="200" class="field">
                    @error('event.title') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="ev-body" class="field-label">More details (optional)</label>
                    <textarea id="ev-body" wire:model="event.body" rows="3" maxlength="1000" class="field"></textarea>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="btn-primary"><x-icon name="check" class="size-5" /> Save</button>
                    <button type="button" wire:click="$set('eventId', null)" class="btn-quiet">Cancel</button>
                </div>
            </form>
        @else
            <button type="button" wire:click="editEvent(0)" class="btn-secondary"><x-icon name="plus" class="size-5" /> Add to the timeline</button>
        @endif
    </section>

    {{-- Medical proof --}}
    <section class="card space-y-4" aria-labelledby="proof">
        <h2 id="proof" class="text-2xl font-semibold">Medical proof (optional)</h2>
        <p class="text-muted">A medical certificate or abstract helps strangers trust the story. It only shows on the website if you add one.</p>
        <p class="rounded-xl bg-gold-50 p-4 text-base font-semibold text-gold-700">
            Before uploading, cover David's address, ID numbers, hospital record numbers and any children's names. The website shows the note "Personal details hidden for privacy."
        </p>
        @if ($proof)
            <ul class="grid grid-cols-2 gap-3" role="list">
                @foreach ($proof as $i => $doc)
                    <li wire:key="pr-{{ $doc }}" class="relative">
                        <img src="{{ ImageStore::url($doc, 'sm') }}" alt="" class="w-full rounded-xl border border-line">
                        <button type="button" wire:click="removeProof({{ $i }})" wire:confirm="Remove this document?" class="absolute top-1 right-1 inline-flex size-10 items-center justify-center rounded-full bg-white/95 text-[#9a3412] shadow">
                            <x-icon name="trash" class="size-5" /><span class="sr-only">Remove document</span>
                        </button>
                    </li>
                @endforeach
            </ul>
        @endif
        <x-admin.upload model="proofUploads" multiple label="Add a document photo" />
        @error('proofUploads.*') <p class="field-error">{{ $message }}</p> @enderror
    </section>
</div>
