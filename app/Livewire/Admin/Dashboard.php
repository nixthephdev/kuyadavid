<?php

namespace App\Livewire\Admin;

use App\Models\ContactMessage;
use App\Models\Prayer;
use App\Support\Fundraising;
use Livewire\Attributes\Title;

#[Title('Main menu')]
class Dashboard extends AdminComponent
{
    public function render()
    {
        return view('livewire.admin.dashboard', [
            'pendingPrayers' => Prayer::whereNull('approved_at')->count(),
            'unreadMessages' => ContactMessage::whereNull('read_at')->count(),
            'fund' => Fundraising::current(),
        ]);
    }
}
