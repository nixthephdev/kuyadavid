<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use App\Support\Fundraising as Progress;
use Livewire\Attributes\Title;

#[Title('Goal and amount raised')]
class Fundraising extends AdminComponent
{
    public string $goal = '';

    public string $raised = '';

    public string $currency = '₱';

    public function mount(): void
    {
        $this->goal = (string) Setting::get('goal_amount', '');
        $this->raised = (string) Setting::get('raised_amount', '');
        $this->currency = (string) Setting::get('currency_symbol', '₱');
    }

    public function save(): void
    {
        $this->goal = str_replace(',', '', trim($this->goal));
        $this->raised = str_replace(',', '', trim($this->raised));

        $this->validate([
            'goal' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'raised' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'currency' => ['required', 'string', 'max:5'],
        ], [
            'goal.numeric' => 'Please type numbers only, for example 500000.',
            'raised.numeric' => 'Please type numbers only, for example 125000.',
        ]);

        Setting::put([
            'goal_amount' => $this->goal === '' ? null : $this->goal,
            'raised_amount' => $this->raised === '' ? 0 : $this->raised,
            'currency_symbol' => $this->currency,
            'progress_updated_at' => now()->toIso8601String(),
        ]);

        $this->notify('Saved. The website now shows the new amount.');
    }

    public function render()
    {
        return view('livewire.admin.fundraising', ['fund' => Progress::current()]);
    }
}
