<?php

namespace App\Livewire\Concerns;

use Illuminate\Support\Facades\RateLimiter;

/**
 * Honeypot + per-IP rate limit for public forms.
 *
 * The form renders a hidden "website" field that people never see. Bots
 * fill it in; when they do we quietly pretend the message was sent.
 */
trait GuardsAgainstSpam
{
    public string $website = '';

    protected function isBot(): bool
    {
        return filled($this->website);
    }

    /** Count this submission; true when the visitor is over the limit. */
    protected function tooManyAttempts(string $form): bool
    {
        $key = $form.':'.request()->ip();
        $limit = config('site.form_limit');

        if (RateLimiter::tooManyAttempts($key, $limit['attempts'])) {
            return true;
        }

        RateLimiter::hit($key, $limit['decay_seconds']);

        return false;
    }

    /** We never store raw IPs; a salted hash is enough to spot repeat spam. */
    protected function ipHash(): string
    {
        return hash('sha256', request()->ip().'|'.config('app.key'));
    }
}
