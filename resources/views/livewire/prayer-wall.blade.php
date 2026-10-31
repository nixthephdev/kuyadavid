<div class="grid gap-10 lg:grid-cols-[minmax(0,26rem)_1fr] lg:items-start">
    <div class="card lg:sticky lg:top-28">
        @if ($sent)
            <div class="rounded-xl bg-sage-50 p-5 text-lg text-sage-900" role="status">
                <x-icon name="check" class="mb-2 size-7 text-sage-700" />
                <p>{{ __('site.prayers.thanks') }}</p>
                <button type="button" class="link mt-3" wire:click="$set('sent', false)">{{ __('site.cta.leave_prayer') }}</button>
            </div>
        @else
            <form wire:submit="submit" class="space-y-5" novalidate>
                <div>
                    <label for="pw-name" class="field-label">{{ __('site.prayers.name') }}</label>
                    <input id="pw-name" type="text" wire:model="name" maxlength="60" autocomplete="name" class="field">
                    @error('name') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="pw-country" class="field-label">{{ __('site.prayers.country') }}</label>
                    <input id="pw-country" type="text" wire:model="country" maxlength="60" autocomplete="country-name" required class="field" @error('country') aria-invalid="true" aria-describedby="pw-country-error" @enderror>
                    @error('country') <p id="pw-country-error" class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="pw-message" class="field-label">{{ __('site.prayers.message') }}</label>
                    <textarea id="pw-message" wire:model="message" rows="5" maxlength="600" required class="field" @error('message') aria-invalid="true" aria-describedby="pw-message-error" @enderror></textarea>
                    @error('message') <p id="pw-message-error" class="field-error">{{ $message }}</p> @enderror
                </div>

                {{-- Honeypot: hidden from people, tempting to bots. --}}
                <div class="absolute -left-[9999px]" aria-hidden="true">
                    <label for="pw-website">Website</label>
                    <input id="pw-website" type="text" wire:model="website" tabindex="-1" autocomplete="off">
                </div>

                <button type="submit" class="btn-primary w-full" wire:loading.attr="disabled">
                    <x-icon name="candle" class="size-5" />
                    <span wire:loading.remove wire:target="submit">{{ __('site.prayers.submit') }}</span>
                    <span wire:loading wire:target="submit">{{ __('site.form.sending') }}</span>
                </button>
            </form>
        @endif
    </div>

    <div>
        @if ($prayers->isEmpty())
            <p class="card text-lg text-muted">{{ __('site.prayers.empty') }}</p>
        @else
            <ul class="grid gap-4 sm:grid-cols-2" role="list">
                @foreach ($prayers as $prayer)
                    <li wire:key="prayer-{{ $prayer->id }}"><x-prayer-card :prayer="$prayer" /></li>
                @endforeach
            </ul>
            <div class="mt-8">{{ $prayers->links(data: ['scrollTo' => false]) }}</div>
        @endif
    </div>
</div>
