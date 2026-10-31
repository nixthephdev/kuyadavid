<?php

namespace App\Livewire;

use App\Livewire\Concerns\GuardsAgainstSpam;
use App\Models\Prayer;
use Livewire\Component;
use Livewire\WithPagination;

class PrayerWall extends Component
{
    use GuardsAgainstSpam, WithPagination;

    public string $name = '';

    public string $country = '';

    public string $message = '';

    public bool $sent = false;

    protected function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:60'],
            'country' => ['required', 'string', 'max:60'],
            'message' => ['required', 'string', 'min:3', 'max:600'],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'name' => __('site.prayers.name'),
            'country' => __('site.prayers.country'),
            'message' => __('site.prayers.message'),
        ];
    }

    public function submit(): void
    {
        if ($this->isBot()) {
            $this->reset('name', 'country', 'message', 'website');
            $this->sent = true;

            return;
        }

        $data = $this->validate();

        if ($this->tooManyAttempts('prayer')) {
            $this->addError('message', __('site.form.too_many'));

            return;
        }

        // Saved as pending: nothing is public until the family approves it.
        Prayer::create([
            'name' => trim($data['name'] ?? '') ?: null,
            'country' => trim($data['country']),
            'message' => trim($data['message']),
            'ip_hash' => $this->ipHash(),
        ]);

        $this->reset('name', 'country', 'message');
        $this->sent = true;
    }

    public function render()
    {
        return view('livewire.prayer-wall', [
            'prayers' => Prayer::approved()->paginate(12),
        ]);
    }
}
