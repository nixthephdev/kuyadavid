<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** One line on the Transparency page: what a donation was used for. */
class Expense extends Model
{
    protected $fillable = ['spent_on', 'item', 'amount', 'receipt', 'note'];

    protected function casts(): array
    {
        return [
            'spent_on' => 'date',
            'amount' => 'decimal:2',
        ];
    }
}
