<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/** Something the family sells to help pay for David's care. */
class ShopItem extends Model
{
    protected $fillable = ['title', 'description', 'photo', 'sort_order'];

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
