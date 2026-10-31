@php
    $text = function (string $key, string $label, string $placeholder = '', string $type = 'text') {
        return compact('key', 'label', 'placeholder', 'type');
    };
    $sections = [
        ['GCash', 'wallet', [
            $text('gcash_name', 'Name on the GCash account'),
            $text('gcash_number', 'GCash number', '0917 123 4567', 'tel'),
        ], 'gcash_qr'],
        ['Bank 1', 'bank', [
            $text('bank_name', 'Bank name', 'For example: UnionBank'),
            $text('bank_account_name', 'Account name'),
            $text('bank_account_number', 'Account number'),
        ], 'bank_qr'],
        ['Bank 2 (optional)', 'bank', [
            $text('bank2_name', 'Bank name', 'For example: MariBank'),
            $text('bank2_account_name', 'Account name'),
            $text('bank2_account_number', 'Account number (leave empty to show only the QR code)'),
        ], 'bank2_qr'],
        ['GoFundMe (for friends abroad)', 'globe', [
            $text('gofundme_url', 'GoFundMe link', 'https://www.gofundme.com/f/…', 'url'),
            $text('gofundme_organizer', 'Name of the person who runs it'),
        ], null],
        ['PayPal (optional)', 'globe', [
            $text('paypal_email', 'PayPal email', '', 'email'),
            $text('paypal_link', 'PayPal.me link', 'https://paypal.me/…', 'url'),
        ], null],
        ['Facebook page', 'facebook', [
            $text('facebook_url', 'Link to the family\'s Facebook page', 'https://facebook.com/…', 'url'),
        ], null],
    ];
@endphp
<div>
    <h1 class="text-3xl font-semibold">Donation details</h1>
    <p class="mt-1 text-lg text-muted">Please check every number twice. Visitors will send money to exactly what you type here. Empty sections are hidden on the website.</p>

    <form wire:submit="save" class="mt-6 space-y-6">
        @foreach ($sections as [$heading, $icon, $fields, $qrKey])
            <fieldset class="card space-y-5">
                <legend class="float-left mb-2 flex items-center gap-2 text-2xl font-semibold"><x-icon :name="$icon" class="size-6 text-sage-700" /> {{ $heading }}</legend>
                @foreach ($fields as $f)
                    <div @class(['clear-left' => $loop->first])>
                        <label for="{{ $f['key'] }}" class="field-label">{{ $f['label'] }}</label>
                        <input id="{{ $f['key'] }}" type="{{ $f['type'] }}" wire:model="form.{{ $f['key'] }}" class="field" placeholder="{{ $f['placeholder'] }}">
                        @error('form.'.$f['key']) <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                @endforeach
                @if ($f['key'] === 'bank_account_number')
                    <div>
                        <label for="bank_extra" class="field-label">Other details (optional)</label>
                        <textarea id="bank_extra" wire:model="form.bank_extra" rows="2" class="field" placeholder="For example: SWIFT code for international transfers"></textarea>
                    </div>
                @endif
                @if ($qrKey)
                    <x-admin.qr-field :key="$qrKey" :saved="$qr[$qrKey] ?? null" :upload="$qrUploads[$qrKey] ?? null" />
                @endif
            </fieldset>
        @endforeach

        <button type="submit" class="btn-primary w-full sm:w-auto" wire:loading.attr="disabled" wire:target="save,qrUploads">
            <x-icon name="check" class="size-5" /> Save donation details
        </button>
    </form>
</div>
