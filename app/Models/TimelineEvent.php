<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TimelineEvent extends Model
{
    protected $fillable = ['when_label', 'title', 'body', 'sort_order'];

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
