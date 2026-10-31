<?php

namespace App\Livewire\Admin;

use App\Models\ShopItem;
use App\Support\ImageStore;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads;

#[Title('Buy from the family')]
class ShopItems extends AdminComponent
{
    use WithFileUploads;

    public ?int $editing = null;

    public string $title = '';

    public string $description = '';

    public ?string $photo = null;

    public $upload = null;

    protected array $photoFields = ['upload'];

    public function create(): void
    {
        $this->resetForm();
        $this->editing = 0;
    }

    public function edit(int $id): void
    {
        $item = ShopItem::findOrFail($id);
        $this->resetForm();
        $this->editing = $item->id;
        $this->title = $item->title;
        $this->description = (string) $item->description;
        $this->photo = $item->photo;
    }

    public function save(ImageStore $images): void
    {
        $this->validate([
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:600'],
            'upload' => ['nullable', ...self::PHOTO_RULES],
        ]);

        $item = $this->editing ? ShopItem::findOrFail($this->editing) : new ShopItem(['sort_order' => (int) ShopItem::max('sort_order') + 1]);
        $old = $item->photo;

        if ($this->upload) {
            $item->photo = $images->store($this->upload, 'shop');
        }

        $item->fill(['title' => trim($this->title), 'description' => trim($this->description) ?: null])->save();

        if ($old && $old !== $item->photo) {
            $images->delete($old);
        }

        $this->notify('Saved.');
        $this->resetForm();
    }

    public function move(int $id, string $direction): void
    {
        $items = ShopItem::ordered()->get()->values();
        $index = $items->search(fn ($i) => $i->id === $id);
        $swap = $direction === 'up' ? $index - 1 : $index + 1;

        if ($index === false || ! isset($items[$swap])) {
            return;
        }

        [$items[$index], $items[$swap]] = [$items[$swap], $items[$index]];
        $items->each(fn (ShopItem $item, int $i) => $item->update(['sort_order' => $i]));
    }

    public function delete(int $id, ImageStore $images): void
    {
        $item = ShopItem::findOrFail($id);
        $images->delete($item->photo);
        $item->delete();
        $this->resetForm();
        $this->notify('Removed.');
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset('editing', 'title', 'description', 'photo', 'upload');
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.shop-items', ['items' => ShopItem::ordered()->get()]);
    }
}
