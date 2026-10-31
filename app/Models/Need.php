<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Need extends Model
{
    protected $fillable = ['title', 'description', 'amount', 'sort_order'];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2'];
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
