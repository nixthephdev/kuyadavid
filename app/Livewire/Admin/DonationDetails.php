<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use App\Support\ImageStore;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads;

#[Title('Donation details')]
class DonationDetails extends AdminComponent
{
    use WithFileUploads;

    public const FIELDS = [
        'gcash_name', 'gcash_number',
        'bank_name', 'bank_account_name', 'bank_account_number', 'bank_extra',
        'bank2_name', 'bank2_account_name', 'bank2_account_number',
        'gofundme_url', 'gofundme_organizer',
        'paypal_email', 'paypal_link',
        'facebook_url',
    ];

    /** One QR code per way to give. */
    public const QR_CODES = ['gcash_qr', 'bank_qr', 'bank2_qr'];

    public array $form = [];

    /** Saved QR codes (image base paths), keyed like QR_CODES. */
    public array $qr = [];

    public array $qrUploads = [];

    protected array $photoFields = ['qrUploads.*'];

    public function mount(): void
    {
        foreach (self::FIELDS as $field) {
            $this->form[$field] = (string) Setting::get($field, '');
        }
        foreach (self::QR_CODES as $key) {
            $this->qr[$key] = Setting::get($key);
        }
    }

    public function save(ImageStore $images): void
    {
        $account = ['nullable', 'string', 'max:40', 'regex:/^[0-9\-\s]*$/'];

        $this->validate([
            'form.gcash_name' => ['nullable', 'string', 'max:100'],
            'form.gcash_number' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s]*$/'],
            'form.bank_name' => ['nullable', 'string', 'max:100'],
            'form.bank_account_name' => ['nullable', 'string', 'max:100'],
            'form.bank_account_number' => $account,
            'form.bank_extra' => ['nullable', 'string', 'max:300'],
            'form.bank2_name' => ['nullable', 'string', 'max:100'],
            'form.bank2_account_name' => ['nullable', 'string', 'max:100'],
            'form.bank2_account_number' => $account,
            'form.gofundme_url' => ['nullable', 'url:https', 'max:255'],
            'form.gofundme_organizer' => ['nullable', 'string', 'max:100'],
            'form.paypal_email' => ['nullable', 'email', 'max:255'],
            'form.paypal_link' => ['nullable', 'url:https', 'max:255'],
            'form.facebook_url' => ['nullable', 'url:https', 'max:255'],
            'qrUploads.*' => ['nullable', ...self::PHOTO_RULES],
        ], [
            'form.gcash_number.regex' => 'Please type the number only, for example 0917 123 4567.',
            'form.bank_account_number.regex' => 'Please type numbers only.',
            'form.bank2_account_number.regex' => 'Please type numbers only.',
            'form.*.url' => 'Please paste the full link, starting with https://',
            'form.paypal_email.email' => 'Please check the email address.',
        ]);

        $values = array_map(fn ($v) => trim((string) $v) ?: null, $this->form);

        foreach ($this->qrUploads as $key => $upload) {
            if ($upload && in_array($key, self::QR_CODES, true)) {
                // QR codes must stay sharp enough to scan.
                $new = $images->store($upload, 'donations', minQuality: 70);
                $images->delete($this->qr[$key] ?? null);
                $this->qr[$key] = $new;
            }
        }
        $this->qrUploads = [];

        Setting::put([...$values, ...$this->qr]);
        $this->notify('Donation details saved.');
    }

    public function removeQr(string $key, ImageStore $images): void
    {
        if (! in_array($key, self::QR_CODES, true)) {
            return;
        }

        $images->delete($this->qr[$key] ?? null);
        $this->qr[$key] = null;
        Setting::put($key, null);
        $this->notify('QR code removed.');
    }

    public function render()
    {
        return view('livewire.admin.donation-details');
    }
}
