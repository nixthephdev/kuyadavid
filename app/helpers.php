<?php

use App\Models\Setting;
use App\Support\Fundraising;

if (! function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('money')) {
    function money(float|string|null $amount): string
    {
        return Fundraising::money($amount);
    }
}

if (! function_exists('paragraphs')) {
    /** Split plain text typed in the admin into paragraphs (blank line = new paragraph). */
    function paragraphs(?string $text): array
    {
        $parts = preg_split('/\R\s*\R/', trim((string) $text)) ?: [];

        return array_values(array_filter(array_map('trim', $parts), 'strlen'));
    }
}

if (! function_exists('localized')) {
    /** A setting in the visitor's language: "{key}_fil" when Filipino is chosen and filled in. */
    function localized(string $key, mixed $default = null): mixed
    {
        if (app()->getLocale() === 'fil' && filled($fil = Setting::get($key.'_fil'))) {
            return $fil;
        }

        return Setting::get($key, $default);
    }
}
