<div>
    @if ($sent)
        <div class="rounded-xl bg-sage-50 p-5 text-lg text-sage-900" role="status">
            <x-icon name="check" class="mb-2 size-7 text-sage-700" />
            <p>{{ __('site.contact.thanks') }}</p>
        </div>
    @else
        <form wire:submit="submit" class="space-y-5" novalidate>
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="cf-name-{{ $this->getId() }}" class="field-label">{{ __('site.contact.name') }}</label>
                    <input id="cf-name-{{ $this->getId() }}" type="text" wire:model="name" maxlength="100" autocomplete="name" required class="field">
                    @error('name') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="cf-email-{{ $this->getId() }}" class="field-label">{{ __('site.contact.email') }}</label>
                    <input id="cf-email-{{ $this->getId() }}" type="email" wire:model="email" maxlength="255" autocomplete="email" required class="field">
                    @error('email') <p class="field-error">{{ $message }}</p> @enderror
                </div>
            </div>
            <div>
                <label for="cf-phone-{{ $this->getId() }}" class="field-label">{{ __('site.contact.phone') }}</label>
                <input id="cf-phone-{{ $this->getId() }}" type="tel" wire:model="phone" maxlength="40" autocomplete="tel" class="field">
                @error('phone') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="cf-topic-{{ $this->getId() }}" class="field-label">{{ __('site.contact.topic') }}</label>
                <select id="cf-topic-{{ $this->getId() }}" wire:model="topic" class="field">
                    @foreach (\App\Models\ContactMessage::TOPICS as $value)
                        <option value="{{ $value }}">{{ __('site.contact.topics.'.$value) }}</option>
                    @endforeach
                </select>
                @error('topic') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="cf-message-{{ $this->getId() }}" class="field-label">{{ __('site.contact.message') }}</label>
                <textarea id="cf-message-{{ $this->getId() }}" wire:model="message" rows="6" maxlength="3000" required class="field"></textarea>
                @error('message') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            {{-- Honeypot: hidden from people, tempting to bots. --}}
            <div class="absolute -left-[9999px]" aria-hidden="true">
                <label for="cf-website-{{ $this->getId() }}">Website</label>
                <input id="cf-website-{{ $this->getId() }}" type="text" wire:model="website" tabindex="-1" autocomplete="off">
            </div>

            <button type="submit" class="btn-primary w-full sm:w-auto" wire:loading.attr="disabled">
                <x-icon name="mail" class="size-5" />
                <span wire:loading.remove wire:target="submit">{{ __('site.contact.submit') }}</span>
                <span wire:loading wire:target="submit">{{ __('site.form.sending') }}</span>
            </button>
        </form>
    @endif
</div>
