<?php

namespace App\Livewire\Admin;

use App\Models\Need;
use Livewire\Attributes\Title;

#[Title('What David needs')]
class Needs extends AdminComponent
{
    public ?int $editing = null;

    public string $title = '';

    public string $description = '';

    public string $amount = '';

    public function create(): void
    {
        $this->resetForm();
        $this->editing = 0;
    }

    public function edit(int $id): void
    {
        $need = Need::findOrFail($id);
        $this->resetForm();
        $this->editing = $need->id;
        $this->title = $need->title;
        $this->description = (string) $need->description;
        $this->amount = $need->amount === null ? '' : (string) (float) $need->amount;
    }

    public function save(): void
    {
        $this->amount = str_replace(',', '', trim($this->amount));

        $this->validate([
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:400'],
            'amount' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
        ], [
            'amount.numeric' => 'Please type numbers only, or leave it empty if you are not sure.',
        ]);

        $need = $this->editing ? Need::findOrFail($this->editing) : new Need(['sort_order' => (int) Need::max('sort_order') + 1]);
        $need->fill([
            'title' => trim($this->title),
            'description' => trim($this->description) ?: null,
            'amount' => $this->amount === '' ? null : $this->amount,
        ])->save();

        $this->notify('Saved.');
        $this->resetForm();
    }

    /** Swap a card with its neighbour to change the order on the website. */
    public function move(int $id, string $direction): void
    {
        $needs = Need::ordered()->get()->values();
        $index = $needs->search(fn ($n) => $n->id === $id);
        $swap = $direction === 'up' ? $index - 1 : $index + 1;

        if ($index === false || ! isset($needs[$swap])) {
            return;
        }

        [$needs[$index], $needs[$swap]] = [$needs[$swap], $needs[$index]];
        $needs->each(fn (Need $need, int $i) => $need->update(['sort_order' => $i]));
    }

    public function delete(int $id): void
    {
        Need::findOrFail($id)->delete();
        $this->resetForm();
        $this->notify('Card deleted.');
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset('editing', 'title', 'description', 'amount');
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.needs', ['needs' => Need::ordered()->get()]);
    }
}
