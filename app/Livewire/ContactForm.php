<?php

namespace App\Livewire;

use App\Livewire\Concerns\GuardsAgainstSpam;
use App\Mail\NewContactMessage;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Throwable;

class ContactForm extends Component
{
    use GuardsAgainstSpam;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $topic = 'general';

    public string $message = '';

    public bool $sent = false;

    public function mount(string $topic = 'general'): void
    {
        $this->topic = in_array($topic, ContactMessage::TOPICS, true) ? $topic : 'general';
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40', 'regex:/^[0-9+()\-\s.]*$/'],
            'topic' => ['required', Rule::in(ContactMessage::TOPICS)],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'name' => __('site.contact.name'),
            'email' => __('site.contact.email'),
            'phone' => __('site.contact.phone'),
            'topic' => __('site.contact.topic'),
            'message' => __('site.contact.message'),
        ];
    }

    public function submit(): void
    {
        if ($this->isBot()) {
            $this->reset('name', 'email', 'phone', 'message', 'website');
            $this->sent = true;

            return;
        }

        $data = $this->validate();

        if ($this->tooManyAttempts('contact')) {
            $this->addError('message', __('site.form.too_many'));

            return;
        }

        $saved = ContactMessage::create([
            ...array_map(fn ($v) => is_string($v) ? trim($v) : $v, $data),
            'phone' => trim($data['phone'] ?? '') ?: null,
            'ip_hash' => $this->ipHash(),
        ]);

        // The message is already saved in the admin, so a mail problem never loses it.
        if ($to = config('site.family_email')) {
            try {
                Mail::to($to)->send(new NewContactMessage($saved));
            } catch (Throwable $e) {
                Log::warning('Contact email could not be sent', ['id' => $saved->id, 'error' => $e->getMessage()]);
            }
        }

        $this->reset('name', 'email', 'phone', 'message');
        $this->sent = true;
    }

    public function render()
    {
        return view('livewire.contact-form');
    }
}
