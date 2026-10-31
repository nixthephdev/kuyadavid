<?php

namespace App\Livewire\Admin;

use App\Models\Prayer;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

#[Title('Prayer messages')]
class Prayers extends AdminComponent
{
    use WithPagination;

    #[Url]
    public string $tab = 'pending';

    public function show(string $tab): void
    {
        $this->tab = $tab === 'approved' ? 'approved' : 'pending';
        $this->resetPage();
    }

    public function approve(int $id): void
    {
        Prayer::findOrFail($id)->approve();
        $this->notify('Approved. It now shows on the prayer wall.');
    }

    public function hide(int $id): void
    {
        Prayer::findOrFail($id)->update(['approved_at' => null]);
        $this->notify('Hidden from the prayer wall.');
    }

    public function delete(int $id): void
    {
        Prayer::findOrFail($id)->delete();
        $this->notify('Message deleted.');
    }

    public function render()
    {
        $query = $this->tab === 'approved' ? Prayer::approved() : Prayer::pending();

        return view('livewire.admin.prayers', [
            'prayers' => $query->paginate(20),
            'pendingCount' => Prayer::whereNull('approved_at')->count(),
        ]);
    }
}
