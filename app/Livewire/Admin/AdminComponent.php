<?php

namespace App\Livewire\Admin;

use Livewire\Component;

/** Shared bits for the admin screens. */
abstract class AdminComponent extends Component
{
    /** Photos: JPG, PNG or WEBP only, up to 5 MB. */
    public const PHOTO_RULES = ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'];

    /** Upload fields on this screen (e.g. "receipt", "photos.*"), for friendly error messages. */
    protected array $photoFields = [];

    protected function notify(string $message = 'Saved.'): void
    {
        $this->dispatch('notify', message: $message);
    }

    protected function messages(): array
    {
        $messages = ['*.required' => 'Please fill this in.'];

        foreach ($this->photoFields as $field) {
            $messages["{$field}.image"] = 'Please choose a photo (JPG, PNG or WEBP).';
            $messages["{$field}.mimes"] = 'Please choose a JPG, PNG or WEBP photo.';
            $messages["{$field}.max"] = 'This photo is too big. Photos can be up to 5 MB.';
            $messages["{$field}.uploaded"] = 'This photo could not be uploaded. Photos can be up to 5 MB.';
        }

        return $messages;
    }
}
