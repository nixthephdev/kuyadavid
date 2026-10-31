<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Prayer extends Model
{
    protected $fillable = ['name', 'country', 'message', 'ip_hash', 'approved_at'];

    protected function casts(): array
    {
        return ['approved_at' => 'datetime'];
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->whereNotNull('approved_at')->latest('approved_at');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->whereNull('approved_at')->latest();
    }

    public function approve(): void
    {
        $this->update(['approved_at' => now()]);
    }

    public function displayName(): string
    {
        return $this->name ?: __('site.prayers.anonymous');
    }
}
