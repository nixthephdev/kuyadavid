<?php

namespace App\Livewire\Admin;

use App\Models\Expense;
use App\Support\ImageStore;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads;

#[Title('Where the money went')]
class Expenses extends AdminComponent
{
    use WithFileUploads;

    /** null = showing the list; 0 = new entry; otherwise the entry being edited. */
    public ?int $editing = null;

    public string $spent_on = '';

    public string $item = '';

    public string $amount = '';

    public string $note = '';

    public ?string $receipt = null;

    public $upload = null;

    public bool $removeReceipt = false;

    protected array $photoFields = ['upload'];

    public function create(): void
    {
        $this->resetForm();
        $this->editing = 0;
        $this->spent_on = now()->toDateString();
    }

    public function edit(int $id): void
    {
        $expense = Expense::findOrFail($id);
        $this->resetForm();
        $this->editing = $expense->id;
        $this->spent_on = $expense->spent_on->toDateString();
        $this->item = $expense->item;
        $this->amount = (string) (float) $expense->amount;
        $this->note = (string) $expense->note;
        $this->receipt = $expense->receipt;
    }

    public function save(ImageStore $images): void
    {
        $this->amount = str_replace(',', '', trim($this->amount));

        $this->validate([
            'spent_on' => ['required', 'date'],
            'item' => ['required', 'string', 'max:200'],
            'amount' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'note' => ['nullable', 'string', 'max:255'],
            'upload' => ['nullable', ...self::PHOTO_RULES],
        ], [
            'amount.numeric' => 'Please type numbers only, for example 2500.',
        ]);

        $expense = $this->editing ? Expense::findOrFail($this->editing) : new Expense;
        $old = $expense->receipt;
        $receipt = $old;

        if ($this->upload) {
            $receipt = $images->store($this->upload, 'receipts', minQuality: 55);
        } elseif ($this->removeReceipt) {
            $receipt = null;
        }

        $expense->fill([
            'spent_on' => $this->spent_on,
            'item' => trim($this->item),
            'amount' => $this->amount,
            'note' => trim($this->note) ?: null,
            'receipt' => $receipt,
        ])->save();

        if ($old && $old !== $receipt) {
            $images->delete($old);
        }

        $this->notify('Saved.');
        $this->resetForm();
    }

    public function delete(int $id, ImageStore $images): void
    {
        $expense = Expense::findOrFail($id);
        $images->delete($expense->receipt);
        $expense->delete();
        $this->resetForm();
        $this->notify('Entry deleted.');
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset('editing', 'spent_on', 'item', 'amount', 'note', 'receipt', 'upload', 'removeReceipt');
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.expenses', [
            'expenses' => Expense::orderByDesc('spent_on')->orderByDesc('id')->get(),
            'total' => (float) Expense::sum('amount'),
        ]);
    }
}
