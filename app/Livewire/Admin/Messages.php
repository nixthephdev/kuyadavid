<?php

namespace App\Livewire\Admin;

use App\Models\ContactMessage;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

#[Title('Contact messages')]
class Messages extends AdminComponent
{
    use WithPagination;

    public ?int $open = null;

    public function toggle(int $id): void
    {
        if ($this->open === $id) {
            $this->open = null;

            return;
        }

        $this->open = $id;
        ContactMessage::whereKey($id)->whereNull('read_at')->update(['read_at' => now()]);
    }

    public function delete(int $id): void
    {
        ContactMessage::findOrFail($id)->delete();
        $this->open = null;
        $this->notify('Message deleted.');
    }

    public function render()
    {
        return view('livewire.admin.messages', [
            'messages' => ContactMessage::latest()->paginate(20),
        ]);
    }
}
